<?php

namespace Repainter\Http\Controllers\Api;

use Illuminate\Http\Request;
use Repainter\Http\Controllers\Controller;

class LevelController extends Controller
{
    public function index()
    {
        return [
            'data' => [
                [
                    'id' => 'level-1',
                    'type' => 'Level',
                    'attributes' => [
                        'name' => 'Smiley Face',
                        'url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/SNice.svg/1200px-SNice.svg.png',
                        'width' => 100,
                        'height' => 100,
                    ],
                ],
                [
                    'id' => 'level-2',
                    'type' => 'Level',
                    'attributes' => [
                        'name' => 'Unicorn',
                        'url' => 'https://lh4.googleusercontent.com/proxy/j4VplCVeNxmkdrc9yc1WIo1p98oq1JqIyQd6R_E5FAkfVl6upJXxHnO-bOeNQshFl1sCjKlLraPWYcyeMXTJEgfFkipPe3LsO83oTU0fqYY',
                        'width' => 100,
                        'height' => 100,
                    ],
                ],
            ],
        ];
    }
}
