<?php

namespace App\Mail\Reports;

use App\Contracts\PdfInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;

class SendMontlyReport extends Mailable
{
    use Queueable, SerializesModels;

    private PdfInterface $pdf;
    private $type;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PdfInterface $pdf, $type = null)
    {
        $this->pdf = $pdf;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $mailText = !is_null($this->type) ? 'Here is monthly report from - ' . ucfirst($this->type) : 'Here is monthly report.';
        $curYear = \Carbon\Carbon::now()->format('Y');
        $prevMonth = new Carbon('first day of last month');
        $prevMonthEnd = new Carbon('last day of last month');
        $prevMonthStartWithYear = $prevMonth->format('Y-m-d');
        $prevMonthEndWithYear = $prevMonthEnd->format('Y-m-d');
        $prevMonthName = Date("F", strtotime("first day of previous month"));
        $pathType = ucfirst($this->type);
        $fileName = !is_null($this->type) ? "Monthly $pathType Report $prevMonthName.pdf"
            : "Monthly Report $prevMonthName.pdf";
        $name = !is_null($this->type) ? "Monthly Planning Report $prevMonth - $prevMonthEnd" : "Monthly Report $prevMonthName $curYear.pdf";
        $filePath = !is_null($this->type) ? $this->pdf->getPath('monthly') . '/' . $pathType . '/' . $prevMonth->format('m-Y') . '/' . $fileName
            : $this->pdf->getPath('monthly') . '/' . $prevMonth->format('m-Y') . '/' . $fileName;
        return $this->view('mail.reports.reports', compact('mailText'))
            ->subject('Monthly Report')
            ->attachFromStorage($filePath, $name, [
                'mime' => 'application/pdf'
            ]);
    }
}
