<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\CdrInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompanySave;
use App\Http\Requests\DateRange;
use App\Http\Requests\MoreData\ChatConversationRequest;
use App\Http\Requests\MoreData\MoreChatsRequest;
use App\Models\Company;
use App\Models\Fee;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderSettings;
use App\Models\Setting;
use App\Models\Tag;
use App\Services\CompanyService;
use App\Services\Update\UpdateDateFeesService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CompanyController extends Controller
{
    private CompanyService $companyService;
    private CdrInterface $cdr;
    private UpdateDateFeesService $updateDateFeesService;

    public function __construct(CompanyService $companyService, CdrInterface $cdr, UpdateDateFeesService $updateDateFeesService)
    {
        $this->companyService = $companyService;
        $this->cdr = $cdr;
        $this->updateDateFeesService = $updateDateFeesService;
    }

    public function edit(Company $company): string
    {
        return json_encode([
            'company' => $company,
        ]);
    }

    public function show(Company $company, DateRange $request)
    {
        $tags = Tag::all();
        $providers = ServiceProvider::all();
        $fees = $company->service_provider_id
            ? Fee::getProviderCompanyCustom($company->id, $company->service_provider_id)
            : Fee::getCompanyCustom($company->id);
        $settings = $company->service_provider_id
            ? ServiceProviderSettings::getCompanyDefaults($company->service_provider_id)
            : Setting::getProviderDefaults();
        $this->cdr->setParams($company, 'company', $request->start, $request->end);
        $this->updateDateFeesService->setParams($company, 'update_company', $request->start, $request->end);
        $feesArray = $this->updateDateFeesService->getDbCurrentFees($company);
        $fixed = $this->cdr->getFixedFee($company);
        $cdrStatistics = $this->cdr->setBillingFee();
        $cdrStatistics['total_income']['fee']['price'] = number_format((float)$cdrStatistics['total_income']['original_fee']['price'] + (float)$fixed['our_fee'], 2, '.', ' ');
        $cdrStatistics['total_income']['fee']['p_price'] = number_format((float)$cdrStatistics['total_income']['original_fee']['p_price'] + (float)$fixed['provider_fee'], 2, '.', ' ');
        $totals = $this->cdr->CurrentCompanyTotals($cdrStatistics);
        $text = $company->announcement()->pluck('text')->first();


        return view('pages.admin.companies.show',
            compact('company', 'fixed', 'tags', 'providers', 'fees', 'settings', 'cdrStatistics', 'feesArray', 'totals', 'text'));
    }

    public function update(Company $company, CompanySave $request): RedirectResponse
    {
        $company->tags()->sync($request->tags);
        $data = $request->validated();
        unset($data['tags']);
        $company->update($data);
        Fee::DeleteCompanyCustom($company->id);
        return redirect()->route('admin.companies.show', [$company, '#configs'])
            ->with(['success' => __('Company data updated successfully')]);
    }

    public function updateCompareExcluding(Company $company, Request $request)
    {
        $update = $company->update(['exclude_compare' => $request->has('exclude_compare')]);
        return $update ? redirect()->back()->withSuccess(__('Companies compare option updated successfully'))
            : redirect()->back()->withError(__('Something went wrong.'));
    }

    public function dashboard(DateRange $request)
    {
        $providerFilter = null;
        if ($request->has('provider')) {
            $providerFilter = $request->provider ? true : false;
        }
        $companies = $this->companyService->getDashboardCompanies($providerFilter);
        $companyIds = collect($companies)->pluck('company_id')->toArray();
        $this->cdr->setParams($companyIds, 'provider_companies', $request->start, $request->end);
        $cdrStatistics = $this->cdr->setBillingFee();
        $settings = Setting::getProviderDefaults();
        $totals = $this->cdr->CurrentCompanyTotals($cdrStatistics);

        return view('pages.admin.companies.dashboard', [
            'companies' => $companies,
            'totals' => $totals,
            'settings' => $settings,
            'tags' => Tag::all(),
        ]);
    }

    public function getChatData(Company $company, MoreChatsRequest $request): \Illuminate\Http\JsonResponse
    {
        $start = is_null($request->start) ? Carbon::now()->format('Y-m-d') : $request->start;
        $end = is_null($request->end) ? Carbon::now()->format('Y-m-d') : $request->end;
        $companyChats = $company->getChatsByDate($start, $end);
        return response()
            ->json([
                'view' => view('pages.admin.companies.moreInfo', compact('companyChats', 'company'))->render(),
                'companyChats' => $companyChats
            ]);

    }

    public function getChatConversations(ChatConversationRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $basePart = config('apiKeys.chatMessage_url');
            $params = ['apikey=' . config('apiKeys.chatMessage_api_key')];
            $dynamicArgument = '/' . $request->chat_id . '/messages';
            $url = $this->companyService->createApiAddress($basePart, $params, $dynamicArgument);
            $chatData = $this->companyService->getChatConversation($url);

            return response()
                ->json([
                    'view' => view('components.billing.conversation-content', compact('chatData'))->render(),
                    'chatData' => $chatData
                ]);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()]);
        }
    }

    public function getMoreInfo(Company $company, DateRange $request): \Illuminate\Http\JsonResponse
    {
        $this->cdr->setParams($company, 'company', $request->start, $request->end);
        $page = !empty($request->page) ? $request->page : '1';
        $moreInfo = $this->cdr->getMoreData($page);

        return response()
            ->json([
                'view' => view('pages.admin.companies.moreInfo', compact('moreInfo', 'page', 'company'))->render(),
            ]);
    }
}
