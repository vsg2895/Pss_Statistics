<?php

namespace App\Services\Excel;

use App\Contracts\ExcelInterface;
use App\Imports\BillingDataWithFeesImport;
use App\Jobs\Excel\ExportExcel;
use App\Jobs\Excel\ImportExcel;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\Setting;
use App\Services\BaseService;
use App\Services\Update\UpdateDateFeesService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;


class ExcelService extends BaseService implements ExcelInterface
{

    private UpdateDateFeesService $updateDateFeesService;

    public function __construct(UpdateDateFeesService $updateDateFeesService)
    {
        $this->updateDateFeesService = $updateDateFeesService;
    }

    public function getPath($type = 'company'): string
    {
        switch ($type) {
            case 'company':
                $path = 'reports/excel-reports/';
                break;
            case 'compare':
                $path = 'reports/compare/excel-reports/';
                break;
            default:
                $path = 'reports/excel-reports/';
        }

        return $path;
    }

    public function getDataFromExport()
    {
        try {
            $this->updateDateFeesService->setParams($this->company, 'update_company', $this->start, $this->end);
            return $this->updateDateFeesService->getBillingDataByDateFilter('all', true);
        } catch (\Exception $exception) {
            Log::error('Error Generate Excel Invoice By Company' . $exception->getMessage() . ' - In Line ' . $exception->getLine());
            return $exception->getMessage() . 'In Line' . $exception->getLine();
        }

    }

    public function getDataFromImport($company, $start, $end, $ids = null): array|string
    {
        try {
            $this->updateDateFeesService->setParams($company, 'update_company', $start, $end);
            $delIds = $this->updateDateFeesService->getBillingDataByDateFilter('ids', null, $ids);
            $data = $this->updateDateFeesService->getBillingDataByDateFilter('all', true, $ids);
            $oldValuesFee = DB::table('billing_data_fees')->whereIn('billing_id', $delIds);
            return [
                'delIds' => $delIds,
                'data' => $data,
                'oldValuesFee' => $oldValuesFee
            ];
        } catch (\Exception $exception) {
            Log::error('Error Import Excel By Company' . $exception->getMessage() . ' - In Line ' . $exception->getLine());
            return $exception->getMessage() . 'In Line' . $exception->getLine();
        }

    }

    public function delBillingDataByIds($delIds): void
    {
        $this->updateDateFeesService->delBillingDataByIds($delIds);
    }

    public function updateViaImport($company, $start, $end, $data, $oldValuesFee, $updated, $chat_fee, $p_chat_fee): array
    {
        return $this->updateDateFeesService->importUpdate($start, $end, $company, $data, $updated, $updated, $oldValuesFee, $chat_fee, $p_chat_fee);
    }

    public function ImportData($company, $file): void
    {
        $import = Excel::queueImport(new BillingDataWithFeesImport($this, $company), $file);

    }

    /**
     * @throws \Throwable
     */
    public function ExportData(): void
    {
        $reportPath = $this->getPath() . $this->company->name . " " . $this->start . "-" . $this->end . '.xlsx';
        $companyData = $this->getDataFromExport();

        $chatFeeId = FeeType::getFeesBySlug($this->company->fees->pluck('fee_type_id')->toArray(), 'chats_fee');
        $chatProviderFeeId = !is_null($this->company->serviceProvider)
            ? FeeType::getFeesBySlug($this->company->serviceProvider->fees
                ->pluck('fee_type_id')->toArray(), 'chats_fee') : null;

        if (!is_null($chatFeeId)) {
            $companyChatFee = is_null($this->company->serviceProvider) ? Fee::feeByFeeType($chatFeeId, $this->company)
                : Fee::feeByFeeTypeCompanyProvider($chatFeeId, $this->company->serviceProvider);
        } else {
            $companyChatFee = Setting::getValueBySlug('chats_fee');
        }
        if (!is_null($chatProviderFeeId)) {
            $companyProviderChatFee = Fee::feeByFeeTypeProvider($chatProviderFeeId, $this->company->serviceProvider, $this->company)
                ? Fee::feeByFeeTypeProvider($chatProviderFeeId, $this->company->serviceProvider, $this->company)
                : $this->company->serviceProvider->getDefaultFeeBySlug('chats_fee');
        } else {
            $companyProviderChatFee = !is_null($this->company->serviceProvider)
                ? $this->company->serviceProvider->getDefaultFeeBySlug('chats_fee') : null;
        }
        $dataCount = $companyData->count();
        $checkMonths = $this->updateDateFeesService->checkMonths($this->start, $this->end);
        ExportExcel::dispatch($dataCount, $companyData, $this->company, $this->start, $this->end,
            $reportPath, $companyChatFee, $companyProviderChatFee, $checkMonths);

    }


}
