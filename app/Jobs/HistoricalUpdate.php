<?php

namespace App\Jobs;

use App\Contracts\LogInterface;
use App\Events\HistoricalUpdateEvent;
use App\Models\BillingData;
use App\Models\BillingDataFees;
use App\Models\FeeType;
use App\Models\FixedFees;
use App\Models\ServiceProviderSettings;
use App\Models\Setting;
use App\Services\Update\UpdateDateFeesService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;


class HistoricalUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    private LogInterface $log;
    public $receiver;
    private UpdateDateFeesService $updateDateFeesService;
    private $start;
    private $end;
    private $company;
    private $data;
    private $checks;
    private $updated;
    private $delIds;
    private $departmentIds;
    private $provider;
    private $all;
    public $monthlyFee;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(LogInterface $log, $receiver, UpdateDateFeesService $updateDateFeesService, $start, $end, $company, $data, $checks, $updated, $delIds, $departmentIds, $provider, $all, $monthlyFee,)
    {
        $this->log = $log;
        $this->receiver = $receiver;
        $this->updateDateFeesService = $updateDateFeesService;
        $this->start = $start;
        $this->end = $end;
        $this->company = $company;
        $this->data = $data;
        $this->checks = $checks;
        $this->updated = $updated;
        $this->delIds = $delIds;
        $this->departmentIds = $departmentIds;
        $this->provider = $provider;
        $this->all = $all;
        $this->monthlyFee = $monthlyFee;

    }
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $updateText = __('Update Process - ');
        $oldChangedValues = [];
        $oldChatValues = [];
        $insertBilling = [];
        $insertBillingFees = [];
        $upsertData = [];
        $freeCallUpdate = false;
        $oldValuesFee = DB::table('billing_data_fees')
            ->whereIn('billing_id', $this->delIds);
        $months = $this->updateDateFeesService->checkMonths($this->start, $this->end);
