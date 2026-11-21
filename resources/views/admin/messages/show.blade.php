@extends('layouts.admin')
@section('title', 'Message')

@section('content')
    <div class="page-head">
        <div>
            <h1>{{ $message->subject }}</h1>
            <p class="muted">From {{ $message->name }} &middot; {{ $message->created_at->format('F j, Y H:i') }}</p>
        </div>
        <div class="actions">
            <form method="POST" action="{{ route('admin.messages.read', $message) }}">
                @csrf @method('PATCH')
                <button type="submit" class="btn btn--light btn--sm">{{ $message->isRead() ? 'Mark as unread' : 'Mark as read' }}</button>
            </form>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Delete this message from {{ $message->name }}?">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn--danger btn--sm"><x-icon name="trash" /> Delete</button>
            </form>
        </div>
    </div>

    <div class="card detail detail--text">
        <dl>
            <dt>Name</dt><dd>{{ $message->name }}</dd>
            <dt>Email</dt><dd><a href="mailto:{{ $message->email }}">{{ $message->email }}</a></dd>
            <dt>Status</dt><dd>{{ $message->isRead() ? 'Read' : 'Unread' }}</dd>
            <dt>Message</dt><dd>{!! nl2br(e($message->body)) !!}</dd>
        </dl>
    </div>

    <a href="{{ route('admin.messages.index') }}" class="back-link"><x-icon name="arrow-left" /> Back to messages</a>
@endsection
