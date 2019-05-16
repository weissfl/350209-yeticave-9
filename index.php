<?php
require_once('init.php');

$lots = getFreshLots();

$categories = getCategories();

$page_content = include_template('index.php', [
    'categories' => $categories,
    'ads' => $lots
]);

$layout_content = include_template('layout_index.php', [
    'categories' => $categories,
    'content' => $page_content,
    'title' => 'Yeti Cave - Главная страница'
]);

echo $layout_content;

