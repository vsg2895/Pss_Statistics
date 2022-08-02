<?php

namespace App\Console\Commands;

use App\Models\DailyStatistic;
use App\Models\DailyStatisticMeta;
use App\Models\ImportedUser;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportDailyStatistics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:daily';

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
            $dailyStatistics = $this->getDailyStatistics();

            $count = 0;
            foreach ($dailyStatistics['data'] as $datum) {
                $count++;
                $statistic = DailyStatistic::updateOrCreate(
                    [ 'servit_user_id' => $datum['servit_user_id'], 'date' => $datum['date'] ],
                    [
                        'daily_calls' => $datum['daily_calls'],
                        'daily_bookings' => $datum['daily_bookings'],
                        'daily_login_time' => $datum['daily_login_time'],
                        'date' => $datum['date']
                    ]
                );

                $dailyStatistics['metaData'][$datum['servit_user_id']]['daily_statistic_id'] = $statistic->id;
                DailyStatisticMeta::updateOrCreate(['daily_statistic_id' => $statistic->id], $dailyStatistics['metaData'][$datum['servit_user_id']]);
            }

            $this->info('User statistics updated successfully. Count: ' . $count);
            Log::info('User statistics updated successfully. Count: ' . $count);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            Log::error('import:daily failed, message: ' . $exception->getMessage());
        }
    }

    private function getDailyStatistics()
    {
        $today = date('Ymd');
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
            'Cookie' => 'PHPSESSID=4jhtrml2nfu4qnrp2phelktnem'
        ])->get("https://gcm.servit.se/RestAPI/V1/userstats?date:ge=$today&date:le=$today&teamid=*TOT*");
//        ])->get("https://gcm.servit.se/RestAPI/V1/userstats?date:ge=20210713&date:le=20210713&teamid=*TOT*");

        $dailyStats = json_decode($response->body(), true) ?: [];
        $userIds = ImportedUser::servitUsers()->pluck('servit_id', 'id')->toArray();

        $data = [];
        $metaData = [];
        foreach ($dailyStats as $stat) {
            if (in_array($stat['userid'], $userIds)) {
                $data[] = [
                    'servit_user_id' => $stat['userid'],
                    'daily_calls' => $stat['inqcalls'],
                    'daily_bookings' => $stat['crmlogspec'],
                    'daily_login_time' => $stat['inqtot'],
                    'date' => Carbon::parse($stat['date'])->format('Y-m-d'),
                ];
                $metaData[$stat['userid']] = [
                    'inqready' => $stat['inqready'],
                    'inqinc'   => $stat['inqinc'],
                    'inqtalk'   => $stat['inqtalk'],
                    'inqbusy'  => $stat['inqbusy'],
                    'inqwrap'  => $stat['inqwrap'],
                    'inqpause' => $stat['inqpause'],
                    'inqring'  => $stat['inqring'],
                    'repbusy'  => $stat['repbusy'],
                    'repnorep' => $stat['repnorep'],
                    'xferout'  => $stat['xferout'],
                    'confout'  => $stat['confout'],
                    'confmiss' => $stat['confmiss'],
                ];
            }
        }

        return ['data' => $data, 'metaData' => $metaData];
    }
}
