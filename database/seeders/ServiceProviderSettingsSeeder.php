<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceProviderSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('service_provider_settings')->insert([
            [
                'name' => 'Calls Fee',
                'slug' => 'calls_fee',
                'value' => 18,
                'description' => "Default fee for calls",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Chats Fee',
                'slug' => 'chats_fee',
                'value' => 30,
                'description' => 'Default fee for chats',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bookings Fee',
                'slug' => 'bookings_fee',
                'value' => 25,
                'description' => "Default fee for bookings",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Above 60 Fee',
                'slug' => 'above_60_fee',
                'value' => 0.18,
                'description' => "Default fee for calls with duration more, than the value set for the 'Time Above Seconds'",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Monthly Fee',
                'slug' => 'monthly_fee',
                'value' => 250,
                'description' => "Default monthly fee",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Free Calls',
                'slug' => 'free_calls',
                'value' => 0,
                'description' => "Default free calls count",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cold Transferred Calls Fee',
                'slug' => 'cold_transferred_calls_fee',
                'value' => 3.99,
                'description' => "Default fee for transferred calls",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Warm Transferred Calls Fee',
                'slug' => 'warm_transferred_calls_fee',
                'value' => 4.99,
                'description' => "Default fee for transferred calls",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
