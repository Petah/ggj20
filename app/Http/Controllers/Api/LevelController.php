<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\LevelResource;
use App\Levels\LevelRepository;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LevelController extends Controller
{
    public function index(LevelRepository $levels): AnonymousResourceCollection
    {
        return LevelResource::collection($levels->all()->shuffle()->values());
    }
}
