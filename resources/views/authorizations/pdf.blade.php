<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <title>Upoważnienie do przetwarzania danych osobowych</title>
    <style>
        @page { margin: 10mm 16mm 12mm 16mm; }
        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 8.8pt;
            line-height: 1.28;
            color: #111827;
        }
        h1 {
            margin: 0 0 3px;
            font-size: 14pt;
            line-height: 1.15;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: .2px;
        }
        .subtitle {
            text-align: center;
            font-size: 8.5pt;
            color: #4b5563;
            margin-bottom: 10px;
        }
        .meta {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 7px;
        }
        .meta td {
            padding: 2px 0;
            vertical-align: top;
        }
        .meta td:first-child {
            width: 34%;
            color: #4b5563;
        }
        .section-title {
            margin: 8px 0 4px;
            font-weight: bold;
            font-size: 9pt;
        }
        .box {
            border: 1px solid #cbd5e1;
            padding: 6px 8px;
            margin-bottom: 6px;
        }
        .scope {
            white-space: pre-line;
        }
        .legal {
            text-align: justify;
            font-size: 8.1pt;
            line-height: 1.25;
            color: #374151;
            margin-top: 6px;
        }
        .legal-basis {
            margin-top: 7px;
            padding-top: 5px;
            border-top: 1px solid #e5e7eb;
            font-size: 7.8pt;
            line-height: 1.22;
            color: #4b5563;
        }
        .status {
            margin-top: 7px;
            border: 1px solid #fecaca;
            background: #fff1f2;
            padding: 5px 7px;
            font-size: 8pt;
            color: #9f1239;
        }
        .signatures {
            width: 100%;
            border-collapse: collapse;
            margin-top: 14px;
        }
        .signatures td {
            width: 50%;
            text-align: center;
            padding: 0 10px;
            vertical-align: top;
        }
        .line {
            border-top: 1px solid #374151;
            margin-top: 28px;
            padding-top: 3px;
            font-size: 7.4pt;
            color: #4b5563;
        }
        .footer {
            position: fixed;
            bottom: -6mm;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 6.8pt;
            color: #9ca3af;
        }
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
        <tr>
            <td>Administrator danych / organizacja:</td>
            <td><strong>{{ $company->name }}</strong></td>
        </tr>
        @if($company->address || $company->postal_code || $company->city)
            <tr>
                <td>Adres:</td>
                <td>{{ trim(($company->address ? $company->address . ', ' : '') . ($company->postal_code ? $company->postal_code . ' ' : '') . ($company->city ?? '')) }}</td>
            </tr>
        @endif
        @if($company->nip)
            <tr><td>NIP:</td><td>{{ $company->nip }}</td></tr>
        @endif
        <tr>
            <td>Osoba upoważniona:</td>
            <td><strong>{{ $authorization->person_name }}</strong></td>
        </tr>
        @if($authorization->person_identifier)
            <tr><td>Identyfikator pracownika:</td><td>{{ $authorization->person_identifier }}</td></tr>
        @endif
        @if($authorization->position)
            <tr><td>Stanowisko / funkcja:</td><td>{{ $authorization->position }}</td></tr>
        @endif
        <tr>
            <td>Data nadania:</td>
            <td>{{ $authorization->issued_at->format('d.m.Y') }}</td>
        </tr>
        <tr>
            <td>Okres obowiązywania:</td>
            <td>
                @if($authorization->valid_until)
                    do {{ $authorization->valid_until->format('d.m.Y') }}
                @else
                    do odwołania lub ustania podstawy do przetwarzania danych w powierzonym zakresie
                @endif
            </td>
        </tr>
    </table>

    <div class="section-title">Zakres upoważnienia</div>
    <div class="box scope">{{ $authorization->scope }}</div>

    <div class="legal">
        Osoba wskazana powyżej zostaje upoważniona do przetwarzania danych osobowych wyłącznie w zakresie niezbędnym do wykonywania powierzonych obowiązków, zgodnie z poleceniami administratora danych, obowiązującymi procedurami wewnętrznymi oraz przepisami o ochronie danych osobowych.
    </div>

    <div class="legal">
        Osoba upoważniona jest zobowiązana do zachowania w poufności danych osobowych oraz informacji dotyczących sposobów ich zabezpieczenia, również po wygaśnięciu niniejszego upoważnienia, oraz do niedopuszczania osób nieuprawnionych do danych.
    </div>

    <div class="legal-basis">
        <strong>Podstawa organizacyjna upoważnienia:</strong> art. 29 oraz art. 32 ust. 1 i 4 rozporządzenia Parlamentu Europejskiego i Rady (UE) 2016/679 (RODO), w związku z zasadą rozliczalności określoną w art. 5 ust. 2 oraz obowiązkiem administratora wynikającym z art. 24 ust. 1 RODO. Niniejsze upoważnienie nie zastępuje właściwej podstawy prawnej przetwarzania danych przez administratora, w szczególności podstaw wynikających z art. 6 i — gdy ma zastosowanie — art. 9 RODO.
    </div>

    @if($authorization->revoked_at)
        <div class="status">
            <strong>Upoważnienie odwołane:</strong> {{ $authorization->revoked_at->format('d.m.Y H:i') }}
            @if($authorization->revocation_reason)<br>Powód: {{ $authorization->revocation_reason }}@endif
        </div>
    @endif

    <table class="signatures">
        <tr>
            <td><div class="line">data i podpis osoby upoważnionej</div></td>
            <td><div class="line">data i podpis administratora danych / osoby uprawnionej</div></td>
        </tr>
    </table>

    <div class="footer">
        Wygenerowano w IOD Manager · {{ now()->format('d.m.Y H:i') }}
    </div>
</body>
</html>
