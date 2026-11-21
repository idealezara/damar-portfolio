@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
    <div class="page-head">
        <div>
            <h1>Dashboard</h1>
            <p class="muted">Overview of your portfolio</p>
        </div>
    </div>

    <div class="stats">
        <x-stat icon="folder" label="Total Projects" :value="$stats['projects']" />
        <x-stat icon="check" label="Published Projects" :value="$stats['published']" />
        <x-stat icon="message-square" label="Total Messages" :value="$stats['messages']" :note="$stats['unread'].' unread'" />
        <x-stat icon="layers" label="Total Skills" :value="$stats['skills']" />
    </div>

    <div class="grid-2">
        <section class="card">
            <h2 class="card__title">Recent Activity</h2>
            @forelse ($activities as $activity)
                <div class="activity">
                    <span class="activity__icon"><x-icon :name="$activity['icon']" /></span>
                    <div>
                        <p>{{ $activity['text'] }}</p>
                        <p class="muted">{{ $activity['at']->diffForHumans() }}</p>
                    </div>
                </div>
            @empty
                <p class="muted">No activity yet. Add your first project to get started.</p>
            @endforelse
        </section>

        <section class="card">
            <h2 class="card__title">Quick Actions</h2>
            <div class="quick">
                <a href="{{ route('admin.projects.create') }}" class="btn btn--dark btn--sm"><x-icon name="plus" /> Add Project</a>
                <a href="{{ route('admin.skills.create') }}" class="btn btn--light btn--sm"><x-icon name="plus" /> Add Skill</a>
                <a href="{{ route('admin.profile.edit') }}" class="btn btn--light btn--sm"><x-icon name="user" /> Edit Profile</a>
                <a href="{{ route('admin.messages.index') }}" class="btn btn--light btn--sm"><x-icon name="message-square" /> Messages</a>
            </div>
        </section>
    </div>
@endsection
