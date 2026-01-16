<?php
/** @var array $categories */
/** @var array $platforms */
/** @var \App\Service\Router $router */
/** @var string|null $error */

ob_start();
?>

<?php if (!empty($error)): ?>
    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<form method="POST" action="<?= $router->generatePath('admin-productions-create') ?>" class="admin-form">
    <div class="form-group">
        <label for="tytul">Tytuł: *</label>
        <input type="text" id="tytul" name="form[tytul]" required
               value="<?= htmlspecialchars($_POST['form']['tytul'] ?? '') ?>">
    </div>

    <div class="form-row">
        <div class="form-group">
            <label for="typ">Typ: *</label>
            <select id="typ" name="form[typ]" required>
                <option value="">-- wybierz --</option>
                <option value="film" <?= ($_POST['form']['typ'] ?? '') === 'film' ? 'selected' : '' ?>>Film</option>
                <option value="serial" <?= ($_POST['form']['typ'] ?? '') === 'serial' ? 'selected' : '' ?>>Serial</option>
            </select>
        </div>

        <div class="form-group">
            <label for="rok">Rok produkcji: *</label>
            <input type="number" id="rok" name="form[rok]" required min="1900" max="2030"
                   value="<?= htmlspecialchars($_POST['form']['rok'] ?? '') ?>">
        </div>
    </div>

    <div class="form-group">
        <label for="kraj">Kraj produkcji:</label>
        <select id="kraj" name="form[kraj]">
            <option value="">-- wybierz kraj --</option>
            <?php
            $kraje = ['USA', 'Polska', 'Wielka Brytania', 'Niemcy', 'Francja', 'Hiszpania', 'Włochy', 'Japonia', 'Korea Południowa', 'Indie', 'Kanada', 'Australia', 'Inne'];
            foreach ($kraje as $k):
            ?>
                <option value="<?= $k ?>" <?= ($_POST['form']['kraj'] ?? '') === $k ? 'selected' : '' ?>><?= $k ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="opis">Opis: *</label>
        <textarea id="opis" name="form[opis]" rows="4" required><?= htmlspecialchars($_POST['form']['opis'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="plakat_url">URL plakatu: *</label>
        <input type="url" id="plakat_url" name="form[plakat_url]" required
               value="<?= htmlspecialchars($_POST['form']['plakat_url'] ?? '') ?>"
               placeholder="https://example.com/plakat.jpg">
    </div>

    <div class="form-group">
        <label>Kategorie:</label>
        <div class="checkbox-group">
            <?php foreach ($categories as $cat): ?>
                <label class="checkbox-label">
                    <input type="checkbox" name="form[categories][]" value="<?= $cat['id'] ?>"
                        <?= in_array($cat['id'], $_POST['form']['categories'] ?? []) ? 'checked' : '' ?>>
                    <?= htmlspecialchars($cat['nazwa']) ?>
                </label>
            <?php endforeach; ?>
            <?php if (empty($categories)): ?>
                <span class="info-text">Brak kategorii - najpierw dodaj kategorie</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <label>Platformy i dostępne sezony:</label>
        <p class="info-text">Zaznacz platformy i wpisz dostępne sezony (np. "1-3", "wszystkie" lub zostaw puste dla filmów)</p>
        <div class="platforms-list">
            <?php foreach ($platforms as $plat): ?>
                <div class="platform-item">
                    <label class="checkbox-label">
                        <input type="checkbox" name="form[platforms][<?= $plat['id'] ?>][selected]" value="1"
                            <?= isset($_POST['form']['platforms'][$plat['id']]['selected']) ? 'checked' : '' ?>>
                        <?= htmlspecialchars($plat['nazwa']) ?>
                    </label>
                    <input type="text" name="form[platforms][<?= $plat['id'] ?>][sezon]"
                           placeholder="np. 1-3"
                           value="<?= htmlspecialchars($_POST['form']['platforms'][$plat['id']]['sezon'] ?? '') ?>"
                           class="sezon-input">
                </div>
            <?php endforeach; ?>
            <?php if (empty($platforms)): ?>
                <span class="info-text">Brak platform - najpierw dodaj platformy</span>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-actions">
        <button type="submit" class="btn btn-primary">Zapisz</button>
        <a href="<?= $router->generatePath('admin-productions') ?>" class="btn btn-secondary">Anuluj</a>
    </div>
</form>

<?php
$content = ob_get_clean();
$page_title = 'Dodaj produkcję';
$title = 'Dodaj produkcję';
$admin_login = $_SESSION['admin_login'] ?? 'Admin';

require __DIR__ . '/../layout.html.php';
?>
