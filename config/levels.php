<?php

/*
| Levels: each one is a painting to repair. Images are in public/images/levels:
| "{image}-actual.png" is the original and "{image}-repair.png" is the splattered
| version the player starts from. "image" defaults to the level's key.
| "colors" is the palette offered to the player.
*/

return [
    'smiley' => [
        'name' => 'Smiley Face',
        'time_limit' => 60,
        'colors' => ['#f9db2c', '#000000', '#ffffff'],
    ],
    'unicorn' => [
        'name' => 'Unicorn',
        'time_limit' => 180,
        'colors' => ['#650e41', '#880a51', '#bd3187', '#d66f9e', '#fff8ee', '#e9ccc4', '#fecbc7', '#d3d0ff', '#fef140', '#fcbd3a', '#ffffff'],
    ],
    'glass' => [
        'name' => 'Stained Glass',
        'time_limit' => 90,
        'colors' => ['#ff0000', '#ff9900', '#e06666', '#f1c232', '#d9d9d9', '#00c910', '#93c47d', '#d9ead3', '#000000'],
    ],
    'moon' => [
        'name' => 'Moon',
        'time_limit' => 60,
        'colors' => ['#d7cbca', '#998f8c', '#635b5a', '#000000'],
    ],
    'nz' => [
        'name' => 'New Zealand',
        'time_limit' => 60,
        'colors' => ['#657151', '#172642'],
    ],
    'monalisa' => [
        'name' => 'Mona Lisa',
        'time_limit' => 120,
        'colors' => ['#bd8545', '#5f4b31', '#837e5d', '#2d1f15'],
    ],
    'behold-the-man' => [
        'name' => 'Behold the Man',
        'time_limit' => 60,
        'colors' => ['#89837d', '#716757', '#4d4c3f', '#47484b', '#352a27', '#a58d61', '#77633e', '#784d32', '#5e4c2e', '#2a1314', '#1c1415', '#000000', '#ffffff'],
    ],
];
