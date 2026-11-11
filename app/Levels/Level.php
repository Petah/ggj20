<?php

namespace App\Levels;

/**
 * One painting to repair. The splattered image the player starts from is
 * public (public/images/levels/{image}-repair.png). The original is kept out
 * of public/ (resources/levels/{image}-actual.png) and only shown once a
 * round is over, so it can't simply be submitted as a "painting".
 */
final readonly class Level
{
    /**
     * @param  list<string>  $colors  Palette offered to the player, as hex strings.
     */
    public function __construct(
        public string $id,
        public string $name,
        public string $image,
        public int $timeLimit,
        public array $colors,
    ) {}

    public function repairUrl(): string
    {
        return "images/levels/{$this->image}-repair.png";
    }

    public function originalPath(): string
    {
        return resource_path("levels/{$this->image}-actual.png");
    }

    public function repairPath(): string
    {
        return public_path($this->repairUrl());
    }
}
