<x-layouts.app title="{{ $company->short_name ?: $company->name }}">
    <div class="iod-page-head">
        <div>
            <div class="iod-eyebrow">Organizacja</div>
            <h1 class="iod-page-title">{{ $company->name }}</h1>
            <p class="iod-page-subtitle">@if($company->city){{ $company->city }}@endif @if($company->nip) · NIP {{ $company->nip }}@endif</p>
        </div>
        <div class="iod-actions">
            <a href="{{ route('dashboard') }}" class="iod-btn-secondary">← Wszystkie firmy</a>
            @if(auth()->user()->is_super_admin)<a href="{{ route('companies.edit',$company) }}" class="iod-btn-primary">Edytuj firmę</a>@endif
        </div>
    </div>

    <div class="iod-grid iod-grid-3">
        <a href="{{ route('registers.index',$company) }}" class="iod-card iod-stat"><div class="iod-stat-label">Rejestry</div><div class="iod-stat-value">{{ $registersCount }}</div><div class="iod-stat-meta" style="color:#4f46e5;font-weight:700">Otwórz rejestry →</div></a>
        <a href="{{ route('authorizations.index',$company) }}" class="iod-card iod-stat"><div class="iod-stat-label">Aktywne upoważnienia</div><div class="iod-stat-value">{{ $activeAuthorizationsCount }}</div><div class="iod-stat-meta" style="color:#4f46e5;font-weight:700">Zarządzaj upoważnieniami →</div></a>
        <a href="{{ route('reminders.index',$company) }}" class="iod-card iod-stat"><div class="iod-stat-label">Najbliższe przypomnienia</div><div class="iod-stat-value">{{ $upcomingReminders->count() }}</div><div class="iod-stat-meta" style="color:#4f46e5;font-weight:700">Zobacz terminy →</div></a>
    </div>

    <section class="iod-section">
        <div class="iod-section-head"><h2 class="iod-section-title">Najbliższe terminy</h2></div>
        <div class="iod-card iod-list">
            @forelse($upcomingReminders as $r)
                <div class="iod-list-item"><div><div class="iod-list-title">{{ $r->title }}</div><div class="iod-list-meta">{{ $r->description }}</div></div><span class="iod-badge iod-badge-warning">{{ ($r->next_due_at ?: $r->due_at)?->format('d.m.Y H:i') }}</span></div>
            @empty
                <div class="iod-empty"><strong>Brak aktywnych przypomnień</strong>Nie ma obecnie zbliżających się terminów dla tej firmy.</div>
            @endforelse
        </div>
    </section>

    @if(auth()->user()->is_super_admin)
        <section class="iod-section iod-card iod-card-pad">
            <div class="iod-section-head"><div><h2 class="iod-section-title">Konta przypisane do firmy</h2><div class="iod-card-subtitle">Zarządzaj dostępem użytkowników do tej organizacji.</div></div></div>
            <div class="iod-list" style="margin:0 -22px 22px">
                @forelse($companyUsers as $u)
                    <div class="iod-list-item">
                        <div><div class="iod-list-title">{{ $u->name }}</div><div class="iod-list-meta">{{ $u->email }} · {{ $u->pivot->role }}{{ $u->pivot->can_write?' · zapis':'' }}</div></div>
                        @if($u->id!==auth()->id())<form method="POST" action="{{ route('companies.users.destroy',[$company,$u]) }}">@csrf @method('DELETE')<button class="iod-btn-danger">Usuń dostęp</button></form>@endif
                    </div>
                @empty
                    <div class="iod-empty">Brak dodatkowych kont przypisanych do firmy.</div>
                @endforelse
            </div>
            <form method="POST" action="{{ route('companies.users.store',$company) }}" class="iod-form-grid">
                @csrf
                <div class="iod-field"><label class="iod-label">Imię i nazwisko</label><input name="name" required class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">E-mail</label><input type="email" name="email" required class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">Hasło</label><input type="password" name="password" required class="iod-input" placeholder="Minimum 14 znaków"></div>
                <div class="iod-field"><label class="iod-label">Powtórz hasło</label><input type="password" name="password_confirmation" required class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">Rola</label><select name="role" class="iod-select"><option value="company_admin">Administrator — podgląd</option><option value="iod">IOD — pełny dostęp</option></select></div>
                <label style="display:flex;align-items:center;gap:9px;font-size:14px;color:#475569"><input type="checkbox" name="can_write" value="1" style="width:16px;height:16px"> Zezwól na zapis</label>
                <div class="iod-actions" style="grid-column:1/-1"><button class="iod-btn-primary">Dodaj / przypisz konto</button></div>
            </form>
        </section>
    @endif
</x-layouts.app>
