<?php
require_once('init.php');

$category_id = $_GET['id'] ?? '';
$current_category_name = '';
$pages = '';
$pages_count = 0;
$lots = [];
$page_current = $_GET['page'] ?? 1;
$page_items = 9;

if (!empty($category_id) && ($current_category_name = getNameCategory($category_id)) !== null) {

    $lots = getСategoryLots([$category_id, $page_items, 0]);

    $lots_count = getCountСategoryLots($category_id);
    $pages_count = ceil($lots_count / $page_items);

    $page_content = include_template('all-lots.php', [
        'categories' => $categories,
        'lots' => $lots,
        'current_category_name' => $current_category_name,
        'pages' => $pages
    ]);

    if ($pages_count > 1) {
        if ($page_current > $pages_count || $page_current <= 0) {
            http_response_code(404);
            $page_content = include_template('404.php');
        } else {
            $offset = ($page_current - 1) * $page_items;
            $lots = getСategoryLots([$category_id, $page_items, $offset]);
            $pages = range(1, $pages_count);

            $page_content = include_template('all-lots.php', [
                'categories' => $categories,
                'lots' => $lots,
                'current_category_name' => $current_category_name,
                'pages' => $pages,
                'page_current' => $page_current,
                'category_id' => $category_id
            ]);
        }
    }

} else {
    http_response_code(404);
    $page_content = include_template('404.php');
}

$layout_content = include_template('layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'title' => 'Yeti Cave - Все лоты в категории ' . ($current_category_name ?? '404 страница не найдена')
]);

echo $layout_content;