@extends('layouts.public')
@section('title', $project->title.' | '.$profile->full_name)

@section('content')
    <section class="section project-detail">
        <div class="container">
            <a href="{{ route('home') }}#projects" class="back-link"><x-icon name="arrow-left" /> Back to Projects</a>

            @unless ($project->is_published)
                <x-alert type="error">This project is a draft. Only signed-in admins can see this preview.</x-alert>
            @endunless

            <div class="project-detail__grid">
                <div class="project-detail__info">
                    @if ($project->category)<span class="tag">{{ $project->category->name }}</span>@endif
                    <h1 class="project-detail__title">{{ $project->title }}</h1>
                    <p class="section__lead">{{ $project->short_description }}</p>

                    @if (count($project->technology_list))
                        <ul class="chips" aria-label="Technologies used">
                            @foreach ($project->technology_list as $tech)
                                <li>{{ $tech }}</li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="prose">{!! nl2br(e($project->description)) !!}</div>

                    <p class="muted">Added {{ $project->created_at->format('F j, Y') }}</p>

                    <div class="hero__actions">
                        @if ($project->project_url)
                            <a href="{{ $project->project_url }}" class="btn btn--dark" target="_blank" rel="noopener noreferrer">View Live Demo <x-icon name="arrow-up-right" /></a>
                        @endif
                        @if ($project->github_url)
                            <a href="{{ $project->github_url }}" class="btn btn--light" target="_blank" rel="noopener noreferrer"><x-icon name="github" /> Source Code</a>
                        @endif
                    </div>
                </div>

                <figure class="project-detail__media">
                    <button type="button" data-lightbox aria-label="Open image preview">
                        <img src="{{ $project->thumbnail_url }}" alt="{{ $project->title }} preview"
                             onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
                    </button>
                    <figcaption>Click the image to enlarge</figcaption>
                </figure>
            </div>
        </div>
    </section>
@endsection
