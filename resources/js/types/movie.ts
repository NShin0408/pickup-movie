// 映画の型定義
export interface Movie {
  id: number;
  title: string;
  poster_path: string | null;
  overview?: string;
  release_date?: string;
  vote_average?: number;
  vote_count?: number;
}

// ロードモア用のレスポンス型
export interface LoadMoreResponse {
  movies: Movie[];
  hasMore: boolean;
}