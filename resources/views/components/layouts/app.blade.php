<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'IOD Manager' }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900" x-data="{ mobileNav: false }">
    <div class="min-h-screen lg:grid lg:grid-cols-[280px_1fr]">
        <aside class="hidden min-h-screen border-r border-slate-800 bg-slate-950 text-slate-200 lg:flex lg:flex-col">
            <div class="border-b border-slate-800 px-6 py-6">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 font-bold text-white">IOD</span>
                    <span>
                        <span class="block font-semibold text-white">IOD Manager</span>
                        <span class="block text-xs text-slate-500">Panel ochrony danych</span>
                    </span>
                </a>
            </div>

            <nav class="flex-1 space-y-2 px-4 py-6 text-sm">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl bg-slate-900 px-4 py-3 font-medium text-white">
                    <span class="h-2 w-2 rounded-full bg-indigo-400"></span>
                    Dashboard
                </a>
                <a href="{{ route('security.two-factor') }}" class="flex items-center gap-3 rounded-xl px-4 py-3 text-slate-400 transition hover:bg-slate-900 hover:text-white">
                    <span class="h-2 w-2 rounded-full bg-slate-600"></span>
                    Bezpieczeństwo / 2FA
                </a>
            </nav>

            @auth
                <div class="border-t border-slate-800 p-4">
                    <div class="rounded-2xl bg-slate-900 p-4">
                        <div class="text-sm font-semibold text-white">{{ auth()->user()->name }}</div>
                        <div class="mt-1 truncate text-xs text-slate-500">{{ auth()->user()->email }}</div>
                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                            @csrf
                            <button class="w-full rounded-xl border border-slate-700 px-3 py-2 text-sm font-medium text-slate-300 transition hover:border-slate-600 hover:bg-slate-800 hover:text-white">Wyloguj</button>
                        </form>
                    </div>
                </div>
            @endauth
        </aside>

        <div class="min-w-0">
            <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
                <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button type="button" class="rounded-lg border border-slate-200 p-2 text-slate-600 lg:hidden" @click="mobileNav = !mobileNav">Menu</button>
                        <div>
                            <div class="text-sm font-semibold text-slate-900">{{ $title ?? 'IOD Manager' }}</div>
                            <div class="text-xs text-slate-500">Centrum zarządzania obowiązkami IOD</div>
                        </div>
                    </div>
                    @auth
                        <div class="hidden text-right sm:block">
                            <div class="text-sm font-medium text-slate-700">{{ auth()->user()->name }}</div>
                            <div class="text-xs text-slate-400">{{ auth()->user()->is_super_admin ? 'Administrator / IOD' : 'Użytkownik' }}</div>
                        </div>
                    @endauth
                </div>
                <div x-show="mobileNav" x-cloak class="border-t border-slate-200 bg-white px-4 py-3 lg:hidden">
                    <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Dashboard</a>
                    <a href="{{ route('security.two-factor') }}" class="mt-1 block rounded-lg px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-100">Bezpieczeństwo / 2FA</a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-1">@csrf<button class="w-full rounded-lg px-3 py-2 text-left text-sm font-medium text-rose-600 hover:bg-rose-50">Wyloguj</button></form>
                </div>
            </header>

            <main class="px-4 py-6 sm:px-6 lg:px-8 lg:py-8">
                @if(session('status'))
                    <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
                @endif
                @if(session('warning'))
                    <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800">{{ session('warning') }}</div>
                @endif
                @if($errors->any())
                    <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700"><ul class="list-disc pl-5">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
