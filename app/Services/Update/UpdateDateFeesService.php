<?php

namespace App\Services\Update;


use App\Models\BillingData;
use App\Models\Company;
use App\Models\DailyChat;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\ServiceProviderSettings;
use App\Models\Setting;
use App\Services\BaseService;
use App\Services\Insert\SetBillingPricesService;
use Carbon\Carbon;
use DateInterval;
use Illuminate\Support\Facades\DB;

class UpdateDateFeesService extends BaseService
{
    public $priceField = 'price';
//    change free_calls in billing data fields
    public $freeBaseField = 'free_call';
    public $freeCallField = 'free_calls';
    public $chatField = 'chats_fee';
    public $oldAnswer = 'calls_fee';
    public $changeType = 'we';
    public SetBillingPricesService $setBillingPricesService;

    public function __construct(SetBillingPricesService $setBillingPricesService)
    {
        $this->setBillingPricesService = $setBillingPricesService;
    }

    public function initialGetDataFromUpdate($checks, $company, $start, $end, $setType, $departmentIds = null)
    {
        $this->setParams($company, $setType, $start, $end);

        return [
            'updated' => array_intersect_key($checks, FeeType::FEE_TYPES_UPDATE_DATA_FEES),
            'data' => $this->getBillingDataByDateFilter('all'),
            'delIds' => $this->getBillingDataByDateFilter('ids'),
            'departmentIds' => !is_null($departmentIds) ? array_unique($departmentIds) : $departmentIds
        ];
    }

    public function importUpdate($start, $end, $company, $data, $checks, $updated, $oldValuesFee, $chat_fee, $p_chat_fee, $departmentIds = null)
    {
        $insertData = [];
        $insertDataFee = [];
        $generalFeeTypes = $this->getGeneralTypesProfit($start, $end);
        $feePriceVariants = $this->allTypePricesByCompany($company->company_id, $company, $generalFeeTypes['defaultsWe'], $generalFeeTypes['defaultsProviderCompany'],
            $generalFeeTypes['feeWeCompany'], $generalFeeTypes['feeWeProvider'], $generalFeeTypes['feeProviderCompany']);
        $priceKey = is_array($this->company) || is_null($company->service_provider_id) ? 'we' : 'provider_company';
        $updatedFessPrices = $this->getUpdatedFees($checks, $feePriceVariants, $priceKey, FeeType::FEE_TYPES_NAMES_BILLING_DATA_FEES, false);
        $feePriceVariants[$priceKey] = $updatedFessPrices['updatedFess'];
        foreach ($data as $elem) {
            $dataTables = $this->setBillingPricesService->setPricesImportUpdate($elem, $checks, $feePriceVariants,
                $generalFeeTypes['defaultsWe'], true);
            $insertData[] = $dataTables['billingData'];
            if (isset($dataTables['billingDataFee'][$this->chatField])) {
                unset($dataTables['billingDataFee'][$this->chatField]);
            }
            $insertDataFee[] = $dataTables['billingDataFee'];
        }

        return [
            'billingData' => $insertData,
            'billingDataFees' => $insertDataFee,
        ];
    }

