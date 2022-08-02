<?php

namespace App\Providers;

use App\Models\FeeType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('components.db_fees', function ($view) {
            $feesWithTableNames = FeeType::CORRESPONDE_TABLE_NAME_TO_HISTORICAL_FEES;
            $view->with('feesWithTableNames', $feesWithTableNames);
        });

        View::composer(['*'], function ($view) {
            $currStart = Carbon::now()->startOfMonth()->format('Y-m-d');//month start
            $currEnd = Carbon::now()->format('Y-m-d');//toda
            $defaultStart = request()->start ? request()->start
                : Carbon::now()->startOfMonth()->format('Y-m-d');//month start
            $defaultEnd = request()->end ? request()->end
                : Carbon::now()->format('Y-m-d');//today

            $todayStart = request()->start ? request()->start
                : Carbon::now()->format('Y-m-d');
            $todayEnd = request()->end ? request()->end
                : Carbon::now()->format('Y-m-d');

            $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
            $lastMonth = new Carbon('first day of last month');

            $view->with('lastWeekStart', $lastWeekStart)
                ->with('thisMonday', Carbon::now()->startOfWeek()->format('Y-m-d'))
                ->with('thisMonthStart', Carbon::now()->startOfMonth()->format('Y-m-d'))
                ->with('thisMonthEnd', Carbon::now()->endOfMonth()->format('Y-m-d'))
                ->with('lastMonday', $lastWeekStart->format('Y-m-d'))
                ->with('lastFriday', $lastWeekStart->addDays(4))
                ->with('lastMonth', $lastMonth)
                ->with('lastMonthStart', $lastMonth->startOfMonth()->format('Y-m-d'))
                ->with('lastMonthEnd', $lastMonth->endOfMonth()->format('Y-m-d'))
                ->with('start', $defaultStart)
                ->with('end', $defaultEnd)
                ->with('currStart', $currStart)
                ->with('currEnd', $currEnd)
                ->with('todayStart', $todayStart)
                ->with('todayEnd', $todayEnd);
        });
    }
}
