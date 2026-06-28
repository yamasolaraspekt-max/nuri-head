<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Customer\NewLeadsController;
use App\Http\Controllers\Task\PersonalTaskController;
use App\Http\Controllers\Employee\Note\PersonalNoteReminderController;
use App\Http\Controllers\FusionWebhookController;

use App\Http\Controllers\Api\MobileAuthController;
use App\Http\Controllers\Api\MobilePlannerApiController;
use App\Http\Controllers\Api\MobileAttendanceController;
use App\Http\Controllers\Api\MobileCalendarController;
use App\Http\Controllers\Api\MobileEmployeesController;
use App\Http\Controllers\Api\MobileCustomersController;
use App\Http\Controllers\Api\MobileProfileController;
use App\Http\Controllers\Api\MasterSetApiController;

use App\Http\Controllers\Planner\PlannerApiAuthController;
use App\Http\Controllers\Planner\PlannerEmployeeApiController;
use App\Http\Controllers\Planner\PlannerMobileCustomerImageController;
use App\Http\Controllers\Planner\PlannerMasterSetController;
use App\Http\Controllers\Planner\PlannerItemMaterialController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| This file is automatically prefixed with /api.
|
*/

/*
|--------------------------------------------------------------------------
| Authenticated Laravel user
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Lead Suggestions
|--------------------------------------------------------------------------
| Final URLs:
| GET /api/lead-name-suggestions
| GET /api/lead-lastname-suggestions
|--------------------------------------------------------------------------
*/

Route::get('/lead-name-suggestions', [NewLeadsController::class, 'getnameSuggestions'])
    ->name('api.lead.name-suggestions');

Route::get('/lead-lastname-suggestions', [NewLeadsController::class, 'getLastnameSuggestions'])
    ->name('api.lead.lastname-suggestions');

/*
|--------------------------------------------------------------------------
| Personal Task Notifications
|--------------------------------------------------------------------------
| Final URL:
| GET /api/notifications/task/{task_id}
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/notifications/task/{task_id}', [PersonalTaskController::class, 'getTaskNotifications'])
        ->whereNumber('task_id')
        ->name('api.notifications.personal_task');
});

/*
|--------------------------------------------------------------------------
| Fusion Webhooks
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:10,1')->group(function () {
    Route::post('/fusion-form/webhook/entries', [FusionWebhookController::class, 'getEntries'])
        ->name('api.fusion.webhook.entries');
});

Route::post('/fusion/webhook', [FusionWebhookController::class, 'receive'])
    ->middleware('fusion.token')
    ->name('api.fusion.webhook.receive');

/*
|--------------------------------------------------------------------------
| Mobile Login API
|--------------------------------------------------------------------------
| Final URL:
| POST /api/mobile/login
|--------------------------------------------------------------------------
*/

Route::prefix('mobile')
    ->name('api.mobile.')
    ->middleware('throttle:20,1')
    ->group(function () {
        Route::post('/login', [MobileAuthController::class, 'login'])
            ->name('login');
    });

/*
|--------------------------------------------------------------------------
| Protected Mobile API
|--------------------------------------------------------------------------
| Final prefix:
| /api/mobile/...
|--------------------------------------------------------------------------
*/

Route::prefix('mobile')
    ->name('api.mobile.')
    ->middleware('auth:sanctum')
    ->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Auth / Profile
        |--------------------------------------------------------------------------
        */

        Route::get('/me', [MobileAuthController::class, 'me'])
            ->name('me');

        Route::post('/logout', [MobileAuthController::class, 'logout'])
            ->name('logout');

        Route::get('/profile', [MobileProfileController::class, 'show'])
            ->name('profile');

        /*
        |--------------------------------------------------------------------------
        | Tasks
        |--------------------------------------------------------------------------
        */

        Route::get('/tasks', [MobilePlannerApiController::class, 'index'])
            ->name('tasks.index');

        Route::post('/tasks/sync', [MobilePlannerApiController::class, 'sync'])
            ->name('tasks.sync');

        /*
        |--------------------------------------------------------------------------
        | Attendance
        |--------------------------------------------------------------------------
        */

        Route::post('/attendance/action', [MobileAttendanceController::class, 'action'])
            ->name('attendance.action');

        Route::post('/attendance/location', [MobileAttendanceController::class, 'location'])
            ->name('attendance.location');

        Route::post('/attendance/sync', [MobileAttendanceController::class, 'sync'])
            ->name('attendance.sync');

        Route::get('/attendance/history', [MobileAttendanceController::class, 'history'])
            ->name('attendance.history');

        Route::post('/attendance/log', [MobileAttendanceController::class, 'log'])
            ->name('attendance.log');

        /*
        |--------------------------------------------------------------------------
        | Calendar
        |--------------------------------------------------------------------------
        */

        Route::get('/calendar', [MobileCalendarController::class, 'index'])
            ->name('calendar.index');

        Route::post('/calendar', [MobileCalendarController::class, 'store'])
            ->name('calendar.store');

        Route::get('/calendar/{id}', [MobileCalendarController::class, 'show'])
            ->whereNumber('id')
            ->name('calendar.show');

        /*
        |--------------------------------------------------------------------------
        | Employees / Customers
        |--------------------------------------------------------------------------
        */

        Route::get('/employees', [MobileEmployeesController::class, 'index'])
            ->name('employees.index');

        Route::get('/customers', [MobileCustomersController::class, 'index'])
            ->name('customers.index');
    });

