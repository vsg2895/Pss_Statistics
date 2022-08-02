<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendFreeCallExpireMail extends Mailable
{
    use Queueable, SerializesModels;

    private $text;
    private $company;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($text, $companyName)
    {
        $this->text = $text;
        $this->company = $companyName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): static
    {
        $text = $this->text;
        $company = $this->company;
        return $this->view('mail.free-call-expire', compact('text', 'company'))
            ->subject('Expire Free Call Count');
    }
}
