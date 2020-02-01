<?php

namespace Repainter\Http\Controllers\Api;

use Illuminate\Http\Request;
use Repainter\Http\Controllers\Controller;
use Repainter\Models\HighScore;

class HighScoreController extends Controller
{
    public function submit(Request $request)
    {
        $highScore = HighScore::create([
            'level_id' => $request->input('data.attributes.levelId'),
            'player_name' => $request->input('data.attributes.playerName'),
            'score' => $request->input('data.attributes.score') ?: 0,
            'bonus' => $request->input('data.attributes.bonus') ?: 0,
        ]);
        return $this->getHighScores($request->input('data.attributes.levelId'), $highScore);
    }

    public function fetch(Request $request, string $levelId)
    {
        return $this->getHighScores($levelId);
    }

    private function getHighScores(string $levelId, ?HighScore $newHighScore = null)
    {
        $highScores = HighScore::where('level_id', $levelId)->orderBy('score', 'desc')->limit(5)->get();
        return [
            'data' => $highScores->map(function ($highScore) use ($newHighScore) {
                return [
                    'id' => $highScore->id,
                    'type' => 'HighScore',
                    'attributes' => [
                        'levelId' => $highScore->level_id,
                        'playerName' => $highScore->player_name,
                        'score' => (float) $highScore->score,
                        'bonus' => (float) $highScore->bonus,
                        'timeAgo' => $highScore->created_at->diffForHumans(),
                        'new' => $newHighScore && $newHighScore->id == $highScore->id,
                    ],
                ];
            })
        ];
    }
}
