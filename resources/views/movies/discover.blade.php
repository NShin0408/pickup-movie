<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>映画リスト</title>
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
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
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
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .placeholder {
            width: 100%;
            aspect-ratio: 2/3;
            background-color: #333;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.3);
        }

        .movie-title {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 8px;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            color: #fff;
            font-size: 12px;
            border-radius: 0 0 4px 4px;
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

        .reset-link {
            display: inline-block;
            margin-top: 15px;
            color: rgba(61, 164, 255, 0.8);
            text-decoration: none;
            padding: 6px 12px;
            border: 1px solid rgba(61, 164, 255, 0.4);
            border-radius: 4px;
            transition: all 0.2s ease;
        }

        .reset-link:hover {
            background-color: rgba(61, 164, 255, 0.1);
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
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 10px;
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
                <div class="movie-grid">
                    @foreach ($movies as $movie)
                        <div class="movie-item">
                            @if($movie['poster_path'])
                                <img
                                    src="https://image.tmdb.org/t/p/w300{{ $movie['poster_path'] }}"
                                    alt="{{ $movie['title'] }}"
                                    class="movie-poster"
                                    loading="lazy"
                                >
                            @else
                                <div class="placeholder" title="{{ $movie['title'] }}">
                                    画像なし
                                </div>
                            @endif
                            <div class="movie-title">{{ $movie['title'] }}</div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="no-results">
                    <p>条件に一致する映画が見つかりませんでした</p>
                    <a href="/" class="reset-link">すべての条件をリセット</a>
                </div>
            @endif
        </div>
    </div>

    <div class="attribution">
        映画情報提供元: TMDb<br>
        配信情報提供元: JustWatch
    </div>
</div>
</body>
</html>
