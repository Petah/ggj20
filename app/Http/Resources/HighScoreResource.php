<?php

namespace App\Http\Resources;

use App\Models\HighScore;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin HighScore */
class HighScoreResource extends JsonResource
{
    /** Request attribute naming the high score just submitted, so the game can highlight it. */
    public const NEW_ID = 'new_high_score_id';

    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'HighScore',
            'attributes' => [
                'levelId' => $this->level_id,
                'playerName' => $this->player_name,
                'score' => (float) $this->score,
                'bonus' => $this->bonus,
                'total' => $this->total(),
                'timeAgo' => $this->created_at->diffForHumans(),
                'new' => $request->attributes->get(self::NEW_ID) === $this->id,
            ],
        ];
    }
}
