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
}
