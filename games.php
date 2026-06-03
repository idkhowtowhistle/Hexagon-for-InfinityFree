<?php
require __DIR__ . '/includes/bootstrap.php';

$games = tables_ready()
    ? db()->query('SELECT id, name, description, active_players FROM games ORDER BY id DESC')->fetchAll()
    : [];

render_header('Games');
?>
<section class="content">
    <div class="section-title">
        <h2>Games</h2>
        <span class="subtle"><?= count($games) ?> places</span>
    </div>

    <div class="grid">
        <?php foreach ($games as $game): ?>
            <article class="item-card">
                <div class="item-thumb">
                    <img src="assets/hexagon512.png" alt="">
                </div>
                <div class="item-body">
                    <h3><?= e($game['name']) ?></h3>
                    <p class="subtle"><?= e($game['description']) ?></p>
                    <strong><?= (int) $game['active_players'] ?> playing</strong>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php render_footer(); ?>
