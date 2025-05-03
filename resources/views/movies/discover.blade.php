<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>映画リスト</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        html {
            overflow-y: scroll; /* 常にスクロールバーを表示して、幅の変化を防ぐ */
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: #000;
            color: #fff;
            font-family: 'Helvetica Neue', Arial, sans-serif;
            padding: 20px;
            width: 100%;
            overflow-x: hidden; /* 横スクロールを防止 */
        }

        .main-container {
            max-width: 1200px;
            width: 100%;
            margin: 0 auto;
            position: relative;
            display: block; /* flexではなくblockレイアウトに変更 */
        }

        .header {
            margin-bottom: 20px;
            padding: 0 10px;
            text-align: center;
            width: 100%;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        /* フィルターセクションを固定幅のブロックとして配置 */
        .filters-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 30px;
        }

        .filters-container {
            width: 800px; /* 固定幅 */
            max-width: 100%; /* モバイル対応 */
            min-height: 220px;
        }

        .filters {
            display: block; /* flexではなくblockに変更 */
            width: 100%;
        }

        .filter-section {
            margin-bottom: 15px;
            width: 100%;
            text-align: center;
        }

        .filter-title {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 8px;
            text-align: center;
        }

        .filter-options {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 10px;
            margin: 0 auto;
            width: 100%;
        }

        .category-tab {
            padding: 8px 16px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            font-size: 14px;
            transition: background-color 0.2s ease;
            display: inline-block;
        }

        .category-tab:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .category-tab.active {
            background-color: rgba(255, 255, 255, 0.4);
        }

        .language-option, .streaming-option {
            padding: 6px 12px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            font-size: 13px;
            transition: background-color 0.2s ease;
            display: inline-block;
        }

        .language-option:hover, .streaming-option:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .language-option.active, .streaming-option.active {
            background-color: rgba(255, 255, 255, 0.4);
        }

        /* コンテンツエリアも固定幅のブロックとして配置 */
        .content-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .content-container {
            width: 1100px; /* 固定幅 */
            max-width: 100%; /* モバイル対応 */
            min-height: 300px;
        }

        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); /* 画像サイズを大きく */
            gap: 20px; /* 間隔も少し広げる */
            width: 100%;
        }

        .movie-item {
            position: relative;
            transition: transform 0.2s ease;
            cursor: pointer;
        }

        .movie-item:hover {
            transform: scale(1.05);
        }

        .movie-poster {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
            border-radius: 6px; /* 角を少し丸く */
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4); /* シャドウを強調 */
        }

        .movie-title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px; /* パディングを増やす */
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.9)); /* グラデーションを強く */
            color: #fff;
            font-size: 13px; /* フォントサイズを少し大きく */
            border-radius: 0 0 6px 6px;
            opacity: 0;
            transition: opacity 0.2s ease;
        }

        .movie-item:hover .movie-title {
            opacity: 1;
        }

        .no-results {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
            text-align: center;
            padding: 50px 0;
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
            background-color: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .loading {
            text-align: center;
            padding: 20px;
            margin-top: 20px;
        }

        .loading-spinner {
            display: inline-block;
            width: 30px;
            height: 30px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .load-more-btn {
            display: block;
            margin: 20px auto;
            padding: 12px 24px;
            background-color: rgba(255, 255, 255, 0.2);
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.2s ease;
        }

        .load-more-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .attribution {
            width: 100%;
            font-size: 12px;
            color: rgba(255, 255, 255, 0.5);
            text-align: center;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .movie-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); /* モバイルでも少し大きく */
                gap: 15px;
            }
        }
    </style>
</head>
<body>
<div class="main-container">
    <div class="header">
        <h1>映画リスト</h1>
    </div>

    <div class="filters-wrapper">
        <div class="filters-container">
            <div class="filters">
                <div class="filter-section">
                    <div class="filter-title">カテゴリー</div>
                    <div class="filter-options">
                        <a href="/?category=popular&language={{ $currentLanguage }}&streaming={{ $currentStreaming }}" class="category-tab {{ $currentCategory === 'popular' ? 'active' : '' }}">
                            人気
                        </a>
                        <a href="/?category=top_rated&language={{ $currentLanguage }}&streaming={{ $currentStreaming }}" class="category-tab {{ $currentCategory === 'top_rated' ? 'active' : '' }}">
                            高評価
                        </a>
                        <a href="/?category=now_playing&language={{ $currentLanguage }}&streaming={{ $currentStreaming }}" class="category-tab {{ $currentCategory === 'now_playing' ? 'active' : '' }}">
                            上映中
                        </a>
                    </div>
                </div>

                <div class="filter-section">
                    <div class="filter-title">言語</div>
                    <div class="filter-options">
                        @foreach ($languages as $code => $name)
                            <a href="/?category={{ $currentCategory }}&language={{ $code }}&streaming={{ $currentStreaming }}"
                               class="language-option {{ $currentLanguage === $code ? 'active' : '' }}">
                                {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="filter-section">
                    <div class="filter-title">配信サービス</div>
                    <div class="filter-options">
                        @foreach ($streamingServices as $id => $name)
                            <a href="/?category={{ $currentCategory }}&language={{ $currentLanguage }}&streaming={{ $id }}"
                               class="streaming-option {{ (string)$currentStreaming === (string)$id ? 'active' : '' }}">
                                {{ $name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-wrapper">
        <div class="content-container">
            @if(count($movies) > 0)
                <div class="movie-grid" id="movie-grid">
                    @foreach ($movies as $movie)
                        @if($movie['poster_path'])
                            <div class="movie-item" data-id="{{ $movie['id'] }}">
                                <img
                                    src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                                    alt="{{ $movie['title'] }}"
                                    class="movie-poster"
                                    loading="lazy"
                                >
                                <div class="movie-title">{{ $movie['title'] }}</div>
                            </div>
                        @endif
                    @endforeach
                </div>

                <div class="loading" id="loading" style="display: none;">
                    <div class="loading-spinner"></div>
                </div>

                <button class="load-more-btn" id="load-more-btn">もっと見る</button>
            @else
                <div class="no-results">
                    <p>条件に一致する映画が見つかりませんでした</p>
                </div>
            @endif
        </div>
    </div>

    <div class="attribution">
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
        $(document).on('click', '.movie-item', function() {
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
                                    <div class="movie-item" data-id="${movie.id}">
                                        <img
                                            src="https://image.tmdb.org/t/p/w342${movie.poster_path}"
                                            alt="${movie.title}"
                                            class="movie-poster"
                                            loading="lazy"
                                        >
                                        <div class="movie-title">${movie.title}</div>
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
