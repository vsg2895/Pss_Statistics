<?php

namespace App\Console\Commands\Customers;

use App\Models\DailyCompanyData;
use App\Services\Reports\CompanyJobsService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SaveBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:billing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save daily billing data grouped by companies.';

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
            $companyService = new CompanyJobsService();
            $companyData = $companyService->getCompaniesData();
            DailyCompanyData::insert($companyData);

            $message = "Billing saved successfully. Total rows: " . count($companyData);
            $this->info($message);
            Log::info($message);
        } catch (\Exception $exception) {
            $message = 'save:billing failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine();
            $this->error($exception->getMessage());
            Log::error($message);
        }
    }
}
