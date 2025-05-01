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
        $streamingService = $request->query('streaming', 'all');

        $movies = match ($category) {
            'top_rated' => $this->tmdbService->getTopRatedMovies($language, $streamingService),
            'now_playing' => $this->tmdbService->getNowPlayingMovies($language, $streamingService),
            default => $this->tmdbService->getPopularMovies($language, $streamingService),
        };

        return view('movies.discover', [
            'movies' => $movies,
            'currentCategory' => $category,
            'currentLanguage' => $language,
            'currentStreaming' => $streamingService,
            'languages' => TMDBService::$languages,
            'streamingServices' => TMDBService::$streamingServices
        ]);
    }

}
