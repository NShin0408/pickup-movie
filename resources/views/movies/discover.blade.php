<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>映画リスト</title>
    <style>
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
        }

        .header {
            margin-bottom: 20px;
            padding: 0 10px;
            text-align: center;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .filters {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
        }

        .filter-section {
            margin-bottom: 15px;
        }

        .filter-title {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 8px;
            text-align: center;
        }

        .category-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .category-tab {
            padding: 8px 16px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }

        .category-tab:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .category-tab.active {
            background-color: rgba(255, 255, 255, 0.4); /* より白っぽい背景 */
        }

        .language-filter, .streaming-filter {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .language-option, .streaming-option {
            padding: 6px 12px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            text-decoration: none;
            color: #fff;
            font-size: 13px;
            transition: background-color 0.2s ease;
        }

        .language-option:hover, .streaming-option:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .language-option.active, .streaming-option.active {
            background-color: rgba(255, 255, 255, 0.4); /* より白っぽい背景 */
        }

        .movie-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 15px;
            margin: 0 auto;
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

        .attribution {
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

            .category-tabs, .language-filter, .streaming-filter {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
<div class="header">
    <h1>映画リスト</h1>

    <div class="filters">
        <div class="filter-section">
            <div class="filter-title">カテゴリー</div>
            <div class="category-tabs">
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
            <div class="language-filter">
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
            <div class="streaming-filter">
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

<div class="attribution">
    映画情報提供元: TMDb<br>
    配信情報提供元: JustWatch
</div>
</body>
</html>
