<?php

use App\Http\Controllers\Api\HighScoreController;
use App\Http\Controllers\Api\LevelController;
use App\Http\Controllers\Api\RoundController;
use Illuminate\Support\Facades\Route;

Route::get('levels', [LevelController::class, 'index']);
Route::get('levels/{level}/high-scores', [HighScoreController::class, 'index']);
Route::get('rounds/{round}/original', [RoundController::class, 'original']);

// Live scoring runs every few hundred milliseconds while someone paints.
Route::post('rounds/{round}/painting', [RoundController::class, 'painting'])->middleware('throttle:painting');

Route::middleware('throttle:rounds')->group(function () {
    Route::post('rounds', [RoundController::class, 'store']);
    Route::post('rounds/{round}/finish', [RoundController::class, 'finish']);
    Route::post('rounds/{round}/high-score', [HighScoreController::class, 'store']);
});
