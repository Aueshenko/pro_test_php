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

    public function findById($id)
    {
        return $this->productRepository->findById($id);
    }

    public function deleteOne($productId): bool
    {
        return $this->productRepository->deleteOne($productId) > 0;
    }
}