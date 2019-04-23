/*Заполняем таблицу категории*/
INSERT INTO categories (name, char_code)
VALUES ('Доски и лыжи', 'boards'),
('Крепления', 'attachment'),
('Ботинки', 'boots'),
('Одежда', 'clothing'),
('Инструменты', 'tools'),
('Разное', 'other');

/*Заполняем таблицу пользователей*/
INSERT INTO users (date_reg, email, name, password, avatar_url, contacts)
VALUES ('2019-04-02 21:02:19', 'stepan@ya.io', 'stepan', '123a3f', '', 'Москва, ул. Мира, д. 10, кв. 226'),
('2019-04-03 18:37:44', 'dmitriy@gmail.com', 'dmitriy', '753y8v', '', 'Санкт-Петербург, ул. Ленина, д. 17, кв. 13');

/*Заполняем таблицу с лотами*/
INSERT INTO lots (date, name, category_id, price, img_url, description, date_finish, step, user_id)
VALUES ('2019-04-05 15:22:01', '2014 Rossignol District Snowboard', '1', '10999', 'img/lot-1.jpg', '', '2019-04-06 00:00:00', '100', '1'),
('2019-04-05 16:12:09', 'DC Ply Mens 2016/2017 Snowboard', '1', '159999', 'img/lot-1.jpg', '', '2019-04-06 00:00:00', '100', '1'),
('2019-04-05 18:20:52', 'Крепления Union Contact Pro 2015 года размер L/XL', '2', '8000', 'img/lot-3.jpg', '', '2019-04-06 00:00:00', '100', '1'),
('2019-04-06 12:00:47', 'Ботинки для сноуборда DC Mutiny Charocal', '3', '10999', 'img/lot-4.jpg', '', '2019-04-07 00:00:00', '100', '1'),
('2019-04-06 15:02:33', 'Куртка для сноуборда DC Mutiny Charocal', '4', '7500', 'img/lot-5.jpg', '', '2019-04-07 00:00:00', '100', '2'),
('2019-04-08 22:44:26', 'Маска Oakley Canopy', '6', '5400', 'img/lot-6.jpg', '', '2019-04-08 00:00:00', '100', '2');

/*Заполняем таблицу ставок*/
INSERT INTO bets (date, price, user_id, lot_id)
VALUES ('2019-04-06 15:10:10', '11099', '2', '1'),
('2019-04-06 15:12:19', '11099', '2', '3');

/*Получаем все категории*/
SELECT * FROM categories;

/*Получаем самые сновые открытые лоты*/
SELECT l.name, l.price, l.img_url, b.price, c.name FROM lots AS l
JOIN bets AS b ON l.id = b.lot_id
JOIN categories AS c ON l.category_id = c.id
WHERE l.date < l.date_finish
ORDER BY l.date DESC LIMIT 3;

/*Получаем лот по его id и категорию лота*/
SELECT l.*, c.name FROM lots AS l JOIN categories AS c ON l.category_id = c.id WHERE l.id = 1;

/*Обновляем назване лота по его id*/
UPDATE lots SET name = '2015 Rossignol District Snowboard' WHERE id = 1;

/*Получаем список 3-х самых свежих ставок для лота, по его id*/
SELECT price  FROM bets WHERE lot_id = 3 ORDER BY date DESC LIMIT 3;
