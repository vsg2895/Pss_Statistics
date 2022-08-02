<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\LogInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Fees\UpdateDateFees;
use App\Http\Requests\Fees\UpdateDateFeesAll;
use App\Jobs\HistoricalUpdate;
use App\Models\Company;
use App\Models\ServiceProvider;
use App\Services\Update\UpdateDateFeesService;
use Illuminate\Support\Facades\Auth;


class DateFeesController extends Controller
{
    private UpdateDateFeesService $updateDateFeesService;
    private LogInterface $log;
    public string $anchor = '#date-update';
    public string $successFees;

    public function __construct(UpdateDateFeesService $updateDateFeesService, LogInterface $log)
    {
        $this->updateDateFeesService = $updateDateFeesService;
        $this->log = $log;
        $this->successFees = __('Fees updated successfully');
    }

    /**
     * @throws \Throwable
     */
    public function updateFeesByDate(Company $company, UpdateDateFees $request): \Illuminate\Http\RedirectResponse
    {
        if (is_null($request->checks)) {
            $this->updateDateFeesService->updateFeesGeneral($request->values, $request->fee_type_ids, $company, 'company_id');
        } else {
            $initialData = $this->updateDateFeesService->initialGetDataFromUpdate($request->checks, $company, $request->start, $request->end, 'update_company');
            if (array_key_exists('monthly_fee', $request->checks)) {
                $monthlyFee = $request->checks['monthly_fee'];
                $request->checks = $request->except(['checks.monthly_fee'])['checks'];
            } else {
                $monthlyFee = false;
            }
            HistoricalUpdate::dispatch($this->log, Auth::user(), $this->updateDateFeesService, $request->start, $request->end,
                $company, $initialData['data'], $request->checks, $initialData['updated'], $initialData['delIds'], null, null, false, $monthlyFee);
            $this->anchor = '#statistics';
            $this->successFees = __('Fees By Date update process started ...');
        }
        return redirect()->route('admin.companies.show', [$company, 'start' => $request->start, 'end' => $request->end, $this->anchor])
            ->with(['success' => $this->successFees]);

    }

    public function updateFeesByDateAll(UpdateDateFeesAll $request): \Illuminate\Http\RedirectResponse
    {
        $companies = Company::whereIn('company_id', $request->ids)->with('departments')->get();
        $departmentIds = [];
        $companies->pluck('departments')->map(function ($el) use (&$departmentIds) {
            $el->map(function ($department) use (&$departmentIds) {
                $departmentIds[] = $department->department_id;
            });
        });

        $initialData = $this->updateDateFeesService->initialGetDataFromUpdate($request->checks, $request->ids, $request->start, $request->end, 'update_companies', $departmentIds);
        $companiesWithCalls = $companies->whereIn('company_id', array_keys($initialData['data']->groupBy('company_id')->all()));
        HistoricalUpdate::dispatch($this->log, Auth::user(), $this->updateDateFeesService, $request->start, $request->end,
            $companiesWithCalls, $initialData['data'], $request->checks, $initialData['updated'], $initialData['delIds'], $initialData['departmentIds'], null, true, null);

        $this->anchor = '#statistics';
        $this->successFees = __('Fees By Date update process started ...');

        return redirect()->back()->with(['success' => $this->successFees]);

    }

    public function updateFeesProviderByDate(ServiceProvider $provider, UpdateDateFees $request): \Illuminate\Http\RedirectResponse
    {
        if (is_null($request->checks)) {
            $this->updateDateFeesService->updateFeesGeneral($request->values, $request->fee_type_ids, $provider, 'service_provider_id');
        } else {
            $companyIds = $provider->companies->pluck('company_id')->toArray();
            $companies = $provider->companies()->with('departments')->get();
            $departmentIds = [];
            $companies->pluck('departments')->map(function ($el) use (&$departmentIds) {
                $el->map(function ($department) use (&$departmentIds) {
                    $departmentIds[] = $department->department_id;
                });
            });
            if (array_key_exists('monthly_fee', $request->checks)) {
                $monthlyFee = $request->checks['monthly_fee'];
                $request->checks = $request->except(['checks.monthly_fee'])['checks'];
            } else {
                $monthlyFee = false;
            }
            $initialData = $this->updateDateFeesService->initialGetDataFromUpdate($request->checks, $companyIds, $request->start, $request->end, 'update_provider', $departmentIds);
            $companiesWithCalls = $companies->whereIn('company_id', array_keys($initialData['data']->groupBy('company_id')->all()));
            HistoricalUpdate::dispatch($this->log, Auth::user(), $this->updateDateFeesService, $request->start, $request->end,
                $companiesWithCalls, $initialData['data'], $request->checks, $initialData['updated'], $initialData['delIds'], $initialData['departmentIds'], $provider, false, $monthlyFee);

            $this->anchor = '#statistics';
            $this->successFees = __('Fees By Date update process started ...');
        }

        return redirect()->route('admin.service-providers.show', [$provider, 'start' => $request->start, 'end' => $request->end, $this->anchor])
            ->with(['success' => $this->successFees]);

    }

}
