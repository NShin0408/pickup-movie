<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TMDBService;

class CacheMovies extends Command
{
    protected $signature = 'cache:movies';

    protected $description = 'Cache movies data for popular, top rated, and now playing for all languages and pages 1-3';

    protected TMDBService $tmdbService;

    public function __construct(TMDBService $tmdbService)
    {
        parent::__construct();
        $this->tmdbService = $tmdbService;
    }

    public function handle(): void
    {
        $categories = $this->tmdbService::$categories;
        $languages = array_keys($this->tmdbService::$languages);
        $streamingService = 'all'; // 配信サービスをキャッシュに含めるならここもループにできます

        foreach ($categories as $category) {
            foreach ($languages as $language) {
                for ($page = 1; $page <= 3; $page++) {
                    $this->info("Caching $category, language: $language, page: $page");

                    // 各カテゴリに対応するメソッドを呼ぶ
                    switch ($category) {
                        case 'popular':
                            $this->tmdbService->getPopularMovies($language, $streamingService, $page);
                            break;
                        case 'top_rated':
                            $this->tmdbService->getTopRatedMovies($language, $streamingService, $page);
                            break;
                        case 'now_playing':
                            $this->tmdbService->getNowPlayingMovies($language, $streamingService, $page);
                            break;
                    }
                }
            }
        }

        $this->info('Caching complete!');
    }
}
