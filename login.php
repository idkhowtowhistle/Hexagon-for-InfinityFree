<?php
require __DIR__ . '/includes/bootstrap.php';

if (current_user()) {
    header('Location: home.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if (!tables_ready()) {
        $error = 'Configure o banco em includes/config.php e abra install.php antes de entrar.';
    } else {
        $stmt = db()->prepare('SELECT id, password_hash FROM users WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = (int) $user['id'];
            session_regenerate_id(true);

            db()->prepare('UPDATE users SET last_login = CURRENT_TIMESTAMP WHERE id = ?')->execute([$user['id']]);

            header('Location: home.php');
            exit;
        }

        $error = 'Invalid username or password.';
    }
}

render_header('Log in');
?>
<section class="auth-page">
    <div class="auth-card panel">
        <div class="logo-wrap">
            <div class="logo-lockup">
                <small>Project</small>
                <img src="assets/hexagon512.png" alt="H">
                <span>exagon</span>
            </div>
        </div>

        <h1>Log in to Hexagon</h1>

        <?php if ($error): ?>
            <div class="message error"><?= e($error) ?></div>
        <?php endif; ?>

        <form class="form-stack" method="post">
            <?= csrf_field() ?>
            <div class="field">
                <label for="username">Username</label>
                <input id="username" name="username" autocomplete="username" required>
            </div>

            <div class="field">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required>
            </div>

            <button class="button" type="submit">Log in</button>
        </form>

        <p class="form-note">Need an account? <a href="index.php">Sign up</a>.</p>
    </div>
</section>
<?php render_footer(); ?>
