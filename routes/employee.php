<?php

use App\Http\Controllers\UserStatisticController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'employee', 'as' => 'employee.'], function () {
    Route::group(['middleware' => ['auth:employee', 'agentLogs']], function () {
        Route::get('user-statistics', [UserStatisticController::class, 'index'])->name('user_statistics');
    });
    require __DIR__ . '/employee-auth.php';
});
