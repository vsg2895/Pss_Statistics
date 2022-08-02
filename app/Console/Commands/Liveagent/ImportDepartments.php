<?php

namespace App\Console\Commands\Liveagent;

use App\Contracts\LiveagentApiInterface;
use App\Models\Department;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ImportDepartments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:departments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import departments from liveagent';

    private LiveagentApiInterface $liveagentApi;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(LiveagentApiInterface $liveagentApi)
    {
        parent::__construct();
        $this->liveagentApi = $liveagentApi;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            $response = $this->liveagentApi->getData('departments', '?_page=1&_perPage=1000');

            $departments = json_decode($response->body(), true) ?: [];

            if ($departments) {
                $departmentIds = Department::withTrashed()->pluck('department_id')->toArray();

                $data = [];
                $count = 0;
                foreach ($departments as $department) {
                    if (!in_array($department['department_id'], $departmentIds)) {
                        $data[] = [
                            'department_id' => $department['department_id'],
                            'name' => $department['name'] ?? 'unknown',
                            'online_status' => $department['online_status'] ?? 'unknown',
                            'agent_ids' => json_encode($department['agent_ids']),
                            'mailaccount_id' => $department['mailaccount_id'] ?? 'unknown',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $count++;
                    }
                }

                Department::insert($data);

                $message = 'Departments imported successfully. New departments count: ' . $count;
            } else {
                $message = 'No Departments to import';
            }
            $this->info($message);
            Log::info($message);
        } catch (\Exception $exception) {
            $message = 'import:departments failed, Message: ' . $exception->getMessage() . ' Line: ' .$exception->getLine();
            $this->error($message);
            Log::error($message);
        }
    }
}
