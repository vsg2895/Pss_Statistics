<?php

namespace App\Jobs\Excel;

use App\Contracts\ExcelInterface;
use App\Imports\BillingDataWithFeesImport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Facades\Excel;

class ImportExcel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private ExcelInterface $excel;
    private $company;
    private $file;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($excel, $company, $file)
    {
        $this->excel = $excel;
        $this->company = $company;
        $this->file = $file;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Excel::import(new BillingDataWithFeesImport($this->excel, $this->company), $this->file);
    }
}
