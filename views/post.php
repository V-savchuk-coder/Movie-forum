<?php
session_start();
include_once '../config/db.php';
include_once '../models/Post.php';
include_once '../models/Comment.php';

$post = null;
$comments = [];

if (isset($_GET['id'])) {
    $postId = $_GET['id'];
    $post = Models\Post::getPostById($postId);  // Завантажуємо пост
    if ($post) {
        $comments = Models\Comment::getCommentsByPostId($postId);  // Завантажуємо коментарі, якщо пост знайдено
    } else {
        // Якщо пост не знайдений, можна вивести повідомлення або переадресувати
        echo "Post not found!";
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_comment']) && isset($post)) {
    $userId = $_SESSION['user_id'];
    $content = $_POST['content'];
    Models\Comment::addComment($postId, $userId, $content);
    header("Location: post.php?id=" . $postId);
    exit();
}
?>

<!-- HTML для відображення посту та коментарів -->
<?php if ($post): ?>
    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
    <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>

    <h3>Comments</h3>
    <?php if ($comments): ?>
        <?php foreach ($comments as $comment): ?>
            <p><strong><?php echo htmlspecialchars($comment['username']); ?>:</strong> <?php echo nl2br(htmlspecialchars($comment['content'])); ?></p>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No comments yet.</p>
    <?php endif; ?>

    <!-- Форма для додавання коментаря -->
    <form method="POST">
        <textarea name="content" placeholder="Add a comment" required></textarea>
        <button type="submit" name="submit_comment">Post Comment</button>
    </form>
<?php else: ?>
    <p>No post found with this ID.</p>
<?php endif; ?>