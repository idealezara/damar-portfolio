<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:120'],
            'category' => ['nullable', 'string', 'max:60', 'regex:/[A-Za-z0-9]/'],
            'short_description' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:10000'],
            'technologies' => ['nullable', 'string', 'max:255'],
            'project_url' => ['nullable', 'url:http,https', 'max:255'],
            'github_url' => ['nullable', 'url:http,https', 'max:255'],
            'thumbnail' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_published' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'thumbnail.max' => 'The thumbnail must not be larger than 2 MB.',
            'thumbnail.mimes' => 'The thumbnail must be a JPG, JPEG, PNG or WEBP image.',
            'category.regex' => 'The category must contain letters or numbers.',
        ];
    }
}
