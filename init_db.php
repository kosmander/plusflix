<?php

// Skrypt inicjalizujacy baze danych

echo "=== Inicjalizacja bazy danych PLUSFLIX ===\n\n";

$dbFile = __DIR__ . '/data.db';

// usun stara baze jesli istnieje
if (file_exists($dbFile)) {
    echo "Usuwam stara baze danych...\n";
    unlink($dbFile);
}

// utworz polaczenie (automatycznie tworzy plik)
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "Utworzono nowa baze: $dbFile\n\n";

// wczytaj i wykonaj pliki SQL w kolejnosci
$sqlFiles = [
    'sql/01-platforms.sql',
    'sql/02-categories.sql',
    'sql/03-tags.sql',
    'sql/04-productions.sql',
    'sql/05-ratings.sql',
    'sql/06-production_platform.sql',
    'sql/07-production_category.sql',
    'sql/08-production_tag.sql',
    'sql/09-seed.sql',
    'sql/10-administrators.sql',
];

foreach ($sqlFiles as $file) {
    $path = __DIR__ . '/' . $file;
    if (file_exists($path)) {
        echo "Wykonuje: $file\n";
        $sql = file_get_contents($path);
        $pdo->exec($sql);
    } else {
        echo "UWAGA: Brak pliku $file\n";
    }
}

// dodaj admina z prawdziwym hashem hasla
echo "\nTworzenie konta admina...\n";

// usun placeholdera i dodaj prawdziwego admina
$pdo->exec("DELETE FROM administrators");

$haslo = password_hash('admin123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("INSERT INTO administrators (login, password, email) VALUES (?, ?, ?)");
$stmt->execute(['admin', $haslo, 'admin@plusflix.pl']);

echo "Admin utworzony!\n";
echo "  Login: admin\n";
echo "  Haslo: admin123\n";

echo "\nBaza danych gotowa.\n";
