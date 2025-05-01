<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TMDBService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.themoviedb.org/3';
    protected string $language = 'ja-JP';

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
    }

    public function getPopularMovies(): array
    {
        $params = [
            'api_key' => $this->apiKey,
            'language' => $this->language,
            'page' => 1,
        ];

        $response = Http::get("{$this->baseUrl}/movie/popular", $params);

        return $response->json()['results'] ?? [];
    }

    public function getTopRatedMovies(): array
    {
        $params = [
            'api_key' => $this->apiKey,
            'language' => $this->language,
            'page' => 1,
        ];

        $response = Http::get("{$this->baseUrl}/movie/top_rated", $params);

        return $response->json()['results'] ?? [];
    }

    public function getNowPlayingMovies(): array
    {
        $params = [
            'api_key' => $this->apiKey,
            'language' => $this->language,
            'page' => 1,
        ];

        $response = Http::get("{$this->baseUrl}/movie/now_playing", $params);

        return $response->json()['results'] ?? [];
    }
}
