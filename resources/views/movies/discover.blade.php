<h1>人気の映画（日本語）</h1>

<ul>
    @foreach ($movies as $movie)
        <li>
            <strong>{{ $movie['title'] }}</strong><br>
            <img src="https://image.tmdb.org/t/p/w200{{ $movie['poster_path'] }}" alt="{{ $movie['title'] }}">
        </li>
    @endforeach
</ul>
