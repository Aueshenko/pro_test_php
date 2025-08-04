<?php

namespace App\Model;

use App\Core\Database;
use PDO;

class ProductRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function findAll($filters = [])
    {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id";
        
        if (!empty($filters['name'])) {
            $sql .= " WHERE p.name LIKE '%" . $filters['name'] . "%'";
        }
        
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $productId): ?array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $productId]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);

        return $product ?: null;
    }

    public function addOne(array $data): bool
    {
        $sql = "INSERT INTO products (name, description, price, category_id) 
            VALUES (:name, :description, :price, :category_id)";

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'] ?? null,
            ':price' => $data['price'],
            ':category_id' => $data['category_id'] ?? null,
        ]);
    }

    public function updateOne(array $data): int
    {
        $sql = "UPDATE products 
            SET name = :name,
                description = :description,
                price = :price,
                category_id = :category_id
            WHERE id = :id";

        $stmt = $this->pdo->prepare($sql);

        $stmt->execute([
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':category_id' => $data['category_id'],
            ':id' => $data['id'],
        ]);

        return $stmt->rowCount();
    }

    public function deleteOne($productId): int
    {
        $stmt = $this->pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $productId]);
        return $stmt->rowCount();
    }
}