<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            [
                'name'  => 'Main Point',
                'slug'  => 'main_point',
                'value' => "31",
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Calls Point',
                'slug'  => 'calls_point',
                'value' => "1",
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Bookings Point',
                'slug'  => 'bookings_point',
                'value' => "2",
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Chats Point',
                'slug'  => 'chats_point',
                'value' => "3",
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Missed call seconds',
                'slug'  => 'missed_call_seconds',
                'value' => "3",
                'description' => "Waiting time minimal seconds, to calculate as missed call",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Min calls Planning Page',
                'slug'  => 'min_calls_planning_page',
                'value' => "10",
                'description' => "Minimal calls count, that agent need to complete to be calculated as worked agent in that hour.",
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Main Point Planning Page',
                'slug'  => 'main_point_planning_page',
                'value' => "29",
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Money For Second',
                'slug'  => 'money_for_second',
                'value' => "0.3",
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Office IP',
                'slug'  => 'office_ip',
                'value' => "83.140.18.190",
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Difference Percentage',
                'slug'  => 'difference_percentage',
                'value' => "10",
                'description' => 'Page: planning. The percent, that need to be equal or less from Agents count and Theory count difference, to show green background.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Page 1',
                'slug'  => 'page_1',
                'value' => "page_1 value",
                'description' => 'http://int.personligtsvar.se/pages/page_1?apiKey=hKL80mn15KtI7qr7K87uqo7s1bHEo5lfYAa2pzPPuh7wn7HKZfSnuJE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Page 2',
                'slug'  => 'page_2',
                'value' => "page_2 value",
                'description' => 'http://int.personligtsvar.se/pages/page_2?apiKey=hKL80mn15KtI7qr7K87uqo7s1bHEo5lfYAa2pzPPuh7wn7HKZfSnuJE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Page 3',
                'slug'  => 'page_3',
                'value' => "page_3 value",
                'description' => 'http://int.personligtsvar.se/pages/page_3?apiKey=hKL80mn15KtI7qr7K87uqo7s1bHEo5lfYAa2pzPPuh7wn7HKZfSnuJE',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name'  => 'Footer Text',
                'slug'  => 'footer_text',
                'value' => "Personlig Svarsservice | Smidesvagen 10-12 | 171 41 Solna | Tel: 020 - 170 71 00",
                'description' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
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
                'description' => "Default fee for calls with duration more, than 1 minute",
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
            [
                'name' => 'Time Above Seconds',
                'slug' => 'time_above_seconds',
                'value' => 60,
                'description' => "Default fee for transferred calls",
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
