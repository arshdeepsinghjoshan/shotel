<?php

use Illuminate\Support\Facades\Route;
use Modules\Logger\App\Http\Controllers\LogController;

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

Route::group(['middleware' => ['prevent-back-history', 'admin']], function () {
    Route::get('/log', [LogController::class, 'index']);
    Route::get('/log/view/{id}', [LogController::class, 'view']);
    Route::get('/log/list', [LogController::class, 'list']);
});
