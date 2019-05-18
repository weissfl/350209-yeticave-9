<?php
require_once('init.php');

if (isset($_GET['id']) && !empty($_GET['id']) && ($lot = getLot($_GET['id']))) {
    $page_content = include_template('lot.php', ['lot' => $lot]);
} else {
    http_response_code(404);
    $page_content = include_template('404.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $errors = [];
    $bet = $_POST;
    $current_price = currentPrice($lot['price'], $lot['last_price']);
    $min_bet = minBet($current_price, $lot['step']);

    if (!isset($_SESSION['user'])) {
        http_response_code(403);
        exit();
    }

    if (empty($bet['cost'])) {
        $errors['cost'] = 'Введите вашу ставку';
    } elseif (!is_numeric($bet['cost'])) {
        $errors['cost'] = 'Cтавка должно быть числом';
    } elseif ($bet['cost'] < 0) {
        $errors['cost'] = 'Cтавка должно быть больше нуля';
    } elseif (!is_int($bet['cost'] + 0)) {
        $errors['cost'] = 'Cтавка должно быть целым числом';
    } elseif ($min_bet > $bet['cost']) {
        $errors['cost'] = 'Ваша cтавка должно быть больше чем минимальная ставка';
    }

    if (count($errors) > 0) {
        $page_content = include_template('lot.php', [
            'errors' => $errors,
            'lot' => $lot
        ]);
    } else {
        insertBet([$bet['cost'], $_SESSION['user']['id'], $_GET['id']]);

        $lot = getLot($_GET['id']);
        $page_content = include_template('lot.php', ['lot' => $lot]);
    }
}

$layout_content = include_template('layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'title' => ($lot['name'] ?? '404 страница не найдена') . ' - Yeti Cave'
]);

echo $layout_content;
