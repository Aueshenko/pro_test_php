<?php

namespace App\Controller;

use App\Helper\FlashMessageHelper;
use App\Helper\SeoHelper;
use App\Service\ProductService;
use App\Service\CategoryService;

class ProductController extends BaseController
{
    private ProductService $productService;
    private CategoryService $categoryService;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->categoryService = new CategoryService();
    }

    public function list(): void
    {
        $filters = $this->productService->getFiltersFromRequest($_GET);

        $seo_data = SeoHelper::buildCatalogSeo();

        $this->render('product_list', [
            'products' => $this->productService->findAll($filters),
            'categories' => $this->categoryService->findAll(),
            'filters' => $filters,
            'seo' => $seo_data,
            'flashMessage' => FlashMessageHelper::getStatusMessage($_GET),
            'exportUrl' => $this->productService->buildExportUrl($_GET)
        ]);
    }

    public function show($id): void
    {
        $product = $this->productService->findById($id);

        if (!$product) {
            header("HTTP/1.0 404 Not Found");
            echo "<h1>404 - Продукт не найден</h1>";
            exit;
        }

        $seo_data = SeoHelper::buildProductSeo($product);

        $this->render('product_detail', [
            'product' => $product,
            'seo' => $seo_data
        ]);
    }

    public function add(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->productService->addOne($_POST);

            if ($result === true) {
                $this->redirect('/index.php', ['action' => 'list', 'status' => 'added']);
            }

            $params = array_merge(['error' => 'validation'], $_POST);
            foreach ($result as $field => $msg) {
                $params["error_$field"] = $msg;
            }

            $this->redirect('/index.php', array_merge(['action' => 'add'], $params));
        }

        $categories = $this->categoryService->findAll();

        $formData = [
            'name' => $_GET['name'] ?? '',
            'description' => $_GET['description'] ?? '',
            'price' => $_GET['price'] ?? '',
            'category_id' => $_GET['category_id'] ?? '',
        ];

        $errors = [];
        foreach ($_GET as $key => $value) {
            if (str_starts_with($key, 'error_')) {
                $field = substr($key, 6);
                $errors[$field] = $value;
            }
        }

        $this->render('add_product', ['categories' => $categories, 'formData' => $formData, 'errors' => $errors]);
    }

    public function edit($productId): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = $_POST;
            $data['productId'] = $productId;

            $result = $this->productService->updateOne($data);

            if ($result === true) {
                $this->redirect('/index.php', ['action' => 'list', 'status' => 'updated']);
            }
            $this->redirect('/index.php', ['action' => 'list', 'status' => 'error']);
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

        $this->redirect('/index.php', ['action' => 'list', 'status' => $status]);
    }
}