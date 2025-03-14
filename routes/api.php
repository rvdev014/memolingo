<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WordController;
use App\Http\Controllers\Api\CategoryController;
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

    Route::post('/category', [CategoryController::class, 'store'])->name('category.store'); // tested
    Route::delete('/category/{categoryId}', [CategoryController::class, 'delete'])->name('category.delete'); // tested

    Route::controller(WordController::class)->prefix('/word')->group(function () {
        Route::get('/', 'getWords')->name('word.getWords');
        Route::get('/dictionary', 'getDictionaryWords')->name('word.getDictionaryWords');
        Route::post('/', 'store')->name('word.store');
        Route::get('/{wordId}', 'show')->name('word.show');
        Route::put('/{wordId}', 'update')->name('word.update');
        Route::delete('/{wordId}', 'delete')->name('word.delete');
        Route::post('/{wordId}/move', 'move')->name('word.move');
        Route::post('/{wordId}/progress', 'progress')->name('word.progress');
    });
});

Route::group([], function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
});
