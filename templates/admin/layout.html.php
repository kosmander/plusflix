<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Panel Admina' ?> - PLUSFLIX</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <!-- sidebar z menu -->
        <aside class="sidebar">
            <div class="logo">
                <h1>PLUSFLIX</h1>
                <span>Panel Admina</span>
            </div>

            <nav class="admin-nav">
                <ul>
                    <li><a href="<?= $router->generatePath('admin-dashboard') ?>">Dashboard</a></li>
                    <li><a href="<?= $router->generatePath('admin-productions') ?>">Produkcje</a></li>
                    <li><a href="<?= $router->generatePath('admin-platforms') ?>">Platformy</a></li>
                    <li><a href="<?= $router->generatePath('admin-categories') ?>">Kategorie</a></li>
                </ul>
            </nav>

            <div class="admin-user">
                <span>Zalogowany: <?= htmlspecialchars($admin_login ?? 'Admin') ?></span>
                <a href="<?= $router->generatePath('admin-logout') ?>" class="logout-btn">Wyloguj</a>
            </div>
        </aside>

        <!-- glowna czesc -->
        <main class="main-content">
            <header class="content-header">
                <h2><?= $page_title ?? 'Dashboard' ?></h2>
            </header>

            <div class="content-body">
                <?= $content ?? '' ?>
            </div>

            <!-- komunikat o braku gwarancji -->
            <footer class="admin-footer">
                <p>PLUSFLIX - Aplikacja nie gwarantuje poprawności wszystkich danych.</p>
            </footer>
        </main>
    </div>
</body>
</html>
