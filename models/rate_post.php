<?php
include_once '../config/db.php';
include_once '../models/PostRating.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['post_id'], $_POST['user_id'], $_POST['rating'])) {
        $postId = (int)$_POST['post_id'];
        $userId = (int)$_POST['user_id'];
        $rating = (int)$_POST['rating'];

        // Додаємо оцінку
        Models\PostRating::ratePost($postId, $userId, $rating);

        // Повертаємо оновлену кількість лайків і дизлайків
        $response = [
            'like_count' => Models\PostRating::countLikes($postId),
            'dislike_count' => Models\PostRating::countDislikes($postId)
        ];
        echo json_encode($response);
    } else {
        throw new Exception("Недійсний запит.");
    }
} catch (Exception $e) {
     json_encode(['error' => $e->getMessage()]);
}