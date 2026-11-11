<?php

namespace App\Http\Resources;

use App\Levels\Level;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Level */
class LevelResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        $size = @getimagesize($this->repairPath()) ?: [0, 0];

        return [
            'id' => $this->id,
            'type' => 'Level',
            'attributes' => [
                'name' => $this->name,
                'timeLimit' => $this->timeLimit,
                'colors' => array_map(fn (string $hex) => ['hex' => $hex], $this->colors),
                'url' => $this->repairUrl(),
                'width' => $size[0],
                'height' => $size[1],
            ],
        ];
    }
}
