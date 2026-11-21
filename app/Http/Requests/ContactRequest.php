<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    /** On validation errors, return to the contact section instead of the page top. */
    protected $redirect = '/#contact';

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:80'],
            'email' => ['required', 'email', 'max:255'],
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            // Spam traps: honeypot must stay empty, timer is checked in the controller.
            'website' => ['nullable', 'string'],
            'started_at' => ['nullable', 'string'],
        ];
    }
}
