<?php

declare(strict_types=1);

/**
 * IOD Manager - destructive installation reset tool.
 *
 * CLI only. It:
 *  - reads current DB credentials from .env,
 *  - shows the target database before doing anything,
 *  - requires an exact confirmation phrase,
 *  - drops all views/tables in the configured database,
 *  - removes .env and storage/app/installed.lock,
 *  - clears Laravel runtime caches,
 *  - keeps the database itself, DB user, vendor/ and public/build intact.
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("Ten skrypt można uruchomić wyłącznie z CLI.\n");
}

$root = dirname(__DIR__);
$envFile = $root . '/.env';
$lockFile = $root . '/storage/app/installed.lock';
$confirmationPhrase = 'RESET IOD MANAGER';

function fail(string $message, int $code = 1): never
{
    fwrite(STDERR, "BŁĄD: {$message}\n");
    exit($code);
}

function envValue(string $contents, string $key): ?string
{
    if (!preg_match('/^' . preg_quote($key, '/') . '=(.*)$/m', $contents, $match)) {
        return null;
    }

    $value = trim($match[1]);

    if ($value === '') {
        return '';
    }

    if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
        (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
        $quote = $value[0];
        $value = substr($value, 1, -1);
        if ($quote === '"') {
            $value = stripcslashes($value);
        }
    } else {
        $value = preg_replace('/\s+#.*$/', '', $value) ?? $value;
    }

    return $value;
}

function clearDirectory(string $directory): void
{
    if (!is_dir($directory)) {
        return;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($directory, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($iterator as $item) {
        /** @var SplFileInfo $item */
        if ($item->getFilename() === '.gitignore') {
            continue;
        }

        $path = $item->getPathname();
        if ($item->isLink() || $item->isFile()) {
            if (!@unlink($path)) {
                fail("Nie udało się usunąć pliku: {$path}");
            }
        } elseif ($item->isDir()) {
            @rmdir($path);
        }
    }
}

if (!is_file($envFile)) {
    fail('Brak pliku .env. Nie znam danych bazy do wyczyszczenia. Jeśli baza jest już pusta, aplikacja jest gotowa do ponownego uruchomienia installer.php.');
}

$env = file_get_contents($envFile);
if ($env === false) {
    fail('Nie udało się odczytać pliku .env.');
}

$connection = envValue($env, 'DB_CONNECTION') ?? 'mysql';
$host = envValue($env, 'DB_HOST') ?? '127.0.0.1';
$port = envValue($env, 'DB_PORT') ?? '3306';
$database = envValue($env, 'DB_DATABASE');
$username = envValue($env, 'DB_USERNAME');
$password = envValue($env, 'DB_PASSWORD') ?? '';

if (!in_array($connection, ['mysql', 'mariadb'], true)) {
    fail("Skrypt resetujący obsługuje tylko MariaDB/MySQL. DB_CONNECTION={$connection}");
}

if ($database === null || $database === '' || $username === null || $username === '') {
    fail('W .env brakuje DB_DATABASE lub DB_USERNAME.');
}

if (!ctype_digit((string) $port) || (int) $port < 1 || (int) $port > 65535) {
    fail('Nieprawidłowy DB_PORT w .env.');
}

if (!extension_loaded('pdo_mysql')) {
    fail('Brakuje rozszerzenia PHP pdo_mysql.');
}

fwrite(STDOUT, "\nIOD Manager — RESET INSTALACJI\n");
fwrite(STDOUT, "================================\n");
fwrite(STDOUT, "Host DB : {$host}:{$port}\n");
fwrite(STDOUT, "Baza    : {$database}\n");
fwrite(STDOUT, "Użytk.  : {$username}\n");
fwrite(STDOUT, "\nUWAGA: wszystkie tabele i widoki w tej bazie zostaną BEZPOWROTNIE usunięte.\n");
fwrite(STDOUT, "Sama baza i użytkownik DB pozostaną. vendor/ i public/build pozostaną.\n\n");
fwrite(STDOUT, "Aby kontynuować wpisz dokładnie: {$confirmationPhrase}\n> ");

$input = trim((string) fgets(STDIN));
if (!hash_equals($confirmationPhrase, $input)) {
    fwrite(STDOUT, "Anulowano. Nie dokonano żadnych zmian.\n");
    exit(0);
}

try {
    $dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4', $host, (int) $port, $database);
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_NUM,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');

    // Drop views first so no view remains dependent on a table being removed.
    $views = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'VIEW'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($views as $view) {
        $quoted = '`' . str_replace('`', '``', (string) $view) . '`';
        $pdo->exec("DROP VIEW IF EXISTS {$quoted}");
        fwrite(STDOUT, "Usunięto widok: {$view}\n");
    }

    $tables = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        $quoted = '`' . str_replace('`', '``', (string) $table) . '`';
        $pdo->exec("DROP TABLE IF EXISTS {$quoted}");
        fwrite(STDOUT, "Usunięto tabelę: {$table}\n");
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
} catch (Throwable $e) {
    fail('Nie udało się wyczyścić bazy. Szczegóły: ' . $e->getMessage());
}

if (is_file($lockFile) && !@unlink($lockFile)) {
    fail("Baza została wyczyszczona, ale nie udało się usunąć {$lockFile}. Usuń go ręcznie przed ponowną instalacją.");
}

clearDirectory($root . '/storage/framework/cache/data');
clearDirectory($root . '/storage/framework/sessions');
clearDirectory($root . '/storage/framework/views');

foreach (glob($root . '/bootstrap/cache/*.php') ?: [] as $cacheFile) {
    if (basename($cacheFile) !== '.gitignore' && !@unlink($cacheFile)) {
        fail("Nie udało się usunąć cache Laravel: {$cacheFile}");
    }
}

if (!@unlink($envFile)) {
    fail('Baza została wyczyszczona, ale nie udało się usunąć .env. Usuń go ręcznie przed uruchomieniem installer.php.');
}

fwrite(STDOUT, "\nRESET ZAKOŃCZONY POMYŚLNIE.\n");
fwrite(STDOUT, "- baza jest pusta,\n");
fwrite(STDOUT, "- .env został usunięty,\n");
fwrite(STDOUT, "- installed.lock został usunięty,\n");
fwrite(STDOUT, "- cache/sesje/widoki Laravel zostały wyczyszczone.\n\n");
fwrite(STDOUT, "Teraz wejdź na /installer.php i wykonaj instalację od początku.\n");
