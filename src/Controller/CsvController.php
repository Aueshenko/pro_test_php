<?php

namespace App\Controller;

use App\Service\CsvService;
use App\Service\ProductService;

class CsvController extends BaseController
{
    private ProductService $productService;
    private CsvService $csvService;

    public function __construct()
    {
        $this->productService = new ProductService();
        $this->csvService = new CsvService();
    }

    public function import(): void
    {
        $result = [
            'formError' => null,
            'success' => null,
            'rowErrors' => []
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['csv_file'])) {
            $csvResult = $this->csvService->handleCsvImport($_FILES['csv_file']);

            if ($csvResult['formError']) {
                $result['formError'] = $csvResult['formError'];
            }
            else {
                $importResult = $this->productService->importProducts($csvResult['rows']);

                $result['success'] = $importResult['success'];
                $result['rowErrors'] = $importResult['errors'];
            }
        }

        $this->render('import_form', $result);
    }

    public function export(): void
    {
        $data = $this->productService->getProducts($_GET);
        $this->csvService->handleCsvExport($data['products']);
        exit;
    }
}