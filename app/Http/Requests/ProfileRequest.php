<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:80'],
            'title' => ['required', 'string', 'max:80'],
            'hero_description' => ['required', 'string', 'max:300'],
            'biography' => ['required', 'string', 'max:1000'],
            'motivation' => ['nullable', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'github_url' => ['nullable', 'url:http,https', 'max:255'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:255'],
            'availability_enabled' => ['required', 'boolean'],
            'availability_text' => ['nullable', 'string', 'max:60'],
            'profile_image' => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'profile_image.max' => 'The portrait must not be larger than 2 MB.',
            'profile_image.mimes' => 'The portrait must be a PNG, JPG, JPEG or WEBP image.',
        ];
    }
}
