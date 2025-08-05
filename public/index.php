<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\ProductController;

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

$controller = new ProductController();

switch ($action) {
    case 'add':
        $controller->add();
        break;
    case 'edit':
        $controller->edit($id);
        break;
    case 'delete':
        $controller->delete($id);
        break;
    case 'import':
        $controller->import();
        break;
    case 'export':
        $controller->export();
        break;
    case 'show':
        $controller->show($id);
        break;
    case 'sitemap':
        $controller->sitemap();
        break;
    default:
        $controller->list();
        break;
}