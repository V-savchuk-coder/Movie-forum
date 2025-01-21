<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!-- Підключення до CSS для панелі навігації -->
<link rel="stylesheet" href="../public/css/sidebar.css">

<div class="sidebar">
    <a href="../views/profile.php" class="sidebar-item">
        <i class="fas fa-user"></i>
        <span>Профіль</span>
    </a>
    <a href="../views/home.php" class="sidebar-item">
        <i class="fas fa-comments"></i>
        <span>Пости форуму</span>
    </a>
    <form action="../views/logout.php" method="post" class="sidebar-item">
        <button type="submit" class="logout-button">
            <i class="fas fa-sign-out-alt"></i>
            <span>Вийти</span>
        </button>
    </form>
</div>

<!-- Підключення Font Awesome для іконок -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">