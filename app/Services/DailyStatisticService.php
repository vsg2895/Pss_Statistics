<?php

namespace App\Services;

use App\Models\DailyChat;
use App\Models\DailyStatistic;
use App\Models\EazyChat;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DailyStatisticService
{
    private bool $rangeFilter;
    private $start;
    private $end;
    private $start_date;
    private $compare_date;

    public function __construct($rangeFilter, $start, $end, $start_date, $compare_date)
    {
        $this->rangeFilter = $rangeFilter;
        $this->start = $start;
        $this->end = $end;
        $this->start_date = $start_date;
        $this->compare_date = $compare_date;
    }

    public function getDailyStats($settings): array
    {
        $dailyStats = [];
        $todayQuery = $this->getDateQueryStatistic();
//        $dailyStats['today']['daily_calls'] = $todayQuery->sum('daily_calls');
        $dailyStats['today']['daily_bookings'] = $todayQuery->sum('daily_bookings');
        $dailyStats['today']['daily_login_time'] = $todayQuery->sum('daily_login_time');
        $dailyStats['today']['daily_login_time_show'] = get_hour_format($dailyStats['today']['daily_login_time']);
        $dailyStats['today']['daily_chats'] = $this->getDateQueryChats()->count();
        $dailyStats['today']['daily_chats'] += $this->getEazyChatsQuery()->count();

        $calls = $this->getQueryCalls();

        $dailyStats = $this->getCallsData($dailyStats, $calls, $settings);

        if (!$this->rangeFilter) {
            $yesterdayQuery = $this->getDateQueryStatistic('last_week');
//            $dailyStats['last_week']['daily_calls'] = $yesterdayQuery->sum('daily_calls');
            $dailyStats['last_week']['daily_bookings'] = $yesterdayQuery->sum('daily_bookings');
            $dailyStats['last_week']['daily_login_time'] = $yesterdayQuery->sum('daily_login_time');
            $dailyStats['last_week']['daily_login_time_show'] = get_hour_format($dailyStats['last_week']['daily_login_time']);
            $dailyStats['last_week']['daily_chats'] = $this->getDateQueryChats('last_week')->count();
            $dailyStats['last_week']['daily_chats'] += $this->getEazyChatsQuery('last_week')->count();


            $calls = $this->getQueryCalls('last_week');
            $dailyStats = $this->getCallsData($dailyStats, $calls, $settings, 'last_week');
        }

        return $this->getMedianValues($dailyStats, $settings);
    }

    private function getCallsData($dailyStats, $calls, $settings, $check = 'today')
    {
        if ($this->rangeFilter) {
            $daysDiff = Carbon::parse($this->start)->diffInDays(Carbon::parse($this->end));
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
        $userStats = $this->getDateQueryStatistic()
            ->select('daily_statistics.*', 'imported_users.servit_username as username', 'imported_users.id as user_id')
            ->leftJoin('imported_users', 'daily_statistics.servit_user_id', '=', 'imported_users.servit_id')
            ->with(['dailyStatisticMeta', 'agent.attachment', 'agent.latestAgentLog'])
            ->orderBy('daily_calls', 'desc')
            ->get();

        $servitChats = $this->getDateQueryChats()->select('*', DB::raw('count(id) as daily_chats'))
            ->groupBy('user_id')->pluck('daily_chats', 'user_id')->toArray();
        $eazyChats = $this->getEazyChatsQuery()->select('*', DB::raw('count(id) as daily_chats'))
            ->groupBy('imported_user_id')->pluck('daily_chats', 'imported_user_id')->toArray();

        $chats = sum_arrays($eazyChats, $servitChats);

        $usIt = [];

        foreach ($userStats->groupBy('user_id') as $userId => $item) {
            $usIt[$userId]['daily_chats'] = array_key_exists($userId, $chats)
                ? $chats[$userId] : 0;

            $agentPoint = $item[0]->agent->agent_point ?: $settings['main_point'];

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

            $usIt[$userId]['pause_time'] = $item->sum('dailyStatisticMeta.inqpause');
            $usIt[$userId]['talk_time'] = $item->sum('dailyStatisticMeta.inqtalk');

            $usIt[$userId]['profile_pic'] = $item[0]->agent->attachment ? $item[0]->agent->attachment->path : asset('images/personlig/default.jpg');
            $usIt[$userId]['from_office'] = $this->getFromOfficeStatus($item[0]->agent->latestAgentLog, $settings['office_ip']);
        }

        usort($usIt, 'sort_by_calls');
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
                    $query = DailyStatistic::where('date', $this->start_date ?? date('Y-m-d'));
                    break;
                default:
                    $query = DailyStatistic::where('date', $this->compare_date ?? Carbon::now()->subDays(7)->format('Y-m-d'));
            }
        } else {
            $query = DailyStatistic::whereBetween('date', [$this->start, $this->end]);
        }

        return $query;
    }

    private function getDateQueryChats($check = 'today')
    {
        if (!$this->rangeFilter) {
            switch ($check) {
                case 'today':
                    $query = DailyChat::where('date', $this->start_date ?? date('Y-m-d'));
                    break;
                default:
                    $query = DailyChat::where('date', $this->compare_date ?? Carbon::now()->subDays(7)->format('Y-m-d'));
            }
        } else {
            $query = DailyChat::whereBetween('date', [$this->start, $this->end]);
        }

        return $query;
    }

    private function getEazyChats($chats, $check = 'today')
    {
        if (!$this->rangeFilter) {
            switch ($check) {
                case 'today':
                    $query = DailyChat::where('date', $this->start_date ?? date('Y-m-d'));
                    break;
                default:
                    $query = DailyChat::where('date', $this->compare_date ?? Carbon::now()->subDays(7)->format('Y-m-d'));
            }
        } else {
            $query = DailyChat::whereBetween('date', [$this->start, $this->end]);
        }

        return $query;
    }

    private function getEazyChatsQuery($check = 'today')
    {
        if (!$this->rangeFilter) {
            switch ($check) {
                case 'today':
                    $query = EazyChat::whereRaw('DATE(date) = ?', $this->start_date ? [$this->start_date] : [date('Y-m-d')]);
                    break;
                default:
                    $query = EazyChat::whereRaw('DATE(date) = ?', $this->compare_date ? [$this->compare_date] : [Carbon::now()->subDays(7)->format('Y-m-d')]);
            }
        } else {
            $query = EazyChat::whereRaw('DATE(date) >= ?', [$this->start])
                ->whereRaw('DATE(date) <= ?', [$this->end]);
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
                    $query = $query->whereRaw('DATE(started_at) = ?', $this->start_date ? [$this->start_date] : [date('Y-m-d')]);
                    break;
                default:
                    $query = $query->whereRaw('DATE(started_at) = ?', $this->compare_date ? [$this->compare_date] : [Carbon::now()->subDays(7)->format('Y-m-d')]);
            }
        } else {//if selected date range, no need to compare with previous week
            $query = $query->whereRaw('DATE(started_at) >= ?', [$this->start])
                ->whereRaw('DATE(started_at) <= ?', [$this->end]);
        }

        //todo check connected_at is not null, is null values included or not
        $query->addSelect(DB::raw("TIMESTAMPDIFF(second,started_at,connected_at) AS waiting_time"),
            DB::raw("TIMESTAMPDIFF(second,started_at,hang_up_at) AS missed_waiting_time"),
            DB::raw("TIMESTAMPDIFF(second,connected_at,hang_up_at) AS talk_time"));

        return $query;
    }

    private function getAboveSixteenCalls($answeredCalls)
    {
        $above60 = $answeredCalls->filter(function ($call) {
            return $call->talk_time > 60;
        });

        return $above60->map(function ($call) {
            return $call->talk_time - 60;
        })->sum();
    }

    private function getFromOfficeStatus($latestAgentLog, $officeIp)
    {
        $fromOfficeStatus = null;
        if ($latestAgentLog) {
            if ($latestAgentLog->ip == $officeIp) $fromOfficeStatus = true;
            else $fromOfficeStatus = false;
        }

        return $fromOfficeStatus;
    }
}
