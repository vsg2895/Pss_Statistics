<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AnnouncementRequest;
use App\Models\Company;
use App\Models\ServiceProvider;
use App\Services\AnnouncementService;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public $anchor = '#configs';
    public AnnouncementService $announcementService;

    public function __construct(AnnouncementService $announcementService)
    {
        $this->announcementService = $announcementService;
    }

    public function companyStore(Company $company, AnnouncementRequest $request)
    {
        try {
            $this->announcementService->setParams($company);
            $this->announcementService->addRecord($request->validated());
            return redirect()->route('admin.companies.show', [$company, request()->start, request()->end, $this->anchor])
                ->with(['success' => 'Announcement Add Successfully']);
        } catch (\Exception $exception) {
            return redirect()->back()->withError(__('Something went wrong.'));
        }

    }

    public function providerStore(ServiceProvider $provider, AnnouncementRequest $request)
    {
        try {
            $this->announcementService->setParams([], 'provider', null, null, $provider);
            $this->announcementService->addRecord($request->validated());
            return redirect()->route('admin.service-providers.show', [$provider, request()->start, request()->end, $this->anchor])
                ->with(['success' => 'Announcement Add Successfully']);
        } catch (\Exception $exception) {
            return redirect()->back()->withError(__('Something went wrong.'));
        }
    }
}
