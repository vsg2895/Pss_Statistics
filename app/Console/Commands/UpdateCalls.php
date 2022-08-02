<?php

namespace App\Console\Commands;

use App\Models\Call;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateCalls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:calls';

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
            $calls = Call::withoutGlobalScope('open')->where('hang_up_at', '0000-00-00 00:00:00')->get();

            $count = 0;
            foreach ($calls as $ourCall) {
                $response = Http::withHeaders([
                    'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
                ])->get("https://gcm.servit.se/RestAPI/V1/calls?garpnum=$ourCall->call_id");

                $call = json_decode($response->body(), true) ?: [];
                $count++;

                if (!$call) {
                    $ourCall->update(['hang_up_at' => $ourCall['connected_at'] ?: $ourCall['started_at']]);
                    continue;
                }

                $garpnum = intval($call['garpnum']);
                $callData = [
                    'agent_id' => $call['agentid'] ?: null,
                    'aid' => $call['aid'],
                    'bid' => $call['bid'],
                    'cid' => $call['cid'] ?: null,
                    'calid' => $call['calid'],//OPEN,CLSD
                    'site_number' => $call['siteno'] ?: null,
                    'company_number' => $call['companyno'] ?: null,
                    'xid' => $call['xid'] ?: null,
                    'xresult' => $call['siteno'] ?: null,
                    'started_at' => $call['startpts'],
                    'connected_at' => $call['connectpts'] ?: null,
                    'hang_up_at' => $call['hangpts'] ?: ($call['connectpts'] ?: $call['startpts']),
                ];

                $ourCall->update($callData);
                echo $garpnum . '--';
            }

            $this->info('Calls updated successfully. Total: ' . $count);
            Log::info('Calls updated successfully. Total: ' . $count);
            if (count($calls) >= 1000) $this->handle();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            Log::error('update:calls failed, message: ' . $exception->getMessage());
        }
    }
}
