<?php

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

Route::post('login', [UserController::class, 'login']);
Route::post('logout', [UserController::class, 'logout']);
Route::post('register', [UserController::class, 'register']);

// admin
Route::middleware('jwt.verify')->group(function(){
    Route::prefix('admin')->group(function(){
        // me
        Route::get('me', [UserController::class, 'getAuthenticatedUser']);
    });
});
