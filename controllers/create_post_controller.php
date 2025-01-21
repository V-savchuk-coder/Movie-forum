<?php

include_once '../config/db.php';
include_once '../models/Post.php';
include_once '../admin/sidebar.php';
include_once '../controllers/create_post_controller.php';

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