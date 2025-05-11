import { Movie, LoadMoreResponse } from '@/types/movie';

export class MovieList {
  private currentPage: number = 1;
  private isLoading: boolean = false;
  private hasMore: boolean = true;
  private readonly category: string;
  private readonly language: string;
  private readonly streaming: string;
  private readonly movieGridSelector: string = '#movie-grid';
  private readonly loadingSelector: string = '#loading';
  private scrollTriggerCount: number = 0;

  constructor(options: {
    category: string;
    language: string;
    streaming: string;
  }) {
    this.category = options.category;
    this.language = options.language;
    this.streaming = options.streaming;

    this.init();
  }

  private init(): void {
    // 映画アイテムのクリックイベント
    document.addEventListener('click', (e: MouseEvent) => {
      const target = e.target as HTMLElement;
      const movieItem = target.closest('[data-id]') as HTMLElement;

      if (movieItem) {
        const movieId = movieItem.getAttribute('data-id');
        if (movieId) {
          window.location.href = `/movies/${movieId}`;
        }
      }
    });

    // スクロールイベント
    window.addEventListener('scroll', this.handleScroll.bind(this));
  }

  private loadMoreMovies(): void {
    if (this.isLoading || !this.hasMore) return;

    this.isLoading = true;
    this.currentPage++;

    // ローディング表示
    const loading = document.querySelector(this.loadingSelector);

    if (loading) {
      loading.classList.remove('hidden');
    }

    // Fetch APIでリクエスト
    fetch(`/load-more?category=${this.category}&language=${this.language}&streaming=${this.streaming}&page=${this.currentPage}`)
      .then(response => {
        if (!response.ok) {
          throw new Error('Network response was not ok');
        }
        return response.json();
      })
      .then((data: LoadMoreResponse) => {
        if (data.movies && data.movies.length > 0) {
          this.appendMovies(data.movies);
          this.hasMore = data.hasMore;
        } else {
          this.hasMore = false;
        }
      })
      .catch(error => {
        console.error('Error loading more movies:', error);
      })
      .finally(() => {
        // ローディング非表示
        if (loading) {
          loading.classList.add('hidden');
        }

        this.isLoading = false;
      });
  }

  private appendMovies(movies: Movie[]): void {
    const movieGrid = document.querySelector(this.movieGridSelector);
    if (!movieGrid) return;

    movies.forEach(movie => {
      if (movie.poster_path) {
        const div = document.createElement('div');
        div.className = 'relative transition-transform duration-200 cursor-pointer hover:scale-105 movie-item';
        div.setAttribute('data-id', movie.id.toString());

        const img = document.createElement('img');
        img.src = `https://image.tmdb.org/t/p/w342${movie.poster_path}`;
        img.alt = movie.title;
        img.className = 'w-full aspect-poster object-cover rounded-lg shadow-movie-poster';
        const existingItems = movieGrid.querySelectorAll('.movie-item').length;
        img.loading = existingItems < 20 ? 'eager' : 'lazy';

        const overlay = document.createElement('div');
        overlay.className = 'movie-title-overlay';
        overlay.textContent = movie.title;

        div.appendChild(img);
        div.appendChild(overlay);

        movieGrid.appendChild(div);
      }
    });
  }

  private handleScroll(): void {
    this.scrollTriggerCount++;
    if (this.isLoading || !this.hasMore) return;

    // ページの閾値を超えたらロードを実行
    const scrollPosition = window.scrollY + window.innerHeight;
    const baseThreshold = this.scrollTriggerCount < 3 ? 0.45 : (this.scrollTriggerCount < 6 ? 0.5 : 0.8);
    const threshold = document.body.scrollHeight * baseThreshold;

    if (scrollPosition >= threshold) {
      this.loadMoreMovies();
    }
  }
}
