<?php
/** @var array $platforms */
/** @var \App\Service\Router $router */
/** @var string|null $success */

ob_start();
?>

<?php if ($success === 'created'): ?>
    <div class="alert alert-success">Platforma została dodana!</div>
<?php elseif ($success === 'updated'): ?>
    <div class="alert alert-success">Platforma została zaktualizowana!</div>
<?php elseif ($success === 'deleted'): ?>
    <div class="alert alert-success">Platforma została usunięta!</div>
<?php endif; ?>

<div class="actions-bar">
    <a href="<?= $router->generatePath('admin-platforms-create') ?>" class="btn btn-primary">+ Dodaj platformę</a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Logo</th>
            <th>Nazwa</th>
            <th>URL</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($platforms)): ?>
            <tr>
                <td colspan="5" class="empty-row">Brak platform</td>
            </tr>
        <?php else: ?>
            <?php foreach ($platforms as $platform): ?>
                <tr>
                    <td><?= $platform['id'] ?></td>
                    <td><img src="<?= htmlspecialchars($platform['logo_url']) ?>" alt="logo" class="table-logo"></td>
                    <td><?= htmlspecialchars($platform['nazwa']) ?></td>
                    <td><a href="<?= htmlspecialchars($platform['platform_url']) ?>" target="_blank">Link</a></td>
                    <td class="actions">
                        <a href="<?= $router->generatePath('admin-platforms-edit', ['id' => $platform['id']]) ?>" class="btn btn-small">Edytuj</a>
                        <a href="<?= $router->generatePath('admin-platforms-delete', ['id' => $platform['id']]) ?>"
                           class="btn btn-small btn-danger"
                           onclick="return confirm('Na pewno usunąć tę platformę?')">Usuń</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
$page_title = 'Platformy streamingowe';
$title = 'Platformy';
$admin_login = $_SESSION['admin_login'] ?? 'Admin';

require __DIR__ . '/../layout.html.php';
?>
