/** @type {import('tailwindcss').Config} */
module.exports = {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                'movie-dark': '#000',
                'movie-light': '#fff',
                'movie-gray': 'rgba(255, 255, 255, 0.7)',
                'movie-hover': 'rgba(255, 255, 255, 0.2)',
                'movie-active': 'rgba(255, 255, 255, 0.4)',
                'movie-muted': 'rgba(255, 255, 255, 0.5)',
                'movie-panel': 'rgba(255, 255, 255, 0.1)',
                'movie-panel-hover': 'rgba(255, 255, 255, 0.2)',
                'movie-panel-active': 'rgba(255, 255, 255, 0.4)',
            },
            boxShadow: {
                'movie-poster': '0 4px 12px rgba(0, 0, 0, 0.4)',
                'movie-service': '0 4px 8px rgba(0, 0, 0, 0.3)',
                'movie-trailer': '0 5px 15px rgba(0, 0, 0, 0.5)',
            },
            aspectRatio: {
                'poster': '2/3',
            },
        },
    },
    plugins: [],
}
