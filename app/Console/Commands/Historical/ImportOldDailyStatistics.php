<?php

namespace App\Console\Commands\Historical;

use App\Models\DailyStatistic;
use App\Models\DailyStatisticMeta;
use App\Models\ImportedUser;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportOldDailyStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old:daily';

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
//        $from = '20190819';
//        $to = '20200101';

//        $from = '20200101';
//        $to = '20200601';

//        $from = '20200601';
//        $to = '20200831';

//        $from = '20200831';
//        $to = '20201131';

//        $from = '20201131';
//        $to = '20210131';

//        $from = '20210131';
//        $to = '20210431';

//        $from = '20210431';
//        $to = '20210731';

        $from = '20210731';
        $to = '20210922';

        $dailyStatistics = $this->getDailyStatistics($from, $to);

        foreach ($dailyStatistics['data'] as $datum) {
            $statistic = DailyStatistic::updateOrCreate(
                ['servit_user_id' => $datum['servit_user_id'], 'date' => $datum['date']],
                [
                    'daily_calls' => $datum['daily_calls'],
                    'daily_bookings' => $datum['daily_bookings'],
                    'daily_login_time' => $datum['daily_login_time'],
                    'date' => $datum['date']
                ]
            );

            $datum['meta']['daily_statistic_id'] = $statistic->id;
            DailyStatisticMeta::updateOrCreate(['daily_statistic_id' => $statistic->id], $datum['meta']);
            echo $datum['servit_user_id'] . '-' . $datum['date'] . '/';
        }

        $this->info("User statistics added from: $from to: $to Added: " . $dailyStatistics['servitUserStats'] . " Skiped: " . $dailyStatistics['notServitUserStats']);
        Log::info("User statistics added from: $from to: $to Added: " . $dailyStatistics['servitUserStats'] . " Skiped: " . $dailyStatistics['notServitUserStats']);

    }

    private function getDailyStatistics($from, $to)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
            'Cookie' => 'PHPSESSID=4jhtrml2nfu4qnrp2phelktnem'
//        ])->get("https://gcm.servit.se/RestAPI/V1/userstats?date:ge=$today&date:le=$today&teamid=*TOT*");
        ])->get("https://gcm.servit.se/RestAPI/V1/userstats?date:ge=$from&date:le=$to&teamid=*TOT*");

        $dailyStats = json_decode($response->body(), true) ?: [];
        $userIds = ImportedUser::servitUsers()->pluck('servit_id', 'id')->toArray();

        $data = [];
        $servitUserStats = 0;
        $notServitUserStats = 0;
        foreach ($dailyStats as $stat) {

            if (in_array($stat['userid'], $userIds)) {
                $data[] = [
                    'servit_user_id' => $stat['userid'],
                    'daily_calls' => $stat['inqcalls'],
                    'daily_bookings' => $stat['crmlogspec'],
                    'daily_login_time' => $stat['inqtot'],
                    'date' => Carbon::parse($stat['date'])->format('Y-m-d'),
                    'meta' => [
                        'inqready' => $stat['inqready'],
                        'inqinc' => $stat['inqinc'],
                        'inqtalk'   => $stat['inqtalk'],
                        'inqbusy' => $stat['inqbusy'],
                        'inqwrap' => $stat['inqwrap'],
                        'inqpause' => $stat['inqpause'],
                        'inqring' => $stat['inqring'],
                        'repbusy' => $stat['repbusy'],
                        'repnorep' => $stat['repnorep'],
                        'xferout' => $stat['xferout'],
                        'confout' => $stat['confout'],
                        'confmiss' => $stat['confmiss']
                    ],
                ];

                $servitUserStats++;
            } else {
                $notServitUserStats++;
            }
        }


        return ['data' => $data, 'servitUserStats' => $servitUserStats, 'notServitUserStats' => $notServitUserStats];
    }
}
