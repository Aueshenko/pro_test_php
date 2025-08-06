<?php

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static $pdo;

    public static function getConnection()
    {
        if (self::$pdo === null) {
            $db_host = 'localhost';
            $db_name = 'pro_ecommerce';
            $db_user = 'root';
            $db_pass = '';

            try {
                self::$pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_pass);
            } catch (PDOException $e) {
                die("Ошибка подключения к базе данных: " . $e->getMessage());
            }
        }
        return self::$pdo;
    }

    public static function setConnection(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }
}