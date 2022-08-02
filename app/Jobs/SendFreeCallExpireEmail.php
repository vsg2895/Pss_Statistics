<?php

namespace App\Jobs;

use App\Mail\DeletedTeleTwoUsers;
use App\Mail\SendFreeCallExpireMail;
use App\Models\Company;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendFreeCallExpireEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $emailTo;
    private $company;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emailTo, $company)
    {
        $this->emailTo = $emailTo;
        $this->company = $company;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $companyIds = [];
        if (is_array($this->company) && !empty($this->company)) {
            foreach ($this->company as $company) {
                Mail::to($this->emailTo)->send(new SendFreeCallExpireMail('Your Free Calls Are Expire', $company->name));
                $companyIds[] = $company->id;
            }
            Company::whereIn('id', $companyIds)->update(['notified_free_call' => true]);
        }

    }
}
