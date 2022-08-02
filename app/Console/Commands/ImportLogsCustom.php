<?php

namespace App\Console\Commands;

use App\Models\DailyLog;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportLogsCustom extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:logsCustom';

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
//            $logs = $this->getLogs();
//            dd($logs);
            //"10:00-11:00"
        //"agent_id" => "THEWID" missing from servit

            $hours = get_working_hours();
            foreach ($hours as $start => $end) {
                $date = "20210903";

                $hourAgo = Carbon::parse($date.'080001')->subHour()->startOfHour();
                $dateTimeFrom = $date . str_replace(':', '', $start) . "00";
                $dateTimeTo = $date . str_replace(':', '', $end) . "00";
                $startHour = $start;
                $hourRange = $startHour . '-' . $end;
                $getDateTimeFrom = $dateTimeFrom;

                if ($start !== "13:00" && $start !== "14:00")
                    continue;

                if ($start === "07:00")
                    $getDateTimeFrom = $hourAgo->subHour()->format('YmdHis');//get logged in data started from 06:00

                $responseLogon = Http::withHeaders([
                    'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
                ])->get("https://gcm.servit.se/RestAPI/V1/userlogs?useraction=LOGON&ts:gt=$getDateTimeFrom&ts:lt=$dateTimeTo");
//          ])->get("https://gcm.servit.se/RestAPI/V1/userlogs?useraction=LOGON&ts:gt=20210826130000&ts:lt=20210826140000");

                $logonData = json_decode($responseLogon->body(), true) ?: [];

                $responseLogoff = Http::withHeaders([
                    'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
                ])->get("https://gcm.servit.se/RestAPI/V1/userlogs?useraction=LOGOFF&ts:gt=$dateTimeFrom&ts:lt=$dateTimeTo");
//        ])->get("https://gcm.servit.se/RestAPI/V1/userlogs?useraction=LOGOFF&ts:gt=20210826130000&ts:lt=20210826140000");

                $logoffData = json_decode($responseLogoff->body(), true) ?: [];

                //if there is one row, servit response is one array, otherwise it is arrays in one array
                if (isset($logonData['ts'])) $logonData = [$logonData];
                if (isset($logoffData['ts'])) $logoffData = [$logoffData];

                $logonData = collect($logonData)->sortBy('ts')->groupBy('userid')->all();
                $logoffData = collect($logoffData)->sortBy('ts')->groupBy('userid')->all();

//                dd($responseLogon->body(), $responseLogoff->body());

//                if ($start === "08:00")
//                    dd($logonData, $logoffData);

                $insertData = [];

                foreach ($logonData as $username => $logonItem) {//insert based on logon data


//                    if ($username !== 'EMIFOG') continue;


                    for ($i = 0; $i < count($logonItem); $i++) {
                        if (isset($logoffData[$username])) {
                            $alreadyLogon = $logonItem[0]['ts'] > $logoffData[$username][0]['ts'];//logon during previous hours
                            if ($i === 0) {
                                if ($alreadyLogon) {
                                    $insertData[] = $this->getLogData($date, $username, $hourRange, null, $logoffData[$username][0]['ts'], $dateTimeFrom);
                                    if (count($logoffData[$username]) > 1) {//if already logon, but have more logs
                                        $insertData[] = $this->getLogData($date, $username, $hourRange, $logonItem[0]['ts'], $logoffData[$username][$i+1]['ts']);
                                    } elseif (count($logonItem) === 1 && count($logoffData[$username]) === 1) {
                                        $insertData[] = $this->getLogData($date, $username, $hourRange, $logonItem[0]['ts'], null, $dateTimeFrom, $dateTimeTo);
                                    }
                                } else {
                                    $insertData[] = $this->getLogData($date, $username, $hourRange, $logonItem[0]['ts'], $logoffData[$username][0]['ts']);//0 and $i is the same for this case
                                }
                            } else {
                                if ($alreadyLogon) {
                                    $logoffTime = isset($logoffData[$username][$i + 1]['ts']) ? $logoffData[$username][$i + 1]['ts'] : null;
                                } else {
                                    $logoffTime = isset($logoffData[$username][$i]['ts']) ? $logoffData[$username][$i]['ts'] : null;
                                }
                                $insertData[] = $this->getLogData($date, $username, $hourRange, $logonItem[$i]['ts'], $logoffTime, $dateTimeFrom, $dateTimeTo);
                            }
                        } else {
                            $insertData[] = $this->getLogData($date, $username, $hourRange, $logonItem[$i]['ts'], null, null, $dateTimeTo);
                        }
                    }
                }

                foreach ($logoffData as $username => $logoffItem) {//insert based on logoff data (users, that logoff during that hour range, but logon before it)
                    if (!isset($logonData[$username])) {
                        $insertData[] = $this->getLogData($date, $username, $hourRange, null, $logoffItem[0]['ts'], $dateTimeFrom);
                    }
                }

                $insertData = $this->checkExistingLogons($date, $hourRange, $insertData, $logonData, $logoffData);

                DB::table('daily_logs')->insert($insertData);

                $message = "Logs imported successfully. Hour Range: $hourRange, Count: " . count($insertData);
                $this->info($message);
                Log::info($message);
            }
        } catch (\Exception $exception) {
            $this->error($exception->getMessage() . ' Line: ' . $exception->getLine());
            Log::error('import:logs failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine());
        }
    }

    private function getMinutesDiff($start, $end)
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        return $start->diffInMinutes($end);
    }

    private function getLogData($date, $username, $hourRange, $logonTime, $logoffTime, $dateTimeFrom = null, $dateTimeTo = null)
    {
        $logonTime  = $logonTime ? Carbon::parse($logonTime)->format('Y-m-d H:i:s') : $logonTime;
        $logoffTime = $logoffTime ? Carbon::parse($logoffTime)->format('Y-m-d H:i:s') : $logoffTime;

        return [
            'agent_id' => $username,
            'time_range' => $hourRange,
            'login_minutes' => $this->getMinutesDiff($logonTime ?: $dateTimeFrom, $logoffTime ?: $dateTimeTo),
            'logon_time' => $logonTime,
            'logoff_time' => $logoffTime,
            'date' => Carbon::parse($date)->format('Y-m-d'),
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    private function checkExistingLogons($date, $hourRange, $insertData, $logonData, $logoffData)
    {
        $previousHourRange = $this->getPreviousHourRange($hourRange);
        $todayDate = Carbon::parse($date)->format('Y-m-d');
        $existingLogonData = DailyLog::where('date', $todayDate)->where('time_range', $previousHourRange)
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->whereNull('logoff_time')->where('logon_time', '<>', null);
                })->orWhere(function ($q) {
                    $q->whereNull('logoff_time')->whereNull('logon_time');
                });
            })->groupBy('agent_id')->get();

        foreach ($existingLogonData as $item) {
            if (!array_key_exists($item->agent_id, $logonData) && !array_key_exists($item->agent_id, $logoffData)) {
                $insertData[] = [
                    'agent_id' => $item->agent_id,
                    'time_range' => $hourRange,
                    'login_minutes' => 60,
                    'logon_time' => null,
                    'logoff_time' => null,
                    'date' => $todayDate,
                    'created_at' => date('Y-m-d H:i:s')
                ];
            }
        }

        return $insertData;
    }

    private function getLogs()
    {
        return DailyLog::select('*', DB::raw('SUM(login_minutes) as total_minutes'))
            ->dateRange("2021-08-30", "2021-08-30")->where('time_range', '17:00-18:00')
            ->groupBy('agent_id')->get()->toArray();
    }

    private function getPreviousHourRange($hourRange)
    {
        $parts = explode('-', $hourRange);

        return Carbon::parse($parts[0])->subHour()->format('H') . ':00-' . Carbon::parse($parts[1])->subHour()->format('H') . ':00';
    }

}
