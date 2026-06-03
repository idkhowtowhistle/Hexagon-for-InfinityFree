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
    $gender = (string) ($_POST['gender'] ?? 'nonbinary');

    if (!tables_ready()) {
        $error = 'Configure o banco em includes/config.php e abra install.php antes de criar contas.';
    } elseif (!config('registration_enabled')) {
        $error = 'Registration is currently closed.';
    } elseif (!preg_match('/^[A-Za-z0-9_]{3,20}$/', $username)) {
        $error = 'Use 3-20 characters: letters, numbers, and underscores only.';
    } elseif (strlen($password) < 8) {
        $error = 'Use a password with at least 8 characters.';
    } elseif (!in_array($gender, ['male', 'female', 'nonbinary'], true)) {
        $error = 'Choose a valid gender option.';
    } else {
        try {
            $stmt = db()->prepare('INSERT INTO users (username, password_hash, gender) VALUES (?, ?, ?)');
            $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $gender]);

            $_SESSION['user_id'] = (int) db()->lastInsertId();
            session_regenerate_id(true);
            header('Location: home.php');
            exit;
        } catch (PDOException $exception) {
            $error = $exception->getCode() === '23000'
                ? 'That username is already taken.'
                : 'Could not create the account. Check your database settings.';
        }
    }
}

render_header('Landing');
?>
<video class="hero-video" autoplay muted loop playsinline>
    <source src="assets/HXGBLUR.webm" type="video/webm">
</video>
<div class="hero-scrim"></div>

<section class="landing">
    <div class="landing-card">
        <audio src="assets/hexabite/3_sndBite1.mp3" data-bite-audio></audio>
        <audio src="assets/hexabite/1_sndStart.mp3" data-done-audio></audio>

        <div class="logo-wrap">
            <div class="logo-lockup">
                <small>Project</small>
                <button type="button" data-clicker-button aria-label="Click Hexagon">
                    <img src="assets/hexagon512.png" alt="H" data-hex-icon>
                </button>
                <span>exagon</span>
                <img class="noob" src="assets/noob.gif" alt="">
            </div>
        </div>

        <p class="emote" data-emote>(^_^)</p>
        <p class="clicker-text">Has been clicked <strong data-clicker-count><?= page_clicker() ?></strong> times.</p>

        <?php if ($error): ?>
            <div class="message error"><?= e($error) ?></div>
        <?php endif; ?>

        <?php if (config('registration_enabled')): ?>
            <form class="form-stack" method="post">
                <?= csrf_field() ?>
                <div class="field">
                    <label for="username">Username</label>
                    <input id="username" name="username" autocomplete="username" placeholder="(3-20 Characters, no spaces)" required>
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="new-password" placeholder="(Unique)" required>
                </div>

                <div class="field">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="nonbinary" selected>None</option>
                    </select>
                </div>

                <button class="button" type="submit">Sign up</button>
            </form>
        <?php else: ?>
            <h2 class="form-note">Sorry! Registration is currently closed.</h2>
        <?php endif; ?>

        <p class="form-note">
            By clicking Sign up, you agree to the <a href="terms.php">Terms</a> and <a href="privacy.php">Privacy</a>.
        </p>
    </div>
</section>
<?php render_footer(); ?>
