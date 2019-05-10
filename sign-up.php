<?php
require('functions.php');

$categories = getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $required_fields = [
        'email' => 'e-mail',
        'password' => 'пароль',
        'name' => 'имя',
        'message' => 'контактные данные'
    ];

    $errors = [];
    $sign_up = $_POST;

    foreach ($required_fields as $field => $name) {
        if (empty($sign_up[$field])) {
            $errors[$field] = 'Пожалуйста заполните поле ' . $name;
        }
    }

    foreach ($sign_up as $field => $value) {

        if ($field === "email" && ! empty($value)) {
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                $errors[$field] = 'E-mail должен быть корректным';
            }
            elseif (checkEmail($value)){
                $errors[$field] = 'Учетная запись с таким E-mail уже существет. Введите другой E-mail';
            }
        }
    }

    if (! empty($_FILES['avatar']['name']) && ! empty($_FILES['avatar']['tmp_name'])) {
        $tmp_path = $_FILES['avatar']['tmp_name'];
        $file_expansion = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $name = uniqid() . '.' . $file_expansion;

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_type = finfo_file($finfo, $tmp_path);

        if(! ($file_type == "image/png" || $file_type == "image/jpeg")){
            $errors['avatar'] = "Изображение должно быть в формате png или jpeg";
        }
        elseif(count($errors) === 0) {
            move_uploaded_file($tmp_path, 'uploads/' . $name);
            $sign_up['avatar'] = 'uploads/' . $name;
        }
    }
    else {
        $sign_up['avatar'] = NULL;
    }

    if(isset($sign_up['password'])) {
        $sign_up['password'] = password_hash($sign_up['password'], PASSWORD_DEFAULT);
    }

    if (count($errors) > 0) {
        $page_content = include_template('sign-up.php', [
            'categories' => $categories,
            'errors' => $errors,
            'sign_up' => $sign_up
        ]);
    }
    else {
        insertUser([$sign_up['email'], $sign_up['password'], $sign_up['name'], $sign_up['message'], $sign_up['avatar']]);
        header("Location: login.php");
    }
}
else {
    $page_content = include_template('sign-up.php', ['categories' => $categories]);
}

$layout_content = include_template('layout.php', [
    'categories' => $categories,
    'content' => $page_content,
    'is_auth' => $is_auth,
    'user_name' => $user_name,
    'title' => 'Регистрация нового аккаунта - Yeti Cave'
]);

echo $layout_content;