<?php
/** @var array $platform */
/** @var \App\Service\Router $router */
/** @var string|null $error */

ob_start();
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= $router->generatePath('admin-platforms-edit', ['id' => $platform['id']]) ?>" class="admin-form">
    <div class="form-group">
        <label for="nazwa">Nazwa platformy: *</label>
        <input type="text" id="nazwa" name="form[nazwa]" required
               value="<?= htmlspecialchars($_POST['form']['nazwa'] ?? $platform['nazwa']) ?>">
    </div>

    <div class="form-group">
        <label for="logo_url">URL logo: *</label>
        <input type="url" id="logo_url" name="form[logo_url]" required
               value="<?= htmlspecialchars($_POST['form']['logo_url'] ?? $platform['logo_url']) ?>">
    </div>

    <div class="form-group">
        <label for="platform_url">URL platformy: *</label>
        <input type="url" id="platform_url" name="form[platform_url]" required
               value="<?= htmlspecialchars($_POST['form']['platform_url'] ?? $platform['platform_url']) ?>">
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Zapisz zmiany</button>
        <a href="<?= $router->generatePath('admin-platforms') ?>" class="btn btn-secondary">Anuluj</a>
    </div>
</form>

<?php
$content = ob_get_clean();
$page_title = 'Edytuj platformę';
$title = 'Edytuj platformę';
$admin_login = $_SESSION['admin_login'] ?? 'Admin';

require __DIR__ . '/../layout.html.php';
?>
