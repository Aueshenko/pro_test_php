<?php

namespace App\Controller;

use App\Model\ProductRepository;

class ProductController
{
    private $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }

    public function list()
    {
        $filters = [
            'name' => $_GET['search_name'] ?? '',
            'category_id' => $_GET['search_category'] ?? ''
        ];
        
        $products = $this->repository->findAll($filters);
        $categories = $this->repository->findAllCategories();
        
        $seo_data = [
            'title' => 'Админ-панель',
            'description' => 'Управление продуктами'
        ];
        
        $this->render('product_list', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $filters,
            'seo' => $seo_data
        ]);
    }

    public function show($id)
    {
        $product = $this->repository->findById($id);

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

    public function add()
    {
        $categories = $this->repository->findAllCategories();
        $this->render('add_product', ['categories' => $categories]);
    }

    public function edit($id)
    {
        $product = [];
        $categories = $this->repository->findAllCategories();
        $this->render('edit_product', [
            'product' => $product,
            'categories' => $categories
        ]);
    }
    
    public function delete($id)
    {
        header('Location: /index.php?action=list');
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