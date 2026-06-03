<?php

declare(strict_types=1);

session_start();

$config = require __DIR__ . '/config.php';

function config(string $key, mixed $fallback = null): mixed
{
    global $config;
    return $config[$key] ?? $fallback;
}

function has_real_db_config(): bool
{
    return !str_contains((string) config('db_host'), 'sqlXXX')
        && !str_contains((string) config('db_name'), '00000000')
        && !str_contains((string) config('db_user'), '00000000')
        && (string) config('db_pass') !== 'CHANGE_ME';
}

function db(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    if (!has_real_db_config()) {
        throw new RuntimeException('Edite includes/config.php com os dados MySQL do InfinityFree.');
    }

    $dsn = sprintf(
        'mysql:host=%s;dbname=%s;charset=utf8mb4',
        config('db_host'),
        config('db_name')
    );

    $pdo = new PDO($dsn, config('db_user'), config('db_pass'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    return $pdo;
}

function db_error(): ?string
{
    try {
        db();
        return null;
    } catch (Throwable $exception) {
        return $exception->getMessage();
    }
}

function db_ready(): bool
{
    return db_error() === null;
}

function tables_ready(): bool
{
    if (!db_ready()) {
        return false;
    }

    try {
        db()->query('SELECT 1 FROM site_config LIMIT 1');
        db()->query('SELECT 1 FROM users LIMIT 1');
        return true;
    } catch (Throwable) {
        return false;
    }
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function verify_csrf(): void
{
    $token = $_POST['csrf'] ?? '';

    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        exit('Invalid session token.');
    }
}

function flash(?string $message = null): ?string
{
    if ($message !== null) {
        $_SESSION['flash'] = $message;
        return null;
    }

    $message = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $message;
}

function current_user(): ?array
{
    if (empty($_SESSION['user_id'])) {
        return null;
    }

    if (!tables_ready()) {
        return null;
    }

    $stmt = db()->prepare('SELECT id, username, coins, gender, role, joindate FROM users WHERE id = ? LIMIT 1');
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();

    return $user ?: null;
}

function require_login(): array
{
    $user = current_user();

    if (!$user) {
        header('Location: login.php');
        exit;
    }

    return $user;
}

function page_clicker(): int
{
    if (!tables_ready()) {
        return 0;
    }

    try {
        $stmt = db()->query('SELECT page_clicker FROM site_config WHERE id = 1');
        $value = $stmt->fetchColumn();
    } catch (Throwable) {
        return 0;
    }

    return $value === false ? 0 : (int) $value;
}

function site_alert(): string
{
    if (!tables_ready()) {
        return '';
    }

    try {
        $stmt = db()->query('SELECT sitealert FROM site_config WHERE id = 1');
        $value = $stmt->fetchColumn();
    } catch (Throwable) {
        return '';
    }

    return $value === false ? '' : (string) $value;
}

function render_header(string $title = ''): void
{
    $user = current_user();
    $fullTitle = trim($title) !== '' ? $title . ' - ' . config('app_name') : config('app_name');
    $alert = site_alert();
    $setupNeeded = !tables_ready();
    ?>
    <!doctype html>
    <html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($fullTitle) ?></title>
        <link rel="icon" href="assets/favicon.ico">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&display=swap&text=ProjectHexagon" rel="stylesheet">
        <link rel="stylesheet" href="assets/styles.css">
        <script src="assets/app.js" defer></script>
    </head>
    <body>
        <div class="page-shell">
            <header class="topbar">
                <nav class="nav-inner">
                    <a class="brand" href="<?= $user ? 'home.php' : 'index.php' ?>" aria-label="Project Hexagon">
                        <img class="brand-full" src="assets/hexagonfull.png" alt="Hexagon">
                        <img class="brand-mark" src="assets/hexagon128.png" alt="H">
                    </a>

                    <div class="nav-links">
                        <a href="home.php">Home</a>
                        <a href="games.php">Games</a>
                        <a href="catalog.php">Catalog</a>
                        <a href="people.php">People</a>
                        <a href="develop.php">Develop</a>
                    </div>

                    <div class="account-links">
                        <?php if ($user): ?>
                            <a class="coin-pill" href="home.php">Moon <?= number_format((int) $user['coins']) ?></a>
                            <a class="button ghost" href="logout.php">Logout</a>
                        <?php else: ?>
                            <a class="button ghost" href="login.php">Log in</a>
                            <a class="button" href="index.php">Sign up</a>
                        <?php endif; ?>
                    </div>
                </nav>

                <?php if ($user && $alert !== ''): ?>
                    <div class="site-alert"><?= e($alert) ?></div>
                <?php endif; ?>

                <?php if ($setupNeeded): ?>
                    <div class="site-alert">
                        Banco nao configurado. Abra <a href="install.php">install.php</a> depois de editar includes/config.php.
                    </div>
                <?php endif; ?>
            </header>

            <main class="<?= $title === 'Landing' ? 'main no-pad' : 'main' ?>">
    <?php
}

function render_footer(): void
{
    ?>
            </main>
            <footer class="footer">
                <div class="footer-links">
                    <a href="privacy.php">Privacy</a>
                    <a href="terms.php">Terms</a>
                </div>
                <div class="footer-row">
                    <img src="assets/hexagon512.png" alt="H">
                    <span></span>
                    <strong>Project Hexagon</strong>
                </div>
            </footer>
        </div>
    </body>
    </html>
    <?php
}
