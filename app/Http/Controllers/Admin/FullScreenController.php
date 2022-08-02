<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\DailyStatisticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class FullScreenController extends Controller
{
    private DailyStatisticService $dailyStatisticService;

    public function __construct()
    {
        $this->dailyStatisticService = new DailyStatisticService(
            request()->date_range === 'true',
            date('Y-m-d'),
            date('Y-m-d'),
            request()->start_date,
            request()->compare_date
        );
    }

    public function index(Request $request)
    {
        $settings = Setting::pluck('value', 'slug')->toArray();
        $dailyStats = $this->dailyStatisticService->getDailyStats($settings);
        $userStats = $this->dailyStatisticService->getUserStats($settings);
        $liveData = Cache::get('svara_live') ?: [];


        return $request->expectsJson()
            ? response()->json(view('async.fullScreen', [
                'dailyStats' => $dailyStats,
                'userStats' => $userStats,
                'liveData' => $liveData,
            ])->render())
            : view('pages.admin.fullScreen', [
                'dailyStats' => $dailyStats,
                'userStats' => $userStats,
                'liveData' => $liveData,
            ]);
    }

    public function getLiveData()
    {
//        Cache::forget('svara_live');
//        Cache::forget('user_status');
        $liveData = Cache::get('svara_live');
        $userStatuses = Cache::get('user_status');

        return response()->json(['calls_queue' => $liveData, 'user_status' => $userStatuses]);
    }
}
