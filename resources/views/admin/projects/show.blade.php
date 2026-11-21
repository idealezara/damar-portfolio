@extends('layouts.admin')
@section('title', $project->title)

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $project->title }}</h1>
            <p class="muted"><x-status-badge :published="$project->is_published" /> &nbsp; Created {{ $project->created_at->format('M j, Y') }}</p>
        </div>
        <div class="actions">
            <a href="{{ route('projects.show', $project->slug) }}" target="_blank" rel="noopener" class="btn btn--light btn--sm">Open public page <x-icon name="external-link" /></a>
            <a href="{{ route('admin.projects.edit', $project) }}" class="btn btn--dark btn--sm"><x-icon name="pencil" /> Edit</a>
        </div>
    </div>

    <div class="card detail">
        <img class="detail__image" src="{{ $project->thumbnail_url }}" alt="{{ $project->title }}" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'">
        <dl>
            <dt>Category</dt><dd>{{ $project->category?->name ?? '-' }}</dd>
            <dt>Short description</dt><dd>{{ $project->short_description }}</dd>
            <dt>Technologies</dt><dd>{{ $project->technologies ?: '-' }}</dd>
            <dt>Project URL</dt><dd>@if ($project->project_url)<a href="{{ $project->project_url }}" target="_blank" rel="noopener noreferrer">{{ $project->project_url }}</a>@else - @endif</dd>
            <dt>GitHub URL</dt><dd>@if ($project->github_url)<a href="{{ $project->github_url }}" target="_blank" rel="noopener noreferrer">{{ $project->github_url }}</a>@else - @endif</dd>
            <dt>Description</dt><dd>{!! nl2br(e($project->description)) !!}</dd>
        </dl>
    </div>

    <a href="{{ route('admin.projects.index') }}" class="back-link"><x-icon name="arrow-left" /> Back to projects</a>
@endsection
