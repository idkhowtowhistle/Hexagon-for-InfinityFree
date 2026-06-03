<?php
require __DIR__ . '/includes/bootstrap.php';

$users = tables_ready()
    ? db()->query('SELECT id, username, joindate FROM users ORDER BY id DESC LIMIT 50')->fetchAll()
    : [];

render_header('People');
?>
<section class="content">
    <div class="section-title">
        <h2>People</h2>
        <span class="subtle"><?= count($users) ?> recent users</span>
    </div>

    <div class="grid">
        <?php foreach ($users as $member): ?>
            <article class="item-card">
                <div class="item-thumb">
                    <img src="assets/hexagon512.png" alt="">
                </div>
                <div class="item-body">
                    <h3><?= e($member['username']) ?></h3>
                    <p class="subtle">Joined <?= e(date('M j, Y', strtotime($member['joindate']))) ?></p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php render_footer(); ?>
