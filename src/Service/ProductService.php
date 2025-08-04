<?php

namespace App\Service;

use App\Model\ProductRepository;

class ProductService
{
    private ProductRepository $productRepository;

    public function __construct()
    {
        $this->productRepository = new ProductRepository();
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

    public function updateOne(array $data): bool
    {
        $data = [
            'id' => (int)$data['productId'],
            'name' => trim($data['name'] ?? ''),
            'description' => trim($data['description'] ?? ''),
            'price' => (float)($data['price'] ?? 0),
            'category_id' => !empty($data['category_id']) ? (int)$data['category_id'] : null,
        ];

        return $this->productRepository->updateOne($data) > 0;
    }

    public function deleteOne($productId): bool
    {
        return $this->productRepository->deleteOne($productId) > 0;
    }
}