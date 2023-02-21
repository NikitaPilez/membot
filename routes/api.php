<?php

use App\Http\Controllers\TelegramController;
use App\Http\Controllers\VideoController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/download-video', [VideoController::class, 'downloadVideo']);
Route::get('/telegram-auth', [TelegramController::class, 'auth']);
Route::post('/telegram-auth', [TelegramController::class, 'auth']);
