<?php

use App\Http\Controllers\Admin\FullScreenController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('update-data', function () {
//    \Illuminate\Support\Facades\Artisan::call('import:daily');

    return back();
});
Route::group(['middleware' => 'locale'], function () {
    Route::get('/', function () {
            return redirect('home');
    });
    Route::get('home', [HomeController::class, 'index'])->name('home');
    Route::get('set-locale/{locale}', [LanguageController::class, 'setLocale']);
    Route::get('/welcome', function () {
        return view('welcome');
    })->name('welcome');

    Route::get('icons', function () {return view('pages.icons');})->name('icons');
    Route::get('tables', function () {return view('pages.tables');})->name('table');

    Route::get('get-live-data', [FullScreenController::class, 'getLiveData']);

    Route::group(['middleware' => 'apiKey'], function () {
        Route::get('full-screen-statistics', [FullScreenController::class, 'index']);
        Route::get('pages/{page}', [PageController::class, 'index']);

    });

    Route::group(['middleware' => 'auth:web,employee'], function () {
        Route::get('full-screen', [FullScreenController::class, 'index'])->name('full-screen');
        Route::group(['prefix' => 'profile', 'as' => 'profile.'], function () {
            Route::get('edit', [ProfileController::class, 'edit'])->name('edit');
            Route::put('update', [ProfileController::class, 'update'])->name('update');
            Route::put('update/password', [ProfileController::class, 'password'])->name('update.password');
            Route::post('update-image', [ProfileController::class, 'updateImage'])->name('update_image');
        });
    });

    require __DIR__.'/admin.php';
    require __DIR__.'/employee.php';
    require __DIR__.'/auth.php';
    require __DIR__.'/actions.php';
});

Route::group(['middleware' => 'webhookCheckKey', 'prefix' => 'webhook'], function () {
    Route::get('test', [WebhookController::class, 'test']);
    //this is an endpoint to get chats from Eazy(another portal for chats), just keep this as it
    Route::post('companies', [WebhookController::class, 'saveEazyChats']);
});

