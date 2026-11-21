@props(['published' => false])
<span class="badge {{ $published ? 'badge--solid' : 'badge--outline' }}"><span class="badge__dot" aria-hidden="true"></span>{{ $published ? 'Published' : 'Draft' }}</span>
