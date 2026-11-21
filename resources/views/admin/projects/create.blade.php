@extends('layouts.admin')
@section('title', 'Add Project')

@section('content')
    <div class="page-head">
        <div>
            <h1>Add New Project</h1>
            <p class="muted">Fill in the details below.</p>
        </div>
    </div>

    @include('admin.projects._form', ['project' => null, 'action' => route('admin.projects.store'), 'method' => 'POST'])
@endsection
