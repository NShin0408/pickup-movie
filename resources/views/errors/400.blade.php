<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>400 Bad Request - Pickup Movie</title>
    <meta name="robots" content="noindex, nofollow">
    @if (app()->environment('development'))
        @vite(['resources/css/app.css', 'resources/js/app.ts'])
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ asset('favicon/site.webmanifest') }}">
        <link rel="shortcut icon" href="{{ asset('favicon/favicon.ico') }}">
    @else
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        @endphp
        <link rel="stylesheet" href="{{ secure_asset('build/' . $manifest['resources/css/app.css']['file']) }}">
        <script type="module" src="{{ secure_asset('build/' . $manifest['resources/js/app.ts']['file']) }}"></script>
        <link rel="apple-touch-icon" sizes="180x180" href="{{ secure_asset('favicon/apple-touch-icon.png') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ secure_asset('favicon/favicon-32x32.png') }}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{ secure_asset('favicon/favicon-16x16.png') }}">
        <link rel="manifest" href="{{ secure_asset('favicon/site.webmanifest') }}">
        <link rel="shortcut icon" href="{{ secure_asset('favicon/favicon.ico') }}">
    @endif
</head>
<body class="bg-movie-dark text-movie-light font-sans px-3 sm:px-5 pt-10 pb-10 w-full overflow-x-hidden">
<div class="max-w-[700px] mx-auto text-center">
    <div class="mb-8">
        <img src="{{ asset('images/logo.png') }}" alt="Pickup Movie ロゴ" class="mx-auto w-[180px] sm:w-[220px] h-auto">
    </div>

    <h1 class="text-4xl sm:text-5xl font-bold mb-4 text-movie-light">400 Bad Request</h1>
    <p class="text-lg text-movie-gray mb-6">リクエストに誤りがあります。URLやパラメータをご確認ください。</p>

    <a href="{{ url('/') }}"
       class="inline-block py-3 px-6 bg-movie-panel text-movie-light rounded transition-colors hover:bg-movie-panel-hover text-base">
        トップページに戻る
    </a>
</div>
<footer class="w-full text-center text-sm text-movie-muted mt-10 pt-10 border-t border-movie-panel">
    <div class="flex justify-center gap-5">
        <a href="/terms" class="hover:underline">利用規約</a>
        <a href="/privacy" class="hover:underline">プライバシーポリシー</a>
    </div>
    <div class="mt-3">&copy; {{ date('Y') }} Pickup Movie</div>
</footer>
</body>
</html>
