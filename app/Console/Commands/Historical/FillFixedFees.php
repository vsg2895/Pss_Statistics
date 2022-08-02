<?php

namespace App\Console\Commands\Historical;

use App\Models\Company;
use App\Models\Fee;
use App\Models\FixedFees;
use App\Models\ServiceProvider;
use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FillFixedFees extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fixed:fees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill previous month correspond fixed fees from companies & providers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $companiesIdsWithoutProviderFixed = Company::withoutProviderFixed()->pluck('company_id', 'id')->toArray();
            $providerIdsWithoutFixed = ServiceProvider::withoutFixed()->pluck('id')->toArray();
            DB::beginTransaction();
            $insertArray = array_merge($this->buildInsertedData($companiesIdsWithoutProviderFixed, 'company'),
                $this->buildInsertedData($providerIdsWithoutFixed, 'provider'));
            FixedFees::insert($insertArray);
            DB::commit();
            $this->info('Fixed Fees Added Successfully From - ' . count($insertArray) . " Companies And Providers");
            Log::info('Fixed Fees Added Successfully From - ' . count($insertArray) . " Companies And Providers");
        } catch (\Exception $e) {
            $this->info("Fixed Fees Added Error" . $e->getMessage() . " In - " . $e->getLine());
            Log::error("Fixed Fees Added Error" . $e->getMessage() . " In - " . $e->getLine());
        }

    }

    private function buildInsertedData($arrayObj, $type): array
    {
        $insert = [];
        $lastMonth = new Carbon('first day of last month');
        $lastMonthStart = $lastMonth->startOfMonth()->format('Y-m-d');
        $nullableField = $type == 'company' ? 'service_provider_id' : 'company_id';
        $valueField = $type == 'company' ? 'company_id' : 'service_provider_id';
        foreach ($arrayObj as $key => $elem) {
            $customFee = $type == 'company' ? Fee::getCompanyCustom($key) : Fee::getProviderCustom($elem);
            $fee = $customFee['monthly_fee'] ?? Setting::getValueBySlug('monthly_fee');
            $insert[] = [
                'id' => $type == 'company' ? $elem . '_0_' . $lastMonthStart : '0_' . $elem . '_' . $lastMonthStart,
                'fee_type_id' => 5,
                $nullableField => NULL,
                $valueField => $elem,
                'fee' => $fee,
                'date' => $lastMonthStart,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }

        return $insert;
    }
}
