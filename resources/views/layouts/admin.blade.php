<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') | Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<div class="admin">
    <aside class="sidebar" id="admin-sidebar" aria-label="Admin sidebar">
        <div class="sidebar__brand">
            <span class="brand brand--light">{{ $profile->initials }}</span>
            <span class="sidebar__sub">Admin Panel</span>
            <button type="button" class="sidebar__close" data-sidebar-close aria-label="Close menu"><x-icon name="x" /></button>
        </div>

        <nav class="sidebar__nav" aria-label="Admin navigation">
            <a href="{{ route('admin.dashboard') }}" @class(['is-active' => request()->routeIs('admin.dashboard')])><x-icon name="layout-dashboard" /> Dashboard</a>
            <a href="{{ route('admin.projects.index') }}" @class(['is-active' => request()->routeIs('admin.projects.*')])><x-icon name="folder" /> Projects</a>
            <a href="{{ route('admin.skills.index') }}" @class(['is-active' => request()->routeIs('admin.skills.*')])><x-icon name="layers" /> Skills</a>
            <a href="{{ route('admin.messages.index') }}" @class(['is-active' => request()->routeIs('admin.messages.*')])>
                <x-icon name="message-square" /> Messages
                @if ($unreadCount > 0)<span class="count">{{ $unreadCount }}</span>@endif
            </a>
            <a href="{{ route('admin.profile.edit') }}" @class(['is-active' => request()->routeIs('admin.profile.*')])><x-icon name="user" /> Profile</a>
        </nav>

        <form method="POST" action="{{ route('admin.logout') }}" class="sidebar__logout">
            @csrf
            <button type="submit"><x-icon name="log-out" /> Logout</button>
        </form>
    </aside>
    <div class="sidebar-backdrop" data-sidebar-backdrop></div>

    <div class="admin__main">
        <header class="topbar">
            <button type="button" class="nav-toggle" data-sidebar-toggle aria-controls="admin-sidebar" aria-expanded="false" aria-label="Open menu"><x-icon name="menu" /></button>
            <a href="{{ route('home') }}" target="_blank" rel="noopener" class="topbar__link">View site <x-icon name="external-link" /></a>
            <span class="topbar__user">Welcome, {{ auth()->user()->name }}</span>
        </header>

        <div class="admin__content">
            @if (session('success'))
                <x-alert>
                    {{ session('success') }}
                    @if (session('view_url'))
                        <a href="{{ session('view_url') }}" target="_blank" rel="noopener" class="alert__link">View on site</a>
                    @endif
                </x-alert>
            @endif
            @if ($errors->any())<x-alert type="error">Please correct the errors below.</x-alert>@endif

            @yield('content')
        </div>
    </div>
</div>

<dialog id="confirm-dialog" class="dialog">
    <form method="dialog">
        <h2>Please confirm</h2>
        <p id="confirm-message">Are you sure?</p>
        <div class="dialog__actions">
            <button type="submit" value="cancel" class="btn btn--light btn--sm">Cancel</button>
            <button type="submit" value="confirm" class="btn btn--danger btn--sm">Delete</button>
        </div>
    </form>
</dialog>
</body>
</html>