/*
|--------------------------------------------------------------------------
| Secure Master Set API
|--------------------------------------------------------------------------
| Final prefix:
| /api/secure/master-sets/...
|--------------------------------------------------------------------------
*/

Route::prefix('secure/master-sets')
    ->name('api.secure.master-sets.')
    ->middleware('throttle:60,1')
    ->group(function () {
        Route::get('/', [MasterSetApiController::class, 'index'])
            ->name('index');

        Route::get('/{id}', [MasterSetApiController::class, 'show'])
            ->whereNumber('id')
            ->name('show');
    });

Route::get('/secure/master-sets-debug', [MasterSetApiController::class, 'debug'])
    ->middleware('throttle:60,1')
    ->name('api.secure.master-sets.debug');

/*
|--------------------------------------------------------------------------
| Planner / Nuriva Integration API
|--------------------------------------------------------------------------
| Final prefix:
| /api/planner/...
|--------------------------------------------------------------------------
*/

Route::prefix('planner')
    ->as('api.planner.')
    ->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Planner Auth
        |--------------------------------------------------------------------------
        | Final URL:
        | POST /api/planner/auth/token
        |--------------------------------------------------------------------------
        */

        Route::post('/auth/token', [PlannerApiAuthController::class, 'token'])
            ->middleware('throttle:10,1')
            ->name('auth.token');

        /*
        |--------------------------------------------------------------------------
        | Protected Planner API
        |--------------------------------------------------------------------------
        */

        Route::middleware('auth:sanctum')->group(function () {
            /*
            |--------------------------------------------------------------------------
            | Auth
            |--------------------------------------------------------------------------
            */

            Route::get('/auth/me', [PlannerApiAuthController::class, 'me'])
                ->name('auth.me');

            Route::post('/auth/logout', [PlannerApiAuthController::class, 'logout'])
                ->name('auth.logout');

            Route::post('/auth/logout-all', [PlannerApiAuthController::class, 'logoutAll'])
                ->name('auth.logout_all');

            /*
            |--------------------------------------------------------------------------
            | Employee Work / Reports
            |--------------------------------------------------------------------------
            */

            Route::get('/my-work', [PlannerEmployeeApiController::class, 'myWork'])
                ->name('my_work');

            Route::get('/my-day-report', [PlannerEmployeeApiController::class, 'myDayReport'])
                ->name('my_day_report');

            Route::get('/employees/{employee}/work', [PlannerEmployeeApiController::class, 'employeeWork'])
                ->whereNumber('employee')
                ->name('employees.work');

            Route::get('/employees/{employee}/day-report', [PlannerEmployeeApiController::class, 'employeeDayReport'])
                ->whereNumber('employee')
                ->name('employees.day_report');

            /*
            |--------------------------------------------------------------------------
            | Nuriva: Complete Planner Item With Report
            |--------------------------------------------------------------------------
            | Final URL:
            | PATCH /api/planner/items/{item}/complete-report
            |--------------------------------------------------------------------------
            */

            Route::patch('/items/{item}/complete-report', [PlannerEmployeeApiController::class, 'completeItemWithReport'])
                ->whereNumber('item')
                ->name('items.complete_report');

            /*
            |--------------------------------------------------------------------------
            | Nuriva: Customer Image Upload
            |--------------------------------------------------------------------------
            | Final URLs:
            | POST /api/planner/customer-images/upload
            | GET  /api/planner/customer-images
            |--------------------------------------------------------------------------
            |
            | Nuriva sends task/customer photos here.
            | nuri-head saves them into:
            | - images table
            | - storage/app/uploads/customers
            |--------------------------------------------------------------------------
            */

            Route::post('/customer-images/upload', [PlannerMobileCustomerImageController::class, 'upload'])
                ->name('customer_images.upload');

            Route::get('/customer-images', [PlannerMobileCustomerImageController::class, 'index'])
                ->name('customer_images.index');



            Route::get('/master-sets', [PlannerMasterSetController::class, 'index'])
                ->name('master_sets.index');

            Route::get('/master-sets/search', [PlannerMasterSetController::class, 'search'])
                ->name('master_sets.search');

            Route::get('/master-sets/{masterSet}', [PlannerMasterSetController::class, 'show'])
                ->whereNumber('masterSet')
                ->name('master_sets.show');

            Route::get('/items/{plannerItem}/master-sets', [PlannerMasterSetController::class, 'linked'])
                ->whereNumber('plannerItem')
                ->name('items.master_sets.linked');

            Route::post('/items/{item}/master-sets/{masterSet}', [PlannerMasterSetController::class, 'link'])
                ->whereNumber('item')
                ->whereNumber('masterSet')
                ->name('items.master_sets.link');

            Route::delete('/items/{item}/master-sets/{masterSet}', [PlannerMasterSetController::class, 'unlink'])
                ->whereNumber('item')
                ->whereNumber('masterSet')
                ->name('items.master_sets.unlink');

            Route::post('/plans/{plan}/master-sets/{masterSet}/add', [PlannerMasterSetController::class, 'addToPlan'])
                ->whereNumber('plan')
                ->whereNumber('masterSet')
                ->name('plans.master_sets.add');


            Route::get('/items/{item}/materials', [PlannerItemMaterialController::class, 'index'])
                ->whereNumber('item')
                ->name('items.materials.index');

            Route::post('/items/{item}/materials', [PlannerItemMaterialController::class, 'store'])
                ->whereNumber('item')
                ->name('items.materials.store');

            
        });
    });