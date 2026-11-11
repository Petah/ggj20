<?php

use App\Models\HighScore;
use App\Models\Round;

function finishedRound(float $score = 80, string $level = 'smiley'): Round
{
    $round = Round::create(['level_id' => $level, 'started_at' => now()->subSeconds(20)]);
    $round->recordScore($score);
    $round->finish();

    return $round->fresh();
}

it('adds a finished round to the high scores, using the score the server recorded', function () {
    $round = finishedRound(80);

    $this->postJson("/api/rounds/{$round->id}/high-score", [
        'player_name' => '  Dave  ',
        'score' => 100, // ignored: the score comes from the round
        'bonus' => 10000,
    ])
        ->assertOk()
        ->assertJsonPath('data.0.attributes.playerName', 'Dave')
        ->assertJsonPath('data.0.attributes.score', 80)
        ->assertJsonPath('data.0.attributes.bonus', $round->bonus)
        ->assertJsonPath('data.0.attributes.total', 80_000 + $round->bonus)
        ->assertJsonPath('data.0.attributes.new', true);
});

it('refuses a round that has not finished', function () {
    $round = Round::create(['level_id' => 'smiley', 'started_at' => now()]);

    $this->postJson("/api/rounds/{$round->id}/high-score", ['player_name' => 'Dave'])->assertConflict();
});

it('accepts each round only once', function () {
    $round = finishedRound();
    $this->postJson("/api/rounds/{$round->id}/high-score", ['player_name' => 'Dave'])->assertOk();

    $this->postJson("/api/rounds/{$round->id}/high-score", ['player_name' => 'Dave again'])->assertConflict();
    expect(HighScore::count())->toBe(1);
});

it('refuses a round finished too long ago', function () {
    $round = finishedRound();
    $this->travel(Round::HIGH_SCORE_WINDOW_MINUTES + 1)->minutes();

    $this->postJson("/api/rounds/{$round->id}/high-score", ['player_name' => 'Dave'])->assertConflict();
});

it('validates the player name', function (mixed $name) {
    $round = finishedRound();

    $this->postJson("/api/rounds/{$round->id}/high-score", ['player_name' => $name])->assertUnprocessable()->assertJsonValidationErrors('player_name');
})->with([
    'missing' => null,
    'only spaces' => '   ',
    'too long' => str_repeat('a', 25),
]);

it('lists the top ten for a level, best first', function () {
    foreach (range(1, 12) as $i) {
        HighScore::factory()->create(['level_id' => 'smiley', 'score' => $i * 5, 'bonus' => 0]);
    }
    HighScore::factory()->create(['level_id' => 'moon', 'score' => 99, 'bonus' => 0]);

    $scores = $this->getJson('/api/levels/smiley/high-scores')->assertOk()->json('data');

    expect($scores)->toHaveCount(10)
        ->and(array_column(array_column($scores, 'attributes'), 'score'))->toEqual([60, 55, 50, 45, 40, 35, 30, 25, 20, 15]);
});

it('still shows a new score that did not make the top ten', function () {
    HighScore::factory()->count(10)->create(['level_id' => 'smiley', 'score' => 99, 'bonus' => 0]);
    $round = finishedRound(10);

    $scores = $this->postJson("/api/rounds/{$round->id}/high-score", ['player_name' => 'Dave'])->json('data');

    expect($scores)->toHaveCount(11)
        ->and(end($scores)['attributes'])->toMatchArray(['playerName' => 'Dave', 'new' => true]);
});

it('returns 404 for the high scores of an unknown level', function () {
    $this->getJson('/api/levels/pikachu/high-scores')->assertNotFound();
});

it('prunes old rounds that never made the table, but keeps ones that did', function () {
    $abandoned = Round::create(['level_id' => 'smiley', 'started_at' => now()]);
    $kept = finishedRound();
    HighScore::factory()->create(['round_id' => $kept->id]);
    $this->travel(2)->days();

    $this->artisan('model:prune', ['--model' => Round::class])->assertSuccessful();

    expect(Round::find($abandoned->id))->toBeNull()
        ->and(Round::find($kept->id))->not->toBeNull();
});
