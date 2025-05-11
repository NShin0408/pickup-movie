<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TMDBService
{
    protected ?string $apiKey;
    protected string $baseUrl = 'https://api.themoviedb.org/3';
    protected string $defaultLanguage = 'ja-JP'; // デフォルト表示言語
    protected string $defaultRegion = 'JP'; // 日本のリージョンコード

    public static array $categories = [
        'popular',
        'top_rated',
        'now_playing'
    ];

    // 言語選択オプションの定義（ISO 639-1 + ISO 3166-1）
    public static array $languages = [
        'all' => 'すべての言語',
        'ja-JP' => '日本語',
        'en-US' => '英語',
        'ko-KR' => '韓国語',
        'fr-FR' => 'フランス語',
        'es-ES' => 'スペイン語',
        'zh-CN' => '中国語',
        'th-TH' => 'タイ語',
        'fa-IR' => 'ペルシャ語',
        'de-DE' => 'ドイツ語',
        'it-IT' => 'イタリア語',
        'pl-PL' => 'ポーランド語',
        'fi-FI' => 'フィンランド語',
        'hi-IN' => 'ヒンディー語'
    ];

    // 配信サービスの定義
    public static array $streamingServices = [
        'all' => 'すべてのサービス',
        '84' => 'U-NEXT',
        '337' => 'Disney+',
        '9' => 'Amazon Prime Video',
        '8' => 'Netflix',
        '2' => 'Apple TV+',
        '15' => 'Hulu',
        '190' => 'dTV'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
        if (!$this->apiKey) {
            logger()->error('TMDB API Key is missing!');
        }
    }

    private function getCache(string $key): mixed
    {
        if (cache()->has($key)) {
            logger()->info("Cache HIT for key: $key");
            return cache()->get($key);
        }
        return null;
    }

    private function setCache(string $key, mixed $value): void // デフォルト6時間
    {
        logger()->info("Set cache for key: $key");
        cache()->put($key, $value, 21600);
    }

    /**
     * 選択された言語から表示言語と原語のパラメータを取得
     */
    private function getLanguageParams(?string $selectedLanguage): array
    {
        if (!$selectedLanguage || $selectedLanguage === 'all') {
            return [
                'display_language' => $this->defaultLanguage,
                'original_language' => null
            ];
        }

        // 言語コード（ja-JP）を分割
        $parts = explode('-', $selectedLanguage);

        if (count($parts) !== 2) {
            return [
                'display_language' => $this->defaultLanguage,
                'original_language' => null
            ];
        }

        $langCode = $parts[0]; // ja

        return [
            // APIリクエスト用にアンダースコア形式に変換（ja-JP）
            'display_language' => $this->defaultLanguage,
            // オリジナル言語フィルタには言語コードのみ使用（ja）
            'original_language' => $langCode
        ];
    }

    /**
     * TMDBのAPIに対してGETリクエストを実行し、結果を返す
     */
    private function makeApiRequest(string $endpoint, array $params = []): array
    {
        $params['api_key'] = $this->apiKey;

        // アダルトコンテンツは常に除外
        $params['include_adult'] = false;

        $url = $this->baseUrl . $endpoint;
        $response = Http::get($url, $params);

        if (!$response->successful()) {
            // ログに記録したり、例外を投げたりすることもできます
            return [];
        }

        return $response->json() ?? [];
    }

    /**
     * ポスターがある映画だけをフィルタリング
     */
    private function filterMoviesWithPosters(array $results, int $limit = 20): array
    {
        // ポスターがある映画だけをフィルタリング
        $filteredResults = array_filter($results, function($movie) {
            return !empty($movie['poster_path']);
        });

        // 指定件数に制限
        return array_slice(array_values($filteredResults), 0, $limit);
    }

    /**
     * 映画情報を取得する共通メソッド
     */
    private function fetchMovies(string $endpoint, array $langParams, string $streamingService, array $additionalParams = [], int $page = 1): array
    {
        // 配信サービスの指定または言語フィルタがある場合はdiscoverエンドポイントを使用
        if (($streamingService && $streamingService !== 'all') || $langParams['original_language']) {
            $options = array_merge($additionalParams, ['page' => $page]);
            return $this->discoverMovies($options, $langParams, $streamingService);
        }

        // 通常のエンドポイントを使用
        $params = array_merge($additionalParams, [
            'language' => $langParams['display_language'],
            'page' => $page,
        ]);

        $response = $this->makeApiRequest($endpoint, $params);
        $results = $response['results'] ?? [];

        return $this->filterMoviesWithPosters($results);
    }

    /**
     * 人気映画を取得
     */
    public function getPopularMovies(string $selectedLanguage, string $streamingService, int $page = 1): array
    {
        $langParams = $this->getLanguageParams($selectedLanguage);
        $cacheKey = "popular_" . $selectedLanguage . "_" . $streamingService . "_" . $page;

        logger()->info("Trying cache key: $cacheKey");
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $result = $this->fetchMovies('/movie/popular', $langParams, $streamingService, [
            'sort_by' => 'popularity.desc'
        ], $page);

        $this->setCache($cacheKey, $result);
        return $result;
    }

    /**
     * 高評価映画を取得
     */
    public function getTopRatedMovies(string $selectedLanguage, string $streamingService, int $page = 1): array
    {
        $langParams = $this->getLanguageParams($selectedLanguage);
        $cacheKey = "top_rated_" . $selectedLanguage . "_" . $streamingService . "_" . $page;

        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $result = $this->fetchMovies('/movie/top_rated', $langParams, $streamingService, [
            'sort_by' => 'vote_average.desc',
            'vote_count.gte' => 100
        ], $page);

        $this->setCache($cacheKey, $result);
        return $result;
    }

    /**
     * 上映中の映画を取得
     */
    public function getNowPlayingMovies(string $selectedLanguage, string $streamingService, int $page = 1): array
    {
        $langParams = $this->getLanguageParams($selectedLanguage);

        $cacheKey = "now_playing_" . $selectedLanguage . "_" . $streamingService . "_" . $page;
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        // 現在の日付と1ヶ月前の日付を取得
        $now = date('Y-m-d');
        $oneMonthAgo = date('Y-m-d', strtotime('-1 month'));

        $result = $this->fetchMovies('/movie/now_playing', $langParams, $streamingService, [
            'sort_by' => 'release_date.desc',
            'release_date.gte' => $oneMonthAgo,
            'release_date.lte' => $now
        ], $page);

        $this->setCache($cacheKey, $result);
        return $result;
    }

    /**
     * 映画を検索する
     */
    public function discoverMovies(array $options = [], array $langParams = [], ?string $streamingService = null): array
    {
        $defaultOptions = [
            'page' => 1,
        ];

        $params = array_merge($defaultOptions, $options, [
            'language' => $langParams['display_language'] ?? $this->defaultLanguage,
        ]);

        // オリジナル言語フィルターを追加
        if (!empty($langParams['original_language'])) {
            $params['with_original_language'] = $langParams['original_language'];
        }

        // 配信サービスでフィルタリング
        if ($streamingService && $streamingService !== 'all') {
            $params['with_watch_providers'] = $streamingService;
            $params['watch_region'] = $this->defaultRegion;
        }

        $response = $this->makeApiRequest('/discover/movie', $params);
        $results = $response['results'] ?? [];

        return $this->filterMoviesWithPosters($results);
    }

    /**
     * 映画の詳細情報を取得
     */
    public function getMovieDetails(int $movieId): array
    {
        $cacheKey = "movie_details_" . $movieId;
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $params = [
            'language' => $this->defaultLanguage,
            'append_to_response' => 'credits,similar,recommendations,videos,watch/providers'
        ];

        $url = "/movie/" . $movieId;
        $result = $this->makeApiRequest($url, $params);

        $this->setCache($cacheKey, $result);
        return $result;
    }

    /**
     * 映画のビデオ情報（トレーラーなど）を取得
     */
    public function getMovieVideos(int $movieId): array
    {
        $cacheKey = "movie_videos_" . $movieId;
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $params = [
            'language' => $this->defaultLanguage,
        ];

        $url = "/movie/" . $movieId . "/videos";
        $response = $this->makeApiRequest($url, $params);
        $videos = $response['results'] ?? [];

        // 日本語のビデオがない場合は英語のビデオも取得
        if (empty($videos) && $this->defaultLanguage !== 'en-US') {
            $params['language'] = 'en-US';
            $response = $this->makeApiRequest($url, $params);
            $videos = $response['results'] ?? [];
        }

        $this->setCache($cacheKey, $videos);
        return $videos;
    }

    /**
     * 公式トレーラーやティーザーを優先して返す
     */
    public function getOfficialTrailer(int $movieId): array
    {
        $videos = $this->getMovieVideos($movieId);

        if (empty($videos)) {
            return [];
        }

        // タイプと名前でフィルタリング (優先順位順)
        $typeOrder = ['Trailer', 'Teaser'];

        foreach ($typeOrder as $type) {
            // 公式トレーラーを探す
            $officialTrailers = array_filter($videos, function($video) use ($type) {
                return $video['type'] === $type &&
                    ($video['official'] ?? true) &&
                    $video['site'] === 'YouTube';
            });

            if (!empty($officialTrailers)) {
                return reset($officialTrailers); // 最初の要素を返す
            }
        }

        // 公式トレーラーが見つからない場合は、YouTubeの動画を返す
        $youtubeVideos = array_filter($videos, function($video) {
            return $video['site'] === 'YouTube';
        });

        if (!empty($youtubeVideos)) {
            return reset($youtubeVideos);
        }

        return [];
    }

    /**
     * YouTubeの埋め込みURLを生成
     */
    public function getYoutubeEmbedUrl(string $youtubeKey): string
    {
        return "https://www.youtube-nocookie.com/embed/" . $youtubeKey;
    }

    /**
     * 特定の監督の映画を取得
     */
    public function getDirectorMovies(int $directorId, int $excludeMovieId = null, int $limit = 10): array
    {
        $cacheKey = "director_movies_" . $directorId . "_" . $excludeMovieId . "_" . $limit;
        $cached = $this->getCache($cacheKey);
        if ($cached !== null) {
            return $cached;
        }

        $params = [
            'language' => $this->defaultLanguage,
            'with_crew' => $directorId,
            'sort_by' => 'popularity.desc'
        ];

        $response = $this->makeApiRequest('/discover/movie', $params);
        $results = $response['results'] ?? [];

        // 現在の映画を除外
        if ($excludeMovieId) {
            $results = array_filter($results, function($movie) use ($excludeMovieId) {
                return $movie['id'] != $excludeMovieId;
            });
        }

        $results = $this->filterMoviesWithPosters($results, $limit);
        $this->setCache($cacheKey, $results);
        return $results;
    }

    /**
     * 映画から監督情報を取得
     */
    public function getDirectorFromMovie(array $movie): array
    {
        if (empty($movie['credits']['crew'])) {
            return [];
        }

        $directors = array_filter($movie['credits']['crew'], function($crewMember) {
            return $crewMember['job'] === 'Director';
        });

        if (empty($directors)) {
            return [];
        }

        // 最初の監督を返す
        return reset($directors);
    }
}
