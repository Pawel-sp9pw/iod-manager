<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>2FA — IOD Manager</title>
    <link rel="stylesheet" href="{{ asset('css/iod-manager.css') }}">
</head>
<body>
<div class="iod-login">
    <section class="iod-login-hero">
        <div class="iod-login-copy">
            <div class="iod-login-pill"><span style="width:8px;height:8px;border-radius:999px;background:#34d399"></span> Drugi etap logowania</div>
            <h1 class="iod-login-title">Potwierdź tożsamość</h1>
            <p class="iod-login-text">Wprowadź kod TOTP z aplikacji uwierzytelniającej albo użyj jednego z kodów odzyskiwania.</p>
        </div>
    </section>
    <section class="iod-login-panel">
        <div class="iod-login-card">
            <div class="iod-login-logo">2FA</div>
            <h2 class="iod-login-heading">Uwierzytelnianie dwuskładnikowe</h2>
            <p class="iod-login-subheading">Podaj 6-cyfrowy kod z aplikacji lub kod odzyskiwania.</p>
            @if($errors->any())<div class="iod-alert iod-alert-error">Kod jest nieprawidłowy. Spróbuj ponownie.</div>@endif
            <form method="POST" action="/two-factor-challenge">
                @csrf
                <div class="iod-field"><label class="iod-label">Kod TOTP</label><input name="code" inputmode="numeric" autocomplete="one-time-code" autofocus class="iod-input" placeholder="123456"></div>
                <div style="text-align:center;font-size:11px;color:#94a3b8;margin:14px 0">lub</div>
                <div class="iod-field"><label class="iod-label">Kod odzyskiwania</label><input name="recovery_code" autocomplete="one-time-code" class="iod-input" placeholder="Kod odzyskiwania"></div>
                <button class="iod-btn-primary" style="width:100%;padding:12px 16px">Potwierdź i zaloguj</button>
            </form>
        </div>
    </section>
</div>
</body>
</html>
