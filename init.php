<?php
session_start();

//Подключение информации о седенении с БД
if (file_exists('config.php')) {
    require_once 'config.php';
} else {
    exit('Скопируйте config.default.php в config.php и установите настройки приложения');
}

require_once('vendor/autoload.php');
require_once('helpers.php');
require_once('functions.php');

$categories = getCategories();