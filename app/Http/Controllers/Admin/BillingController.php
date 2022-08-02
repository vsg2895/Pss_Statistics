<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\CdrInterface;
use App\Contracts\ExcelInterface;
use App\Exports\BillingDataWithFeesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\DateRange;
use App\Http\Requests\Excel\ImportRequest;
use App\Imports\BillingDataWithFeesImport;
use App\Models\Company;
use App\Models\FeeType;
use App\Services\Update\UpdateDateFeesService;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Facades\Excel;

class BillingController extends Controller
{
    private CdrInterface $cdr;
    private ExcelInterface $excel;
    private UpdateDateFeesService $updateDateFeesService;

    public function __construct(CdrInterface $cdr, ExcelInterface $excel, UpdateDateFeesService $updateDateFeesService)
    {
        $this->cdr = $cdr;
        $this->excel = $excel;
        $this->updateDateFeesService = $updateDateFeesService;
    }

    public function index(DateRange $request): \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Contracts\Foundation\Application
    {
        $monthStart = $request->start ?? Carbon::now()->startOfMonth()->format('Y-m-d');
        $today = $request->end ?? Carbon::now()->format('Y-m-d');
        $this->cdr->setParams([], 'all', $monthStart, $today);
        $cdrStatistics = $this->cdr->setBillingFee();

        $fixed = $this->cdr->getFixedForAll();
//        dd('sss');
        return view('pages.admin.billing.index', compact('cdrStatistics', 'fixed'));
    }

    public function exportExcel(Company $company, Request $request)
    {
        $this->excel->setParams($company, 'company', $request->start, $request->end);
        $filePath = $this->excel->ExportData();

        return redirect()->back()->withSuccess('Excel Prepare To Export');
    }

    public function importExcel(Company $company, ImportRequest $request)
    {
        try {
            $this->excel->ImportData($company, $request->file('import'));
            return redirect()->back()->withSuccess(__('Excel import started. You will be notified about result.'));
        } catch (ValidationException $e) {
            return redirect()->back()->withSuccess($e->getMessage());
        }

    }

}