    public function hystoricalUpdate($start, $end, $company, $data, $checks, $updated, $oldValuesFee, $departmentIds = null)
    {
        $insertData = [];
        $insertDataFee = [];
        $generalFeeTypes = $this->getGeneralTypesProfit($start, $end);
        $feePriceVariants = $this->allTypePricesByCompany($company->company_id, $company, $generalFeeTypes['defaultsWe'], $generalFeeTypes['defaultsProviderCompany'],
            $generalFeeTypes['feeWeCompany'], $generalFeeTypes['feeWeProvider'], $generalFeeTypes['feeProviderCompany']);
        $priceKey = is_array($this->company) || is_null($company->service_provider_id) ? 'we' : 'provider_company';
        $updatedFessPrices = $this->getUpdatedFees($checks, $feePriceVariants, $priceKey, FeeType::FEE_TYPES_NAMES_BILLING_DATA_FEES);
        $feePriceVariants[$priceKey] = $updatedFessPrices['updatedFess'];
        $updatedFessPrices['getOldValuesKeys'][] = 'billing_id';
        $oldValuesFee = $oldValuesFee->get($updatedFessPrices['getOldValuesKeys'])->groupBy('billing_id');
        $oldValuesFee = $oldValuesFee->map(function ($item) {
            unset($item[0]->billing_id);
            return $item->sole();
        })->toArray();

        $oldChangedValuesFee = [];
        $intersectUpdated = $priceKey === "provider_company" ? $this->addPrefixKeys($updated) : $updated;

        foreach ($oldValuesFee as $key => $value) {
            $oldChangedValuesFee[$key] = array_intersect_key(json_decode(json_encode($value), true), $intersectUpdated);
        }
        foreach ($data as $elem) {
            $dataTables = $this->setBillingPricesService->setPricesUpdate($elem, $checks, $feePriceVariants,
                $generalFeeTypes['defaultsWe'], $oldValuesFee, $this->changeType, true);
            $insertData[] = $dataTables['billingData'];
            if (isset($dataTables['billingDataFee'][$this->chatField])) {
                unset($dataTables['billingDataFee'][$this->chatField]);
            }
            $insertDataFee[] = $dataTables['billingDataFee'];
        }
        $checkMonths = $this->checkMonths($start, $end);
        $insertData = collect($insertData);

        $freeUpdate = $this->updateFreeCalls($checkMonths, $insertData, $feePriceVariants[$priceKey], $oldValuesFee);

        $chatUpdate = $updated['chats_fee'] ?? null;
        $freeCallUpdate = $updated['free_calls'] ?? null;

        $chatOldValue = [];
        if (!is_null($chatUpdate)) {
            $chatOldValue = $this->chatOldValue($this->priceField, $departmentIds);
            $this->updateChatsFees($this->priceField, $chatUpdate, $departmentIds);
        }
        $freeUpdate = json_decode(json_encode($freeUpdate), true);

        return [
            'billingData' => $freeUpdate,
            'billingDataFees' => $insertDataFee,
            'oldChangedValues' => $oldChangedValuesFee,
            'chatOldValue' => $chatOldValue,
            'freeCallUpdate' => count($checkMonths) && !is_null($freeCallUpdate)
        ];
    }

    public function updateFreeCalls($months, $billingDataAll, $feePriceVariants, $oldValuesFee)
    {
        foreach ($months as $m) {
            $m['start'] = $m['start'] . ' 00:00:00';
            $m['end'] = $m['end'] . ' 23:59:59';
            $currentFreeCount = $this->getFreeCallData($billingDataAll, $m['start'], $m['end'], $this->freeBaseField, 1, 1);
            if (isset($feePriceVariants[$this->freeCallField]) && (int)$feePriceVariants[$this->freeCallField] !== $currentFreeCount) {
                $difFree = (int)$feePriceVariants[$this->freeCallField] > $currentFreeCount
                    ? (int)$feePriceVariants[$this->freeCallField] - $currentFreeCount
                    : $currentFreeCount - (int)$feePriceVariants[$this->freeCallField];
                if ((int)$feePriceVariants[$this->freeCallField] > $currentFreeCount) {
                    $billingDataAll->where('date', '>=', $m['start'])
                        ->where('date', '<=', $m['end'])
                        ->where($this->freeBaseField, 0)
                        ->whereNull('deleted_at')
                        ->where('status', 'AN')->take($difFree)->map(function ($cf) use ($feePriceVariants, $oldValuesFee) {
                            $minusCallsFee = isset($feePriceVariants['calls_fee'])
                                ? (float)$feePriceVariants['calls_fee']
                                : $oldValuesFee[$cf->id]->{$this->oldAnswer};
                            $cf->{$this->freeBaseField} = 1;
                            $cf->{$this->priceField} = $cf->{$this->priceField} - $minusCallsFee;

                            return $cf;
                        });
                } else {
                    $billingDataAll->where('date', '>=', $m['start'])
                        ->where('date', '<=', $m['end'])
                        ->where($this->freeBaseField, 1)->whereNull('deleted_at')->where('status', 'AN')->reverse()->take($difFree)->map(function ($cf) use ($feePriceVariants, $oldValuesFee) {
                            $plusCallsFee = isset($feePriceVariants['calls_fee'])
                                ? (float)$feePriceVariants['calls_fee']
                                : $oldValuesFee[$cf->id]->{$this->oldAnswer};
                            $cf->{$this->freeBaseField} = 0;
                            $cf->{$this->priceField} = $cf->{$this->priceField} + $plusCallsFee;

                            return $cf;
                        });
                }

            }
        }

        return $billingDataAll;
    }

    public function getFreeCallsCountByCompany($company): array
    {
        $freeCount = BillingData::select()->selectRaw("COUNT(id) as calls_count")
            ->whereRaw('DATE(date) >= ?', [$this->start])->whereRaw('DATE(date) <= ?', [$this->end])
            ->where('status', 'AN')
            ->whereNull('deleted_at');
        $freeCount = !is_array($company)
            ? $freeCount->where('company_id', $company->company_id)
            : $freeCount->whereIn('company_id', $company)->groupBy('company_id');
        $freeCount = $freeCount->where($this->freeBaseField, 1)
            ->pluck('calls_count')->toArray();
        if (count($freeCount)) {
            return [
                is_array($company) ? max($freeCount) : $freeCount[0]
            ];
        } else {
            return $freeCount;
        }

    }

