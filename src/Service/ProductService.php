<?php

namespace App\Service;

use App\Model\ProductRepository;
use App\Utils\CsvHandler;
use App\Validation\ProductValidator;

class ProductService
{
    private ProductRepository $productRepository;
    private ProductValidator $productValidator;
    private CsvHandler $csvHandler;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->productValidator = new ProductValidator();
        $this->csvHandler = new CsvHandler();
    }

    public function getFiltersFromRequest(array $query): array
    {
        return [
            'name' => $query['search_name'] ?? '',
            'category_id' => $query['search_category'] ?? ''
        ];
    }

    public function findAll($filters = []): array
    {
        return $this->productRepository->findAll($filters);
    }

    public function findById($productId): array
    {
        return $this->productRepository->findById($productId);
    }

    public function addOne(array $data): bool|array
    {
        if (!$this->productValidator->validate($data)) {
            return $this->productValidator->getErrors();
        }

        $cleanData = $this->productValidator->sanitize($data);

        $result = $this->productRepository->addOne($cleanData);

        return $result ? true : ['db_error' => 'Ошибка при сохранении в базе'];
    }

    public function updateOne(array $data): bool|array
    {
        if (!$this->productValidator->validate($data)) {
            return $this->productValidator->getErrors();
        }

        $cleanData = $this->productValidator->sanitize($data);
        $cleanData['id'] = (int)($data['productId'] ?? 0);

        return $this->productRepository->updateOne($cleanData) > 0
            ? true
            : ['db_error' => 'Ошибка при обновлении в базе'];
    }

    public function deleteOne($productId): bool
    {
        return $this->productRepository->deleteOne($productId) > 0;
    }

    public function handleCsvImport(array $file): array
    {
        $result = [
            'formError' => null,
            'success' => null,
            'rowErrors' => []
        ];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $result['formError'] = match ($file['error']) {
                UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Файл слишком большой',
                UPLOAD_ERR_NO_FILE => 'Файл не выбран',
                default => 'Ошибка загрузки файла'
            };
            return $result;
        }

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if ($extension !== 'csv') {
            $result['formError'] = 'Можно загружать только файлы с расширением CSV';
            return $result;
        }

        $importResult = $this->importFromCsv($file['tmp_name']);
        $result['success'] = $importResult['success'];
        $result['rowErrors'] = $importResult['errors'];

        return $result;
    }

    public function handleCsvExport($products): void
    {
        if (empty($products)) {
            $products = [[
                'id' => '',
                'name' => '',
                'description' => '',
                'price' => '',
                'category_id' => '',
                'category_name' => ''
            ]];
        }

        $filename = 'products_export_' . date('Y-m-d_H-i-s') . '.csv';

        $this->csvHandler->outputToBrowser($filename, $products);
    }

    public function importFromCsv(string $filePath): array
    {
        $rows = $this->csvHandler->read($filePath);

        $errors = [];
        $validProducts = [];

        foreach ($rows as $index => $row) {
            if (!$this->productValidator->validate($row)) {
                $errors[$index + 1] = $this->productValidator->getErrors();
                continue;
            }

            $validProducts[] = $this->productValidator->sanitize($row);
        }

        $successCount = 0;
        if (!empty($validProducts)) {
            try {
                $successCount = $this->productRepository->addMany($validProducts);
            } catch (\Exception $e) {
                $errors['db_many_insert'] = ['Ошибка при массовой вставке: ' . $e->getMessage()];
            }
        }

        return [
            'success' => $successCount,
            'errors' => $errors
        ];
    }

    public function buildExportUrl(array $currentQuery): string
    {
        $params = $currentQuery;
        $params['action'] = 'export';

        return '/index.php?' . http_build_query($params);
    }
}