<?php

use App\Http\Controllers\admin\ClarificationController;
use App\Http\Controllers\admin\ReportAdminController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\user\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'middleware' => ['api', 'auth:api'],
], function(){
    Route::post('login', [UserController::class, 'login'])->withoutMiddleware(['auth:api']);
    Route::post('logout', [UserController::class, 'logout']);
    Route::post('register', [UserController::class, 'register'])->withoutMiddleware(['auth:api']);
    Route::get('me', [UserController::class, 'getAuthenticatedUser']);

});

// guest
Route::prefix('guest')->group(function(){
    Route::get('', [GuestController::class, 'index']);
    Route::get('landing', [GuestController::class, 'landing']);
    Route::get('show/{id}', [GuestController::class, 'show']);
    Route::get('search/{keyword}', [GuestController::class, 'search']);
});


// MUST LOGIN
Route::middleware(['jwt.verify'])->group(function(){
    // admin
    Route::middleware(['is.admin'])->group(function(){
        Route::prefix('admin')->group(function(){
            Route::prefix('clarification')->group(function(){
                // aman
                Route::get('', [ClarificationController::class, 'index']);
                Route::post('store', [ClarificationController::class, 'store']);
                Route::get('show/{id}', [ClarificationController::class, 'show']);
                Route::delete('destroy/{id}', [ClarificationController::class, 'destroy']);
            });
            Route::prefix('report')->group(function(){
                // aman
                Route::get('', [ReportAdminController::class, 'index']);
                Route::get('show/{id}', [ReportAdminController::class, 'show']);
                Route::delete('destroy/{id}', [ReportAdminController::class, 'destroy']);
                Route::get('trash', [ReportAdminController::class, 'trash']);
                Route::get('restore/{id}', [ReportAdminController::class, 'restore']);
                Route::delete('force-delete/{id}', [ReportAdminController::class, 'forceDelete']);
            });
        });
    });

    // user
    Route::prefix('user')->group(function(){
        Route::prefix('report')->group(function(){
            // aman
            Route::get('', [ReportController::class, 'index']);
            Route::get('show/{id}', [ReportController::class, 'show']);
            Route::post('store', [ReportController::class, 'store']);
            Route::put('update/{id}', [ReportController::class, 'update']);
            Route::delete('destroy/{id}', [ReportController::class, 'destroy']);
            
        });
    });
});
