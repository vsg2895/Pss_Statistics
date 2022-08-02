<?php

namespace App\Providers;

use App\Contracts\CdrInterface;
use App\Contracts\ExcelInterface;
use App\Contracts\LiveagentApiInterface;
use App\Contracts\LogInterface;
use App\Contracts\PdfInterface;
use App\Contracts\StatisticServiceInterface;
use App\Contracts\TeleTwoApiInterface;
use App\Events\Excel\NotifyExcelDuration;
use App\Events\Excel\NotifyExcelImport;
use App\Events\HistoricalUpdateEvent;
use App\Services\DailyStatisticService;
use App\Services\DailyStatisticService1;
use App\Services\Excel\ExcelService;
use App\Services\Insert\TeleTwoApiService;
use App\Services\LiveagentApi\LiveagentService;
use App\Services\LogService;
use App\Services\Reports\CdrService;
use App\Services\Reports\PdfService;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Queue\Events\JobProcessed;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;
use Maatwebsite\Excel\Events\AfterImport;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Events\BeforeExport;
use Maatwebsite\Excel\Events\BeforeImport;
use Maatwebsite\Excel\Events\BeforeSheet;
use Maatwebsite\Excel\Jobs\QueueImport;
use Maatwebsite\Excel\Jobs\ReadChunk;
use Maatwebsite\Excel\Reader;
use Maatwebsite\Excel\Sheet;
use Maatwebsite\Excel\Writer;


class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container bindings that should be registered.
     *
     * @var array
     */
    public array $bindings = [
        PdfInterface::class => PdfService::class,
        CdrInterface::class => CdrService::class,
        LiveagentApiInterface::class => LiveagentService::class,
        TeleTwoApiInterface::class => TeleTwoApiService::class,
        ExcelInterface::class => ExcelService::class,
        LogInterface::class => LogService::class,
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        $this->app->bind(StatisticServiceInterface::class, function () {
            if (request()->route()->getName() === "home") {
                return new DailyStatisticService(
                    request()->date_range === 'true',
                    request()->start,
                    request()->end,
                    request()->start_date,
                    request()->compare_date
                );
            }

            return new DailyStatisticService1();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Writer::listen(BeforeExport::class, function () {
            event(new NotifyExcelDuration(__("Excel Export Report Is Started ..."), null));
        });

//        Queue::failing(function (JobFailed $event) {
//            event(new NotifyExcelImport(__("Something went wrong4."), null, null, null, null));
//            Log::error($event->job->payload()['data']['commandName'] . $event->exception->getMessage());
//        });

        Queue::after(function (JobProcessed $event) {
            sleep(1);
            $updateText = __('Historical Data Updated Successfully');
            $payload = $event->job->payload();
            switch ($payload['data']['commandName']) {
                case "App\Jobs\HistoricalUpdate":
                    event(new HistoricalUpdateEvent(null, $updateText, null, null));
                    break;
                case "Maatwebsite\Excel\Jobs\StoreQueuedExport":
                    event(new NotifyExcelDuration(__("Excel Export Report Is Ready To Check"), config('app.url') . 'admin/reports'));
                    break;
            }
        });
    }
}
