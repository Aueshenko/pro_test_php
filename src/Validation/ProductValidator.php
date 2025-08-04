<?php

namespace App\Validation;

class ProductValidator
{
    protected array $errors = [];

    protected int $maxNameLength = 255;

    protected float $maxPrice = 99999999.99;

    public function validate(array $data): bool
    {
        $this->errors = [];

        $name = trim($data['name'] ?? '');
        if ($name === '') {
            $this->errors['name'] = 'Название не должно быть пустым';
        }
        elseif (mb_strlen($name) > $this->maxNameLength) {
            $this->errors['name'] = "Название не должно превышать $this->maxNameLength символов";
        }

        if (!isset($data['price']) || trim($data['price']) === '') {
            $this->errors['price'] = 'Цена не должна быть пустой';
        }
        elseif (!is_numeric($data['price'])) {
            $this->errors['price'] = 'Цена должна быть числом';
        }
        else {
            $price = (float)$data['price'];
            if ($price < 0) {
                $this->errors['price'] = 'Цена не может быть отрицательной';
            }
            elseif ($price > $this->maxPrice) {
                $this->errors['price'] = "Цена не должна превышать $this->maxPrice";
            }
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function sanitize(array $data): array
    {
        return [
            'name' => trim($data['name'] ?? ''),
            'description' => trim($data['description'] ?? ''),
            'price' => isset($data['price']) ? (float)$data['price'] : 0,
            'category_id' => !empty($data['category_id']) ? (int)$data['category_id'] : null,
            'image_url' => trim($data['image_url'] ?? ''),
        ];
    }
}
