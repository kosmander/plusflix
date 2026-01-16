<?php
/** @var array $categories */
/** @var \App\Service\Router $router */
/** @var string|null $success */

ob_start();
?>

<?php if ($success === 'created'): ?>
    <div class="alert alert-success">Kategoria została dodana!</div>
<?php elseif ($success === 'updated'): ?>
    <div class="alert alert-success">Kategoria została zaktualizowana!</div>
<?php elseif ($success === 'deleted'): ?>
    <div class="alert alert-success">Kategoria została usunięta!</div>
<?php elseif ($success === 'cannot_delete'): ?>
    <div class="alert alert-error">Nie można usunąć kategorii - jest przypisana do produkcji!</div>
<?php endif; ?>

<div class="actions-bar">
    <a href="<?= $router->generatePath('admin-categories-create') ?>" class="btn btn-primary">+ Dodaj kategorię</a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nazwa</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($categories)): ?>
            <tr>
                <td colspan="3" class="empty-row">Brak kategorii</td>
            </tr>
        <?php else: ?>
            <?php foreach ($categories as $cat): ?>
                <tr>
                    <td><?= $cat['id'] ?></td>
                    <td><?= htmlspecialchars($cat['nazwa']) ?></td>
                    <td class="actions">
                        <a href="<?= $router->generatePath('admin-categories-edit', ['id' => $cat['id']]) ?>" class="btn btn-small">Edytuj</a>
                        <a href="<?= $router->generatePath('admin-categories-delete', ['id' => $cat['id']]) ?>"
                           class="btn btn-small btn-danger"
                           onclick="return confirm('Na pewno usunąć tę kategorię?')">Usuń</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
$page_title = 'Kategorie';
$title = 'Kategorie';
$admin_login = $_SESSION['admin_login'] ?? 'Admin';

require __DIR__ . '/../layout.html.php';
?>
