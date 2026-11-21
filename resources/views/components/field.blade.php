@props(['name', 'label', 'type' => 'text', 'value' => null, 'rows' => 5, 'hint' => null])
<div class="field">
    <label for="{{ $name }}">{{ $label }}@if ($attributes->has('required')) <span class="field__req" aria-hidden="true">*</span>@endif</label>
    @if ($type === 'textarea')
        <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->class(['is-invalid' => $errors->has($name)]) }}>{{ old($name, $value) }}</textarea>
    @else
        <input id="{{ $name }}" type="{{ $type }}" name="{{ $name }}" @if ($type !== 'password') value="{{ old($name, $value) }}" @endif {{ $attributes->class(['is-invalid' => $errors->has($name)]) }}>
    @endif
    @if ($hint)<p class="field__hint">{{ $hint }}</p>@endif
    @error($name)<p class="field__error">{{ $message }}</p>@enderror
</div>
