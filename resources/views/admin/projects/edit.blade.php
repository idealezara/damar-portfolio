@extends('layouts.admin')
@section('title', 'Edit Project')

@section('content')
    <div class="page-head">
        <div>
            <h1>Edit Project</h1>
            <p class="muted">Update "{{ $project->title }}".</p>
        </div>
    </div>

    @include('admin.projects._form', ['project' => $project, 'action' => route('admin.projects.update', $project), 'method' => 'PUT'])
@endsection
