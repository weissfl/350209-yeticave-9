<?php
require('functions.php');

$lots = getFreshLots();

$categories = getCategories();

$page_content = include_template('index.php', [
    'categories' => $categories,
    'ads' => $lots
]);

$layout_content = include_template('layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'Yeti Cave - Главная страница'
]);

echo $layout_content;

