<?php

namespace App\Console\Commands;

use App\Models\ImportedUser;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
            $servitUsers = $this->getServitUsers();
            $liveagentUsers = $this->getLiveagentUsers();

            $result = $this->importUsers($servitUsers, $liveagentUsers);
            $message = "Users imported successfully. Duplicated: ".$result['duplicated'].", New: ".$result['new']."";
            $this->info($message);
            Log::info($message);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            Log::error('import:users failed, message: ' . $exception->getMessage());
        }
    }

    private function getServitUsers(): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
            'Cookie' => 'PHPSESSID=4jhtrml2nfu4qnrp2phelktnem'
        ])->get('https://gcm.servit.se/RestAPI/V1/users');

        $servitApiUsers = json_decode($response->body(), true);

        $servitUsers = [];

        foreach ($servitApiUsers as $user) {
            $email = trim(strtolower($user['usermail']));
            $validEmail = $email && strpos($email, '@personligtsvar.se');
            if ($validEmail) {
                $servitUsers[] = [
                    'servit_id' => $user['userid'],
                    'servit_username' => $user['username'],
                    'email' => $email,
                ];
            }
        }

        return $servitUsers;
    }

    private function getLiveagentUsers(): array
    {
        $response = Http::withHeaders([
            'apikey' => config('apiKeys.liveagent_api_key'),
        ])->get('https://psservice.liveagent.se/api/v3/agents', [
            '_perPage' => 1000
        ]);

        $liveagentApiUsers = json_decode($response->body(), true);
        $liveagentUsers = [];

        foreach ($liveagentApiUsers as $user) {
            $email = trim(strtolower($user['email']));
            $validEmail = $email && strpos($email, '@personligtsvar.se');
            if ($validEmail) {
                $liveagentUsers[] = [
                    'liveagent_id' => $user['id'],
                    'liveagent_username' => $user['name'],
                    'email' => $email
                ];
            }
        }

        return $liveagentUsers;
    }

    private function importUsers($servitUsers, $liveagentUsers): array
    {
        $merged = array_merge($servitUsers, $liveagentUsers);
        $withUniqueEmails = collect($merged)->groupBy('email');

        $withUniqueEmails = $withUniqueEmails->map(function ($items, $key){
            if (count($items) === 2) {
                return array_merge($items[0], $items[1]);
            } if (count($items) === 3) {//duplicated emails from servit
                return array_merge($items[1], $items[2]);
            } else {
                return $items[0];
            }
        });
        $data = $withUniqueEmails->values()->all();

        $importedUserEmails = ImportedUser::all()->pluck('email')->toArray();

        $duplicated = 0;
        $new = 0;

        foreach ($data as $user) {
            if (!in_array($user['email'], $importedUserEmails)) {
                $new++;
                ImportedUser::create($user);
            } else {
                $duplicated++;
                ImportedUser::where('email', $user['email'])->update($user);
            }
        }

        return ['duplicated' => $duplicated, 'new' => $new];
    }
}
