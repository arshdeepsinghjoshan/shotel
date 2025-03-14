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

use Modules\Smtp\Http\Controllers\MailConfigurationController;
use Modules\Smtp\Http\Controllers\SmtpEmailQueueController;




Route::group(['middleware' => 'prevent-back-history'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::group(['middleware' => 'admin'], function () {
            Route::get('/email-queue', [SmtpEmailQueueController::class, 'index']);
            Route::get('/email-queues/get-list', [SmtpEmailQueueController::class, 'getEmailQueuesList']);
            Route::get('/email-queue/view/{id}', [SmtpEmailQueueController::class, 'view']);
            Route::get('/email-queue/email-verification/{id}', [SmtpEmailQueueController::class, 'emailVerification']);
            Route::get('/email-queue/update/', [SmtpEmailQueueController::class, 'update']);
            Route::post('/mail-configuration/add', [MailConfigurationController::class, 'Store'])->name('smtp.add');
            Route::post('/mail-configuration/update', [MailConfigurationController::class, 'update'])->name('smtp.update');
            Route::get('/email-queue/account', [MailConfigurationController::class, 'index']);
            Route::get('/smtp-accout/get-list', [MailConfigurationController::class, 'getSmtpAccountList']);
            Route::get('/email-queue/account/add', [MailConfigurationController::class, 'add']);
            Route::get('/email-queue/account/edit/{id}', [MailConfigurationController::class, 'edit']);
            Route::get('/email-queue/account/delete/{id}', [MailConfigurationController::class, 'finalDelete']);
            Route::get('/email-queue/account/view/{id}', [MailConfigurationController::class, 'view']);
            Route::get('/smtp/account/stateChange/{id}/{state_id}', [MailConfigurationController::class, 'stateChange']);
            Route::get('/email-queue/delete/{id}', [SmtpEmailQueueController::class, 'finalDelete']);
        });
    });
});
