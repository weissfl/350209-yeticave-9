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

//Выполняет запрос
function getData($sql, $data = [])
{
    $link = DbConnectionProvider::getConnection();

    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if($result === false) {
        exit('Ошибка при попытке получить результат prepared statement: ' . mysqli_stmt_error($stmt));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
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

//Возвращает текущую цену
function currentPrice($price, $last_bet)
{
    if(! empty($last_bet)) {
        $current_price = $last_bet;
    }
    else {
        $current_price = $price;
    }

    return $current_price;
}

//Возвращает минимальную ставку
function minBet($current_price, $step) {
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

//Получает оставшееся время жизни лота в минутах
function lifetime_lot($time_end)
{
    $time_diff = strtotime($time_end) - time();

    $hours = floor($time_diff / 3600);
    $minutes = floor(($time_diff % 3600) / 60);

    $time = $hours . ':' . $minutes;

    return $time;
}
