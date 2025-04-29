<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->query('query');

        $response = Http::get('https://api.themoviedb.org/3/search/movie', [
            'api_key' => config('services.tmdb.api_key'),
            'language' => 'ja-JP',
            'query' => $query,
        ]);

        $movies = $response->json();

        return view('movies.index', ['movies' => $movies['results'] ?? []]);
    }

    public function discover()
    {
        $response = Http::get('https://api.themoviedb.org/3/discover/movie', [
            'api_key' => config('services.tmdb.api_key'),
            'language' => 'ja-JP',
            'sort_by' => 'popularity.desc',
            'year' => 2024,
            'with_original_language' => 'ja', // 日本語映画だけ
            // 他にも genreやvote_averageなどを追加可能
        ]);

        $movies = $response->json();

        return view('movies.discover', ['movies' => $movies['results'] ?? []]);
    }
}