    public function getFreeCallData($array, $start, $end, $freeField, $freeFlag = null, $freeCount = null): int|\Illuminate\Support\Collection
    {
        $result = collect($array)
            ->where('date', '>=', $start)
            ->where('date', '<=', $end);

        $result = $freeFlag ? $result->where($freeField, 1) : $result->where($freeField, 0);

        return $freeCount ? $result->count() : $result;
    }

    public function chatOldValue($priceField, $departmentIds)
    {
        return !is_null($departmentIds)
            ? DailyChat::whereIn('department_id', $departmentIds)
                ->whereBetween('date', [$this->start, $this->end])
                ->pluck('daily_chats' . '.' . $priceField, 'daily_chats.department_id')->unique()->toArray()
            : $this->company->getChatsByDate($this->start, $this->end)
                ->pluck('daily_chats' . '.' . $priceField, 'daily_chats.department_id')->unique()->toArray();
    }

    public function updateChatsFees($priceField, $chatUpdate, $departmentIds)
    {
        return !is_null($departmentIds)
            ? DailyChat::whereIn('department_id', $departmentIds)
                ->whereBetween('date', [$this->start, $this->end])
                ->update([$priceField => (float)$chatUpdate])
            : $this->company->getChatsByDate($this->start, $this->end)
                ->update([$priceField => (float)$chatUpdate]);

    }

    public function getUpdatedFees(&$checks, $feePriceVariants, $priceKey, $getOldValuesKeys, $changeKey = true)
    {
        $updatedFess = array_intersect_key($checks, $feePriceVariants[$priceKey]->toArray());
        $updatedFess = array_merge($feePriceVariants[$priceKey]->toArray(), $updatedFess);

        if (!is_array($this->company) && !is_null($this->company->service_provider_id) && $changeKey) {
//            add all keys "p_" prefix but updating providers fees values
            $this->priceField = 'provider_price';
            $this->freeBaseField = 'p_free_call';
            $this->chatField = 'p_chats_fee';
            $this->oldAnswer = 'p_calls_fee';
            $this->changeType = 'provider_company';
            $getOldValuesKeys = $this->addPrefixKeys($getOldValuesKeys);
            $updatedFess = $this->addPrefixKeys($updatedFess);
            $checks = $this->addPrefixKeys($checks);
        }

        return [
            'updatedFess' => $updatedFess,
            'getOldValuesKeys' => array_keys($getOldValuesKeys)
        ];
    }

    public function getDbCurrentFees($company, $departmentIds = null): array
    {
        $feesArray = [];
        $allUpdatedData = $this->getBillingDataByDateFilter('all', 'join');
        $gettingKeys = is_array($this->company) || is_null($this->company->service_provider_id)
            ? array_keys(FeeType::FEE_TYPES_NAMES_BILLING_DATA_FEES)
            : array_keys($this->addPrefixKeys(FeeType::FEE_TYPES_NAMES_BILLING_DATA_FEES));
        $this->changeKeysByProvider();
        foreach ($allUpdatedData as $item) {
            foreach (collect($item)->only($gettingKeys) as $key => $value) {
                $feesArray[$key][] = $value;
            }
        }
        $chatsFees = !is_array($company)
            ? $company->getChatsByDate($this->start, $this->end)
                ->pluck($this->priceField)->toArray()
            : DailyChat::whereIn('department_id', $departmentIds)
                ->whereBetween('date', [$this->start, $this->end])
                ->pluck($this->priceField)->toArray();

        $freeCount = $this->getFreeCallsCountByCompany($company);
        if (count($chatsFees)) {
            $feesArray[$this->chatField] = array_unique($chatsFees);
        }
        $feesArray[$this->freeCallField] = array_unique($freeCount);

        foreach ($feesArray as $key => $item) {
            $feesArray[$key] = array_unique($item);
        }

        return $feesArray;
    }


    public function changeKeysByProvider(): void
    {
        if (!is_array($this->company) && !is_null($this->company->service_provider_id)) {
            $this->priceField = 'provider_price';
            $this->freeBaseField = 'p_free_call';
            $this->freeCallField = 'p_free_calls';
            $this->chatField = 'p_chats_fee';
            $this->oldAnswer = 'p_calls_fee';
            $this->changeType = 'provider_company';
        }

    }

