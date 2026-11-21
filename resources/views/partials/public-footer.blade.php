<footer class="footer">
    <div class="container footer__inner">
        <div>
            <a href="{{ route('home') }}#home" class="brand">{{ $profile->initials }}</a>
            <p class="footer__name">{{ $profile->full_name }}</p>
            <p class="footer__muted">{{ $profile->title }}</p>
        </div>

        <nav class="footer__nav" aria-label="Footer navigation">
            <a href="{{ route('home') }}#home">Home</a>
            <a href="{{ route('home') }}#projects">Projects</a>
            <a href="{{ route('home') }}#about">About</a>
            <a href="{{ route('home') }}#contact">Contact</a>
        </nav>

        <div class="social">
            @if ($profile->github_url)<a href="{{ $profile->github_url }}" target="_blank" rel="noopener noreferrer" aria-label="GitHub"><x-icon name="github" /></a>@endif
            @if ($profile->instagram_url)<a href="{{ $profile->instagram_url }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><x-icon name="instagram" /></a>@endif
            @if ($profile->email)<a href="mailto:{{ $profile->email }}" aria-label="Email"><x-icon name="mail" /></a>@endif
        </div>
    </div>
    <div class="container footer__legal">
        <p>&copy; {{ date('Y') }} {{ $profile->full_name }}. All rights reserved.</p>
    </div>
</footer>
