<?php
/** @var \App\Service\Router $router */
/** @var string|null $error */

ob_start();
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= $router->generatePath('admin-categories-create') ?>" class="admin-form">
    <div class="form-group">
        <label for="nazwa">Nazwa kategorii: *</label>
        <input type="text" id="nazwa" name="form[nazwa]" required
               value="<?= htmlspecialchars($_POST['form']['nazwa'] ?? '') ?>">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Zapisz</button>
        <a href="<?= $router->generatePath('admin-categories') ?>" class="btn btn-secondary">Anuluj</a>
    </div>
</form>

<?php
$content = ob_get_clean();
$page_title = 'Dodaj kategorię';
$title = 'Dodaj kategorię';
$admin_login = $_SESSION['admin_login'] ?? 'Admin';

require __DIR__ . '/../layout.html.php';
?>
