<x-layouts.app title="Bezpieczeństwo / 2FA">
    <div class="iod-page-head">
        <div>
            <div class="iod-eyebrow">Bezpieczeństwo konta</div>
            <h1 class="iod-page-title">Uwierzytelnianie dwuskładnikowe</h1>
            <p class="iod-page-subtitle">TOTP zwiększa bezpieczeństwo konta IOD i jest wymagane do korzystania z aplikacji.</p>
        </div>
        <a href="{{ route('dashboard') }}" class="iod-btn-secondary">← Dashboard</a>
    </div>

    <div class="iod-grid iod-grid-2" style="align-items:start;max-width:980px">
        <section class="iod-card iod-card-pad">
            <div class="iod-card-title">Status 2FA</div>
            <div class="iod-card-subtitle">Skonfiguruj aplikację uwierzytelniającą dla tego konta.</div>
            <div style="margin-top:20px">
                @if(!auth()->user()->two_factor_secret)
                    <div class="iod-alert iod-alert-warning">2FA nie jest jeszcze skonfigurowane.</div>
                    <form method="POST" action="/user/two-factor-authentication">@csrf<button class="iod-btn-primary">Włącz 2FA</button></form>
                @else
                    <div class="iod-alert iod-alert-success">Klucz 2FA został utworzony.</div>
                    @if(!auth()->user()->two_factor_confirmed_at)
                        <p class="iod-page-subtitle" style="margin-bottom:14px">Zeskanuj kod QR, a następnie wpisz kod z aplikacji.</p>
                        <div style="display:inline-block;background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:14px;margin-bottom:18px">{!! auth()->user()->twoFactorQrCodeSvg() !!}</div>
                        <form method="POST" action="/user/confirmed-two-factor-authentication">@csrf
                            <div class="iod-field"><label class="iod-label">Kod potwierdzający</label><input name="code" inputmode="numeric" autocomplete="one-time-code" class="iod-input" placeholder="123456"></div>
                            <button class="iod-btn-primary">Potwierdź 2FA</button>
                        </form>
                    @else
                        <div class="iod-badge iod-badge-success">2FA aktywne</div>
                        <p class="iod-page-subtitle">Twoje konto jest chronione uwierzytelnianiem dwuskładnikowym.</p>
                    @endif
                @endif
            </div>
        </section>

        <section class="iod-card iod-card-pad">
            <div class="iod-card-title">Dobre praktyki</div>
            <div class="iod-card-subtitle">Zasady dla konta administratora / IOD.</div>
            <div class="iod-list" style="margin:14px -22px -22px">
                <div class="iod-list-item"><div><div class="iod-list-title">Aplikacja TOTP</div><div class="iod-list-meta">Używaj zaufanej aplikacji uwierzytelniającej.</div></div></div>
                <div class="iod-list-item"><div><div class="iod-list-title">Kody odzyskiwania</div><div class="iod-list-meta">Przechowuj je poza urządzeniem używanym do logowania.</div></div></div>
                <div class="iod-list-item"><div><div class="iod-list-title">Hasło</div><div class="iod-list-meta">Nie używaj go w innych usługach i regularnie kontroluj dostęp do konta.</div></div></div>
            </div>
        </section>
    </div>
</x-layouts.app>
