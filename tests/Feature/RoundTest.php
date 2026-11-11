<?php

use App\Models\Round;

function startRound(string $level = 'smiley'): string
{
    return test()->postJson('/api/rounds', ['level_id' => $level])->assertCreated()->json('data.id');
}

it('lists levels without giving away the original paintings', function () {
    $response = $this->getJson('/api/levels')->assertOk();

    expect($response->json('data'))->not->toBeEmpty();
    foreach ($response->json('data') as $level) {
        expect($level['attributes'])->toHaveKeys(['name', 'timeLimit', 'colors', 'url'])
            ->not->toHaveKey('actual')
            ->and($level['attributes']['url'])->toEndWith('-repair.png');
    }
});

it('starts a round for a known level', function () {
    $this->postJson('/api/rounds', ['level_id' => 'smiley'])
        ->assertCreated()
        ->assertJsonPath('data.attributes.levelId', 'smiley')
        ->assertJsonPath('data.attributes.timeLimit', 60)
        ->assertJsonPath('data.attributes.finished', false)
        ->assertJsonPath('data.attributes.original', null);
});

it('refuses to start a round for an unknown level', function () {
    $this->postJson('/api/rounds', ['level_id' => 'pikachu'])->assertUnprocessable()->assertJsonValidationErrors('level_id');
});

it('scores paintings live and keeps the latest score', function () {
    $id = startRound();

    $this->postJson("/api/rounds/{$id}/painting", ['image' => paintingFromFile(public_path('images/levels/smiley-repair.png'))])
        ->assertOk()->assertJsonPath('data.attributes.score', 0);

    $this->postJson("/api/rounds/{$id}/painting", ['image' => paintingFromFile(resource_path('levels/smiley-actual.png'))])
        ->assertOk()->assertJsonPath('data.attributes.score', 100);

    expect((float) Round::find($id)->score)->toBe(100.0);
});

it('rejects a painting that is not an image', function (mixed $image) {
    $id = startRound();

    $this->postJson("/api/rounds/{$id}/painting", ['image' => $image])->assertUnprocessable()->assertJsonValidationErrors('image');
})->with([
    'missing' => null,
    'not a data url' => 'https://example.com/painting.png',
    'broken png' => 'data:image/png;base64,bm90IGFuIGltYWdl',
]);

it('stops scoring paintings once the time limit has passed', function () {
    $id = startRound();
    $this->travel(60 + Round::GRACE_SECONDS + 1)->seconds();

    $this->postJson("/api/rounds/{$id}/painting", ['image' => blankPainting()])->assertUnprocessable();
});

it('works out the time bonus on the server when a round finishes early', function () {
    $this->freezeSecond();
    $id = startRound();
    $this->postJson("/api/rounds/{$id}/painting", ['image' => paintingFromFile(resource_path('levels/smiley-actual.png'))]);
    $this->travel(30)->seconds();

    $this->postJson("/api/rounds/{$id}/finish")
        ->assertOk()
        ->assertJsonPath('data.attributes.finished', true)
        ->assertJsonPath('data.attributes.score', 100)
        ->assertJsonPath('data.attributes.bonus', 5000);
});

it('gives no bonus when the clock ran out', function () {
    $this->freezeSecond();
    $id = startRound();
    $this->travel(65)->seconds();

    $this->postJson("/api/rounds/{$id}/finish")->assertOk()->assertJsonPath('data.attributes.bonus', 0);
    expect(Round::find($id)->finished_at->equalTo(Round::find($id)->started_at->addSeconds(60)))->toBeTrue();
});

it('finishes a round only once and then stops scoring it', function () {
    $id = startRound();
    $this->postJson("/api/rounds/{$id}/finish")->assertOk();

    $this->postJson("/api/rounds/{$id}/finish")->assertConflict();
    $this->postJson("/api/rounds/{$id}/painting", ['image' => blankPainting()])->assertConflict();
});

it('only shows the original painting after the round is over', function () {
    $id = startRound();
    $this->get("/api/rounds/{$id}/original")->assertForbidden();

    $original = $this->postJson("/api/rounds/{$id}/finish")->json('data.attributes.original');

    expect($original)->toEndWith("/api/rounds/{$id}/original");
    $this->get("/api/rounds/{$id}/original")->assertOk()->assertHeader('content-type', 'image/png');
});

it('returns 404 for a round that does not exist', function () {
    $this->postJson('/api/rounds/01JZZZZZZZZZZZZZZZZZZZZZZZ/finish')->assertNotFound();
});
