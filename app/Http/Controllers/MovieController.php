<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected TMDBService $tmdbService;

    public function __construct(TMDBService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function discover(Request $request)
    {
        $category = $request->query('category', 'popular');

        $movies = match ($category) {
            'top_rated' => $this->tmdbService->getTopRatedMovies(),
            'now_playing' => $this->tmdbService->getNowPlayingMovies(),
            default => $this->tmdbService->getPopularMovies(),
        };

        return view('movies.discover', [
            'movies' => $movies,
            'currentCategory' => $category
        ]);

    }
}
