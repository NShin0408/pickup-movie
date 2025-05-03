<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $movie['title'] ?? '映画詳細' }}</title>
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
            padding: 0;
            overflow-x: hidden;
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
        }

        .back-button {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: rgba(255, 255, 255, 0.1);
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        .back-button:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .movie-header {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            margin-bottom: 30px;
        }

        .movie-poster {
            flex: 0 0 300px;
        }

        .poster-image {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }

        .movie-info {
            flex: 1;
            min-width: 300px;
        }

        .movie-title {
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .movie-meta {
            margin-bottom: 20px;
            color: rgba(255, 255, 255, 0.7);
        }

        .movie-overview {
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .movie-genres {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 20px;
        }

        .genre-tag {
            background-color: rgba(255, 255, 255, 0.2);
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.9rem;
        }

        .section-title {
            font-size: 1.4rem;
            margin: 30px 0 15px 0;
        }

        .trailer-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9アスペクト比 */
            height: 0;
            overflow: hidden;
            max-width: 100%;
            margin-bottom: 30px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }

        .trailer-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: none;
        }

        .cast-list {
            display: flex;
            gap: 15px;
            overflow-x: auto;
            padding-bottom: 15px;
        }

        .cast-item {
            flex: 0 0 120px;
        }

        .cast-image {
            width: 100%;
            aspect-ratio: 2/3;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 8px;
        }

        .cast-name {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 4px;
        }

        .cast-character {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="/" class="back-button">← 一覧に戻る</a>
        
        @if(!empty($movie))
            <div class="movie-header">
                <div class="movie-poster">
                    @if(!empty($movie['poster_path']))
                        <img src="https://image.tmdb.org/t/p/w500{{ $movie['poster_path'] }}" alt="{{ $movie['title'] }}" class="poster-image">
                    @endif
                </div>
                
                <div class="movie-info">
                    <h1 class="movie-title">{{ $movie['title'] }}</h1>
                    
                    <div class="movie-meta">
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
                        <div class="movie-genres">
                            @foreach($movie['genres'] as $genre)
                                <span class="genre-tag">{{ $genre['name'] }}</span>
                            @endforeach
                        </div>
                    @endif
                    
                    @if(!empty($movie['overview']))
                        <p class="movie-overview">{{ $movie['overview'] }}</p>
                    @else
                        <p class="movie-overview">概要はありません</p>
                    @endif
                </div>
            </div>
            
            @if($trailerUrl)
                <h2 class="section-title">トレーラー</h2>
                <div class="trailer-container">
                    <iframe src="{{ $trailerUrl }}" title="YouTube trailer" allowfullscreen></iframe>
                </div>
            @endif
            
            @if(!empty($movie['credits']['cast']))
                <h2 class="section-title">キャスト</h2>
                <div class="cast-list">
                    @foreach(array_slice($movie['credits']['cast'], 0, 10) as $cast)
                        <div class="cast-item">
                            @if(!empty($cast['profile_path']))
                                <img src="https://image.tmdb.org/t/p/w185{{ $cast['profile_path'] }}" alt="{{ $cast['name'] }}" class="cast-image">
                            @else
                                <div class="cast-image" style="background-color: #333; display: flex; align-items: center; justify-content: center;">
                                    <span>No Image</span>
                                </div>
                            @endif
                            <div class="cast-name">{{ $cast['name'] }}</div>
                            <div class="cast-character">{{ $cast['character'] }}</div>
                        </div>
                    @endforeach
                </div>
            @endif
        @else
            <div style="text-align: center; padding: 50px 0;">
                <h1>映画情報が見つかりませんでした</h1>
            </div>
        @endif
    </div>
</body>
</html>