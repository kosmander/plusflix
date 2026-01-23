<?php

namespace App\Controller;

use App\Model\Production;
use App\Model\Category;
use App\Model\Platform;
use App\Service\Templating;
use App\Service\Router;
use App\Service\Config;
use PDO;

class SearchController
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

    public function indexAction(): string
    {
        $query = $_GET['q'] ?? '';
        $kategoria = $_GET['kategoria'] ?? '';
        $rok = $_GET['rok'] ?? '';
        $kraj = $_GET['kraj'] ?? '';
        $typ = $_GET['typ'] ?? '';
        $sort = $_GET['sort'] ?? '';

        $pdo = $this->getPdo();

        // budujemy zapytanie
        $sql = "SELECT DISTINCT p.* FROM productions p";
        $joins = [];
        $where = [];
        $params = [];

        // jezeli filtrujemy po kategorii to musimy dolaczyc tabele
        if ($kategoria) {
            $joins[] = "JOIN production_category pc ON p.id = pc.id_produkcji";
            $where[] = "pc.id_kategorii = :kategoria";
            $params['kategoria'] = $kategoria;
        }

        // wyszukiwanie po tytule
        if ($query) {
            $where[] = "p.tytul LIKE :query";
            $params['query'] = '%' . $query . '%';
        }

        // filtr po roku
        if ($rok) {
            $where[] = "p.rok = :rok";
            $params['rok'] = $rok;
        }

        // filtr po kraju
        if ($kraj) {
            $where[] = "p.kraj = :kraj";
            $params['kraj'] = $kraj;
        }

        // filtr po typie (film/serial)
        if ($typ) {
            $where[] = "p.typ = :typ";
            $params['typ'] = $typ;
        }

        // skladamy zapytanie
        if (count($joins) > 0) {
            $sql .= " " . implode(" ", $joins);
        }
        if (count($where) > 0) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        // sortowanie
        switch ($sort) {
            case 'rok_asc':
                $sql .= " ORDER BY p.rok ASC";
                break;
            case 'rok_desc':
                $sql .= " ORDER BY p.rok DESC";
                break;
            case 'tytul_asc':
                $sql .= " ORDER BY p.tytul ASC";
                break;
            case 'tytul_desc':
                $sql .= " ORDER BY p.tytul DESC";
                break;
            default:
                $sql .= " ORDER BY p.tytul ASC";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $productions = [];
        foreach ($rows as $row) {
            $productions[] = Production::fromArray($row);
        }

        // pobieramy kategorie do selecta
        $categories = Category::findAll();

        // pobieramy unikalne kraje do selecta
        $sqlKraje = "SELECT DISTINCT kraj FROM productions WHERE kraj IS NOT NULL ORDER BY kraj";
        $kraje = $pdo->query($sqlKraje)->fetchAll(PDO::FETCH_COLUMN);

        // pobieramy unikalne lata do selecta
        $sqlLata = "SELECT DISTINCT rok FROM productions ORDER BY rok DESC";
        $lata = $pdo->query($sqlLata)->fetchAll(PDO::FETCH_COLUMN);

        return $this->templating->render('search/index.html.php', [
            'productions' => $productions,
            'categories' => $categories,
            'kraje' => $kraje,
            'lata' => $lata,
            'filters' => [
                'q' => $query,
                'kategoria' => $kategoria,
                'rok' => $rok,
                'kraj' => $kraj,
                'typ' => $typ,
                'sort' => $sort
            ],
            'router' => $this->router
        ]);
    }

    // endpoint dla autouzupelniania - zwraca JSON
    public function suggestAction(): string
    {
        $query = $_GET['q'] ?? '';

        if (strlen($query) < 4) {
            return json_encode([]);
        }

        $pdo = $this->getPdo();
        $sql = "SELECT id, tytul, rok, typ FROM productions WHERE tytul LIKE :query LIMIT 10";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['query' => '%' . $query . '%']);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        header('Content-Type: application/json');
        return json_encode($results);
    }

    // losowy film
    public function randomAction(): void
    {
        $pdo = $this->getPdo();
        $sql = "SELECT id FROM productions ORDER BY RANDOM() LIMIT 1";
        $stmt = $pdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            header('Location: ?action=production-show&id=' . $row['id']);
        } else {
            header('Location: ?action=search');
        }
        exit;
    }
}
