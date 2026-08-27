# IOD Manager

Self-hosted aplikacja dla Inspektora Ochrony Danych zarządzającego wieloma organizacjami.

## Technologia

- PHP 8.3+
- Laravel 13
- Laravel Fortify + TOTP 2FA
- Blade + Alpine.js + Vite
- MariaDB / MySQL
- klasyczny hosting Nginx/Apache, przygotowany pod aaPanel

Docker nie jest wymagany ani używany w docelowym wdrożeniu.

## Główne funkcje MVP

- wiele firm z izolacją danych,
- globalne konto IOD,
- konta ograniczone do konkretnej firmy,
- role `iod` i `company_admin`,
- osobne prawo zapisu `can_write`,
- rejestry RODO i wpisy,
- wydawanie i odwoływanie upoważnień z historią,
- przypomnienia jednorazowe i cykliczne,
- historia wykonania przypomnień,
- audit log,
- silne hasła,
- TOTP 2FA i recovery codes,
- zabezpieczenie CSRF, rate limiting oraz polityki dostępu Laravel.

## Instalacja na aaPanel

### 1. Utwórz stronę

W aaPanel utwórz stronę dla wybranej domeny/subdomeny i ustaw PHP **8.3 lub nowsze**.

Katalog projektu może przykładowo wyglądać tak:

```text
/www/wwwroot/iod.twojadomena.pl/iod-manager
```

**Document Root strony musi wskazywać na katalog:**

```text
/www/wwwroot/iod.twojadomena.pl/iod-manager/public
```

Nie ustawiaj katalogu głównego repozytorium jako publicznego katalogu WWW.

### 2. Pobierz projekt

W terminalu aaPanel:

```bash
cd /www/wwwroot/iod.twojadomena.pl
git clone https://github.com/Pawel-sp9pw/iod-manager.git
cd iod-manager
```

### 3. Zainstaluj zależności

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

Jeżeli Node.js/NPM nie jest dostępny globalnie, można zbudować frontend na innym komputerze i przesłać gotowy katalog `public/build`.

### 4. Uprawnienia katalogów

Użytkownik, na którym działa PHP, musi mieć prawo zapisu do:

```text
storage/
bootstrap/cache/
```

Przykład — dostosuj użytkownika/grupę do swojej konfiguracji aaPanel:

```bash
chown -R www:www storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
```

### 5. Utwórz bazę w aaPanel

W aaPanel utwórz pustą bazę MariaDB/MySQL oraz osobnego użytkownika bazy. Nie używaj konta `root` aplikacji.

### 6. Uruchom instalator WWW

Wejdź na:

```text
https://twoja-domena.pl/installer.php
```

Instalator:

- sprawdzi PHP i wymagane rozszerzenia,
- sprawdzi prawa zapisu,
- sprawdzi połączenie z MariaDB/MySQL,
- utworzy `.env`,
- wygeneruje `APP_KEY`,
- ustawi produkcyjne parametry aplikacji,
- wykona migracje Laravel,
- utworzy pierwsze konto administratora/IOD,
- zahashuje jego hasło Argon2id,
- utworzy `storage/app/installed.lock` blokujący ponowną instalację.

Hasło administratora musi mieć minimum 14 znaków, małą i wielką literę, cyfrę oraz znak specjalny.

Po udanej instalacji **usuń z serwera:**

```text
public/installer.php
```

Plik blokady pozostaw:

```text
storage/app/installed.lock
```

### 7. HTTPS

Włącz certyfikat Let's Encrypt w aaPanel i wymuś HTTPS. Po instalacji `.env` powinien zawierać m.in.:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://twoja-domena.pl
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

### 8. Rewrite dla Laravel

Document Root musi wskazywać na `public/`. Dla Nginx wymagany jest standardowy fallback Laravel:

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

aaPanel zwykle pozwala ustawić reguły rewrite z poziomu konfiguracji strony.

### 9. Scheduler

W aaPanel dodaj zadanie Cron wykonywane co minutę:

```bash
cd /www/wwwroot/iod.twojadomena.pl/iod-manager && php artisan schedule:run >> /dev/null 2>&1
```

Scheduler będzie obsługiwał między innymi cykliczne przypomnienia.

### 10. Kolejka

Na początku aplikacja może działać z kolejką bazodanową. Jeśli używane będą zadania asynchroniczne/powiadomienia, uruchom stale:

```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

W aaPanel najlepiej uruchomić worker przez Supervisor/Process Manager z automatycznym restartem.

## Bezpieczeństwo

- `.env` nigdy nie powinien być dostępny z WWW,
- Document Root zawsze ustawiaj na `public/`,
- nie commituj `.env`, dumpów baz danych, sekretów 2FA ani kluczy aplikacji,
- po instalacji usuń `public/installer.php`,
- wykonuj szyfrowane kopie bazy i `storage/app`,
- regularnie testuj odtwarzanie backupu,
- konto bazy aplikacji powinno mieć dostęp tylko do bazy IOD Managera,
- dla serwera produkcyjnego wyłącz `display_errors`,
- nie wystawiaj panelu bazy danych publicznie bez dodatkowego zabezpieczenia.

## Model dostępu

Każdy rekord operacyjny jest związany z firmą (`company_id`). Użytkownik bez uprawnień globalnych widzi wyłącznie firmy przypisane przez `company_user`.

- `is_super_admin = true` — administracja całej instalacji,
- `iod` — dostęp operacyjny dla przypisanej firmy,
- `company_admin` — konto firmy, domyślnie do podglądu,
- `can_write` — jawne prawo modyfikacji danych w danej firmie.

## Po pierwszym uruchomieniu

Zaloguj się kontem utworzonym w instalatorze i skonfiguruj TOTP 2FA wraz z recovery codes. Następnie dodaj firmy, którymi zarządzasz, oraz ewentualne konta administratorów firm.
