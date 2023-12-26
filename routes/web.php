<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\TelegramController;
use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('sendTelegram/{video}', [TelegramController::class, 'send'])->name('send.telegram');

Route::group(['prefix' => 'admin'], function () {
    Route::group(['prefix' => 'download'], function () {
        Route::post('video', [VideoController::class, 'downloadVideo'])->name('download.video');
        Route::get('status/get', [VideoController::class, 'getStatus'])->name('download.status.get');
        Route::get('status/delete', [VideoController::class, 'deleteStatus'])->name('download.status.delete');
    });
});

Route::get('register', [RegisterController::class, 'index'])->name('register');
Route::post('register', [RegisterController::class, 'register']);
Route::get('login', [LoginController::class, 'index'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::get('logout', [LoginController::class, 'logout']);
