<?php

namespace App\Providers;

use Illuminate\Support\Facades\Response;
use Illuminate\Support\ServiceProvider;

class ResponseMacroServiceProvider extends ServiceProvider
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
        Response::macro('success', function ($message = null, $data = [], $status = 200, $headers = [], $inJson = true) {
            if (!$message) {
                $message = __('Well done!');
            }
            return $inJson ? response()->json([
                'data' => $data,
                'message' => $message
            ], $status)->withHeaders($headers)
                : response($data)->withHeaders($headers);
        });

        Response::macro('error', function ($message = 'Something went wrong!', $status = 400) {
            return response()->json([
                'message' => $message
            ], $status);
        });
    }
}
