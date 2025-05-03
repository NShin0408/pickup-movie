<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $movie['title'] ?? '映画詳細' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-movie-dark text-movie-light font-sans p-0 overflow-x-hidden">
<div class="max-w-[1100px] mx-auto p-5">
    <a href="/" class="inline-block my-5 py-2.5 px-5 bg-movie-panel text-movie-light no-underline rounded transition-colors hover:bg-movie-panel-hover">
        ← 一覧に戻る
    </a>

    @if(!empty($movie))
        <div class="flex flex-wrap gap-7 mb-7">
            <div class="flex-none w-[300px]">
                @if(!empty($movie['poster_path']))
                    <img src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}"
                         alt="{{ $movie['title'] }}"
                         class="w-full rounded-lg shadow-movie-trailer">
                @endif
            </div>

            <div class="flex-1 min-w-[300px]">
                <h1 class="text-4xl mb-2.5">{{ $movie['title'] }}</h1>

                <div class="mb-5 text-movie-gray">
                    @if(!empty($movie['release_date']))
                        公開日: {{ \Carbon\Carbon::parse($movie['release_date'])->format('Y年m月d日') }}
                    @endif

                    @if(!empty($movie['runtime']))
                        | {{ $movie['runtime'] }}分
                    @endif

                    @if(!empty($movie['vote_average']))
                        | 評価: {{ number_format($movie['vote_average'], 1) }}/10 ({{ number_format($movie['vote_count']) }}件)
                    @endif
                </div>

                @if(!empty($movie['genres']))
                    <div class="flex flex-wrap gap-2.5 mb-5">
                        @foreach($movie['genres'] as $genre)
                            <span class="bg-movie-panel-hover py-1.5 px-2.5 rounded text-sm">
                                {{ $genre['name'] }}
                            </span>
                        @endforeach
                    </div>
                @endif

                @if(!empty($director))
                    <div class="mb-5">
                        <span class="text-movie-gray">監督:</span> {{ $director['name'] }}
                    </div>
                @endif

                @if(!empty($movie['overview']))
                    <p class="text-lg leading-relaxed mb-5">{{ $movie['overview'] }}</p>
                @else
                    <p class="text-lg leading-relaxed mb-5">概要はありません</p>
                @endif

                <!-- 配信サービス情報の表示 -->
                @php
                    $providers = $movie['watch/providers']['results']['JP'] ?? null;
                    $hasServices = (!empty($providers['flatrate']) || !empty($providers['rent']));
                @endphp

                @if($hasServices)
                    <div class="mt-5">
                        <h2 class="text-2xl mt-7 mb-4">配信サービス</h2>

                        @if(!empty($providers['flatrate']))
                            <div class="mb-5">
                                <div class="text-base text-movie-gray mb-2.5">ストリーミング</div>
                                <div class="flex flex-wrap gap-4">
                                    @foreach($providers['flatrate'] as $service)
                                        <div class="w-[60px] h-[60px] rounded-xl overflow-hidden shadow-movie-service">
                                            <img src="https://image.tmdb.org/t/p/original{{ $service['logo_path'] }}"
                                                 alt="{{ $service['provider_name'] }}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if(!empty($providers['rent']))
                            <div class="mb-5">
                                <div class="text-base text-movie-gray mb-2.5">レンタル</div>
                                <div class="flex flex-wrap gap-4">
                                    @foreach($providers['rent'] as $service)
                                        <div class="w-[60px] h-[60px] rounded-xl overflow-hidden shadow-movie-service">
                                            <img src="https://image.tmdb.org/t/p/original{{ $service['logo_path'] }}"
                                                 alt="{{ $service['provider_name'] }}"
                                                 class="w-full h-full object-cover">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="mt-5">
                        <h2 class="text-2xl mt-7 mb-4">配信サービス</h2>
                        <p class="text-movie-muted italic">現在、日本での配信情報はありません</p>
                    </div>
                @endif
            </div>
        </div>

        @if($trailerUrl)
            <h2 class="text-2xl mt-7 mb-4">トレーラー</h2>
            <div class="relative pb-[56.25%] h-0 overflow-hidden max-w-full mb-7 rounded-lg shadow-movie-trailer">
                <iframe src="{{ $trailerUrl }}"
                        title="YouTube trailer"
                        allowfullscreen
                        class="absolute top-0 left-0 w-full h-full border-none"></iframe>
            </div>
        @endif

        <!-- 同じ監督の他作品セクション -->
        @if(!empty($director) && !empty($directorMovies))
            <h2 class="text-2xl mt-7 mb-4">{{ $director['name'] }} 監督の他作品</h2>
            <div class="flex gap-4 overflow-x-auto pb-4">
                @foreach($directorMovies as $directorMovie)
                    @if(!empty($directorMovie['poster_path']))
                        <div class="flex-none w-[150px] cursor-pointer movie-item"
                             onclick="window.location.href='/movies/{{ $directorMovie['id'] }}'">
                            <div class="relative">
                                <img src="https://image.tmdb.org/t/p/w300{{ $directorMovie['poster_path'] }}"
                                     alt="{{ $directorMovie['title'] }}"
                                     class="w-full aspect-poster object-cover rounded-lg shadow-movie-poster mb-2">
                                <div class="movie-title-overlay">
                                    {{ $directorMovie['title'] }}
                                </div>
                            </div>
                            <div class="text-sm truncate">{{ $directorMovie['title'] }}</div>
                            @if(!empty($directorMovie['release_date']))
                                <div class="text-xs text-movie-gray">
                                    {{ substr($directorMovie['release_date'], 0, 4) }}年
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- おすすめ映画セクション -->
        @if(!empty($movie['recommendations']['results']))
            <h2 class="text-2xl mt-7 mb-4">おすすめ映画</h2>
            <div class="flex gap-4 overflow-x-auto pb-4">
                @foreach(array_slice($movie['recommendations']['results'], 0, 10) as $recommended)
                    @if(!empty($recommended['poster_path']))
                        <div class="flex-none w-[150px] cursor-pointer movie-item"
                             onclick="window.location.href='/movies/{{ $recommended['id'] }}'">
                            <div class="relative">
                                <img src="https://image.tmdb.org/t/p/w300{{ $recommended['poster_path'] }}"
                                     alt="{{ $recommended['title'] }}"
                                     class="w-full aspect-poster object-cover rounded-lg shadow-movie-poster mb-2">
                                <div class="movie-title-overlay">
                                    {{ $recommended['title'] }}
                                </div>
                            </div>
                            <div class="text-sm truncate">{{ $recommended['title'] }}</div>
                            @if(!empty($recommended['release_date']))
                                <div class="text-xs text-movie-gray">
                                    {{ substr($recommended['release_date'], 0, 4) }}年
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <!-- 類似映画セクション -->
        @if(!empty($movie['similar']['results']))
            <h2 class="text-2xl mt-7 mb-4">類似映画</h2>
            <div class="flex gap-4 overflow-x-auto pb-4">
                @foreach(array_slice($movie['similar']['results'], 0, 10) as $similar)
                    @if(!empty($similar['poster_path']))
                        <div class="flex-none w-[150px] cursor-pointer movie-item"
                             onclick="window.location.href='/movies/{{ $similar['id'] }}'">
                            <div class="relative">
                                <img src="https://image.tmdb.org/t/p/w300{{ $similar['poster_path'] }}"
                                     alt="{{ $similar['title'] }}"
                                     class="w-full aspect-poster object-cover rounded-lg shadow-movie-poster mb-2">
                                <div class="movie-title-overlay">
                                    {{ $similar['title'] }}
                                </div>
                            </div>
                            <div class="text-sm truncate">{{ $similar['title'] }}</div>
                            @if(!empty($similar['release_date']))
                                <div class="text-xs text-movie-gray">
                                    {{ substr($similar['release_date'], 0, 4) }}年
                                </div>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <div class="w-full text-xs text-movie-muted text-center mt-7 pt-5 border-t border-movie-panel">
            映画情報提供元: TMDb<br>
            配信情報提供元: JustWatch
        </div>
    @else
        <div class="text-center py-12">
            <h1 class="text-2xl">映画情報が見つかりませんでした</h1>
        </div>
    @endif
</div>
</body>
</html>
