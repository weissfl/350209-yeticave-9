<?php
require('helpers.php');
require('functions.php');

$is_auth = rand(0, 1);

$user_name = 'weissfl';

$con = mysqli_connect("localhost", "root", "3d199xz", "yeticave");
mysqli_set_charset($con, "utf8");

if ($con == false) {
    print("Ошибка подключения: " . mysqli_connect_error());
}
else {
    $sql_lots = "SELECT l.name, l.price AS start_price, l.img_url, b.price, c.NAME AS cat FROM lots AS l
    LEFT JOIN bets AS b ON l.id = b.lot_id
    LEFT JOIN categories AS c ON l.category_id = c.id
    WHERE NOW() < l.date_finish AND l.winner_id IS NULL
    ORDER BY l.date DESC LIMIT 6;";
    $res_lots = mysqli_query($con, $sql_lots);
    $rows_lots = mysqli_fetch_all($res_lots, MYSQLI_ASSOC);

    $sql_cat = "SELECT * FROM categories;";
    $res_cat = mysqli_query($con, $sql_cat);
    $rows_cat = mysqli_fetch_all($res_cat, MYSQLI_ASSOC);
}

$page_content = include_template('index.php', [
    'categories' => $rows_cat,
    'ads' => $rows_lots
]);

$layout_content = include_template('layout.php', [
    'categories' => $rows_cat,
    'content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'Yeti Cave - Главная страница'
]);

echo $layout_content;

