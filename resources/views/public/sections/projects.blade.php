<section id="projects" class="section section--alt">
    <div class="container">
        <div class="reveal">
            <p class="eyebrow">My Projects</p>
            <h2 class="section__title">Featured Projects</h2>
            <p class="section__lead">A selection of projects I have built.</p>
        </div>

        @if ($draftCount > 0)
            <p class="admin-note">Admin notice: {{ $draftCount }} draft {{ \Illuminate\Support\Str::plural('project', $draftCount) }} hidden from visitors. Publish {{ $draftCount === 1 ? 'it' : 'them' }} in <a href="{{ route('admin.projects.index', ['status' => 'draft']) }}">Admin &gt; Projects</a>.</p>
        @endif

        @if ($projects->isEmpty())
            <div class="empty reveal">
                <x-icon name="folder" />
                <h3>No projects yet</h3>
                <p>Projects added from the admin dashboard will appear here.</p>
            </div>
        @else
            @if ($categories->isNotEmpty())
                <div class="filters" role="group" aria-label="Filter projects by category">
                    <button type="button" class="filter is-active" data-filter="all">All</button>
                    @foreach ($categories as $category)
                        <button type="button" class="filter" data-filter="{{ $category->slug }}">{{ $category->name }}</button>
                    @endforeach
                </div>
            @endif

            <div class="project-grid" id="project-grid">
                @foreach ($projects as $project)
                    <a href="{{ route('projects.show', $project->slug) }}" class="project-card reveal"
                       data-category="{{ $project->category?->slug ?? 'uncategorized' }}">
                        <div class="project-card__media">
                            <img src="{{ $project->thumbnail_url }}" alt="{{ $project->title }} thumbnail" loading="lazy"
                                 onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
                        </div>
                        <div class="project-card__body">
                            @if ($project->category)<span class="tag">{{ $project->category->name }}</span>@endif
                            <h3>{{ $project->title }}</h3>
                            <p>{{ $project->short_description }}</p>
                            <span class="project-card__arrow"><x-icon name="arrow-up-right" /></span>
                        </div>
                    </a>
                @endforeach
            </div>

            <p class="empty-filter" id="filter-empty" hidden>No projects in this category yet.</p>
        @endif
    </div>
</section>
