<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Message;
use Illuminate\Contracts\Encryption\DecryptException;

class ContactController extends Controller
{
    public function store(ContactRequest $request)
    {
        $success = 'Thank you! Your message has been sent.';

        // Bots that fill the hidden field, or submit faster than a human could,
        // get the same friendly response but nothing is saved.
        if ($request->filled('website') || $this->submittedTooFast($request)) {
            return redirect(route('home').'#contact')->with('success', $success);
        }

        Message::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'subject' => $request->validated('subject'),
            'body' => $request->validated('message'),
        ]);

        return redirect(route('home').'#contact')->with('success', $success);
    }

    private function submittedTooFast(ContactRequest $request): bool
    {
        try {
            $startedAt = (int) decrypt((string) $request->input('started_at'));
        } catch (DecryptException) {
            return true; // missing or tampered token
        }

        return now()->timestamp - $startedAt < 3;
    }
}
