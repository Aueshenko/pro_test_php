<?php

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\CsvController;
use App\Controller\ProductController;
use App\Controller\SitemapController;

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

$productController = new ProductController();
$csvController = new CsvController();
$sitemapController = new SitemapController();

switch ($action) {
    case 'add':
        $productController->add();
        break;
    case 'edit':
        $productController->edit($id);
        break;
    case 'delete':
        $productController->delete($id);
        break;
    case 'import':
        $csvController->import();
        break;
    case 'export':
        $csvController->export();
        break;
    case 'show':
        $productController->show($id);
        break;
    case 'sitemap':
        $sitemapController->sitemap();
        break;
    default:
        $productController->list();
        break;
}