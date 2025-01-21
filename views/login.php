<?php  ?>

<link rel="stylesheet" href="../public/css/style.css"> <!-- Підключення стилю -->

<h2>Вхід</h2>
<form action="login.php" method="post">
    <label for="username">Ім'я користувача:</label>
    <input type="text" id="username" name="username" required>

    <label for="password">Пароль:</label>
    <input type="password" id="password" name="password" required>

    <button type="submit">Увійти</button>
</form>



<?php
include 'C:\xampp\htdocs\movie_forum\config\db.php'; // Підключення до бази даних


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username']; // Додаємо ім'я користувача до сесії
        header("Location: ../views/home.php");
        exit;
    } else {
        echo "<p>Невірне ім'я користувача або пароль. <a href='register.php'>Зареєструйтесь тут</a></p>";
    }
}
?>