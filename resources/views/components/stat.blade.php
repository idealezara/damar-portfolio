@props(['icon', 'label', 'value', 'note' => null])
<div class="stat">
    <x-icon :name="$icon" />
    <p class="stat__label">{{ $label }}</p>
    <p class="stat__value">{{ $value }}</p>
    @if ($note)<p class="stat__note">{{ $note }}</p>@endif
</div>
