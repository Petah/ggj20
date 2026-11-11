<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ScorePaintingRequest;
use App\Http\Requests\StartRoundRequest;
use App\Http\Resources\RoundResource;
use App\Models\Round;
use App\Services\PaintingScorer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RoundController extends Controller
{
    /** Start the clock on a level. */
    public function store(StartRoundRequest $request): JsonResponse
    {
        $round = Round::create([
            'level_id' => $request->string('level_id')->toString(),
            'started_at' => now(),
        ]);

        return RoundResource::make($round)->response()->setStatusCode(201);
    }

    /**
     * Score the painting so far. The game calls this while the player paints
     * to drive the live score gauge; the latest score is the one that counts.
     */
    public function painting(ScorePaintingRequest $request, Round $round, PaintingScorer $scorer): RoundResource|JsonResponse
    {
        if ($error = $this->notPlayable($round)) {
            return $error;
        }

        try {
            $painting = PaintingScorer::imageFromDataUrl($request->string('image')->toString());
        } catch (InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage(), 'errors' => ['image' => [$e->getMessage()]]], 422);
        }

        $level = $round->level();
        $score = $scorer->score(
            $painting,
            PaintingScorer::imageFromFile($level->repairPath()),
            PaintingScorer::imageFromFile($level->originalPath()),
        );

        return DB::transaction(function () use ($round, $score) {
            $round = Round::query()->lockForUpdate()->findOrFail($round->id);

            // The round may have finished while this painting was being scored.
            if ($error = $this->notPlayable($round)) {
                return $error;
            }

            $round->recordScore($score);

            return RoundResource::make($round);
        });
    }

    /** Stop the clock. The score is the latest one recorded; the bonus uses the server's time. */
    public function finish(Round $round): RoundResource|JsonResponse
    {
        return DB::transaction(function () use ($round) {
            $round = Round::query()->lockForUpdate()->findOrFail($round->id);

            if ($round->isFinished()) {
                return response()->json(['message' => 'This round has already finished.'], 409);
            }

            $round->finish();

            return RoundResource::make($round);
        });
    }

    /** The original painting, shown for comparison once the round is over. */
    public function original(Round $round): BinaryFileResponse|JsonResponse
    {
        if (! $round->isFinished()) {
            return response()->json(['message' => 'Finish the round to see the original.'], 403);
        }

        return response()->file($round->level()->originalPath(), ['Cache-Control' => 'private, max-age=3600']);
    }

    private function notPlayable(Round $round): ?JsonResponse
    {
        if ($round->isFinished()) {
            return response()->json(['message' => 'This round has already finished.'], 409);
        }
        if ($round->hasExpired()) {
            return response()->json(['message' => 'This round ran out of time.'], 422);
        }

        return null;
    }
}
