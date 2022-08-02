<?php

namespace App\Console\Commands\Customers;

use App\Models\Call;
use App\Models\CallMeta;
use App\Models\Company;
use App\Models\CompanyMeta;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import companies';

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
            $lastCompany = Company::orderBy('id', 'desc')->first();
            $datetimeFrom = '20210916100930';
            if ($lastCompany) {
                $datetimeFrom = $lastCompany->added_at;
                $datetimeFrom = Carbon::parse($datetimeFrom)->format('YmdHis');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
            ])->get(config('apiKeys.servit_url') . "/companies?addts:gt=$datetimeFrom");
//            ])->get(config('apiKeys.servit_url') . "/companies?Info_type=pbx_stat&addts:gt=$datetimeFrom");

            $companies = json_decode($response->body(), true) ?: [];


            if (isset($companies['companyno'])) $companies = [$companies];
//dd($companies);
            $count = 0;
            $companyData = [];
            $companyMeta = [];
            foreach ($companies as $company) {
                $count++;
                $id = intval($company['companyno']);

                $companyData[] = [
                    'company_id' => $id,
                    'siteno' => $company['siteno'],
                    'name' => $company['company'],
                    'url' => $company['url'] ?? null,
                    'orgno' => $company['orgno'] ?? null,
                    'city' => $company['city'] ?? null,
                    'added_at' => Carbon::parse($company['addts'])->format('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
                $company = $this->unsetKeys($company);
                $companyMeta[] = [
                    'company_id' => $id,
                    'meta_data' => json_encode($company),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }

            Company::insert($companyData);
            CompanyMeta::insert($companyMeta);

            $this->info('Companies imported successfully. Total: ' . $count);
            Log::info('Companies imported successfully. Total: ' . $count);

            if (count($companies) >= 1000) $this->handle();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            Log::error('import:companies failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine());
        }
    }

    private function unsetKeys($company)
    {
        unset($company['companyno']);
        unset($company['siteno']);
        unset($company['company']);
        unset($company['url']);
        unset($company['orgno']);
        unset($company['city']);
        unset($company['addts']);

        return $company;
    }
}
