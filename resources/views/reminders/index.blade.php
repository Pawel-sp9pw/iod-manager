<x-layouts.app title="Przypomnienia">
    <div class="iod-page-head">
        <div>
            <div class="iod-eyebrow">{{ $company->short_name ?: $company->name }}</div>
            <h1 class="iod-page-title">Przypomnienia i cykle RODO</h1>
            <p class="iod-page-subtitle">Zarządzaj terminami, zadaniami cyklicznymi i wykonaniem obowiązków ochrony danych.</p>
        </div>
        <a href="{{ route('companies.show',$company) }}" class="iod-btn-secondary">← Wróć do firmy</a>
    </div>

    @if(auth()->user()->canWriteCompany($company->id))
        <section class="iod-card iod-card-pad">
            <div class="iod-section-head"><div><h2 class="iod-section-title">Dodaj przypomnienie</h2><div class="iod-card-subtitle">Ustaw termin jednorazowy albo cykliczny.</div></div></div>
            <form method="POST" action="{{ route('reminders.store',$company) }}" class="iod-form-grid">
                @csrf
                <div class="iod-field"><label class="iod-label">Nazwa zadania *</label><input name="title" required class="iod-input"></div>
                <div class="iod-field"><label class="iod-label">Termin *</label><input type="datetime-local" name="due_at" required class="iod-input"></div>
                <div class="iod-field" style="grid-column:1/-1"><label class="iod-label">Opis</label><textarea name="description" class="iod-textarea"></textarea></div>
                <div class="iod-field"><label class="iod-label">Powtarzalność</label><select name="recurrence" class="iod-select"><option value="none">Jednorazowe</option><option value="daily">Codziennie</option><option value="weekly">Co tydzień</option><option value="monthly">Co miesiąc</option><option value="quarterly">Co kwartał</option><option value="yearly">Co rok</option><option value="custom">Własny interwał</option></select></div>
                <div class="iod-field"><label class="iod-label">Własny interwał — dni</label><input type="number" min="1" max="3650" name="custom_interval_days" class="iod-input"></div>
                <label style="grid-column:1/-1;display:flex;align-items:center;gap:9px;font-size:14px;color:#475569"><input type="checkbox" name="email_notification" value="1" style="width:16px;height:16px"> Powiadomienie e-mail po skonfigurowaniu SMTP</label>
                <div class="iod-actions" style="grid-column:1/-1"><button class="iod-btn-primary">Dodaj przypomnienie</button></div>
            </form>
        </section>
    @endif

    <section class="iod-section">
        <div class="iod-section-head"><h2 class="iod-section-title">Lista przypomnień</h2></div>
        <div class="iod-card iod-list">
            @forelse($reminders as $r)
                <div class="iod-list-item" style="align-items:flex-start">
                    <div style="min-width:0;flex:1">
                        <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap">
                            <div class="iod-list-title">{{ $r->title }}</div>
                            <span class="iod-badge {{ $r->active ? 'iod-badge-success' : 'iod-badge-neutral' }}">{{ $r->active ? 'Aktywne' : 'Zakończone' }}</span>
                            <span class="iod-badge iod-badge-neutral">{{ $r->recurrence }}</span>
                        </div>
                        <div class="iod-list-meta">Termin: {{ ($r->next_due_at ?: $r->due_at)?->format('d.m.Y H:i') }}</div>
                        @if($r->description)<p style="margin:10px 0 0;color:#475569;line-height:1.6">{{ $r->description }}</p>@endif
                    </div>
                    @if($r->active && auth()->user()->canWriteCompany($company->id))
                        <form method="POST" action="{{ route('reminders.complete',[$company,$r]) }}">@csrf<button class="iod-btn-primary" style="background:#047857">Oznacz jako wykonane</button></form>
                    @endif
                </div>
            @empty
                <div class="iod-empty"><strong>Brak przypomnień</strong>Dodaj pierwszy termin lub cykliczne zadanie RODO.</div>
            @endforelse
        </div>
        <div style="margin-top:16px">{{ $reminders->links() }}</div>
    </section>
</x-layouts.app>
