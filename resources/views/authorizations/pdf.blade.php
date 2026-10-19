<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Upoważnienie do przetwarzania danych osobowych</title>
    <style>
        @page { margin: 28mm 22mm 24mm 22mm; }
        body { font-family: "DejaVu Sans", sans-serif; font-size: 11pt; line-height: 1.45; color: #111827; }
        h1 { margin: 0 0 8px; font-size: 18pt; text-align: center; text-transform: uppercase; letter-spacing: .4px; }
        .subtitle { text-align: center; font-size: 10pt; color: #4b5563; margin-bottom: 28px; }
        .meta { width: 100%; border-collapse: collapse; margin: 0 0 20px; }
        .meta td { padding: 5px 0; vertical-align: top; }
        .meta td:first-child { width: 38%; color: #4b5563; }
        .section-title { margin: 20px 0 8px; font-weight: bold; font-size: 11pt; }
        .box { border: 1px solid #cbd5e1; border-radius: 4px; padding: 12px 14px; margin-bottom: 14px; }
        .scope { white-space: pre-line; }
        .legal { text-align: justify; font-size: 10pt; color: #374151; margin-top: 18px; }
        .signatures { width: 100%; border-collapse: collapse; margin-top: 42px; }
        .signatures td { width: 50%; text-align: center; padding: 0 16px; vertical-align: top; }
        .line { border-top: 1px solid #374151; margin-top: 52px; padding-top: 6px; font-size: 9pt; color: #4b5563; }
        .status { margin-top: 18px; border: 1px solid #fecaca; background: #fff1f2; padding: 10px 12px; font-size: 10pt; color: #9f1239; }
        .footer { position: fixed; bottom: -12mm; left: 0; right: 0; text-align: center; font-size: 8pt; color: #9ca3af; }
    </style>
</head>
<body>
    <h1>Upoważnienie do przetwarzania danych osobowych</h1>
    <div class="subtitle">
        @if($authorization->authorization_number)
            Nr {{ $authorization->authorization_number }}
        @else
            Dokument ewidencyjny nr {{ $authorization->id }}
        @endif
    </div>

    <table class="meta">
        <tr><td>Administrator danych / organizacja:</td><td><strong>{{ $company->name }}</strong></td></tr>
        @if($company->address || $company->postal_code || $company->city)
            <tr><td>Adres:</td><td>{{ trim(($company->address ? $company->address . ', ' : '') . ($company->postal_code ? $company->postal_code . ' ' : '') . ($company->city ?? '')) }}</td></tr>
        @endif
        @if($company->nip)<tr><td>NIP:</td><td>{{ $company->nip }}</td></tr>@endif
        <tr><td>Osoba upoważniona:</td><td><strong>{{ $authorization->person_name }}</strong></td></tr>
        @if($authorization->person_identifier)<tr><td>Identyfikator pracownika:</td><td>{{ $authorization->person_identifier }}</td></tr>@endif
        @if($authorization->position)<tr><td>Stanowisko / funkcja:</td><td>{{ $authorization->position }}</td></tr>@endif
        <tr><td>Data nadania upoważnienia:</td><td>{{ $authorization->issued_at->format('d.m.Y') }}</td></tr>
        <tr><td>Okres obowiązywania:</td><td>
            @if($authorization->valid_until)
                do {{ $authorization->valid_until->format('d.m.Y') }}
            @else
                do odwołania lub ustania podstawy do przetwarzania danych w powierzonym zakresie
            @endif
        </td></tr>
    </table>

    <div class="section-title">Zakres upoważnienia</div>
    <div class="box scope">{{ $authorization->scope }}</div>

    <div class="legal">
        Na podstawie niniejszego upoważnienia osoba wskazana powyżej jest uprawniona do przetwarzania danych osobowych wyłącznie w zakresie niezbędnym do wykonywania powierzonych obowiązków oraz zgodnie z poleceniami administratora danych, obowiązującymi procedurami wewnętrznymi i przepisami o ochronie danych osobowych.
    </div>

    <div class="legal">
        Osoba upoważniona zobowiązana jest do zachowania w poufności danych osobowych oraz informacji dotyczących sposobów ich zabezpieczenia, także po ustaniu zatrudnienia, współpracy lub wygaśnięciu niniejszego upoważnienia. Upoważnienie nie uprawnia do przetwarzania danych poza wskazanym zakresem ani do udostępniania ich osobom nieuprawnionym.
    </div>

    <div class="legal">
        Dokument stanowi potwierdzenie nadania uprawnienia do przetwarzania danych osobowych w rozumieniu zasad wynikających z art. 29 oraz art. 32 ust. 4 RODO.
    </div>

    @if($authorization->revoked_at)
        <div class="status">
            <strong>Upoważnienie odwołane:</strong> {{ $authorization->revoked_at->format('d.m.Y H:i') }}
            @if($authorization->revocation_reason)<br>Powód: {{ $authorization->revocation_reason }}@endif
        </div>
    @endif

    <table class="signatures">
        <tr>
            <td><div class="line">podpis osoby upoważnionej</div></td>
            <td><div class="line">podpis administratora danych / osoby uprawnionej do nadania upoważnienia</div></td>
        </tr>
    </table>

    <div class="footer">
        Wygenerowano w IOD Manager · {{ now()->format('d.m.Y H:i') }}
    </div>
</body>
</html>
