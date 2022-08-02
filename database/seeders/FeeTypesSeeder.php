<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FeeTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('fee_types')->insert([
            [
                'name' => 'Calls Fee',
                'slug' => 'calls_fee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chats Fee',
                'slug' => 'chats_fee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bookings Fee',
                'slug' => 'bookings_fee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Above 60 Fee',
                'slug' => 'above_60_fee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Monthly Fee',
                'slug' => 'monthly_fee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Free Calls',
                'slug' => 'free_calls',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cold Transferred Calls Fee',
                'slug' => 'cold_transferred_calls_fee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Warm Transferred Calls Fee',
                'slug' => 'warm_transferred_calls_fee',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Time Above Seconds',
                'slug' => 'time_above_seconds',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
