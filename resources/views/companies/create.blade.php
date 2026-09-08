<x-layouts.app title="Dodaj firmę">
    <div class="iod-page-head">
        <div>
            <div class="iod-eyebrow">Organizacje</div>
            <h1 class="iod-page-title">Dodaj firmę</h1>
            <p class="iod-page-subtitle">Utwórz nową organizację i uzupełnij podstawowe dane administratora.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="iod-btn-secondary">← Wróć</a>
    </div>
    <div class="iod-card iod-card-pad" style="max-width:920px">
        <form method="POST" action="{{ route('companies.store') }}" class="iod-form-grid">
            @csrf
            @include('companies.partials.form')
            <div class="iod-actions" style="grid-column:1/-1;margin-top:4px">
                <button class="iod-btn-primary">Zapisz firmę</button>
                <a href="{{ route('dashboard') }}" class="iod-btn-ghost">Anuluj</a>
            </div>
        </form>
    </div>
</x-layouts.app>
