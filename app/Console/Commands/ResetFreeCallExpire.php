<?php

namespace App\Console\Commands;

use App\Models\Company;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ResetFreeCallExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:free-call';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset Company Expire Field Every Month Start';

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
        try {
            Company::where('notified_free_call', true)->update(['notified_free_call' => false]);
            $this->info('Companies free-call expire number successfully reset');
            Log::info('Companies free-call expire number successfully reset');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            Log::error($exception->getMessage());
        }

    }
}
