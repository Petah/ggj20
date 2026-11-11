<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(TestCase::class)->in('Unit');

/** A level's image as a base64 data URL, the way the game sends paintings. */
function paintingFromFile(string $path): string
{
    return 'data:image/png;base64,'.base64_encode((string) file_get_contents($path));
}

/** A plain white painting: the player wiped the canvas. */
function blankPainting(): string
{
    $image = imagecreatetruecolor(100, 100);
    imagefill($image, 0, 0, (int) imagecolorallocate($image, 255, 255, 255));
    ob_start();
    imagepng($image);

    return 'data:image/png;base64,'.base64_encode((string) ob_get_clean());
}
