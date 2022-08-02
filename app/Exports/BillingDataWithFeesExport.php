<?php

namespace App\Exports;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromView;


class BillingDataWithFeesExport implements FromView, ShouldQueue
{
    use Exportable;

    public $companyExportData;
    public $company;
    public string $start;
    public string $end;
    public $companyChatFee;
    public $companyProviderChatFee;
    public array $checkMonths;
    public string $viewName;


    public function __construct($companyExportData, $company, $start, $end, $companyChatFee, $companyProviderChatFee, $checkMonths, $viewName = 'components.billing.excels.billing-report-company')
    {
        $this->companyExportData = $companyExportData;
        $this->company = $company;
        $this->start = $start;
        $this->end = $end;
        $this->companyChatFee = $companyChatFee;
        $this->companyProviderChatFee = $companyProviderChatFee;
        $this->checkMonths = $checkMonths;
        $this->viewName = $viewName;

    }

    public function view(): View
    {
        return view($this->viewName, [
            'billingByCompany' => $this->companyExportData,
            'startExcel' => $this->start,
            'endExcel' => $this->end,
            'company' => $this->company,
            'chatFee' => $this->companyChatFee,
            'p_chatFee' => $this->companyProviderChatFee,
            'checkMonths' => $this->checkMonths,
        ]);
    }

}
