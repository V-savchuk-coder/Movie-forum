<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Підключення до бази даних
include 'C:/xampp/htdocs/movie_forum/config/db.php';

// Отримання даних користувача
$stmt = $pdo->prepare("SELECT username, email, password, avatar FROM users WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Видалення поста користувача
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_post'])) {
    $post_id = $_POST['post_id'];

    // Видалення записів у `post_ratings`, пов'язаних із постом
    $stmt_delete_ratings = $pdo->prepare("DELETE FROM post_ratings WHERE post_id = :post_id");
    $stmt_delete_ratings->execute(['post_id' => $post_id]);

    // Видалення записів у `comments`, пов'язаних із постом
    $stmt_delete_comments = $pdo->prepare("DELETE FROM comments WHERE post_id = :post_id");
    $stmt_delete_comments->execute(['post_id' => $post_id]);

    // Видалення самого поста з бази даних
    $stmt_delete_post = $pdo->prepare("DELETE FROM posts WHERE id = :post_id AND user_id = :user_id");
    $stmt_delete_post->execute(['post_id' => $post_id, 'user_id' => $_SESSION['user_id']]);

    echo "<p>Пост видалено!</p>";
}

// Оновлення тексту поста
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_post'])) {
    $post_id = $_POST['post_id'];
    $new_content = $_POST['post_text'];
    
    $stmt_update_post = $pdo->prepare("UPDATE posts SET content = :content WHERE id = :post_id AND user_id = :user_id");
    $stmt_update_post->execute(['content' => $new_content, 'post_id' => $post_id, 'user_id' => $_SESSION['user_id']]);
    
    echo "<p>Пост оновлено!</p>";
}

// Отримання постів користувача
$stmt_posts = $pdo->prepare("SELECT * FROM posts WHERE user_id = :user_id ORDER BY created_at DESC");
$stmt_posts->execute(['user_id' => $_SESSION['user_id']]);
$posts = $stmt_posts->fetchAll(PDO::FETCH_ASSOC);

// Обробка форми для редагування профілю
// Обробка форми для редагування профілю
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $avatar = null;

    // Перевірка, чи був завантажений файл аватарки
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
        $avatar_tmp = $_FILES['avatar']['tmp_name'];
        $avatar_name = basename($_FILES['avatar']['name']);
        $avatar_path = 'uploads/' . $avatar_name;

        // Перевірка, чи існує файл з таким ім'ям
        if (file_exists($avatar_path)) {
            // Якщо файл вже існує, генеруємо нове ім'я
            $avatar_name = uniqid() . '-' . $avatar_name;
            $avatar_path = 'uploads/' . $avatar_name;
        }

        // Перевірка типу файлу
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (in_array($_FILES['avatar']['type'], $allowed_types)) {
            move_uploaded_file($avatar_tmp, $avatar_path);
            $avatar = $avatar_path; // Зберігаємо шлях до файлу аватарки
        } else {
            echo "<p>Невірний тип файлу для аватарки. Дозволені лише зображення JPEG, PNG або GIF.</p>";
        }
    } else {
        // Якщо аватарка не була завантажена, залишаємо попередній
        $avatar = $user['avatar'];
    }

    // Оновлення інформації про користувача
    $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email, avatar = :avatar WHERE id = :id");
    $stmt->execute(['username' => $username, 'email' => $email, 'avatar' => $avatar, 'id' => $_SESSION['user_id']]);

    echo "<p>Профіль оновлено!</p>";
}

// Обробка форми для зміни пароля
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Перевірка поточного пароля
    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            // Оновлення пароля в базі даних
            $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->execute(['password' => $hashed_password, 'id' => $_SESSION['user_id']]);

            echo "<p>Пароль змінено!</p>";
        } else {
            echo "<p>Нові паролі не збігаються!</p>";
        }
    } else {
        echo "<p>Неправильний поточний пароль!</p>";
    }
}
?>

<!-- Підключення sidebar -->
<?php include '../navigation/sidebar.php'; ?>

