<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class MarkInactiveCompanies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'deactivate:companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Make inactive all companies, that don\'t have calls for last 12 months';

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
            $oneYearAgo = Carbon::now()->subYear()->format('Y-m-d H:i:s');

            $companies = Company::whereDoesntHave('calls', function ($query) use ($oneYearAgo) {
                return $query->where('started_at', '>=', $oneYearAgo);
            })->where('active', '1')->where('added_at', '<', $oneYearAgo)->get();

            foreach ($companies as $company) {
                $company->update(['active' => '0']);
            }

            $message = 'deactivate:companies run successfully, ' . count($companies) . ' companies deactivated';
            $this->info($message);
            Log::info($message);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage() . ' Line: ' . $exception->getLine());
            Log::error('deactivate:companies failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine());
        }

    }
}
