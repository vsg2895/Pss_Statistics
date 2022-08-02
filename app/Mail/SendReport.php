<?php

namespace App\Mail;

use App\Contracts\PdfInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendReport extends Mailable
{
    use Queueable, SerializesModels;

    private PdfInterface $pdf;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(PdfInterface $pdf)
    {
        $this->pdf = $pdf;
    }

    public function build()
    {
        $mailText = 'Here is daily report.';
        return $this->view('mail.reports.reports', compact('mailText'))
            ->subject('Daily Report')
            ->attachFromStorage($this->pdf->getPath() . '/'
                . date("m-Y") . '/Daily Report ' . date("d-m-Y") . '.pdf', 'Daily Report ' . date("d-m-Y"), [
                'mime' => 'application/pdf'
            ]);
    }
}
