<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pages\EmployeeStatistic;
use App\Models\ImportedUser;
use App\Models\Setting;
use App\Services\EmployeeStatisticService;

class EmployeeStatisticController extends Controller
{
    private EmployeeStatisticService $statisticService;

    public function __construct()
    {
        $this->statisticService = new EmployeeStatisticService(request()->start_date, request()->end_date);
    }

    public function index(ImportedUser $servitUser, EmployeeStatistic $request)
    {
        //redirect with date filter from dashboard page if they exist
        $previousRouteName = app('router')->getRoutes()->match(app('request')->create(url()->previous()))->getName();
        $previousUrl = isset(parse_url(url()->previous())['query']) ? parse_url(url()->previous())['query'] : null;
        parse_str($previousUrl, $previousUrlParams);
        if ($previousRouteName === 'home' && is_null($request->redirect) && isset($previousUrlParams['start']) && isset($previousUrlParams['end']))
            return redirect("admin/employee-statistics/$servitUser->servit_id?start_date=".$previousUrlParams['start']."&end_date=".$previousUrlParams['end']."&redirect=1");

        $settings = Setting::pluck('value', 'slug')->toArray();
        $dailyStats = $this->statisticService->getDailyUserStats($settings, $servitUser);

        return view('pages.admin.employeeStatistics', [
            'dailyStats' => $dailyStats,
            'servitUser' => $servitUser
        ]);
    }
}
