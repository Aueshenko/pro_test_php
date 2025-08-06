<?php

use PHPUnit\Framework\TestCase;
use App\Model\ProductRepository;
use App\Core\Database;

class ProductRepositoryTest extends TestCase
{
    private ProductRepository $repository;

    public static function setUpBeforeClass(): void
    {
        Database::setConnection(new PDO('sqlite::memory:'));

        $pdo = Database::getConnection();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->exec("
            CREATE TABLE categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT
            );
        ");

        $pdo->exec("
            CREATE TABLE products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT,
                description TEXT,
                price REAL,
                category_id INTEGER,
                FOREIGN KEY (category_id) REFERENCES categories(id)
            );
        ");

        $pdo->exec("INSERT INTO categories (name) VALUES ('Тестовая категория')");
    }

    protected function setUp(): void
    {
        $this->repository = new ProductRepository();
        Database::getConnection()->exec("DELETE FROM products");
    }

    public function testAddAndFindProduct(): void
    {
        $this->repository->addOne([
            'name' => 'Тестовый продукт',
            'description' => 'Описание',
            'price' => 100.5,
            'category_id' => 1
        ]);

        $products = $this->repository->findAll();
        $this->assertCount(1, $products);

        $product = $this->repository->findById($products[0]['id']);
        $this->assertEquals('Тестовый продукт', $product['name']);
    }

    public function testUpdateProduct(): void
    {
        $this->repository->addOne([
            'name' => 'Старое название',
            'description' => 'Desc',
            'price' => 100,
            'category_id' => 1
        ]);

        $product = $this->repository->findAll()[0];
        $updatedRows = $this->repository->updateOne([
            'id' => $product['id'],
            'name' => 'Новое название',
            'description' => 'Обновленное описание',
            'price' => 200,
            'category_id' => 1
        ]);

        $this->assertEquals(1, $updatedRows);

        $updatedProduct = $this->repository->findById($product['id']);
        $this->assertEquals('Новое название', $updatedProduct['name']);
        $this->assertEquals(200, $updatedProduct['price']);
    }

    public function testDeleteProduct(): void
    {
        $this->repository->addOne([
            'name' => 'Для удаления',
            'description' => 'Описание',
            'price' => 50,
            'category_id' => 1
        ]);

        $product = $this->repository->findAll()[0];

        $deletedRows = $this->repository->deleteOne($product['id']);
        $this->assertEquals(1, $deletedRows);

        $this->assertNull($this->repository->findById($product['id']));
    }

    public function testCountAll(): void
    {
        $this->repository->addOne([
            'name' => 'Продукт 1',
            'description' => '',
            'price' => 10,
            'category_id' => 1
        ]);

        $this->repository->addOne([
            'name' => 'Продукт 2',
            'description' => '',
            'price' => 20,
            'category_id' => 1
        ]);

        $this->assertEquals(2, $this->repository->countAll());
    }
}