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

        $this->validateParams($category, $language, $streamingService);

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

        $this->validateParams($category, $language, $streamingService);

        $movies = $this->getMoviesByCategory($category, $language, $streamingService, $page);

        return response()->json([
            'movies' => $movies,
            'hasMore' => count($movies) == 20, // 20件取得できたら、まだ次のページがある
        ]);
    }

    public function show($id): View|Application|Factory
    {
        // TMDBService のインスタンスを取得
        $tmdbService = app(TMDBService::class);

        // 映画の詳細情報を取得
        $movie = $tmdbService->getMovieDetails($id);

        // トレーラーURLの取得
        $trailerUrl = null;
        $directorMovies = [];
        $director = null;

        if (!empty($movie)) {
            $trailer = $tmdbService->getOfficialTrailer($id);
            if (!empty($trailer)) {
                $trailerUrl = $tmdbService->getYoutubeEmbedUrl($trailer['key']);
            }

            // 監督情報を取得
            $director = $tmdbService->getDirectorFromMovie($movie);

            // 監督の他の作品を取得
            if (!empty($director)) {
                $directorMovies = $tmdbService->getDirectorMovies($director['id'], $movie['id']);
            }
        }

        return view('movies.show', compact('movie', 'trailerUrl', 'director', 'directorMovies'));
    }

    /**
     * カテゴリに基づいて映画を取得する
     */
    private function getMoviesByCategory(string $category, string $language, string $streamingService, int $page = 1): array
    {
        return match ($category) {
            'top_rated' => $this->tmdbService->getTopRatedMovies($language, $streamingService, $page),
            'now_playing' => $this->tmdbService->getNowPlayingMovies($language, $streamingService, $page),
            default => $this->tmdbService->getPopularMovies($language, $streamingService, $page),
        };
    }

    private function validateParams(string $category, string $language, string $streamingService): void
    {
        $allowedCategories = TMDBService::$categories;
        $allowedLanguages = array_keys(TMDBService::$languages);
        $allowedStreamings = array_keys(TMDBService::$streamingServices);

        if (!in_array($category, $allowedCategories)) {
            abort(400, 'Invalid category');
        }

        if ($language !== 'all' && !in_array($language, $allowedLanguages)) {
            abort(400, 'Invalid language');
        }

        if ($streamingService !== 'all' && !in_array($streamingService, $allowedStreamings)) {
            abort(400, 'Invalid streaming service');
        }
    }
}
