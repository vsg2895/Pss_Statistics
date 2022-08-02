<?php

use App\Http\Controllers\Admin\AnnouncementController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\CompareCompaniesController;
use App\Http\Controllers\Admin\DateFeesController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\EmployeeStatisticController;
use App\Http\Controllers\Admin\PlanningController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceProviderController;
use App\Http\Controllers\Admin\ServiceProviderUserController;
use App\Http\Controllers\Admin\Settings\TranslationController;
use App\Http\Controllers\Admin\Settings\VariableController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\TeleTwoUsersController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Spatie\RolePermissionController;
use BeyondCode\LaravelWebSockets\Facades\WebSocketsRouter;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'as' => 'admin.'], function () {
    Route::get('/', function () {
        return redirect('home');
    });

    Route::get('companies/dashboard', [CompanyController::class, 'dashboard'])->name('companies.dashboard');

//   Get Chat,s messages
    Route::post('company-chats/{company}', [CompanyController::class, 'getChatData'])->name('companies.chat.more');
    Route::post('company-chats-conversations', [CompanyController::class, 'getChatConversations'])->name('get.chat.conversations');
    Route::get('companies/{company}/more-info', [CompanyController::class, 'getMoreInfo'])->name('companies.more-info');

//     end
// Db current values getting routes
//    Route::post('db/{company}', [DateFeesController::class, 'currentDbValues'])->name('company.currentDb');
//    Route::post('db-provider/{provider}', [DateFeesController::class, 'currentDbProviderValues'])->name('provider.currentDb');

//    Socket Routes
//    WebSocketsRouter::webSocket('/my-websocket', \App\Http\SocketHandler\MyCustomHandler::class);
//    End Socket Routes

    Route::put('companies/update_fees/{company}', [DateFeesController::class, 'updateFeesByDate'])->name('company.updateFeesByDate');
    Route::put('all-companies/update_fees', [DateFeesController::class, 'updateFeesByDateAll'])->name('company.updateFeesByDateAll');
    Route::post('announcement/company/{company}', [AnnouncementController::class, 'companyStore'])->name('company.announcement.store');
    Route::put('provider/update_fees/{provider}', [DateFeesController::class, 'updateFeesProviderByDate'])->name('provider.updateFeesByDate');
    Route::post('announcement/provider/{provider}', [AnnouncementController::class, 'providerStore'])->name('provider.announcement.store');
    Route::get('companies/compare', [CompareCompaniesController::class, 'compareByDateRange'])->name('compare.companies.dateRange');


    Route::group(['middleware' => ['auth:web']], function () {
        Route::resource('roles', RolePermissionController::class);
        Route::post('store/permission', [RolePermissionController::class, 'storePermission'])->name('permission.store');
        Route::post('attach-permission/{role}', [RolePermissionController::class, 'attachPermissionToRole'])->name('role-permission.attach');
        Route::post('detach-permission/{role}', [RolePermissionController::class, 'detachPermissionToRole'])->name('role-permission.detach');
        Route::get('get/permissions/{role}', [RolePermissionController::class, 'getRolePermissions'])->name('role.permissions.get');
        Route::get('get/guard-roles/{guard}', [RolePermissionController::class, 'getGuardRoles'])->name('get.guest.roles');
        Route::delete('permission/delete/{permission}', [RolePermissionController::class, 'deletePermission'])->name('delete.permission');

        Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
            Route::resource('variables', VariableController::class)->except(['show']);
            Route::get('billing', [VariableController::class, 'billing'])->name('billing');
            Route::get('translations', [TranslationController::class, 'index'])->name('translations.index');
            Route::post('translations', [TranslationController::class, 'store'])->name('translations.store');
            Route::get('translations-sp', [TranslationController::class, 'indexSp'])->name('translations.index.sp');
            Route::post('translations-sp', [TranslationController::class, 'storeSp'])->name('translations.store.sp');
        });

        Route::group(['prefix' => 'reports'], function () {
            Route::get('export-pdf', [ReportController::class, 'exportPdf'])->name('export_pdf');
            Route::get('export-pdf-planing', [ReportController::class, 'exportPdfPlaning'])->name('export_pdf.planing');
            Route::get('/', [ReportController::class, 'index'])->name('reports.index');
            Route::get('/report-delete', [ReportController::class, 'delete'])->name('reports.delete');
            Route::get('/download', [ReportController::class, 'download'])->name('reports.download');
        });

//        Excel Routes
        Route::post('export-excel/{company}', [BillingController::class, 'exportExcel'])->name('export.excel');
        Route::post('compare-export-excel', [CompareCompaniesController::class, 'exportExcel'])->name('compare.export.excel');
        Route::post('import-excel/{company}', [BillingController::class, 'importExcel'])->name('import.excel');
//        End Excel Routes

        Route::get('planning', [PlanningController::class, 'index'])->name('planning');
        Route::get('employee-statistics/{imported_user}', [EmployeeStatisticController::class, 'index'])->name('employee_statistics');
        Route::get('agents/{agent}/edit', [UserController::class, 'editAgent'])->name('agents.edit');
        Route::patch('agents/{agent}', [UserController::class, 'updateAgent'])->name('agents.update');

        Route::get('billing', [BillingController::class, 'index'])->name('billing.index');
        Route::resource('users', UserController::class)->except('create', 'show');
//        Relative user roles routes
        Route::get('users/roles-permissions/{user}', [UserController::class, 'getRolesPermissions'])->name('users.roles-permissions.get');
        Route::post('users/roles/store/{user}', [UserController::class, 'storeRole'])->name('users.roles.store');
        Route::post('users/roles/delete/{user}', [UserController::class, 'deleteRole'])->name('users.roles.delete');
        Route::post('users/permission/store/{user}', [UserController::class, 'storePermission'])->name('users.permission.store');
        Route::post('users/permission/delete/{user}', [UserController::class, 'deletePermission'])->name('users.permission.delete');
//    End Relative user roles routes
        Route::resource('companies', CompanyController::class)->except(['store', 'delete']);
        Route::post('update-company-compare/{company}', [CompanyController::class, 'updateCompareExcluding'])->name('update.compare.excluding');
        Route::get('companies-fees', [BillingController::class, 'companyFees'])->name('billing.company.fees');
        Route::resource('tags', TagController::class)->except(['show', 'delete']);
        Route::resource('service-providers', ServiceProviderController::class);
        Route::post('service-providers/upload-file', [ServiceProviderController::class, 'uploadFileForAll'])->name('service-providers.upload-file.all');
        Route::post('service-providers/upload-media', [ServiceProviderController::class, 'uploadMediaForAll'])->name('service-providers.upload-media.all');
        Route::post('service-providers/upload-file/{serviceProvider}', [ServiceProviderController::class, 'uploadFile'])->name('service-providers.upload-file');
        Route::post('service-providers/upload-media/{serviceProvider}', [ServiceProviderController::class, 'uploadMedia'])->name('service-providers.upload-media');
        Route::delete('service-providers/delete-file/{attachment}', [ServiceProviderController::class, 'deleteFile'])->name('service-providers.delete-file');
        Route::delete('service-providers/delete-media/{attachment}', [ServiceProviderController::class, 'deleteMedia'])->name('service-providers.delete-media');
        Route::post('service-providers/delete-file-all', [ServiceProviderController::class, 'deleteFileAll'])->name('service-providers.delete-file.all');
        Route::post('service-providers/delete-media-all', [ServiceProviderController::class, 'deleteMediaAll'])->name('service-providers.delete-media.all');
        Route::resources([
            'service-provider-users' => ServiceProviderUserController::class
        ]);
        Route::get('departments', [DepartmentController::class, 'index'])->name('department.index');
        Route::post('department/update/{department}', [DepartmentController::class, 'update'])->name('department.update');
        Route::delete('department/delete/{department}', [DepartmentController::class, 'delete'])->name('department.delete');
        Route::post('department/activate/{id}', [DepartmentController::class, 'activate'])->name('department.activate');
        Route::resource('integrations', TeleTwoUsersController::class);
        Route::get('insert-tele-two-users', [TeleTwoUsersController::class, 'insertTeleTwoUsers'])->name('insert-tele-two-users');
        Route::post('insert-tele-two-users-more', [TeleTwoUsersController::class, 'showMore'])->name('insert-tele-two-users-more');

    });
});
