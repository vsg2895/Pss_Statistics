<?php

namespace App\Http\Controllers\Admin\Settings;

use App\Contracts\LogInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Setting\SettingSave;
use App\Models\FeeType;
use App\Models\ServiceProviderSettings;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VariableController extends Controller
{
    private LogInterface $log;

    public function __construct(LogInterface $log)
    {
        $this->log = $log;
    }

    public function index(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $settings = Setting::whereNotIn('slug', FeeType::FEE_TYPES)->get();


        return view('pages.admin.settings.variables', ['settings' => $settings]);
    }

    public function edit(Setting $setting): string
    {
        return json_encode(['setting' => $setting]);
    }

    public function store(SettingSave $request): RedirectResponse
    {
        DB::beginTransaction();

        Setting::create($request->validated());
        FeeType::create($request->only(['name', 'slug']));
        $this->addProviderFees($request->only(['name', 'slug']));

        DB::commit();

        $this->log->actionArrayInfo('Default_Setting_Added', $request->validated());

        return back()->with(['settings' => Setting::all(), 'success' => __('Setting created successfully')]);
    }

    public function update(SettingSave $request, Setting $setting): RedirectResponse
    {
        $data = $request->validated();
        $logData = array_merge($data, ['oldValue' => $setting->value]);

        $this->log->actionArrayInfo('Default_Fee_Update', $logData);

        //todo fix log issue, for the first time log is trying to write to file, it fails with file permission error,
        // - but when we run update again, it works

        $setting->update($data);
        return back()->with(['settings' => Setting::all(), 'success' => __('Setting updated successfully')]);
    }

    public function billing(): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $settings = Setting::getProviderDefaults();

        return view('pages.admin.settings.variables', ['settings' => $settings]);
    }

    public function addProviderFees($data)
    {
        $providerIds = ServiceProviderSettings::groupBy('service_provider_id')->pluck('service_provider_id')->toArray();

        $insertData = [];
        $data['value'] = FeeType::FEE_TYPES_VALUES[$data['slug']] ?? 0;
        $data['description'] = FeeType::FEE_TYPES_DESCRIPTIONS[$data['slug']] ?? null;
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        foreach ($providerIds as $id) {
            $data['service_provider_id'] = $id;
            $insertData[] = $data;
        }

        ServiceProviderSettings::insert($insertData);
    }
}
