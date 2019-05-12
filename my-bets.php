<?php
session_start();
require('functions.php');

$categories = getCategories();

$bets = getBets($_SESSION['user']['id']);

$page_content = include_template('my-bets.php', [
    'categories' => $categories,
    'bets' => $bets
]);

$layout_content = include_template('layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'title' => 'Мои ставки - Yeti Cave'
]);

echo $layout_content;
