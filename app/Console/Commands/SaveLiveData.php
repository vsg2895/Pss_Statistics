<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SaveLiveData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:live';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
            ])->get("https://gcm.servit.se/RestAPI/V1/queues/SVARA?info_type=status");
            $liveData = json_decode($response->body(), true);

            Cache::forever('svara_live', $liveData);
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
            ])->get("https://gcm.servit.se/RestAPI/V1/users?info_type=status");
            $userStatuses = json_decode($response->body(), true) ?: [];

            $groupedData = collect($userStatuses)->groupBy('userid')->all();
            Cache::forever('user_status', $groupedData);

            $this->info('Live data updated successfully.');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            Log::error('save:live failed, message: ' . $exception->getMessage());
        }
    }
}
