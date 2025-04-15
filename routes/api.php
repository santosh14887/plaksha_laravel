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
	/*Route::post('subscribe', [
        'as'    =>  'register-interest',
        'uses'  =>  'NotificationChannels\ExpoPushNotifications\Http\ExpoController@subscribe',
    ]);

    Route::post('unsubscribe', [
        'as'    =>  'remove-interest',
        'uses'  =>  'NotificationChannels\ExpoPushNotifications\Http\ExpoController@unsubscribe',
    ]);*/
	//Route::post('/subscribe', [App\Http\Controllers\ExpoController::class, 'subscribe'])->name('subscribe');
	Route::post('/logout', [App\Http\Controllers\ApiController::class, 'logout'])->name('logout');
	Route::post('/user', [App\Http\Controllers\ApiController::class, 'user'])->name('user');
	Route::post('/dashboard', [App\Http\Controllers\ApiController::class, 'dashboard'])->name('dashboard');
	Route::post('/get_user_issue', [App\Http\Controllers\ApiController::class, 'get_user_issue'])->name('get_user_issue');
	Route::post('/notification', [App\Http\Controllers\ApiController::class, 'notification'])->name('notification');
	Route::post('add_issue', [App\Http\Controllers\ApiController::class, 'add_issue'])->name('add_issue');
	Route::post('/issue_category', [App\Http\Controllers\ApiController::class, 'issue_category'])->name('issue_category');
	Route::post('/user_update', [App\Http\Controllers\ApiController::class, 'user_update'])->name('user_update');
	Route::post('/get_order', [App\Http\Controllers\ApiController::class, 'get_order'])->name('get_order');
	Route::post('/get_ticket', [App\Http\Controllers\ApiController::class, 'get_ticket'])->name('get_ticket');
	Route::post('/add_ticket', [App\Http\Controllers\ApiController::class, 'add_ticket'])->name('add_ticket');
	Route::post('/update_assign_dispatch_status', [App\Http\Controllers\ApiController::class, 'update_assign_dispatch_status'])->name('update_assign_dispatch_status');
});
/* Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/user', [App\Http\Controllers\ApiController::class, 'user'])->name('user');
    Route::post('/sign-out', [AuthenticationController::class, 'logout']);
}); */
