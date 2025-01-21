<?php
namespace Models;

use PDO;
include 'C:\xampp\htdocs\movie_forum\config\db.php';

class Post {
    public static function createPost($userId, $title, $content, $categoryId) {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, title, content, category_id, created_at) VALUES (:user_id, :title, :content, :category_id, NOW())");
        $stmt->execute([
            'user_id' => $userId,
            'title' => $title,
            'content' => $content,
            'category_id' => $categoryId
        ]);
    }

    public static function getPosts() {
        global $pdo;
        $stmt = $pdo->query("
            SELECT posts.*, 
                   categories.name as category_name,
                   users.avatar as user_avatar,  -- Додаємо аватар користувача
                   COUNT(CASE WHEN post_ratings.rating = 1 THEN 1 END) AS like_count,
                   COUNT(CASE WHEN post_ratings.rating = -1 THEN 1 END) AS dislike_count
            FROM posts
            LEFT JOIN categories ON posts.category_id = categories.id
            LEFT JOIN users ON posts.user_id = users.id  -- Зв'язок з користувачем
            LEFT JOIN post_ratings ON posts.id = post_ratings.post_id
            GROUP BY posts.id
            ORDER BY like_count DESC
        ");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }



    public static function getCategoryById($categoryId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = :category_id");
        $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        $stmt->execute();
    
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return $result ? $result['name'] : 'Unknown';
    }

    public static function getPostById($postId) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT posts.*, categories.name as category_name FROM posts JOIN categories ON posts.category_id = categories.id WHERE posts.id = :id");
        $stmt->execute(['id' => $postId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);  // Повертає пост або null
    }

// Пошук постів по ключовому слову і категорії, включаючи автора
public static function searchPosts($query = '', $categoryId = null) {
    global $pdo;

    // Оновлений SQL запит з додаванням інформації про автора
    $sql = "SELECT p.id, p.title, p.content, p.created_at, c.name AS category_name, 
                   u.username, u.avatar
            FROM posts p
            JOIN categories c ON p.category_id = c.id
            LEFT JOIN users u ON p.user_id = u.id
            WHERE p.title LIKE :query";

    // Якщо є фільтр по категорії, додаємо умову
    if ($categoryId) {
        $sql .= " AND p.category_id = :category_id";
    }

    // Підготовка та виконання запиту
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':query', '%' . $query . '%');
    
    if ($categoryId) {
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
    }

    $stmt->execute();

    // Повертаємо результат
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
?>