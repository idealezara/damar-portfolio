@extends('layouts.admin')
@section('title', 'Messages')

@section('content')
    <div class="page-head">
        <div>
            <h1>Messages</h1>
            <p class="muted">Messages sent through the contact form.</p>
        </div>
    </div>

    <div class="card card--flush">
        <div class="table-wrap">
            <table class="table table--cards">
                <thead><tr><th>From</th><th>Subject</th><th>Received</th><th>Status</th><th class="right">Actions</th></tr></thead>
                <tbody>
                @forelse ($messages as $message)
                    <tr @class(['is-unread' => ! $message->isRead()])>
                        <td>
                            <a href="{{ route('admin.messages.show', $message) }}" class="table__title">{{ $message->name }}</a>
                            <p class="muted">{{ $message->email }}</p>
                        </td>
                        <td>{{ \Illuminate\Support\Str::limit($message->subject, 50) }}</td>
                        <td>{{ $message->created_at->format('M j, Y H:i') }}</td>
                        <td><span class="badge {{ $message->isRead() ? 'badge--outline' : 'badge--solid' }}">{{ $message->isRead() ? 'Read' : 'Unread' }}</span></td>
                        <td class="right">
                            <div class="actions">
                                <a href="{{ route('admin.messages.show', $message) }}" class="icon-btn" aria-label="View message"><x-icon name="eye" /></a>
                                <form method="POST" action="{{ route('admin.messages.read', $message) }}">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="icon-btn" aria-label="{{ $message->isRead() ? 'Mark as unread' : 'Mark as read' }}" title="{{ $message->isRead() ? 'Mark as unread' : 'Mark as read' }}"><x-icon :name="$message->isRead() ? 'mail' : 'mail-open'" /></button>
                                </form>
                                <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" data-confirm="Delete this message from {{ $message->name }}?">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="icon-btn icon-btn--danger" aria-label="Delete message"><x-icon name="trash" /></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="table__empty">No messages yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $messages->links() }}
@endsection
