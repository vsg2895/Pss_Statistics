<?php

namespace App\Services\Reports;

use App\Models\Call;
use App\Models\Company;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CompanyJobsService
{
    private $start;
    private $end;
    private $providerFees;
    private $companyFees;
    private $defaultFees;
    private $feeTypeIdSlugs;
    private string $providerPrefix = 'p_';

    public function __construct($start = null, $end = null)
    {
        $this->start = $start ?? date('Y-m-d');
        $this->end = $end ?? date('Y-m-d');
        $this->providerFees = $this->getFees('providers');
        $this->companyFees = $this->getFees('companies');
        $this->defaultFees = Setting::getProviderDefaults()->pluck('value', 'slug')->toArray();
        $this->feeTypeIdSlugs = FeeType::get()->pluck('slug', 'id')->toArray();
    }

    public function getCompaniesData(): array
    {
        //get bookings
        $bookingsDateFilter = $this->getDateFilter('bookings', 'added_at', 'WHERE');
        $bookings = DB::select("SELECT COUNT(bookings.id) as bookings_count, contacts.company_id FROM bookings
                        INNER JOIN contacts ON bookings.contact_id=contacts.contact_id
                        $bookingsDateFilter group by company_id");

        $calls = Call::select('company_number', DB::raw("COUNT(id) as calls_count"))
            ->dateRange($this->start, $this->end)->groupBy('company_number')->get();

        $companyIds = collect($bookings)->pluck('company_id')->toArray();
        $companyIdsFromCalls = $calls->pluck('company_number')->toArray();
        $companyIds = array_unique(array_merge($companyIds, $companyIdsFromCalls));

        $companies = Company::select('id', 'company_id', 'service_provider_id', 'name as company_name')
            ->whereIn('company_id', $companyIds)
            ->with('fees.feeType')
            ->get()->toArray();

        foreach ($companies as &$company) {
            $this->setFees($company);
            $this->setBookingsCount($company, $bookings);
            $this->setCallsData($company, $calls);
            $company['date'] = Carbon::now();
        }

        return $companies;
    }

    private function setFees(&$company)
    {
        //if company belongs to provider
        //calls_fee will be amount we charge from provider
        //p_calls_fee will be amount provider charge from company
        if ($company['service_provider_id']) {
            $company = $this->setFeesWithProvider($company);
        }
        //calls_fee will be amount we charge from company
        //p_calls_fee will be nullable
        else {
            $company = $this->setFeesWithoutProvider($company);
        }

        //unset unnecessary keys
        unset($company['id']);
        unset($company['fees']);
    }

    //set provider custom fees, if missing, set defaults
    private function setFeesWithProvider($company)
    {
        foreach ($this->feeTypeIdSlugs as $feeTypeId => $feeTypeSlug) {
            //set fees, that provider charge from companies
            if (array_key_exists($company['service_provider_id'], $this->providerFees)
                && array_key_exists($feeTypeId, $this->providerFees[$company['service_provider_id']])) {//custom fee found
                $customFee = $this->providerFees[$company['service_provider_id']];
                $company[$this->providerPrefix . $feeTypeSlug] = $customFee[$feeTypeId];
//                dd($providerCustomFee, $this->feeTypeIdSlugs, $this->feeTypeSlugIds, $company);
            } else {//set default
                $company[$this->providerPrefix . $feeTypeSlug] = $this->defaultFees[$feeTypeSlug];
            }

            //set fees, that we charge from provider
            if (array_key_exists($company['id'], $this->companyFees)
                && array_key_exists($feeTypeId, $this->companyFees[$company['id']])) {//custom fee found
                $customFee = $this->companyFees[$company['id']];
                $company[$feeTypeSlug] = $customFee[$feeTypeId];
            } else {//set default
                $company[$feeTypeSlug] = $this->defaultFees[$feeTypeSlug];
            }
        }

        return $company;
    }

    //set provider custom fees, if missing, set defaults
    private function setFeesWithoutProvider($company)
    {
        foreach ($this->feeTypeIdSlugs as $feeTypeId => $feeTypeSlug) {
            //set fees, that provider charge from companies, in this case it is 0, as there is no provider
            $company[$this->providerPrefix . $feeTypeSlug] = 0;

            //set fees, that we charge from company
            $company[$feeTypeSlug] = $this->defaultFees[$feeTypeSlug];
        }

        return $company;
    }

    private function setBookingsCount(&$company, $bookings)
    {
        $bookings = collect($bookings)->pluck('bookings_count', 'company_id')->all();
        $company['bookings_count'] = array_key_exists($company['company_id'], $bookings)
            ? $bookings[$company['company_id']]
            : 0;
    }

    private function setCallsData(&$company, $calls)
    {
        //set calls count
        $calls = collect($calls)->pluck('calls_count', 'company_number')->all();
        $company['calls_count'] = array_key_exists($company['company_id'], $calls)
            ? $calls[$company['company_id']]
            : 0;

        //set time above seconds
        $callsDateFilter = $this->getDateFilter('calls', 'started_at');
        $timeAboveSeconds = $company['time_above_seconds'];
        $pTimeAboveSeconds = $company['p_time_above_seconds'];
        $companyId = $company['company_id'];

        $callSeconds = DB::select("SELECT
            SUM(IF(TIMESTAMPDIFF(second, calls.connected_at, calls.hang_up_at) - $timeAboveSeconds > 0, TIMESTAMPDIFF(second, calls.connected_at, calls.hang_up_at) - $timeAboveSeconds, 0)) as time_above_seconds,
            SUM(IF(TIMESTAMPDIFF(second, calls.connected_at, calls.hang_up_at) - $pTimeAboveSeconds > 0, TIMESTAMPDIFF(second, calls.connected_at, calls.hang_up_at) - $pTimeAboveSeconds, 0)) as p_time_above_seconds
            from calls
            where company_number = $companyId
            $callsDateFilter
            ");

        $company['time_above_seconds_value'] = isset($callSeconds[0]) ? $callSeconds[0]->time_above_seconds : 0;
        $company['p_time_above_seconds_value'] = isset($callSeconds[0]) ? $callSeconds[0]->p_time_above_seconds : 0;
    }

    private function getDateFilter($table, $column, $query = 'AND'): string
    {
        $today = "'" . $this->start . "'";
        $dateFilter = " $query DATE(`$table`.`$column`) = $today";
        if ($this->start !== $this->end) {
            $start = "'" . $this->start . "'";
            $end = "'" . $this->end . "'";
            $dateFilter = " $query DATE(`$table`.`$column`) >= $start AND DATE(`$table`.`$column`) <= $end";
        }

        return $dateFilter;
    }

    private function getFees($type)
    {
        $feesRes = [];
        $column = match ($type) {
            'companies' => 'company_id',
            default => 'service_provider_id',
        };
        $fees = Fee::select('fee_type_id', $column, 'fee')->whereNotNull($column)->get()
            ->mapToGroups(function ($item) use ($column) {
                return [$item->{$column} => [
                    $item->fee_type_id => $item->fee,
                ]];
            })->toArray();


        foreach ($fees as $id => $feez) {
            foreach ($feez as $fee) {
                $feesRes[$id][key($fee)] = $fee[key($fee)];
            }
        }

        return $feesRes;
    }

    public function getTotals($companies): array
    {
        $totalAnswered = array_sum(array_column($companies, 'answered_calls'));
        $totalBookings = array_sum(array_column($companies, 'bookings_count'));
        $timeAbove = get_hour_format(array_sum(array_column($companies, 'time_above')));
        $callsRevenue = array_sum(array_column($companies, 'calls_revenue'));
        $bookingsRevenue = array_sum(array_column($companies, 'bookings_revenue'));
        $totalIncome = array_sum(array_column($companies, 'income')) ?: 1;


        $monthlyFeeIncome = array_sum(array_column($companies, 'monthly_fee_income'));
        $monthlyFeeCharge = array_sum(array_column($companies, 'monthly_fee_charge'));
        $monthlyFeeRevenue = array_sum(array_column($companies, 'monthly_fee_revenue'));

        $totalRevenue  = array_sum(array_column($companies, 'revenue')) + $monthlyFeeRevenue;
        $incomeVsRevenue = round($totalRevenue * 100 / $totalIncome);

        $floatingFeesIncome = array_sum(array_column($companies, 'calls_income'))
            + array_sum(array_column($companies, 'bookings_income'));

        return [
            'totalAnswered' => $totalAnswered,
            'totalRevenue'  => $totalRevenue,
            'totalBookings' => $totalBookings,
            'timeAbove' => $timeAbove,
            'totalCallsRevenue' => $callsRevenue,
            'totalBookingsRevenue' => $bookingsRevenue,
            'incomeVsRevenue' => $incomeVsRevenue,
            'monthlyFeeIncome' => $monthlyFeeIncome,
            'monthlyFeeCharge' => $monthlyFeeCharge,
            'monthlyFeeRevenue' => $monthlyFeeRevenue,
            'floatingFeesIncome' => round($floatingFeesIncome),
        ];
    }

    private function getData($companies): array
    {
        $feeTypes = FeeType::get()->pluck('id', 'slug')->toArray();
        $companyIds = collect($companies)->pluck('id')->toArray();
        $fees = Fee::whereIn('company_id', $companyIds)->get();
        $bookingsDateFilter = $this->getDateFilter('bookings', 'added_at', 'WHERE');

        $bookings = DB::select("SELECT COUNT(bookings.id) as bookings_count, contacts.company_id FROM bookings
                        INNER JOIN contacts ON bookings.contact_id=contacts.contact_id
                        $bookingsDateFilter group by company_id");
        $bookings = collect($bookings)->pluck('bookings_count', 'company_id')->all();

//        dd($bookings);
        foreach ($companies as $company) {
            //set calls money
            $callsMoney = $this->getCallsMoney($company, $feeTypes, $fees);
            $earnings['calls_money'] = $callsMoney;
            $company->calls_income = $callsMoney['income'];
            $company->calls_revenue = $callsMoney['income'] - $callsMoney['charge'];

            //set bookings_count
            array_key_exists($company->company_id, $bookings)
                ? $company->bookings_count = $bookings[$company->company_id]
                : $company->bookings_count = 0;

            //set bookings money
            $bookingsMoney = $company->bookings_count ? $this->getBookingsMoney($company, $feeTypes, $fees) : [
                'income' => 0,
                'charge' => 0,
            ];
            $earnings['bookings_money'] = $bookingsMoney;
            $company->bookings_income = $bookingsMoney['income'];
            $company->bookings_revenue = $bookingsMoney['income'] - $bookingsMoney['charge'];

            //set monthly fee
            $monthlyFeeMoney = $this->getMonthlyFee($company, $feeTypes, $fees);
            $company->monthly_fee_income = $monthlyFeeMoney['income'];
            $company->monthly_fee_charge = $monthlyFeeMoney['charge'];
            $company->monthly_fee_revenue = $monthlyFeeMoney['income'] - $monthlyFeeMoney['charge'];

            $company->income = array_sum(array_column($earnings, 'income'));
            $company->charge = array_sum(array_column($earnings, 'charge'));
            $company->revenue = array_sum(array_column($earnings, 'income')) - array_sum(array_column($earnings, 'charge'));
        }

        return $companies;
    }

    // call fees calculation
    private function getCallsMoney($company, $feeTypes, $fees)
    {
        $monthStart = Carbon::now()->startOfMonth()->format("Y-m-d");
        $currentMonthCallsCount = Call::answered()->where('company_number', $company->company_id)
            ->whereRaw('DATE(started_at) >= ?', [$monthStart])->count();
        $freeCallsCount = $this->getFreeCallsCount($company->id, $feeTypes, $fees);

        //get value to charge from companies
        $callsToCharge = $currentMonthCallsCount - $freeCallsCount['income'];
        $callsCustomFee = $fees->where('company_id', $company->id)->where('fee_type_id', $feeTypes['calls_fee'])->first();
        $callsIncome = 0;
        if ($callsToCharge > 0) {
            if ($callsCustomFee) {//todo fix or check, what if todays' some calls inside free calls count, rest need to be paid
                $callsIncome = $company->answered_calls * $callsCustomFee->fee;
            } else {
                $callsIncome = $company->answered_calls * $this->defaultFees->where('slug', FeeType::FEE_TYPES['calls_fee'])->first()->value;
            }
        }

        //get value to charge from companies
        $callsToCharge = $currentMonthCallsCount - $freeCallsCount['charge'];
        $callsCustomFee = $this->providerCustomFees->where('fee_type_id', $feeTypes['calls_fee'])->first();
        $callsCharge = 0;
        if ($callsToCharge > 0) {
            if ($callsCustomFee) {
                $callsCharge = $company->answered_calls * $callsCustomFee->fee;
            } else {
                $callsCharge = $company->answered_calls * $this->providerDefaultFees->where('slug', FeeType::FEE_TYPES['calls_fee'])->first()->value;
            }
        }

        $timeAboveMoney = intval($company->time_above) ? $this->getTimeAboveMoney($company, $feeTypes, $fees) : [
            'income' => 0,
            'charge' => 0,
        ];

        return [
            'income' => $callsIncome + $timeAboveMoney['income'],
            'charge' => $callsCharge + $timeAboveMoney['charge'],
        ];
    }

    private function getFreeCallsCount($companyId, $feeTypes, $fees)
    {
        $freeCallsFeeTypeId = $feeTypes['free_calls'];
        $freeCallsCustomFee = $fees->where('company_id', $companyId)->where('fee_type_id', $freeCallsFeeTypeId)->first();

        //get count from company
        if ($freeCallsCustomFee) { //get custom free calls count for that company
            $freeCallsCount = $freeCallsCustomFee->fee;
        } else { //if there is no custom value, get it from default settings set by provider
            $freeCallsCount = $this->defaultFees->where('slug', FeeType::FEE_TYPES['free_calls'])->first()->value;
        }

        $freeCallsCustomFeeProvider = $this->providerCustomFees->where('fee_type_id', $freeCallsFeeTypeId)->first();
        //get count from provider
        if ($freeCallsCustomFeeProvider) { //get custom free calls count for provider
            $freeCallsCountCharge = $freeCallsCustomFeeProvider->fee;
        } else { //if there is no custom value, get it from default settings set by admin(from admin settings)
            $freeCallsCountCharge = $this->providerDefaultFees->where('slug', FeeType::FEE_TYPES['free_calls'])->first()->value;
        }

        return [
            'income' => $freeCallsCount,
            'charge' => $freeCallsCountCharge,
        ];
    }

    private function getTimeAboveMoney($company, $feeTypes, $fees)
    {
        $timeAboveFeeTypeId = $feeTypes['above_60_fee'];
        $timeAboveSeconds = intval($company->time_above);

        //get income from company
        $timeAboveCustomFee = $fees->where('company_id', $company->id)->where('fee_type_id', $timeAboveFeeTypeId)->first();
        if ($timeAboveCustomFee) { //get custom free calls count for that company
            $timeAboveIncome = $timeAboveSeconds * $timeAboveCustomFee->fee;
        } else { //if there is no custom value, get it from default settings set by provider
            $timeAboveIncome = $timeAboveSeconds * $this->defaultFees->where('slug', FeeType::FEE_TYPES['above_60_fee'])->first()->value;
        }

        //get charge amount from provider
        $timeAboveCustomFee = $this->providerCustomFees->where('fee_type_id', $timeAboveFeeTypeId)->first();
        if ($timeAboveCustomFee) { //get custom free calls count for provider
            $timeAboveCharge = $timeAboveSeconds * $timeAboveCustomFee->fee;
        } else { //if there is no custom value, get it from default settings set by admin(from admin settings)
            $timeAboveCharge = $timeAboveSeconds * $this->providerDefaultFees->where('slug', FeeType::FEE_TYPES['above_60_fee'])->first()->value;
        }

        return [
            'income' => $timeAboveIncome,
            'charge' => $timeAboveCharge,
        ];

    }
    // end of call fees calculation

    // booking fees calculation
    private function getBookingsMoney($company, $feeTypes, $fees)
    {
        $bookingsFeeTypeId = $feeTypes['bookings_fee'];
        $bookingsCustomFee = $fees->where('company_id', $company->id)->where('fee_type_id', $bookingsFeeTypeId)->first();
        $bookingsCount = $company->bookings_count;

        //get count from company
        if ($bookingsCustomFee) { //get custom free calls count for that company
            $bookingsIncome = $bookingsCount * $bookingsCustomFee->fee;
        } else { //if there is no custom value, get it from default settings set by provider
            $bookingsIncome = $bookingsCount * $this->defaultFees->where('slug', FeeType::FEE_TYPES['bookings_fee'])->first()->value;
        }

        $bookingsCustomFeeProvider = $this->providerCustomFees->where('fee_type_id', $bookingsFeeTypeId)->first();
        //get count from provider
        if ($bookingsCustomFeeProvider) { //get custom free calls count for provider
            $bookingsCharge = $bookingsCount * $bookingsCustomFeeProvider->fee;
        } else { //if there is no custom value, get it from default settings set by admin(from admin settings)
            $bookingsCharge = $bookingsCount * $this->providerDefaultFees->where('slug', FeeType::FEE_TYPES['bookings_fee'])->first()->value;
        }

        return [
            'income' => $bookingsIncome,
            'charge' => $bookingsCharge,
        ];
    }
    // end of booking fees calculation

    private function getMonthlyFee($company, $feeTypes, $fees)
    {
        $monthlyFeeTypeId = $feeTypes['monthly_fee'];

        $monthlyCustomFee = $fees->where('company_id', $company->id)->where('fee_type_id', $monthlyFeeTypeId)->first();
        if ($monthlyCustomFee)
            $monthlyFeeIncome = $monthlyCustomFee->fee;
        else
            $monthlyFeeIncome = $this->defaultFees->where('slug', FeeType::FEE_TYPES['monthly_fee'])->first()->value;

        $monthlyCustomFeeProvider = $this->providerCustomFees->where('fee_type_id', $monthlyFeeTypeId)->first();
        if ($monthlyCustomFeeProvider)
            $monthlyFeeCharge = $monthlyCustomFeeProvider->fee;
        else
            $monthlyFeeCharge = $this->providerDefaultFees->where('slug', FeeType::FEE_TYPES['monthly_fee'])->first()->value;

        return [
            'income' => $monthlyFeeIncome,
            'charge' => $monthlyFeeCharge,
        ];
    }
}
