<?php

namespace Repainter\Http\Controllers\Api;

use Repainter\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function compare(Request $request)
    {
        var_dump($request);
        return [
            'data' => [
                'id' => 'some id',
                'type' => 'Comparison',
                'attributes' => [
                    'percent' => 23,
                ],
            ],
        ];
    }
}
