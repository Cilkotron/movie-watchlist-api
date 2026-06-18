<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition(): array
    {
        return [
            'imdb_id' => $this->faker->unique()->regexify('tt[0-9]{7}'),
            'title' => $this->faker->sentence(3),
            'year' => (string) $this->faker->numberBetween(1980, 2025),
            'genre' => $this->faker->randomElement([
                'Action',
                'Drama',
                'Comedy',
                'Thriller',
                'Sci-Fi',
            ]),
            'director' => $this->faker->name(),
            'actors' => $this->faker->name() . ', ' . $this->faker->name(),
            'plot' => $this->faker->paragraph(),
            'poster_url' => $this->faker->imageUrl(200, 300, 'movies'),
            'imdb_rating' => (string) $this->faker->randomFloat(1, 1, 10),
            'runtime' => $this->faker->numberBetween(80, 180) . ' min',
            'country' => $this->faker->country(),
        ];
    }
}
