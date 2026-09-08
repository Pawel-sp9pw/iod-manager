<x-layouts.app title="Edytuj firmę">
    <div class="iod-page-head">
        <div>
            <div class="iod-eyebrow">Organizacje</div>
            <h1 class="iod-page-title">Edytuj firmę</h1>
            <p class="iod-page-subtitle">Zaktualizuj dane organizacji i jej status w panelu IOD.</p>
        </div>
        <a href="{{ route('companies.show',$company) }}" class="iod-btn-secondary">← Wróć do firmy</a>
    </div>
    <div class="iod-card iod-card-pad" style="max-width:920px">
        <form method="POST" action="{{ route('companies.update',$company) }}" class="iod-form-grid">
            @csrf
            @method('PUT')
            @include('companies.partials.form')
            <div class="iod-actions" style="grid-column:1/-1;margin-top:4px">
                <button class="iod-btn-primary">Zapisz zmiany</button>
                <a href="{{ route('companies.show',$company) }}" class="iod-btn-ghost">Anuluj</a>
            </div>
        </form>
    </div>
</x-layouts.app>
