<?php
require('helpers.php');
require('functions.php');

$categories = getCategories();

$page_content = include_template('lot.php', []);

$layout_content = include_template('layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'Yeti Cave - Главная страница'
]);

echo $layout_content;