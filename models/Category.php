<?php
namespace Models;

use PDO;

class Category {
    public static function getAllCategories() {
        global $pdo;
        $sql = "SELECT * FROM categories";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}