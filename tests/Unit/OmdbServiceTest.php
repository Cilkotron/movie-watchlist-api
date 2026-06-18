<?php

namespace Tests\Unit;

use App\Services\OmdbService;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class OmdbServiceTest extends TestCase
{
    public function test_find_by_imdb_id_returns_movie_data(): void
    {
        Http::fake([
            '*' => Http::response([
                'Response' => 'True',
                'Title' => 'The Shawshank Redemption',
                'imdbID' => 'tt0111161',
            ], 200),
        ]);

        $service = new OmdbService();

        $result = $service->findByImdbId('tt0111161');

        $this->assertNotNull($result);
        $this->assertEquals('The Shawshank Redemption', $result['Title']);
        $this->assertEquals('tt0111161', $result['imdbID']);
    }
    public function test_find_by_imdb_id_returns_null_when_movie_not_found(): void
    {
        Http::fake([
            '*' => Http::response([
                'Response' => 'False',
                'Error' => 'Movie not found!',
            ], 200),
        ]);

        $service = new OmdbService();

        $result = $service->findByImdbId('invalid');

        $this->assertNull($result);
    }
    public function test_find_by_imdb_id_returns_null_on_api_failure(): void
    {
        Http::fake([
            '*' => Http::response([], 500),
        ]);

        $service = new OmdbService();

        $result = $service->findByImdbId('tt0111161');

        $this->assertNull($result);
    }
    public function test_find_by_title_returns_movie_data(): void
    {
        Http::fake([
            '*' => Http::response([
                'Response' => 'True',
                'Title' => 'Inception',
                'imdbID' => 'tt1375666',
            ], 200),
        ]);

        $service = new OmdbService();

        $result = $service->findByTitle('Inception');

        $this->assertNotNull($result);
        $this->assertEquals('Inception', $result['Title']);
    }
}
