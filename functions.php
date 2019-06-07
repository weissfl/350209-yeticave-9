<?php

/**
 * Получает ресурс соединения с БД
 * @return ссылка на ресурс соединения
 */
class DbConnectionProvider {
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

/**
 * Выполняет запрос на получние данных
 * @param $sql string SQL запрос
 * @param array $data Данные для вставки на место плейсхолдеров
 * @return array|null Результат запроса в виде массива
 */
function getData($sql, $data = []) {
    $link = DbConnectionProvider::getConnection();

    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result === false) {
        exit('Ошибка при попытке получить результат prepared statement: ' . mysqli_stmt_error($stmt));
    }

    return mysqli_fetch_all($result, MYSQLI_ASSOC);
}

/**
 * Выполняет запрос на добавлнеие записи
 * @param $sql string SQL запрос
 * @param array $data Данные для вставки на место плейсхолдеров
 * @return int|string ID добавленной записи
 */
function insertData($sql, $data = []) {
    $link = DbConnectionProvider::getConnection();

    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);

    if (mysqli_errno($link) !== 0) {
        exit('Ошибка получния разультата из подготовленного выражения: ' . mysqli_error($link));
    }

    return mysqli_insert_id($link);
}

/**
 * Выполняет запрос на добавлнеие записи
 * @param $sql string SQL запрос
 * @param array $data Данные для вставки на место плейсхолдеров
 * @return bool true - в случае успеха
 */
function updateData($sql, $data = []) {
    $link = DbConnectionProvider::getConnection();

    $stmt = db_get_prepare_stmt($link, $sql, $data);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_get_result($stmt);

    if (mysqli_errno($link) !== 0) {
        exit('Ошибка получния разультата из подготовленного выражения: ' . mysqli_error($link));
    }

    return true;
}

/**
 * Получает категории
 * @return array Массив с данными о категориях
 */
function getCategories(): array {
    $sql = "SELECT * FROM categories";
    $categories = getData($sql);

    return $categories;
}

/**
 * Получает все лоты
 * @return array Возвращает массив со всеми активными лотами, отсортирвованными по дате создания
 */
function getFreshLots(): array {
    $sql = "SELECT l.id, l.name, l.price AS start_price, l.img_url, MAX(b.price) AS last_price, c.NAME AS cat, l.date, l.date_finish FROM lots AS l
    LEFT JOIN bets AS b ON l.id = b.lot_id
    LEFT JOIN categories AS c ON l.category_id = c.id
    WHERE NOW() < l.date_finish AND l.winner_id IS NULL
    GROUP BY l.id
    ORDER BY l.date DESC";

    return getData($sql);
}

/**
 * Получает количество активных лотов в категории по её ID
 * @param $id ID категории
 * @return mixed|null Возвращает количество активных лотов в заданной категории или null в случае если категория пуста
 */
function getCountСategoryLots($id) {
    $sql = "SELECT COUNT(*) AS count FROM lots 
    WHERE category_id = ? AND NOW() < date_finish AND winner_id IS NULL";

    $result = getData($sql, [$id]);

    return $result[0]['count'] ?? null;
}

/**
 * Получает лоты для одной категории
 * @param $data Данные для вставки на место плейсхолдеров
 * @return array Возвращает массив с информацие о лотах в категории
 */
function getСategoryLots($data): array {
    $sql = "SELECT l.id, l.name, l.price AS start_price, l.img_url, l.date, l.date_finish FROM lots AS l
    WHERE NOW() < l.date_finish AND l.winner_id IS NULL AND l.category_id = ?
    ORDER BY l.date DESC LIMIT ? OFFSET ?";

    return getData($sql, $data);
}

/**
 * Получает имя категории по её ID
 * @param int $id ID категории
 * @return mixed|null Возвращает имя категории или null если категории с таким ID не существет
 */
function getNameCategory(int $id) {
    $sql = "SELECT name FROM categories
    WHERE id = ?";

    $result = getData($sql, [$id]);

    return $result[0]['name'] ?? null;
}

/**
 * Получает данные для страницы лота
 * @param int $id ID лота
 * @return array|null Возвращает маассив с данными для заданного лота или null если лота с таким ID не существет
 */
function getLot(int $id): ?array {
    $sql = "SELECT l.id, l.date, l.name, l.description, l.img_url, l.price, l.date_finish, l.step, l.user_id, l.winner_id, l.category_id, c.name AS cat, MAX(b.price) AS last_price FROM lots AS l
    JOIN categories AS c ON l.category_id = c.id
    LEFT JOIN bets AS b ON l.id = b.lot_id
    WHERE l.id = ?
    GROUP BY l.id";

    $row = getData($sql, [$id]);

    return $row[0] ?? null;
}

