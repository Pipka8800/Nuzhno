-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Мар 27 2025 г., 07:39
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `hotel_management`
--

-- --------------------------------------------------------

--
-- Структура таблицы `floors`
--

CREATE TABLE `floors` (
  `floor_id` int(11) NOT NULL,
  `floor_number` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `floors`
--

INSERT INTO `floors` (`floor_id`, `floor_number`) VALUES
(1, '1 этаж'),
(2, '2 этаж'),
(3, '3 этаж');

-- --------------------------------------------------------

--
-- Структура таблицы `rooms`
--

CREATE TABLE `rooms` (
  `room_id` int(11) NOT NULL,
  `room_number` varchar(10) NOT NULL,
  `floor_id` int(11) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `status` enum('Свободен','Занят','Грязный','Назначен к уборке','Чистый') DEFAULT 'Свободен'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `rooms`
--

INSERT INTO `rooms` (`room_id`, `room_number`, `floor_id`, `category_id`, `status`) VALUES
(101, '101', 1, 1, 'Свободен'),
(102, '102', 1, 1, 'Свободен'),
(103, '103', 1, 2, 'Свободен'),
(104, '104', 1, 2, 'Свободен'),
(105, '105', 1, 3, 'Свободен'),
(106, '106', 1, 3, 'Свободен'),
(107, '107', 1, 4, 'Свободен'),
(108, '108', 1, 4, 'Свободен'),
(109, '109', 1, 5, 'Свободен'),
(110, '110', 1, 5, 'Свободен'),
(201, '201', 2, 6, 'Свободен'),
(202, '202', 2, 6, 'Свободен'),
(203, '203', 2, 6, 'Свободен'),
(204, '204', 2, 7, 'Свободен'),
(205, '205', 2, 7, 'Свободен'),
(206, '206', 2, 7, 'Свободен'),
(207, '207', 2, 1, 'Свободен'),
(208, '208', 2, 1, 'Свободен'),
(209, '209', 2, 1, 'Свободен'),
(301, '301', 3, 8, 'Свободен'),
(302, '302', 3, 8, 'Свободен'),
(303, '303', 3, 8, 'Свободен'),
(304, '304', 3, 9, 'Свободен'),
(305, '305', 3, 9, 'Свободен'),
(306, '306', 3, 9, 'Свободен');

-- --------------------------------------------------------

--
-- Структура таблицы `room_categories`
--

CREATE TABLE `room_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `room_categories`
--

INSERT INTO `room_categories` (`category_id`, `category_name`) VALUES
(1, 'Одноместный стандарт'),
(2, 'Одноместный эконом'),
(3, 'Стандарт двухместный с 2 раздельными кроватями'),
(4, 'Эконом двухместный с 2 раздельными кроватями'),
(5, '3-местный бюджет'),
(6, 'Бизнес с 1 или 2 кроватями'),
(7, 'Двухкомнатный двухместный стандарт с 1 или 2 кроватями'),
(8, 'Студия'),
(9, 'Люкс с 2 двуспальными кроватями');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `floors`
--
ALTER TABLE `floors`
  ADD PRIMARY KEY (`floor_id`);

--
-- Индексы таблицы `rooms`
--
ALTER TABLE `rooms`
  ADD PRIMARY KEY (`room_id`),
  ADD KEY `floor_id` (`floor_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Индексы таблицы `room_categories`
--
ALTER TABLE `room_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `room_categories`
--
ALTER TABLE `room_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `rooms`
--
ALTER TABLE `rooms`
  ADD CONSTRAINT `rooms_ibfk_1` FOREIGN KEY (`floor_id`) REFERENCES `floors` (`floor_id`),
  ADD CONSTRAINT `rooms_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `room_categories` (`category_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
