<?php

namespace App\Services;

use App\Contracts\StatisticService;
use App\Contracts\StatisticServiceInterface;
use App\Models\Call;
use App\Models\DailyChat;
use App\Models\DailyStatistic;
use App\Models\DailyStatisticMeta;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyStatisticService1 extends BaseService
{
    private $rangeFilter;

    public function __construct()
    {
        $this->rangeFilter = request()->date_range === 'true';
    }

    public function getDailyStats($settings): array
    {
        $dailyStats = [];

        $todayQuery = $this->getDateQueryStatistic()->get();
//        dd($todayQuery);
//        $dailyStats['today']['daily_calls'] = $todayQuery->sum('daily_calls');
        $dailyStats['today']['daily_bookings'] = $todayQuery->sum('daily_bookings');
        $dailyStats['today']['daily_login_time'] = $todayQuery->sum('daily_login_time');
        $dailyStats['today']['daily_login_time_show'] = get_hour_format($dailyStats['today']['daily_login_time']);
        $dailyStats['today']['daily_chats'] = $this->getDateQueryChats()->count();

        $calls = $this->getQueryCalls();

        $dailyStats = $this->getCallsData($dailyStats, $calls, $settings);

        if (!$this->rangeFilter) {
            $yesterdayQuery = $this->getDateQueryStatistic('last_week')->get();
//            $dailyStats['last_week']['daily_calls'] = $yesterdayQuery->sum('daily_calls');
            $dailyStats['last_week']['daily_bookings'] = $yesterdayQuery->sum('daily_bookings');
            $dailyStats['last_week']['daily_login_time'] = $yesterdayQuery->sum('daily_login_time');
            $dailyStats['last_week']['daily_chats'] = $this->getDateQueryChats('last_week')->count();

            $calls = $this->getQueryCalls('last_week');
            $dailyStats = $this->getCallsData($dailyStats, $calls, $settings, 'last_week');
        }

        return $this->getMedianValues($dailyStats, $settings);
    }

    private function getCallsData($dailyStats, $calls, $settings, $check = 'today') {
        if ($this->rangeFilter) {
            $daysDiff = Carbon::parse(request()->start)->diffInDays(Carbon::parse(request()->end));
            if ($daysDiff > 62)
                $calls = $calls->lazyById();
            else
                $calls = $calls->get();
        } else
            $calls = $calls->get();

        $answeredCalls = $calls->filter(function ($item) {
            return !is_null($item->connected_at);
        });

        $missedCalls = $calls->filter(function ($item) use ($settings) {
            return is_null($item->connected_at) && $item->missed_waiting_time >= $settings['missed_call_seconds'];
        });

        if ($check == 'today') {
            $dailyStats['today']['daily_calls'] = count($answeredCalls);
            $dailyStats['today']['daily_missed'] = $missedCalls->count();
            $dailyStats['today']['avg_waiting_time'] = round($answeredCalls->sum('waiting_time') / max($answeredCalls->count(), 1), 2);
            $dailyStats['today']['avg_talk_time'] = round($answeredCalls->sum('talk_time') / max($answeredCalls->count(), 1), 2);;
            $dailyStats['today']['above_sixteen'] = $this->getAboveSixteenCalls($answeredCalls);
            $dailyStats['today']['above_sixteen_money'] = round($dailyStats['today']['above_sixteen'] * $settings['money_for_second']);
        } else {
            $dailyStats['last_week']['daily_calls'] = count($answeredCalls);
            $dailyStats['last_week']['daily_missed'] = $missedCalls->count();
            $dailyStats['last_week']['avg_waiting_time'] = round($answeredCalls->sum('waiting_time') / max($answeredCalls->count(), 1), 2);
            $dailyStats['last_week']['avg_talk_time'] = round($answeredCalls->sum('talk_time') / max($answeredCalls->count(), 1), 2);;
            $dailyStats['last_week']['above_sixteen'] = $this->getAboveSixteenCalls($answeredCalls);
            $dailyStats['last_week']['above_sixteen_money'] = round($dailyStats['last_week']['above_sixteen'] * $settings['money_for_second']);
        }

        return $dailyStats;
    }

    public function getUserStats($settings)
    {
//        $userStats = $this->getDateQueryStatistic()
//            ->select('daily_statistics.*', 'imported_users.servit_username as username', 'imported_users.id as user_id', 'imported_users.agent_point as agent_point',
//                'daily_statistic_meta.inqpause as inqpause', 'daily_statistic_meta.inqtalk as inqtalk')
//            ->leftJoin('imported_users', 'daily_statistics.servit_user_id', '=', 'imported_users.servit_id')
//            ->leftJoin('daily_statistic_meta', 'daily_statistics.id', '=', 'daily_statistic_meta.daily_statistic_id')
//            ->get();

        $userStats = $this->getDateQueryStatistic()
            ->select('daily_statistics.*', 'imported_users.servit_username as username', 'imported_users.id as user_id')
            ->leftJoin('imported_users', 'daily_statistics.servit_user_id', '=', 'imported_users.servit_id')
            ->with(['dailyStatisticMeta', 'agent.attachment'])
            ->get();

        $chats = $this->getDateQueryChats()->select('*', DB::raw('count(id) as daily_chats'))
            ->groupBy('user_id')->pluck('daily_chats', 'user_id')->toArray();

        $usIt = [];

        foreach ($userStats->groupBy('user_id') as $userId => $item) {
            $usIt[$userId]['daily_chats'] = array_key_exists($userId, $chats)
                ? $chats[$userId] : 0;

            $agentPoint = $item[0]->agent_point ?: $settings['main_point'];

            $totalPoints = $item->sum('daily_calls') * $settings['calls_point']
                + $item->sum('daily_bookings') * $settings['bookings_point']
                + $usIt[$userId]['daily_chats'] * $settings['chats_point'];

            $usIt[$userId]['servit_user_id'] = $item[0]->servit_user_id;
            $usIt[$userId]['username'] = $item[0]->username;
            $usIt[$userId]['daily_calls'] = $item->sum('daily_calls');
            $usIt[$userId]['daily_bookings'] = $item->sum('daily_bookings');
            $usIt[$userId]['daily_login_time'] = get_hour_format($item->sum('daily_login_time'));

            $usIt[$userId]['points'] = $totalPoints;
            $usIt[$userId]['progress'] = get_median_value($totalPoints, $item->sum('daily_login_time'), $agentPoint);

//            $usIt[$userId]['pause_time'] = $item->sum('inqpause');
//            $usIt[$userId]['talk_time'] = $item->sum('inqtalk');
            $usIt[$userId]['pause_time'] = $item->sum('dailyStatisticMeta.inqpause');
            $usIt[$userId]['talk_time'] = $item->sum('dailyStatisticMeta.inqtalk');

            $usIt[$userId]['profile_pic'] = $item[0]->agent->attachment ? $item[0]->agent->attachment->path : asset('images/personlig/default.jpg');
        }

        return $usIt;
    }

    public function getMedianValues($dailyStats, $settings)
    {
        $dailyTotalPoints = $dailyStats['today']['daily_calls'] * $settings['calls_point']
            + $dailyStats['today']['daily_bookings'] * $settings['bookings_point']
            + $dailyStats['today']['daily_chats'] * $settings['chats_point'];
        $totalDailyMedian = get_median_value($dailyTotalPoints, $dailyStats['today']['daily_login_time'], $settings['main_point']);
        $dailyStats['today']['median_value'] = $totalDailyMedian;

        if (!$this->rangeFilter) {
            $lastWeekTotalPoints = $dailyStats['last_week']['daily_calls'] * $settings['calls_point']
                + $dailyStats['last_week']['daily_bookings'] * $settings['bookings_point']
                + $dailyStats['last_week']['daily_chats'] * $settings['chats_point'];
            $totalLastWeekMedian = get_median_value($lastWeekTotalPoints, $dailyStats['last_week']['daily_login_time'], $settings['main_point']);
            $dailyStats['last_week']['median_value'] = $totalLastWeekMedian;
        }

        return $dailyStats;
    }

    private function getDateQueryStatistic($check = 'today')
    {
        if (!$this->rangeFilter) {
            switch ($check) {
                case 'today':
                    $query = DailyStatistic::where('date', request()->start_date ?? date('Y-m-d'));
                    $query = DB::table('daily_statistics')->where('date', request()->start_date ?? date('Y-m-d'));
                    break;
                default:
//                    $query = DB::table('daily_statistics')->where('date', request()->compare_date ?? Carbon::now()->subDays(7)->format('Y-m-d'));
                    $query = DailyStatistic::where('date', request()->compare_date ?? Carbon::now()->subDays(7)->format('Y-m-d'));
            }
        } else {
//            $query = DB::table('daily_statistics')->whereBetween('date', [request()->start, request()->end]);
            $query = DailyStatistic::whereBetween('date', [request()->start, request()->end]);
        }

        return $query;
    }

    private function getDateQueryChats($check = 'today')
    {
        if (!$this->rangeFilter) {
            switch ($check) {
                case 'today':
                    $query = DailyChat::where('date', request()->start_date ?? date('Y-m-d'));
                    break;
                default:
                    $query = DailyChat::where('date', request()->compare_date ?? Carbon::now()->subDays(7)->format('Y-m-d'));
            }
        } else {
            $query = DailyChat::whereBetween('date', [request()->start, request()->end]);
        }

        return $query;
    }

    private function getQueryCalls($check = 'today')
    {
        $query = DB::table('calls')->select('id', 'started_at', 'connected_at', 'hang_up_at', 'agent_id');

        //get date filter
        if (!$this->rangeFilter) {
            switch ($check) {
                case 'today':
//                    $query = $query->today();
                    $query = $query->whereRaw('DATE(started_at) = ?', request()->start_date ? [request()->start_date] : [date('Y-m-d')]);
                    break;
                default:
                    $query = $query->whereRaw('DATE(started_at) = ?', request()->compare_date ? [request()->compare_date] : [Carbon::now()->subDays(7)->format('Y-m-d')]);
            }
        } else {//if selected date range, no need to compare with previous week
            $query = $query->whereRaw('DATE(started_at) >= ?', [request()->start])
                ->whereRaw('DATE(started_at) <= ?', [request()->end]);
        }

        $query->addSelect(DB::raw( "TIMESTAMPDIFF(second,started_at,connected_at) AS waiting_time"),
            DB::raw( "TIMESTAMPDIFF(second,started_at,hang_up_at) AS missed_waiting_time"),
            DB::raw( "TIMESTAMPDIFF(second,connected_at,hang_up_at) AS talk_time"));

        return $query;
    }

    private function getAboveSixteenCalls($answeredCalls, $check = 'today', $forUser = false, $servitId = null)
    {
        $above60 = $answeredCalls->filter(function ($call) {
            return $call->talk_time > 60;
        });

        return $above60->map(function ($call) {
            return $call->talk_time - 60;
        })->sum();
    }
}
