<section id="home" class="hero">
    <div class="container hero__grid">
        <div class="hero__text reveal">
            @if ($profile->availability_enabled && $profile->availability_text)
                <p class="status"><span class="status__dot" aria-hidden="true"></span>{{ $profile->availability_text }}</p>
            @endif

            <p class="hero__hello">Hello, I'm</p>
            <h1 class="hero__name">
                <span class="outline">{{ $profile->first_name }}</span>
                @if ($profile->last_name)<span class="solid">{{ $profile->last_name }}</span>@endif
            </h1>
            <p class="hero__title">{{ $profile->title }}</p>
            <p class="hero__intro">{{ $profile->hero_text }}</p>

            <div class="hero__actions">
                <a href="#projects" class="btn btn--dark">View Projects <x-icon name="arrow-right" /></a>
                <a href="#contact" class="btn btn--light">Contact Me</a>
            </div>

            <a href="#projects" class="scroll-hint"><span></span>Scroll to explore</a>
        </div>

        <div class="hero__portrait reveal">
            <img src="{{ $profile->portrait_url }}" alt="Portrait of {{ $profile->full_name }}"
                 width="560" height="700" fetchpriority="high"
                 onerror="this.onerror=null;this.src='{{ asset('images/portrait-default.png') }}'">
        </div>
    </div>
</section>
