<?php
require_once('init.php');

$category_id = '';
$search_results = '';
$pages = '';
$page_current = $_GET['page'] ?? 1;
$page_items = 3;

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['search'])) {
    $category_id = trim($_GET['search']);
    $items_count = countSearchLots($category_id);

    $page_content = include_template('search.php', [
        'categories' => $categories,
        'keyword' => $category_id,
        'search_results' => $search_results
    ]);

    if ($items_count > 0) {
        $pages_count = ceil($items_count / $page_items);

        if ($page_current > $pages_count || $page_current <= 0) {
            http_response_code(404);
            $page_content = include_template('404.php');
        } else {
            $offset = ($page_current - 1) * $page_items;
            $pages = range(1, $pages_count);

            $search_results = searchLots([$category_id, $page_items, $offset]);

            $page_content = include_template('search.php', [
                'categories' => $categories,
                'keyword' => $category_id,
                'search_results' => $search_results,
                'pages' => $pages,
                'page_current' => $page_current
            ]);
        }
    }

} else {
    $page_content = include_template('search.php', [
        'categories' => $categories,
        'keyword' => $category_id,
        'search_results' => $search_results,
        'pages' => $pages,
        'page_current' => $page_current
    ]);
}

$layout_content = include_template('layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'keyword' => $category_id,
    'title' => 'Результаты поиска - Yeti Cave'
]);

echo $layout_content;