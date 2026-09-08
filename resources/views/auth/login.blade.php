<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Logowanie — IOD Manager</title>
    @include('components.iod-styles')
</head>
<body>
<div class="iod-login">
    <section class="iod-login-hero">
        <div class="iod-login-copy">
            <div class="iod-login-pill"><span style="width:8px;height:8px;border-radius:999px;background:#34d399"></span> Bezpieczny panel Inspektora Ochrony Danych</div>
            <h1 class="iod-login-title">IOD Manager</h1>
            <p class="iod-login-text">Zarządzaj organizacjami, rejestrami RODO, upoważnieniami, terminami i bezpieczeństwem dostępu w jednym uporządkowanym miejscu.</p>
            <div class="iod-login-features">
                <div class="iod-login-feature">Wiele organizacji w jednym panelu</div>
                <div class="iod-login-feature">Rejestry i historia działań</div>
                <div class="iod-login-feature">Upoważnienia i odwołania</div>
                <div class="iod-login-feature">2FA i kontrola dostępu</div>
            </div>
        </div>
    </section>

    <section class="iod-login-panel">
        <div class="iod-login-card">
            <div class="iod-login-logo">IOD</div>
            <h2 class="iod-login-heading">Zaloguj się</h2>
            <p class="iod-login-subheading">Wprowadź dane konta, aby przejść do panelu IOD Manager.</p>

            @if($errors->any())
                <div class="iod-alert iod-alert-error">Nieprawidłowy e-mail lub hasło. Spróbuj ponownie.</div>
            @endif
            @if(session('status'))
                <div class="iod-alert iod-alert-success">{{ session('status') }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="iod-field">
                    <label for="email" class="iod-label">E-mail</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus class="iod-input" placeholder="iod@firma.pl">
                </div>
                <div class="iod-field">
                    <label for="password" class="iod-label">Hasło</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required class="iod-input" placeholder="••••••••••••••">
                </div>
                <label style="display:flex;align-items:center;gap:9px;font-size:13px;color:#64748b;margin:2px 0 18px">
                    <input type="checkbox" name="remember" style="width:16px;height:16px"> Zapamiętaj mnie na tym urządzeniu
                </label>
                <button type="submit" class="iod-btn-primary" style="width:100%;padding:12px 16px">Zaloguj się do panelu</button>
            </form>

            <div class="iod-login-footer">IOD Manager · dostęp wyłącznie dla uprawnionych użytkowników</div>
        </div>
    </section>
</div>
</body>
</html>
