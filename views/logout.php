<?php
session_start();
session_unset(); // Очищуємо сесію
session_destroy(); // Знищуємо сесію
header("Location: http://localhost/movie_forum/views/login.php"); // Перенаправляємо на сторінку входу
exit;
?>