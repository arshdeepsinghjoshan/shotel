<?php

use Illuminate\Support\Facades\Route;
use Modules\Comment\App\Http\Controllers\CommentController;


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

Route::group(['middleware' => 'prevent-back-history'], function () {
    Route::group(['middleware' => 'auth'], function () {
        Route::get('/comment', [CommentController::class, 'index']);
        Route::post('/comment/add', [CommentController::class, 'add'])->name('comment.add');
        Route::get('/comment/get-list', [CommentController::class, 'getList']);
        Route::get('/comment/view/{id}', [CommentController::class, 'view']);
    });
});
