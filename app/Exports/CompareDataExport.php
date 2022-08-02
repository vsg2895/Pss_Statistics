<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;

class CompareDataExport implements FromView, ShouldAutoSize, WithEvents, WithTitle, ShouldQueue
{
    public $compareExportDataAll;
    public $compareExportData;
    public string $start;
    public string $end;
    public string $s_start;
    public string $s_end;
    public string $key;
    public string $viewName;


    public function __construct($compareExportDataAll, $compareExportData, $start, $end, $s_start, $s_end, $key, $viewName = 'components.compare.excelImport')
    {
        $this->compareExportDataAll = $compareExportDataAll;
        $this->compareExportData = $compareExportData;
        $this->start = $start;
        $this->end = $end;
        $this->s_start = $s_start;
        $this->s_end = $s_end;
        $this->key = $key;
        $this->viewName = $viewName;

    }
    // ...

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $cellRange = 'A1:W1'; // All headers
                $cellRangeName = 'B'; // All companies names
                $event->sheet->getDelegate()->getStyle($cellRange)->getFont()->setSize(14);
                $event->sheet->getDelegate()->getStyle($cellRangeName)->getFont()->setSize(12);
            },
        ];
    }

    public function title(): string
    {
        return $this->key;
    }

    public function view(): View
    {
        return view($this->viewName, [
            'compareData' => $this->compareExportDataAll,
            'data' => $this->compareExportData,
            'compare_start' => $this->start,
            'compare_end' => $this->end,
            'compare_s_start' => $this->s_start,
            'compare_s_end' => $this->s_end,
            'currentKey' => $this->key,
        ]);
    }
}
