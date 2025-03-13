<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BuyRequestController;
use App\Http\Controllers\Api\ContactRequestController;
use App\Http\Controllers\Api\ManualController;
use App\Http\Controllers\Api\TourController;
use App\Http\Controllers\Api\TourRequestController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', fn(Request $request) => $request->user());
});

Route::group([], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
});
