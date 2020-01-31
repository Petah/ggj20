<?php

use Illuminate\Http\Request;
use Repainter\Http\Controllers\Api\CompareController;
use Repainter\Http\Controllers\Api\LevelController;

Route::get('level/list', [LevelController::class, 'index']);
Route::post('compare', [CompareController::class, 'compare']);
