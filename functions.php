<?php
$is_auth = rand(0, 1);

$user_name = 'weissfl';

if (file_exists('config.php')) {
    require_once 'config.php';
} else {
    exit('Скопируйте config.default.php в config.php и установите настройки приложения');
}

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

function getData($sql)
{
    $link = DbConnectionProvider::getConnection();
    $result = mysqli_query($link, $sql);

    if ($result) {
        $result = mysqli_fetch_all($result, MYSQLI_ASSOC);
        if (is_null($result)) {
            exit(mysqli_error($link));
        }
    } else {
        exit(mysqli_error($link));
    }

    return $result;
}

function getCategories(): array
{
    $sql = "SELECT * FROM categories";
    $categories = getData($sql);

    return $categories;
}

function getFreshLots(): array
{
    $sql = "SELECT l.id, l.name, l.price AS start_price, l.img_url, MAX(b.price), c.NAME AS cat FROM lots AS l
    LEFT JOIN bets AS b ON l.id = b.lot_id
    LEFT JOIN categories AS c ON l.category_id = c.id
    WHERE NOW() < l.date_finish AND l.winner_id IS NULL
    GROUP BY l.id
    ORDER BY l.date DESC
    LIMIT 6;";
    $lots = getData($sql);

    return $lots;
}

function format_price(float $number): string
{
    $number = ceil($number);

    if ($number >= 1000) {
        $number = number_format($number, 0, '', ' ');
    }

    $number .= '<b class="rub">р</b>';

    return $number;
}

function warning_finishing($time_end)
{
    $time_diff = strtotime($time_end) - time();

    if ($time_diff <= 3600) {
        return true;
    } else {
        return false;
    }
}

function lifetime_lot($time_end)
{
    $time_diff = strtotime($time_end) - time();

    $hours = floor($time_diff / 3600);
    $minutes = floor(($time_diff % 3600) / 60);

    $time = $hours . ':' . $minutes;

    return $time;
}