<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="movie-category" content="{{ $currentCategory }}">
    <meta name="movie-language" content="{{ $currentLanguage }}">
    <meta name="movie-streaming" content="{{ $currentStreaming }}">
    <title>映画リスト</title>
    @if (app()->environment('development'))
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
    @else
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        @endphp
        <link rel="stylesheet" href="{{ secure_asset('build/' . $manifest['resources/css/app.css']['file']) }}">
        <script type="module" src="{{ secure_asset('build/' . $manifest['resources/js/app.ts']['file']) }}"></script>
    @endif
</head>
<body class="bg-movie-dark text-movie-light font-sans p-5 w-full overflow-x-hidden">
<div class="max-w-[1200px] w-full mx-auto relative block">
    <div class="mb-5 px-2.5 text-center w-full">
        <h1 class="text-2xl font-medium mb-5">映画リスト</h1>
    </div>

    <div class="w-full flex justify-center mb-7">
        <div class="w-[800px] max-w-full min-h-[220px]">
            <div class="block w-full">
                <div class="mb-4 w-full text-center">
                    <div class="text-sm text-movie-gray mb-2 text-center">カテゴリー</div>
                    <div class="flex justify-center flex-wrap gap-2.5 mx-auto w-full">
                        <a href="/?category=popular&language={{ $currentLanguage }}&streaming={{ $currentStreaming }}"
                           class="py-2 px-4 bg-movie-panel rounded text-movie-light text-sm transition-colors inline-block
                                     {{ $currentCategory === 'popular' ? 'bg-movie-panel-active' : '' }} hover:bg-movie-panel-hover">
                            人気
                        </a>
                        <a href="/?category=top_rated&language={{ $currentLanguage }}&streaming={{ $currentStreaming }}"
                           class="py-2 px-4 bg-movie-panel rounded text-movie-light text-sm transition-colors inline-block
                                     {{ $currentCategory === 'top_rated' ? 'bg-movie-panel-active' : '' }} hover:bg-movie-panel-hover">
                            高評価
                        </a>
                        <a href="/?category=now_playing&language={{ $currentLanguage }}&streaming={{ $currentStreaming }}"
                           class="py-2 px-4 bg-movie-panel rounded text-movie-light text-sm transition-colors inline-block
                                     {{ $currentCategory === 'now_playing' ? 'bg-movie-panel-active' : '' }} hover:bg-movie-panel-hover">
                            上映中
                        </a>
                    </div>
                </div>

                <div class="mb-4 w-full text-center">
                    <div class="text-sm text-movie-gray mb-2 text-center">言語</div>
                    <div class="flex justify-center flex-wrap gap-2.5 mx-auto w-full">
                        @foreach ($languages as $code => $name)
                            <a href="/?category={{ $currentCategory }}&language={{ $code }}&streaming={{ $currentStreaming }}"
                               class="py-1.5 px-3 bg-movie-panel rounded text-movie-light text-sm transition-colors inline-block
                                     {{ $currentLanguage === $code ? 'bg-movie-panel-active' : '' }} hover:bg-movie-panel-hover">
                                {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="mb-4 w-full text-center">
                    <div class="text-sm text-movie-gray mb-2 text-center">配信サービス</div>
                    <div class="flex justify-center flex-wrap gap-2.5 mx-auto w-full">
                        @foreach ($streamingServices as $id => $name)
                            <a href="/?category={{ $currentCategory }}&language={{ $currentLanguage }}&streaming={{ $id }}"
                               class="py-1.5 px-3 bg-movie-panel rounded text-movie-light text-sm transition-colors inline-block
                                     {{ (string)$currentStreaming === (string)$id ? 'bg-movie-panel-active' : '' }} hover:bg-movie-panel-hover">
                                {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-full flex justify-center">
        <div class="w-[1100px] max-w-full min-h-[300px]">
            @if(count($movies) > 0)
                <div class="grid grid-cols-4 sm:grid-cols-5 gap-1.5 sm:gap-3 md:gap-5 w-full" id="movie-grid">
                    @foreach ($movies as $movie)
                        @if($movie['poster_path'])
                            <div class="relative transition-transform duration-200 cursor-pointer hover:scale-105 movie-item" data-id="{{ $movie['id'] }}">
                                <img
                                    src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                                    alt="{{ $movie['title'] }}"
                                    class="w-full aspect-poster object-cover rounded-lg shadow-movie-poster"
                                    loading="lazy"
                                >
                                <div class="movie-title-overlay">
                                    {{ $movie['title'] }}
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>


                <div class="text-center p-5 mt-5 hidden" id="loading">
                    <div class="inline-block w-[30px] h-[30px] border-3 border-movie-panel rounded-full border-t-movie-light spin-animation"></div>
                </div>

                <button class="block mx-auto my-5 py-3 px-6 bg-movie-panel-hover text-movie-light border-none rounded cursor-pointer text-base transition-colors hover:bg-movie-panel-active" id="load-more-btn">
                    もっと見る
                </button>
            @else
                <div class="w-full max-w-[600px] mx-auto text-center py-12 text-movie-gray text-base bg-movie-panel/20 rounded-lg">
                    <p>条件に一致する映画が見つかりませんでした</p>
                </div>
            @endif
        </div>
    </div>

    <div class="w-full text-xs text-movie-muted text-center mt-7">
        映画情報提供元: TMDb<br>
        配信情報提供元: JustWatch
    </div>
</div>
<footer class="w-full text-center text-sm text-movie-muted mt-10 pt-10 border-t border-movie-panel">
    <div class="flex justify-center gap-5">
        <a href="/terms" class="hover:underline">利用規約</a>
        <a href="/privacy" class="hover:underline">プライバシーポリシー</a>
    </div>
    <div class="mt-3">&copy; {{ date('Y') }} Pickup Movie</div>
</footer>
</body>
</html>
