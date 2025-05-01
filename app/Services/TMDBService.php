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

    public function discoverMovies(array $options = []): array
    {
        $defaultOptions = [
            'sort_by' => 'popularity.desc',
            'year' => date('Y'),
            'include_adult' => false,
            'with_original_language' => 'ja', // 日本語映画をデフォルトで表示
            'page' => 1, // 1ページ目
        ];

        $params = array_merge($defaultOptions, $options, [
            'api_key' => $this->apiKey,
            'language' => $this->language,
        ]);

        $response = Http::get("{$this->baseUrl}/discover/movie", $params);

        return $response->json()['results'] ?? [];
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
