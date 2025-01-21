<?php
session_start();
include_once '../config/db.php';
include_once '../models/Post.php';
include_once '../models/Comment.php';

include_once '../navigation/sidebar.php';

// Якщо є параметр id в URL, завантажуємо пост
if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    $post = Models\Post::getPostById($postId);
    if ($post) {
        $comments = Models\Comment::getCommentsByPostId($postId);
    } else {
        echo "Post not found!";
        exit();
    }
} else {
    echo "No post specified!";
    exit();
}

// Якщо форма з коментарем відправлена
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment'])) {
    $userId = $_SESSION['user_id'];
    $content = $_POST['content'];
    Models\Comment::addComment($postId, $userId, $content);
    header("Location: single_post.php?id=" . $postId);
    exit();
}
?>

<!DOCTYPE html>
<html lang="uk">
<a href="home.php" class="all-posts-button">Усі пости</a>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="../public/css/single_post.css"> <!-- Підключення стилів -->

</head>
<body>

<div class="post-container">

    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <p class="post-category"><strong>Категорія:</strong> <?php echo htmlspecialchars($post['category_name']); ?></p>
    <p class="post-content"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

</div>

<div class="comments-section">
    <h3>Коментарі</h3>
    <?php if ($comments): ?>
        <?php foreach ($comments as $comment): ?>
            <div class="comment">
                <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-comments">Наразі немає коментарів.</p>
    <?php endif; ?>
</div>

<div class="comment-form">
    <form method="POST">
        <textarea name="content" placeholder="Додати коментар" required></textarea>
        <button type="submit" name="submit_comment">Додати коментар</button>
    </form>
</div>

</body>
</html>
