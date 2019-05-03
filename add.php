<?php
require('functions.php');

$categories = getCategories();

$page_content = include_template('add.php', ['categories' => $categories]);

$layout_content = include_template('layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'Добавление лота - Yeti Cave'
]);

echo $layout_content;
