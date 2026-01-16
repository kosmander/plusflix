<?php
/** @var array $stats */
/** @var \App\Service\Router $router */
/** @var string $admin_login */

// budujemy content dla layoutu
ob_start();
?>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3>Produkcje</h3>
        <p class="stat-number"><?= $stats['productions'] ?? 0 ?></p>
        <a href="<?= $router->generatePath('admin-productions') ?>">Zarządzaj</a>
    </div>

    <div class="stat-card">
        <h3>Platformy</h3>
        <p class="stat-number"><?= $stats['platforms'] ?? 0 ?></p>
        <a href="<?= $router->generatePath('admin-platforms') ?>">Zarządzaj</a>
    </div>

    <div class="stat-card">
        <h3>Kategorie</h3>
        <p class="stat-number"><?= $stats['categories'] ?? 0 ?></p>
        <a href="<?= $router->generatePath('admin-categories') ?>">Zarządzaj</a>
    </div>

    <div class="stat-card">
        <h3>Oceny do moderacji</h3>
        <p class="stat-number"><?= $stats['ratings_pending'] ?? 0 ?></p>
        <span class="stat-info">oczekujące</span>
    </div>
</div>

<div class="quick-actions">
    <h3>Szybkie akcje</h3>
    <a href="<?= $router->generatePath('admin-productions-create') ?>" class="btn btn-primary">+ Dodaj produkcję</a>
    <a href="<?= $router->generatePath('admin-platforms-create') ?>" class="btn btn-secondary">+ Dodaj platformę</a>
    <a href="<?= $router->generatePath('admin-categories-create') ?>" class="btn btn-secondary">+ Dodaj kategorię</a>
</div>

<?php
$content = ob_get_clean();
$page_title = 'Dashboard';
$title = 'Dashboard';

// dolaczamy layout
require __DIR__ . '/layout.html.php';
?>
