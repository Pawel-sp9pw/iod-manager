<?php

declare(strict_types=1);

/**
 * IOD Manager - one-time web installer for aaPanel / classic PHP hosting.
 *
 * SECURITY:
 * - works only before storage/app/installed.lock exists,
 * - refuses to overwrite an existing .env,
 * - never stores submitted database/admin passwords in session or logs,
 * - creates the application key locally using random_bytes(),
 * - requires dependencies to be installed before it can run migrations.
 */

$root = dirname(__DIR__);
$lockFile = $root . '/storage/app/installed.lock';
$envFile = $root . '/.env';
$envExample = $root . '/.env.example';
$autoload = $root . '/vendor/autoload.php';
$bootstrap = $root . '/bootstrap/app.php';

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('Referrer-Policy: no-referrer');
header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline'; form-action 'self'; frame-ancestors 'none'; base-uri 'none'");
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (is_file($lockFile)) {
    http_response_code(403);
    exit('IOD Manager jest już zainstalowany. Usuń public/installer.php z serwera.');
}

session_name('iod_manager_installer');
session_set_cookie_params([
    'httponly' => true,
    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    'samesite' => 'Strict',
]);
session_start();

if (!isset($_SESSION['installer_csrf'])) {
    $_SESSION['installer_csrf'] = bin2hex(random_bytes(32));
}

