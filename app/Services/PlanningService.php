<?php

namespace App\Services;

use App\Models\DailyLog;
use App\Models\ImportedUser;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PlanningService extends BaseService
{
    public function getHourlyData($start = null, $end = null)
    {
        if (!is_null($start) && !is_null($end)) {
            request()->start_date = $start;
            request()->end_date = $end;
        }

        $settings = Setting::getSettings();
        $days = $this->getDaysDiff();
        $hours = get_working_hours();
        $calls = $this->getCalls();
        $bookings = $this->getBookings();
        $chats = $this->getChats();
        $logs = $this->getLogs();

        $data = $this->getAllData($calls, $bookings, $chats, $logs, $hours, $days, $settings);
        return $this->getAvgEmployeeCount($data, $days, $settings);
    }

    private function getDaysDiff()
    {
        $days = 1;

        if (request()->start_date) {
            $start = Carbon::parse(request()->start_date);
            $end = Carbon::parse(request()->end_date)->addDay();
            $days = $start->diffInDaysFiltered(function (Carbon $date) {
                return !$date->isWeekend();
            }, $end);
        }

        return $days;
    }

    private function getSecondsDiff($start, $end): float|int
    {
        $start = Carbon::parse($start);
        $end = Carbon::parse($end);
        return $start->diffInSeconds($end);
    }

    private function getCalls(): \Illuminate\Support\Collection
    {
        $query = DB::table('calls')->select('id', 'started_at', 'connected_at', 'hang_up_at', 'agent_id',
            DB::raw("TIMESTAMPDIFF(second,started_at,hang_up_at) AS missed_waiting_time"));
        $query = request()->start_date
            ? $query->whereRaw('DATE(started_at) >= ?', [request()->start_date])
                ->whereRaw('DATE(started_at) <= ?', [request()->end_date])
            : $query->whereRaw('DATE(started_at) = ?', [date('Y-m-d')]);

//        return $query->lazyById();
        return $query->get();//todo check big time range issue
    }

    private function getBookings(): \Illuminate\Support\Collection
    {
        $query = DB::table('bookings')->select(DB::raw("COUNT(id) as count"), DB::raw("CONCAT(TIME_FORMAT(added_at, '%H'), ':00') as hour"));
        $query = request()->start_date
            ? $query->whereRaw('DATE(added_at) >= ?', [request()->start_date])
                ->whereRaw('DATE(added_at) <= ?', [request()->end_date])
            : $query->whereRaw("DATE(added_at) = ?", [date('Y-m-d')]);

        return $query->groupBy(DB::raw("HOUR(added_at)"))->pluck('count', 'hour');
    }

    private function getChats(): \Illuminate\Support\Collection
    {
        $query = DB::table('daily_chats')->select(DB::raw("COUNT(id) as count"), DB::raw("CONCAT(TIME_FORMAT(date_created, '%H'), ':00') as hour"));

        $query = request()->start_date
            ? $query->whereRaw('DATE(date_created) >= ?', [request()->start_date])
                ->whereRaw('DATE(date_created) <= ?', [request()->end_date])
            : $query->whereRaw("DATE(date_created) = ?", [date('Y-m-d')]);

        return $query->groupBy(DB::raw("HOUR(date_created)"))->pluck('count', 'hour');
    }

    private function getLogs(): array
    {
        $query = DailyLog::select('*', DB::raw('SUM(login_minutes) as total_minutes'),
            DB::raw('GROUP_CONCAT(DISTINCT agent_id) as agent_ids'))->groupBy('time_range');
        $query = request()->start_date
            ? $query->dateRange(request()->start_date, request()->end_date)
            : $query->today();

        $data = [];
        foreach ($query->get() as $item) {
            $agentIds = explode(',', $item->agent_ids);
            $data[$item->time_range] = [
                'total_minutes' => $item->total_minutes,
                'agent_ids' => $agentIds,
            ];
        }

        return $data;
    }

    private function getAllData($calls, $bookings, $chats, $logs, $hours, $days, $settings): array
    {
        $data = [];
        foreach ($hours as $start => $end) {
            $currentHourCalls = $calls->filter(function ($item) use ($start, $end) {
                $itemHour = Carbon::parse($item->started_at)->format('H');
                $start = explode(':', $start)[0];
                $end = explode(':', $end)[0];
                return $itemHour >= $start && $itemHour < $end;
            });
            $data[$start . '-' . $end]['calls'] = count($currentHourCalls);
            $data[$start . '-' . $end]['avg_waiting_time'] = $this->getAvgWaitingTime($currentHourCalls);
            $data[$start . '-' . $end]['agentsCount'] = isset($logs[$start . '-' . $end]['total_minutes'])
                ? round($logs[$start . '-' . $end]['total_minutes'] / (60 * $days), 1)
                : 0;
            $data[$start . '-' . $end]['agentsList'] = isset($logs[$start . '-' . $end]['agent_ids'])
                ? $this->getAgentNames($logs[$start . '-' . $end]['agent_ids'])
                : [];

            $data[$start . '-' . $end]['missedCalls'] = $this->getMissedCallsCount($currentHourCalls, $settings);
            $data[$start . '-' . $end]['progress'] = $this->getProgress($data[$start . '-' . $end], $settings, $days);
            $data[$start . '-' . $end]['difference_percentage'] = $settings['difference_percentage'];
            $data[$start . '-' . $end]['bookings'] = isset($bookings[$start]) ? $bookings[$start] : 0;
            $data[$start . '-' . $end]['chats'] = isset($chats[$start]) ? $chats[$start] : 0;
        }

        return $data;
    }

    private function getAvgWaitingTime($currentHourCalls): float
    {
        $answeredCalls = $currentHourCalls->filter(function ($call) {
            return !is_null($call->connected_at);
        });

        $answeredCalls->map(function ($call) {
            $call->waiting_time = $this->getSecondsDiff($call->started_at, $call->connected_at);
            return $call;
        });

        return round($answeredCalls->sum('waiting_time') / max(count($answeredCalls), 1));
    }

    private function getMissedCallsCount($currentHourCalls, $settings): int
    {
        $groupedByAgents = $currentHourCalls->groupBy('agent_id');
        $missedCalls = isset($groupedByAgents[""]) ? $groupedByAgents[""] : collect([]);

        $missedCalls = $missedCalls->filter(function ($item, $index) use ($settings) {
            return $item->missed_waiting_time >= $settings['missed_call_seconds'];
        });

        return count($missedCalls);
    }

    private function getAvgEmployeeCount($data, $days, $settings): array
    {
        $data = collect($data)->map(function ($datum) use ($settings, $days) {
            $totalPoints = $datum['calls'] * $settings['calls_point'];
            $datum['teoryEmpCount'] = round($totalPoints / ($settings['main_point_planning_page'] * $days), 1);

            return $datum;
        });

        return $data->all();
    }

    private function getProgress($hourData, $settings, $days): float|int
    {
        if ($hourData['agentsCount'] === 0) {
            return 0;
        } else {
            $earnedPoints = ($hourData['calls'] - $hourData['missedCalls']) * $settings['calls_point'];
            $expectedPoints = $settings['main_point_planning_page'] * $hourData['agentsCount'] * $days;

            return round($earnedPoints * 100 / max($expectedPoints, 1));
        }
    }

    private function getAgentNames($agentIds)
    {
        return ImportedUser::select('servit_username')->whereIn('servit_id', $agentIds)->get()
            ->pluck('servit_username')->toArray();
    }

}
