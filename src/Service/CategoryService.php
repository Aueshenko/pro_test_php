<?php

namespace App\Service;

use App\Model\CategoryRepository;

class CategoryService
{
    private CategoryRepository $categoryRepository;

    public function __construct()
    {
        $this->categoryRepository = new CategoryRepository();
    }

    public function findAll(): array
    {
        return $this->categoryRepository->findAll();
    }
}