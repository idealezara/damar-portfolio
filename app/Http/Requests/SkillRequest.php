<?php

namespace App\Http\Requests;

use App\Models\Skill;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->is_admin === true;
    }

    protected function prepareForValidation(): void
    {
        // Tidy spacing/case so "FA-Brands   fa-php " still works.
        $this->merge([
            'icon_class' => Str::lower(preg_replace('/\s+/', ' ', trim((string) $this->input('icon_class')))),
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:60'],
            'icon_class' => ['required', 'string', 'max:80', 'regex:'.Skill::ICON_PATTERN],
            'category' => ['nullable', 'string', 'max:40'],
            'level' => ['nullable', Rule::in(Skill::LEVELS)],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return [
            'icon_class.required' => 'Enter a Font Awesome icon class, for example fa-brands fa-php.',
            'icon_class.regex' => 'Use one style prefix (fa-solid, fa-regular or fa-brands) followed by one icon name, for example "fa-brands fa-php" or "fa-solid fa-database".',
            'level.in' => 'Choose one of the listed skill levels.',
        ];
    }
}
