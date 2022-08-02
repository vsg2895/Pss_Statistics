<?php

namespace App\Console\Commands\Historical;

use App\Models\Call;
use App\Models\CallMeta;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;

class ImportOldCalls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old:calls';

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
        $lastCall = Call::withoutGlobalScope('open')->where('started_at', '>=', '2019-08-12 06:29:44')->orderBy('id', 'desc')->first();
        $datetimeFrom = '20220429000000';
        if ($lastCall) {
            $datetimeFrom = $lastCall->started_at;
            $datetimeFrom = Carbon::parse($datetimeFrom)->format('YmdHis');
        }

        $response = Http::withHeaders([
//            'Authorization' => 'Basic TklLQkVSOjdYRlNWVjlS',
            'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
//    ])->get("https://gcm.servit.se/RestAPI/V1/calls?startts:gt=20210727120000&startts:lt=20210727130000");
        ])->get("https://gcm.servit.se/RestAPI/V1/calls?startts:gt=$datetimeFrom");

        $calls = json_decode($response->body(), true) ?: [];

        $count = 0;
        foreach ($calls as $call) {
            $count++;
            $callData = [
                'call_id' => intval($call['garpnum']),
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
                'call_id' => intval($call['garpnum']),
                'meta_data' => json_encode($call)
            ];

            Call::withoutGlobalScope('open')->updateOrCreate(['call_id' => intval($call['garpnum'])], $callData);
//            Call::withoutGlobalScope('open')->create($callData);
//            CallMeta::create($callMeta);
            CallMeta::updateOrCreate(['call_id' => intval($call['garpnum'])], $callMeta);
            echo intval($call['garpnum']) . '--';

        }

        echo 'RESULT: imported ' . $count . '=========================';

        if (count($calls) >= 1000) $this->handle();
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
