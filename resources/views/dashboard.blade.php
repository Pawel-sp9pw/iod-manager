<x-layouts.app title="Dashboard">
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Panel IOD</p>
            <h1 class="mt-2 text-3xl font-bold tracking-tight text-slate-950">Twoje organizacje</h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-500">Wybierz firmę, aby przejść do jej rejestrów, upoważnień, przypomnień i ustawień dostępu.</p>
        </div>
        @if(auth()->user()->is_super_admin)
            <a href="{{ route('companies.create') }}" class="iod-btn-primary">+ Dodaj firmę</a>
        @endif
    </div>

    <div class="mb-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <div class="iod-card p-5">
            <div class="text-sm font-medium text-slate-500">Firmy</div>
            <div class="mt-2 text-3xl font-bold tracking-tight text-slate-950">{{ $companies->count() }}</div>
            <div class="mt-2 text-xs text-slate-400">Organizacje dostępne na Twoim koncie</div>
        </div>
        <div class="iod-card p-5">
            <div class="text-sm font-medium text-slate-500">Aktywne</div>
            <div class="mt-2 text-3xl font-bold tracking-tight text-emerald-600">{{ $companies->where('active', true)->count() }}</div>
            <div class="mt-2 text-xs text-slate-400">Firmy aktualnie objęte obsługą</div>
        </div>
        <div class="iod-card p-5">
            <div class="text-sm font-medium text-slate-500">Bezpieczeństwo</div>
            <div class="mt-2 text-lg font-bold text-slate-950">2FA</div>
            <a href="{{ route('security.two-factor') }}" class="mt-2 inline-block text-xs font-semibold text-indigo-600 hover:text-indigo-500">Przejdź do ustawień →</a>
        </div>
        <div class="iod-card p-5">
            <div class="text-sm font-medium text-slate-500">Rola</div>
            <div class="mt-2 text-lg font-bold text-slate-950">{{ auth()->user()->is_super_admin ? 'Administrator / IOD' : 'Użytkownik' }}</div>
            <div class="mt-2 text-xs text-slate-400">Uprawnienia bieżącego konta</div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1fr_320px]">
        <section class="iod-card overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4 sm:px-6">
                <div>
                    <h2 class="font-semibold text-slate-950">Lista firm</h2>
                    <p class="mt-1 text-xs text-slate-500">Kliknij organizację, aby otworzyć jej panel.</p>
                </div>
            </div>

            <div class="divide-y divide-slate-100">
                @forelse($companies as $company)
                    <a href="{{ route('companies.show', $company) }}" class="group flex flex-col gap-4 px-5 py-5 transition hover:bg-slate-50 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="truncate font-semibold text-slate-950 group-hover:text-indigo-600">{{ $company->short_name ?: $company->name }}</h3>
                                @if($company->active)
                                    <span class="iod-badge bg-emerald-100 text-emerald-700">Aktywna</span>
                                @else
                                    <span class="iod-badge bg-slate-100 text-slate-600">Nieaktywna</span>
                                @endif
                            </div>
                            @if($company->short_name)
                                <div class="mt-1 truncate text-sm text-slate-500">{{ $company->name }}</div>
                            @endif
                            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-400">
                                @if($company->city)<span>{{ $company->city }}</span>@endif
                                @if($company->nip)<span>NIP {{ $company->nip }}</span>@endif
                            </div>
                        </div>
                        <div class="flex shrink-0 items-center gap-2 text-sm font-semibold text-indigo-600">Otwórz panel <span aria-hidden="true">→</span></div>
                    </a>
                @empty
                    <div class="px-6 py-12 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">0</div>
                        <h3 class="mt-4 font-semibold text-slate-900">Brak przypisanych firm</h3>
                        <p class="mt-2 text-sm text-slate-500">Dodaj pierwszą organizację, aby rozpocząć pracę w IOD Manager.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <aside class="space-y-6">
            <div class="iod-card p-5">
                <h2 class="font-semibold text-slate-950">Szybkie akcje</h2>
                <div class="mt-4 space-y-3">
                    @if(auth()->user()->is_super_admin)
                        <a href="{{ route('companies.create') }}" class="iod-btn-primary w-full">Dodaj organizację</a>
                    @endif
                    <a href="{{ route('security.two-factor') }}" class="iod-btn-secondary w-full">Ustawienia 2FA</a>
                </div>
            </div>

            <div class="rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-700 p-5 text-white shadow-lg shadow-indigo-600/20">
                <div class="text-sm font-semibold text-indigo-100">Bezpieczeństwo konta</div>
                <p class="mt-2 text-sm leading-6 text-indigo-100">Dla konta IOD zalecamy aktywne uwierzytelnianie dwuskładnikowe i bezpieczne przechowywanie kodów odzyskiwania.</p>
                <a href="{{ route('security.two-factor') }}" class="mt-4 inline-flex rounded-xl bg-white/15 px-3 py-2 text-sm font-semibold text-white transition hover:bg-white/20">Sprawdź ustawienia</a>
            </div>
        </aside>
    </div>
</x-layouts.app>
