<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_login();

render_header('Home');
?>
<section class="content">
    <div class="profile-hero">
        <a class="avatar-box" href="profile.php">
            <img src="assets/hexagon512.png" alt="<?= e($user['username']) ?>">
        </a>

        <h1>Welcome back, <?= e($user['username']) ?>...</h1>
    </div>

    <div class="section-title">
        <h2>Friends</h2>
        <a class="subtle" href="people.php">See all</a>
    </div>
    <div class="panel">
        <div class="empty">Friends are ready for a future upgrade.</div>
    </div>

    <div class="section-title">
        <h2>Recently Played</h2>
        <a class="subtle" href="games.php">See all</a>
    </div>
    <div class="panel">
        <div class="empty">Maybe play some <a href="games.php">games</a>?</div>
    </div>
</section>
<?php render_footer(); ?>
