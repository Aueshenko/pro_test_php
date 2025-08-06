<?php

namespace App\Service;

use App\Utils\CsvHandler;

class CsvService
{
    private CsvHandler $csvHandler;

    public function __construct()
    {
        $this->csvHandler = new CsvHandler();
    }

    public function handleCsvImport(array $file): array
    {
        $result = [
            'formError' => null,
            'rows' => []
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

        $rows = $this->csvHandler->read($file['tmp_name']);
        $result['rows'] = $rows;

        return $result;
    }

    public function handleCsvExport(array $products): void
    {
        $csvData = [];

        foreach ($products as $product) {
            $csvData[] = [
                'name'        => $product['name'] ?? '',
                'description' => $product['description'] ?? '',
                'price'       => $product['price'] ?? '',
                'category_id' => $product['category_id'] ?? '',
                'image_url'   => $product['image_url'] ?? '',
            ];
        }

        if (empty($csvData)) {
            $csvData[] = [
                'name' => '',
                'description' => '',
                'price' => '',
                'category_id' => '',
                'image_url' => ''
            ];
        }

        $filename = 'products_export_' . date('Y-m-d_H-i-s') . '.csv';
        $this->csvHandler->outputToBrowser($filename, $csvData);
    }

    public function buildExportUrl(array $currentQuery): string
    {
        $params = $currentQuery;
        $params['action'] = 'export';

        return '/index.php?' . http_build_query($params);
    }
}