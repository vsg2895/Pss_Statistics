<?php

namespace App\Console\Commands;

use App\Models\Call;
use App\Models\CallMeta;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportCalls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:calls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import calls with meta data';

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
            $lastCall = Call::withoutGlobalScope('open')->where('started_at', '>=', Carbon::now()->subDay()->format('Y-m-d H:i:s'))
                ->orderBy('id', 'desc')->first();
            $datetimeFrom = Carbon::today();
            if ($lastCall) $datetimeFrom = $lastCall->started_at;
            $datetimeTo = Carbon::parse($datetimeFrom)->format('YmdHis');
            $datetimeFrom = Carbon::parse($datetimeFrom)->subMinutes(15)->format('YmdHis');
            $currentHour = Carbon::now()->format('H:i');

//            $url = "https://gcm.servit.se/RestAPI/V1/calls?startts:ge=$datetimeFrom&startts:le=$datetimeTo";
//            if ($currentHour === "06:56") //import CLSD status calls during night
                $url = "https://gcm.servit.se/RestAPI/V1/calls?startts:ge=$datetimeFrom";

            //if carbon now == 7, import night calls
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
            ])->get($url);

            $calls = json_decode($response->body(), true) ?: [];

            $this->info('Calls import started with ' . count($calls) . ' calls.');
            $count = 0;
            foreach ($calls as $call) {
                $count++;
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
                    'hang_up_at' => $call['hangpts'],
                ];

                $call = $this->unsetKeys($call);
                $callMeta = [
                    'meta_data' => json_encode($call)
                ];

                Call::withoutGlobalScope('open')->updateOrCreate(['call_id' => intval($call['garpnum'])], $callData);
                CallMeta::updateOrCreate(['call_id' => intval($call['garpnum'])], $callMeta);
                echo $garpnum . '--';
            }

            $this->info('Calls imported successfully. Total: ' . $count);
            Log::info('Calls imported successfully. Total: ' . $count);

            if (count($calls) >= 1000) $this->handle();
        } catch (\Exception $exception) {//niklas.berg@personligtsvar.se
            $this->error($exception->getMessage());
            Log::error('import:calls failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine());
        }
    }

    private function unsetKeys($call)
    {
        unset($call['callid']);
        unset($call['agentid']);
        unset($call['aid']);
        unset($call['bid']);
        unset($call['cid']);
        unset($call['calid']);
        unset($call['siteno']);
        unset($call['companyno']);
        unset($call['xid']);
        unset($call['siteno']);
        unset($call['startpts']);
        unset($call['startts']);
        unset($call['connectpts']);
        unset($call['connecpts']);
        unset($call['hangpts']);
        unset($call['hangts']);
        return $call;
    }

}
