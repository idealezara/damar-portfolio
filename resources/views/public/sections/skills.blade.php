<section id="skills" class="section section--alt">
    <div class="container">
        <div class="reveal">
            <p class="eyebrow">Skills &amp; Technologies</p>
            <h2 class="section__title">What I work with</h2>
        </div>

        @if ($skills->isEmpty())
            <div class="empty reveal">
                <x-icon name="layers" />
                <h3>No skills listed yet</h3>
                <p>Skills added from the admin dashboard will appear here.</p>
            </div>
        @else
            <ul class="skills">
                @foreach ($skills as $skill)
                    <li class="skill reveal">
                        <span class="skill__icon"><i class="{{ $skill->safe_icon_class }}" data-skill-icon aria-hidden="true"></i></span>
                        <span class="skill__text">
                            <span class="skill__name">{{ $skill->name }}</span>
                            @if ($skill->category || $skill->level)
                                <span class="skill__meta">{{ collect([$skill->category, $skill->level])->filter()->implode(' · ') }}</span>
                            @endif
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</section>
