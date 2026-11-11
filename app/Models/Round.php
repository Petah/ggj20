<?php

namespace App\Models;

use App\Levels\Level;
use App\Levels\LevelRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * One attempt at a level. The server records when it started and scores it,
 * so the score and time bonus can't be made up by the browser.
 *
 * @property string $id
 * @property string $level_id
 * @property Carbon $started_at
 * @property Carbon|null $finished_at
 * @property string|null $score
 * @property int|null $bonus
 */
class Round extends Model
{
    use HasUlids, Prunable;

    /** Extra seconds allowed after the time limit, for slow uploads. */
    public const GRACE_SECONDS = 30;

    /** How long after finishing a round its score can still be submitted. */
    public const HIGH_SCORE_WINDOW_MINUTES = 15;

    /** Bonus for finishing instantly; it shrinks to 0 as the clock runs down. */
    public const MAX_BONUS = 10_000;

    protected $fillable = ['level_id', 'started_at'];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'finished_at' => 'datetime',
            'score' => 'decimal:3',
            'bonus' => 'integer',
        ];
    }

    /** @return HasOne<HighScore, $this> */
    public function highScore(): HasOne
    {
        return $this->hasOne(HighScore::class);
    }

    public function level(): Level
    {
        return app(LevelRepository::class)->findOrFail($this->level_id);
    }

    public function isFinished(): bool
    {
        return $this->finished_at !== null;
    }

    public function hasExpired(?Carbon $now = null): bool
    {
        $deadline = $this->started_at->copy()->addSeconds($this->level()->timeLimit + self::GRACE_SECONDS);

        return ($now ?? now())->greaterThan($deadline);
    }

    /** Time bonus, from the server's own clock: same formula the game always used. */
    public function bonusAt(Carbon $finishedAt): int
    {
        $limit = $this->level()->timeLimit;
        $remaining = $limit - $this->started_at->diffInMilliseconds($finishedAt) / 1000;

        return $remaining >= 2 ? (int) round(self::MAX_BONUS * $remaining / $limit) : 0;
    }

    /** The latest live score while the player is painting. */
    public function recordScore(float $score): void
    {
        $this->forceFill(['score' => round($score, 3)])->save();
    }

    /** Stop the clock: the latest score stands, and the bonus comes from the server's time. */
    public function finish(?Carbon $now = null): void
    {
        $now ??= now();
        $deadline = $this->started_at->copy()->addSeconds($this->level()->timeLimit);
        $finishedAt = $now->greaterThan($deadline) ? $deadline : $now;

        $this->forceFill([
            'finished_at' => $finishedAt,
            'score' => $this->score ?? 0,
            'bonus' => $this->bonusAt($finishedAt),
        ])->save();
    }

    public function acceptsHighScore(?Carbon $now = null): bool
    {
        if ($this->finished_at === null || $this->highScore()->exists()) {
            return false;
        }

        return ($now ?? now())->lessThanOrEqualTo($this->finished_at->copy()->addMinutes(self::HIGH_SCORE_WINDOW_MINUTES));
    }

    /**
     * Old rounds nobody put on the high score table.
     *
     * @return Builder<static>
     */
    public function prunable(): Builder
    {
        return static::query()
            ->where('created_at', '<', now()->subDay())
            ->whereDoesntHave('highScore');
    }
}
