<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\DailyChat;
use App\Models\Department;
use App\Models\Fee;
use App\Models\FeeType;
use App\Models\ImportedUser;
use App\Models\ServiceProviderSettings;
use App\Models\Setting;
use App\Services\Insert\ChatPricesService;
use App\Services\Insert\SetBillingPricesService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportDailyChats extends Command
{

    public ChatPricesService $chatPricesService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:chats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Liveagent api use header to get data for your preferred timezone
     *
     * @var string
     */
    private string $timeZoneOffset = '+28800';//60*60*8

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(ChatPricesService $chatPricesService)
    {
        $this->chatPricesService = $chatPricesService;
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
            $companies = Company::all();
            $defaultsWe = Setting::getDefaultsBillingRow(['chats' => 'chats_fee']);
            $defaultsProviderCompany = ServiceProviderSettings::getProviderToCompanyDefaults(['chats' => 'chats_fee']);
            $feeProviderCompany = Fee::getProviderCompanyCustomAll();
            $feeWeProvide = Fee::getProviderCustomAll();
            $feeWeCompany = Fee::getCompanyCustomAll();
            $response = Http::withHeaders([
                'apikey' => config('apiKeys.liveagent_api_key'),
                'Timezone-Offset' => $this->timeZoneOffset,

//    ])->get('https://psservice.liveagent.se/api/v3/chats?_filters=[["date_created","DP","T"]]');
            ])->get('https://psservice.liveagent.se/api/v3/tickets?_page=1&_perPage=1000&_filters=[["date_resolved","DP","T"]]');

            $dailyChats = json_decode($response->body(), true) ?: [];
            $userIds = ImportedUser::liveagentUsers()->pluck('liveagent_id', 'id')->toArray();
            $departmetsWithCompany = Department::with('company')
                ->get()->pluck('company.company_id', 'department_id')->toArray();
            $tag = '1zlj';
            $data = [];
            $count = 0;

            foreach ($dailyChats as $chat) {
                if (in_array($tag, $chat['tags'])) {
                    $currentDeparmentCompany = $departmetsWithCompany[$chat['departmentid']] ?? null;
                    $departmetPrices = $this->chatPricesService->getPricesFromDepartment($currentDeparmentCompany, $companies, $defaultsWe, $defaultsProviderCompany,
                        $feeWeCompany, $feeWeProvide, $feeProviderCompany);
                    $count++;
                    $date = Carbon::parse($chat['date_created'])->format('Y-m-d');
                    if (array_key_exists('agentid', $chat) && in_array($chat['agentid'], $userIds)) {
                        $data[] = [
                            'chat_id' => $chat['id'],
                            'user_id' => array_search($chat['agentid'], $userIds),
                            'department_id' => $chat['departmentid'],
                            'price' => $departmetPrices['we'],
                            'provider_price' => $departmetPrices['provider_company'],
                            'date' => $date,
                            'date_created' => $chat['date_created'],
                        ];
                    } else {
                        $data[] = [
                            'chat_id' => $chat['id'],
                            'user_id' => 0,
                            'department_id' => $chat['departmentid'],
                            'price' => $departmetPrices['we'],
                            'provider_price' => $departmetPrices['provider_company'],
                            'date' => $date,
                            'date_created' => $chat['date_created'],
                        ];
                    }
                }

            }
//            dd('dddd');
            DailyChat::upsert($data, ['chat_id'], ['price', 'provider_price']);

            $this->info('Chats imported successfully. Count: ' . $count);
            Log::info('Chats imported successfully. Count: ' . $count);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            Log::error('import:chats failed, message: ' . $exception->getMessage() . "Line :" . $exception->getLine());
        }
    }
}
