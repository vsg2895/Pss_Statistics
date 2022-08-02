<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\ExcelInterface;
use App\Contracts\PdfInterface;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

class ReportController extends Controller
{
    private PdfInterface $pdf;
    private ExcelInterface $excel;

    public function __construct(PdfInterface $pdf, ExcelInterface $excel)
    {
        $this->pdf = $pdf;
        $this->excel = $excel;
    }

    public function index()
    {
        $lastMonth = Date("m-Y", strtotime("first day of previous month"));
        $dailyReports = Storage::allFiles($this->pdf->getPath() . '/' . date('m-Y'));
        $agentReports = Storage::allFiles($this->pdf->getPath('agent') . '/' . date('m-Y'));
        $monthlyReports = Storage::allFiles($this->pdf->getPath('monthly') . '/' . $lastMonth);
        $excelReports = Storage::allFiles($this->excel->getPath('company'));
        $excelCompareReports = Storage::allFiles($this->excel->getPath('compare'));

        return view('Reports.Pdf.index', [
            'dailyReports' => $dailyReports,
            'monthlyReports' => $monthlyReports,
            'agentReports' => $agentReports,
            'excelReports' => $excelReports,
            'excelCompareReports' => $excelCompareReports,
        ]);
    }

    public function delete(Request $request)
    {
//        dd($request->path);
        return Storage::delete($request->path)
            ? Redirect::to(URL::previous() . $request->anchor)->withSuccess(__('Report deleted successfully'))
            : Redirect::to(URL::previous() . $request->anchor)->withError(__('Something went wrong.'));

    }

    public function exportPdf()
    {
        $this->pdf->setParams(
            request()->date_range === 'true',
            request()->start,
            request()->end,
            request()->start_date,
            request()->compare_date
        );

        $this->pdf->setData();
        $this->pdf->downloadPdf();
    }

    public function exportPdfPlaning()
    {
        $this->pdf->setParams(
            request()->date_range === 'true',
            request()->start,
            request()->end,
            request()->start_date,
            request()->compare_date
        );

        $this->pdf->setData('planing');
        $this->pdf->downloadPdf();
    }

    public function download(Request $request)
    {
        if (strpos($request->path, 'storage/') === 0) {
            $request->path = str_replace('storage/', '', $request->path);
        }
        return Storage::download($request->path);
    }

}
