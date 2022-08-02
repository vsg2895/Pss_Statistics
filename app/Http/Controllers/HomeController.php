<?php

namespace App\Http\Controllers;

use App\Http\Requests\Dashboard\DataFilter;
use App\Models\Setting;
use App\Services\AgentLogService;
use App\Services\DailyStatisticService;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    private DailyStatisticService $dailyStatisticService;

    public function __construct()
    {
        $this->middleware('auth:web,employee');
        //todo make default today date for dashboard page
//        $previousRouteName = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
//        $start = request()->route()->getName() === 'home' && ($previousRouteName === 'employee.user_statistics' || $previousRouteName === 'home' || $previousRouteName === 'login') ?'' :'';
//        $end = '';
        $this->dailyStatisticService = new DailyStatisticService(
            request()->date_range === 'true',
            request()->start,
            request()->end,
            request()->start_date,
            request()->compare_date
        );
    }

    public function index(DataFilter $request)
    {
        $settings = Setting::pluck('value', 'slug')->toArray();
        $dailyStats = $this->dailyStatisticService->getDailyStats($settings);
        $userStats  = $this->dailyStatisticService->getUserStats($settings);

        return $this->getPage($request, $dailyStats, $userStats);
    }

    public function getPage($request, $dailyStats, $userStats)
    {
        if (auth()->guard('web')->check()) {
            return $request->expectsJson()
//                Reports.Pdf.dashboard dashboard
                ? response()->json(view('async.dashboard', [
                    'dailyStats' => $dailyStats,
                    'userStats' => $userStats,
                    'maxData' => Cache::get('max_data'),
                ])->render())
                : view('dashboard', [
                    'dailyStats' => $dailyStats,
                    'userStats' => $userStats,
                    'maxData' => Cache::get('max_data'),
//                    'assets' => [
//                        'css' => [
//                            'dashboard' => file_get_contents(public_path() . '/assets/css/reports/style.css'),
//                        ]
//                    ]
                ]);
        } else {
            (new AgentLogService())->store(auth()->user()->servit_id, $request->ip());
            return view('pages.admin.fullScreen', [
                'dailyStats' => $dailyStats,
                'userStats' => $userStats,
            ]);
        }

    }

}

