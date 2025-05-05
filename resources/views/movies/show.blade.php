@php use Carbon\Carbon; @endphp
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $movie['title'] ?? '映画詳細' }}</title>

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
<body class="bg-movie-dark text-movie-light font-sans p-0 overflow-x-hidden">
<div class="max-w-[1100px] mx-auto p-5">
    <a href="/"
       class="inline-block my-5 py-2.5 px-5 bg-movie-panel text-movie-light no-underline rounded transition-colors hover:bg-movie-panel-hover">
        ← 一覧に戻る
    </a>

    @if(!empty($movie))
        <div class="flex flex-wrap gap-7 mb-7">
            <div class="flex-none md:w-[300px] w-full px-0">
                @if(!empty($movie['poster_path']))
                    <img
                        src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                        srcset="
                            https://image.tmdb.org/t/p/w154{{ $movie['poster_path'] }} 154w,
                            https://image.tmdb.org/t/p/w185{{ $movie['poster_path'] }} 185w,
                            https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }} 342w,
                            https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }} 500w
                        "
                        sizes="(max-width: 640px) 154px, (max-width: 768px) 185px, (max-width: 1024px) 300px, 300px"
                        alt="{{ $movie['title'] }}"
                        class="w-full rounded-lg shadow-movie-trailer sm:max-w-[300px] mx-auto sm:mx-0"
                        loading="lazy"
                    >
                @endif
            </div>

            <div class="flex-1 min-w-[300px]">
                <h1 class="text-3xl md:text-4xl mb-2.5">{{ $movie['title'] }}</h1>

                <div class="mb-5 text-movie-gray">
                    @if(!empty($movie['release_date']))
                        公開日: {{ Carbon::parse($movie['release_date'])->format('Y年m月d日') }}
                    @endif

                    @if(!empty($movie['runtime']))
                        | {{ $movie['runtime'] }}分
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
                    <p class="text-movie-muted italic">現在、この映画の概要情報はありません</p>
                @endif

                <!-- 配信サービス情報の表示 -->
                @php
                    $providers = $movie['watch/providers']['results']['JP'] ?? null;
                    $hasServices = (!empty($providers['flatrate']) || !empty($providers['rent']));
                    $justWatchUrl = $providers['link'] ?? "https://www.justwatch.com/jp/映画/{$movie['id']}";
                @endphp

                @if($hasServices)
                    <div class="mt-5">
                        <h2 class="text-2xl mt-7 mb-4">配信サービス</h2>

                        @if(!empty($providers['flatrate']))
                            <x-streaming-services title="ストリーミング" :services="$providers['flatrate']" :link="$justWatchUrl" />
                        @endif

                        @if(!empty($providers['rent']))
                            <x-streaming-services title="レンタル" :services="$providers['rent']" :link="$justWatchUrl" />
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
                        loading="lazy"
                        class="absolute top-0 left-0 w-full h-full border-none"></iframe>
            </div>
        @endif

        @if(!empty($director) && !empty($directorMovies))
            <x-movie-carousel title="{{ $director['name'] }} 監督の映画" :movies="$directorMovies" carouselId="director-carousel" />
        @endif

        @if(!empty($movie['recommendations']['results']))
            <x-movie-carousel title="おすすめ映画" :movies="array_slice($movie['recommendations']['results'], 0, 10)" carouselId="recommendations-carousel" />
        @endif

        @if(!empty($movie['similar']['results']))
            <x-movie-carousel title="関連映画" :movies="array_slice($movie['similar']['results'], 0, 10)" carouselId="similar-carousel" />
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
<footer class="w-full text-center text-sm text-movie-muted mt-10 pt-10 border-t border-movie-panel">
    <div class="flex justify-center gap-5">
        <a href="/terms" class="hover:underline">利用規約</a>
        <a href="/privacy" class="hover:underline">プライバシーポリシー</a>
    </div>
    <div class="mt-3">&copy; {{ date('Y') }} Pickup Movie</div>
</footer>
</body>
</html>
