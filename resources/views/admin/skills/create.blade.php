@extends('layouts.admin')
@section('title', 'Add Skill')

@section('content')
    <div class="page-head"><div><h1>Add Skill</h1><p class="muted">Add a technology to your skills list.</p></div></div>
    @include('admin.skills._form', ['skill' => null, 'action' => route('admin.skills.store'), 'method' => 'POST'])
@endsection
