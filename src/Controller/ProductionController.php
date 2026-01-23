<?php

namespace App\Controller;

use App\Model\Production;
use App\Model\Rating;
use App\Service\Templating;
use App\Service\Router;
use App\Service\Config;
use PDO;

class ProductionController
{
    private Templating $templating;
    private Router $router;

    public function __construct(Templating $templating, Router $router)
    {
        $this->templating = $templating;
        $this->router = $router;
    }

    private function getPdo(): PDO
    {
        return new PDO(Config::get('db_dsn'), Config::get('db_user'), Config::get('db_pass'));
    }

    public function showAction(int $id): string
    {
        $production = Production::find($id);

        if (!$production) {
            return 'Nie znaleziono produkcji';
        }

        // pobieramy platformy z sezonami
        $platformsWithSeasons = $production->loadPlatformsWithSeasons();

        // pobieramy kategorie
        $categories = $production->loadCategories();

        // pobieramy oceny (tylko zatwierdzone)
        $pdo = $this->getPdo();
        $sql = "SELECT * FROM ratings WHERE id_produkcji = :id AND status_moderacji = 'zatwierdzone' ORDER BY data DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $ratingsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /** @var Rating[] $ratings */
        $ratings = [];
        foreach ($ratingsData as $row) {
            $ratings[] = Rating::fromArray($row);
        }

        // srednia ocen
        $avgRating = 0;
        if (count($ratings) > 0) {
            $sum = 0;
            foreach ($ratings as $r) {
                $sum += $r->getOcena();
            }
            $avgRating = round($sum / count($ratings), 1);
        }

        return $this->templating->render('production/show.html.php', [
            'production' => $production,
            'platformsWithSeasons' => $platformsWithSeasons,
            'categories' => $categories,
            'ratings' => $ratings,
            'avgRating' => $avgRating,
            'router' => $this->router
        ]);
    }

    public function addRatingAction(int $id, ?array $form): string
    {
        $production = Production::find($id);

        if (!$production) {
            header('Location: ?action=search');
            exit;
        }

        $error = null;
        $success = null;

        if ($form) {
            $ocena = (int) ($form['ocena'] ?? 0);
            $tresc = trim($form['tresc'] ?? '');

            if ($ocena < 1 || $ocena > 5) {
                $error = 'Ocena musi być od 1 do 5';
            } else {
                $rating = new Rating();
                $rating->setIdProdukcji($id);
                $rating->setOcena($ocena);
                $rating->setTresc($tresc ?: null);
                $rating->setData(date('Y-m-d H:i:s'));
                $rating->setStatusModeracji('oczekujące');
                $rating->save();

                $success = 'Dziękujemy za ocenę! Zostanie opublikowana po moderacji.';
            }
        }

        // przekieruj z powrotem do strony produkcji
        if ($success) {
            header('Location: ?action=production-show&id=' . $id . '&msg=rating_added');
            exit;
        }

        // jesli blad - pokaz strone produkcji z bledem
        return $this->showActionWithError($id, $error);
    }

    private function showActionWithError(int $id, ?string $error): string
    {
        $production = Production::find($id);

        if (!$production) {
            return 'Nie znaleziono produkcji';
        }

        $platformsWithSeasons = $production->loadPlatformsWithSeasons();
        $categories = $production->loadCategories();

        $pdo = $this->getPdo();
        $sql = "SELECT * FROM ratings WHERE id_produkcji = :id AND status_moderacji = 'zatwierdzone' ORDER BY data DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['id' => $id]);
        $ratingsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /** @var Rating[] $ratings */
        $ratings = [];
        foreach ($ratingsData as $row) {
            $ratings[] = Rating::fromArray($row);
        }

        $avgRating = 0;
        if (count($ratings) > 0) {
            $sum = 0;
            foreach ($ratings as $r) {
                $sum += $r->getOcena();
            }
            $avgRating = round($sum / count($ratings), 1);
        }

        return $this->templating->render('production/show.html.php', [
            'production' => $production,
            'platformsWithSeasons' => $platformsWithSeasons,
            'categories' => $categories,
            'ratings' => $ratings,
            'avgRating' => $avgRating,
            'router' => $this->router,
            'error' => $error
        ]);
    }
}
