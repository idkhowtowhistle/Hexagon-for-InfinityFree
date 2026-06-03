<?php
require __DIR__ . '/includes/bootstrap.php';

$message = '';
$error = '';

function install_schema(): void
{
    db()->exec("
        CREATE TABLE IF NOT EXISTS site_config (
            id TINYINT UNSIGNED NOT NULL PRIMARY KEY,
            page_clicker BIGINT UNSIGNED NOT NULL DEFAULT 0,
            sitealert VARCHAR(255) NOT NULL DEFAULT '',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    db()->exec("
        INSERT INTO site_config (id, page_clicker, sitealert)
        VALUES (1, 0, '')
        ON DUPLICATE KEY UPDATE id = id
    ");

    db()->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            username VARCHAR(20) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            coins BIGINT UNSIGNED NOT NULL DEFAULT 50,
            gender ENUM('male', 'female', 'nonbinary') NOT NULL DEFAULT 'nonbinary',
            role ENUM('user', 'mod', 'admin', 'owner') NOT NULL DEFAULT 'user',
            joindate TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            last_login TIMESTAMP NULL DEFAULT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    db()->exec("
        CREATE TABLE IF NOT EXISTS games (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(80) NOT NULL,
            description VARCHAR(255) NOT NULL DEFAULT '',
            active_players INT UNSIGNED NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY games_name_unique (name)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
    ");

    $stmt = db()->prepare("
        INSERT INTO games (name, description, active_players)
        VALUES
            ('Crossroads', 'Classic brick battle placeholder.', 0),
            ('Baseplate', 'A blank place ready for building.', 0),
            ('Sword Fight', 'Retro combat test place.', 0)
        ON DUPLICATE KEY UPDATE
            description = VALUES(description),
            active_players = VALUES(active_players)
    ");
    $stmt->execute();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    try {
        install_schema();
        $message = 'Instalacao concluida. Agora voce pode abrir a pagina inicial.';
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

render_header('Install');
?>
<section class="content">
    <h1>Install the backend for InfinityFree</h1>

    <div class="panel form-stack">
        <p>
            This page creates the MySQL tables used by the PHP version of Hexagon.
            Before continuing, please edit <strong>includes/config.php</strong> using data from the InfinityFree database.
        </p>

        <?php if (db_error()): ?>
            <div class="message error">
                Unable to connect to the database: <?= e(db_error()) ?>
            </div>
        <?php else: ?>
            <div class="message">
                MySQL connection OK.
                Table status: <?= tables_ready() ? 'ready' : 'not yet created' ?>.
            </div>
        <?php endif; ?>

        <?php if ($message): ?>
            <div class="message"><?= e($message) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="message error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="post">
            <?= csrf_field() ?>
            <button class="button" type="submit">Create tables</button>
            <a class="button ghost" href="index.php">Back</a>
        </form>
    </div>
</section>
<?php render_footer(); ?>
