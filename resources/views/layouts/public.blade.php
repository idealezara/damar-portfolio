<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', $profile->full_name.' | '.$profile->title)</title>
    <meta name="description" content="{{ \Illuminate\Support\Str::limit($profile->hero_text, 150) }}">
    <script>document.documentElement.classList.add('js');</script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="site">
    @include('partials.public-header')

    <main>
        @yield('content')
    </main>

    @include('partials.public-footer')

    <dialog id="lightbox" class="lightbox" aria-label="Image preview">
        <button type="button" class="lightbox__close" data-lightbox-close aria-label="Close preview"><x-icon name="x" /></button>
        <img src="" alt="">
    </dialog>
</body>
</html>
