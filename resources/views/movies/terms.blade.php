<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>利用規約 | Pickup Movie</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if (app()->environment('development'))
        @vite(['resources/css/app.css'])
    @else
        @php
            $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
        @endphp
        <link rel="stylesheet" href="{{ secure_asset('build/' . $manifest['resources/css/app.css']['file']) }}">
    @endif
</head>
<body class="bg-movie-dark text-movie-light font-sans p-5">
<div class="max-w-[800px] mx-auto">
    <a href="/"
       class="inline-block my-5 py-2.5 px-5 bg-movie-panel text-movie-light no-underline rounded transition-colors hover:bg-movie-panel-hover">
        ← 一覧に戻る
    </a>

    <h1 class="text-2xl font-bold mb-4">利用規約</h1>
    <p class="mb-4">本サービス「Pickup Movie」は、ユーザーに対して映画情報を提供する検索サービスです。登録やログインは不要で、どなたでも自由にご利用いただけます。</p>

    <h2 class="text-xl font-semibold mt-6 mb-2">禁止事項</h2>
    <ul class="list-disc pl-5 mb-4">
        <li>本サービスの不正な改変・転載・複製・商用利用</li>
        <li>第三者の権利を侵害する行為</li>
    </ul>

    <h2 class="text-xl font-semibold mt-6 mb-2">免責事項</h2>
    <p class="mb-4">
        当サイトで提供される情報の正確性・完全性については保証しておらず、利用により生じたいかなる損害に対しても一切の責任を負いません。外部サービス（TMDb、JustWatch等）から取得している情報についても、最新性や正確性を保証するものではありません。
    </p>

    <p class="text-sm text-movie-muted mb-6">本規約は予告なく変更される場合があります。</p>
</div>
</body>
</html>
