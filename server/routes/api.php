<?php

use Repainter\Http\Controllers\Api\CompareController;
use Repainter\Http\Controllers\Api\HighScoreController;
use Repainter\Http\Controllers\Api\LevelController;

Route::get('level/list', [LevelController::class, 'index']);
Route::post('high-score/submit', [HighScoreController::class, 'submit']);
Route::get('high-score/fetch/{levelId}', [HighScoreController::class, 'fetch']);
Route::post('compare', [CompareController::class, 'compare']);
