<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logowanie - PLUSFLIX Admin</title>
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-box">
            <h1>PLUSFLIX</h1>
            <h2>Panel Administratora</h2>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= $router->generatePath('admin-login') ?>">
                <div class="form-group">
                    <label for="login">Login:</label>
                    <input type="text" id="login" name="form[login]" required>
                </div>

                <div class="form-group">
                    <label for="password">Hasło:</label>
                    <input type="password" id="password" name="form[password]" required>
                </div>

                <button type="submit" class="btn btn-primary">Zaloguj się</button>
            </form>
        </div>

        <p class="disclaimer">PLUSFLIX - Aplikacja nie gwarantuje poprawności wszystkich danych.</p>
    </div>
</body>
</html>
