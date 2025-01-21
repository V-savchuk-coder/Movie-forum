<?php
namespace Models;

use PDO;
use PDOException;

class PostRating {
    private static function getDBConnection() {
        include '../config/db.php';
        return $pdo;
    }

    // Метод для підрахунку лайків
    public static function countLikes($postId) {
        try {
            $pdo = self::getDBConnection();
            $stmt = $pdo->prepare("SELECT COUNT(*) as like_count FROM post_ratings WHERE post_id = ? AND rating = 1");
            $stmt->execute([$postId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['like_count'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    // Метод для підрахунку дизлайків
    public static function countDislikes($postId) {
        try {
            $pdo = self::getDBConnection();
            $stmt = $pdo->prepare("SELECT COUNT(*) as dislike_count FROM post_ratings WHERE post_id = ? AND rating = -1");
            $stmt->execute([$postId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['dislike_count'] ?? 0;
        } catch (PDOException $e) {
            return 0;
        }
    }

    // Метод для оновлення рейтингу (лайк/дизлайк)
    public static function ratePost($postId, $userId, $rating) {
        try {
            $pdo = self::getDBConnection();
            // Видаляємо старий рейтинг, якщо існує
            $stmt = $pdo->prepare("DELETE FROM post_ratings WHERE post_id = ? AND user_id = ?");
            $stmt->execute([$postId, $userId]);
            
            // Додаємо новий рейтинг
            $stmt = $pdo->prepare("INSERT INTO post_ratings (post_id, user_id, rating) VALUES (?, ?, ?)");
            $stmt->execute([$postId, $userId, $rating]);
        } catch (PDOException $e) {
            // Обробка помилок
        }
    }
}