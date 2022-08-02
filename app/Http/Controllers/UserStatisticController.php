<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Services\EmployeeStatisticService;
use Illuminate\Http\Request;

class UserStatisticController extends Controller
{
    private $dailyStatisticService;

    public function __construct()
    {
        $this->middleware('auth:employee');
        $this->dailyStatisticService = new EmployeeStatisticService(request()->start_date, request()->end_date);
    }

    public function index(Request $request)
    {
        $settings = Setting::pluck('value', 'slug')->toArray();
        $user = auth()->user();
        $dailyStats = $this->dailyStatisticService->getDailyUserStats($settings, $user);

        return view('pages.userStatistics', [
                'dailyStats' => $dailyStats,
                'servitUser' => $user
            ]);
    }
}
