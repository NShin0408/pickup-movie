<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TMDBService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.themoviedb.org/3';
    protected string $defaultLanguage = 'ja_JP'; // デフォルト表示言語
    protected string $defaultRegion = 'JP'; // 日本のリージョンコード

    // 言語選択オプションの定義（ISO 639-1 + ISO 3166-1）
    public static array $languages = [
        'all' => 'すべての言語',
        'ja-JP' => '日本語',
        'en-US' => '英語（米国）',
        'en-GB' => '英語（英国）',
        'ko-KR' => '韓国語',
        'fr-FR' => 'フランス語',
        'es-ES' => 'スペイン語',
        'zh-CN' => '中国語（簡体字）',
        'zh-TW' => '中国語（台湾）', // 台湾の中国語
        'th-TH' => 'タイ語', // タイ語
        'fa-IR' => 'ペルシャ語', // イラン（ペルシャ語）
        'de-DE' => 'ドイツ語',
        'it-IT' => 'イタリア語'
    ];

    // 配信サービスの定義
    public static array $streamingServices = [
        'all' => 'すべてのサービス',
        '1001' => 'U-NEXT',
        '337' => 'Disney+',
        '9' => 'Amazon Prime Video',
        '8' => 'Netflix',
        '2' => 'Apple TV+',
        '138' => 'Hulu',
        '190' => 'dTV'
    ];

    public function __construct()
    {
        $this->apiKey = config('services.tmdb.api_key');
    }

    /**
     * 選択された言語から表示言語(language)とオリジナル言語(with_original_language)のパラメータを取得
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
        $countryCode = $parts[1]; // JP

        return [
            // APIリクエスト用にアンダースコア形式に変換（ja_JP）
            'display_language' => $langCode . '_' . $countryCode,
            // オリジナル言語フィルタには言語コードのみ使用（ja）
            'original_language' => $langCode
        ];
    }

    public function getPopularMovies(?string $selectedLanguage = null, ?string $streamingService = null): array
    {
        $langParams = $this->getLanguageParams($selectedLanguage);

        // 配信サービスの指定がある場合はdiscoverエンドポイントを使用
        if ($streamingService && $streamingService !== 'all') {
            return $this->discoverMovies([
                'sort_by' => 'popularity.desc'
            ], $langParams, $streamingService);
        }

        // 言語フィルタがある場合もdiscoverエンドポイントを使用
        if ($langParams['original_language']) {
            return $this->discoverMovies([
                'sort_by' => 'popularity.desc'
            ], $langParams);
        }

        // 通常のpopularエンドポイントを使用
        $params = [
            'api_key' => $this->apiKey,
            'language' => $langParams['display_language'], // 表示言語（ja_JP形式）
            'page' => 1,
            'include_adult' => false, // アダルトコンテンツ除外
        ];

        $response = Http::get("{$this->baseUrl}/movie/popular", $params);

        return $response->json()['results'] ?? [];
    }

    public function getTopRatedMovies(?string $selectedLanguage = null, ?string $streamingService = null): array
    {
        $langParams = $this->getLanguageParams($selectedLanguage);

        // 配信サービスの指定がある場合はdiscoverエンドポイントを使用
        if ($streamingService && $streamingService !== 'all') {
            return $this->discoverMovies([
                'sort_by' => 'vote_average.desc',
                'vote_count.gte' => 100
            ], $langParams, $streamingService);
        }

        // 言語フィルタがある場合もdiscoverエンドポイントを使用
        if ($langParams['original_language']) {
            return $this->discoverMovies([
                'sort_by' => 'vote_average.desc',
                'vote_count.gte' => 100
            ], $langParams);
        }

        // 通常のtop_ratedエンドポイントを使用
        $params = [
            'api_key' => $this->apiKey,
            'language' => $langParams['display_language'], // 表示言語（ja_JP形式）
            'page' => 1,
            'include_adult' => false, // アダルトコンテンツ除外
        ];

        $response = Http::get("{$this->baseUrl}/movie/top_rated", $params);

        return $response->json()['results'] ?? [];
    }

    public function getNowPlayingMovies(?string $selectedLanguage = null, ?string $streamingService = null): array
    {
        $langParams = $this->getLanguageParams($selectedLanguage);

        // 現在の日付と1ヶ月前の日付を取得
        $now = date('Y-m-d');
        $oneMonthAgo = date('Y-m-d', strtotime('-1 month'));

        // 配信サービスの指定がある場合はdiscoverエンドポイントを使用
        if ($streamingService && $streamingService !== 'all') {
            return $this->discoverMovies([
                'sort_by' => 'release_date.desc',
                'release_date.gte' => $oneMonthAgo,
                'release_date.lte' => $now
            ], $langParams, $streamingService);
        }

        // 言語フィルタがある場合もdiscoverエンドポイントを使用
        if ($langParams['original_language']) {
            return $this->discoverMovies([
                'sort_by' => 'release_date.desc',
                'release_date.gte' => $oneMonthAgo,
                'release_date.lte' => $now
            ], $langParams);
        }

        // 通常のnow_playingエンドポイントを使用
        $params = [
            'api_key' => $this->apiKey,
            'language' => $langParams['display_language'], // 表示言語（ja_JP形式）
            'page' => 1,
            'include_adult' => false, // アダルトコンテンツ除外
        ];

        $response = Http::get("{$this->baseUrl}/movie/now_playing", $params);

        return $response->json()['results'] ?? [];
    }

    public function discoverMovies(array $options = [], array $langParams = [], ?string $streamingService = null): array
    {
        $defaultOptions = [
            'include_adult' => false, // アダルトコンテンツ除外
            'page' => 1,
        ];

        $params = array_merge($defaultOptions, $options, [
            'api_key' => $this->apiKey,
            'language' => $langParams['display_language'] ?? $this->defaultLanguage,
        ]);

        // アダルトコンテンツは明示的に除外
        $params['include_adult'] = false;

        // オリジナル言語フィルターを追加
        if (!empty($langParams['original_language'])) {
            $params['with_original_language'] = $langParams['original_language']; // 制作言語でフィルタリング
        }

        // 配信サービスでフィルタリング
        if ($streamingService) {
            $params['with_watch_providers'] = $streamingService; // 配信サービスでフィルタリング
            $params['watch_region'] = $this->defaultRegion; // 日本国内での配信状況
        }

        $response = Http::get("{$this->baseUrl}/discover/movie", $params);

        return $response->json()['results'] ?? [];
    }

    /**
     * 利用可能な配信サービスのリストを取得
     */
    public function getAvailableWatchProviders(): array
    {
        $params = [
            'api_key' => $this->apiKey,
            'language' => $this->defaultLanguage,
            'watch_region' => $this->defaultRegion,
        ];

        $response = Http::get("{$this->baseUrl}/watch/providers/movie", $params);

        return $response->json()['results'] ?? [];
    }
}