/**
 * Получает историю ставок
 * @param int $lot_id ID лота
 * @return array Возвращает массив со всеми ставками для заданного лота, отстортированный по дате добавления ставок
 */
function getHistoryBets(int $lot_id): array {
    $sql = "SELECT b.date, b.price, b.user_id, b.lot_id, u.name FROM bets AS b
    JOIN users AS u ON u.id = b.user_id
    WHERE lot_id = ?
    ORDER BY b.date DESC;";

    return getData($sql, [$lot_id]);
}

/**
 * Возвращает количество лотов в результате запроса
 * @param $keyword string Слово для поиска
 * @return mixed|null Возвращает количество найденных результатов для заданного слова
 */
function countSearchLots($keyword) {
    $sql = "SELECT COUNT(*) AS count_page
    FROM lots AS l
    JOIN categories AS c ON l.category_id = c.id
    WHERE NOW() < l.date_finish AND l.winner_id IS NULL AND  MATCH(l.NAME, l.description) AGAINST(?)
    ORDER BY l.date DESC";

    $count = getData($sql, [$keyword]);

    return $count[0]['count_page'] ?? null;
}

/**
 * Возвращает результат поиска по лотам
 * @param array $data Данные для вставки на место плейсхолдеров
 * @return array|null Возвращает массив с информацией о найденных лотах, с заданным лимитом и смещением. В случае отстутствия результатов поиска возвращает null
 */
function searchLots($data): ?array {
    $sql = "SELECT l.id, l.name, l.price AS start_price, l.img_url, c.NAME AS cat, l.date, l.date_finish
    FROM lots AS l
    JOIN categories AS c ON l.category_id = c.id
    WHERE NOW() < l.date_finish AND l.winner_id IS NULL AND  MATCH(l.NAME, l.description) AGAINST(?)
    ORDER BY l.date DESC LIMIT ? OFFSET ?";

    return getData($sql, $data);
}

/**
 * Проверяет существование e-mail в базе
 * @param $email string Почтовый ящик
 * @return bool Возвращает true в случае если e-mail найден в базе данных и false если ненайден
 */
function checkEmail($email) {
    $sql = "SELECT email FROM users WHERE email=?";

    return getData($sql, [$email]) !== [];
}

/**
 * Проверяет соответствие введённого пароля
 * @param $email string Почтовый ящик
 * @param $password string Пароль
 * @return bool Возвращает true если почта и прароль соответствуют имеющимся в базе данных
 */
function checkPassword($email, $password) {
    $sql = "SELECT password FROM users WHERE email=?";

    $password_hash = getData($sql, [$email]);

    if (empty($password_hash[0]['password'])) {
        return false;
    }

    return password_verify($password, $password_hash[0]['password']);
}

/**
 * Возвращает массив данных пользователя по его e-mal
 * @param $email string Почтовый ящик
 * @return array Возвращает массив с аднными о пользователе по его e-mail, в случае если пользователь не найден в базе, то возвращает null
 */
function getUser($email): array {
    $sql = "SELECT id, date_reg, email, name, avatar_url, contacts FROM users WHERE email=?";

    $user = getData($sql, [$email]);

    return $user[0] ?? null;
}

/**
 * Возвращает информацию о ставке по id пользователя
 * @param $user_id string ID пользователя
 * @return array|null Возвращает массив с данными о ставке
 */
function getBets($user_id) {
    $sql = "SELECT b.date AS bet_date, b.price AS bet_cost, l.id AS lot_id, l.name AS lot_name, l.img_url AS lot_img, l.date_finish AS lot_date_finish, l.winner_id AS lot_winner, c.name AS cat_name, u.contacts AS user_contacts
    FROM bets AS b
    LEFT JOIN lots AS l ON l.id = b.lot_id
    LEFT JOIN categories AS c ON c.id = l.category_id
    LEFT JOIN users AS u ON u.id = l.user_id
    WHERE b.user_id = ?
    ORDER BY b.date DESC";

    return getData($sql, [$user_id]);
}

/**
 * Добавление лота
 * @param array $data Данные для вставки на место плейсхолдеров
 * @return int|string Возвращает ID добавленного лота
 */
function insertLot($data) {
    $sql = "INSERT INTO lots (name, category_id, description, price, step, date_finish, user_id, img_url, date)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";

    return insertData($sql, $data);
}

/**
 * Добавление пользователя
 * @param array $data Данные для вставки на место плейсхолдеров
 * @return int|string Возвращает ID добавленного пользователя
 */
function insertUser($data) {
    $sql = "INSERT INTO users (email, password, name, contacts, avatar_url, date_reg)
    VALUES (?, ?, ?, ?, ?, NOW())";

    return insertData($sql, $data);
}

/**
 * Добавление ставку
 * @param array $data Данные для вставки на место плейсхолдеров
 * @return int|string Возвращает ID добавленной ставки
 */
