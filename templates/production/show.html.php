<?php
$title = htmlspecialchars($production->getTytul()) . ' - PLUSFLIX';
$msg = $_GET['msg'] ?? null;
?>

<?php ob_start(); ?>

<div class="production-page">
    <a href="?action=search" class="back-link">&larr; Powrót do wyszukiwarki</a>

    <?php if ($msg === 'rating_added'): ?>
        <div class="alert alert-success">Dziękujemy za ocenę! Zostanie opublikowana po moderacji.</div>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="production-details">
        <div class="production-poster">
            <?php if ($production->getPlakatUrl()): ?>
                <img src="<?= htmlspecialchars($production->getPlakatUrl()) ?>" alt="<?= htmlspecialchars($production->getTytul()) ?>">
            <?php else: ?>
                <div class="no-poster">Brak plakatu</div>
            <?php endif; ?>
        </div>

        <div class="production-info-full">
            <h1><?= htmlspecialchars($production->getTytul()) ?></h1>

            <div class="production-meta">
                <span class="meta-item"><?= $production->getRok() ?></span>
                <span class="meta-item"><?= $production->getTyp() === 'film' ? 'Film' : 'Serial' ?></span>
                <?php if ($production->getKraj()): ?>
                    <span class="meta-item"><?= htmlspecialchars($production->getKraj()) ?></span>
                <?php endif; ?>
                <?php if ($avgRating > 0): ?>
                    <span class="meta-item rating-badge">&#9733; <?= $avgRating ?>/5</span>
                <?php endif; ?>
            </div>

            <?php if (count($categories) > 0): ?>
                <div class="production-categories">
                    <?php foreach ($categories as $cat): ?>
                        <span class="category-tag"><?= htmlspecialchars($cat->getNazwa()) ?></span>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="production-description">
                <h3>Opis</h3>
                <p><?= nl2br(htmlspecialchars($production->getOpis())) ?></p>
            </div>

            <?php if (count($platformsWithSeasons) > 0): ?>
                <div class="production-platforms">
                    <h3>Dostępne na platformach</h3>
                    <div class="platforms-list">
                        <?php foreach ($platformsWithSeasons as $item): ?>
                            <?php $plat = $item['platform']; $seasons = $item['seasons']; ?>
                            <a href="<?= htmlspecialchars($plat->getPlatformUrl() ?? '#') ?>" target="_blank" class="platform-link">
                                <?= htmlspecialchars($plat->getNazwa()) ?>
                                <?php if (count($seasons) > 0): ?>
                                    <span class="seasons-info">
                                        (<?= count($seasons) == 1 ? 'Sezon ' . $seasons[0] : 'Sezony ' . implode(', ', $seasons) ?>)
                                    </span>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="production-platforms">
                    <h3>Dostępne na platformach</h3>
                    <p class="no-platforms">Nie znaleziono na żadnej platformie</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="production-ratings">
        <h2>Oceny i recenzje (<?= count($ratings) ?>)</h2>

        <div class="add-rating-form">
            <h3>Dodaj swoją ocenę</h3>
            <form method="POST" action="?action=production-add-rating&id=<?= $production->getId() ?>">
                <div class="form-group">
                    <label>Ocena:</label>
                    <div class="stars-input">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label class="star-label">
                                <input type="radio" name="form[ocena]" value="<?= $i ?>" required>
                                <span class="star">&#9733;</span>
                                <span class="star-number"><?= $i ?></span>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Recenzja (opcjonalnie):</label>
                    <textarea name="form[tresc]" rows="4" placeholder="Napisz co myślisz o tej produkcji..."></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Wyślij ocenę</button>
            </form>
        </div>

        <?php if (count($ratings) > 0): ?>
            <div class="ratings-list">
                <?php foreach ($ratings as $rating): ?>
                    <div class="rating-item">
                        <div class="rating-header">
                            <span class="rating-stars">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <span class="star <?= $i <= $rating->getOcena() ? 'filled' : '' ?>">&#9733;</span>
                                <?php endfor; ?>
                            </span>
                            <span class="rating-date"><?= $rating->getData() ?></span>
                        </div>
                        <?php if ($rating->getTresc()): ?>
                            <div class="rating-content">
                                <?= nl2br(htmlspecialchars($rating->getTresc())) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="no-ratings">Brak ocen. Bądź pierwszy!</p>
        <?php endif; ?>
    </div>
</div>

<?php $main = ob_get_clean(); ?>

<?php require(__DIR__ . '/../base.html.php'); ?>
