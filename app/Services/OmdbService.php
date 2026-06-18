<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OmdbService
{
    private string $apiKey;
    private string $baseUrl = 'https://www.omdbapi.com';

    public function __construct()
    {
        $this->apiKey = config('services.omdb.key');
    }

    public function findByImdbId(string $imdbId): ?array
    {
        $response = Http::get($this->baseUrl, [
            'i'      => $imdbId,
            'apikey' => $this->apiKey,
        ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        if ($data['Response'] === 'False') {
            return null;
        }

        return $data;
    }

    public function findByTitle(string $title): ?array
    {
        $response = Http::get($this->baseUrl, [
            't'      => $title,
            'apikey' => $this->apiKey,
        ]);

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        if ($data['Response'] === 'False') {
            return null;
        }

        return $data;
    }
}
