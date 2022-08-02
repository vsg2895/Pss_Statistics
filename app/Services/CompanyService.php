<?php

namespace App\Services;

use App\Models\Company;
use App\Models\FeeType;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class CompanyService extends BaseService
{
    private $providerDefaultFees;
    private $defaultFees = [];
    private string $timeZoneOffset = '+28800';//60*60*8

    public function __construct($type = 'default')
    {
        $this->providerDefaultFees = Setting::getProviderDefaults();

    }

    public function buildComparingQuery($start, $end)
    {
        return Company::select('companies.*', 'calls.*')->selectRaw('COUNT(calls.id) as calls_count')
            ->join('calls', 'companies.company_id', '=', 'calls.company_number')
            ->groupBy('calls.company_number')
            ->orderBy('companies.id', 'desc')
            ->where('companies.exclude_compare', false)
            ->whereRaw('DATE(`started_at`) >= ?', [$start])
            ->whereRaw('DATE(`started_at`) <= ?', [$end]);
    }

    public function getCompaniesComparingData($start, $end, $s_start, $s_end, $filter = 0): array
    {
        $companyDataStart = is_null($filter) || $filter === 0 ? $this->buildComparingQuery($s_start, $s_end)->get()->groupBy('company_id')
            : $this->buildComparingQuery($s_start, $s_end)->havingRaw("COUNT(calls.id) > $filter")->get()->groupBy('company_id');

        $companyDataEnd = is_null($filter) || $filter === 0 ? $this->buildComparingQuery($start, $end)->get()->groupBy('company_id')
            : $this->buildComparingQuery($start, $end)->havingRaw("COUNT(calls.id) > $filter")->get()->groupBy('company_id');

        if (count($companyDataStart)) {
            $diffIdsStart = array_diff_key($companyDataStart->toArray(), $companyDataEnd->toArray());
            $diffIdsEnd = array_diff_key($companyDataEnd->toArray(), $companyDataStart->toArray());
        }
        if (count($companyDataEnd) && count($companyDataStart)) {
            $lostPercent = round(count($diffIdsStart) * 100 / count($companyDataStart));
            $newPercent = round(count($diffIdsEnd) * 100 / count($companyDataEnd));
        }

        return [
            $s_start . ' - ' . $s_end => [
                'data' => $companyDataStart,
                'diffIds' => count($companyDataStart) && count($companyDataEnd) ? $diffIdsStart : 0,
                'diff_percent' => count($companyDataStart) && count($companyDataEnd) ? $lostPercent : 0,
            ],
            $start . ' - ' . $end => [
                'data' => $companyDataEnd,
                'diffIds' => count($companyDataStart) && count($companyDataEnd) ? $diffIdsEnd : 0,
                'diff_percent' => count($companyDataStart) && count($companyDataEnd) ? $newPercent : 0,
                'netto' => [
                    'companies' => count($companyDataEnd) - count($companyDataStart),
                    'percent' => count($companyDataStart) && count($companyDataEnd) ? $newPercent - $lostPercent : 0,
                ]
            ],
        ];
    }

    public function getCompanies(): array
    {
        $callsDateFilter = $this->getDateFilter('calls', 'started_at');
        $tagFilter = $this->getTagFilter();
        $timeAboveSeconds = $this->providerDefaultFees->where('slug', FeeType::FEE_TYPES['time_above_seconds'])->first()->value;

        $companies = DB::select("select `companies`.`id`, `companies`.`name`, `companies`.`company_id`, `companies`.`city`, `companies`.`added_at`, `companies`.`url`,
            JSON_EXTRACT(`company_meta`.`meta_data`, '$.street') as street,
            COUNT(calls.connected_at) as answered_calls,
            (COUNT(calls.id) - COUNT(calls.connected_at)) as missed_calls,
            SUM(IF(TIMESTAMPDIFF(second, calls.connected_at, calls.hang_up_at) - $timeAboveSeconds > 0, TIMESTAMPDIFF(second, calls.connected_at, calls.hang_up_at) - $timeAboveSeconds, 0)) as time_above
            FROM `companies`
            LEFT join `calls` on `companies`.`company_id` = `calls`.`company_number` $callsDateFilter
            LEFT join `company_meta` on `companies`.`company_id` = `company_meta`.`company_id`
            WHERE companies.active = '1'
            $tagFilter
            group by `companies`.`id`
            order by `companies`.`id` desc
            ");

        return $this->getData($companies);
    }

    public function getMoreData($company_id)
    {
        $company = Company::findOrFail($company_id);

    }

    private function getTagFilter($query = 'AND'): string
    {
        $tagFilter = "";
        if (request()->tags) {
            $tags = implode(',', request()->tags);
            $tagFilter = " $query EXISTS (select * from `tags` inner join `taggables` on `tags`.`id` = `taggables`.`tag_id`
            where `companies`.`id` = `taggables`.`taggable_id`
            and `taggables`.`taggable_type` = 'App\\\Models\\\Company'
            and `tags`.`id` in ($tags))";
        }

        return $tagFilter;
    }

    public function getChatConversation($url)
    {
        $response = Http::withHeaders([
            'apikey' => config('apiKeys.chatMessage_header_apiKey'),
            'Timezone-Offset' => $this->timeZoneOffset,
        ])->get($url);

        return json_decode($response->body(), true)['response']['groups'];

    }

    //-------------- - - Company Dashboard - - -----------------
    public function getDashboardCompanies($providerFilter = null)
    {
        $companiesData = DB::table('billing_data')->whereNull('billing_data.deleted_at')->select('billing_data.*', 'companies.city', 'companies.name', 'companies.id as c_id', 'company_meta.meta_data',
            DB::raw('SUM(duration) as total_duration'),
            DB::raw('json_extract(company_meta.meta_data, "$.street") as street'),
            DB::raw("SUM(IF(duration - billing_data_fees.time_above_seconds > 0 and status = 'AN', duration - billing_data_fees.time_above_seconds, 0)) as time_above"),
            DB::raw("SUM(IF(billing_data_fees.p_time_above_seconds IS NOT NULL and duration - billing_data_fees.p_time_above_seconds > 0 and status = 'AN', duration - billing_data_fees.p_time_above_seconds, 0)) as provider_time_above"),
            DB::raw('SUM(case when status = "AN" then 1 else 0 end) as answered_calls'),
            DB::raw('SUM(case when booking <> "0" then booking else 0 end) as bookings'),
            DB::raw('SUM(case when billing_data.booking <> "0" then billing_data.booking * billing_data_fees.bookings_fee else 0 end) as bookings_price'),
            DB::raw('SUM(case when billing_data.booking <> "0" then billing_data.booking * billing_data_fees.p_bookings_fee else 0 end) as bookings_p_price'))
            ->leftJoin('billing_data_fees', 'billing_data_fees.billing_id', 'billing_data.id')
            ->leftJoin('company_meta', 'billing_data.company_id', 'company_meta.company_id')
            ->leftJoin('companies', 'billing_data.company_id', 'companies.company_id');

        if (!is_null($providerFilter)) {
            $companiesData = $providerFilter ? $companiesData->whereNotNull('companies.service_provider_id')
                : $companiesData->whereNull('companies.service_provider_id');
        }
        if (!is_null(request()->tags)) {
//            whereExists equal right join this station base table is billing data and need use right join because
//            we have to take correspond data depend on tag
            $companiesData = $companiesData->whereExists(function ($query) {
                $query->select('*')
                    ->from('taggables')
                    ->whereColumn('companies.id', 'taggables.taggable_id')
                    ->where('taggables.taggable_type', '=', 'App\\Models\\Company')
                    ->whereIn('taggables.tag_id', request()->tags);
            });
        }
        $companiesData = $companiesData->whereRaw('DATE(`date`) >= ?', [request()->start])
            ->whereRaw('DATE(`date`) <= ?', [request()->end])
            ->groupBy('billing_data.company_id')->get();

        $companiesData = $this->getData($companiesData);
        return $companiesData;

    }

//    public function getDashboardTotals($companies)
//    {
//        $totalAnswered = array_sum(array_column($companies, 'answered_calls'));
//        $totalMissed = array_sum(array_column($companies, 'missed_calls'));//todo missed calls seconds > $settings['missed_call_seconds'] to calculate as missed call
//        $timeAbove = array_sum(array_column($companies, 'time_above'));
//        //todo time above money not checking the custom values for each company,
//        // fix or let have only default values for time above money and seconds
//        $defaultFees = Setting::getProviderDefaults();
//        $chats = array_sum(array_column($companies, 'chats_count'));
//
//        return [
//            'answered' => $totalAnswered,
//            'missed' => $totalMissed,
//            'calls' => $totalAnswered + $totalMissed,
//            'bookings' => array_sum(array_column($companies, 'bookings_count')),
//            'chats' => $chats,
//            'timeAbove' => $timeAbove,
//            'timeAboveMoney' => $timeAbove * $defaultFees->where('slug', FeeType::FEE_TYPES['above_60_fee'])->first()->value,
//            'avg_talk_time' => round(array_sum(array_column($companies, 'talk_time')) / max($totalAnswered, 1)),
//            'avg_waiting_time' => round(array_sum(array_column($companies, 'waiting_time')) / max($totalAnswered, 1)),
//        ];
//    }

    private function getData($companies)
    {
        $bookingsDateFilter = $this->getDateFilter('bookings', 'added_at', 'WHERE');
        $chatsDateFilter = $this->getDateFilter('daily_chats', 'date_created', 'WHERE');
        $bookings = DB::select("SELECT COUNT(bookings.id) as bookings_count, contacts.company_id FROM bookings
                        INNER JOIN contacts ON bookings.contact_id = contacts.contact_id
                        $bookingsDateFilter group by company_id");
        $chats = DB::select("SELECT COUNT(daily_chats.id) as chats_count, departments.company_id FROM daily_chats
                        INNER JOIN departments ON daily_chats.department_id = departments.department_id AND departments.company_id IS NOT NULL
                        $chatsDateFilter group by company_id");

        $bookings = collect($bookings)->pluck('bookings_count', 'company_id')->all();
        $chats = collect($chats)->pluck('chats_count', 'company_id')->all();
        foreach ($companies as $company) {
            $this->checkExistingKey($company, 'company_id', $bookings, 'bookings_count');
            $this->checkExistingKey($company, 'company_id', $chats, 'chats_count');
        }

        return $companies;
    }

    private function checkExistingKey($current, $companyNumber, $checkingCollect, $collectField)
    {
        array_key_exists($current->$companyNumber, $checkingCollect)
            ? $current->$collectField = $checkingCollect[$current->$companyNumber]
            : $current->$collectField = 0;
    }
    //-------------- - - /Company Dashboard - - -----------------
}
