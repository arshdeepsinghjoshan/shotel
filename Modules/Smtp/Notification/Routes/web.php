<?php

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

use Modules\Notification\Http\Controllers\NotificationController;

Route::get('/notification', [NotificationController::class, 'index']);
Route::get('/notification/delete/{id}', [NotificationController::class, 'finalDelete']);
Route::get('/notification/get-list', [NotificationController::class, 'getNotificationList']);
Route::get('/notification/view/{id}', [NotificationController::class, 'view']);
