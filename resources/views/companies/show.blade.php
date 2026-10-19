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

    @if(auth()->user()->canManageCompanyUsers($company->id))
        <section class="iod-section iod-card iod-card-pad">
            <div class="iod-section-head">
                <div>
                    <h2 class="iod-section-title">Użytkownicy firmy</h2>
                    <div class="iod-card-subtitle">Konta mają dostęp wyłącznie do tej organizacji. Administrator firmy może edytować dane i zarządzać użytkownikami, a konto tylko do odczytu nie może niczego zmieniać.</div>
                </div>
            </div>

            <div class="iod-list" style="margin:0 -22px 22px">
                @forelse($companyUsers as $u)
                    @php($accessLevel = ($u->pivot->role === 'iod' || $u->pivot->can_write) ? 'admin' : 'read_only')
                    <div class="iod-list-item" style="align-items:flex-start">
                        <div style="min-width:0;flex:1">
                            <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                                <div class="iod-list-title">{{ $u->name }}</div>
                                @if($accessLevel === 'admin')
                                    <span class="iod-badge iod-badge-success">Administrator firmy</span>
                                @else
                                    <span class="iod-badge iod-badge-neutral">Tylko odczyt</span>
                                @endif
                            </div>
                            <div class="iod-list-meta">{{ $u->email }}</div>
                            <div class="iod-list-meta">
                                {{ $accessLevel === 'admin'
                                    ? 'Może dodawać i edytować dane w tej firmie oraz zarządzać jej użytkownikami.'
                                    : 'Może przeglądać dane tej firmy bez możliwości zapisu.' }}
                            </div>
                        </div>

                        <div style="min-width:250px;display:flex;flex-direction:column;gap:8px">
                            @if($u->id !== auth()->id() || auth()->user()->is_super_admin)
                                <form method="POST" action="{{ route('companies.users.update',[$company,$u]) }}">
                                    @csrf
                                    @method('PUT')
                                    <select name="access_level" class="iod-select">
                                        <option value="admin" @selected($accessLevel === 'admin')>Administrator firmy</option>
                                        <option value="read_only" @selected($accessLevel === 'read_only')>Tylko odczyt</option>
                                    </select>
                                    <button class="iod-btn-secondary" style="width:100%;margin-top:8px">Zmień uprawnienia</button>
                                </form>
                            @endif

                            @if($u->id !== auth()->id())
                                <form method="POST" action="{{ route('companies.users.destroy',[$company,$u]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="iod-btn-danger" style="width:100%">Usuń dostęp</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="iod-empty">Brak dodatkowych kont przypisanych do firmy.</div>
                @endforelse
            </div>

            <div class="iod-section-head">
                <div>
                    <h3 class="iod-section-title">Dodaj użytkownika</h3>
                    <div class="iod-card-subtitle">Nowe konto zostanie przypisane wyłącznie do {{ $company->short_name ?: $company->name }}.</div>
                </div>
            </div>

            <form method="POST" action="{{ route('companies.users.store',$company) }}" class="iod-form-grid">
                @csrf
                <div class="iod-field"><label class="iod-label">Imię i nazwisko</label><input name="name" required class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">E-mail</label><input type="email" name="email" required class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">Hasło</label><input type="password" name="password" required class="iod-input" placeholder="Minimum 14 znaków"></div>
                <div class="iod-field"><label class="iod-label">Powtórz hasło</label><input type="password" name="password_confirmation" required class="iod-input"></div>
                <div class="iod-field" style="grid-column:1/-1">
                    <label class="iod-label">Poziom dostępu</label>
                    <select name="access_level" class="iod-select" required>
                        <option value="read_only">Tylko odczyt — bez możliwości zmiany danych</option>
                        <option value="admin">Administrator firmy — zapis i zarządzanie użytkownikami tej firmy</option>
                    </select>
                </div>
                <div class="iod-actions" style="grid-column:1/-1"><button class="iod-btn-primary">Utwórz i przypisz konto</button></div>
            </form>
        </section>
    @endif
</x-layouts.app>
