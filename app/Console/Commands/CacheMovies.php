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
        $streamingServices = array_keys($this->tmdbService::$streamingServices);

        foreach ($categories as $category) {
            foreach ($languages as $language) {
                foreach ($streamingServices as $streamingService) {
                    for ($page = 1; $page <= 3; $page++) {
                        $this->info("Caching $category, language: $language, page: $page");

                        // 各カテゴリに対応するメソッドを呼ぶ
                        switch ($category) {
                            case 'popular':
                                $this->tmdbService->getPopularMovies($language, $streamingService, $page, true);
                                break;
                            case 'top_rated':
                                $this->tmdbService->getTopRatedMovies($language, $streamingService, $page, true);
                                break;
                            case 'now_playing':
                                $this->tmdbService->getNowPlayingMovies($language, $streamingService, $page, true);
                                break;
                        }
                    }
                }
            }
        }

        $this->info('Caching complete!');
    }
}
