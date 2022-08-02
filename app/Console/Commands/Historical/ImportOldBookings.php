<?php

namespace App\Console\Commands\Historical;

use App\Models\Booking;
use App\Models\BookingMeta;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportOldBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'old:bookings';

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
            $lastBooking = Booking::orderBy('id', 'desc')->first();
            $datetimeFrom = '20170922000000';
            if ($lastBooking) {
                $datetimeFrom = $lastBooking->added_at;
                $datetimeFrom = Carbon::parse($datetimeFrom)->format('YmdHis');
            }

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . config('apiKeys.servit_api_key'),
            ])->get(config('apiKeys.servit_url') . "/crm_logs?type=95&addts:gt=$datetimeFrom");

            $bookings = json_decode($response->body(), true) ?: [];

            if (isset($bookings['tickno'])) $bookings = [$bookings];
            $count = 0;
            $bookingData = [];
            $bookingMeta = [];

            foreach ($bookings as $booking) {
                $count++;
                $bookingId = intval($booking['logno']);

                $bookingData[] = [
                    'booking_id' => $bookingId,
                    'agent_id' => $booking['addby'],
                    'contact_id' => $booking['contactno'],
                    'tickno' => $booking['tickno'],
                    'added_at' => Carbon::parse($booking['addts'])->format('Y-m-d H:i:s'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                $booking = $this->unsetKeys($booking);
                $bookingMeta[] = [
                    'booking_id' => $bookingId,
                    'meta_data' => json_encode($booking),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

//            Booking::create($bookingData);
//            BookingMeta::create($bookingMeta);

//            echo $bookingId . '--';
            }

            Booking::insert($bookingData);
            BookingMeta::insert($bookingMeta);

            echo 'RESULT: Booking imported, count: ' . $count . '========================= Start Date: ' . $datetimeFrom;

            $message = "Bookings imported successfully. Count: " . $count;
            $this->info($message);
            Log::info($message);

            if (count($bookings) >= 1000) $this->handle();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage() . ' Line: ' . $exception->getLine());
            Log::error('import:bookings failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine());
        }
    }

    private function unsetKeys($booking)
    {
        unset($booking['addby']);
        unset($booking['tickno']);
        unset($booking['addts']);

        return $booking;
    }
}
