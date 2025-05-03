module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                // 現在使用している色をここでカスタマイズ
                'movie-dark': '#000',
                'movie-text': '#fff',
                'movie-text-secondary': 'rgba(255, 255, 255, 0.7)',
                'movie-bg-hover': 'rgba(255, 255, 255, 0.2)',
                'movie-bg-active': 'rgba(255, 255, 255, 0.4)',
            },
        },
    },
    plugins: [],
}