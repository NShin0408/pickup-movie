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

        .category-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
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
            background-color: rgba(255, 255, 255, 0.3);
            font-weight: bold;
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

        @media (max-width: 768px) {
            .movie-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 10px;
            }

            .category-tabs {
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
<div class="header">
    <h1>映画リスト</h1>

    <div class="category-tabs">
        <a href="/?category=popular" class="category-tab {{ $currentCategory === 'popular' ? 'active' : '' }}">
            人気
        </a>
        <a href="/?category=top_rated" class="category-tab {{ $currentCategory === 'top_rated' ? 'active' : '' }}">
            高評価
        </a>
        <a href="/?category=now_playing" class="category-tab {{ $currentCategory === 'now_playing' ? 'active' : '' }}">
            上映中
        </a>
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
                    title="{{ $movie['title'] }}"
                >
            @else
                <div class="placeholder" title="{{ $movie['title'] }}">
                    画像なし
                </div>
            @endif
        </div>
    @endforeach
</div>
</body>
</html>
