@extends('layouts.admin')
@section('title', 'Edit Skill')

@section('content')
    <div class="page-head"><div><h1>Edit Skill</h1><p class="muted">Update "{{ $skill->name }}".</p></div></div>
    @include('admin.skills._form', ['skill' => $skill, 'action' => route('admin.skills.update', $skill), 'method' => 'PUT'])
@endsection
