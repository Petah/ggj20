<?php

namespace Repainter\Http\Controllers\Api;

use Repainter\Http\Controllers\Controller;

class LevelController extends Controller
{
    const IMAGES = [
        'smiley' => [
            'image' => 'smiley',
            'name' => 'Smiley Face',
            'timeLimit' => 60,
            'debuffs' => [
                [
                    'startTime' => 10,
                    'duration' => 5,
                    'type' => 'drip',
                ],
            ],
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
        'glass' => [
            'image' => 'glass',
            'name' => 'Stained Glass',
            'timeLimit' => 300,
            'debuffs' => [
            ],
            'colors' => [
                [
                    'hex' => '#ff0000',
                ],
                [
                    'hex' => '#ff9900',
                ],
                [
                    'hex' => '#e06666',
                ],
                [
                    'hex' => '#f1c232',
                ],
                [
                    'hex' => '#d9d9d9',
                ],
                [
                    'hex' => '#00c910',
                ],
                [
                    'hex' => '#93c47d',
                ],
                [
                    'hex' => '#d9ead3',
                ],
                [
                    'hex' => '#000000',
                ],
            ],
        ],
        'skeletor' => [
            'image' => 'skeletor',
            'name' => 'Skeletor',
            'timeLimit' => 300,
            'debuffs' => [
            ],
            'colors' => [
                [
                    'hex' => '#f5c358',
                ],
                [
                    'hex' => '#ae7f35',
                ],
                [
                    'hex' => '#4b270b',
                ],
                [
                    'hex' => '#dfbc84',
                ],
                [
                    'hex' => '#474458',
                ],
                [
                    'hex' => '#553e79',
                ],
                [
                    'hex' => '#28194e',
                ],
                [
                    'hex' => '#748cce',
                ],
                [
                    'hex' => '#000000',
                ],
            ],
        ],
    ];

    public function index()
    {
        $levels = [];
        foreach (static::IMAGES as $id => $data) {
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
}
