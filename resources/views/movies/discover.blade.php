<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>映画リスト</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
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
                <div class="grid grid-cols-[repeat(auto-fill,minmax(180px,1fr))] gap-5 w-full" id="movie-grid">
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

<script>
    $(document).ready(function() {
        let currentPage = 1;
        let isLoading = false;
        let hasMore = true;

        const category = '{{ $currentCategory }}';
        const language = '{{ $currentLanguage }}';
        const streaming = '{{ $currentStreaming }}';

        $('#load-more-btn').click(function() {
            if (isLoading) return;
            loadMoreMovies();
        });

        // 映画アイテムのクリックイベントを追加
        $(document).on('click', '[data-id]', function() {
            const movieId = $(this).data('id');
            if (movieId) {
                window.location.href = '/movies/' + movieId;
            }
        });

        function loadMoreMovies() {
            if (isLoading || !hasMore) return;

            isLoading = true;
            currentPage++;

            // ローディング表示
            $('#loading').show();
            $('#load-more-btn').hide();

            // Ajaxリクエスト
            $.ajax({
                url: '/load-more',
                type: 'GET',
                data: {
                    category: category,
                    language: language,
                    streaming: streaming,
                    page: currentPage
                },
                dataType: 'json',
                success: function(data) {
                    console.log('Data received:', data);

                    if (data.movies && data.movies.length > 0) {
                        // 映画を追加
                        $.each(data.movies, function(index, movie) {
                            if (movie.poster_path) {
                                const movieItem = `
                                    <div class="relative transition-transform duration-200 cursor-pointer hover:scale-105 movie-item" data-id="${movie.id}">
                                        <img
                                            src="https://image.tmdb.org/t/p/w342${movie.poster_path}"
                                            alt="${movie.title}"
                                            class="w-full aspect-poster object-cover rounded-lg shadow-movie-poster"
                                            loading="lazy"
                                        >
                                        <div class="movie-title-overlay">
                                            ${movie.title}
                                        </div>
                                    </div>
                                `;
                                $('#movie-grid').append(movieItem);
                            }
                        });

                        hasMore = data.hasMore;
                    } else {
                        hasMore = false;
                    }

                    // ローディング非表示
                    $('#loading').hide();

                    // もっと見るボタンの表示/非表示
                    if (hasMore) {
                        $('#load-more-btn').show();
                    } else {
                        $('#load-more-btn').hide();
                    }

                    isLoading = false;
                },
                error: function(xhr, status, error) {
                    console.error('Error loading more movies:', error);
                    console.log('XHR:', xhr);

                    // エラー時もローディング非表示
                    $('#loading').hide();
                    $('#load-more-btn').show();
                    isLoading = false;
                }
            });
        }

        // スクロールイベント
        $(window).scroll(function() {
            if (isLoading || !hasMore) return;

            // ページ下部に到達したかチェック
            if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200) {
                loadMoreMovies();
            }
        });
    });
</script>
</body>
</html>
