<?php

namespace App\Levels;

use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** Reads the levels defined in config/levels.php. */
class LevelRepository
{
    /** @return Collection<string, Level> */
    public function all(): Collection
    {
        /** @var array<string, array{name: string, image?: string, time_limit: int, colors: list<string>}> $levels */
        $levels = config('levels', []);

        return collect($levels)->map(fn (array $level, string $id) => new Level(
            id: $id,
            name: $level['name'],
            image: $level['image'] ?? $id,
            timeLimit: $level['time_limit'],
            colors: $level['colors'],
        ));
    }

    public function find(string $id): ?Level
    {
        return $this->all()->get($id);
    }

    public function findOrFail(string $id): Level
    {
        return $this->find($id) ?? throw new NotFoundHttpException("Unknown level [{$id}].");
    }
}
