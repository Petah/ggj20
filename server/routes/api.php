<?php

use Illuminate\Http\Request;
use Repainter\Http\Controllers\Api\CompareController;
use Repainter\Http\Controllers\Api\LevelController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('level/list', [LevelController::class, 'index']);
Route::get('compare', [CompareController::class, 'compare']);