//        data_get => get all "start" keys values
        $monthStarts = data_get($months, '*.start');
        $fixedFeeId = FeeType::getFee('monthly_fee');
        if (is_null($this->departmentIds)) {
            $route = config('app.url') . 'admin/companies/' . $this->company->id . '?start=' . $this->start . '&end=' . $this->end . '&=#statistics';
            $resultUpdate = $this->updateDateFeesService->hystoricalUpdate($this->start, $this->end,
                $this->company, $this->data, $this->checks, $this->updated, $oldValuesFee);
            $insertBilling = $resultUpdate['billingData'];
            $insertBillingFees = $resultUpdate['billingDataFees'];
            $oldChangedValues = $resultUpdate['oldChangedValues'];
            $oldChatValues = $resultUpdate['chatOldValue'];
            $freeCallUpdate = $resultUpdate['freeCallUpdate'];
            $uniqueProviderPart = !is_null($this->company->service_provider_id) ? $this->company->service_provider_id : '0_';
            if ($this->monthlyFee) {
                foreach ($monthStarts as $elem) {
                    $uniqueKey = $this->company->company_id . '_' . $uniqueProviderPart . $elem;
                    $elem = Carbon::createFromFormat('Y-m-d', $elem)->format('Y-m-d');
                    $upsertData[] = [
                        'id' => $uniqueKey,
                        'fee_type_id' => $fixedFeeId,
                        'service_provider_id' => !is_null($this->company->service_provider_id) ? $this->company->service_provider_id : null,
                        'fee' => $this->monthlyFee,
                        'date' => $elem,
                        'company_id' => $this->company->company_id
                    ];
                }
            }
        } else {
            $route = !$this->all
                ? config('app.url') . 'admin/service-providers/' . $this->provider->id . '?start=' . $this->start . '&end=' . $this->end . '&=#statistics'
                : config('app.url') . 'admin/companies/dashboard' . '?start=' . $this->start . '&end=' . $this->end . '&=#statistics-all-companies';
            foreach ($this->company as $company) {
                $currentData = $this->data->where('company_id', $company->company_id);
                $resultUpdate = $this->updateDateFeesService->hystoricalUpdate($this->start, $this->end,
                    $company, $currentData, $this->checks, $this->updated, $oldValuesFee, $this->departmentIds);
                $insertBilling[] = $resultUpdate['billingData'];
                $insertBillingFees[] = $resultUpdate['billingDataFees'];
                $oldChangedValues = $resultUpdate['oldChangedValues'];
                $oldChatValues = $resultUpdate['chatOldValue'];
                $freeCallUpdate = $resultUpdate['freeCallUpdate'];
            }
            $insertBilling = Arr::collapse($insertBilling);
            $insertBillingFees = Arr::collapse($insertBillingFees);
            if (!is_null($this->monthlyFee)) {
                foreach ($monthStarts as $elem) {
                    $uniqueKey = '0_' . $this->provider->id . '_' . $elem;
                    $elem = Carbon::createFromFormat('Y-m-d', $elem)->format('Y-m-d');
                    $upsertData[] = [
                        'id' => $uniqueKey,
                        'fee_type_id' => $fixedFeeId,
                        'service_provider_id' => $this->provider->id,
                        'fee' => $this->monthlyFee,
                        'date' => $elem,
                        'company_id' => null,
                    ];
                }
            }
        }
        event(new HistoricalUpdateEvent($this->receiver->id, $updateText, '30%', $route));
        DB::beginTransaction();
        $this->updateDateFeesService->delBillingDataByIds($this->delIds);
        foreach (array_chunk($insertBilling, 2500) as $chunkedBilling) {
            BillingData::insert($chunkedBilling);
        }
        event(new HistoricalUpdateEvent($this->receiver->id, $updateText, '60%', $route));
        foreach (array_chunk($insertBillingFees, 2500) as $chunkedBilling) {
            BillingDataFees::insert($chunkedBilling);
        }
        if (!is_null($this->monthlyFee)) {
            if (is_null($this->departmentIds)) {
                is_null($this->company->service_provider_id)
                    ? FixedFees::companyFixedCustomByDate($this->company->company_id, 'monthly_fee', $monthStarts)
                    ->upsert($upsertData, ['id'], ['fee'])
                    : FixedFees::providerToCompanyFixedCustomByDate($this->company->service_provider_id, $this->company->company_id, 'monthly_fee', $monthStarts)
                    ->upsert($upsertData, ['id'], ['fee']);
            } else {
                FixedFees::providerFixedCustomByDate($this->provider->id, 'monthly_fee', $monthStarts)
                    ->upsert($upsertData, ['id'], ['fee']);
            }
        }

        event(new HistoricalUpdateEvent($this->receiver->id, $updateText, '100%', $route));
        DB::commit();

        $this->logInfo($this->departmentIds, $this->receiver->name, $this->company, $this->updated,
            $oldChangedValues, $oldChatValues, $freeCallUpdate, $this->log, $this->all, $this->provider);

    }

    private function logInfo($departmentIds, $receiverName, $companies, $updated, $oldChangedValues, $oldChatValues, $freeCallUpdate, $log, $all, $provider): void
    {
        $logKey = is_null($departmentIds) ? 'Historical_Update_Single_Company'
            : ((!$all) ? "Historical_Update_Provider" : "Historical_Update_All_Companies");

        $logArray = $logKey == "Historical_Update_Provider" ?
            [
                'user' => $receiverName,
                'provider' => !is_null($provider) ? $provider->name : "Unknown Provider",
                'updated' => $updated,
                'oldValues' => $oldChangedValues,
                'oldChatsValue' => $oldChatValues,
                'freeCallUpdate' => $freeCallUpdate
            ] :
            [
                'user' => $receiverName,
                'company(ies)' => is_null($departmentIds) ? $companies->name : $companies->pluck('name')->toArray(),
                'updated' => $updated,
                'oldValues' => $oldChangedValues,
                'oldChatsValue' => $oldChatValues,
                'freeCallUpdate' => $freeCallUpdate
            ];

        $log->actionArrayInfo($logKey, $logArray);
    }
}
