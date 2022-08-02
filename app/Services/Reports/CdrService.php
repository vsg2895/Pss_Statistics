<?php

namespace App\Services\Reports;

use App\Contracts\CdrInterface;
use App\Models\Company;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\FixedFees;
use App\Models\ServiceProvider;
use App\Models\ServiceProviderSettings;
use App\Services\Insert\SetBillingPricesService;
use App\Services\Update\UpdateDateFeesService;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\Setting;
use App\Services\BaseService;
use Carbon\CarbonInterval;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CdrService extends BaseService implements CdrInterface
{
    private $providerFee;
    private UpdateDateFeesService $updateDateFeesService;

    public function __construct()
    {
        $this->providerFee = Setting::getSettings();
        $this->updateDateFeesService = new UpdateDateFeesService(new SetBillingPricesService());
    }

    public function setBillingFee(): array
    {
        $billingData = $this->getBillingData();
        $chats = $this->getChatsData();
        $feeKeys = array_keys($chats['fee']);
        $billingData = $this->addDataInGeneralArray($billingData, 'chats_fee', $chats, 'total_income');
        $billingData = $this->setTotalIncome($billingData, 'chats_fee', 'total_income', 'original_fee', 'fee', $feeKeys);

        return $billingData;
    }

    public function CurrentCompanyTotals($cdrStatistics): array
    {
        $totals = [];
        if (!is_array($this->company)) {
            if (!is_null($this->company->service_provider_id)) {
                foreach (FeeType::PROVIDER_CORRESPONDE_NAME_TOTAL_TO_CDRSTATISTICS as $key => $value)
                    $totals[$key] = Arr::get($cdrStatistics, $value);
            } else {
                foreach (FeeType::CORRESPONDE_NAME_TOTAL_TO_CDRSTATISTICS as $key => $value)
                    $totals[$key] = Arr::get($cdrStatistics, $value);
            }
        } else {
            foreach (FeeType::CORRESPONDE_NAME_TOTAL_TO_CDRSTATISTICS as $key => $value)
                $totals[$key] = Arr::get($cdrStatistics, $value);
        }

        $query = DB::table('billing_data')->whereNull('deleted_at')->select('billing_data.*',
            DB::raw('AVG(duration) as avg_talk_time'))
            ->whereRaw('DATE(`date`) >= ?', [$this->start])
            ->whereRaw('DATE(`date`) <= ?', [$this->end]);

        $data = $this->getDataBasedOnType($query);

        $totals['avg_talk_time'] = $this->getTimeAboveCount(round($data->avg_talk_time));
        return $totals;

    }


    private function getChatsData()
    {
//        COALESCE equal to not null if null value sum 0
        $chatFilter = $this->getChatFilterBasedOnType();
        $conditionDateFilter = empty($chatFilter) ? "WHERE" : "AND";
        $chatsDateFilter = $this->getDateFilter('daily_chats', 'date', $conditionDateFilter);
        $chats = DB::select("SELECT COUNT(daily_chats.id) as chats_count, SUM(daily_chats.price) as price, COALESCE(SUM(daily_chats.provider_price), 0) as p_price,
                         departments.company_id FROM daily_chats
                         LEFT JOIN departments ON daily_chats.department_id = departments.department_id AND departments.company_id IS NOT NULL
                         $chatFilter $chatsDateFilter");
        $chats = collect($chats)->first();
        $chats = [
            'name' => 'Chats',
            'count' => $chats->chats_count,
            'original_fee' => [
                'price' => $chats->price,
                'p_price' => $chats->p_price,
            ],
            'fee' => [
                'price' => number_format($chats->price, 2, '.', ' '),
                'p_price' => number_format($chats->p_price, 2, '.', ' ')
            ]
        ];

        return $chats;
    }

    private function calcProviderInCompanyFixed($company, $providerId, $fixed, $custom, $month = null): int
    {
        $res = 0;

        if (!is_null($month)) {
            if (isset($fixed[$company->company_id])) {
                $res += $fixed[$company->company_id];
            } else {
                if (isset($custom[$company->id])) {
                    $res += $custom[$company->id];
                } else {
                    $res += ServiceProviderSettings::getProviderDefaultBySlug($providerId, 'monthly_fee');
                }
            }
        } else {
            if (isset($custom[$company->id])) {
                $res += $custom[$company->id];
            } else {
                $res += ServiceProviderSettings::getProviderDefaultBySlug($providerId, 'monthly_fee');
            }
        }

        return $res;
    }

    private function calcProviderChargeSum($companies, $providerId, $monthStarts): int
    {
        $res = 0;
        $companiesIds = $companies->pluck('company_id', 'id')->toArray();
        $fixed = FixedFees::providerToCompaniesFixedCustomByDate($providerId, array_values($companiesIds), 'monthly_fee', $monthStarts);
        $custom = Fee::getProviderCompaniesCustom(array_keys($companiesIds), $providerId);
        foreach ($companies as $company) {
            if (count($monthStarts)) {
                foreach ($monthStarts as $month) {
                    $res += $this->calcProviderInCompanyFixed($company, $providerId, $fixed, $custom, $month);
                }
            } else {
                $res += $this->calcProviderInCompanyFixed($company, $providerId, $fixed, $custom);
            }
        }

        return $res;
    }

    public function calcWeChargeCompanies($company, $monthStarts, $fixedCompany, $fixedProvider = null): float|int
    {
        $res = 0;
        $fixedSum = 0;
        $fixed = [];
        if (is_null($company->service_provider_id)) {
            if (count($fixedCompany)) {
                $fixedSum = array_sum($fixedCompany);
            }
            $fixed = $fixedCompany;
        } else {
            if (count($fixedProvider)) {
                $fixedSum = array_sum($fixedProvider);
            }
            $fixed = $fixedProvider;
        }
        $monthStartsCount = count($monthStarts) > 0 ? count($monthStarts) : 1;
        $monthsDiffCount = $monthStarts > count($fixed) ? $monthStartsCount - count($fixed) : 0;
        $customFee = is_null($company->service_provider_id)
            ? Fee::getCompanyCustom($company->id)
            : Fee::getProviderCustom($company->service_provider_id);
        if ($monthsDiffCount !== 0) {
            $res = isset($customFee['monthly_fee'])
                ? ($monthsDiffCount * $customFee['monthly_fee']) + $fixedSum
                : ($monthsDiffCount * Setting::getValueBySlug('monthly_fee')) + $fixedSum;
        } else {
            $res = $fixedSum;
        }

        return $res;
    }

    private function calcWeProviderChargeSum($countCompanyIds, $providerId, $fixed, $monthStarts): float|int
    {
        $fixedSum = 0;
        if (count($fixed)) {
            foreach ($fixed as $elem) {
                $fixedSum += $countCompanyIds * $elem;
            }
        }

        $monthStarts = count($monthStarts) > 0 ? count($monthStarts) : 1;
        $monthsDiffCount = $monthStarts > count($fixed) ? $monthStarts - count($fixed) : 0;

        if ($monthsDiffCount !== 0) {
            $res = isset(Fee::getProviderCustom($providerId)['monthly_fee'])
                ? ($monthsDiffCount * ($countCompanyIds * Fee::getProviderCustom($providerId)['monthly_fee'])) + $fixedSum
                : ($monthsDiffCount * ($countCompanyIds * Setting::getValueBySlug('monthly_fee'))) + $fixedSum;
        } else {
            $res = $fixedSum;
        }

        return $res;
    }

    public function getFixedForAll()
    {
        $months = $this->updateDateFeesService->checkMonths($this->start, $this->end);
        $monthStarts = data_get($months, '*.start');
        $sumFixedFee['monthly_fee']['our_fee'] = 0;
        $sumFixedFee['monthly_fee']['provider_fee'] = 0;
        $providers = ServiceProvider::all();
        $companiesWithoutProvider = Company::whereNull('service_provider_id')->get();
        $fixed = FixedFees::providersFixedCustomByDate($providers->pluck('id')->toArray(), 'monthly_fee', $monthStarts);
        foreach ($providers as $obj) {
//            We charge to provider
            $currFixed = count($fixed) && isset($fixed[$obj->id]) ? [$fixed[$obj->id]] : [];
            $sumFixedFee['monthly_fee']['our_fee'] += $this->calcWeProviderChargeSum(count($obj->companies), $obj->id, $currFixed, $monthStarts);
//            Provider charge to company
            $sumFixedFee['monthly_fee']['provider_fee'] += $this->calcProviderChargeSum($obj->companies, $obj->id, $monthStarts);
        }
        $fixedCompany = FixedFees::companiesFixedCustomByDate($companiesWithoutProvider->pluck('company_id')->toArray(), 'monthly_fee', $monthStarts);

        foreach ($companiesWithoutProvider as $obj) {
            $currFixed = count($fixedCompany) && isset($fixedCompany[$obj->id]) ? [$fixedCompany[$obj->company_id]] : [];
            //            We charge to company without provider
            $sumFixedFee['monthly_fee']['our_fee'] += $this->calcWeChargeCompanies($obj, $monthStarts, $currFixed, null);
        }

        return $sumFixedFee['monthly_fee'];
    }

    public function getFixedFee($obj)
    {
        $months = $this->updateDateFeesService->checkMonths($this->start, $this->end);
        $monthStarts = data_get($months, '*.start');
        $sumFixedFee['monthly_fee']['our_fee'] = 0;
        $sumFixedFee['monthly_fee']['provider_fee'] = 0;
        if ($obj instanceof ServiceProvider) {
            $fixed = FixedFees::providerFixedCustomByDate($obj->id, 'monthly_fee', $monthStarts)->pluck('fee')->toArray();
//            We charge to provider
            $providerDefault = $this->calcWeProviderChargeSum(count($obj->companies), $obj->id, $fixed, $monthStarts);
//            Provider charge from companies owned by him
            $providerToCompanyDefault = $this->calcProviderChargeSum($obj->companies, $obj->id, $monthStarts);
            $sumFixedFee['monthly_fee']['provider_fee'] = $providerToCompanyDefault;
            $sumFixedFee['monthly_fee']['our_fee'] = $providerDefault;
        } else {
            $fixedCompany = FixedFees::companyFixedCustomByDate($obj->company_id, 'monthly_fee', $monthStarts)->pluck('fee')->toArray();
            $fixedProvider = FixedFees::providerFixedCustomByDate($obj->service_provider_id, 'monthly_fee', $monthStarts)->pluck('fee')->toArray();
            //            We charge to company
            $companyDefault = $this->calcWeChargeCompanies($obj, $monthStarts, $fixedCompany, $fixedProvider);
//            Provider charge from company
            $providerToCompanyDefault = 0;
            $fixed = FixedFees::providerToCompaniesFixedCustomByDate($obj->service_provider_id, [$obj->company_id], 'monthly_fee', $monthStarts);
            $custom = Fee::getProviderCompaniesCustom([$obj->id], $obj->service_provider_id);
            if (count($monthStarts)) {
                foreach ($monthStarts as $month) {
                    $providerToCompanyDefault += $this->calcProviderInCompanyFixed($obj, $obj->service_provider_id, $fixed, $custom, $month);
                }
            } else {
                $providerToCompanyDefault = $this->calcProviderInCompanyFixed($obj, $obj->service_provider_id, $fixed, $custom);
            }
            $sumFixedFee['monthly_fee']['our_fee'] = $companyDefault;
            $sumFixedFee['monthly_fee']['provider_fee'] = $providerToCompanyDefault;

        }

        return $sumFixedFee['monthly_fee'];
    }

    private function getBillingData(): array
    {

        $query = DB::table('billing_data')->whereNull('billing_data.deleted_at')->select('billing_data.*',
            DB::raw('SUM(duration) as total_duration'),
            DB::raw("SUM(IF(duration - billing_data_fees.time_above_seconds > 0 and status = 'AN', duration - billing_data_fees.time_above_seconds, 0)) as time_above"),
            DB::raw("SUM(IF(billing_data_fees.p_time_above_seconds IS NOT NULL and duration - billing_data_fees.p_time_above_seconds > 0 and status = 'AN', duration - billing_data_fees.p_time_above_seconds, 0)) as provider_time_above"),
            DB::raw('SUM(case when status = "AN" then 1 else 0 end) as answered_calls'),
            DB::raw('SUM(case when status = "MI" then 1 else 0 end) as missed_calls'),
            DB::raw('SUM(case when status = "WT" then 1 else 0 end) as warm_calls'),
            DB::raw('SUM(case when status = "CT" then 1 else 0 end) as cold_calls'),
            DB::raw('SUM(case when status = "CL" then 1 else 0 end) as closed_calls'),
            DB::raw('SUM(case when status = "VK" then 1 else 0 end) as vip_calls'),
            DB::raw('SUM(case when message <> "0" then message else 0 end) as messages'),
            DB::raw('SUM(case when sms <> "0" then sms else 0 end) as sms'),
            DB::raw('SUM(case when email <> "0" then email else 0 end) as emails'),
            DB::raw('SUM(case when booking <> "0" then booking else 0 end) as bookings'),

            DB::raw('SUM(case when billing_data.status = "AN" and billing_data.free_call = false then billing_data_fees.calls_fee else 0 end) as answered_calls_price'),
            DB::raw('SUM(case when billing_data.status = "AN" and billing_data.p_free_call = false and billing_data.p_free_call IS NOT NULL then billing_data_fees.p_calls_fee else 0 end) as answered_calls_p_price'),

            DB::raw('SUM(case when billing_data.status = "WT" then billing_data_fees.warm_transferred_calls_fee else 0 end) as warm_calls_price'),
            DB::raw('SUM(case when billing_data.status = "WT" then billing_data_fees.p_warm_transferred_calls_fee else 0 end) as warm_calls_p_price'),

            DB::raw('SUM(case when billing_data.status = "CT" then billing_data_fees.cold_transferred_calls_fee else 0 end) as cold_calls_price'),
            DB::raw('SUM(case when billing_data.status = "CT" then billing_data_fees.p_cold_transferred_calls_fee else 0 end) as cold_calls_p_price'),

            DB::raw('SUM(case when billing_data.message <> "0" then billing_data.message * billing_data_fees.messages_fee else 0 end) as messages_price'),
            DB::raw('SUM(case when billing_data.message <> "0" then billing_data.message * billing_data_fees.p_messages_fee else 0 end) as messages_p_price'),

            DB::raw('SUM(case when billing_data.sms <> "0" then billing_data.sms * billing_data_fees.sms_fee else 0 end) as sms_price'),
            DB::raw('SUM(case when billing_data.sms <> "0" then billing_data.sms * billing_data_fees.p_sms_fee else 0 end) as sms_p_price'),

            DB::raw('SUM(case when billing_data.email <> "0" then billing_data.email * billing_data_fees.emails_fee else 0 end) as emails_price'),
            DB::raw('SUM(case when billing_data.email <> "0" then billing_data.email * billing_data_fees.p_emails_fee else 0 end) as emails_p_price'),

            DB::raw('SUM(case when billing_data.booking <> "0" then billing_data.booking * billing_data_fees.bookings_fee else 0 end) as bookings_price'),
            DB::raw('SUM(case when billing_data.booking <> "0" then billing_data.booking * billing_data_fees.p_bookings_fee else 0 end) as bookings_p_price'),

            DB::raw("SUM(IF(billing_data.duration - billing_data_fees.time_above_seconds > 0 and status = 'AN', billing_data.duration - billing_data_fees.time_above_seconds, 0) * billing_data_fees.above_60_fee) as time_above_price"),
            DB::raw("SUM(IF(billing_data.duration - billing_data_fees.p_time_above_seconds > 0 and status = 'AN' and billing_data_fees.p_time_above_seconds IS NOT NULL
                           and billing_data_fees.p_above_60_fee IS NOT NULL, billing_data.duration - billing_data_fees.p_time_above_seconds, 0) * IF(billing_data_fees.p_above_60_fee IS NOT NULL, billing_data_fees.p_above_60_fee, 1)) as p_time_above_price"),

            DB::raw('SUM(billing_data.price) as prices'),
            DB::raw('SUM(case when billing_data.provider_price <> "0" then billing_data.provider_price else 0 end) as provider_prices'))
            ->leftJoin('billing_data_fees', 'billing_data_fees.billing_id', 'billing_data.id')
            ->whereRaw('DATE(`date`) >= ?', [$this->start])
            ->whereRaw('DATE(`date`) <= ?', [$this->end]);


        $data = $this->getDataBasedOnType($query);

        return [
            'calls_fee' => [
                'name' => 'Answered Calls',
                'count' => $data->answered_calls,
                'fee' => [
                    'price' => number_format($data->answered_calls_price, 2, '.', ' '),
                    'p_price' => number_format($data->answered_calls_p_price, 2, '.', ' '),
                ]
            ],
            'missed_cals' => [
                'name' => 'Missed Calls',
                'count' => $data->missed_calls,
                'fee' => '-'
            ],
            'warm_transferred_calls_fee' => [
                'name' => 'Warm Transfers',
                'count' => $data->warm_calls,
                'fee' => [
                    'price' => number_format($data->warm_calls_price, 2, '.', ' '),
                    'p_price' => number_format($data->warm_calls_p_price, 2, '.', ' '),
                ]
            ],
            'cold_transferred_calls_fee' => [
                'name' => 'Cold Transfers',
                'count' => $data->cold_calls,
                'fee' => [
                    'price' => number_format($data->cold_calls_price, 2, '.', ' '),
                    'p_price' => number_format($data->cold_calls_p_price, 2, '.', ' '),
                ]
            ],
            'closed_cals' => [
                'name' => 'Closed Time Calls',
                'count' => $data->closed_calls,
                'fee' => '-'

            ],
            '24/7 calls' => [
                'name' => '24/7 Calls',
                'count' => $data->vip_calls,
                'fee' => '-'

            ],
            'messages' => [
                'name' => 'Messages',
                'count' => $data->messages,
                'fee' => [
                    'price' => number_format($data->messages_price, 2, '.', ' '),
                    'p_price' => number_format($data->messages_p_price, 2, '.', ' '),
                ]
            ],
            'sms' => [
                'name' => 'Sms',
                'count' => $data->sms,
                'fee' => [
                    'price' => number_format($data->sms_price, 2, '.', ' '),
                    'p_price' => number_format($data->sms_p_price, 2, '.', ' '),
                ]
            ],
            'emails' => [
                'name' => 'Emails',
                'count' => $data->emails,
                'fee' => [
                    'price' => number_format($data->emails_price, 2, '.', ' '),
                    'p_price' => number_format($data->emails_p_price, 2, '.', ' '),
                ]
            ],
            'bookings_fee' => [
                'name' => 'Bookings',
                'count' => $data->bookings,
                'fee' => [
                    'price' => number_format($data->bookings_price, 2, '.', ' '),
                    'p_price' => number_format($data->bookings_p_price, 2, '.', ' '),
                ]
            ],
            'time_above_income' => [
                'name' => 'Time Above Income',
                'count' => $this->getTimeAboveCount($data->time_above),
                'p_count' => $this->getTimeAboveCount($data->provider_time_above),
                'original_count' => $data->time_above,
                'original_p_count' => $data->provider_time_above,
                'fee' => [
                    'price' => number_format($data->time_above_price, 2, '.', ' '),
                    'p_price' => number_format($data->p_time_above_price, 2, '.', ' '),
                ]
            ],
            'total_income' => [
                'name' => 'Total Income',
                'count' => '-',
                'original_fee' => [
                    'price' => $data->prices,
                    'p_price' => $data->provider_prices,
                ],
                'fee' => [
                    'price' => number_format($data->prices, 2, '.', ' '),
                    'p_price' => number_format($data->provider_prices, 2, '.', ' '),
                ]
            ],

        ];
    }

    //todo check when time above is 0/null, it returns 1 second
    private function getTimeAboveCount($timeAbove, $moreTime = false): int|string
    {
        if ($timeAbove == 0) {
            return 0;
        } else {
            Carbon::setLocale('en');
            $dt = Carbon::now();
            if (!$moreTime) {
                $hours = $dt->diffInHours($dt->copy()->addSeconds($timeAbove));
                $minutes = $dt->diffInMinutes($dt->copy()->addSeconds($timeAbove)->subHours($hours));
                $seconds = $dt->diffInSeconds($dt->copy()->addSeconds($timeAbove)->subHours($hours)->subMinutes($minutes));
                $date = CarbonInterval::hours($hours)->minutes($minutes)->seconds($seconds)->forHumans([
                    'short' => true
                ]);
            } else {
                $minutes = $dt->diffInMinutes($dt->copy()->addSeconds($timeAbove));
                $seconds = $dt->diffInSeconds($dt->copy()->addSeconds($timeAbove)->subMinutes($minutes));
                $date = CarbonInterval::minutes($minutes)->seconds($seconds)->forHumans([
                    'short' => true
                ]);
            }

            return $date;
        }

    }

    private function getDataBasedOnType($query)
    {
        $companyId = is_array($this->company) ? $this->company : $this->company->company_id;
        return match ($this->type) {
            'company' => $query->where('company_id', $companyId)
                ->first(),
            'provider_companies' => $query->whereIn('company_id', $companyId)->first(),
            'all' => $query->first(),
        };
    }


    private function getChatFilterBasedOnType(): string
    {
        $companyId = is_array($this->company) ? $this->company : $this->company->company_id;
        if (empty($companyId)) {
            return '';
        }
        if (is_array($companyId)) {
            $companyId = '(' . implode(',', $companyId) . ')';
        }

        return match ($this->type) {
            'company' => "LEFT JOIN companies ON companies.company_id = departments.company_id WHERE companies.company_id = $companyId",
            'provider_companies' => "LEFT JOIN companies ON companies.company_id = departments.company_id WHERE companies.company_id IN $companyId",
            'all' => '',
        };

    }

    private function getStatusCorrespondingName($status): bool|string
    {
        $correspondingValue = "";
        switch ($status) {
            case "AN":
                $correspondingValue = "Answered";
                break;
            case "MI":
                $correspondingValue = "Missed";
                break;
            case "WT":
                $correspondingValue = "Warm Transfer";
                break;
            case "CT":
                $correspondingValue = "Cold Transfer";
                break;
            case "CL":
                $correspondingValue = 'Closed';
                break;
            case "VK":
                $correspondingValue = '24 calls';
                break;
            default:
                $correspondingValue = false;
        }
        return $correspondingValue;
    }

    public function getMoreData($page)
    {
//        WHERE `billing_data`.`deleted_at` = NULL,
//        WHERE DATE(`billing_data`.`date`) >= '2022-02-01' AND DATE(`billing_data`.`date`) <= '2022-02-28'
        $perPage = 25;
        $offset = ($page - 1) * $perPage;
        $companyFilter = $this->getMoreInfoByType();
        $dateFilter = $this->getDateFilter('billing_data', 'date', 'WHERE');
        $pagingQuery = "LIMIT {$offset}, {$perPage}";
        $moreData = DB::select("select SQL_CALC_FOUND_ROWS *, `billing_data`.`call_id`, `billing_data`.`duration`, `billing_data`.`a_number`, `billing_data`.`b_number`, `billing_data`.`status`,
           `billing_data`.`price`,`billing_data`.`provider_price`,`billing_data`.`date`,`imported_users`.`servit_username`,`imported_users`.`servit_id`, `companies`.`name`
            FROM `billing_data`
            LEFT join `imported_users` on `billing_data`.`agent_id` = `imported_users`.`servit_id`
            LEFT join `companies` on `billing_data`.`company_id` = `companies`.`company_id`
            $dateFilter AND `billing_data`.`deleted_at` IS NULL $companyFilter $pagingQuery
            ");
        $numrows = json_decode(json_encode(DB::select('SELECT FOUND_ROWS()')[0]), true)['FOUND_ROWS()'];
        collect($moreData)->each(function ($item) {
            $item->status = $this->getStatusCorrespondingName($item->status);
            $item->duration = $this->getTimeAboveCount($item->duration, true);
        });
        $moreData = new LengthAwarePaginator($moreData, $numrows, $perPage);

        return $moreData;
    }

    private function getMoreInfoByType(): string
    {
        $companyId = $this->company->company_id;
        return match ($this->type) {
            'company' => "AND companies.company_id = $companyId",
            'all' => "",
        };
    }
}
