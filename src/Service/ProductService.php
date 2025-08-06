<?php

namespace App\Service;

use App\Model\ProductRepository;
use App\Validation\ProductValidator;

class ProductService
{
    private ProductRepository $productRepository;
    private ProductValidator $productValidator;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
        $this->productValidator = new ProductValidator();
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

    public function importProducts(array $rows): array
    {
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
}