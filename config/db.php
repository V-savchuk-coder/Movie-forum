<?php
// Налаштування підключення
$host = 'localhost';  // Хост
$db   = 'movie_forum'; // Назва бази даних
$user = 'root';       // Ім'я користувача
$pass = 'Vlad54668987';  // Пароль
$charset = 'utf8mb4';

// Створення DSN
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
   
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

?>



