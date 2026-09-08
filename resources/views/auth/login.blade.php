<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Logowanie — IOD Manager</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-900">
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,rgba(79,70,229,0.28),transparent_32%),radial-gradient(circle_at_bottom_right,rgba(14,165,233,0.22),transparent_28%)]"></div>
        <div class="absolute inset-0 bg-gradient-to-br from-slate-950 via-slate-950 to-slate-900"></div>

        <main class="relative mx-auto grid min-h-screen max-w-7xl items-center gap-12 px-6 py-12 lg:grid-cols-2 lg:px-10">
            <section class="hidden lg:block text-white">
                <div class="mb-8 inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-4 py-2 text-sm text-slate-300 backdrop-blur">
                    <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                    Bezpieczny panel Inspektora Ochrony Danych
                </div>
                <h1 class="max-w-2xl text-5xl font-bold tracking-tight sm:text-6xl">IOD Manager</h1>
                <p class="mt-6 max-w-xl text-lg leading-8 text-slate-300">Zarządzaj firmami, rejestrami RODO, upoważnieniami i terminami w jednym miejscu.</p>
                <div class="mt-10 grid max-w-xl grid-cols-2 gap-4 text-sm text-slate-300">
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">Wiele organizacji w jednym panelu</div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">2FA i kontrola dostępu</div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">Rejestry i upoważnienia</div>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-4 backdrop-blur">Przypomnienia i historia działań</div>
                </div>
            </section>

            <section class="mx-auto w-full max-w-md">
                <div class="rounded-3xl border border-white/10 bg-white p-7 shadow-2xl shadow-black/30 sm:p-9">
                    <div class="mb-7">
                        <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-2xl bg-indigo-600 text-lg font-bold text-white shadow-lg shadow-indigo-600/25">IOD</div>
                        <h2 class="text-2xl font-bold tracking-tight text-slate-950">Zaloguj się</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Wprowadź dane swojego konta, aby przejść do panelu.</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">Nieprawidłowy e-mail lub hasło. Spróbuj ponownie.</div>
                    @endif

                    @if(session('status'))
                        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf
                        <div>
                            <label for="email" class="iod-label">E-mail</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus class="iod-input" placeholder="iod@firma.pl">
                        </div>
                        <div>
                            <label for="password" class="iod-label">Hasło</label>
                            <input id="password" name="password" type="password" autocomplete="current-password" required class="iod-input" placeholder="••••••••••••••">
                        </div>
                        <label class="flex items-center gap-3 text-sm text-slate-600">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            Zapamiętaj mnie na tym urządzeniu
                        </label>
                        <button type="submit" class="iod-btn-primary w-full py-3">Zaloguj się do panelu</button>
                    </form>

                    <p class="mt-6 text-center text-xs leading-5 text-slate-400">IOD Manager · dostęp wyłącznie dla uprawnionych użytkowników</p>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
