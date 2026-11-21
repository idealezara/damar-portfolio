<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Message;

class MessageController extends Controller
{
    public function index()
    {
        return view('admin.messages.index', [
            'messages' => Message::latest()->paginate(10),
        ]);
    }

    public function show(Message $message)
    {
        return view('admin.messages.show', compact('message'));
    }

    /** Toggles between read and unread. */
    public function toggleRead(Message $message)
    {
        $message->update(['read_at' => $message->isRead() ? null : now()]);

        return back()->with('success', $message->isRead() ? 'Message marked as read.' : 'Message marked as unread.');
    }

    public function destroy(Message $message)
    {
        $message->delete();

        return redirect()->route('admin.messages.index')->with('success', 'Message deleted.');
    }
}
