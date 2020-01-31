<?php

namespace Repainter\Http\Controllers\Api;

use Repainter\Http\Controllers\Controller;

class LevelController extends Controller
{
    const IMAGES = [
        'smiley' => [
            'name' => 'Smiley Face',
        ],
        'unicorn' => [
            'name' => 'Unicorn',
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
                ],
            ];
        }

        return [
            'data' => $levels,
        ];
    }
}
