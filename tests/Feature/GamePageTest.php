<?php

it('serves the game page', function () {
    $this->withoutVite()->get('/')->assertOk()->assertSee('<g-game>', false)->assertSee('Repainter');
});

it('rate limits starting rounds', function () {
    foreach (range(1, 30) as $i) {
        $this->postJson('/api/rounds', ['level_id' => 'smiley'])->assertCreated();
    }

    $this->postJson('/api/rounds', ['level_id' => 'smiley'])->assertTooManyRequests();
});
