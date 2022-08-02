<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeletedTeleTwoUsers extends Mailable
{
    use Queueable, SerializesModels;

    public array $TtuIds;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($TtuIds)
    {
        $this->TtuIds = $TtuIds;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $TtuIds = $this->TtuIds;
        return $this->view('mail.teletwo.deletedUsers', compact('TtuIds'))
            ->subject('Deleted Tele Two Users');
    }
}
