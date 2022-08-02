<?php

namespace App\Services\Reports;

use App\Contracts\PdfInterface;
use App\Models\DailyStatistic;
use App\Models\FeeType;
use App\Models\ImportedUser;
use App\Models\Setting;
use App\Services\DailyStatisticService;
use App\Services\EmployeeStatisticService;
use App\Services\PlanningService;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PdfService implements PdfInterface
{
    private $start;
    private $end;
    private $start_date;
    private $compare_date;
    private $inDateRange;
    private $pdf;
    private $path;
    private $name;


    public function getPdf(): Dompdf
    {

        return new Dompdf(['enable_remote' => true]);
    }

    public function setParams($inDateRange = false, $start = null, $end = null, $startDate = null, $compareDate = null)
    {
        $this->start = $start;
        $this->end = $end;
        $this->start_date = $startDate;
        $this->compare_date = $compareDate;
        $this->inDateRange = $inDateRange;

    }

    public function setData($type = 'daily', $typeMonthly = null)
    {

        $date = $type == 'monthly' ? Date("m-Y", strtotime("first day of previous month"))
            : ($this->start_date ?: date("d-m-Y"));
        $curYear = Carbon::now()->format('Y');

        switch ($type) {
            case 'agent':
                $this->setDailyAgentData();//todo set best and low, set colors
                $this->path = 'reports/' . config('filesystems.paths.agent_reports') . '/'
                    . date("m-Y") . '/Agents Report ' . $date . '.pdf';
                $this->name = "Agents Report $date";
                break;
            case 'planing':
                $this->setPlaningData();//todo set best and low, set colors
                $this->path = 'reports/' . config('filesystems.paths.planing_reports') . '/'
                    . date("m-Y") . '/Planing Report ' . $date . '.pdf';
                $this->name = "Planing Report $date";
                break;
            case 'monthly':
                $lastMonthName = Date("F", strtotime("first day of previous month"));
                $lastMonthFirstDay = Date("Y-m-d", strtotime("first day of previous month"));
                $lastMonthEndDay = Date("Y-m-d", strtotime("last day of previous month"));
                if ($typeMonthly == 'planning') {
                    $this->start_date = $lastMonthFirstDay;
                }
                !is_null($typeMonthly) ? $this->setPlaningData($lastMonthFirstDay, $lastMonthEndDay) : $this->setMonthlyData();
                $pathType = ucfirst($typeMonthly);
                $this->path = $typeMonthly == 'planning' ? 'reports/' . config('filesystems.paths.monthly_reports') . '/'
                    . $curYear . '/Planning/' . $date . "/Monthly $pathType Report " . $lastMonthName . '.pdf'
                    : 'reports/' . config('filesystems.paths.monthly_reports') . '/'
                    . $curYear . '/' . $date . '/Monthly Report ' . $lastMonthName . '.pdf';
//                dd($date,$curYear);
                $this->name = !is_null($typeMonthly) ? "Monthly-Report $pathType $date" . " - " . "$curYear" : "Monthly-Report $date" . " - " . "$curYear";
                break;
            default:
                $this->setDailyData();
                $this->path = 'reports/' . config('filesystems.paths.daily_reports') . '/'
                    . date("m-Y") . '/Daily Report ' . $date . '.pdf';
                $this->name = "Daily-Report $date";
        }
    }

    public function savePdf()
    {
        Storage::put($this->path, $this->pdf->output());
    }

    public function downloadPdf()
    {
        $this->pdf->stream($this->name);
    }

    public function getPath($type = 'daily'): string
    {
        $curYear = Carbon::now()->format('Y');
        switch ($type) {
            case 'agent':
                $path = 'reports/' . config('filesystems.paths.agent_reports');
                break;
            case 'planing':
                $path = 'reports/' . config('filesystems.paths.planing_reports');
                break;
            case 'monthly':
                $path = 'reports/' . config('filesystems.paths.monthly_reports') . '/' . $curYear;
                break;
            default:
                $path = 'reports/' . config('filesystems.paths.daily_reports');
        }

        return $path;
    }

//    public function walkDependKeyType(&$value, $key, $count)
//    {
//
//        return $value;
//    }

    private function setPlaningData($start = null, $end = null)
    {
        $planingService = new PlanningService();
        $data = $planingService->getHourlyData($start, $end);
//        dd($data);
        $this->pdf = $this->getPdf();
        $dataTotal = array_fill_keys(array_keys(Arr::collapse($data)), 0);
        foreach ($data as $elem) {
            foreach ($elem as $key => $el) {
                if (!is_array($el)) {
                    $dataTotal[$key] += (float)$el;
                } else {
                    $dataTotal[$key] = "NaN";
                }
            }
        }
        array_walk($dataTotal, function (&$value, $key, $count) {
            if (in_array($key, array_keys(FeeType::PLANNING_AVERAGE_DECIMAL_FIELDS))) {
                if ($key == 'agentsCount') {
                    $value = $value !== 0.0 && $value !== 0 && $count !== 0 ? ($value * 10 / $count) / 10 : $value;
                } else {
//                    dd($value, $count, 'else');
                    $value = $value !== 0.0 && $value !== 0 && $count !== 0 ? round($value * 10 / $count) / 10 : $value;
                }
            } elseif (in_array($key, array_keys(FeeType::PLANNING_AVERAGE_FIELDS))) {
//                dd($value !== 0.0);
                $value = $value !== 0.0 && $value !== 0 && $count !== 0 ? round($value / $count) : $value;
            }
        }, collect($data)->where($key, '!=', 0.0)->count());
        $dataTotal['difference_percentage'] = $dataTotal['agentsCount'] - $dataTotal['teoryEmpCount'];
        $assets = [
            'css' => [
                'dashboard' => file_get_contents(public_path() . '/assets/css/reports/style.css'),
            ]
        ];
        $this->pdf->loadHtml(view('Reports.Pdf.planing', [
            'dataTotal' => $dataTotal,
            'data' => $data,
            'assets' => $assets,
        ]));


        $this->pdf->setPaper('A4', 'landscape');
        $this->pdf->render();
    }

    private function setMonthlyData()
    {
        $dailyStatisticService = new DailyStatisticService(
            $this->inDateRange,
            $this->start,
            $this->end,
            $this->start_date,
            $this->compare_date
        );
        $this->pdf = $this->getPdf();
        $settings = Setting::pluck('value', 'slug')->toArray();
        $dailyStats = $dailyStatisticService->getDailyStats($settings);
        $userStats = $dailyStatisticService->getUserStats($settings);

        $assets = [
            'css' => [
                'dashboard' => file_get_contents(public_path() . '/assets/css/reports/style.css'),
            ]
        ];

        $this->pdf->loadHtml(view('Reports.Pdf.dashboard', [
            'dailyStats' => $dailyStats,
            'userStats' => $userStats,
            'assets' => $assets,
            'monthly' => true,
//            'img' => Str::remove('\public', public_path()) . '/logo2.png'
        ]));

        $this->pdf->setPaper('A4', 'landscape');
        $this->pdf->render();

    }

    private function setDailyData()
    {
        $dailyStatisticService = new DailyStatisticService(
            $this->inDateRange,
            $this->start,
            $this->end,
            $this->start_date,
            $this->compare_date
        );

        $this->pdf = $this->getPdf();
        $settings = Setting::pluck('value', 'slug')->toArray();
        $dailyStats = $dailyStatisticService->getDailyStats($settings);
        $userStats = $dailyStatisticService->getUserStats($settings);

        $assets = [
            'css' => [
                'dashboard' => file_get_contents(public_path() . '/assets/css/reports/style.css'),
            ]
        ];

        $this->pdf->loadHtml(view('Reports.Pdf.dashboard', [
            'dailyStats' => $dailyStats,
            'userStats' => $userStats,
            'assets' => $assets,
//            'img' => Str::remove('\public', public_path()) . '/logo2.png'
        ]));

        $this->pdf->setPaper('A4', 'landscape');
        $this->pdf->render();
    }

    private function setDailyAgentData()
    {
        $todayServitIds = DailyStatistic::today()->pluck('servit_user_id')->toArray();
        $this->pdf = $this->getPdf();
        $settings = Setting::pluck('value', 'slug')->toArray();
        $assets = [
            'css' => [
                'dashboard' => file_get_contents(public_path() . '/assets/css/reports/style.css'),
            ]
        ];

        $todayAgentsData = [];
        foreach ($todayServitIds as $servitId) {
            $servitUser = ImportedUser::where('servit_id', $servitId)->first();

            $employeeService = new EmployeeStatisticService();
            $dailyStats = $employeeService->getDailyUserStats($settings, $servitUser);
            $dailyStats['name'] = $servitUser->servit_username;

            $todayAgentsData[$servitId] = $dailyStats;
        }

        $this->pdf->loadHtml(view('Reports.Pdf.agents', [
            'todayAgentsData' => $todayAgentsData,
            'assets' => $assets,
        ]));

        $this->pdf->setPaper('A4', 'landscape');
        $this->pdf->render();
    }

}
