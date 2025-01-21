<?php

// controllers/PostController.php
include_once '../models/Post.php';

class PostController {
    public function showPosts() {
        $query = $_GET['query'] ?? null;
        $categoryId = $_GET['category_id'] ?? null;

        if ($query) {
            $posts = Models\Post::searchPosts($query, $categoryId);
        } else {
            $posts = Models\Post::getPosts();
        }

        include '../views/home.php';
    }
}
?>