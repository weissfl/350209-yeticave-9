<?php
require_once('init.php');

if (!isset($_SESSION['user'])) {
    http_response_code(403);
    exit();
}

$page_content = include_template('add.php', ['categories' => $categories]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $required_fields = [
        'lot-name' => 'наименование',
        'category' => 'категория',
        'message' => 'описание',
        'lot-rate' => 'начальная цена',
        'lot-step' => 'шаг ставки',
        'lot-date' => 'дата окончания торгов',
    ];

    $errors = [];
    $lot = $_POST;

    foreach ($required_fields as $field => $name) {
        if (empty($lot[$field])) {
            $errors[$field] = 'Пожалуйста заполните поле ' . $name;
        }
    }

    foreach ($lot as $field => $value) {

        if ($field === "lot-rate" && !is_numeric($value) && !empty($value)) {
            $errors[$field] = 'Поле начальная цена должно быть числом';
        }

        if ($field === "lot-rate" && $value < 0) {
            $errors[$field] = 'Поле начальная цена должно быть числом больше нуля';
        }

        if ($field === "lot-date" && !empty($value)) {
            if (!validDate($value)) {
                $errors[$field] = 'Указанная дата должна быть больше текущей даты, хотя бы на один день';
            }

            if (!is_date_valid($value)) {
                $errors[$field] = 'Поле дата завершения должно быть в формате ГГГГ-ММ-ДД';
            }
        }

        if ($field === "lot-step" && !is_numeric($value) && !empty($value)) {
            $errors[$field] = 'Поле шаг ставки должно быть числом';
        }

        if ($field === "lot-step" && !empty($value) && is_numeric($value)) {
            if ($value < 0) {
                $errors[$field] = 'Поля шаг ставки должно быть больше ноля';
            }

            if (!is_int($value + 0)) {
                $errors[$field] = 'Поля шаг ставки должно быть целым числом';
            }
        }

    }

    if (!empty($_FILES['lot-img']['name']) || !empty($_FILES['lot-img']['tmp_name']) || $_FILES['lot-img']['error'] == UPLOAD_ERR_OK) {
        $tmp_path = $_FILES['lot-img']['tmp_name'];
        $file_expansion = pathinfo($_FILES['lot-img']['name'], PATHINFO_EXTENSION);
        $name = uniqid() . '.' . $file_expansion;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_path);

        if (!($file_type === "image/png" || $file_type === "image/jpeg")) {
            $errors['lot-img'] = "Изображение должно быть в формате png или jpeg";
        } elseif (count($errors) === 0) {
            move_uploaded_file($tmp_path, 'uploads/' . $name);
            $lot['lot-img'] = 'uploads/' . $name;
        }
    } else {
        $errors['lot-img'] = "Вы не загрузили файл";
    }

    if (count($errors) > 0) {
        $page_content = include_template('add.php', [
            'categories' => $categories,
            'errors' => $errors,
            'lot' => $lot
        ]);
    } else {
        $id = insertLot([
            $lot['lot-name'],
            $lot['category'],
            $lot['message'],
            $lot['lot-rate'],
            $lot['lot-step'],
            $lot['lot-date'],
            $_SESSION['user']['id'],
            $lot['lot-img']
        ]);
        header("Location: lot.php?id=" . $id);
    }
}

$layout_content = include_template('layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'title' => 'Добавление лота - Yeti Cave'
]);

echo $layout_content;
