<?php
require('helpers.php');

$is_auth = rand(0, 1);

$user_name = 'weissfl';

//Подключение информации о седенении с БД
if (file_exists('config.php')) {
    require_once 'config.php';
} else {
    exit('Скопируйте config.default.php в config.php и установите настройки приложения');
}

//Ресурс соединения
class DbConnectionProvider
{
    protected static $connection;

    public static function getConnection()
    {
        if (self::$connection === null) {
            self::$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
            if (!self::$connection) {
                exit('Ошибка MySQL: connection failed');
            }

            mysqli_set_charset(self::$connection, 'utf8');
        }

        return self::$connection;
    }
}

//Выполняет запрос на получние данных
function getData($sql, $data = [])
{
    $link = DbConnectionProvider::getConnection();

    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result === false) {
        exit('Ошибка при попытке получить результат prepared statement: ' . mysqli_stmt_error($stmt));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

//Выполняет запрос на добавлнеие записи
function insertData($sql, $data = [])
{
    $link = DbConnectionProvider::getConnection();

    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_errno($link) !== 0) {
        exit('Ошибка получния разультата из подготовленного выражения: ' . mysqli_errno($link));
    }

    return $result;
}

//Получает категории
function getCategories(): array
{
    $sql = "SELECT * FROM categories";
    $categories = getData($sql);

    return $categories;
}

//Получает все лоты
function getFreshLots(): array
{
    $sql = "SELECT l.id, l.name, l.price AS start_price, l.img_url, MAX(b.price) AS last_price, c.NAME AS cat, l.date, l.date_finish FROM lots AS l
    LEFT JOIN bets AS b ON l.id = b.lot_id
    LEFT JOIN categories AS c ON l.category_id = c.id
    WHERE NOW() < l.date_finish AND l.winner_id IS NULL
    GROUP BY l.id
    ORDER BY l.date DESC";
    $lots = getData($sql);

    return $lots;
}

//Получает данные для страницы лота
function getLot(int $id): ?array
{
    $sql = "SELECT l.id, l.date, l.name, l.description, l.img_url, l.price, l.date_finish, l.step, l.user_id, l.winner_id, l.category_id, c.name AS cat, MAX(b.price) AS last_price FROM lots AS l
    JOIN categories AS c ON l.category_id = c.id
    LEFT JOIN bets AS b ON l.id = b.lot_id
    WHERE l.id = ?
    GROUP BY l.id";

    $row = getData($sql, [$id]);

    return $row[0] ?? null;
}

//Проверяет существование e-mail в базе
function checkEmail($email)
{
    $sql = "SELECT email FROM users WHERE email=?";

    return getData($sql, [$email]) !== [];
}

//Проверяет соответствие введённого пароля
function checkPassword($email, $password)
{
    $sql = "SELECT password FROM users WHERE email=?";

    $password_hash = getData($sql, [$email]);

    if (empty($password_hash[0]['password'])) {
        return false;
    }

    return password_verify($password, $password_hash[0]['password']);
}

//Возвращает массив данных пользователя по его e-mal
function getUser($email): array
{
    $sql = "SELECT id, date_reg, email, name, avatar_url, contacts FROM users WHERE email=?";

    $user = getData($sql, [$email]);

    return $user[0] ?? null;
}

//Возвращает информацию о ставке по ip пользователя
function getBets($user_id)
{
    $sql = "SELECT b.date AS bet_date, b.price AS bet_cost, l.id AS lot_id, l.name AS lot_name, l.img_url AS lot_img, l.date_finish AS lot_date_finish, l.winner_id AS lot_winner, c.name AS cat_name, u.contacts AS user_contacts
    FROM bets AS b
    LEFT JOIN lots AS l ON l.id = b.lot_id
    LEFT JOIN categories AS c ON c.id = l.category_id
    LEFT JOIN users AS u ON u.id = l.user_id
    WHERE b.user_id = ?
    ORDER BY b.date DESC";

    return getData($sql, [$user_id]);
}

//Добавление лота
function insertLot($data)
{
    $sql = "INSERT INTO lots (name, category_id, description, price, step, date_finish, user_id, img_url, date)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    return insertData($sql, $data);
}

//Добавление пользователя
function insertUser($data)
{
    $sql = "INSERT INTO users (email, password, name, contacts, avatar_url, date_reg)
    VALUES (?, ?, ?, ?, ?, NOW())";

    return insertData($sql, $data);
}

//Добавление ставку
function insertBet($data)
{
    $sql = "INSERT INTO bets (price, user_id, lot_id, date)
    VALUES (?, ?, ?, NOW())";

    return insertData($sql, $data);
}

//Возвращает текущую цену
function currentPrice($price, $last_bet)
{
    return $last_bet ?? $price;
}

//Возвращает минимальную ставку
function minBet($current_price, $step)
{
    return $current_price + $step;
}

//Форматирует цену
function format_price(float $number): string
{
    $number = ceil($number);

    if ($number >= 1000) {
        $number = number_format($number, 0, '', ' ');
    }

    $number .= '<b class="rub">р</b>';

    return $number;
}

//Получает маркет если до истечения лота меньше 1 часа
function warning_finishing($time_end)
{
    $time_diff = strtotime($time_end) - time();

    return $time_diff <= 3600;
}

//Получает оставшееся время жизни лота
function lifetime_lot($time_end)
{
    $time_difference = strtotime($time_end) - time();

    $hours = floor($time_difference / 3600);
    $minutes = floor(($time_difference % 3600) / 60);

    if ($minutes < 10) {
        $minutes = '0' . $minutes;
    }

    return $hours . ':' . $minutes;
}

//Возвращает время создания ставки
function createdTimeAgo($created_time)
{
    $time = '';

    $time_difference = time() - strtotime($created_time);

    $hours = floor($time_difference / 3600);
    $minutes = floor(($time_difference % 3600) / 60);

    $minutes_plural = get_noun_plural_form(
        $minutes,
        'минута',
        'минуты',
        'минут'
    );

    $hours_plural = get_noun_plural_form(
        $hours,
        'час',
        'часа',
        'часов'
    );

    if ($time_difference < 60) {
        $time = 'Только что';
    } elseif ($time_difference < 3600) {
        $time = $minutes . ' ' . $minutes_plural . ' назад';
    } elseif ($time_difference < 7200) {
        $time = 'Час ' . (($minutes != 0) ? ($minutes . ' ' . $minutes_plural) : '') . ' назад';
    } elseif ($time_difference >= 7200 && $time_difference < 86400) {
        $time = $hours . ' ' . $hours_plural . ' ' . $minutes . ' ' . $minutes_plural . ' назад';
    } elseif ($time_difference < 172800) {
        $time = 'Вчера, в ' . date('H:i', strtotime($created_time));
    } elseif ($time_difference < 259200) {
        $time = 'Позавчера, в ' . date('H:i', strtotime($created_time));
    } else {
        $time = date('d.m.y', strtotime($created_time)) . ' в ' . date('H:i', strtotime($created_time));
    }

    return $time;
}

//Определяет что введенная дата больше текущей на сутки
function validDate($time)
{
    return (strtotime($time) - time()) > 86400;
}
