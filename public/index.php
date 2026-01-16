<?php
require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'autoload.php';

$config = new \App\Service\Config();

$templating = new \App\Service\Templating();
$router = new \App\Service\Router();

$action = $_REQUEST['action'] ?? null;
switch ($action) {
    case 'post-index':
    case null:
        $controller = new \App\Controller\PostController();
        $view = $controller->indexAction($templating, $router);
        break;
    case 'post-create':
        $controller = new \App\Controller\PostController();
        $view = $controller->createAction($_REQUEST['post'] ?? null, $templating, $router);
        break;
    case 'post-edit':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = new \App\Controller\PostController();
        $view = $controller->editAction($_REQUEST['id'], $_REQUEST['post'] ?? null, $templating, $router);
        break;
    case 'post-show':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = new \App\Controller\PostController();
        $view = $controller->showAction($_REQUEST['id'], $templating, $router);
        break;
    case 'post-delete':
        if (! $_REQUEST['id']) {
            break;
        }
        $controller = new \App\Controller\PostController();
        $view = $controller->deleteAction($_REQUEST['id'], $router);
        break;
    case 'info':
        $controller = new \App\Controller\InfoController();
        $view = $controller->infoAction();
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
