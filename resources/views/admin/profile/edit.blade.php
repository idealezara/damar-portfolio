@extends('layouts.admin')
@section('title', 'Profile')

@section('content')
    <div class="page-head">
        <div>
            <h1>Profile</h1>
            <p class="muted">These details appear on the public portfolio.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.profile.update') }}" enctype="multipart/form-data" class="form card">
        @csrf @method('PUT')

        <div class="form-row">
            <x-field name="full_name" label="Full name" :value="$setting->full_name" required maxlength="80" />
            <x-field name="title" label="Professional title" :value="$setting->title" required maxlength="80" hint="Shown in the Hero section." />
        </div>

        <x-field name="hero_description" type="textarea" label="Hero description" :value="$setting->hero_text" :rows="3" required maxlength="300" hint="Short intro shown only in the Hero section on the landing page (max 300 characters)." />
        <x-field name="biography" type="textarea" label="About biography" :value="$setting->biography" :rows="5" required maxlength="1000" hint="Shown only in the About section." />
        <x-field name="motivation" label="Motivation (About heading)" :value="$setting->motivation" maxlength="120" hint="Shown as the large heading in the About section." />

        <div class="form-row">
            <div class="field">
                <label for="availability_enabled">Availability badge</label>
                <select id="availability_enabled" name="availability_enabled">
                    <option value="1" @selected((string) old('availability_enabled', (int) $setting->availability_enabled) === '1')>Show</option>
                    <option value="0" @selected((string) old('availability_enabled', (int) $setting->availability_enabled) === '0')>Hide</option>
                </select>
            </div>
            <x-field name="availability_text" label="Availability text" :value="$setting->availability_text" maxlength="60" />
        </div>

        <x-field name="email" type="email" label="Public email" :value="$setting->email" required maxlength="255" />

        <div class="form-row">
            <x-field name="github_url" type="url" label="GitHub URL" :value="$setting->github_url" placeholder="https://github.com/..." />
            <x-field name="instagram_url" type="url" label="Instagram URL" :value="$setting->instagram_url" placeholder="https://www.instagram.com/..." />
        </div>

        <div class="field">
            <label for="profile_image">Profile portrait</label>
            <div class="upload upload--portrait">
                <img id="portrait-preview" class="upload__preview upload__preview--portrait" src="{{ $setting->portrait_url }}" alt="Current portrait">
                <div>
                    <input type="file" id="profile_image" name="profile_image" accept="image/png,image/jpeg,image/webp" data-preview="#portrait-preview" data-max-mb="2">
                    <p class="field__hint">Use a PNG with a transparent background for the best result. Max 2 MB. Leave empty to keep the current portrait.</p>
                    <p class="field__error" data-upload-error hidden></p>
                    @error('profile_image')<p class="field__error">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="form__actions">
            <button type="submit" class="btn btn--dark btn--sm">Save Changes</button>
        </div>
    </form>
@endsection
