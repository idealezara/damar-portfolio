@php($skill = $skill ?? null)
@php($currentIcon = old('icon_class', $skill?->icon_class ?? ''))
<form method="POST" action="{{ $action }}" class="form card">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <x-field name="name" label="Skill name" :value="$skill?->name" required maxlength="60" />

    <div class="field">
        <label for="icon_class">Font Awesome icon class <span class="field__req" aria-hidden="true">*</span></label>
        <div class="icon-input">
            <span class="icon-preview" aria-hidden="true"><i id="icon-preview" class="{{ preg_match(\App\Models\Skill::ICON_PATTERN, (string) $currentIcon) ? $currentIcon : \App\Models\Skill::FALLBACK_ICON }}"></i></span>
            <input id="icon_class" name="icon_class" type="text" list="icon-suggestions" value="{{ $currentIcon }}"
                   placeholder="fa-brands fa-php" required maxlength="80" autocomplete="off" spellcheck="false"
                   data-icon-input data-icon-preview="#icon-preview" data-icon-message="#icon-message"
                   data-fallback="{{ \App\Models\Skill::FALLBACK_ICON }}"
                   @class(['is-invalid' => $errors->has('icon_class')])>
        </div>
        <datalist id="icon-suggestions">
            @foreach (\App\Models\Skill::ICON_SUGGESTIONS as $class => $label)
                <option value="{{ $class }}">{{ $label }}</option>
            @endforeach
        </datalist>
        <p id="icon-message" class="field__hint" role="status">Examples: fa-brands fa-php, fa-brands fa-laravel, fa-solid fa-database. Browse free icons at fontawesome.com/icons.</p>
        @error('icon_class')<p class="field__error">{{ $message }}</p>@enderror
    </div>

    <div class="form-row">
        <x-field name="category" label="Skill category (optional)" :value="$skill?->category" maxlength="40" placeholder="e.g. Backend" />
        <div class="field">
            <label for="level">Skill level (optional)</label>
            <select id="level" name="level" @class(['is-invalid' => $errors->has('level')])>
                <option value="">Not specified</option>
                @foreach (\App\Models\Skill::LEVELS as $level)
                    <option value="{{ $level }}" @selected(old('level', $skill?->level) === $level)>{{ $level }}</option>
                @endforeach
            </select>
            @error('level')<p class="field__error">{{ $message }}</p>@enderror
        </div>
    </div>

    <x-field name="sort_order" type="number" label="Display order" :value="$skill?->sort_order ?? 0" min="0" max="999" hint="Lower numbers appear first." />

    <div class="form__actions">
        <a href="{{ route('admin.skills.index') }}" class="btn btn--light btn--sm">Cancel</a>
        <button type="submit" class="btn btn--dark btn--sm">Save Skill</button>
    </div>
</form>
