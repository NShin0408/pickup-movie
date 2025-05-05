@props(['title', 'services', 'link'])

<div class="mb-5">
    <div class="text-base text-movie-gray mb-2.5">{{ $title }}</div>
    <div class="flex flex-wrap gap-4">
        @foreach($services as $service)
            <a href="{{ $link }}"
               class="group relative w-[60px] h-[60px] rounded-xl overflow-hidden shadow-movie-service transition-all duration-300 hover:scale-110 hover:shadow-lg"
               target="_blank"
               title="{{ $service['provider_name'] }}で{{ $title === 'レンタル' ? 'レンタル' : '視聴' }}する">
                <img src="https://image.tmdb.org/t/p/original{{ $service['logo_path'] }}"
                     alt="{{ $service['provider_name'] }}"
                     class="w-full h-full object-cover">
            </a>
        @endforeach
    </div>
</div>
