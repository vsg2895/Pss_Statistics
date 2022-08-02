<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\CdrInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Attachment\AddSpFile;
use App\Http\Requests\Attachment\AddSpMedia;
use App\Http\Requests\DateRange;
use App\Http\Requests\ServiceProvider\DeleteForAllRequest;
use App\Http\Requests\ServiceProvider\Save;
use App\Models\Attachment;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderSettings;
use App\Models\Setting;
use App\Services\ServiceProviderService;
use App\Services\Update\UpdateDateFeesService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class ServiceProviderController extends Controller
{
    private string $anchor = '#providers-files';
    private CdrInterface $cdr;
    private UpdateDateFeesService $updateDateFeesService;
    private ServiceProviderService $serviceProviderService;

    public function __construct(CdrInterface $cdr, UpdateDateFeesService $updateDateFeesService, ServiceProviderService $serviceProviderService)
    {
        $this->cdr = $cdr;
        $this->updateDateFeesService = $updateDateFeesService;
        $this->serviceProviderService = $serviceProviderService;
    }

    public function index()
    {
        $allFiles = Attachment::where('type', 'provider-file-all')->get();
        $allMedias = Attachment::where('type', 'provider-media-all')->get();

        return view('pages.admin.providers.index', [
            'providers' => ServiceProvider::all(),
            'providersIds' => $allFiles->groupBy('name'),
            'providersIdsMedia' => $allMedias->groupBy('name'),
            'providersFiles' => $allFiles->unique('name'),
            'providersMedias' => $allMedias->unique('name')
        ]);
    }

    public function show(ServiceProvider $serviceProvider, DateRange $request)
    {
        $serviceProvider = $serviceProvider->load(['serviceProviderUsers', 'companies', 'fees.feeType']);
        $res = $this->serviceProviderService->getShowData($request->start, $request->end, $serviceProvider, $this->cdr, $this->updateDateFeesService);
        $feesArray = $this->updateDateFeesService->getDbCurrentFees($res['companies'], $res['departmentIds']);
        $fixed = $this->cdr->getFixedFee($serviceProvider);
        $cdrStatistics = $this->cdr->setBillingFee();
        $cdrStatistics['total_income']['fee']['price'] = number_format((float)$cdrStatistics['total_income']['original_fee']['price'] + (float)$fixed['our_fee'], 2, '.', ' ');
        $cdrStatistics['total_income']['fee']['p_price'] = number_format((float)$cdrStatistics['total_income']['original_fee']['p_price'] + (float)$fixed['provider_fee'], 2, '.', ' ');
        $text = $serviceProvider->announcement()->pluck('text')->first();

        return view('pages.admin.providers.show', [
            'provider' => $serviceProvider,
            'cdrStatistics' => $cdrStatistics,
            'settings' => Setting::getProviderDefaults(),
            'fees' => $res['fees'],
            'feesArray' => $feesArray,
            'fixed' => $fixed,
            'text' => $text,
            'attachments' => $serviceProvider->documents,
            'medias' => $serviceProvider->medias,
        ]);
    }

    public function store(Save $request): RedirectResponse
    {
        try {
            $this->serviceProviderService->storeProvider($request->validated());

            return back()->with(['serviceProviders' => ServiceProvider::all(), 'success' => __('Service Provider created successfully')]);
        } catch (\Exception $exception) {
            return back()->with(['error' => $exception->getMessage()]);
        }
    }

    public function edit(ServiceProvider $serviceProvider): JsonResponse
    {
        return response()->json(['provider' => $serviceProvider]);
    }

    public function update(Save $request, ServiceProvider $serviceProvider): RedirectResponse
    {
        $serviceProvider->update($request->validated());

        return back()->with(['success' => __('Service Provider updated successfully')]);
    }

    public function destroy(ServiceProvider $serviceProvider): RedirectResponse
    {
        if (count($serviceProvider->serviceProviderUsers) || count($serviceProvider->companies)) {
            return back()->with(['error' => __('At first remove provider companies and users')]);
        }
        DB::beginTransaction();
        ServiceProviderSettings::where('service_provider_id', $serviceProvider->id)->delete();
        $serviceProvider->delete();
        DB::commit();
        return back()->with(['success' => __('Service Provider deleted successfully')]);
    }

    public function uploadMediaForAll(AddSpMedia $request): RedirectResponse
    {
        $this->anchor = "#providers-medias";
        $this->serviceProviderService->uploadAttachment($request->media, 'provider-media-all', false, false, true);

        return redirect()->route('admin.service-providers.index', [$this->anchor])
            ->with('success', __('Media Link uploaded successfully.'));

    }

    public function uploadFileForAll(AddSpFile $request): RedirectResponse
    {
        $this->serviceProviderService->uploadAttachment($request->file, 'provider-file-all', config('filesystems.paths.sp_files_for_all'), true, true);

        return redirect()->route('admin.service-providers.index', [$this->anchor])
            ->with('success', __('File uploaded successfully.'));

    }

    public function uploadMedia(ServiceProvider $serviceProvider, AddSpMedia $request): RedirectResponse
    {
        $this->anchor = '#medias';
        $this->serviceProviderService->uploadAttachment($request->media, 'provider-media', false, false, false, $serviceProvider);

        return Redirect::to(URL::previous() . $this->anchor)
            ->with('success', __('Media uploaded successfully.'));

    }

    public function uploadFile(ServiceProvider $serviceProvider, AddSpFile $request): RedirectResponse
    {
        $this->anchor = '#files';
        $this->serviceProviderService->uploadAttachment($request->file, 'provider-file', config('filesystems.paths.sp_files'), true, false, $serviceProvider);

        return Redirect::to(URL::previous() . $this->anchor)
            ->with('success', __('File uploaded successfully.'));

    }

    public function deleteFile(Attachment $attachment): RedirectResponse
    {
        $this->anchor = '#files';
        $this->serviceProviderService->deleteAttachment($attachment);

        return Redirect::to(URL::previous() . $this->anchor)
            ->with('success', __('File deleted successfully.'));

    }

    public function deleteMedia(Attachment $attachment): RedirectResponse
    {
        $this->anchor = '#medias';
        $this->serviceProviderService->deleteAttachment($attachment, false);

        return Redirect::to(URL::previous() . $this->anchor)
            ->with('success', __('Media deleted successfully.'));
    }

    public function deleteFileAll(DeleteForAllRequest $request): RedirectResponse
    {
        $this->anchor = '#providers-files';
        $this->serviceProviderService->deleteAttachment($request->ids, true, true);

        return Redirect::to(URL::previous() . $this->anchor)
            ->with('success', __('File deleted successfully.'));

    }

    public function deleteMediaAll(DeleteForAllRequest $request): RedirectResponse
    {
        $this->anchor = '#providers-medias';
        $this->serviceProviderService->deleteAttachment($request->ids, false);

        return Redirect::to(URL::previous() . $this->anchor)
            ->with('success', __('Media deleted successfully.'));
    }

}
