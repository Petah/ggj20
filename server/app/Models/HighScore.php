<?php

namespace Repainter\Models;

use Illuminate\Database\Eloquent\Model;

class HighScore extends Model
{
    protected $fillable = [
        'level_id',
        'player_name',
        'score',
    ];
}
