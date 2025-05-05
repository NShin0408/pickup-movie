@props(['title', 'movies', 'carouselId'])

@if(!empty($movies))
    <h2 class="text-2xl mt-7 mb-4">{{ $title }}</h2>
    <div class="relative overflow-hidden py-1.5 sm:py-2 sm:pr-2 touch-pan-x" id="{{ $carouselId }}">
        <div class="flex transition-transform duration-500 ease-in-out px-1">
            @foreach($movies as $movie)
                @if(!empty($movie['poster_path']))
                    <div class="flex-none w-[calc(25%+5px)] sm:w-[190px] cursor-pointer px-1 sm:px-2 md:px-3"
                         onclick="window.location.href='/movies/{{ $movie['id'] }}'">
                        <div class="relative transition-transform duration-200 hover:scale-105 movie-item">
                            <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}"
                                 alt="{{ $movie['title'] }}"
                                 class="w-full aspect-poster object-cover rounded-lg shadow-movie-poster"
                                 loading="lazy"
                            >
                            <div class="movie-title-overlay">
                                {{ $movie['title'] }}
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <button class="absolute top-1/2 left-1 -translate-y-1/2 w-10 h-10 bg-black/60 hover:bg-black/80 rounded-full items-center justify-center z-10 border-none text-white cursor-pointer hidden md:flex"
                data-carousel="{{ $carouselId }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
            </svg>
        </button>
        <button class="absolute top-1/2 right-1 -translate-y-1/2 w-10 h-10 bg-black/60 hover:bg-black/80 rounded-full items-center justify-center z-10 border-none text-white cursor-pointer hidden md:flex"
                data-carousel="{{ $carouselId }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
            </svg>
        </button>
    </div>
@endif