function h(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function extensionStatus(string $extension): bool
{
    return extension_loaded($extension);
}

function updateEnvValue(string $contents, string $key, string $value): string
{
    $escaped = str_replace(["\\", '"', "\r", "\n"], ["\\\\", '\\"', '', '\\n'], $value);
    $line = $key . '="' . $escaped . '"';
    $pattern = '/^' . preg_quote($key, '/') . '=.*$/m';

    if (preg_match($pattern, $contents)) {
        return (string) preg_replace($pattern, $line, $contents, 1);
    }

    return rtrim($contents) . PHP_EOL . $line . PHP_EOL;
}

function render(array $errors = [], array $old = [], bool $success = false): never
{
    global $root, $envFile, $autoload;

    $requirements = [
        'PHP >= 8.3' => version_compare(PHP_VERSION, '8.3.0', '>='),
        'PDO MySQL' => extensionStatus('pdo_mysql'),
        'Mbstring' => extensionStatus('mbstring'),
        'OpenSSL' => extensionStatus('openssl'),
        'Intl' => extensionStatus('intl'),
        'BCMath' => extensionStatus('bcmath'),
        'Ctype' => extensionStatus('ctype'),
        'Fileinfo' => extensionStatus('fileinfo'),
        'Tokenizer' => extensionStatus('tokenizer'),
        'XML' => extensionStatus('xml'),
        'vendor/autoload.php' => is_file($autoload),
        'storage/ zapisywalny' => is_writable($root . '/storage'),
        'bootstrap/cache zapisywalny' => is_writable($root . '/bootstrap/cache'),
        '.env jeszcze nie istnieje' => !is_file($envFile),
    ];

    $allOk = !in_array(false, $requirements, true);
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $defaultUrl = $scheme . '://' . $host;
    ?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Instalator IOD Manager</title>
    <style>
        :root { color-scheme: dark; font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        body { margin:0; background:#0b1220; color:#e5e7eb; }
        main { width:min(900px, calc(100% - 32px)); margin:40px auto; }
        .card { background:#111827; border:1px solid #263244; border-radius:14px; padding:24px; margin:18px 0; box-shadow:0 12px 40px #0004; }
        h1,h2 { margin-top:0; }
        label { display:block; font-weight:650; margin:14px 0 6px; }
        input { width:100%; box-sizing:border-box; padding:11px 12px; border-radius:8px; border:1px solid #374151; background:#0b1220; color:#fff; }
        button { margin-top:20px; padding:12px 18px; border:0; border-radius:9px; font-weight:700; cursor:pointer; background:#e5e7eb; color:#111827; }
        button:disabled { opacity:.45; cursor:not-allowed; }
        .ok { color:#86efac; } .bad { color:#fca5a5; } .warn { color:#fcd34d; }
        .error { background:#3f1518; border:1px solid #7f1d1d; padding:12px; border-radius:8px; margin:8px 0; }
        .success { background:#12351f; border:1px solid #166534; padding:18px; border-radius:10px; }
        code { background:#030712; padding:2px 6px; border-radius:5px; }
        .grid { display:grid; grid-template-columns:1fr 1fr; gap:0 18px; }
        small { color:#9ca3af; }
        @media(max-width:700px){ .grid{grid-template-columns:1fr;} main{margin:18px auto;} }
    </style>
</head>
<body><main>
    <h1>IOD Manager — pierwsza instalacja</h1>
    <p>Instalator dla aaPanel / Nginx / Apache bez Dockera.</p>

    <?php if ($success): ?>
        <div class="success">
            <h2>Instalacja zakończona</h2>
            <p>Utworzono konfigurację, bazę aplikacji i konto administratora. Instalator został zablokowany plikiem <code>storage/app/installed.lock</code>.</p>
            <p><strong>Teraz usuń z serwera plik <code>public/installer.php</code>.</strong></p>
            <p>Przejdź do <a href="/login" style="color:#93c5fd">logowania</a>. Po zalogowaniu skonfiguruj 2FA.</p>
        </div>
    <?php else: ?>
        <section class="card">
            <h2>1. Kontrola serwera</h2>
            <?php foreach ($requirements as $name => $ok): ?>
                <div class="<?= $ok ? 'ok' : 'bad' ?>"><?= $ok ? '✓' : '✗' ?> <?= h($name) ?></div>
            <?php endforeach; ?>
            <?php if (!is_file($autoload)): ?>
                <p class="warn">Brakuje zależności PHP. W terminalu aaPanel, w katalogu projektu, wykonaj:<br><code>composer install --no-dev --optimize-autoloader</code></p>
            <?php endif; ?>
            <?php if (!is_writable($root . '/storage') || !is_writable($root . '/bootstrap/cache')): ?>
                <p class="warn">Nadaj użytkownikowi PHP/Nginx prawo zapisu do <code>storage/</code> i <code>bootstrap/cache/</code>.</p>
            <?php endif; ?>
            <?php if (is_file($envFile)): ?>
                <p class="bad">Plik <code>.env</code> już istnieje. Instalator celowo go nie nadpisze.</p>
            <?php endif; ?>
        </section>

        <?php foreach ($errors as $error): ?><div class="error"><?= h($error) ?></div><?php endforeach; ?>

        <form method="post" class="card" autocomplete="off">
            <input type="hidden" name="csrf" value="<?= h($_SESSION['installer_csrf']) ?>">
            <h2>2. Aplikacja</h2>
            <label>Adres aplikacji</label>
            <input type="url" name="app_url" required value="<?= h($old['app_url'] ?? $defaultUrl) ?>" placeholder="https://iod.twojadomena.pl">
            <small>Docelowo używaj HTTPS. SSL możesz skonfigurować w aaPanel.</small>

            <h2 style="margin-top:26px">3. MariaDB / MySQL</h2>
            <div class="grid">
                <div><label>Host bazy</label><input name="db_host" required value="<?= h($old['db_host'] ?? '127.0.0.1') ?>"></div>
                <div><label>Port</label><input name="db_port" required inputmode="numeric" value="<?= h($old['db_port'] ?? '3306') ?>"></div>
                <div><label>Nazwa bazy</label><input name="db_database" required value="<?= h($old['db_database'] ?? '') ?>"></div>
                <div><label>Użytkownik bazy</label><input name="db_username" required value="<?= h($old['db_username'] ?? '') ?>"></div>
            </div>
            <label>Hasło bazy</label>
            <input type="password" name="db_password" required autocomplete="new-password">

            <h2 style="margin-top:26px">4. Pierwszy administrator / IOD</h2>
            <label>Imię i nazwisko</label>
            <input name="admin_name" required maxlength="255" value="<?= h($old['admin_name'] ?? '') ?>">
            <label>E-mail</label>
            <input type="email" name="admin_email" required maxlength="255" value="<?= h($old['admin_email'] ?? '') ?>">
            <label>Hasło</label>
            <input type="password" name="admin_password" required autocomplete="new-password">
            <small>Minimum 14 znaków, mała i wielka litera, cyfra oraz znak specjalny.</small>
            <label>Powtórz hasło</label>
            <input type="password" name="admin_password_confirmation" required autocomplete="new-password">

            <button type="submit" <?= $allOk ? '' : 'disabled' ?>>Zainstaluj IOD Manager</button>
        </form>
    <?php endif; ?>
</main></body></html>
<?php
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    render();
}

$old = [
    'app_url' => trim((string)($_POST['app_url'] ?? '')),
    'db_host' => trim((string)($_POST['db_host'] ?? '')),
    'db_port' => trim((string)($_POST['db_port'] ?? '')),
    'db_database' => trim((string)($_POST['db_database'] ?? '')),
    'db_username' => trim((string)($_POST['db_username'] ?? '')),
    'admin_name' => trim((string)($_POST['admin_name'] ?? '')),
    'admin_email' => strtolower(trim((string)($_POST['admin_email'] ?? ''))),
];
$dbPassword = (string)($_POST['db_password'] ?? '');
$adminPassword = (string)($_POST['admin_password'] ?? '');
$adminPasswordConfirmation = (string)($_POST['admin_password_confirmation'] ?? '');
$errors = [];

if (!hash_equals($_SESSION['installer_csrf'], (string)($_POST['csrf'] ?? ''))) {
    $errors[] = 'Sesja instalatora wygasła lub token formularza jest nieprawidłowy.';
}
if (is_file($envFile)) {
    $errors[] = 'Plik .env już istnieje. Instalator nie nadpisuje istniejącej konfiguracji.';
}
if (!is_file($envExample) || !is_file($autoload) || !is_file($bootstrap)) {
    $errors[] = 'Brakuje plików aplikacji lub vendor/. Uruchom composer install --no-dev --optimize-autoloader.';
}
if (!version_compare(PHP_VERSION, '8.3.0', '>=') || !extensionStatus('pdo_mysql')) {
    $errors[] = 'Serwer nie spełnia minimalnych wymagań PHP 8.3 + pdo_mysql.';
}
if (!filter_var($old['app_url'], FILTER_VALIDATE_URL)) {
    $errors[] = 'Podaj poprawny URL aplikacji.';
}
if (!filter_var($old['admin_email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Podaj poprawny adres e-mail administratora.';
}
if ($old['admin_name'] === '') {
    $errors[] = 'Podaj imię i nazwisko administratora.';
}
if ($adminPassword !== $adminPasswordConfirmation) {
    $errors[] = 'Hasła administratora nie są identyczne.';
}
if (strlen($adminPassword) < 14 || !preg_match('/[a-z]/', $adminPassword) || !preg_match('/[A-Z]/', $adminPassword) || !preg_match('/\d/', $adminPassword) || !preg_match('/[^A-Za-z0-9]/', $adminPassword)) {
    $errors[] = 'Hasło administratora musi mieć minimum 14 znaków oraz małą i wielką literę, cyfrę i znak specjalny.';
}
if (!ctype_digit($old['db_port']) || (int)$old['db_port'] < 1 || (int)$old['db_port'] > 65535) {
    $errors[] = 'Port bazy danych jest nieprawidłowy.';
}
foreach (['db_host', 'db_database', 'db_username'] as $field) {
    if ($old[$field] === '') {
        $errors[] = 'Uzupełnij wszystkie dane połączenia z bazą.';
        break;
    }
}
if (!is_writable($root) || !is_writable($root . '/storage') || !is_writable($root . '/bootstrap/cache')) {
    $errors[] = 'Katalog projektu, storage lub bootstrap/cache nie ma wymaganych praw zapisu.';
}

if ($errors) {
    render($errors, $old);
}

try {
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $old['db_host'], (int)$old['db_port'], $old['db_database']);
    $pdo = new PDO($dsn, $old['db_username'], $dbPassword, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    $pdo->query('SELECT 1');
} catch (Throwable $e) {
    render(['Nie udało się połączyć z bazą danych. Sprawdź host, port, nazwę bazy, użytkownika i hasło.'], $old);
}

$env = (string) file_get_contents($envExample);
$appKey = 'base64:' . base64_encode(random_bytes(32));
$envValues = [
    'APP_NAME' => 'IOD Manager',
    'APP_ENV' => 'production',
    'APP_KEY' => $appKey,
    'APP_DEBUG' => 'false',
    'APP_URL' => $old['app_url'],
    'LOG_CHANNEL' => 'stack',
    'DB_CONNECTION' => 'mysql',
    'DB_HOST' => $old['db_host'],
    'DB_PORT' => $old['db_port'],
    'DB_DATABASE' => $old['db_database'],
    'DB_USERNAME' => $old['db_username'],
    'DB_PASSWORD' => $dbPassword,
    'SESSION_DRIVER' => 'database',
    'SESSION_SECURE_COOKIE' => str_starts_with(strtolower($old['app_url']), 'https://') ? 'true' : 'false',
    'SESSION_SAME_SITE' => 'lax',
    'CACHE_STORE' => 'database',
    'QUEUE_CONNECTION' => 'database',
];
foreach ($envValues as $key => $value) {
    $env = updateEnvValue($env, $key, (string)$value);
}

$tempEnv = $envFile . '.installer-' . bin2hex(random_bytes(6));
if (file_put_contents($tempEnv, $env, LOCK_EX) === false || !rename($tempEnv, $envFile)) {
    @unlink($tempEnv);
    render(['Nie udało się bezpiecznie utworzyć pliku .env. Sprawdź prawa zapisu katalogu projektu.'], $old);
}
@chmod($envFile, 0640);

try {
    require $autoload;
    $app = require $bootstrap;
    $kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
    $kernel->bootstrap();

    $exit = Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
    if ($exit !== 0) {
        throw new RuntimeException('Migracje Laravel zakończyły się kodem ' . $exit . '.');
    }

    $hash = password_hash($adminPassword, PASSWORD_ARGON2ID);
    if ($hash === false) {
        throw new RuntimeException('Nie udało się zahashować hasła przez Argon2id.');
    }

    $stmt = $pdo->prepare('INSERT INTO users (name, email, email_verified_at, password, is_super_admin, created_at, updated_at) VALUES (:name, :email, NOW(), :password, 1, NOW(), NOW())');
    $stmt->execute([
        ':name' => $old['admin_name'],
        ':email' => $old['admin_email'],
        ':password' => $hash,
    ]);

    Illuminate\Support\Facades\Artisan::call('config:clear');
    Illuminate\Support\Facades\Artisan::call('cache:clear');

    if (!is_dir(dirname($lockFile))) {
        mkdir(dirname($lockFile), 0750, true);
    }
    $lockData = 'installed_at=' . gmdate('c') . PHP_EOL;
    if (file_put_contents($lockFile, $lockData, LOCK_EX) === false) {
        throw new RuntimeException('Nie udało się utworzyć blokady instalatora.');
    }
    @chmod($lockFile, 0640);

    unset($dbPassword, $adminPassword, $adminPasswordConfirmation, $env);
    $_SESSION = [];
    session_destroy();

    render([], [], true);
} catch (Throwable $e) {
    // Do not expose exception details or credentials in the browser.
    // Leave .env in place so an administrator can inspect/fix the server manually;
    // no installed.lock is created, therefore installer remains available.
    render([
        'Instalacja nie została zakończona. Migracja lub tworzenie administratora zakończyło się błędem. Sprawdź logi PHP/Laravel w aaPanel oraz storage/logs/laravel.log. Plik .env został utworzony i dla bezpieczeństwa instalator nie będzie go automatycznie nadpisywał.',
    ], $old);
}
