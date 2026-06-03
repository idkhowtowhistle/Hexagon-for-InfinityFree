<?php
require __DIR__ . '/includes/bootstrap.php';
render_header('Catalog');
?>
<section class="content">
    <div class="section-title">
        <h2>Catalog</h2>
        <span class="subtle">Starter frontend</span>
    </div>

    <div class="grid">
        <?php foreach (['Classic Hat', 'Solid Model', 'Starter Shirt'] as $item): ?>
            <article class="item-card">
                <div class="item-thumb">
                    <img src="assets/hexagon512.png" alt="">
                </div>
                <div class="item-body">
                    <h3><?= e($item) ?></h3>
                    <p class="subtle">This item can be wired to a MySQL catalog table next.</p>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php render_footer(); ?>
