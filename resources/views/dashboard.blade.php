<x-layouts.app title="Dashboard">
    <div class="iod-page-head">
        <div>
            <div class="iod-eyebrow">Panel IOD</div>
            <h1 class="iod-page-title">Twoje organizacje</h1>
            <p class="iod-page-subtitle">Wybierz firmę, aby przejść do rejestrów, upoważnień, przypomnień i ustawień dostępu.</p>
        </div>
        @if(auth()->user()->is_super_admin)
            <a href="{{ route('companies.create') }}" class="iod-btn-primary">+ Dodaj firmę</a>
        @endif
    </div>

    <div class="iod-grid iod-grid-4">
        <div class="iod-card iod-stat"><div class="iod-stat-label">Firmy</div><div class="iod-stat-value">{{ $companies->count() }}</div><div class="iod-stat-meta">Organizacje dostępne na koncie</div></div>
        <div class="iod-card iod-stat"><div class="iod-stat-label">Aktywne</div><div class="iod-stat-value" style="color:#047857">{{ $companies->where('active', true)->count() }}</div><div class="iod-stat-meta">Firmy aktualnie objęte obsługą</div></div>
        <div class="iod-card iod-stat"><div class="iod-stat-label">Bezpieczeństwo</div><div class="iod-stat-value" style="font-size:21px">2FA</div><div class="iod-stat-meta"><a href="{{ route('security.two-factor') }}" style="color:#4f46e5;font-weight:700">Przejdź do ustawień →</a></div></div>
        <div class="iod-card iod-stat"><div class="iod-stat-label">Rola</div><div class="iod-stat-value" style="font-size:18px">{{ auth()->user()->is_super_admin ? 'Administrator / IOD' : 'Użytkownik' }}</div><div class="iod-stat-meta">Uprawnienia bieżącego konta</div></div>
    </div>

    <div class="iod-grid" style="grid-template-columns:minmax(0,1fr) 320px;margin-top:22px">
        <section class="iod-card">
            <div class="iod-card-pad" style="border-bottom:1px solid #e2e8f0;padding-bottom:16px">
                <div class="iod-card-title">Lista firm</div>
                <div class="iod-card-subtitle">Kliknij organizację, aby otworzyć jej panel.</div>
            </div>
            <div class="iod-list">
                @forelse($companies as $company)
                    <a href="{{ route('companies.show', $company) }}" class="iod-list-item">
                        <div style="min-width:0">
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                <span class="iod-list-title">{{ $company->short_name ?: $company->name }}</span>
                                <span class="iod-badge {{ $company->active ? 'iod-badge-success' : 'iod-badge-neutral' }}">{{ $company->active ? 'Aktywna' : 'Nieaktywna' }}</span>
                            </div>
                            @if($company->short_name)<div class="iod-list-meta">{{ $company->name }}</div>@endif
                            <div class="iod-list-meta">@if($company->city){{ $company->city }}@endif @if($company->nip) · NIP {{ $company->nip }}@endif</div>
                        </div>
                        <span style="color:#4f46e5;font-size:13px;font-weight:800;white-space:nowrap">Otwórz panel →</span>
                    </a>
                @empty
                    <div class="iod-empty"><strong>Brak przypisanych firm</strong>Dodaj pierwszą organizację, aby rozpocząć pracę w IOD Manager.</div>
                @endforelse
            </div>
        </section>

        <aside style="display:flex;flex-direction:column;gap:18px">
            <div class="iod-card iod-card-pad">
                <div class="iod-card-title">Szybkie akcje</div>
                <div style="display:flex;flex-direction:column;gap:10px;margin-top:16px">
                    @if(auth()->user()->is_super_admin)<a href="{{ route('companies.create') }}" class="iod-btn-primary">Dodaj organizację</a>@endif
                    <a href="{{ route('security.two-factor') }}" class="iod-btn-secondary">Ustawienia 2FA</a>
                </div>
            </div>
            <div class="iod-card iod-card-pad" style="background:linear-gradient(145deg,#4f46e5,#4338ca);border-color:#4f46e5;color:white">
                <div style="font-size:14px;font-weight:800">Bezpieczeństwo konta</div>
                <p style="font-size:13px;line-height:1.65;color:#e0e7ff;margin:10px 0 16px">Dla konta IOD zalecamy aktywne uwierzytelnianie dwuskładnikowe i bezpieczne przechowywanie kodów odzyskiwania.</p>
                <a href="{{ route('security.two-factor') }}" class="iod-btn-secondary" style="background:rgba(255,255,255,.12);border-color:rgba(255,255,255,.2);color:#fff">Sprawdź ustawienia</a>
            </div>
        </aside>
    </div>

    <style>@media(max-width:900px){.iod-main>.iod-grid[style*="320px"]{grid-template-columns:1fr!important}}</style>
</x-layouts.app>
