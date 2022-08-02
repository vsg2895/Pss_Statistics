<?php

namespace App\Services;

use App\Models\BillingData;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\ServiceProviderSettings;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use mysql_xdevapi\Exception;

class BaseService
{
    public $company;
    public $provider;
    public string $start;
    public string $end;
    public string $type;

    protected function getDateFilter($table, $column, $query = 'AND'): string
    {
        $today = "'" . date('Y-m-d') . "'";
        $dateFilter = " $query DATE(`$table`.`$column`) = $today";
        if (request()->start) {
            $start = "'" . request()->start . "'";
            $end = "'" . request()->end . "'";
            $dateFilter = " $query DATE(`$table`.`$column`) >= $start AND DATE(`$table`.`$column`) <= $end";
        }

        return $dateFilter;
    }

    protected function getDateFilterCompare($table, $column, $start, $end, $query = 'AND'): string
    {
        $today = "'" . date('Y-m-d') . "'";
        $dateFilter = " $query DATE(`$table`.`$column`) = $today";
        if ($start) {
            $dateFilter = " $query DATE(`$table`.`$column`) >= '$start' AND DATE(`$table`.`$column`) <= '$end'";
        }

        return $dateFilter;
    }

    protected function addDataInGeneralArray($generalArray, $keyArray, $appendedAssoc, $position = null)
    {
        if (is_null($position)) {
            $generalArray[$keyArray] = $appendedAssoc;
        } else {
            $saveElem = $generalArray[$position];
            unset($generalArray[$position]);
            $generalArray[$keyArray] = $appendedAssoc;
            $generalArray[$position] = $saveElem;
        }

        return $generalArray;
    }

    public function setParams($company = [], $type = 'company', $start = null, $end = null, $provider = null)
    {
        $this->company = $company;
        $this->provider = $provider;
        $this->type = $type;
        $this->start = $start ?: date('Y-m-d');
        $this->end = $end ?: date('Y-m-d');
//        $this->start = '2022-01-27';
//        $this->end = '2022-01-27';

    }

    public function getGeneralTypesProfit($startDate, $nowDate, $freeCount = false)
    {
        $defaultsWe = Setting::getDefaultsBillingRow(FeeType::FEE_TYPES_IN_INSERT);
        $defaultsProviderCompany = ServiceProviderSettings::getProviderToCompanyDefaults(FeeType::FEE_TYPES_IN_INSERT);
        $feeProviderCompany = Fee::getProviderCompanyCustomAll();
        $feeWeProvide = Fee::getProviderCustomAll();
        $feeWeCompany = Fee::getCompanyCustomAll();
        $issetFreeCountFree = [];
        $issetFreeCountP_free = [];
//            check in selected interval free call count
        $issetFreeCount = BillingData::select()->selectRaw("COUNT(id) as calls_count")
            ->whereRaw('DATE(date) >= ?', [$startDate])->whereRaw('DATE(date) <= ?', [$nowDate])
            ->where('status', 'AN');
        if ($freeCount) {
            $issetFreeCountFree = $issetFreeCount->where('free_call', 1)->whereNull('deleted_at')->groupBy('company_id')->pluck('calls_count', 'company_id')->toArray();
            $issetFreeCountP_free = $issetFreeCount->where('p_free_call', 1)->whereNull('deleted_at')->groupBy('company_id')->pluck('calls_count', 'company_id')->toArray();
        } else {
            $issetFreeCount = $issetFreeCount->whereNull('deleted_at')->groupBy('company_id')->pluck('calls_count', 'company_id')->toArray();
        }

        return [
            'defaultsWe' => $defaultsWe,
            'defaultsProviderCompany' => $defaultsProviderCompany,
            'feeProviderCompany' => $feeProviderCompany,
            'feeWeProvider' => $feeWeProvide,
            'feeWeCompany' => $feeWeCompany,
            'issetFreeCount' => !$freeCount ? $issetFreeCount
                : ['free' => $issetFreeCountFree, 'p_free' => $issetFreeCountP_free],
        ];
    }

    public function allTypePricesByCompany($companyId, $company, $defaultsWe, $defaultsProviderCompany, $feeWeCompany, $feeWeProvider, $feeProviderCompany): bool|array
    {
        try {
            if (!is_null($company)) {
                //  we charge default
                $defaults['we'] = $defaultsWe->pluck('value', 'slug');
                if ($company->service_provider_id) {
                    //  provider charge to company custom
                    $fees['provider_company'] = $feeProviderCompany->where('company_id', $company->id)
                        ->where('service_provider_id', $company->service_provider_id)->pluck('fee', 'feeType.slug');
                    //  we charge custom
                    $fees['we'] = $feeWeProvider->where('service_provider_id', $company->service_provider_id)->pluck('fee', 'feeType.slug');
                    //  provider charge to company default
                    $defaults['provider_company'] = $defaultsProviderCompany->where('service_provider_id', $company->service_provider_id)
                        ->pluck('value', 'slug');
                } else {
                    $fees['provider_company'] = NULL;
                    //  we charge custom
                    $fees['we'] = $feeWeCompany->where('company_id', $company->id)->pluck('fee', 'feeType.slug');
                    //  provider charge to company default
                    $defaults['provider_company'] = NULL;
                }
                foreach ($fees as $key => $value) {
                    if (!is_null($value) && count($value) > 0) {
                        foreach ($value as $key_elem => $elem) {
                            if (!is_null($defaults[$key]) && array_key_exists($key_elem, $defaults[$key]->all())) {
                                $defaults[$key][$key_elem] = $elem;
                            }
                        }
                    }
                }
            } else {
                Log::info('Company Not Found In Id : ' . $companyId);
                $defaults = false;
            }

            return $defaults;
        } catch (\Exception $exception) {
            Log::info('Something was wrong set prices by company : ' . $companyId .
                'In line ' . $exception->getLine() . $exception->getMessage());
            return false;
        }

    }

    public function delPrefixKey(&$array, $prefix): array
    {
        return array_combine(
            array_map(function ($k) use ($prefix) {
                if (substr($k, 0, 2) === $prefix) {
                    $k = str_replace($prefix, '', $k);
                }
                return $k;
            }, array_keys($array)), $array
        );

    }

    public function setTotalIncome($cdrStatistics, $appendedName, $baseName, $originalPricesKey, $pricesKey, $arrayKeys): array
    {
        foreach ($arrayKeys as $key) {
            $cdrStatistics[$baseName][$pricesKey][$key] =
                number_format((float)$cdrStatistics[$baseName][$originalPricesKey][$key] + (float)$cdrStatistics[$appendedName][$originalPricesKey][$key], 2, '.', ' ');

            $cdrStatistics[$baseName][$originalPricesKey][$key] =
                $cdrStatistics[$baseName][$originalPricesKey][$key] + (float)$cdrStatistics[$appendedName][$originalPricesKey][$key];
        }
        return $cdrStatistics;
    }

    public function createApiAddress($basePart, $params, $dynamicArgument): string
    {
        $api = $basePart . $dynamicArgument;
        foreach ($params as $key => $param) {
            $api = $key == 0 ? $api . '?' . $param : $api . '&' . $param;
        }

        return $api;
    }


}
