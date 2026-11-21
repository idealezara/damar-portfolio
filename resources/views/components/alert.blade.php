@props(['type' => 'success'])
<div {{ $attributes->class(['alert', 'alert--'.$type]) }} role="{{ $type === 'error' ? 'alert' : 'status' }}">
    <x-icon :name="$type === 'error' ? 'x' : 'check'" />
    <div>{{ $slot }}</div>
</div>
