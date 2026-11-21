@php($project = $project ?? null)
<form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="form card">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <x-field name="title" label="Title" :value="$project?->title" required maxlength="120" />

    <div class="form-row">
        <div class="field">
            <label for="category">Category</label>
            <input id="category" name="category" list="category-list" maxlength="60" value="{{ old('category', $project?->category?->name) }}" placeholder="e.g. Web App" @class(['is-invalid' => $errors->has('category')])>
            <datalist id="category-list">
                @foreach ($categories as $category)<option value="{{ $category->name }}">@endforeach
            </datalist>
            <p class="field__hint">Pick an existing category or type a new one.</p>
            @error('category')<p class="field__error">{{ $message }}</p>@enderror
        </div>

        <div class="field">
            <label for="is_published">Status</label>
            <select id="is_published" name="is_published">
                <option value="1" @selected((string) old('is_published', (int) ($project?->is_published ?? 1)) === '1')>Published</option>
                <option value="0" @selected((string) old('is_published', (int) ($project?->is_published ?? 1)) === '0')>Draft</option>
            </select>
            <p class="field__hint">Published projects appear on the public site immediately. Drafts stay hidden.</p>
            @error('is_published')<p class="field__error">{{ $message }}</p>@enderror
        </div>
    </div>

    <x-field name="short_description" label="Short description" :value="$project?->short_description" required maxlength="200" hint="Shown on project cards (max 200 characters)." />
    <x-field name="description" type="textarea" label="Full description" :value="$project?->description" :rows="8" required maxlength="10000" />
    <x-field name="technologies" label="Technologies used" :value="$project?->technologies" maxlength="255" placeholder="Laravel, PHP, MySQL" hint="Separate with commas." />

    <div class="form-row">
        <x-field name="project_url" type="url" label="Project URL" :value="$project?->project_url" placeholder="https://" />
        <x-field name="github_url" type="url" label="GitHub URL (optional)" :value="$project?->github_url" placeholder="https://github.com/..." />
    </div>

    <div class="field">
        <label for="thumbnail">Thumbnail</label>
        <div class="upload">
            <img id="thumb-preview" class="upload__preview" src="{{ $project?->thumbnail_url ?? asset('images/placeholder.svg') }}" alt="Thumbnail preview">
            <div>
                <input type="file" id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/webp" data-preview="#thumb-preview" data-max-mb="2">
                <p class="field__hint">JPG, PNG or WEBP, max 2 MB.@if ($project) Leave empty to keep the current image.@endif</p>
                <p class="field__error" data-upload-error hidden></p>
                @error('thumbnail')<p class="field__error">{{ $message }}</p>@enderror
            </div>
        </div>
    </div>

    <div class="form__actions">
        <a href="{{ route('admin.projects.index') }}" class="btn btn--light btn--sm">Cancel</a>
        <button type="submit" class="btn btn--dark btn--sm">Save Project</button>
    </div>
</form>
