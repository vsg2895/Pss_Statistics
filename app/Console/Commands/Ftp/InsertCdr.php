<?php

namespace App\Console\Commands\Ftp;

use App\Jobs\SendFreeCallExpireEmail;
use App\Models\BillingData;
use App\Models\BillingDataFees;
use App\Models\Company;
use App\Services\Insert\SetBillingPricesService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;


class InsertCdr extends Command
{

    public SetBillingPricesService $setbillingprices;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert:cdr {--year=} {--day=} {--month=} {--prevDay} {--single}';//day = 18-01-2022, month = 01-2022,year = 2022
    /**laravel
     * --prevDay flag insert only files according hours
     * -- single flag insert only daily general files
     */


    /**laravel
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert to db from cdr files';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(SetBillingPricesService $setbillingprices)
    {
        $this->setbillingprices = $setbillingprices;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()//todo add variable and set if all files, it means for loop for specific folder and insert all
    {//todo check last hours for Monday and thuesday
        //worked from gcmcdr-2022012608
        try {
            $dayArgument = $this->option('day');
            $monthArgument = $this->option('month');
            $yearArgument = $this->option('year');
            $prevDayArgument = $this->option('prevDay');
            $singleArgument = $this->option('single');
            $year = Carbon::now()->subHours(2)->format('Y');
            $month = Carbon::now()->subHours(2)->format('m-Y');
            $day = Carbon::now()->subHours(2)->format('d-m-Y');
//            $day = "18-01-2022";
            if ($yearArgument) {
                $yearPath = storage_path() . "/app/files/cdr/$yearArgument";
                if (file_exists($yearPath)) {
                    $files = array_diff(scandir($yearPath), ['..', '.']);
                    foreach ($files as $file) {
                        $Date = $this->setbillingprices->StartEndDate('month', $file);
                        $count = $this->monthProcess($file, $Date['start'], $Date['now'], $singleArgument);
                    }
                    $this->info('Calls imported from cdr folder successfully. Total: ' . $count['billing'] . ', Folder: ' . $yearArgument);
                    $this->info('Calls_Fees imported from cdr folder successfully. Total: ' . $count['billingFee'] . ', Folder: ' . $yearArgument);
                } else {
                    $message = 'insert:cdr {year}/filename failed, message: File not found. Day argument: ' . $yearArgument;
                    $this->error($message);
                    Log::error($message);
                }
                //                $this->info('Calls imported from cdr folder successfully. Total: ' . $count['billing'] . ', Folder: ' . $dayArgument);
//                $this->info('Calls_Fees imported from cdr folder successfully. Total: ' . $count['billingFee'] . ', Folder: ' . $dayArgument);
//                Log::info('Calls imported from cdr folder successfully. Total: ' . $count['billing'] . ', Folder: ' . $dayArgument);
//                Log::info('Calls_Fees imported from cdr folder successfully. Total: ' . $count['billingFee'] . ', Folder: ' . $dayArgument);
            } elseif ($dayArgument) {//loop for all files in the folder for that date
                $Date = $this->setbillingprices->StartEndDate('day', $dayArgument);
                $monthCustom = explode('-', $dayArgument)[1] . '-' . explode('-', $dayArgument)[2];
                $count = $this->dayProcess($dayArgument, $monthCustom, $Date['start'], $Date['now'], $singleArgument);
//                $this->checkExpireFreeCall($count['companies'], $count['data']);
                $this->info('Calls imported from cdr folder successfully. Total: ' . $count['billing'] . ', Folder: ' . $dayArgument);
                $this->info('Calls_Fees imported from cdr folder successfully. Total: ' . $count['billingFee'] . ', Folder: ' . $dayArgument);
                Log::info('Calls imported from cdr folder successfully. Total: ' . $count['billing'] . ', Folder: ' . $dayArgument);
                Log::info('Calls_Fees imported from cdr folder successfully. Total: ' . $count['billingFee'] . ', Folder: ' . $dayArgument);
            } elseif ($prevDayArgument) {//loop for all files in the folder for that date
                $prevDay = Carbon::now()->subDay()->format('d-m-Y');
                $Date = $this->setbillingprices->StartEndDate('day', $prevDay);
                $monthCustom = explode('-', $prevDay)[1] . '-' . explode('-', $prevDay)[2];
                $count = $this->dayProcess($prevDay, $monthCustom, $Date['start'], $Date['now'], $singleArgument);
                $this->info('Calls imported from cdr folder successfully. Total: ' . $count['billing'] . ', Folder: ' . $prevDay);
                $this->info('Calls_Fees imported from cdr folder successfully. Total: ' . $count['billingFee'] . ', Folder: ' . $prevDay);
                Log::info('Calls imported from cdr folder successfully. Total: ' . $count['billing'] . ', Folder: ' . $prevDay);
                Log::info('Calls_Fees imported from cdr folder successfully. Total: ' . $count['billingFee'] . ', Folder: ' . $prevDay);
            } elseif ($monthArgument) {//loop for all files in the folder for that date
                $Date = $this->setbillingprices->StartEndDate('month', $monthArgument);
                $count = $this->monthProcess($monthArgument, $Date['start'], $Date['now'], $singleArgument);
                $this->info('Calls imported from cdr folder successfully. Total: ' . $count['billing'] . ', Folder: ' . $monthArgument);
                $this->info('Calls_Fees imported from cdr folder successfully. Total: ' . $count['billingFee'] . ', Folder: ' . $monthArgument);
                Log::info('Calls imported from cdr folder successfully. Total: ' . $count['billing'] . ', Folder: ' . $monthArgument);
                Log::info('Calls_Fees imported from cdr folder successfully. Total: ' . $count['billingFee'] . ', Folder: ' . $monthArgument);
            } else {
                $count = 0;
                $countFee = 0;
                $usIt = Carbon::now()->subHours(2)->format('YmdH');
                $fileName = "gcmcdr-$usIt.txt";
                $Date = $this->setbillingprices->StartEndDate('daily');
                $file = fopen(storage_path() . "/app/files/cdr/$year/$month/$day/$fileName", 'r');
                $data = $this->defaultProcess($file, $Date['start'], $Date['now']);
                $this->checkExpireFreeCall($data['companies'], $data);
                $count += count($data['insertDataBilling']);
                $countFee += count($data['insertDataBillingFee']);
                $this->info('Calls upserted from cdr successfully. Total: ' . $count . ', Filename: ' . $fileName);
                $this->info('Calls_Fee upserted from cdr successfully. Total: ' . $countFee . ', Filename: ' . $fileName);
                Log::info('Calls upserted from cdr successfully. Total: ' . $count . ', Filename: ' . $fileName);
                Log::info('Calls_Fee upserted from cdr successfully. Total: ' . $countFee . ', Filename: ' . $fileName);
            }
        } catch (\Exception $exception) {
            $messageText = 'insert:cdr failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine();
            $this->error($messageText);
            Log::error($messageText);
            Log::error($exception);
//            Mail::send([], [], function ($message) use ($messageText) {
//                $message->to(config('mail.mail_dev'))->subject('insert:cdr failed')->setBody($messageText);
//            });
        }
    }

    private function checkExpireFreeCall($companies, $data): void
    {
//        $dataBillingColumn = Arr::collapse($data['insertDataBilling']);
        dump($data);
        $companyIds = array_unique(array_column($data['insertDataBilling'], 'company_id'));

        $companyIds = collect($companyIds)->map(function ($companyId) {
            return (int)explode("\n", $companyId)[0];
        })->toArray();
        $priceVariants = [];
        $sending = [];
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $nowDate = Carbon::now()->format('Y-m-d');
        $profits = $this->setbillingprices->getGeneralTypesProfit($startDate, $nowDate, true);
        $correspondWe = Arr::where($profits['issetFreeCount']['free'], function ($value, $key) use ($companyIds) {
            return in_array($key, $companyIds);
        });
        $correspondProvider = Arr::where($profits['issetFreeCount']['p_free'], function ($value, $key) use ($companyIds) {
            return in_array($key, $companyIds);
        });
        foreach ($companyIds as $companyId) {
            $company = $companies->where('company_id', $companyId)->first();
            $priceVariants[$companyId] = $this->setbillingprices
                ->allTypePricesByCompany($companyId, $company, $profits['defaultsWe'],
                    $profits['defaultsProviderCompany'], $profits['feeProviderCompany'], $profits['feeWeProvider'], $profits['feeWeCompany']);
            if (!is_null($priceVariants[$companyId]['provider_company']) && $priceVariants[$companyId]['provider_company']['free_calls'] != 0) {
                $currentFreeCallValue = $priceVariants[$companyId]['provider_company']->toArray()['free_calls'];
                if (isset($correspondProvider[$companyId]) && $correspondProvider[$companyId] >= (int)$currentFreeCallValue
                    && !$company->notified_free_call) {
                    $sending[] = $company;

                }
            } else {
                $currentFreeCallValue = $priceVariants[$companyId]['we']->toArray()['free_calls'];
                if (isset($correspondWe[$companyId]) && $correspondWe[$companyId] >= (int)$currentFreeCallValue
                    && !$company->notified_free_call) {
                    $sending[] = $company;
                }
            }
        }

        SendFreeCallExpireEmail::dispatch('stepanyan281995@gmail.com', $sending);
    }

    private function dayProcess($date, $month, $startDate, $nowDate, $single)
    {
        $year = Carbon::createFromFormat('d-m-Y', $date)->format('Y');
        $day = $date;
        $path = storage_path() . "/app/files/cdr/$year/$month/$day";

        if (file_exists($path)) {
            $files = array_diff(scandir($path), ['..', '.']);
            $count = 0;
            $countFee = 0;
            $data = [];
            foreach ($files as $key => $fileName) {
                if ((!$single && strlen($fileName) !== 19) || ($single && strlen($fileName) === 19)) {
                    $file = fopen($path . '/' . $fileName, 'r');
                    $this->info('Inserting cdr file by day. Filename: ' . $fileName);
                    Log::info('Inserting cdr file by day. Filename: ' . $fileName);
                    $data[] = $this->defaultProcess($file, $startDate, $nowDate);
                    $keyArr = count($data) ? count($data) - 1 : 0;
                    $this->info('Inserting cdr file by day success. Filename: ' . $fileName . ' Inserted: ' . count($data[$keyArr]['insertDataBilling']) . 'rows.');
                    Log::info('Inserting cdr file by day success. Filename: ' . $fileName . ' Inserted: ' . count($data[$keyArr]['insertDataBilling']) . 'rows.');
                    $count += count($data[$keyArr]['insertDataBilling']);
                    $countFee += count($data[$keyArr]['insertDataBillingFee']);
                    $companies = $data[$keyArr]['companies'];
                } elseif ((!$single && strlen($fileName) === 19 || $single && strlen($fileName) !== 19)) {
                    $this->info('Inappropriate File for insert type' . ' - ' . $fileName);
                    continue;
                } else {
                    $message = 'insert:cdr {day}/filename failed, message: File not found. Date argument: ' . $date . ' Filename: ' . $fileName;
                    $this->error($message);
                }

            }
            return [
                'data' => $data,
                'companies' => $companies,
                'billing' => $count,
                'billingFee' => $countFee
            ];
        } else {
            $message = 'insert:cdr {day} failed, message: Folder not found. Day argument: ' . $date;
            $this->error($message);
            Log::error($message);
            die();
        }
    }

    private
    function monthProcess($date, $startDate, $nowDate, $single)
    {
//        try {
        $year = Carbon::createFromFormat('m-Y', $date)->format('Y');
        $path = storage_path() . "/app/files/cdr/$year/$date";
        if (file_exists($path)) {
            $files = array_diff(scandir($path), ['..', '.']);
            $count = 0;
            $countFee = 0;
            foreach ($files as $fileName) {
                $gcmcdrCurrent = array_diff(scandir($path . "/" . $fileName), array('..', '.'));
                foreach ($gcmcdrCurrent as $gcmcdrElem) {
                    if ((!$single && strlen($gcmcdrElem) !== 19) || ($single && strlen($gcmcdrElem) === 19)) {
                        $file = fopen($path . '/' . $fileName . '/' . $gcmcdrElem, 'r');
                        $this->info('Inserting cdr file by month. Filename: ' . $gcmcdrElem);
                        Log::info('Inserting cdr file by month. Filename: ' . $gcmcdrElem);
                        $data = $this->defaultProcess($file, $startDate, $nowDate);
//                        dd($data);
                        $this->info('Inserting cdr file by month success. Filename: ' . $gcmcdrElem . ' Inserted: ' . count($data['insertDataBilling']) . 'rows.');
                        $this->info('Inserting cdr fees file by month success. Filename: ' . $gcmcdrElem . ' Inserted: ' . count($data['insertDataBillingFee']) . 'rows.');
                        Log::info('Inserting cdr file by month success. Filename: ' . $gcmcdrElem . ' Inserted: ' . count($data['insertDataBilling']) . 'rows.');
                        $count += count($data['insertDataBilling']);
                        $countFee += count($data['insertDataBillingFee']);
                    } elseif ((!$single && strlen($gcmcdrElem) === 19) || ($single && strlen($gcmcdrElem) !== 19)) {
                        $this->info('Inappropriate File for insert type' . ' - ' . $gcmcdrElem);
                        continue;
                    } else {
                        $message = 'insert:cdr {month}/filename failed, message: File not found. Date argument: ' . $date . ' Filename: ' . $gcmcdrElem;
                        $this->error($message);
//                        Log::error($message);
                    }
                }
            }

            return [
                'billing' => $count,
                'billingFee' => $countFee
            ];
        } else {
            $message = 'insert:cdr {month} failed, message: Folder not found. Date argument: ' . $date;
            $this->error($message);
            Log::error($message);
            die();
        }
//        } catch (\Exception $exception) {
//            dd($exception->getMessage(), $exception->getLine() . "in monthproccess");
//        }

    }

    private function defaultProcess($file, $startDate, $nowDate)
    {
        try {
            $insertData = [];
            $insertDataFee = [];
            $companies = Company::all();
            $profits = $this->setbillingprices->getGeneralTypesProfit($startDate, $nowDate);
//            $defaultsWe = Setting::getDefaultsBillingRow(FeeType::FEE_TYPES_IN_INSERT);
//            $defaultsProviderCompany = ServiceProviderSettings::getProviderToCompanyDefaults(FeeType::FEE_TYPES_IN_INSERT);
//            $feeProviderCompany = Fee::getProviderCompanyCustomAll();
//            $feeWeProvide = Fee::getProviderCustomAll();
//            $feeWeCompany = Fee::getCompanyCustomAll();
////            check in selected interval free call count
//            $issetFreeCount = BillingData::select()->selectRaw("COUNT(id) as calls_count")
//                ->whereRaw('DATE(date) >= ?', [$startDate])->whereRaw('DATE(date) <= ?', [$nowDate])
//                ->where('status', 'AN')->groupBy('company_id')->pluck('calls_count', 'company_id')->toArray();
            $issetFreeCountCurrent = [];
            $currData = [];
            $billingIds = BillingData::pluck('id')->toArray();
            $index = 1;
            while (!feof($file)) {
                $data = explode("\t", explode(" ", fgets($file))[0]);
                $index++;
                if (!isset($data[1])) {
//                    dump($data);
                    continue;
                }
                $uniqueId = $data[0] . "_" . $this->getStatus($data[6]) . "_" . $index;
//                $companyId = (int)explode("\n", $data[13])[0];
                $dataRow = [
                    'id' => $uniqueId,
                    'call_id' => $data[0],
                    'duration' => $data[3] == 'X' ? 0 : $data[3],
                    'a_number' => $data[4],
                    'b_number' => $data[5],
                    'status' => $this->getStatus($data[6]),
                    'message' => $data[8],
                    'sms' => $data[9],
                    'email' => $data[10],
                    'booking' => $data[7],
                    'agent_id' => $data[11] == '' ? null : $data[11],
                    'site_id' => $data[12],
                    'company_id' => $data[13],
                    'date' => Carbon::createFromFormat('dmyHi', $data[1] . $data[2])->format('Y-m-d H:i:s'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $companyId = (int)explode("\n", $dataRow['company_id'])[0];
                $curPrices = $this->setbillingprices
                    ->priceVariants($companyId, $companies, $profits['defaultsWe'],
                        $profits['defaultsProviderCompany'], $profits['feeProviderCompany'], $profits['feeWeProvider'], $profits['feeWeCompany']);

                if (!in_array($uniqueId, $billingIds)) {
                    if ($curPrices) {
//                    dump($curPrices, 'hh',$uniqueId);
                        $dataTables = $this->setbillingprices->setPrices($dataRow, $curPrices, $companyId, $profits['defaultsWe'], $issetFreeCountCurrent);
//                        dump($dataTables);
                        $currData = $dataTables['curCountFrees'];
                        $insertData[] = $dataTables['billingData'];
                        $insertDataFee[] = $dataTables['billingDataFee'];
                    }
//                dd($dataTables, 'datatablesss');
                } else {
                    $this->info('Duplicate' . $uniqueId . ' - file');
                }
            }
            fclose($file);
            $billingData = $this->setbillingprices->checkFreeCalls($profits['issetFreeCount'], $currData, $insertData);

            foreach ($billingData as $elemBilling) {
                BillingData::create($elemBilling);
            }
            foreach ($insertDataFee as $elemFee) {
                BillingDataFees::create($elemFee);
            }
//            BillingData::insert($billingData);
//            BillingDataFees::insert($insertDataFee);

            return [
                'insertDataBilling' => $insertData,
                'insertDataBillingFee' => $insertDataFee,
                'companies' => $companies,
            ];
        } catch (\Exception $exception) {
            $messageText = 'insert:cdr failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine() . 'base';
            $this->error($messageText);
            Log::error($messageText);
            Log::error($exception);
//            Mail::send([], [], function ($message) use ($messageText) {
//                $message->to(config('mail.mail_dev'))->subject('insert:cdr failed')->setBody($messageText);
//            });
        }
    }

    private
    function getStatus($cdrStatus)
    {
        return match ($cdrStatus) {
            'IN' => 'AN',
            'MI' => 'MI',
            'AV' => 'WT',
            'KO' => 'CT',
            'ST' => 'CL',
            'VK' => 'VK',
            default => $cdrStatus
        };
    }
}
