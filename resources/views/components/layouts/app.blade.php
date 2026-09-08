<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'IOD Manager' }}</title>
    @include('components.iod-styles')
</head>
<body>
<div class="iod-shell">
    <aside class="iod-sidebar">
        <a href="{{ route('dashboard') }}" class="iod-brand">
            <span class="iod-brand-mark">IOD</span>
            <span>
                <span class="iod-brand-title">IOD Manager</span>
                <span class="iod-brand-subtitle">Panel ochrony danych</span>
            </span>
        </a>

        <nav class="iod-nav">
            <div class="iod-nav-label">Nawigacja</div>
            <a href="{{ route('dashboard') }}" class="iod-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                <span class="iod-nav-dot"></span> Dashboard
            </a>
            <a href="{{ route('security.two-factor') }}" class="iod-nav-link {{ request()->routeIs('security.*') ? 'is-active' : '' }}">
                <span class="iod-nav-dot"></span> Bezpieczeństwo / 2FA
            </a>
        </nav>

        @auth
            <div class="iod-userbox">
                <div class="iod-usercard">
                    <div class="iod-user-name">{{ auth()->user()->name }}</div>
                    <div class="iod-user-email">{{ auth()->user()->email }}</div>
                    <form method="POST" action="{{ route('logout') }}" style="margin-top:12px">
                        @csrf
                        <button class="iod-btn-secondary" style="width:100%;background:#0f172a;color:#cbd5e1;border-color:#334155">Wyloguj</button>
                    </form>
                </div>
            </div>
        @endauth
    </aside>

    <div class="iod-content">
        <header class="iod-topbar">
            <div>
                <div class="iod-topbar-title">{{ $title ?? 'IOD Manager' }}</div>
                <div class="iod-topbar-subtitle">Centrum zarządzania obowiązkami IOD</div>
            </div>
            @auth
                <div style="text-align:right">
                    <div style="font-size:13px;font-weight:700;color:#334155">{{ auth()->user()->name }}</div>
                    <div style="font-size:11px;color:#94a3b8;margin-top:2px">{{ auth()->user()->is_super_admin ? 'Administrator / IOD' : 'Użytkownik' }}</div>
                </div>
            @endauth
        </header>

        <main class="iod-main">
            @if(session('status'))
                <div class="iod-alert iod-alert-success">{{ session('status') }}</div>
            @endif
            @if(session('warning'))
                <div class="iod-alert iod-alert-warning">{{ session('warning') }}</div>
            @endif
            @if($errors->any())
                <div class="iod-alert iod-alert-error"><ul style="margin:0;padding-left:18px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            {{ $slot }}
        </main>
    </div>
</div>
</body>
</html>
