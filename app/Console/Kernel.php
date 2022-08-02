<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Spatie\ShortSchedule\ShortSchedule;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\ImportUsers::class,
        \App\Console\Commands\ImportDailyStatistics::class,
        \App\Console\Commands\ImportDailyChats::class,
        \App\Console\Commands\ImportCalls::class,
        \App\Console\Commands\UpdateCalls::class,
        \App\Console\Commands\SaveLiveData::class,
        \App\Console\Commands\ImportLogs::class,
        \App\Console\Commands\Historical\ImportOldBookings::class,
        \App\Console\Commands\Customers\ImportCompanies::class,
        \App\Console\Commands\ResetFreeCallExpire::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('import:users')->daily();
        $schedule->command('import:companies')->dailyAt('00:15');
        $schedule->command('old:contacts')->dailyAt('00:17');
        $schedule->command('save:max')->dailyAt('00:18');
        $schedule->command('import:departments')->dailyAt('00:20')->weekdays();
        $schedule->command('save:billing')->weekdays()->dailyAt('20:00');
        $schedule->command('import:daily')->everyFiveMinutes()->weekdays()->between('6:56', '19:20');
        $schedule->command('import:chats')->everyFiveMinutes()->weekdays()->between('6:56', '19:20');
        $schedule->command('import:calls')->everyFiveMinutes()->weekdays()->between('6:56', '19:20');
        $schedule->command('update:calls')->hourly()->weekdays()->between('6:56', '20:00');
        $schedule->command('import:logs')->hourlyAt(1)->weekdays()->between('08:00', '18:20');
        $schedule->command('old:bookings')->hourlyAt(1)->weekdays()->between('08:00', '18:20');
        $schedule->command('import:cdr')->hourlyAt(2);
        $schedule->command('insert:cdr')->hourlyAt(5);
        $schedule->command('insert:cdr --prevDay')->dailyAt('01:30');//todo:check karogha esi hanenq, orinak esorva eghatsnern el update anem, heto gishery kpoxvi datan, es inchi hamar einq drel?
        $schedule->command('import:cdr --prevDay')->dailyAt('06:00');
        $schedule->command('pdf:report')->dailyAt('18:30')->weekdays();
        $schedule->command('mail:reports')->dailyAt('18:35')->weekdays();
        $schedule->command('deactivate:companies')->twiceMonthly(1, 16, '01:00');
        $schedule->command('reset:free-call')->monthlyOn(1, '00:01');
        $schedule->command('pdf:report --monthly')->monthlyOn(1, '01:01');
        $schedule->command('pdf:report --monthly --type=planning')->monthlyOn(1, '01:31');
        $schedule->command('mail:reports --monthly')->monthlyOn(1, '10:00');
        $schedule->command('mail:reports --monthly --type=planning')->monthlyOn(1, '10:30');
        $schedule->command('fixed:fees')->monthlyOn(1, '08:28');
    }

    protected function shortSchedule(ShortSchedule $shortSchedule)
    {
        $shortSchedule->command('save:live')->everySecond(10)->between('6:56', '19:20');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
