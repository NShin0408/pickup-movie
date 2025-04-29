<h1>検索結果</h1>

<ul>
    @foreach ($movies as $movie)
        <li>
            <strong>{{ $movie['title'] }}</strong><br>
            公開日: {{ $movie['release_date'] ?? '不明' }}<br>
            <img src="https://image.tmdb.org/t/p/w200{{ $movie['poster_path'] }}" alt="{{ $movie['title'] }}">
        </li>
    @endforeach
</ul>
