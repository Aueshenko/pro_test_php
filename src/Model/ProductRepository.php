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
    
    public function findAllCategories()
    {
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function findById($id)
    {
        return null;
    }

}