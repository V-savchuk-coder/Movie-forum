<?php
session_start();
include_once '../config/db.php';
include_once '../models/Post.php';
include_once '../models/Category.php';
include_once '../models/PostRating.php';
include_once '../navigation/sidebar.php';
include_once '../models/rate_post.php';


if (isset($_SESSION['user_id'])) {
    // Access $_SESSION['user_id']
    $user_id = $_SESSION['user_id'];
} else {
    // Handle the case where the key doesn't exist (maybe redirect to login)
    echo "User not logged in.";
}


$categories = Models\Category::getAllCategories();
$query = isset($_GET['query']) ? $_GET['query'] : '';
$categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : null;
$posts = Models\Post::searchPosts($query, $categoryId);
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;


?>

<link rel="stylesheet" href="../public/css/home.css">
<div class="posts-container">
    <!-- Кнопка для переходу на всі пости -->
    <div class="view-all-posts-button-container">
        <a href="home.php" class="view-all-posts-button">Усі пости</a>
    </div>
    
    <h1 class="posts">Пости </h1>   

    <!-- Форма пошуку та фільтрації постів -->
    <div class="search-container">
        <form method="GET" action="home.php">
            <input type="text" name="query" value="<?php echo htmlspecialchars($query); ?>" placeholder="Search posts" required>
            <select name="category_id">
                <option value="">All Categories</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>" <?php echo ($category['id'] == $categoryId) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit">Search</button>
        </form>
    </div>

    <!-- Пости -->
    <?php if ($posts): ?>
    <?php foreach ($posts as $post): ?>
        <div class="post" id="post-<?php echo $post['id']; ?>">
            <h2><a href="single_post.php?id=<?php echo $post['id']; ?>"><?php echo htmlspecialchars($post['title']); ?></a></h2>
            <p><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
            <p><strong>Категорії:</strong> <?php echo htmlspecialchars($post['category_name']); ?></p>
            <p><strong>Створено в:</strong> <?php echo $post['created_at']; ?></p>
            <!-- Інформація про автора -->
            <p><strong>Автор:</strong> <?php echo htmlspecialchars($post['username']); ?></p>





            <!-- Лайки та дизлайки -->
            <button class="like-btn" onclick="ratePost(<?php echo $post['id']; ?>, <?php echo $userId; ?>, 1)">👍</button>
            <span id="like-count-<?php echo $post['id']; ?>"><?php echo Models\PostRating::countLikes($post['id']); ?></span>

            <button class="dislike-btn" onclick="ratePost(<?php echo $post['id']; ?>, <?php echo $userId; ?>, -1)">👎</button>
            <span id="dislike-count-<?php echo $post['id']; ?>"><?php echo Models\PostRating::countDislikes($post['id']); ?></span>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p class="no-posts">No posts found.</p>
<?php endif; ?>

    <?php if ($userId): ?>
        <div class="create-post-button-container">
            <a href="create_posts.php" class="create-post-button">Create Post</a>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript для AJAX-запитів -->
<script>
function ratePost(postId, userId, rating) {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "../models/rate_post.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4 && xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            document.getElementById('like-count-' + postId).innerText = response.like_count;
            document.getElementById('dislike-count-' + postId).innerText = response.dislike_count;
        }
    };
    xhr.send("post_id=" + postId + "&user_id=" + userId + "&rating=" + rating);
}
</script>