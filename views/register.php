

<link rel="stylesheet" href="../public/css/style.css"> <!-- Підключаємо стиль -->

<h2>Реєстрація</h2>
<form action="register.php" method="post">
    <label for="username">Ім'я користувача:</label>
    <input type="text" id="username" name="username" required>

    <label for="email">Електронна пошта:</label>
    <input type="email" id="email" name="email" required>

    <label for="password">Пароль:</label>
    <input type="password" id="password" name="password" required>

    <label for="confirm_password">Підтвердження пароля:</label>
    <input type="password" id="confirm_password" name="confirm_password" required>

    <button type="submit">Зареєструватися</button>
</form>


<?php
include 'C:\xampp\htdocs\movie_forum\config\db.php'; // Підключення до бази даних

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        echo "<p>Паролі не збігаються!</p>";
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->rowCount() > 0) {
            echo "<p>Ім'я користувача або електронна пошта вже зайняті!</p>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
            $stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashed_password]);

            header("Location: login.php");
            exit;
        }
    }
}
?>