<!-- HTML для відображення профілю -->
<link rel="stylesheet" href="../public/css/profile.css"> <!-- Підключення стилю -->

<div class="profile-container content">
    <h2 class="profile-heading">Профіль користувача <?= htmlspecialchars($user['username']) ?> </h2>

    <!-- Навігація по вкладках -->
    <div class="tabs">
        <button class="tab-button" id="profile-tab">Основна інформація</button>
        <button class="tab-button" id="update-tab">Оновити профіль</button>
        <button class="tab-button" id="password-tab">Змінити пароль</button>
    </div>

    <!-- Контент для вкладок -->
    <div id="profile-info" class="tab-content">
        <h3>Інформація про користувача</h3>
        <p>Ім'я користувача: <?= htmlspecialchars($user['username']) ?></p>
        <p>Email: <?= htmlspecialchars($user['email']) ?></p>

        <h3>Пости користувача</h3>
        <!-- Виведення постів користувача -->
        <?php if ($posts): ?>
    <ul>
        <?php foreach ($posts as $post): ?>
            <li>
                <a href="single_post.php?id=<?= $post['id']; ?>"><?= htmlspecialchars($post['title']); ?></a>
                <span><?= $post['created_at']; ?></span>

                <!-- Контейнер для кнопок -->
                <div class="post-buttons">
                     <!-- Форма для видалення поста -->
                    <form method="post" style="display: inline;">
                     <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
                    <button type="submit" name="delete_post" onclick="return confirm('Ви впевнені, що хочете видалити цей пост?');" class="post-button delete">Видалити</button>
                    </form>

                     <!-- Кнопка для редагування поста -->
                     <button onclick="toggleEditForm(<?= $post['id']; ?>)" class="post-button edit">Змінити</button>
                </div>

                <!-- Форма для редагування поста -->
                <form method="post" id="edit-form-<?= $post['id']; ?>" style="display: none;">
                 <input type="hidden" name="post_id" value="<?= $post['id']; ?>">
                 <textarea name="post_text"><?= htmlspecialchars($post['content']); ?></textarea>
                <button type="submit" name="update_post">Оновити пост</button>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
            <p>У вас немає постів.</p>
<?php endif; ?>
</div>

    <div id="update-profile" class="tab-content">
        <h3>Оновити профіль</h3>
        <form method="post" enctype="multipart/form-data">
            <label>Ім'я користувача:</label>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>

            <label>Електронна пошта:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <!-- Поле для завантаження аватарки -->
            <label>Аватарка:</label>
            <input type="file" name="avatar">

            <button type="submit" name="update_profile">Оновити профіль</button>
        </form>
    </div>
    <div class="avatar-container">
    <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="Avatar" class="avatar">
    </div>

    <div id="change-password" class="tab-content">
        <h3>Змінити пароль</h3>
        <form method="post">
            <label>Поточний пароль:</label>
            <input type="password" name="current_password" required>

            <label>Новий пароль:</label>
            <input type="password" name="new_password" required>

            <label>Підтвердження нового пароля:</label>
            <input type="password" name="confirm_password" required>

            <button type="submit" name="change_password">Змінити пароль</button>
        </form>
    </div>
</div>

<script>
// JavaScript для вкладок
document.getElementById('profile-tab').addEventListener('click', function() {
    showTab('profile-info');
});
document.getElementById('update-tab').addEventListener('click', function() {
    showTab('update-profile');
});
document.getElementById('password-tab').addEventListener('click', function() {
    showTab('change-password');
});

function showTab(tabId) {
    const tabs = document.querySelectorAll('.tab-content');
    tabs.forEach(tab => {
        if (tab.id === tabId) {
            tab.style.display = 'block';
        } else {
            tab.style.display = 'none';
        }
    });
}

// За умовчанням відображати вкладку "Основна інформація"
showTab('profile-info');

// Показ/приховування форми редагування поста
function toggleEditForm(postId) {
    const form = document.getElementById(`edit-form-${postId}`);
    form.style.display = form.style.display === 'none' ? 'block' : 'none';
}
</script>