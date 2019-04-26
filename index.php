<?php
require('helpers.php');
require('functions.php');

$is_auth = rand(0, 1);

$user_name = 'weissfl';

$connect = mysqli_connect("localhost", "root", "3d199xz", "yeticave");
mysqli_set_charset($connect, "utf8");

if ($connect === false) {
    exit("Ошибка подключения: " . mysqli_connect_error());
}
else {
    $sql_lots = "SELECT l.id, l.name, l.price AS start_price, l.img_url, MAX(b.price), c.NAME AS cat FROM lots AS l
    LEFT JOIN bets AS b ON l.id = b.lot_id
    LEFT JOIN categories AS c ON l.category_id = c.id
    WHERE NOW() < l.date_finish AND l.winner_id IS NULL
    GROUP BY l.id, c.NAME
    ORDER BY l.date DESC
    LIMIT 6;";
    $result_lots = mysqli_query($connect, $sql_lots);

    if ($result_lots) {
        $lots = mysqli_fetch_all($result_lots, MYSQLI_ASSOC);
        if(is_null($lots)){
            exit(mysqli_error($connect));
        }
    }
    else {
        exit(mysqli_error($connect));
    }

    $sql_categories = "SELECT * FROM categories;";
    $result_categories = mysqli_query($connect, $sql_categories);

    if ($result_lots) {
        $categories = mysqli_fetch_all($result_categories, MYSQLI_ASSOC);
        if(is_null($categories)){
            exit(mysqli_error($connect));
        }
    }
    else {
        exit(mysqli_error($connect));
    }
}

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

