<?php

namespace Repainter\Http\Controllers\Api;

use Repainter\Http\Controllers\Controller;

class LevelController extends Controller
{
    const IMAGES = [
        'smiley' => [
            'name' => 'Smiley Face',
            'colors' => [
                [
                    'name' => 'Yellow',
                    'hex' => '#f9db2c',
                ],
                [
                    'name' => 'Black',
                    'hex' => '#000000',
                ],
                [
                    'name' => 'White',
                    'hex' => '#ffffff',
                ],
            ],
        ],
        'unicorn' => [
            'name' => 'Unicorn',
            'colors' => [
                [
                    'hex' => '#650e41',
                ],
                [
                    'hex' => '#880a51',
                ],
                [
                    'hex' => '#bd3187',
                ],
                [
                    'hex' => '#d66f9e',
                ],
                [
                    'hex' => '#fff8ee',
                ],
                [
                    'hex' => '#e9ccc4',
                ],
                [
                    'hex' => '#d3d0ff',
                ],
                [
                    'hex' => '#fecbc7',
                ],
                [
                    'hex' => '#fef140',
                ],
                [
                    'hex' => '#fcbd3a',
                ],
            ],
        ],
    ];

    public function index()
    {
        $levels = [];
        foreach (static::IMAGES as $image => $data) {
            $repairImage = 'images/levels/' . $image . '-repair.png';
            [$width, $height] = getimagesize(public_path($repairImage));
            $levels[] = [
                'id' => uniqid('uid-'),
                'type' => 'Level',
                'attributes' => [
                    'name' => $data['name'],
                    'url' => $repairImage,
                    'width' => $width,
                    'height' => $height,
                    'colors' => $data['colors'],
                ],
            ];
        }

        return [
            'data' => $levels,
        ];
    }
}
