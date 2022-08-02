<?php

namespace App\Exports;

use App\Services\CompanyService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class GlobalCompareDataExport implements WithMultipleSheets, ShouldQueue
{
    public $compareExportData;
    public CompanyService $companyService;
    public string $start;
    public string $end;
    public string $s_start;
    public string $s_end;
    public $calls_count;
    public string $viewName;


    public function __construct($companyService, $start, $end, $s_start, $s_end, $calls_count, $viewName = 'components.compare.excelImport')
    {
        $this->companyService = $companyService;
        $this->calls_count = $calls_count;
        $this->compareExportData = $this->companyService->getCompaniesComparingData($start, $end, $s_start, $s_end, $calls_count);
        $this->start = $start;
        $this->end = $end;
        $this->s_start = $s_start;
        $this->s_end = $s_end;
        $this->viewName = $viewName;
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        foreach ($this->compareExportData as $key => $dataExport) {
            $sheets[] = new CompareDataExport($this->compareExportData, $dataExport, $this->start, $this->end, $this->s_start, $this->s_end, $key);
        }

        return $sheets;
    }
}