    public function getBillingDataByDateFilter($type, $join = null, $ids = null)
    {
        $query = DB::table('billing_data')->whereNull('billing_data.deleted_at');
        if (is_null($join)) {
            $query = $query->select('id', 'call_id', 'duration', 'a_number', 'b_number', 'status', 'message', 'sms', 'email',
                'booking', 'agent_id', 'site_id', 'company_id', 'free_call', 'p_free_call', 'date', 'created_at', 'updated_at');

        } else {
            $query = $query->select('billing_data.id', 'billing_data.call_id', 'billing_data.duration', 'billing_data.a_number', 'billing_data.b_number', 'billing_data.status', 'billing_data.message', 'billing_data.sms', 'billing_data.email',
                'billing_data.booking', 'billing_data.agent_id', 'billing_data.site_id', 'billing_data.company_id', 'billing_data.free_call', 'billing_data.p_free_call', 'billing_data.date', 'billing_data.created_at', 'billing_data.deleted_at',
                'billing_data.updated_at', 'billing_data_fees.*')
                ->join('billing_data_fees', 'billing_data.id', '=', 'billing_data_fees.billing_id');
        }
        $query = $query->whereRaw('DATE(`date`) >= ?', [$this->start])->whereRaw('DATE(`date`) <= ?', [$this->end]);

        if (!is_null($ids)) {
            $query = $query->whereIn('billing_data.id', $ids);
        }
        return $type == 'all' ? $this->getBillingFilterVariantOnType($query)
            : $this->getBillingFilterVariantOnType($query)->pluck('id')->toArray();

    }

    public function addPrefixKeys($array): array
    {
        return array_combine(
            array_map(function ($k) {
                return 'p_' . $k;
            }, array_keys($array)), $array
        );
    }

    private function getBillingFilterVariantOnType($query)
    {
        $companyId = is_array($this->company) ? $this->company : $this->company->company_id;

        return match ($this->type) {
            'update_company' => $query->where('company_id', $companyId)
                ->get(),
            'update_provider', 'update_companies' => $query->whereIn('company_id', $companyId)
                ->get()
        };
    }

    public function checkMonths($start, $end): array
    {
        $interval = DateInterval::createFromDateString('1 month');
        $period = \Carbon\CarbonPeriod::create($start, $interval, $end);
        $searchIntervals = [];

        foreach ($period as $date) {
            $months[] = $date->format("Y-m");
        }

        $periodStartD = \Carbon\CarbonPeriod::create($start, '1 month', $end)->getStartDate()->day;
        $periodEndD = \Carbon\CarbonPeriod::create($start, '1 month', $end)->getEndDate()->day;
        $periodEndMonthEnd = \Carbon\CarbonPeriod::create($start, '1 month', $end)->getEndDate()->endOfMonth()->day;
        $monthsCount = count($months);

        if ($periodStartD != 1 && isset($months[0])) {
            unset($months[0]);
        }
        if ($periodEndD != $periodEndMonthEnd) {
            unset($months[$monthsCount - 1]);
        }
        foreach ($months as $month) {
            $searchIntervals[] = [
                'start' => Carbon::createFromFormat('Y-m', $month)->startOfMonth()->format('Y-m-d'),
                'end' => Carbon::createFromFormat('Y-m', $month)->endOfMonth()->format('Y-m-d')
            ];
        }

        return $searchIntervals;
    }

    public function delBillingDataByIds($ids): void
    {
        DB::table('billing_data')->whereIn('id', $ids)->delete();
        DB::table('billing_data_fees')->whereIn('billing_id', $ids)->delete();
    }

    public function updateFeesGeneral($fees, $feeTypeIds, $changedObj, $conditionField): void
    {
        $typeObj = $changedObj instanceof Company;
        $data = [];
        if ($typeObj) {
            $defaultFees = $changedObj->service_provider_id
                ? ServiceProviderSettings::getCompanyDefaults($changedObj->service_provider_id)
                    ->pluck('value', 'feeType.id')->toArray()
                : Setting::getProviderDefaults()->pluck('value', 'feeType.id')->toArray();
        } else {
            $defaultFees = Setting::getProviderDefaults()->pluck('value', 'feeType.id')->toArray();
        }
        foreach ($fees as $i => $fee) {
            if ($fee != $defaultFees[$feeTypeIds[$i]]) {
                $data[] = $typeObj
                    ? [
                        'fee_type_id' => $feeTypeIds[$i],
                        $conditionField => $changedObj->id,
                        'service_provider_id' => $changedObj->service_provider_id ? $changedObj->service_provider_id : NULL,
                        'fee' => $fee,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                    : [
                        'fee_type_id' => $feeTypeIds[$i],
                        $conditionField => $changedObj->id,
                        'fee' => $fee,
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
            }
        }

        DB::beginTransaction();
        $typeObj ? Fee::DeleteCompanyCustom($changedObj->id)
            : Fee::where($conditionField, $changedObj->id)->delete();
        DB::table('fees')->insert($data);
        DB::commit();
    }

}
