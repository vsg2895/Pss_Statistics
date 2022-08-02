<?php

namespace App\Jobs\Excel;

use App\Exports\BillingDataWithFeesExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ExportExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $dataCount;
    private $companyData;
    private $company;
    private $start;
    private $end;
    private $reportPath;
    private $companyChatFee;
    private $companyProviderChatFee;
    private $checkMonths;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($dataCount, $companyData, $company, $start, $end, $reportPath, $companyChatFee, $companyProviderChatFee,$checkMonths)
    {
        $this->dataCount = $dataCount;
        $this->companyData = $companyData;
        $this->company = $company;
        $this->start = $start;
        $this->end = $end;
        $this->reportPath = $reportPath;
        $this->companyChatFee = $companyChatFee;
        $this->companyProviderChatFee = $companyProviderChatFee;
        $this->checkMonths = $checkMonths;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Excel::store(new BillingDataWithFeesExport($this->companyData, $this->company, $this->start, $this->end, $this->companyChatFee, $this->companyProviderChatFee,$this->checkMonths),
            $this->reportPath);

    }
}
