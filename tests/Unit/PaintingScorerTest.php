<?php

use App\Levels\LevelRepository;
use App\Services\PaintingScorer;

// Read straight from the config file: datasets are built before the app boots.
dataset('levels', array_keys(require __DIR__.'/../../config/levels.php'));

beforeEach(function () {
    $this->scorer = new PaintingScorer;
});

it('gives a perfect repair 100', function (string $levelId) {
    $level = app(LevelRepository::class)->findOrFail($levelId);
    $original = PaintingScorer::imageFromFile($level->originalPath());
    $repair = PaintingScorer::imageFromFile($level->repairPath());

    expect($this->scorer->score($original, $repair, $original))->toEqualWithDelta(100.0, 0.001);
})->with('levels');

it('gives an untouched painting 0', function (string $levelId) {
    $level = app(LevelRepository::class)->findOrFail($levelId);
    $repair = PaintingScorer::imageFromFile($level->repairPath());

    expect($this->scorer->score($repair, $repair, PaintingScorer::imageFromFile($level->originalPath())))->toEqualWithDelta(0.0, 0.001);
})->with('levels');

it('scores a wiped canvas below zero', function () {
    $level = app(LevelRepository::class)->findOrFail('smiley');
    $blank = PaintingScorer::imageFromDataUrl(blankPainting());

    expect($this->scorer->score(
        $blank,
        PaintingScorer::imageFromFile($level->repairPath()),
        PaintingScorer::imageFromFile($level->originalPath()),
    ))->toBeLessThan(0.0);
});

it('rewards a partial repair with a score between 0 and 100', function () {
    $level = app(LevelRepository::class)->findOrFail('smiley');
    $original = PaintingScorer::imageFromFile($level->originalPath());
    $repair = PaintingScorer::imageFromFile($level->repairPath());

    // Repair the left half only.
    $half = imagecreatetruecolor(imagesx($repair), imagesy($repair));
    imagecopy($half, $repair, 0, 0, 0, 0, imagesx($repair), imagesy($repair));
    imagecopy($half, $original, 0, 0, 0, 0, intdiv(imagesx($original), 2), imagesy($original));

    expect($this->scorer->score($half, $repair, $original))->toBeGreaterThan(0.0)->toBeLessThan(100.0);
});

it('rejects data that is not a PNG or JPEG data URL', function (string $input) {
    PaintingScorer::imageFromDataUrl($input);
})->with([
    'plain text' => 'hello',
    'gif' => 'data:image/gif;base64,R0lGODlhAQABAAAAACw=',
    'broken png' => 'data:image/png;base64,bm90IGFuIGltYWdl',
])->throws(InvalidArgumentException::class);

it('has images and a palette for every level', function (string $levelId) {
    $level = app(LevelRepository::class)->findOrFail($levelId);

    expect($level->originalPath())->toBeFile()
        ->and($level->repairPath())->toBeFile()
        ->and(array_slice((array) getimagesize($level->originalPath()), 0, 2))->toEqual(array_slice((array) getimagesize($level->repairPath()), 0, 2))
        ->and($level->colors)->not->toBeEmpty()->each->toMatch('/^#[0-9a-f]{6}$/i');
})->with('levels');

it('keeps original paintings out of the public folder', function (string $levelId) {
    $level = app(LevelRepository::class)->findOrFail($levelId);

    expect($level->originalPath())->not->toStartWith(public_path());
})->with('levels');
