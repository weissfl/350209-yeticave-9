<?php
session_start();
require('functions.php');

$categories = getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $required_fields = [
        'email' => 'e-mail',
        'password' => 'пароль'
    ];

    $errors = [];
    $login = $_POST;

    foreach ($required_fields as $field => $name) {
        if (empty($login[$field])) {
            $errors[$field] = 'Пожалуйста заполните поле ' . $name;
        }
    }

    if (count($errors) === 0) {
        $check_email = checkEmail($login['email']);

        if ($check_email) {
            $check_password = checkPassword($login['email'], $login['password']);
        }

        if ($check_email && $check_password) {
            $_SESSION['user'] = getUser($login['email']);
            header("Location: /");
            exit();
        } else {
            $errors['form'] = 'Вы ввели неверный email/пароль';
        }
    }

    if (count($errors) > 0) {
        $page_content = include_template('login.php', [
            'categories' => $categories,
            'errors' => $errors,
            'sign_up' => $login
        ]);
    }
} else {
    $page_content = include_template('login.php', ['categories' => $categories]);
}

$layout_content = include_template('layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'Вход - Yeti Cave'
]);

echo $layout_content;