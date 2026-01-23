<?php
$title = 'Wyszukiwarka - PLUSFLIX';
$f = $filters;
?>

<?php ob_start(); ?>

<div class="search-page">
    <div class="search-header">
        <h1>PLUSFLIX</h1>
        <p>Znajdź swój ulubiony film lub serial</p>
    </div>

    <div class="search-container">
        <div class="search-filters">
            <form method="GET" action="">
                <input type="hidden" name="action" value="search">

                <div class="search-box">
                    <input type="text" name="q" id="search-input" value="<?= htmlspecialchars($f['q']) ?>" placeholder="Szukaj filmu lub serialu...">
                    <div id="suggestions"></div>
                </div>

                <div class="filters">
                    <div class="filter-group">
                        <label>Typ:</label>
                        <select name="typ">
                            <option value="">Wszystkie</option>
                            <option value="film" <?= $f['typ'] === 'film' ? 'selected' : '' ?>>Film</option>
                            <option value="serial" <?= $f['typ'] === 'serial' ? 'selected' : '' ?>>Serial</option>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Kategoria:</label>
                        <select name="kategoria">
                            <option value="">Wszystkie</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat->getId() ?>" <?= $f['kategoria'] == $cat->getId() ? 'selected' : '' ?>><?= htmlspecialchars($cat->getNazwa()) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Rok:</label>
                        <select name="rok">
                            <option value="">Wszystkie</option>
                            <?php foreach ($lata as $r): ?>
                                <option value="<?= $r ?>" <?= $f['rok'] == $r ? 'selected' : '' ?>><?= $r ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Kraj:</label>
                        <select name="kraj">
                            <option value="">Wszystkie</option>
                            <?php foreach ($kraje as $k): ?>
                                <option value="<?= htmlspecialchars($k) ?>" <?= $f['kraj'] === $k ? 'selected' : '' ?>><?= htmlspecialchars($k) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label>Sortowanie:</label>
                        <select name="sort">
                            <option value="tytul_asc" <?= $f['sort'] === 'tytul_asc' ? 'selected' : '' ?>>Tytuł A-Z</option>
                            <option value="tytul_desc" <?= $f['sort'] === 'tytul_desc' ? 'selected' : '' ?>>Tytuł Z-A</option>
                            <option value="rok_desc" <?= $f['sort'] === 'rok_desc' ? 'selected' : '' ?>>Rok malejąco</option>
                            <option value="rok_asc" <?= $f['sort'] === 'rok_asc' ? 'selected' : '' ?>>Rok rosnąco</option>
                        </select>
                    </div>
                </div>

                <div class="filter-buttons">
                    <button type="submit" class="btn btn-primary">Zastosuj filtry</button>
                    <a href="?action=search" class="btn btn-secondary">Wyczyść filtry</a>
                    <a href="?action=search-random" class="btn btn-random">Losowy film</a>
                </div>
            </form>
        </div>

        <div class="search-results">
            <h2>Wyniki (<?= count($productions) ?>)</h2>

            <?php if (count($productions) === 0): ?>
                <p class="no-results">Nie znaleziono żadnych produkcji.</p>
            <?php else: ?>
                <div class="productions-grid">
                    <?php foreach ($productions as $prod): ?>
                        <div class="production-card">
                            <a href="?action=production-show&id=<?= $prod->getId() ?>">
                                <?php if ($prod->getPlakatUrl()): ?>
                                    <img src="<?= htmlspecialchars($prod->getPlakatUrl()) ?>" alt="<?= htmlspecialchars($prod->getTytul()) ?>">
                                <?php else: ?>
                                    <div class="no-poster">Brak plakatu</div>
                                <?php endif; ?>
                                <div class="production-info">
                                    <h3><?= htmlspecialchars($prod->getTytul()) ?></h3>
                                    <p><?= $prod->getRok() ?> | <?= $prod->getTyp() ?></p>
                                    <?php if ($prod->getKraj()): ?>
                                        <p class="country"><?= htmlspecialchars($prod->getKraj()) ?></p>
                                    <?php endif; ?>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
// autouzupelnianie
var searchInput = document.getElementById('search-input');
var suggestionsBox = document.getElementById('suggestions');

searchInput.addEventListener('input', function() {
    var query = this.value;

    if (query.length < 4) {
        suggestionsBox.innerHTML = '';
        suggestionsBox.style.display = 'none';
        return;
    }

    fetch('?action=search-suggest&q=' + encodeURIComponent(query))
        .then(function(response) {
            return response.json();
        })
        .then(function(data) {
            if (data.length === 0) {
                suggestionsBox.innerHTML = '';
                suggestionsBox.style.display = 'none';
                return;
            }

            var html = '';
            for (var i = 0; i < data.length; i++) {
                html += '<div class="suggestion" data-id="' + data[i].id + '">';
                html += data[i].tytul + ' (' + data[i].rok + ')';
                html += '</div>';
            }
            suggestionsBox.innerHTML = html;
            suggestionsBox.style.display = 'block';

            // klikniecie w sugestie
            var suggestions = suggestionsBox.querySelectorAll('.suggestion');
            for (var j = 0; j < suggestions.length; j++) {
                suggestions[j].addEventListener('click', function() {
                    window.location.href = '?action=production-show&id=' + this.dataset.id;
                });
            }
        });
});

// zamknij sugestie po kliknieciu poza
document.addEventListener('click', function(e) {
    if (e.target !== searchInput) {
        suggestionsBox.style.display = 'none';
    }
});
</script>

<?php $main = ob_get_clean(); ?>

<?php require(__DIR__ . '/../base.html.php'); ?>
