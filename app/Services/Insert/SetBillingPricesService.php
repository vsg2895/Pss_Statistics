<?php

namespace App\Services\Insert;

use App\Models\FeeType;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;


class SetBillingPricesService extends BaseService
{
    public function priceVariants($companyId, $companies, $defaultsWe, $defaultsProviderCompany,
                                  $feeProviderCompany, $feeWeProvider, $feeWeCompany)
    {
        try {
            $company = $companies->where('company_id', $companyId)->first();
            return $this->allTypePricesByCompany($companyId, $company, $defaultsWe, $defaultsProviderCompany, $feeWeCompany, $feeWeProvider, $feeProviderCompany);

        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function checkFreeCalls(&$allCounts, $appendCount, &$dataRow)
    {
        foreach ($dataRow as &$row) {
            $companyId = (int)explode("\n", $row['company_id'])[0];
            if ($row['status'] == "AN") {
                $allCounts[$companyId] = $allCounts[$companyId] ?? 0;
                $row['free_call'] = $allCounts[$companyId] < (int)$appendCount[$companyId]['we_free'];
                $row['price'] = !$row['free_call'] ? $row['price'] + (float)$appendCount[$companyId]['we_answer'] : $row['price'];
                if (!is_null($appendCount[$companyId]['provider_free'])) {
                    $row['p_free_call'] = $allCounts[$companyId] < $appendCount[$companyId]['provider_free'];
                    $row['provider_price'] = !$row['p_free_call'] ? $row['provider_price'] + (float)$appendCount[$companyId]['provider_answer'] : $row['provider_price'];
                } else {
                    $row['p_free_call'] = NULL;
                }
                $allCounts[$companyId] = (int)$allCounts[$companyId] + 1;
            } else {
                $row['free_call'] = NULL;
                $row['p_free_call'] = NULL;
            }
        }

        return $dataRow;
    }

    public function setPrices(&$dataRow, $companyPrices, $companyId, $defaultsWe, &$issetFreeCountCurrent, $update = null)
    {
        $res = [];
        foreach ($companyPrices as $keyPrices => $curPrices) {
            $res[$keyPrices] = 0;
            $statusArray = FeeType::FEE_TYPES_IN_STATUS_UPDATE_BILLING;
            if (!is_null($curPrices) && count($curPrices) > 0) {
                foreach (FeeType::FEE_TYPES_IN_SUM_PRICES as $key => $elem) {
                    $dataRow = is_object($dataRow)
                        ? json_decode(json_encode($dataRow), true)
                        : $dataRow;
                    $res[$keyPrices] += (($dataRow[$key] == '' ? '0' : $dataRow[$key]) * $curPrices[$elem]);
                }
                if ($dataRow['status'] == "AN") {

                    $res[$keyPrices . '_free'] = $curPrices['free_calls'];
                    $res[$keyPrices . '_answer'] = $curPrices['calls_fee'];
                    if (is_numeric($dataRow['duration']) && $dataRow['duration'] > $curPrices['time_above_seconds']) {
                        $incomeDuration = (int)$dataRow['duration'] - $curPrices['time_above_seconds'];
                        $res[$keyPrices] += ($incomeDuration * $curPrices['above_60_fee']);
                    }

                    $freeType = $keyPrices != 'we' ? 'p_free_call' : 'free_call';
                    if (!is_null($update) && $dataRow[$freeType] == 0) {
                        $res[$keyPrices] += (float)$curPrices[array_search($dataRow['status'], $statusArray)];
                    }

                } else {
                    if (in_array($dataRow['status'], $statusArray)) {
                        $res[$keyPrices] += $curPrices[array_search($dataRow['status'], $statusArray)];
                    }
                    $res[$keyPrices . '_free'] = NULL;
                    $res[$keyPrices . '_answer'] = NULL;
                }
            } else {
                $res[$keyPrices] = NULL;
                $res[$keyPrices . '_free'] = NULL;
                $res[$keyPrices . '_answer'] = NULL;

            }


        }
        if ($dataRow['status'] == "AN") {
            $issetFreeCountCurrent[$companyId] = [
                'we_free' => $res['we_free'],
                'we_answer' => $res['we_answer'],
                'provider_free' => $res['provider_company_free'],
                'provider_answer' => $res['provider_company_answer'],
            ];
        }
        $dataRow['price'] = $res['we'];
        $dataRow['provider_price'] = $res['provider_company'];
        $dataRowFee = [
            'billing_id' => $dataRow['id'],
        ];

        $dataRowFee = $this->setBillingCallFees($companyPrices, $dataRowFee, $defaultsWe);

        return [
            'billingData' => $dataRow,
            'billingDataFee' => $dataRowFee,
            'curCountFrees' => $issetFreeCountCurrent
        ];

    }

    public function setPricesImportUpdate($dataRow, $checks, &$companyPrices, $defaultsWe, $update = null)
    {
        $res = [];
        foreach ($companyPrices as $keyPrices => &$curPrices) {
            if (is_null($curPrices)) {
                continue;
            }
            $curPrices = $checks;
            $res[$keyPrices] = 0;
            $statusArray = FeeType::FEE_TYPES_IN_STATUS_UPDATE_BILLING;
            if (isset($curPrices[$dataRow->billing_id]['free_call'])) {
                $dataRow->free_call = $curPrices[$dataRow->billing_id]['free_call'];
            }
            if ($keyPrices != 'we') {
                $dataRow->p_free_call = $curPrices[$dataRow->billing_id]['p_free_call'];
            }
            if (!is_null($curPrices) && count($curPrices[$dataRow->billing_id]) > 0) {
                foreach (FeeType::FEE_TYPES_IN_SUM_PRICES as $key => $elem) {
                    if ($keyPrices == 'we') {
                        $res[$keyPrices] += ($dataRow->{$key} * $curPrices[$dataRow->billing_id][$elem]);
                    } else {
                        $res[$keyPrices] += ($dataRow->{$key} * $curPrices[$dataRow->billing_id]['p_' . $elem]);
                    }
                }
                if ($dataRow->status == "AN") {
                    if (is_numeric($dataRow->duration) && $dataRow->duration > $curPrices[$dataRow->billing_id]['time_above_seconds'] && $keyPrices == 'we') {
                        $incomeDuration = (int)$dataRow->duration - $curPrices[$dataRow->billing_id]['time_above_seconds'];
                        $res[$keyPrices] += ($incomeDuration * $curPrices[$dataRow->billing_id]['above_60_fee']);
                    }
                    if (is_numeric($dataRow->duration) && $keyPrices != 'we') {
                        if (isset($curPrices[$dataRow->billing_id]['p_time_above_seconds']) && $dataRow->duration > $curPrices[$dataRow->billing_id]['p_time_above_seconds']) {
                            $incomeDuration = (int)$dataRow->duration - $curPrices[$dataRow->billing_id]['p_time_above_seconds'];
                            $res[$keyPrices] += ($incomeDuration * $curPrices[$dataRow->billing_id]['p_above_60_fee']);
                        }
                    }
                    $freeType = $keyPrices != 'we' ? 'p_free_call' : 'free_call';
                    if (isset($curPrices[$dataRow->billing_id]['free_call']) || isset($curPrices[$dataRow->billing_id]['p_free_call'])) {
                        if ($dataRow->{$freeType} == 0) {
                            $statusCorrespondSlug = $freeType == 'p_free_call'
                                ? 'p_' . array_search($dataRow->status, $statusArray)
                                : array_search($dataRow->status, $statusArray);
                            $res[$keyPrices] += $curPrices[$dataRow->billing_id][$statusCorrespondSlug];
                        }
                    }
                } else {
                    if (in_array($dataRow->status, $statusArray)) {
                        $res[$keyPrices] += $curPrices[$dataRow->billing_id][array_search($dataRow->status, $statusArray)];
                    }
                }
            } else {
                $res[$keyPrices] = NULL;
            }
            $dataRow->id = $dataRow->billing_id;
            $dataRowFee[$dataRow->billing_id] = [
                'billing_id' => $dataRow->billing_id,
            ];
        }
        $dataRow->price = $res['we'];
        $dataRow->provider_price = $res['provider_company'] ?? null;
        $dataRow = array_intersect_key(json_decode(json_encode($dataRow), true),
            array_flip(Schema::getColumnListing('billing_data')));

        $dataRowFee = $this->setBillingImportCallFees($companyPrices, $dataRowFee, $defaultsWe);

        $dataRowFee = array_intersect_key(json_decode(json_encode($dataRowFee), true),
            array_flip(Schema::getColumnListing('billing_data_fees')));

        return [
            'billingData' => $dataRow,
            'billingDataFee' => $dataRowFee,
        ];
    }

    public function setBillingImportCallFees($companyPrices, &$dataRowFee, $defaultsWe)
    {
        $value = $companyPrices['we'];
        $k = '';
        foreach ($value[array_keys($dataRowFee)[0]] as $key_fee => $elem) {
            if ($key_fee != "free_calls") {
                $dataRowFee[array_keys($dataRowFee)[0]][$key_fee] = $elem;
            }
            $k = array_keys($dataRowFee)[0];
        }
        $dataRowFee = Arr::get($dataRowFee, $k);

        return $dataRowFee;

    }

    public function setPricesUpdate($dataRow, $checks, &$companyPrices, $defaultsWe, $oldValuesFee, $changeType, $update = null)
    {
        $res = [];
        foreach ($companyPrices as $keyPrices => &$curPrices) {
            if ($changeType == $keyPrices) {
                $curPrices = array_diff_key(json_decode(json_encode($oldValuesFee[$dataRow->id]), true), $checks);
                $curPrices = array_merge($checks, $curPrices);
                $curPrices = $this->delPrefixKey($curPrices, 'p_');
            }
            $res[$keyPrices] = 0;
            $statusArray = FeeType::FEE_TYPES_IN_STATUS_UPDATE_BILLING;
            if (!is_null($curPrices) && count($curPrices) > 0) {
                foreach (FeeType::FEE_TYPES_IN_SUM_PRICES as $key => $elem) {
                    $res[$keyPrices] += ($dataRow->{$key} * $curPrices[$elem]);
                }
                if ($dataRow->status == "AN") {
                    if (is_numeric($dataRow->duration) && $dataRow->duration > $curPrices['time_above_seconds']) {
                        $incomeDuration = (int)$dataRow->duration - $curPrices['time_above_seconds'];
                        $res[$keyPrices] += ($incomeDuration * $curPrices['above_60_fee']);
                    }
                    $freeType = $keyPrices != 'we' ? 'p_free_call' : 'free_call';
                    if (!is_null($update) && $dataRow->{$freeType} == 0) {
                        $res[$keyPrices] += $curPrices[array_search($dataRow->status, $statusArray)];
                    }

                } else {
                    if (in_array($dataRow->status, $statusArray)) {
                        $res[$keyPrices] += $curPrices[array_search($dataRow->status, $statusArray)];
                    }
                }
            } else {
                $res[$keyPrices] = NULL;
            }

            $dataRowFee['billing_id'] = $dataRow->id;
        }
        $dataRow->price = $res['we'];
        $dataRow->provider_price = $res['provider_company'];

        $companyPrices[$changeType] = $this->delPrefixKey($companyPrices[$changeType], 'p_');
        $dataRowFee = $this->setBillingCallFees($companyPrices, $dataRowFee, $defaultsWe);
//        dd($dataRowFee, 'setbillingprices');
        return [
            'billingData' => $dataRow,
            'billingDataFee' => $dataRowFee,
        ];
    }


    public function setBillingCallFees($companyPrices, &$dataRowFee, $defaultsWe)
    {
        $feeKeys = $defaultsWe->pluck('slug')->toArray();
        foreach ($companyPrices as $key => $value) {
            if (!is_null($value)) {
                foreach ($value as $key_fee => $elem) {
                    if ($key_fee != "free_calls") {
                        $key_fee = $key == "provider_company" ? "p_" . $key_fee : $key_fee;
                        $dataRowFee[$key_fee] = $elem;
                    }

                }
            } else {
                foreach ($feeKeys as $key_fee) {
                    if ($key_fee != "free_calls") {
                        $key_fee = "p_" . $key_fee;
                        $dataRowFee[$key_fee] = null;
                    }
                }
            }
        }

        return $dataRowFee;
    }

    public function StartEndDate($commandType, $commandTime = null)
    {
        switch ($commandType) {
            case 'day':
                $insertedMonth = \Illuminate\Support\Carbon::createFromFormat('d-m-Y', $commandTime)->format('m');
                $startDate = Carbon::createFromFormat('m', $insertedMonth)->startOfMonth()->format('Y-m-d');
                $nowDate = Carbon::createFromFormat('d-m-Y', $commandTime)->endOfMonth()->format('Y-m-d');
                break;
            case 'month':
                $insertedMonth = \Illuminate\Support\Carbon::createFromFormat('m-Y', $commandTime)->format('m');
                $currentMonth = Carbon::now()->format('m');
                $startDate = Carbon::createFromFormat('m', $insertedMonth)->startOfMonth()->format('Y-m-d');
                $nowDate = $insertedMonth != $currentMonth
                    ? Carbon::createFromFormat('m', $insertedMonth)->endOfMonth()->format('Y-m-d')
                    : Carbon::createFromFormat('m', $currentMonth)->format('Y-m-d');
                break;
            default:
                $startDate = \Illuminate\Support\Carbon::now()->startOfMonth()->format('Y-m-d');
                $nowDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
        }

        return [
            'start' => $startDate,
            'now' => $nowDate,
        ];
    }

}
