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
        $allowedServiceNames = ['すべてのサービス', 'U-NEXT', 'Amazon Prime Video', 'Netflix'];

        foreach ($categories as $category) {
            foreach ($languages as $language) {
                foreach ($this->tmdbService::$streamingServices as $streamingServiceId => $streamingServiceName) {
                    if (!in_array($streamingServiceName, $allowedServiceNames, true)) {
                        continue;
                    }

                    for ($page = 1; $page <= 3; $page++) {
                        $this->info("Caching $category, language: $language, page: $page");

                        $result = [];
                        // 各カテゴリに対応するメソッドを呼ぶ
                        switch ($category) {
                            case 'popular':
                                $result = $this->tmdbService->getPopularMovies($language, $streamingServiceId, $page, true);
                                break;
                            case 'top_rated':
                                $result = $this->tmdbService->getTopRatedMovies($language, $streamingServiceId, $page, true);
                                break;
                            case 'now_playing':
                                $result = $this->tmdbService->getNowPlayingMovies($language, $streamingServiceId, $page, true);
                                break;
                        }

                        // 20件未満ならループを抜ける
                        if (count($result) < 20) {
                            $this->info("No results. Breaking out of page loop.");
                            break;
                        }
                    }
                }
            }
        }

        $this->info('Caching complete!');
    }
}
