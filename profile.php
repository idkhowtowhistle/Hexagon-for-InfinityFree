<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_login();

render_header('Profile');
?>
<section class="content">
    <div class="profile-hero">
        <div class="avatar-box">
            <img src="assets/hexagon512.png" alt="<?= e($user['username']) ?>">
        </div>

        <div>
            <h1><?= e($user['username']) ?></h1>
            <p class="subtle">Coins: <?= number_format((int) $user['coins']) ?></p>
            <p class="subtle">Joined <?= e(date('M j, Y', strtotime($user['joindate']))) ?></p>
        </div>
    </div>
</section>
<?php render_footer(); ?>
