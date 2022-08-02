<?php

namespace App\Console\Commands\Reports;

use App\Contracts\PdfInterface;
use App\Services\EmployeeStatisticService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class GeneratePdfReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pdf:report {--monthly} {--type=}';
    /**
     * -- monthly flag generate monthly report based on dashboard
     */

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Reports {--monthly?} {--type?}';

    private PdfInterface $pdf;
    private $type;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(PdfInterface $pdf)
    {
        parent::__construct();
        $this->pdf = $pdf;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $monthlyArgument = $this->option('monthly');
        $this->type = $this->option('type');
        if ($monthlyArgument) {
//            dd($this->type);
            $this->generateMonthly($this->type);
            $this->info('pdf:reports-monthly | Monthly report saved successfully');
        } else {
            $this->generateDaily();
            $this->generateAgentsDaily();
            $this->info('pdf:reports | Daily and agent reports saved successfully');
        }

    }

    private function generateMonthly($type = null)
    {
        try {
            $start = new Carbon('first day of last month');
            $end = new Carbon('last day of last month');
            $this->pdf->setParams(true, $start, $end);
            $this->pdf->setData('monthly', $type);
            $this->pdf->savePdf();
            Log::info('pdf:reports-monthly | Monthly report saved successfully');
        } catch (\Exception $exception) {
            Log::error('pdf:reports-monthly | Monthly report failed. Message: ' . $exception->getMessage() . '  Line: ' . $exception->getLine());
            Log::error($exception->getMessage() . '  Line: ' . $exception->getLine());
        }
    }

    private function generateDaily()
    {
        try {
            $this->pdf->setParams();
            $this->pdf->setData();
            $this->pdf->savePdf();
            Log::info('pdf:reports | Daily report saved successfully');
        } catch (\Exception $exception) {
            Log::error('pdf:reports | Daily report failed. Message: ' . $exception->getMessage() . '  Line: ' . $exception->getLine());
            Log::error($exception->getMessage() . '  Line: ' . $exception->getLine());
        }
    }

    private function generateAgentsDaily()
    {
        try {
            $this->pdf->setParams();
            $this->pdf->setData('agent');
            $this->pdf->savePdf();
            Log::info('pdf:reports | Agents daily report saved successfully');
        } catch (\Exception $exception) {
            Log::error('pdf:reports | Agents daily report failed. Message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine());
            $this->error($exception->getMessage() . ' Line: ' . $exception->getLine());
        }

    }
}
