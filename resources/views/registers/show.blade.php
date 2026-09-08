<x-layouts.app title="{{ $register->name }}">
    <div class="iod-page-head">
        <div>
            <div class="iod-eyebrow">Rejestr RODO</div>
            <h1 class="iod-page-title">{{ $register->name }}</h1>
            <p class="iod-page-subtitle">{{ $register->description ?: 'Rejestr wpisów i zdarzeń dla tej organizacji.' }}</p>
        </div>
        <a href="{{ route('registers.index',$company) }}" class="iod-btn-secondary">← Wszystkie rejestry</a>
    </div>

    @if(auth()->user()->canWriteCompany($company->id))
        <section class="iod-card iod-card-pad">
            <div class="iod-section-head"><div><h2 class="iod-section-title">Dodaj wpis</h2><div class="iod-card-subtitle">Uzupełnij podstawowe informacje o nowym zdarzeniu.</div></div></div>
            <form method="POST" action="{{ route('register_entries.store',[$company,$register]) }}" class="iod-form-grid">
                @csrf
                <div class="iod-field"><label class="iod-label">Tytuł wpisu</label><input name="title" required class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">Status</label><input name="status" value="active" required class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">Data zdarzenia</label><input type="date" name="event_date" class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">Notatki</label><textarea name="notes" class="iod-textarea"></textarea></div>
                <div class="iod-field" style="grid-column:1/-1"><label class="iod-label">Dodatkowe dane JSON</label><textarea name="data_json" class="iod-textarea" placeholder='np. {"podstawa_prawna":"..."}'></textarea><div class="iod-help">Pole techniczne dla danych dodatkowych; pozostaw puste, jeśli nie jest potrzebne.</div></div>
                <div class="iod-actions" style="grid-column:1/-1"><button class="iod-btn-primary">Dodaj wpis</button></div>
            </form>
        </section>
    @endif

    <section class="iod-section">
        <div class="iod-section-head"><h2 class="iod-section-title">Wpisy</h2></div>
        <div class="iod-card iod-list">
            @forelse($entries as $e)
                <div class="iod-list-item" style="align-items:flex-start">
                    <div style="min-width:0;flex:1">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap"><div class="iod-list-title">{{ $e->title }}</div><span class="iod-badge iod-badge-neutral">{{ $e->status }}</span></div>
                        <div class="iod-list-meta">{{ $e->event_date?->format('d.m.Y') ?: 'bez daty' }}</div>
                        @if($e->notes)<p style="margin:10px 0 0;color:#475569;line-height:1.6;white-space:pre-line">{{ $e->notes }}</p>@endif
                    </div>
                    @if(auth()->user()->canWriteCompany($company->id))<form method="POST" action="{{ route('register_entries.destroy',[$company,$register,$e]) }}">@csrf @method('DELETE')<button class="iod-btn-danger">Archiwizuj</button></form>@endif
                </div>
            @empty
                <div class="iod-empty"><strong>Brak wpisów</strong>Ten rejestr nie zawiera jeszcze żadnych pozycji.</div>
            @endforelse
        </div>
        <div style="margin-top:16px">{{ $entries->links() }}</div>
    </section>
</x-layouts.app>
