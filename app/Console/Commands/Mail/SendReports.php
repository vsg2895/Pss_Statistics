<?php

namespace App\Console\Commands\Mail;

use App\Contracts\PdfInterface;
use App\Mail\Reports\SendMontlyReport;
use App\Mail\SendReport;
use App\Services\Reports\PdfService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Exception;

class SendReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:reports {--monthly} {--type=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily reports {--monthly?} {--type=?}';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $emails = config('reports.reports_receivers_emails');
        $monthlyArgument = $this->option('monthly');
        $monthlyType = $this->option('type');

        try {
            foreach ($emails as $email) {
                !$monthlyArgument ? Mail::to($email)->send(new SendReport(new PdfService())) : (($monthlyType == 'planning') ? Mail::to($email)->send(new SendMontlyReport(new PdfService(), $monthlyType))
                    : Mail::to($email)->send(new SendMontlyReport(new PdfService())));
            }
            $this->info('mail:reports worked successfully.');
            Log::info('mail:reports worked successfully.');
        } catch (\Exception $exception) {
            $this->error('mail:reports failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine());
            Log::error('mail:reports failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine());
        }
    }
}
