<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'autoload.php';

$config = new \App\Service\Config();

$templating = new \App\Service\Templating();
$router = new \App\Service\Router();

$action = $_REQUEST['action'] ?? null;
$view = null;

switch ($action) {
    // ====== STRONA GLOWNA ======
    case null:
        header('Location: ?action=search');
        exit;

    // ====== WYSZUKIWARKA ======
    case 'search':
        $controller = new \App\Controller\SearchController($templating, $router);
        $view = $controller->indexAction();
        break;
    case 'search-suggest':
        $controller = new \App\Controller\SearchController($templating, $router);
        $view = $controller->suggestAction();
        break;
    case 'search-random':
        $controller = new \App\Controller\SearchController($templating, $router);
        $controller->randomAction();
        break;

    // ====== WIDOK PRODUKCJI ======
    case 'production-show':
        if (!$_REQUEST['id']) {
            break;
        }
        $controller = new \App\Controller\ProductionController($templating, $router);
        $view = $controller->showAction((int)$_REQUEST['id']);
        break;
    case 'production-add-rating':
        if (!$_REQUEST['id']) {
            break;
        }
        $controller = new \App\Controller\ProductionController($templating, $router);
        $view = $controller->addRatingAction((int)$_REQUEST['id'], $_POST['form'] ?? null);
        break;

    // ====== PANEL ADMINA ======
    case 'admin-login':
        $controller = new \App\Controller\AdminController($templating, $router);
        $view = $controller->loginAction($_POST['form'] ?? null);
        break;
    case 'admin-logout':
        $controller = new \App\Controller\AdminController($templating, $router);
        $controller->logoutAction();
        break;
    case 'admin-dashboard':
        $controller = new \App\Controller\AdminController($templating, $router);
        $view = $controller->dashboardAction();
        break;

    // Kategorie
    case 'admin-categories':
        $controller = new \App\Controller\AdminController($templating, $router);
        $view = $controller->categoriesIndexAction();
        break;
    case 'admin-categories-create':
        $controller = new \App\Controller\AdminController($templating, $router);
        $view = $controller->categoriesCreateAction($_POST['form'] ?? null);
        break;
    case 'admin-categories-edit':
        $controller = new \App\Controller\AdminController($templating, $router);
        $view = $controller->categoriesEditAction((int)($_REQUEST['id'] ?? 0), $_POST['form'] ?? null);
        break;
    case 'admin-categories-delete':
        $controller = new \App\Controller\AdminController($templating, $router);
        $controller->categoriesDeleteAction((int)($_REQUEST['id'] ?? 0));
        break;

    // Platformy
    case 'admin-platforms':
        $controller = new \App\Controller\AdminController($templating, $router);
        $view = $controller->platformsIndexAction();
        break;
    case 'admin-platforms-create':
        $controller = new \App\Controller\AdminController($templating, $router);
        $view = $controller->platformsCreateAction($_POST['form'] ?? null);
        break;
    case 'admin-platforms-edit':
        $controller = new \App\Controller\AdminController($templating, $router);
        $view = $controller->platformsEditAction((int)($_REQUEST['id'] ?? 0), $_POST['form'] ?? null);
        break;
    case 'admin-platforms-delete':
        $controller = new \App\Controller\AdminController($templating, $router);
        $controller->platformsDeleteAction((int)($_REQUEST['id'] ?? 0));
        break;

    // Produkcje
    case 'admin-productions':
        $controller = new \App\Controller\AdminController($templating, $router);
        $view = $controller->productionsIndexAction();
        break;
    case 'admin-productions-create':
        $controller = new \App\Controller\AdminController($templating, $router);
        $view = $controller->productionsCreateAction($_POST['form'] ?? null);
        break;
    case 'admin-productions-edit':
        $controller = new \App\Controller\AdminController($templating, $router);
        $view = $controller->productionsEditAction((int)($_REQUEST['id'] ?? 0), $_POST['form'] ?? null);
        break;
    case 'admin-productions-delete':
        $controller = new \App\Controller\AdminController($templating, $router);
        $controller->productionsDeleteAction((int)($_REQUEST['id'] ?? 0));
        break;

    default:
        $view = 'Not found';
        break;
}

if ($view) {
    echo $view;
}
