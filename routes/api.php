<?php

use Illuminate\Http\Request;
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
Route::post('login', [App\Http\Controllers\ApiController::class, 'login'])->name('login');
Route::middleware(['auth:sanctum'])->group(function () {
	Route::post('/update_assign_dispatch_status', [App\Http\Controllers\ApiController::class, 'update_assign_dispatch_status'])->name('update_assign_dispatch_status');
});
/* Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', [App\Http\Controllers\ApiController::class, 'user'])->name('user');
    Route::post('/sign-out', [AuthenticationController::class, 'logout']);
}); */
