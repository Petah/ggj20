<?php

namespace App\Services;

use GdImage;
use InvalidArgumentException;

/**
 * Scores a repainted image against the original.
 *
 * All three images (the splattered "repair" image the player started from,
 * the player's painting, and the original) are shrunk to a small grid and
 * lightly blurred, so the score is about colour and shape rather than exact
 * pixels. The score is how much of the gap between the splattered image and
 * the original the player closed: 100 is a perfect repair, 0 is no better
 * than the splattered image, and below 0 is worse.
 */
class PaintingScorer
{
    /** Side length of the grid the images are compared on. */
    private const GRID = 100;

    /** Blur radius in grid cells (2 means a 5x5 neighbourhood). */
    private const BLUR = 2;

    /** A cell within this much (0-255, on every channel) of the splattered image counts as unchanged. */
    private const TOLERANCE = 24;

    public function score(GdImage $painting, GdImage $repair, GdImage $original): float
    {
        $repairGrid = $this->blurredGrid($repair);
        $paintingGrid = $this->blurredGrid($painting);
        $originalGrid = $this->blurredGrid($original);

        $cells = self::GRID * self::GRID;
        $repairScore = 0.0;
        $paintingScore = 0.0;

        foreach ($originalGrid as $i => $originalColour) {
            $repairColour = $repairGrid[$i];
            $paintingColour = $paintingGrid[$i];

            // A cell barely different from the splattered image hasn't been painted;
            // treat it as unchanged so stray noise isn't rewarded. (The 2020 version
            // checked each colour channel separately, which marked real repairs as
            // unpainted wherever the slime matched the original in one channel, so a
            // perfect repaint could score as low as 38.)
            if ($this->isUnchanged($paintingColour, $repairColour, $originalColour)) {
                $paintingColour = $repairColour;
            }

            for ($channel = 0; $channel < 3; $channel++) {
                $repairScore += $this->similarity($repairColour[$channel], $originalColour[$channel]) / 3 / $cells;
                $paintingScore += $this->similarity($paintingColour[$channel], $originalColour[$channel]) / 3 / $cells;
            }
        }

        if ($repairScore === 0.0 && $paintingScore === 0.0) {
            return 0.0;
        }

        if ($repairScore >= 100.0) {
            return $paintingScore;
        }

        return ($paintingScore - $repairScore) / (100.0 - $repairScore) * 100.0;
    }

    public static function imageFromDataUrl(string $dataUrl): GdImage
    {
        if (! preg_match('#^data:image/(png|jpeg);base64,#', $dataUrl, $match)) {
            throw new InvalidArgumentException('The painting must be a PNG or JPEG data URL.');
        }

        $binary = base64_decode(substr($dataUrl, strlen($match[0])), true);
        $image = $binary === false ? false : @imagecreatefromstring($binary);

        if (! $image instanceof GdImage) {
            throw new InvalidArgumentException('The painting could not be read as an image.');
        }

        return $image;
    }

    public static function imageFromFile(string $path): GdImage
    {
        $contents = is_file($path) ? file_get_contents($path) : false;
        $image = $contents === false ? false : @imagecreatefromstring($contents);

        if (! $image instanceof GdImage) {
            throw new InvalidArgumentException("Could not read image {$path}.");
        }

        return $image;
    }

    /**
     * Unchanged means close to the splattered image on every channel, and closer
     * to it than to the original (so a correct repair is never snapped back).
     *
     * @param  array{0: float, 1: float, 2: float}  $painting
     * @param  array{0: float, 1: float, 2: float}  $repair
     * @param  array{0: float, 1: float, 2: float}  $original
     */
    private function isUnchanged(array $painting, array $repair, array $original): bool
    {
        for ($channel = 0; $channel < 3; $channel++) {
            if (abs($painting[$channel] - $repair[$channel]) > self::TOLERANCE) {
                return false;
            }
        }

        return $this->distance($painting, $repair) < $this->distance($painting, $original);
    }

    /**
     * @param  array{0: float, 1: float, 2: float}  $a
     * @param  array{0: float, 1: float, 2: float}  $b
     */
    private function distance(array $a, array $b): float
    {
        return ($a[0] - $b[0]) ** 2 + ($a[1] - $b[1]) ** 2 + ($a[2] - $b[2]) ** 2;
    }

    /** 0-100: how close two channel values are. */
    private function similarity(float $a, float $b): float
    {
        return 100.0 - abs($a - $b) * 100.0 / 255.0;
    }

    /**
     * Shrink the image to the grid and average each cell with its neighbours.
     *
     * @return list<array{0: float, 1: float, 2: float}>
     */
    private function blurredGrid(GdImage $image): array
    {
        $size = self::GRID;
        $small = imagecreatetruecolor($size, $size);
        imagecolortransparent($small, (int) imagecolorallocate($small, 0, 0, 0));
        imagealphablending($small, false);
        imagecopyresampled($small, $image, 0, 0, 0, 0, $size, $size, imagesx($image), imagesy($image));

        $pixels = [];
        for ($y = 0; $y < $size; $y++) {
            for ($x = 0; $x < $size; $x++) {
                $colour = imagecolorat($small, $x, $y);
                $pixels[$y][$x] = [($colour >> 16) & 0xFF, ($colour >> 8) & 0xFF, $colour & 0xFF];
            }
        }

        $grid = [];
        for ($y = 0; $y < $size; $y++) {
            $top = max(0, $y - self::BLUR);
            $bottom = min($size - 1, $y + self::BLUR);
            for ($x = 0; $x < $size; $x++) {
                $left = max(0, $x - self::BLUR);
                $right = min($size - 1, $x + self::BLUR);
                $sum = [0, 0, 0];
                for ($ny = $top; $ny <= $bottom; $ny++) {
                    for ($nx = $left; $nx <= $right; $nx++) {
                        $sum[0] += $pixels[$ny][$nx][0];
                        $sum[1] += $pixels[$ny][$nx][1];
                        $sum[2] += $pixels[$ny][$nx][2];
                    }
                }
                $count = ($bottom - $top + 1) * ($right - $left + 1);
                $grid[] = [$sum[0] / $count, $sum[1] / $count, $sum[2] / $count];
            }
        }

        return $grid;
    }
}
