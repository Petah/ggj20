<?php

namespace Database\Factories;

use App\Models\HighScore;
use App\Models\Round;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<HighScore> */
class HighScoreFactory extends Factory
{
    public function definition(): array
    {
        return [
            'round_id' => fn (array $attributes) => Round::create([
                'level_id' => $attributes['level_id'],
                'started_at' => now()->subMinute(),
            ])->id,
            'level_id' => 'smiley',
            'player_name' => fake()->firstName(),
            'score' => fake()->randomFloat(3, 0, 100),
            'bonus' => fake()->numberBetween(0, 10_000),
        ];
    }
}
