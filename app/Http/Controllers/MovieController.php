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
        $language = $request->query('language', 'all');

        $movies = match ($category) {
            'top_rated' => $this->tmdbService->getTopRatedMovies($language),
            'now_playing' => $this->tmdbService->getNowPlayingMovies($language),
            default => $this->tmdbService->getPopularMovies($language),
        };

        return view('movies.discover', [
            'movies' => $movies,
            'currentCategory' => $category,
            'currentLanguage' => $language,
            'languages' => TMDBService::$languages
        ]);
    }
}
