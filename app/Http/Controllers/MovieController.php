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
        $options = [];

        // リクエストからフィルターオプションを取得できるようにする（オプション）
        if ($request->has('year')) {
            $options['year'] = $request->query('year');
        }

        $movies = $this->tmdbService->discoverMovies($options);

        return view('movies.discover', ['movies' => $movies]);
    }
}
