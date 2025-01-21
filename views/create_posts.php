<?php
session_start();
include_once '../config/db.php';
include_once '../models/Post.php';
include_once '../navigation/sidebar.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_post'])) {
    $userId = $_SESSION['user_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];
    $categoryId = $_POST['category_id'];

    Models\Post::createPost($userId, $title, $content, $categoryId);
    header("Location: home.php");
    exit();
}
?>
<link rel="stylesheet" href="../public/css/create_post.css"> <!-- Підключення стилю -->
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Створити Пост</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

 

    <div class="posts-container">
        <h1>Створити новий пост</h1>

        <form method="POST" class="create-post-form">
            <div class="form-group">
                <label for="title">Заголовок:</label>
                <input type="text" name="title" id="title" placeholder="Введіть заголовок" required>
            </div>
            <div class="form-group">
                <label for="content">Контент:</label>
                <textarea name="content" id="content" placeholder="Введіть контент" rows="5" required></textarea>
            </div>
            <div class="form-group">
                <label for="category_id">Категорія:</label>
                <select name="category_id" id="category_id" required>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM categories");
                    while ($category = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='{$category['id']}'>{$category['name']}</option>";
                    }
                    ?>
                </select>
            </div>
            <button type="submit" name="submit_post" class="submit-button">Опублікувати</button>
            
            
            <div class="backbutton">
            <a href="../views/home.php" class="back-button">Назад</a>
            </div>

        </form>
    </div>

</body>
</html>