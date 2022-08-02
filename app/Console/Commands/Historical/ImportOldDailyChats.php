<?php

namespace App\Console\Commands\Historical;

use App\Models\DailyChat;
use App\Models\ImportedUser;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportOldDailyChats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old:chats';

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
        $userIds = ImportedUser::liveagentUsers()->pluck('liveagent_id', 'id')->toArray();
        $tag = '1zlj';
        $tag1 = 'cb75';

        foreach (range(0, 197) as $page) {
            echo "------------------------$page----------------------------";
            $response = Http::withHeaders([
                'apikey' => config('apiKeys.liveagent_api_key'),
            ])->get('https://psservice.liveagent.se/api/v3/tickets?_page='.$page.'&_perPage=1000');

            $dailyChats = json_decode($response->body(), true) ?: [];

            $count = 0;
            $data = [];
            foreach ($dailyChats as $chat) {
                if (in_array($tag, $chat['tags']) || in_array($tag1, $chat['tags'])) {
                    $date = Carbon::parse($chat['date_created'])->format('Y-m-d');
                    if (array_key_exists('agentid', $chat) && in_array($chat['agentid'], $userIds)) {
                        $data[] = [
                            'chat_id' => $chat['id'],
                            'user_id' => array_search($chat['agentid'], $userIds),
                            'date' => $date,
                            'date_created' => $chat['date_created'],
                        ];
                        /*DailyChat::updateOrCreate(['chat_id' => $chat['id']],
                            [
                                'chat_id' => $chat['id'],
                                'user_id' => array_search($chat['agentid'], $userIds),
                                'date' => $date,
                                'date_created' => $chat['date_resolved'],
                            ]);*/
                    } else {
                        $data[] = [
                            'chat_id' => $chat['id'],
                            'user_id' => 0,
                            'date' => $date,
                            'date_created' => $chat['date_created'],
                        ];
                        /*DailyChat::updateOrCreate(['chat_id' => $chat['id']],
                            [
                                'chat_id' => $chat['id'],
                                'user_id' => 0,
                                'date' => $date,
                                'date_created' => $chat['date_resolved'],
                            ]);*/
                    }
                    $count++;
                    echo $chat['id'] . '--';
                }
            }
            DailyChat::upsert($data, ['chat_id'], []);


            Log::info('Imported old:chats, Count: '.$count.' _page='.$page.'&_perPage=1000');
        }
    }
}
