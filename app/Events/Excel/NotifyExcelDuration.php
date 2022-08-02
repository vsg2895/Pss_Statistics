<?php

namespace App\Events\Excel;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotifyExcelDuration implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $message;
    public $checkRedirect;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $checkRedirect)
    {
        $this->message = $message;
        $this->checkRedirect = $checkRedirect;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        sleep(1);
        return ['excelExport-channel'];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'excel-export-event';
    }
}
