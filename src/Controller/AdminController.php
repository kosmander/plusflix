<?php
namespace App\Controller;

use App\Model\Administrator;
use App\Service\Config;
use App\Service\Templating;
use App\Service\Router;
use PDO;

class AdminController
{
    private Templating $templating;
    private Router $router;

    public function __construct(Templating $templating, Router $router)
    {
        $this->templating = $templating;
        $this->router = $router;
    }

    // polaczenie z baza (do CRUDow)
    private function getDb(): PDO
    {
        $dsn = Config::get('db_dsn');
        $pdo = new PDO($dsn, '', '');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    }

    // sprawdzanie czy admin jest zalogowany
    private function checkAuth(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['admin_id'])) {
            return false;
        }

        // sprawdzanie timeout sesji (ADM05)
        $timeout = Config::get('session_timeout');
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout) {
            // sesja wygasla - wyloguj
            session_unset();
            session_destroy();
            return false;
        }

        // odswiezenie czasu aktywnosci
        $_SESSION['last_activity'] = time();
        return true;
    }

    // wymuszenie zalogowania - przekierowanie do loginu
    private function requireAuth(): void
    {
        if (!$this->checkAuth()) {
            $this->router->redirect($this->router->generatePath('admin-login'));
            exit;
        }
    }

    // logowanie

    public function loginAction(?array $formData): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $error = null;

        // jesli juz zalogowany to przekieruj do dashboardu
        if (isset($_SESSION['admin_id'])) {
            $this->router->redirect($this->router->generatePath('admin-dashboard'));
            exit;
        }

        // obsluga formularza logowania
        if ($formData !== null) {
            $login = $formData['login'] ?? '';
            $password = $formData['password'] ?? '';

            if (empty($login) || empty($password)) {
                $error = 'Wypełnij wszystkie pola!';
            } else {
                $admin = Administrator::findByLogin($login);

                if ($admin && $admin->verifyPassword($password)) {
                    // logowanie udane
                    $_SESSION['admin_id'] = $admin->id;
                    $_SESSION['admin_login'] = $admin->login;
                    $_SESSION['last_activity'] = time();

                    $this->router->redirect($this->router->generatePath('admin-dashboard'));
                    exit;
                } else {
                    $error = 'Nieprawidłowy login lub hasło!';
                }
            }
        }

        return $this->templating->render('admin/login.html.php', [
            'error' => $error,
            'router' => $this->router
        ]);
    }

    public function logoutAction(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();

        $this->router->redirect($this->router->generatePath('admin-login'));
        exit;
    }

    // dashboard

    public function dashboardAction(): string
    {
        $this->requireAuth();

        // pobierz statystyki do dashboardu
        $pdo = $this->getDb();

        // zliczanie rekordow
        $stats = [];
        $stats['productions'] = $pdo->query("SELECT COUNT(*) FROM productions")->fetchColumn();
        $stats['platforms'] = $pdo->query("SELECT COUNT(*) FROM platforms")->fetchColumn();
        $stats['categories'] = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        $stats['ratings_pending'] = $pdo->query("SELECT COUNT(*) FROM ratings WHERE status_moderacji = 'oczekujące'")->fetchColumn();

        return $this->templating->render('admin/dashboard.html.php', [
            'stats' => $stats,
            'router' => $this->router,
            'admin_login' => $_SESSION['admin_login'] ?? 'Admin'
        ]);
    }

    // CRUD kategorie

    public function categoriesIndexAction(): string
    {
        $this->requireAuth();

        $pdo = $this->getDb();
        $stmt = $pdo->query("SELECT * FROM categories ORDER BY nazwa");
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // komunikat sukcesu
        $success = $_GET['success'] ?? null;

        return $this->templating->render('admin/categories/index.html.php', [
            'categories' => $categories,
            'router' => $this->router,
            'success' => $success
        ]);
    }

    public function categoriesCreateAction(?array $formData): string
    {
        $this->requireAuth();

        $error = null;

        if ($formData !== null) {
            $nazwa = trim($formData['nazwa'] ?? '');

            if (empty($nazwa)) {
                $error = 'Nazwa kategorii jest wymagana!';
            } else {
                $pdo = $this->getDb();

                // sprawdz czy nie ma duplikatu
                $check = $pdo->prepare("SELECT id FROM categories WHERE nazwa = :nazwa");
                $check->execute(['nazwa' => $nazwa]);

                if ($check->fetch()) {
                    $error = 'Kategoria o takiej nazwie już istnieje!';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO categories (nazwa) VALUES (:nazwa)");
                    $stmt->execute(['nazwa' => $nazwa]);

                    $this->router->redirect($this->router->generatePath('admin-categories') . '&success=created');
                    exit;
                }
            }
        }

        return $this->templating->render('admin/categories/create.html.php', [
            'error' => $error,
            'router' => $this->router
        ]);
    }

    public function categoriesEditAction(int $id, ?array $formData): string
    {
        $this->requireAuth();

        $pdo = $this->getDb();
        $error = null;

        // pobierz kategorie
        $stmt = $pdo->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$category) {
            $this->router->redirect($this->router->generatePath('admin-categories'));
            exit;
        }

        if ($formData !== null) {
            $nazwa = trim($formData['nazwa'] ?? '');

            if (empty($nazwa)) {
                $error = 'Nazwa kategorii jest wymagana!';
            } else {
                // sprawdz duplikat (ale nie sam siebie)
                $check = $pdo->prepare("SELECT id FROM categories WHERE nazwa = :nazwa AND id != :id");
                $check->execute(['nazwa' => $nazwa, 'id' => $id]);

                if ($check->fetch()) {
                    $error = 'Kategoria o takiej nazwie już istnieje!';
                } else {
                    $stmt = $pdo->prepare("UPDATE categories SET nazwa = :nazwa WHERE id = :id");
                    $stmt->execute(['nazwa' => $nazwa, 'id' => $id]);

                    $this->router->redirect($this->router->generatePath('admin-categories') . '&success=updated');
                    exit;
                }
            }
        }

        return $this->templating->render('admin/categories/edit.html.php', [
            'category' => $category,
            'error' => $error,
            'router' => $this->router
        ]);
    }

    public function categoriesDeleteAction(int $id): void
    {
        $this->requireAuth();

        $pdo = $this->getDb();

        // sprawdz czy kategoria nie jest przypisana do produkcji (ADM10)
        $check = $pdo->prepare("SELECT COUNT(*) FROM production_category WHERE id_kategorii = :id");
        $check->execute(['id' => $id]);

        if ($check->fetchColumn() > 0) {
            // nie mozna usunac - jest uzywana
            $this->router->redirect($this->router->generatePath('admin-categories') . '&success=cannot_delete');
            exit;
        }

        $stmt = $pdo->prepare("DELETE FROM categories WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $this->router->redirect($this->router->generatePath('admin-categories') . '&success=deleted');
        exit;
    }

    // CRUD platformy

    public function platformsIndexAction(): string
    {
        $this->requireAuth();

        $pdo = $this->getDb();
        $stmt = $pdo->query("SELECT * FROM platforms ORDER BY nazwa");
        $platforms = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $success = $_GET['success'] ?? null;

        return $this->templating->render('admin/platforms/index.html.php', [
            'platforms' => $platforms,
            'router' => $this->router,
            'success' => $success
        ]);
    }

    public function platformsCreateAction(?array $formData): string
    {
        $this->requireAuth();

        $error = null;

        if ($formData !== null) {
            $nazwa = trim($formData['nazwa'] ?? '');
            $logo_url = trim($formData['logo_url'] ?? '');
            $platform_url = trim($formData['platform_url'] ?? '');

            if (empty($nazwa) || empty($logo_url) || empty($platform_url)) {
                $error = 'Wszystkie pola są wymagane!';
            } else {
                $pdo = $this->getDb();

                // sprawdz duplikat nazwy (ADM12)
                $check = $pdo->prepare("SELECT id FROM platforms WHERE nazwa = :nazwa");
                $check->execute(['nazwa' => $nazwa]);

                if ($check->fetch()) {
                    $error = 'Platforma o takiej nazwie już istnieje!';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO platforms (nazwa, logo_url, platform_url) VALUES (:nazwa, :logo_url, :platform_url)");
                    $stmt->execute([
                        'nazwa' => $nazwa,
                        'logo_url' => $logo_url,
                        'platform_url' => $platform_url
                    ]);

                    $this->router->redirect($this->router->generatePath('admin-platforms') . '&success=created');
                    exit;
                }
            }
        }

        return $this->templating->render('admin/platforms/create.html.php', [
            'error' => $error,
            'router' => $this->router
        ]);
    }

    public function platformsEditAction(int $id, ?array $formData): string
    {
        $this->requireAuth();

        $pdo = $this->getDb();
        $error = null;

        $stmt = $pdo->prepare("SELECT * FROM platforms WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $platform = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$platform) {
            $this->router->redirect($this->router->generatePath('admin-platforms'));
            exit;
        }

        if ($formData !== null) {
            $nazwa = trim($formData['nazwa'] ?? '');
            $logo_url = trim($formData['logo_url'] ?? '');
            $platform_url = trim($formData['platform_url'] ?? '');

            if (empty($nazwa) || empty($logo_url) || empty($platform_url)) {
                $error = 'Wszystkie pola są wymagane!';
            } else {
                // sprawdz duplikat (nie sam siebie)
                $check = $pdo->prepare("SELECT id FROM platforms WHERE nazwa = :nazwa AND id != :id");
                $check->execute(['nazwa' => $nazwa, 'id' => $id]);

                if ($check->fetch()) {
                    $error = 'Platforma o takiej nazwie już istnieje!';
                } else {
                    $stmt = $pdo->prepare("UPDATE platforms SET nazwa = :nazwa, logo_url = :logo_url, platform_url = :platform_url WHERE id = :id");
                    $stmt->execute([
                        'nazwa' => $nazwa,
                        'logo_url' => $logo_url,
                        'platform_url' => $platform_url,
                        'id' => $id
                    ]);

                    $this->router->redirect($this->router->generatePath('admin-platforms') . '&success=updated');
                    exit;
                }
            }
        }

        return $this->templating->render('admin/platforms/edit.html.php', [
            'platform' => $platform,
            'error' => $error,
            'router' => $this->router
        ]);
    }

    public function platformsDeleteAction(int $id): void
    {
        $this->requireAuth();

        $pdo = $this->getDb();

        // usuniecie powiazanych rekordow
        $pdo->prepare("DELETE FROM production_platform WHERE id_platformy = :id")->execute(['id' => $id]);

        $stmt = $pdo->prepare("DELETE FROM platforms WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $this->router->redirect($this->router->generatePath('admin-platforms') . '&success=deleted');
        exit;
    }

    // CRUD produkcje

    public function productionsIndexAction(): string
    {
        $this->requireAuth();

        $pdo = $this->getDb();
        $stmt = $pdo->query("SELECT * FROM productions ORDER BY tytul");
        $productions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $success = $_GET['success'] ?? null;

        return $this->templating->render('admin/productions/index.html.php', [
            'productions' => $productions,
            'router' => $this->router,
            'success' => $success
        ]);
    }

    public function productionsCreateAction(?array $formData): string
    {
        $this->requireAuth();

        $pdo = $this->getDb();
        $error = null;

        // pobierz kategorie i platformy do formularza
        $categories = $pdo->query("SELECT * FROM categories ORDER BY nazwa")->fetchAll(PDO::FETCH_ASSOC);
        $platforms = $pdo->query("SELECT * FROM platforms ORDER BY nazwa")->fetchAll(PDO::FETCH_ASSOC);

        if ($formData !== null) {
            $tytul = trim($formData['tytul'] ?? '');
            $opis = trim($formData['opis'] ?? '');
            $typ = $formData['typ'] ?? '';
            $rok = intval($formData['rok'] ?? 0);
            $kraj = trim($formData['kraj'] ?? '');
            $plakat_url = trim($formData['plakat_url'] ?? '');
            $selected_categories = $formData['categories'] ?? [];
            $platforms_data = $formData['platforms'] ?? [];

            // walidacja (ADM08)
            if (empty($tytul) || empty($opis) || empty($typ) || $rok < 1900 || empty($plakat_url)) {
                $error = 'Wypełnij poprawnie wszystkie wymagane pola!';
            } elseif (!in_array($typ, ['film', 'serial'])) {
                $error = 'Nieprawidłowy typ produkcji!';
            } else {
                // sprawdz duplikat tytul+rok (ADM09)
                $check = $pdo->prepare("SELECT id FROM productions WHERE tytul = :tytul AND rok = :rok");
                $check->execute(['tytul' => $tytul, 'rok' => $rok]);

                if ($check->fetch()) {
                    $error = 'Produkcja o tym tytule i roku już istnieje!';
                } else {
                    // dodaj produkcje
                    $stmt = $pdo->prepare("INSERT INTO productions (tytul, opis, typ, rok, kraj, plakat_url) VALUES (:tytul, :opis, :typ, :rok, :kraj, :plakat_url)");
                    $stmt->execute([
                        'tytul' => $tytul,
                        'opis' => $opis,
                        'typ' => $typ,
                        'rok' => $rok,
                        'kraj' => $kraj ?: null,
                        'plakat_url' => $plakat_url
                    ]);

                    $production_id = $pdo->lastInsertId();

                    // dodaj powiazania z kategoriami (ADM06)
                    foreach ($selected_categories as $cat_id) {
                        $pdo->prepare("INSERT INTO production_category (id_produkcji, id_kategorii) VALUES (:prod, :cat)")
                            ->execute(['prod' => $production_id, 'cat' => $cat_id]);
                    }

                    // dodaj powiazania z platformami i sezonami (ADM07)
                    foreach ($platforms_data as $plat_id => $plat_info) {
                        if (isset($plat_info['selected'])) {
                            $sezon = trim($plat_info['sezon'] ?? '');
                            $pdo->prepare("INSERT INTO production_platform (id_produkcji, id_platformy, dostepny_sezon) VALUES (:prod, :plat, :sezon)")
                                ->execute(['prod' => $production_id, 'plat' => $plat_id, 'sezon' => $sezon ?: null]);
                        }
                    }

                    $this->router->redirect($this->router->generatePath('admin-productions') . '&success=created');
                    exit;
                }
            }
        }

        return $this->templating->render('admin/productions/create.html.php', [
            'categories' => $categories,
            'platforms' => $platforms,
            'error' => $error,
            'router' => $this->router
        ]);
    }

    public function productionsEditAction(int $id, ?array $formData): string
    {
        $this->requireAuth();

        $pdo = $this->getDb();
        $error = null;

        // pobierz produkcje
        $stmt = $pdo->prepare("SELECT * FROM productions WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $production = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$production) {
            $this->router->redirect($this->router->generatePath('admin-productions'));
            exit;
        }

        // pobierz wszystkie kategorie i platformy
        $categories = $pdo->query("SELECT * FROM categories ORDER BY nazwa")->fetchAll(PDO::FETCH_ASSOC);
        $platforms = $pdo->query("SELECT * FROM platforms ORDER BY nazwa")->fetchAll(PDO::FETCH_ASSOC);

        // pobierz aktualne powiazania
        $stmt = $pdo->prepare("SELECT id_kategorii FROM production_category WHERE id_produkcji = :id");
        $stmt->execute(['id' => $id]);
        $current_categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $stmt = $pdo->prepare("SELECT id_platformy, dostepny_sezon FROM production_platform WHERE id_produkcji = :id");
        $stmt->execute(['id' => $id]);
        $platforms_raw = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $current_platforms = [];
        $current_platforms_seasons = [];
        foreach ($platforms_raw as $p) {
            $current_platforms[] = $p['id_platformy'];
            $current_platforms_seasons[$p['id_platformy']] = $p['dostepny_sezon'] ?? '';
        }

        if ($formData !== null) {
            $tytul = trim($formData['tytul'] ?? '');
            $opis = trim($formData['opis'] ?? '');
            $typ = $formData['typ'] ?? '';
            $rok = intval($formData['rok'] ?? 0);
            $kraj = trim($formData['kraj'] ?? '');
            $plakat_url = trim($formData['plakat_url'] ?? '');
            $selected_categories = $formData['categories'] ?? [];
            $platforms_data = $formData['platforms'] ?? [];

            if (empty($tytul) || empty($opis) || empty($typ) || $rok < 1900 || empty($plakat_url)) {
                $error = 'Wypełnij poprawnie wszystkie wymagane pola!';
            } elseif (!in_array($typ, ['film', 'serial'])) {
                $error = 'Nieprawidłowy typ produkcji!';
            } else {
                // sprawdz duplikat (nie sam siebie)
                $check = $pdo->prepare("SELECT id FROM productions WHERE tytul = :tytul AND rok = :rok AND id != :id");
                $check->execute(['tytul' => $tytul, 'rok' => $rok, 'id' => $id]);

                if ($check->fetch()) {
                    $error = 'Produkcja o tym tytule i roku już istnieje!';
                } else {
                    // aktualizuj produkcje
                    $stmt = $pdo->prepare("UPDATE productions SET tytul = :tytul, opis = :opis, typ = :typ, rok = :rok, kraj = :kraj, plakat_url = :plakat_url WHERE id = :id");
                    $stmt->execute([
                        'tytul' => $tytul,
                        'opis' => $opis,
                        'typ' => $typ,
                        'rok' => $rok,
                        'kraj' => $kraj ?: null,
                        'plakat_url' => $plakat_url,
                        'id' => $id
                    ]);

                    // usun stare powiazania i dodaj nowe
                    $pdo->prepare("DELETE FROM production_category WHERE id_produkcji = :id")->execute(['id' => $id]);
                    $pdo->prepare("DELETE FROM production_platform WHERE id_produkcji = :id")->execute(['id' => $id]);

                    foreach ($selected_categories as $cat_id) {
                        $pdo->prepare("INSERT INTO production_category (id_produkcji, id_kategorii) VALUES (:prod, :cat)")
                            ->execute(['prod' => $id, 'cat' => $cat_id]);
                    }

                    // dodaj powiazania z platformami i sezonami (ADM07)
                    foreach ($platforms_data as $plat_id => $plat_info) {
                        if (isset($plat_info['selected'])) {
                            $sezon = trim($plat_info['sezon'] ?? '');
                            $pdo->prepare("INSERT INTO production_platform (id_produkcji, id_platformy, dostepny_sezon) VALUES (:prod, :plat, :sezon)")
                                ->execute(['prod' => $id, 'plat' => $plat_id, 'sezon' => $sezon ?: null]);
                        }
                    }

                    $this->router->redirect($this->router->generatePath('admin-productions') . '&success=updated');
                    exit;
                }
            }

            // zachowaj wybrane wartosci przy bledzie
            $current_categories = $selected_categories;
            // przetworz platforms_data na current_platforms i current_platforms_seasons
            $current_platforms = [];
            $current_platforms_seasons = [];
            foreach ($platforms_data as $plat_id => $plat_info) {
                if (isset($plat_info['selected'])) {
                    $current_platforms[] = $plat_id;
                    $current_platforms_seasons[$plat_id] = $plat_info['sezon'] ?? '';
                }
            }
        }

        return $this->templating->render('admin/productions/edit.html.php', [
            'production' => $production,
            'categories' => $categories,
            'platforms' => $platforms,
            'current_categories' => $current_categories,
            'current_platforms' => $current_platforms,
            'current_platforms_seasons' => $current_platforms_seasons,
            'error' => $error,
            'router' => $this->router
        ]);
    }

    public function productionsDeleteAction(int $id): void
    {
        $this->requireAuth();

        $pdo = $this->getDb();

        // usun powiazania
        $pdo->prepare("DELETE FROM production_category WHERE id_produkcji = :id")->execute(['id' => $id]);
        $pdo->prepare("DELETE FROM production_platform WHERE id_produkcji = :id")->execute(['id' => $id]);
        $pdo->prepare("DELETE FROM ratings WHERE id_produkcji = :id")->execute(['id' => $id]);

        // usun produkcje
        $stmt = $pdo->prepare("DELETE FROM productions WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $this->router->redirect($this->router->generatePath('admin-productions') . '&success=deleted');
        exit;
    }
}
