<?php

namespace App\Models;

use Database\Factories\HighScoreFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $round_id
 * @property string $level_id
 * @property string $player_name
 * @property string $score
 * @property int $bonus
 * @property Carbon $created_at
 */
class HighScore extends Model
{
    /** @use HasFactory<HighScoreFactory> */
    use HasFactory;

    protected $fillable = ['round_id', 'level_id', 'player_name', 'score', 'bonus'];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:3',
            'bonus' => 'integer',
        ];
    }

    /** @return BelongsTo<Round, $this> */
    public function round(): BelongsTo
    {
        return $this->belongsTo(Round::class);
    }

    /** The game's displayed score: accuracy (0-100) x 1000, plus the time bonus. */
    public function total(): int
    {
        $score = (float) $this->score;

        return $score < 0 ? (int) round($score * 100) : (int) round($score * 1000 + $this->bonus);
    }

    /**
     * @param  Builder<HighScore>  $query
     * @return Builder<HighScore>
     */
    public function scopeForLevel(Builder $query, string $levelId): Builder
    {
        return $query->where('level_id', $levelId)
            ->orderByRaw('(score * 1000 + bonus) desc')
            ->orderBy('created_at');
    }
}
