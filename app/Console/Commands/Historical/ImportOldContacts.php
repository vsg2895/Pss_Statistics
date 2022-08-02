<?php

namespace App\Console\Commands\Historical;

use App\Models\Contact;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportOldContacts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old:contacts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import all contacts';

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
            $lastContact = Contact::orderBy('id', 'desc')->first();
            $datetimeFrom = '20151126142642';
            if ($lastContact) {
                $datetimeFrom = $lastContact->added_at;
                $datetimeFrom = Carbon::parse($datetimeFrom)->format('YmdHis');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
            ])->get(config('apiKeys.servit_url') . "/contacts?addts:gt=$datetimeFrom");

            $contacts = json_decode($response->body(), true) ?: [];

            if (isset($contacts['cotactno'])) $contacts = [$contacts];
            $count = 0;
            $contactData = [];

            foreach ($contacts as $contact) {
                $count++;
                $contactId = intval($contact['contactno']);

                $contactData[] = [
                    'name' => $contact['name'],
                    'contact_id' => $contactId,
                    'company_id' => $contact['companyno'],
                    'added_at' => Carbon::parse($contact['addts'])->format('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];
            }

            Contact::insert($contactData);

            echo 'RESULT: Contacts imported, count: ' . $count . '========================= Start Date: ' . $datetimeFrom;

            $message = "Contacts imported successfully. Count: " . $count;
            $this->info($message);
            Log::info($message);

            if (count($contacts) >= 1000) $this->handle();
        } catch (\Exception $exception) {
            $this->error('import:contacts failed ' . $exception->getMessage() . ' Line: ' . $exception->getLine());
            Log::error('import:contacts failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine());
        }
    }
}
