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
  private readonly loadMoreBtnSelector: string = '#load-more-btn';

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
    // もっと見るボタンのクリックイベント
    const loadMoreBtn = document.querySelector(this.loadMoreBtnSelector);
    if (loadMoreBtn) {
      loadMoreBtn.addEventListener('click', this.loadMoreMovies.bind(this));
    }

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
    const loadMoreBtn = document.querySelector(this.loadMoreBtnSelector);

    if (loading) {
      loading.classList.remove('hidden');
    }

    if (loadMoreBtn) {
      loadMoreBtn.classList.add('hidden');
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

        // もっと見るボタンの表示/非表示
        if (loadMoreBtn) {
          if (this.hasMore) {
            loadMoreBtn.classList.remove('hidden');
          } else {
            loadMoreBtn.classList.add('hidden');
          }
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

        div.innerHTML = `
          <img
            src="https://image.tmdb.org/t/p/w342${movie.poster_path}"
            alt="${movie.title}"
            class="w-full aspect-poster object-cover rounded-lg shadow-movie-poster"
            loading="lazy"
          >
          <div class="movie-title-overlay">
            ${movie.title}
          </div>
        `;

        movieGrid.appendChild(div);
      }
    });
  }

  private handleScroll(): void {
    if (this.isLoading || !this.hasMore) return;

    // ページ下部に到達したかチェック
    if ((window.innerHeight + window.scrollY) >= document.body.offsetHeight - 200) {
      this.loadMoreMovies();
    }
  }
}
