<?php
/** @var array $productions */
/** @var \App\Service\Router $router */
/** @var string|null $success */

ob_start();
?>

<?php if ($success === 'created'): ?>
    <div class="alert alert-success">Produkcja została dodana!</div>
<?php elseif ($success === 'updated'): ?>
    <div class="alert alert-success">Produkcja została zaktualizowana!</div>
<?php elseif ($success === 'deleted'): ?>
    <div class="alert alert-success">Produkcja została usunięta!</div>
<?php endif; ?>

<div class="actions-bar">
    <a href="<?= $router->generatePath('admin-productions-create') ?>" class="btn btn-primary">+ Dodaj produkcję</a>
</div>

<table class="data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Plakat</th>
            <th>Tytuł</th>
            <th>Typ</th>
            <th>Rok</th>
            <th>Kraj</th>
            <th>Akcje</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($productions)): ?>
            <tr>
                <td colspan="7" class="empty-row">Brak produkcji</td>
            </tr>
        <?php else: ?>
            <?php foreach ($productions as $prod): ?>
                <tr>
                    <td><?= $prod['id'] ?></td>
                    <td><img src="<?= htmlspecialchars($prod['plakat_url']) ?>" alt="plakat" class="table-poster"></td>
                    <td><?= htmlspecialchars($prod['tytul']) ?></td>
                    <td><?= $prod['typ'] === 'film' ? 'Film' : 'Serial' ?></td>
                    <td><?= $prod['rok'] ?></td>
                    <td><?= htmlspecialchars($prod['kraj'] ?? '-') ?></td>
                    <td class="actions">
                        <a href="<?= $router->generatePath('admin-productions-edit', ['id' => $prod['id']]) ?>" class="btn btn-small">Edytuj</a>
                        <a href="<?= $router->generatePath('admin-productions-delete', ['id' => $prod['id']]) ?>"
                           class="btn btn-small btn-danger"
                           onclick="return confirm('Na pewno usunąć tę produkcję?')">Usuń</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php
$content = ob_get_clean();
$page_title = 'Produkcje';
$title = 'Produkcje';
$admin_login = $_SESSION['admin_login'] ?? 'Admin';

require __DIR__ . '/../layout.html.php';
?>
