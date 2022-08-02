<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\ExcelInterface;
use App\Exports\GlobalCompareDataExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Company\CompareCompaniesData;
use App\Services\CompanyService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CompareCompaniesController extends Controller
{
    private CompanyService $companyService;
    private ExcelInterface $excel;

    public function __construct(CompanyService $companyService, ExcelInterface $excel)
    {
        $this->companyService = $companyService;
        $this->excel = $excel;
    }

    public function compareByDateRange(CompareCompaniesData $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $compareData = $this->companyService->getCompaniesComparingData($request->start, $request->end, $request->s_start, $request->s_end, $request->calls_count);

        return view('pages.admin.companies.compare', [
            'compareData' => $compareData,
        ]);
    }

    public function exportExcel(CompareCompaniesData $request): \Illuminate\Http\RedirectResponse
    {
        $reportPath = $this->excel->getPath('compare') . $request->s_start . "-" . $request->s_end . " - " . $request->start . "-" . $request->end . '.xlsx';
        Excel::store(new GlobalCompareDataExport($this->companyService, $request->start, $request->end, $request->s_start, $request->s_end, $request->calls_count),
            $reportPath);

        return redirect()->back();
    }
}
