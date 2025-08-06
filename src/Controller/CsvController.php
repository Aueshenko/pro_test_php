<?php

namespace App\Controller;

use App\Service\ProductService;

class CsvController extends BaseController
{
    private ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function import(): void
    {
        $result = [
            'formError' => null,
            'success' => null,
            'rowErrors' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
            $result = $this->productService->handleCsvImport($_FILES['csv_file']);
        }

        $this->render('import_form', $result);
    }

    public function export(): void
    {
        $filters = $this->productService->getFiltersFromRequest($_GET);
        $products = $this->productService->findAll($filters);
        $this->productService->handleCsvExport($products);
        exit;
    }
}