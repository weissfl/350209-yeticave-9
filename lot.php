<?php
require('functions.php');

$categories = getCategories();
$lot = getPage($_GET['id']);

if($lot !== NULL) {
    $page_content = include_template('lot.php', ['lot' => $lot]);
}
else {
    http_response_code(404);

    $page_content = include_template('404.php', []);
}

$layout_content = include_template('layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => ((isset($lot['name'])) ? $lot['name'] : '404 страница не найдена') . ' - Yeti Cave'
]);

echo $layout_content;