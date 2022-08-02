<?php

namespace App\Imports;

use App\Contracts\ExcelInterface;
use App\Events\Excel\NotifyExcelImport;
use App\Models\BillingData;
use App\Models\BillingDataFees;
use App\Models\FeeType;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Events\AfterImport;

class BillingDataWithFeesImport implements ToCollection, WithHeadingRow, WithChunkReading, ShouldQueue, WithEvents
{
    use Importable, RegistersEventListeners;

    public ExcelInterface $excel;
    public $company;
    public static $companyId;
    public static $start;
    public static $end;

    public function __construct(ExcelInterface $excel, $company)
    {
        $this->excel = $excel;
        $this->company = $company;
        self::$companyId = $company->id;
    }

    private function validator($arrayData)
    {
        return Validator::make($arrayData, [
            '*.start' => 'required',
            '*.end' => 'required',
            '*.id' => 'required|exists:billing_data,id',
            '*.status' => 'required',
            '*.free_call' => 'nullable|numeric|min:0|max:1',
            '*.p_free_call' => 'nullable|numeric|min:0|max:1',
            '*.calls_fee' => 'required',
            '*.bookings_fee' => 'required',
            '*.above_60_fee' => 'required',
            '*.cold_transferred_calls_fee' => 'required',
            '*.warm_transferred_calls_fee' => 'required',
            '*.time_above_seconds' => 'required',
            '*.messages_fee' => 'required',
            '*.sms_fee' => 'required',
            '*.emails_fee' => 'required',
            '*.chats_fee' => 'nullable',
            '*.p_calls_fee' => 'nullable',
            '*.p_bookings_fee' => 'nullable',
            '*.p_above_60_fee' => 'nullable',
            '*.p_cold_transferred_calls_fee' => 'nullable',
            '*.p_warm_transferred_calls_fee' => 'nullable',
            '*.p_time_above_seconds' => 'nullable',
            '*.p_messages_fee' => 'nullable',
            '*.p_sms_fee' => 'nullable',
            '*.p_emails_fee' => 'nullable',
            '*.p_chats_fee' => 'nullable',
        ]);

    }

    /**
     * @param Collection $collection
     * @throws \Illuminate\Validation\ValidationException
     */
    public function collection(Collection $collection)
    {
        if ($this->validator($collection->toArray())->fails()) {
            Log::error($this->validator($collection->toArray())->errors());
            event(new NotifyExcelImport(__("Something went wrong."), null, null, null, null));
        }
        $dataInExcel = collect($collection->toArray())->groupBy('id');
        $filterDataExcel = $dataInExcel->toArray();
        $start = reset($filterDataExcel)[0]['start'];
        $end = reset($filterDataExcel)[0]['end'];
        self::$start = $start;
        self::$end = $end;
        $chat_fee = reset($filterDataExcel)[0]['chats_fee'];
        $p_chat_fee = reset($filterDataExcel)[0]['p_chats_fee'];
        $updated = $this->filterNeededKeys($filterDataExcel, FeeType::FEE_TYPES_UPDATE_DATA_EXCEL_FEES);
        $data = $this->excel->getDataFromImport($this->company, $start, $end, array_keys($updated));

        if (count($data['data']->pluck('company_id')->unique()) > 1 || $data['data']->pluck('company_id')->unique()[0] != $this->company->company_id) {
            event(new NotifyExcelImport(__("Something went wrong."), null, null, null, null));
            Log::error($data['data']->pluck('company_id')->unique()[0] . 'count' . $this->company->company_id);

        }
        $resultUpdate = $this->excel->updateViaImport($this->company, $start, $end, $data['data'], $data['oldValuesFee'], $updated, $chat_fee, $p_chat_fee);

        DB::beginTransaction();
        $this->excel->delBillingDataByIds($data['delIds']);
        $insertBilling = $resultUpdate['billingData'];
        $insertBillingFees = $resultUpdate['billingDataFees'];
        BillingData::insert($insertBilling);
        BillingDataFees::insert($insertBillingFees);
        $this->company->getChatsByDate($start, $end)
            ->update(['price' => (float)$chat_fee, 'provider_price' => (float)$p_chat_fee]);
        DB::commit();

    }


    public static function afterImport(AfterImport $event)
    {
        sleep(3);
        event(new NotifyExcelImport(__("File Imported Successfully"), self::$start, self::$end, self::$companyId, 1));
    }

    public function chunkSize(): int
    {
        return 300;
    }


    public function filterNeededKeys(&$dataInExcel, $excelTypes)
    {
        foreach ($dataInExcel as &$data) {
            $data = array_intersect_key($data[0], $excelTypes);
        }

        return $dataInExcel;
    }

}