function insertBet($data) {
    $sql = "INSERT INTO bets (price, user_id, lot_id, date)
    VALUES (?, ?, ?, NOW())";

    return insertData($sql, $data);
}

/**
 * Возвращает список победителей
 * @return array|null Возвращает массив со списком текущих победителей
 */
function getWinner() {
    $sql = "SELECT b.user_id, b.lot_id, l.NAME AS lot_name, u.NAME AS username, u.email
            FROM bets AS b
            JOIN lots AS l ON b.lot_id = l.id
            JOIN users AS u ON u.id = b.user_id
            WHERE ISNULL(l.winner_id) AND l.date_finish < NOW()
            GROUP BY b.user_id, b.lot_id
            HAVING MAX(b.date)";

    return getData($sql);
}

/**
 * Записывает победителя в табицу лота
 * @param array $data Данные для вставки на место плейсхолдеров
 * @return bool Возвращает true - в случае успеха
 */
function setWinner($data) {
    $sql = "UPDATE lots SET winner_id = ? WHERE id = ?";

    return updateData($sql, $data);
}

/**
 * Возвращает текущую цену
 * @param $price string Стартовая цена лота
 * @param $last_bet string Сумма последней ставки
 * @return mixed Возвращает текущую цену лота с учетом ставки, если ставок не было, то возвращает стартовую цену
 */
function currentPrice($price, $last_bet) {
    return $last_bet ?? $price;
}

/**
 * Возвращает минимальную ставку
 * @param $current_price string Текущая цена
 * @param $step string Шаг ставки
 * @return mixed Возвращате минимально возможную ставку
 */
function minBet($current_price, $step) {
    return $current_price + $step;
}

/**
 * Форматирует цену
 * @param float $number Значене цены которое надо отформатировать
 * @return string Возвращает отформатированную цену со знаком рубля
 */
function format_price(float $number): string {
    $number = ceil($number);

    if ($number >= 1000) {
        $number = number_format($number, 0, '', ' ');
    }

    $number .= '<b class="rub">р</b>';

    return $number;
}

/**
 * Проверяет осталось ли до конца жизни лота 1 час или меньше
 * @param $time_end string Время завершения жизни лота
 * @return bool возвращает true если до окончания жизни лота остлся один час или меньше
 */
function warningOneHourLeft($time_end) {
    $time_diff = strtotime($time_end) - time();

    return $time_diff <= 3600;
}

/**
 * Проверяет живой ли лот
 * @param $time_end string Время окончания жизни лота
 * @return bool Возвращает true если лот живой и false если время жизни истекло
 */
function warningFinishing($time_end) {
    return (strtotime($time_end) - time()) < 0;
}

/**
 * Получает оставшееся время жизни лота
 * @param $time_end string время окончания жизни лота
 * @return string Возвращает время оставшееся до окончания жизни лота в формате ЧЧ:ММ
 */
function lifetime_lot($time_end) {
    $time_difference = strtotime($time_end) - time();

    $hours = floor($time_difference / 3600);
    $minutes = floor(($time_difference % 3600) / 60);

    if ($minutes < 10) {
        $minutes = '0' . $minutes;
    }

    return $hours . ':' . $minutes;
}

/**
 * Форматирует время создания ставки
 * @param $created_time string  Время создания ставки
 * @return string Возвращает время создания ставки в человеческом формате
 */
function createdTimeAgo($created_time) {
    $time = date('d.m.y', strtotime($created_time)) . ' в ' . date('H:i', strtotime($created_time));

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
    } elseif ($time_difference < 60 * 60) {
        $time = $minutes . ' ' . $minutes_plural . ' назад';
    } elseif ($time_difference < 60 * 60 * 2) {
        $time = 'Час ' . (($minutes != 0) ? ($minutes . ' ' . $minutes_plural) : '') . ' назад';
    } elseif ($time_difference >= 60 * 60 * 2 && $time_difference < 24 * 60 * 60) {
        $time = $hours . ' ' . $hours_plural . ' ' . $minutes . ' ' . $minutes_plural . ' назад';
    } elseif ($time_difference < 24 * 60 * 60 * 2) {
        $time = 'Вчера, в ' . date('H:i', strtotime($created_time));
    } elseif ($time_difference < 24 * 60 * 60 * 3) {
        $time = 'Позавчера, в ' . date('H:i', strtotime($created_time));
    }

    return $time;
}

/**
 * Определяет что дата больше минимум на один день
 * @param $time string Дата
 * @return bool Возвращает true если заданная дата больше хотя бы на один день от текущей
 */
function validDate($time) {
    $time = strtotime($time);
    $time_tomorrow = strtotime('+1 day 00:00:00');

    return $time >= $time_tomorrow;
}
