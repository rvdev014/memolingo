<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WordController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\IntakeController;
use App\Http\Controllers\Api\DictionaryController;
use App\Http\Controllers\Api\LexiconController;
use App\Http\Controllers\Api\NormalizeController;

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

Route::middleware('auth:sanctum')->group(function() {
    Route::get('/user', fn(Request $request) => $request->user());

    // Intake endpoints
    Route::post('/intake', [IntakeController::class, 'store'])->name('intake.store');
    Route::post('/intake/confirm', [IntakeController::class, 'confirm'])->name('intake.confirm');

    // Dictionary endpoints
    Route::get('/dictionary', [DictionaryController::class, 'index'])->name('dictionary.index');
    Route::post('/dictionary/learn/start', [DictionaryController::class, 'startLearning'])->name('dictionary.learn.start');
    Route::post('/dictionary/learn/answer', [DictionaryController::class, 'submitAnswer'])->name('dictionary.learn.answer');

    // Lexicon stats
    Route::get('/lexicon/stats', [LexiconController::class, 'stats'])->name('lexicon.stats');

    // Normalize utility
    Route::post('/normalize', [NormalizeController::class, 'normalize'])->name('normalize');

    // Legacy word/category endpoints (keeping for backward compatibility)
    Route::post('/category', [CategoryController::class, 'store'])->name('category.store');
    Route::delete('/category/{id}', [CategoryController::class, 'delete'])->name('category.delete');

    Route::controller(WordController::class)->prefix('/word')->group(function() {
        Route::post('/', 'store')->name('word.store');
        Route::get('/', 'getWords')->name('word.getWords');
        Route::get('/dictionary', 'getDictionaryWords')->name('word.getDictionaryWords');
        Route::get('/{id}', 'show')->name('word.show');
        Route::put('/{id}', 'update')->name('word.update');
        Route::delete('/{id}', 'delete')->name('word.delete');
        Route::post('/{id}/move', 'move')->name('word.move');
        Route::post('/{id}/progress', 'progress')->name('word.progress');
    });
});

Route::group([], function() {
    Route::post('/login', [AuthController::class, 'login'])->name('login'); // tested
    Route::post('/register', [AuthController::class, 'register'])->name('register'); // tested
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth:sanctum'); // tested
    Route::post('/me', [AuthController::class, 'me'])->name('me'); // tested
});
