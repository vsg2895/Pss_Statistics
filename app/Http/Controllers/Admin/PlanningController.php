<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Pages\EmployeeStatistic;
use App\Services\PlanningService;

class PlanningController extends Controller
{
    private PlanningService $planningService;//todo for bookings and chats we have departmentId and name, try to use it to find company

    public function __construct(PlanningService $planningService)
    {
        $this->planningService = $planningService;
    }

    public function index(EmployeeStatistic $request)
    {
        $hourlyData = $this->planningService->getHourlyData();

        return view('pages.admin.planning', ['data' => $hourlyData]);
    }
}
