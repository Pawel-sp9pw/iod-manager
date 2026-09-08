<x-layouts.app title="Upoważnienia">
    <div class="iod-page-head">
        <div>
            <div class="iod-eyebrow">{{ $company->short_name ?: $company->name }}</div>
            <h1 class="iod-page-title">Upoważnienia</h1>
            <p class="iod-page-subtitle">Ewidencja wydanych i odwołanych upoważnień do przetwarzania danych.</p>
        </div>
        <a href="{{ route('companies.show',$company) }}" class="iod-btn-secondary">← Wróć do firmy</a>
    </div>

    @if(auth()->user()->canWriteCompany($company->id))
        <section class="iod-card iod-card-pad">
            <div class="iod-section-head"><div><h2 class="iod-section-title">Wydaj nowe upoważnienie</h2><div class="iod-card-subtitle">Zarejestruj osobę, zakres i okres obowiązywania upoważnienia.</div></div></div>
            <form method="POST" action="{{ route('authorizations.store',$company) }}" class="iod-form-grid">
                @csrf
                <div class="iod-field"><label class="iod-label">Numer upoważnienia</label><input name="authorization_number" class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">Osoba *</label><input name="person_name" required class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">Identyfikator pracownika</label><input name="person_identifier" class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">Stanowisko</label><input name="position" class="iod-input"></div>
                <div class="iod-field" style="grid-column:1/-1"><label class="iod-label">Zakres upoważnienia *</label><textarea name="scope" required class="iod-textarea"></textarea></div>
                <div class="iod-field"><label class="iod-label">Data wydania</label><input type="date" name="issued_at" required value="{{ now()->toDateString() }}" class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">Ważne do</label><input type="date" name="valid_until" class="iod-input"></div>
                <div class="iod-actions" style="grid-column:1/-1"><button class="iod-btn-primary">Wydaj upoważnienie</button></div>
            </form>
        </section>
    @endif

    <section class="iod-section">
        <div class="iod-section-head"><h2 class="iod-section-title">Historia upoważnień</h2></div>
        <div class="iod-card iod-list">
            @forelse($authorizations as $a)
                <div class="iod-list-item" style="align-items:flex-start">
                    <div style="min-width:0;flex:1">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                            <div class="iod-list-title">{{ $a->person_name }}</div>
                            @if($a->authorization_number)<span class="iod-badge iod-badge-neutral">{{ $a->authorization_number }}</span>@endif
                            @if($a->revoked_at)<span class="iod-badge iod-badge-danger">Odwołane</span>@else<span class="iod-badge iod-badge-success">Aktywne</span>@endif
                        </div>
                        <div class="iod-list-meta">Wydano {{ $a->issued_at->format('d.m.Y') }} @if($a->valid_until) · ważne do {{ $a->valid_until->format('d.m.Y') }} @endif</div>
                        @if($a->position)<div class="iod-list-meta">{{ $a->position }}</div>@endif
                        <p style="margin:10px 0 0;color:#475569;line-height:1.6;white-space:pre-line">{{ $a->scope }}</p>
                        @if($a->revocation_reason)<div class="iod-alert iod-alert-error" style="margin:12px 0 0">Powód odwołania: {{ $a->revocation_reason }}</div>@endif
                    </div>
                    @if(!$a->revoked_at && auth()->user()->canWriteCompany($company->id))
                        <form method="POST" action="{{ route('authorizations.revoke',[$company,$a]) }}" style="min-width:230px">@csrf
                            <input name="revocation_reason" required placeholder="Powód odwołania" class="iod-input" style="padding:9px 10px;font-size:13px">
                            <button class="iod-btn-danger" style="margin-top:8px;width:100%">Odwołaj upoważnienie</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="iod-empty"><strong>Brak upoważnień</strong>Nie zarejestrowano jeszcze żadnych upoważnień.</div>
            @endforelse
        </div>
        <div style="margin-top:16px">{{ $authorizations->links() }}</div>
    </section>
</x-layouts.app>
