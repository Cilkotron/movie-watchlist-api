<?php

namespace Database\Factories;

use App\Models\WatchlistItem;
use App\Models\User;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class WatchlistItemFactory extends Factory
{
    protected $model = WatchlistItem::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'movie_id' => Movie::factory(),
            'status' => $this->faker->randomElement([
                'to_watch',
                'watching',
                'watched',
            ]),
            'user_rating' => $this->faker->numberBetween(1, 10),
            'notes' => $this->faker->sentence(),
            'watched_at' => null,
        ];
    }
}
