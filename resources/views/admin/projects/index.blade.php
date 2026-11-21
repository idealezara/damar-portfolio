@extends('layouts.admin')
@section('title', 'Projects')

@section('content')
    <div class="page-head">
        <div>
            <h1>Manage Projects</h1>
            <p class="muted">Manage your portfolio projects.</p>
        </div>
        <a href="{{ route('admin.projects.create') }}" class="btn btn--dark btn--sm"><x-icon name="plus" /> Add Project</a>
    </div>

    <form method="GET" action="{{ route('admin.projects.index') }}" class="toolbar">
        <div class="search">
            <x-icon name="search" />
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Search projects" aria-label="Search projects">
        </div>
        <select name="status" aria-label="Filter by status">
            <option value="">All statuses</option>
            <option value="published" @selected(request('status') === 'published')>Published</option>
            <option value="draft" @selected(request('status') === 'draft')>Draft</option>
        </select>
        <button type="submit" class="btn btn--light btn--sm">Filter</button>
        @if (request()->hasAny(['q', 'status']))<a href="{{ route('admin.projects.index') }}" class="muted">Clear</a>@endif
    </form>

    <div class="card card--flush">
        <div class="table-wrap">
            <table class="table table--cards table--thumb">
                <thead>
                    <tr><th>Image</th><th>Title</th><th class="col-hide-sm">Category</th><th>Status</th><th class="right">Actions</th></tr>
                </thead>
                <tbody>
                @forelse ($projects as $project)
                    <tr>
                        <td class="td-thumb"><img class="thumb" src="{{ $project->thumbnail_url }}" alt="" onerror="this.onerror=null;this.src='{{ asset('images/placeholder.svg') }}'"></td>
                        <td>
                            <a href="{{ route('admin.projects.show', $project) }}" class="table__title">{{ $project->title }}</a>
                            <p class="muted table__sub">{{ \Illuminate\Support\Str::limit($project->short_description, 60) }}</p>
                        </td>
                        <td class="col-hide-sm">{{ $project->category?->name ?? '-' }}</td>
                        <td><x-status-badge :published="$project->is_published" /></td>
                        <td class="right">
                            <div class="actions">
                                <a href="{{ route('admin.projects.show', $project) }}" class="icon-btn" aria-label="View {{ $project->title }}"><x-icon name="eye" /></a>
                                <form method="POST" action="{{ route('admin.projects.status', $project) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="icon-btn" aria-label="{{ $project->is_published ? 'Unpublish' : 'Publish' }} {{ $project->title }}" title="{{ $project->is_published ? 'Unpublish (hide from site)' : 'Publish (show on site)' }}"><x-icon :name="$project->is_published ? 'x' : 'check'" /></button>
                                </form>
                                <a href="{{ route('admin.projects.edit', $project) }}" class="icon-btn" aria-label="Edit {{ $project->title }}"><x-icon name="pencil" /></a>
                                <form method="POST" action="{{ route('admin.projects.destroy', $project) }}" data-confirm="Delete &quot;{{ $project->title }}&quot;? This also removes its image and cannot be undone.">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-btn icon-btn--danger" aria-label="Delete {{ $project->title }}"><x-icon name="trash" /></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="table__empty">No projects found. <a href="{{ route('admin.projects.create') }}">Add your first project</a>.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $projects->links() }}
@endsection
