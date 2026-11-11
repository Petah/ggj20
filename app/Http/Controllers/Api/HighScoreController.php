<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SubmitHighScoreRequest;
use App\Http\Resources\HighScoreResource;
use App\Levels\LevelRepository;
use App\Models\HighScore;
use App\Models\Round;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class HighScoreController extends Controller
{
    /** Number of scores shown for each level. */
    public const TABLE_SIZE = 10;

    public function index(string $level, LevelRepository $levels): AnonymousResourceCollection
    {
        return $this->table($levels->findOrFail($level)->id);
    }

    /** Put a scored round on the table. The score comes from the round, never from the request. */
    public function store(SubmitHighScoreRequest $request, Round $round): AnonymousResourceCollection|JsonResponse
    {
        return DB::transaction(function () use ($request, $round) {
            $round = Round::query()->lockForUpdate()->findOrFail($round->id);

            if (! $round->acceptsHighScore()) {
                return response()->json(['message' => 'This round can\'t be added to the high scores.'], 409);
            }

            $highScore = $round->highScore()->create([
                'level_id' => $round->level_id,
                'player_name' => $request->string('player_name')->toString(),
                'score' => $round->score,
                'bonus' => $round->bonus ?? 0,
            ]);

            return $this->table($round->level_id, $highScore);
        });
    }

    private function table(string $levelId, ?HighScore $new = null): AnonymousResourceCollection
    {
        $scores = HighScore::query()->forLevel($levelId)->limit(self::TABLE_SIZE)->get();

        if ($new) {
            // Show the player's own score even when it didn't make the top ten.
            if (! $scores->contains('id', $new->id)) {
                $scores->push($new);
            }
            request()->attributes->set(HighScoreResource::NEW_ID, $new->id);
        }

        return HighScoreResource::collection($scores);
    }
}
