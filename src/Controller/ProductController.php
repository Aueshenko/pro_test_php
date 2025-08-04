<?php

namespace App\Controller;

use App\Helper\FlashMessageHelper;
use App\Service\ProductService;
use App\Service\CategoryService;

class ProductController
{
    private ProductService $productService;
    private CategoryService $categoryService;
    private FlashMessageHelper $flashMessageHelper;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->categoryService = new CategoryService();
        $this->flashMessageHelper = new FlashMessageHelper();
    }

    public function list(): void
    {
        $filters = $this->productService->getFiltersFromRequest($_GET);

        $seo_data = [
            'title' => 'Админ-панель',
            'description' => 'Управление продуктами'
        ];

        $this->render('product_list', [
            'products' => $this->productService->findAll($filters),
            'categories' => $this->categoryService->findAll(),
            'filters' => $filters,
            'seo' => $seo_data,
            'flashMessage' => $this->flashMessageHelper->getStatusMessage($_GET)
        ]);
    }

    public function show($id): void
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 - Продукт не найден (или метод findById не реализован)</h1>";
            exit;
        }

        $seo_data = [];

        $this->render('product_detail', [
            'product' => $product,
            'seo' => $seo_data
        ]);
    }

    public function add(): void
    {
        $categories = $this->categoryService->findAll();
        $this->render('add_product', ['categories' => $categories]);
    }

    public function edit($productId): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['productId'] = $productId;

            $updateStatus = $this->productService->updateOne($data);
            $status = $updateStatus ? 'updated' : 'error';

            header('Location: /index.php?action=list&status=' . $status);
            exit;
        }

        $product = $this->productService->findById($productId);
        $categories = $this->categoryService->findAll();

        $this->render('edit_product', [
            'product' => $product,
            'categories' => $categories
        ]);
    }
    
    public function delete($id): void
    {
        $deleteStatus = $this->productService->deleteOne($id);
        $status = $deleteStatus ? 'deleted' : 'error';

        header('Location: /index.php?action=list&status=' . $status);
        exit();
    }
    
    public function import()
    {
        $this->render('import_form');
    }

    public function export()
    {
        header('Content-Type: text/plain');
        echo "Функционал экспорта должен быть реализован здесь.";
        exit();
    }

    public function sitemap()
    {
        header('Content-Type: application/xml; charset=utf-8');
        echo '<?xml version="1.0" encoding="UTF-8"?>';
    }
    
    private function render($view, $data = [])
    {
        extract($data);
        $content = __DIR__ . '/../../templates/' . $view . '.phtml';
        require __DIR__ . '/../../templates/admin_layout.phtml';
    }
}