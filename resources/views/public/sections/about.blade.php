<section id="about" class="section about">
    <div class="container about__grid">
        <div class="about__text reveal">
            <p class="eyebrow">About Me</p>
            <h2 class="about__heading">{{ \Illuminate\Support\Str::ucfirst($profile->motivation ?: $profile->title) }}</h2>
            <p class="about__bio">{{ $profile->biography }}</p>

            <ul class="about__links">
                @if ($profile->github_url)
                    <li><a href="{{ $profile->github_url }}" target="_blank" rel="noopener noreferrer"><x-icon name="github" /> GitHub</a></li>
                @endif
                @if ($profile->instagram_url)
                    <li><a href="{{ $profile->instagram_url }}" target="_blank" rel="noopener noreferrer"><x-icon name="instagram" /> Instagram</a></li>
                @endif
                @if ($profile->email)
                    <li><a href="mailto:{{ $profile->email }}"><x-icon name="mail" /> {{ $profile->email }}</a></li>
                @endif
            </ul>
        </div>

        <div class="about__portrait reveal">
            <img src="{{ $profile->portrait_url }}" alt="{{ $profile->full_name }}" loading="lazy"
                 onerror="this.onerror=null;this.src='{{ asset('images/portrait-default.png') }}'">
        </div>
    </div>
</section>
