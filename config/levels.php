<?php

/*
| Levels: each one is a painting to repair. The splattered version the player
| starts from is public/images/levels/{image}-repair.png; the original is
| resources/levels/{image}-actual.png, kept private until a round ends.
| "image" defaults to the level's key. "colors" is the palette offered to the player.
*/

return [
    // Original art made for the 2026 update.
    'kiwi' => [
        'name' => 'Kiwi',
        'time_limit' => 60,
        'colors' => ['#392b3e', '#946044', '#c99462', '#efbc59', '#fff1d6'],
    ],
    'pavlova' => [
        'name' => 'Pavlova',
        'time_limit' => 120,
        'colors' => ['#392b3e', '#fff1d6', '#ee5263', '#83bb51', '#64bfcb', '#e6b66a'],
    ],
    'rocket' => [
        'name' => 'Rocket',
        'time_limit' => 60,
        'colors' => ['#392b3e', '#fff1d6', '#ef6560', '#64bfcb', '#f39b48', '#f6cd58'],
    ],
    'sunset' => [
        'name' => 'Sunset',
        'time_limit' => 90,
        'colors' => ['#392b3e', '#f3aa88', '#f6cd58', '#a77fb0', '#d17b91', '#64bfcb'],
    ],
    'robot' => [
        'name' => 'Robot',
        'time_limit' => 90,
        'colors' => ['#392b3e', '#64bfcb', '#fff1d6', '#ef6560', '#f6cd58', '#c5e7eb'],
    ],

    // Levels from the 2020 jam.
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
