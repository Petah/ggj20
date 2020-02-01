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
            'score' => $request->input('data.attributes.score'),
        ]);
        return $this->getHighScores($request->input('data.attributes.levelId'));
    }

    public function fetch(Request $request, string $levelId)
    {
        return $this->getHighScores($levelId);
    }

    private function getHighScores(string $levelId)
    {
        $highScores = HighScore::where('level_id', $levelId)->orderBy('score', 'desc')->limit(5)->get();
        return [
            'data' => $highScores->map(function ($highScore) {
                return [
                    'id' => $highScore->id,
                    'type' => 'HighScore',
                    'attributes' => [
                        'levelId' => $highScore->level_id,
                        'playerName' => $highScore->player_name,
                        'score' => $highScore->score,
                        'timeAgo' => $highScore->created_at->diffForHumans(),
                    ],
                ];
            })
        ];
    }
}
