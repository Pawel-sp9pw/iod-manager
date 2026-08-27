<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'IOD Manager' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">
<header class="border-b border-slate-800 bg-slate-900/80">
    <div class="mx-auto max-w-7xl px-6 py-4 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="font-semibold tracking-wide">IOD Manager</a>
        @auth <span class="text-sm text-slate-400">{{ auth()->user()->name }}</span> @endauth
    </div>
</header>
<main class="mx-auto max-w-7xl px-6 py-8">
    @if(session('warning'))<div class="mb-6 rounded-lg border border-amber-700 bg-amber-950/40 p-4">{{ session('warning') }}</div>@endif
    {{ $slot }}
</main>
</body>
</html>
