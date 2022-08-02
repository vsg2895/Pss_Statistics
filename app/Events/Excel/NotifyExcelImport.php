<?php

namespace App\Events\Excel;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NotifyExcelImport implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private $message;
    private $start;
    private $end;
    private $company;
    private $checkRedirect;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($message, $start, $end, $company, $checkRedirect)
    {
        $this->message = $message;
        $this->start = $start;
        $this->end = $end;
        $this->company = $company;
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
        return ['excelImport-channel'];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'excel-import-event';
    }

    public function broadcastWith()
    {
        $companyId = !is_null($this->company) ? $this->company->id : $this->company;
        return ['message' => $this->message, 'company' => $companyId, 'start' => $this->start,
            'end' => $this->end, 'checkRedirect' => $this->checkRedirect];
    }
}
