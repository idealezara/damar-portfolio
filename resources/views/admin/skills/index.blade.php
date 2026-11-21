@extends('layouts.admin')
@section('title', 'Skills')

@section('content')
    <div class="page-head">
        <div>
            <h1>Manage Skills</h1>
            <p class="muted">Skills shown in the Skills &amp; Technologies section.</p>
        </div>
        <a href="{{ route('admin.skills.create') }}" class="btn btn--dark btn--sm"><x-icon name="plus" /> Add Skill</a>
    </div>

    <div class="card card--flush">
        <div class="table-wrap">
            <table class="table table--cards table--thumb">
                <thead><tr><th>Icon</th><th>Name</th><th class="col-hide-sm">Category</th><th class="col-hide-sm">Level</th><th class="col-hide-sm">Order</th><th class="right">Actions</th></tr></thead>
                <tbody>
                @forelse ($skills as $skill)
                    <tr>
                        <td class="td-thumb"><span class="activity__icon"><i class="{{ $skill->safe_icon_class }}" data-skill-icon aria-hidden="true"></i></span></td>
                        <td>
                            <span class="table__title">{{ $skill->name }}</span>
                            <p class="muted code">{{ $skill->safe_icon_class }}</p>
                        </td>
                        <td class="col-hide-sm">{{ $skill->category ?: '-' }}</td>
                        <td class="col-hide-sm">{{ $skill->level ?: '-' }}</td>
                        <td class="col-hide-sm">{{ $skill->sort_order }}</td>
                        <td class="right">
                            <div class="actions">
                                <a href="{{ route('admin.skills.edit', $skill) }}" class="icon-btn" aria-label="Edit {{ $skill->name }}"><x-icon name="pencil" /></a>
                                <form method="POST" action="{{ route('admin.skills.destroy', $skill) }}" data-confirm="Delete the skill &quot;{{ $skill->name }}&quot;?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-btn icon-btn--danger" aria-label="Delete {{ $skill->name }}"><x-icon name="trash" /></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="table__empty">No skills yet. <a href="{{ route('admin.skills.create') }}">Add one</a>.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
