<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TMDBService
{
    protected string $apiKey;
    protected string $baseUrl = 'https://api.themoviedb.org/3';
    protected string $defaultLanguage = 'ja_JP'; // デフォルト表示言語

    // 言語選択オプションの定義（ISO 639-1 + ISO 3166-1）
    public static array $languages = [
        'all' => 'すべての言語',
        'ja-JP' => '日本語',
        'en-US' => '英語',
        'ko-KR' => '韓国語',
        'fr-FR' => 'フランス語',
        'es-ES' => 'スペイン語',
        'zh-CN' => '中国語',
        'zh-TW' => '中国語（台湾）',
        'th-TH' => 'タイ語', // タイ語
        'fa-IR' => 'ペルシャ語', // イラン（ペルシャ語）
        'de-DE' => 'ドイツ語',
        'it-IT' => 'イタリア語'
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

    public function getPopularMovies(?string $selectedLanguage = null): array
    {
        $langParams = $this->getLanguageParams($selectedLanguage);

        $params = [
            'api_key' => $this->apiKey,
            'language' => $langParams['display_language'], // 表示言語（ja_JP形式）
            'page' => 1,
            'include_adult' => false,
        ];

        // オリジナル言語フィルターを追加
        if ($langParams['original_language']) {
            // discoverエンドポイントを使用して言語フィルタリング
            return $this->discoverMovies([
                'sort_by' => 'popularity.desc'
            ], $langParams);
        }

        // 言語フィルタなしの場合は通常のエンドポイントを使用
        $response = Http::get("{$this->baseUrl}/movie/popular", $params);

        return $response->json()['results'] ?? [];
    }

    public function getTopRatedMovies(?string $selectedLanguage = null): array
    {
        $langParams = $this->getLanguageParams($selectedLanguage);

        $params = [
            'api_key' => $this->apiKey,
            'language' => $langParams['display_language'], // 表示言語（ja_JP形式）
            'page' => 1,
            'include_adult' => false,
        ];

        // オリジナル言語フィルターを追加
        if ($langParams['original_language']) {
            // discoverエンドポイントを使用して言語フィルタリング
            return $this->discoverMovies([
                'sort_by' => 'vote_average.desc',
                'vote_count.gte' => 100
            ], $langParams);
        }

        // 言語フィルタなしの場合は通常のエンドポイントを使用
        $response = Http::get("{$this->baseUrl}/movie/top_rated", $params);

        return $response->json()['results'] ?? [];
    }

    public function getNowPlayingMovies(?string $selectedLanguage = null): array
    {
        $langParams = $this->getLanguageParams($selectedLanguage);

        $params = [
            'api_key' => $this->apiKey,
            'language' => $langParams['display_language'], // 表示言語（ja_JP形式）
            'page' => 1,
            'include_adult' => false,
        ];

        // オリジナル言語フィルターを追加
        if ($langParams['original_language']) {
            // 現在の日付と1ヶ月前の日付を取得
            $now = date('Y-m-d');
            $oneMonthAgo = date('Y-m-d', strtotime('-1 month'));

            // discoverエンドポイントを使用して言語フィルタリング
            return $this->discoverMovies([
                'sort_by' => 'release_date.desc',
                'release_date.gte' => $oneMonthAgo,
                'release_date.lte' => $now
            ], $langParams);
        }

        // 言語フィルタなしの場合は通常のエンドポイントを使用
        $response = Http::get("{$this->baseUrl}/movie/now_playing", $params);

        return $response->json()['results'] ?? [];
    }

    public function discoverMovies(array $options = [], array $langParams = []): array
    {
        $defaultOptions = [
            'include_adult' => false,
            'page' => 1,
        ];

        $params = array_merge($defaultOptions, $options, [
            'api_key' => $this->apiKey,
            'language' => $langParams['display_language'] ?? $this->defaultLanguage,
        ]);

        // オリジナル言語フィルターを追加
        if (!empty($langParams['original_language'])) {
            $params['with_original_language'] = $langParams['original_language']; // 制作言語でフィルタリング
        }

        $response = Http::get("{$this->baseUrl}/discover/movie", $params);

        return $response->json()['results'] ?? [];
    }
}
