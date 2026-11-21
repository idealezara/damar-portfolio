<header class="header">
    <div class="container header__inner">
        <a href="{{ route('home') }}#home" class="brand" aria-label="{{ $profile->full_name }} - home">{{ $profile->initials }}</a>

        <nav class="nav" id="site-nav" aria-label="Main navigation">
            <a href="{{ route('home') }}#home" data-spy="home">Home</a>
            <a href="{{ route('home') }}#projects" data-spy="projects">Projects</a>
            <a href="{{ route('home') }}#about" data-spy="about">About</a>
            <a href="{{ route('home') }}#contact" data-spy="contact">Contact</a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn--dark btn--sm nav__cta">Dashboard <x-icon name="arrow-right" /></a>
        </nav>

        <button type="button" class="nav-toggle" data-nav-toggle aria-controls="site-nav" aria-expanded="false" aria-label="Toggle menu">
            <x-icon name="menu" />
        </button>
    </div>
</header>
