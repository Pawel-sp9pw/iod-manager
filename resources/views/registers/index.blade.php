<x-layouts.app title="Rejestry RODO">
    <div class="iod-page-head">
        <div>
            <div class="iod-eyebrow">{{ $company->short_name ?: $company->name }}</div>
            <h1 class="iod-page-title">Rejestry RODO</h1>
            <p class="iod-page-subtitle">Centralne miejsce dla rejestrów, wpisów i historii działań związanych z ochroną danych.</p>
        </div>
        <a href="{{ route('companies.show',$company) }}" class="iod-btn-secondary">← Wróć do firmy</a>
    </div>

    <div class="iod-grid iod-grid-2">
        @forelse($registers as $r)
            <a href="{{ route('registers.show',[$company,$r]) }}" class="iod-card iod-card-pad">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:14px">
                    <div><div class="iod-card-title">{{ $r->name }}</div><div class="iod-card-subtitle">{{ $r->type }}</div></div>
                    <span class="iod-badge iod-badge-neutral">{{ $r->entries_count }} wpisów</span>
                </div>
                <div style="margin-top:16px;color:#4f46e5;font-size:13px;font-weight:800">Otwórz rejestr →</div>
            </a>
        @empty
            <div class="iod-card iod-empty" style="grid-column:1/-1"><strong>Brak rejestrów</strong>Utwórz pierwszy rejestr dla tej organizacji.</div>
        @endforelse
    </div>

    @if(auth()->user()->canWriteCompany($company->id))
        <section class="iod-section iod-card iod-card-pad">
            <div class="iod-section-head"><div><h2 class="iod-section-title">Nowy rejestr</h2><div class="iod-card-subtitle">Dodaj kolejny obszar ewidencji dla organizacji.</div></div></div>
            <form method="POST" action="{{ route('registers.store',$company) }}" class="iod-form-grid">
                @csrf
                <div class="iod-field"><label class="iod-label">Nazwa rejestru</label><input name="name" required class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">Typ rejestru</label><select name="type" class="iod-select"><option value="processing_activities">Czynności przetwarzania</option><option value="breaches">Naruszenia</option><option value="data_subject_requests">Żądania osób</option><option value="processors">Podmioty przetwarzające</option><option value="dpia_risk">DPIA / ryzyko</option><option value="training">Szkolenia</option><option value="inspections">Kontrole</option><option value="other">Inny</option></select></div>
                <div class="iod-field" style="grid-column:1/-1"><label class="iod-label">Opis</label><textarea name="description" class="iod-textarea"></textarea></div>
                <div class="iod-actions" style="grid-column:1/-1"><button class="iod-btn-primary">Utwórz rejestr</button></div>
            </form>
        </section>
    @endif
</x-layouts.app>
