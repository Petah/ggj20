<?php

namespace Repainter\Http\Controllers\Api;

use Repainter\Http\Controllers\Controller;

class LevelController extends Controller
{
    public function index()
    {
        $levels = [];
        foreach (static::getImages() as $id => $data) {
            $repairImage = 'images/levels/' . $data['image'] . '-repair.png';
            [$width, $height] = getimagesize(public_path($repairImage));
            $levels[] = [
                'id' => $id,
                'type' => 'Level',
                'attributes' => array_merge($data, [
                    'url' => $repairImage,
                    'width' => $width,
                    'height' => $height,
                ]),
            ];
        }

        return [
            'data' => $levels,
        ];
    }

    public static function getImages()
    {
        $darkenAmount = -30;
        return [
            'smiley' => [
                'image' => 'smiley',
                'name' => 'Smiley Face',
                'timeLimit' => 5,
                'debuffs' => [
                    [
                        'startTime' => 10,
                        'duration' => 5,
                        'type' => 'drip',
                    ],
                ],
                'colors' => [
                    ['hex' => '#f9db2c', 'border' => static::adjustBrightness('#f9db2c', $darkenAmount)],
                    ['hex' => '#000000', 'border' => static::adjustBrightness('#000000', $darkenAmount)],
                    ['hex' => '#ffffff', 'border' => static::adjustBrightness('#ffffff', $darkenAmount)],
                ],
            ],
            'unicorn' => [
                'image' => 'unicorn',
                'name' => 'Unicorn',
                'timeLimit' => 180,
                'debuffs' => [
                    [
                        'startTime' => 60,
                        'duration' => 10,
                        'type' => 'enlarge',
                    ],
                    [
                        'startTime' => 120,
                        'duration' => 10,
                        'type' => 'drip',
                    ],
                ],
                'colors' => [
                    ['hex' => '#650e41', 'border' => static::adjustBrightness('#650e41', $darkenAmount)],
                    ['hex' => '#880a51', 'border' => static::adjustBrightness('#880a51', $darkenAmount)],
                    ['hex' => '#bd3187', 'border' => static::adjustBrightness('#bd3187', $darkenAmount)],
                    ['hex' => '#d66f9e', 'border' => static::adjustBrightness('#d66f9e', $darkenAmount)],
                    ['hex' => '#fff8ee', 'border' => static::adjustBrightness('#fff8ee', $darkenAmount)],
                    ['hex' => '#e9ccc4', 'border' => static::adjustBrightness('#e9ccc4', $darkenAmount)],
                    ['hex' => '#fecbc7', 'border' => static::adjustBrightness('#fecbc7', $darkenAmount)],
                    ['hex' => '#d3d0ff', 'border' => static::adjustBrightness('#d3d0ff', $darkenAmount)],
                    ['hex' => '#fef140', 'border' => static::adjustBrightness('#fef140', $darkenAmount)],
                    ['hex' => '#fcbd3a', 'border' => static::adjustBrightness('#fcbd3a', $darkenAmount)],
                    ['hex' => '#ffffff', 'border' => static::adjustBrightness('#ffffff', $darkenAmount)],
                ],
            ],
        ];
    }

    private static function adjustBrightness($hex, $steps)
    {
        return '#111111';
        // Steps should be between -255 and 255. Negative = darker, positive = lighter
        $steps = max(-255, min(255, $steps));

        // Normalize into a six character long hex string
        $hex = str_replace('#', '', $hex);
        if (strlen($hex) == 3) {
            $hex = str_repeat(substr($hex, 0, 1), 2) . str_repeat(substr($hex, 1, 1), 2) . str_repeat(substr($hex, 2, 1), 2);
        }

        // Split into three parts: R, G and B
        $color_parts = str_split($hex, 2);
        $return = '#';

        foreach ($color_parts as $color) {
            $color   = hexdec($color); // Convert to decimal
            $color   = max(0, min(255, $color + $steps)); // Adjust color
            $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
        }

        return $return;
    }
}
