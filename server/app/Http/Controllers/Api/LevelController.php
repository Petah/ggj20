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
            $actualImage = 'images/levels/' . $data['image'] . '-censored.png';
            if (!is_file(public_path($actualImage))) {
                $actualImage = 'images/levels/' . $data['image'] . '-actual.png';
            }
            [$width, $height] = getimagesize(public_path($repairImage));
            $levels[] = [
                'id' => $id,
                'type' => 'Level',
                'attributes' => array_merge($data, [
                    'url' => $repairImage,
                    'actual' => $actualImage,
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
        $images = [
            'smiley' => [
                'image' => 'smiley',
                'name' => 'Smiley Face',
                'timeLimit' => 60,
                'debuffs' => [],
                'colors' => [
                    ['hex' => '#f9db2c'],
                    ['hex' => '#000000'],
                    ['hex' => '#ffffff'],
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
                    ['hex' => '#650e41'],
                    ['hex' => '#880a51'],
                    ['hex' => '#bd3187'],
                    ['hex' => '#d66f9e'],
                    ['hex' => '#fff8ee'],
                    ['hex' => '#e9ccc4'],
                    ['hex' => '#fecbc7'],
                    ['hex' => '#d3d0ff'],
                    ['hex' => '#fef140'],
                    ['hex' => '#fcbd3a'],
                    ['hex' => '#ffffff'],
                ],
            ],
            'pikachu' => [
                'image' => 'pikachu',
                'name' => 'Pikachu',
                'timeLimit' => 100,
                'debuffs' => [],
                'colors' => [
                    ['hex' => '#230905'],
                    ['hex' => '#5E2009'],
                    ['hex' => '#C05119'],
                    ['hex' => '#E9C52C'],
                    ['hex' => '#FFFFFF'],
                ],
            ],
            'glass' => [
                'image' => 'glass',
                'name' => 'Stained Glass',
                'timeLimit' => 90,
                'debuffs' => [],
                'colors' => [
                    ['hex' => '#ff0000'],
                    ['hex' => '#ff9900'],
                    ['hex' => '#e06666'],
                    ['hex' => '#f1c232'],
                    ['hex' => '#d9d9d9'],
                    ['hex' => '#00c910'],
                    ['hex' => '#93c47d'],
                    ['hex' => '#d9ead3'],
                    ['hex' => '#000000'],
                ],
            ],
            'pingu' => [
                'image' => 'pingu',
                'name' => 'Pingu',
                'timeLimit' => 180,
                'debuffs' => [],
                'colors' => [
                    ['hex' => '#212239'],
                    ['hex' => '#bd2e39'],
                    ['hex' => '#f0b958'],
                    ['hex' => '#edded1'],
                    ['hex' => '#c9c3c3'],
                ],
            ],
            'skeletor' => [
                'image' => 'skeletor',
                'name' => 'Skeletor',
                'timeLimit' => 120,
                'debuffs' => [],
                'colors' => [
                    ['hex' => '#f5c358'],
                    ['hex' => '#ae7f35'],
                    ['hex' => '#4b270b'],
                    ['hex' => '#dfbc84'],
                    ['hex' => '#474458'],
                    ['hex' => '#553e79'],
                    ['hex' => '#28194e'],
                    ['hex' => '#748cce'],
                    ['hex' => '#000000'],
                ],
            ],
            'fry' => [
                'image' => 'fry',
                'name' => 'Suspicious fry',
                'timeLimit' => 120,
                'debuffs' => [],
                'colors' => [
                    ['hex' => '#ffb590'],
                    ['hex' => '#ed7119'],
                    ['hex' => '#ad272b'],
                    ['hex' => '#446181'],
                    ['hex' => '#3c4e69'],
                    ['hex' => '#2f5e6f'],
                    ['hex' => '#193236'],
                    ['hex' => '#ffffff'],
                    ['hex' => '#000000'],
                ],
            ],
            'dickbut' => [
                'image' => 'dickbut',
                'name' => 'D**kbut',
                'timeLimit' => 60,
                'debuffs' => [],
                'colors' => [
                    ['hex' => '#000000'],
                    ['hex' => '#ffffff'],
                ],
            ],
            'moon' => [
                'image' => 'moon',
                'name' => 'Moon',
                'timeLimit' => 60,
                'debuffs' => [],
                'colors' => [
                    ['hex' => '#d7cbca'],
                    ['hex' => '#998f8c'],
                    ['hex' => '#635b5a'],
                    ['hex' => '#000000'],
                ],
            ],
            'nz' => [
                'image' => 'nz',
                'name' => 'New Zealand',
                'timeLimit' => 60,
                'debuffs' => [],
                'colors' => [
                    ['hex' => '#657151'],
                    ['hex' => '#172642'],
                ],
            ],
            'monalisa' => [
                'image' => 'monalisa',
                'name' => 'Mona Lisa',
                'timeLimit' => 120,
                'debuffs' => [],
                'colors' => [
                    ['hex' => '#bd8545'],
                    ['hex' => '#5f4b31'],
                    ['hex' => '#837e5d'],
                    ['hex' => '#2d1f15'],
                ],
            ],
            'behold-then-man' => [
                'image' => 'behold-the-man',
                'name' => 'Behold the Man',
                'timeLimit' => 60,
                'debuffs' => [],
                'colors' => [
                    ['hex' => '#89837d'],
                    ['hex' => '#716757'],
                    ['hex' => '#4d4c3f'],
                    ['hex' => '#47484b'],
                    ['hex' => '#352a27'],
                    ['hex' => '#a58d61'],
                    ['hex' => '#77633e'],
                    ['hex' => '#784d32'],
                    ['hex' => '#5e4c2e'],
                    ['hex' => '#352a27'],
                    ['hex' => '#2a1314'],
                    ['hex' => '#1c1415'],
                    ['hex' => '#000000'],
                    ['hex' => '#ffffff'],
                ],
            ],
        ];
        uksort($images, function () {
            return rand() > rand();
        });
        return $images;
    }
}
