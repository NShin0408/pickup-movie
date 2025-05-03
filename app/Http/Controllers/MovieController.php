<?php

namespace App\Http\Controllers;

use App\Services\TMDBService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected TMDBService $tmdbService;

    public function __construct(TMDBService $tmdbService)
    {
        $this->tmdbService = $tmdbService;
    }

    public function discover(Request $request): View|Application|Factory
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

    public function loadMore(Request $request): JsonResponse
    {
        $category = $request->query('category', 'popular');
        $language = $request->query('language', 'all');
        $streamingService = $request->query('streaming', 'all');
        $page = (int)$request->query('page', 2); // デフォルトは2ページ目から

        $movies = match ($category) {
            'top_rated' => $this->tmdbService->getTopRatedMovies($language, $streamingService, $page),
            'now_playing' => $this->tmdbService->getNowPlayingMovies($language, $streamingService, $page),
            default => $this->tmdbService->getPopularMovies($language, $streamingService, $page),
        };

        return response()->json([
            'movies' => $movies,
            'hasMore' => count($movies) == 20, // 20件取得できたら、まだ次のページがある
        ]);
    }
}
