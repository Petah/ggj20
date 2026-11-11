<?php

namespace App\Http\Resources;

use App\Models\Round;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Round */
class RoundResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'type' => 'Round',
            'attributes' => [
                'levelId' => $this->level_id,
                'startedAt' => $this->started_at->toIso8601String(),
                'timeLimit' => $this->level()->timeLimit,
                'score' => $this->score === null ? null : (float) $this->score,
                'bonus' => $this->bonus,
                'finished' => $this->isFinished(),
                'original' => $this->isFinished() ? url("api/rounds/{$this->id}/original") : null,
            ],
        ];
    }
}
