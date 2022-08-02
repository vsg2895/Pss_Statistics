<?php

namespace App\Services;

use App\Models\Call;
use App\Models\DailyChat;
use App\Models\DailyStatistic;
use App\Models\DailyStatisticMeta;
use App\Models\EazyChat;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class EmployeeStatisticService extends BaseService
{
    private $start_date;
    private $end_date;

    public function __construct($startDate = null, $endDate = null)
    {
        $this->start_date = $startDate;
        $this->end_date = $endDate;
    }

    public function getDailyUserStats($settings, $servitUser)
    {
        $todayQuery = $this->getStatistics($servitUser->servit_id);
        $agentPoint = $servitUser->agent_point;

        $dailyStats = $this->getFromMetaData($todayQuery);
        $dailyStats['bookings'] = $todayQuery->sum('daily_bookings');
        $dailyStats['login_time'] = $todayQuery->sum('daily_login_time');

        $todayChats = $this->getChats($servitUser->id);
        $dailyStats['chats'] = $todayChats ?? 0;

        $todayAvgTalkTime = $this->getAvgTalkTimeCalls($servitUser->servit_id);
        $dailyStats['calls'] = $this->getCalls($servitUser->servit_id);
        $dailyStats['weekday_calls'] = $this->getWeekdayCalls($servitUser->servit_id);

        $dailyStats['avg_talk_time'] = $todayAvgTalkTime;
        $dailyStats['progress'] = $this->getUserProgress($dailyStats, $settings, $agentPoint);

        return $this->getMedianValues($dailyStats, $settings, $agentPoint);
    }

    public function getMedianValues($dailyStats, $settings, $agentPoint)
    {
        $dailyTotalPoints = $dailyStats['calls'] * $settings['calls_point']
            + $dailyStats['bookings'] * $settings['bookings_point']
            + $dailyStats['chats'] * $settings['chats_point'];


        $totalDailyMedian = get_median_value($dailyTotalPoints, $dailyStats['login_time'], $agentPoint ?: $settings['main_point']);

        $dailyStats['median_value'] = $totalDailyMedian;

        return $dailyStats;
    }

    private function getStatistics($servitId)
    {
        $query = DailyStatistic::where('servit_user_id', $servitId)->with(['agent' => function($query) {
            $query->select('servit_id', 'agent_point');
        }]);
        return  $this->start_date
            ? $query->dateRange($this->start_date, $this->end_date)->get()
            : $query->today()->get();
    }

    private function getChats($importedUserId)
    {
        $query = DailyChat::where('user_id', $importedUserId);
        $query = $this->start_date ? $query->dateRange($this->start_date, $this->end_date) : $query->today();
        $servitChatCount = $query->count();

        $query = EazyChat::where('imported_user_id', $importedUserId);
        $query = $this->start_date ? $query->dateRange($this->start_date, $this->end_date) : $query->today();
        $eazyChatCount = $query->count();

        return $servitChatCount + $eazyChatCount;
    }

    private function getFromMetaData($userStatistics)
    {
        $statisticIds = $userStatistics->pluck('id')->toArray();
        $statisticMeta = DailyStatisticMeta::whereIn('daily_statistic_id', $statisticIds)->get();

        $dailyStats['avg_pickup_time'] = round($statisticMeta->sum('inqring') / max($userStatistics->sum('daily_calls'), 1));
        $dailyStats['pause_time'] = round($statisticMeta->sum('inqpause'));
        $dailyStats['repbusy'] = round($statisticMeta->sum('repbusy'));
        $dailyStats['repnorep'] = round($statisticMeta->sum('repnorep'));

        return $dailyStats;
    }

    private function getAvgTalkTimeCalls($servitId)
    {
        $query =  $this->start_date ? Call::dateRange($this->start_date, $this->end_date) : Call::today();
        $query = $query->answered()->where('agent_id', $servitId)
            ->select("*", DB::raw( "TIMESTAMPDIFF(second,connected_at,hang_up_at) AS talk_time"));

        return round($query->get()->sum('talk_time') / max($query->count(), 1), 2);
    }

    public function getUserProgress($stat, $settings, $agentPoint)
    {
        $totalPoints = $stat['calls'] * $settings['calls_point']
            + $stat['bookings'] * $settings['bookings_point']
            + $stat['chats'] * $settings['chats_point'];

        return get_median_value($totalPoints, $stat['login_time'], $agentPoint ?: $settings['main_point']);
    }

    private function getCalls($agentId)
    {
        $query = DB::table('calls')->select('id', 'agent_id', 'started_at')
            ->where('agent_id', $agentId);

        if ($this->start_date) {
            $query = $query->whereRaw('DATE(started_at) >= ?', [$this->start_date])
                ->whereRaw('DATE(started_at) <= ?', [$this->end_date]);
        } else {
            $query = $query->whereRaw('DATE(started_at) = ?', [date('Y-m-d')]);
        }

        return $query->count();
    }

    private function getWeekdayCalls($agentId)
    {
        $startOfTheWeek = Carbon::now()->startOfWeek()->format('Y-m-d');

        return DB::table('calls')->select('id', 'agent_id', 'started_at',
            DB::raw('DAYNAME(started_at) as day_name'), DB::raw('COUNT(id) as count'))
            ->where('agent_id', $agentId)->whereRaw('DATE(started_at) >= ?', [$startOfTheWeek])
            ->whereRaw('DATE(started_at) <= ?', [date("Y-m-d")])
            ->groupBy('day_name')->pluck('count', 'day_name')->toArray();
    }
}
