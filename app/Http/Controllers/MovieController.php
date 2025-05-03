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

    /**
     * 映画一覧を表示
     */
    public function discover(Request $request): View|Application|Factory
    {
        $category = $request->query('category', 'popular');
        $language = $request->query('language', 'all');
        $streamingService = $request->query('streaming', 'all');

        $movies = $this->getMoviesByCategory($category, $language, $streamingService);

        return view('movies.discover', [
            'movies' => $movies,
            'currentCategory' => $category,
            'currentLanguage' => $language,
            'currentStreaming' => $streamingService,
            'languages' => TMDBService::$languages,
            'streamingServices' => TMDBService::$streamingServices
        ]);
    }

    /**
     * 追加の映画を読み込む（無限スクロール用）
     */
    public function loadMore(Request $request): JsonResponse
    {
        $category = $request->query('category', 'popular');
        $language = $request->query('language', 'all');
        $streamingService = $request->query('streaming', 'all');
        $page = (int)$request->query('page', 2); // デフォルトは2ページ目から

        $movies = $this->getMoviesByCategory($category, $language, $streamingService, $page);

        return response()->json([
            'movies' => $movies,
            'hasMore' => count($movies) == 20, // 20件取得できたら、まだ次のページがある
        ]);
    }

    /**
     * 映画詳細ページを表示
     */
    public function show(int $movieId): View|Application|Factory
    {
        $movie = $this->tmdbService->getMovieDetails($movieId);
        $trailer = $this->tmdbService->getOfficialTrailer($movieId);

        $trailerUrl = null;
        if ($trailer) {
            $trailerUrl = $this->tmdbService->getYoutubeEmbedUrl($trailer['key']);
        }

        return view('movies.show', [
            'movie' => $movie,
            'trailerUrl' => $trailerUrl,
        ]);
    }

    /**
     * カテゴリに基づいて映画を取得する
     */
    private function getMoviesByCategory(string $category, ?string $language, ?string $streamingService, int $page = 1): array
    {
        return match ($category) {
            'top_rated' => $this->tmdbService->getTopRatedMovies($language, $streamingService, $page),
            'now_playing' => $this->tmdbService->getNowPlayingMovies($language, $streamingService, $page),
            default => $this->tmdbService->getPopularMovies($language, $streamingService, $page),
        };
    }
}
