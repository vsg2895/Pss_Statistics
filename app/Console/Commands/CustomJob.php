<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingMeta;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CustomJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run oen time tasks if need.';

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
            $missingMeta = [];
            Booking::where('contact_id', 0)->with('bookingMeta')->chunk(5000, function ($bookings) use ($missingMeta){
                foreach ($bookings as $booking)  {
                    if ($booking->bookingMeta) {
                        $contactId = json_decode($booking->bookingMeta->meta_data)->contactno;

                        $booking->update(['contact_id' => $contactId]);
                    } else {
                        $missingMeta[$booking->id] = $booking->booking_id;
                    }
                }
            });

            Log::info('Bookings with missing meta data: ' . json_encode($missingMeta));

            $this->info('All booking updated successfully');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage() .  ' Line: ' . $exception->getLine());
            Log::error('custom:run failed, message: ' . $exception->getMessage() . ' Line: ' . $exception->getLine());
        }
    }
}
