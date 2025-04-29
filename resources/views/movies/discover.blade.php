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
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 0 10px;
        }

        .header h1 {
            font-size: 24px;
            font-weight: 500;
        }

        .search-form {
            display: flex;
            align-items: center;
        }

        .search-input {
            background-color: rgba(255, 255, 255, 0.2);
            border: none;
            border-radius: 4px;
            color: #fff;
            padding: 8px 12px;
            font-size: 14px;
            outline: none;
            width: 250px;
        }

        .search-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
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

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .search-form {
                width: 100%;
            }

            .search-input {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>映画リスト</h1>
        <form action="/movies" method="GET" class="search-form">
            <input type="text" name="query" placeholder="映画を検索..." class="search-input">
        </form>
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
