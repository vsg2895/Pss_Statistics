<?php

namespace App\Console\Commands\OneTime;

use App\Contracts\LiveagentApiInterface;
use App\Models\Company;
use App\Models\DailyChat;
use App\Models\DailyChatMeta;
use App\Models\Department;
use App\Models\Fee;
use App\Models\ServiceProviderSettings;
use App\Models\Setting;
use App\Services\Insert\ChatPricesService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateChats extends Command
{
    public ChatPricesService $chatPricesService;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:chats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update chats(add department id for historical chats)';

    private LiveagentApiInterface $liveagentApi;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(LiveagentApiInterface $liveagentApi, ChatPricesService $chatPricesService)
    {
        parent::__construct();
        $this->liveagentApi = $liveagentApi;
        $this->chatPricesService = $chatPricesService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $tag = '1zlj';
        $tag1 = 'cb75';
        try {
            $companies = Company::all();
            $defaultsWe = Setting::getDefaultsBillingRow(['chats' => 'chats_fee']);
            $defaultsProviderCompany = ServiceProviderSettings::getProviderToCompanyDefaults(['chats' => 'chats_fee']);
            $feeProviderCompany = Fee::getProviderCompanyCustomAll();
            $feeWeProvide = Fee::getProviderCustomAll();
            $feeWeCompany = Fee::getCompanyCustomAll();

            foreach (range(170, 202) as $page) {
                echo "------------------------$page----------------------------";
                $response = $this->liveagentApi->getData('tickets', '?_page=' . $page . '&_perPage=1000');

                $dailyChats = json_decode($response->body(), true) ?: [];
                $departmetsWithCompany = Department::with('company')
                    ->get()->pluck('company.company_id', 'department_id')->toArray();
//                dump(array_key_exists('02c54b43', $departmetsWithCompany));
//                dd($departmetsWithCompany);
                $count = 0;
                $metaCount = 0;
                $chatMeta = [];

                if (count($dailyChats)) {
                    foreach ($dailyChats as $chat) {

                        $chatId = $chat['id'];
                        $companyId = $departmetsWithCompany[$chat['departmentid']] ?? null;
                        $departmetPrices = $this->chatPricesService->getPricesFromDepartment($companyId, $companies, $defaultsWe, $defaultsProviderCompany,
                            $feeWeCompany, $feeWeProvide, $feeProviderCompany);
                        DailyChat::where('chat_id', $chatId)
                            ->update([
                                'department_id' => $chat['departmentid'],
                                'price' => $departmetPrices['we'],
                                'provider_price' => $departmetPrices['provider_company'],
                            ]);

                        if (in_array($tag, $chat['tags']) || in_array($tag1, $chat['tags'])) {
                            $metaCount++;
                            $chat = $this->unsetKeys($chat);
                            $chatMeta[] = [
                                'chat_id' => $chatId,
                                'meta_data' => json_encode($chat),
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ];
                        }

                        $count++;
                        echo $chatId . '--';
                    }
//                    dd('dsdsds');
                    DailyChatMeta::upsert($chatMeta, ['chat_id'], []);

                    $message = 'Imported update:chats, MetaCount: ' . $metaCount . ' Count: ' . $count . ' _page=' . $page . '&_perPage=1000';
                } else {
                    $message = 'No Chats to update';
                }

                $this->info($message);
                Log::info($message);
            }
        } catch (\Exception $exception) {
            $message = 'update:chats failed, Message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine();
            $this->error($message);
            Log::error($message);
        }
    }

    private function unsetKeys($chat)
    {
        unset($chat['id']);
        unset($chat['departmentid']);
        unset($chat['tags']);
        unset($chat['custom_fields']);
        unset($chat['date_created']);
        if (isset($chat['agentid'])) unset($chat['agentid']);

        return $chat;
    }
}
