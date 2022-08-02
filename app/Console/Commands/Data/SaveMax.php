<?php

namespace App\Console\Commands\Data;

use App\Models\Booking;
use App\Models\Call;
use App\Models\DailyChat;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SaveMax extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:max';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Save max data for calls, booking and chats to cache.';

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
            $data = [];
            $data = $this->getMaxCalls($data);
            $data = $this->getMaxBookings($data);
            $data = $this->getMaxChats($data);

            Cache::forever('max_data', $data);

            $this->info('Max data saved to cache');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            Log::error('save:max failed, message: ' . $exception->getMessage() . ', line: ' . $exception->getLine());
        }
    }

    private function getMaxCalls($data)
    {
        $callsData = Call::select(DB::raw('COUNT(connected_at) as answered'), DB::raw('DATE(started_at) as date'),
            DB::raw('COUNT(id) - COUNT(connected_at) as missed'))
            ->groupByRaw('DATE(started_at)')->get();

        $maxAnswered = $callsData->max('answered');

        $data['calls'] = [
            'max_answered' => [
                'count' => $maxAnswered,
                'date' => $callsData->where('answered', $maxAnswered)->first()->date,
            ],
        ];

        return $data;
    }

    private function getMaxBookings($data)
    {
        $bookingsData = Booking::select(DB::raw('COUNT(id) as count'), DB::raw('DATE(added_at) as date'))
            ->groupByRaw('DATE(added_at)')->get();

        $max = $bookingsData->max('count');

        $data['bookings'] = [
            'max' => [
                'count' => $max,
                'date' => $bookingsData->where('count', $max)->first()->date,
            ],
        ];

        return $data;
    }

    private function getMaxChats($data)
    {
        $chatsData = DailyChat::select(DB::raw('COUNT(id) as count'), DB::raw('DATE(date_created) as date'))
            ->groupByRaw('DATE(date_created)')->get();

        $max = $chatsData->max('count');

        $data['chats'] = [
            'max' => [
                'count' => $max,
                'date' => $chatsData->where('count', $max)->first()->date,
            ],
        ];

        return $data;
    }
}
