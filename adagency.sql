-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Час створення: Чрв 06 2025 р., 12:57
-- Версія сервера: 10.4.32-MariaDB
-- Версія PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База даних: `adagency`
--

-- --------------------------------------------------------

--
-- Структура таблиці `adspace`
--

CREATE TABLE `adspace` (
  `adspace_id` int(11) NOT NULL,
  `ad_type` varchar(100) NOT NULL,
  `ad_description` text NOT NULL,
  `ad_price` decimal(10,2) NOT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `adspace`
--

INSERT INTO `adspace` (`adspace_id`, `ad_type`, `ad_description`, `ad_price`, `photo`) VALUES
(1, 'Діджитал борд', 'Яскравий LED-дисплей з можливістю динамічної зміни контенту', 4950.00, 'uploads/1749150079_digital_board.jpg'),
(2, 'Рекламний щит', 'Статичний білборд стандартного розміру 3х6 м', 8609.79, 'uploads/1749150277_billboard.jpg'),
(3, 'Суперсайти', 'Велика зовнішня реклама для автотрас і центрів міст', 11479.72, 'images/supersite.jpg'),
(4, 'Сітілайти', 'Освітлені рекламні площини формату 1.2х1.8 м для пішоходів', 3587.42, 'images/citylight.jpg'),
(5, 'Сітіборди', 'Сучасні білборди меншого розміру в міській зоні', 4304.90, 'uploads/1749132062_cityboard.jpg'),
(6, 'Зупинки громадського транспорту', 'Реклама на зупинках із великим пасажиропотоком', 4304.90, 'uploads/1749132081_bus_stop.jpg'),
(7, 'Колони', 'Реклама, розміщена на колонах і опорах у центрі міста', 2869.93, 'uploads/1749132103_column.jpg');

-- --------------------------------------------------------

--
-- Структура таблиці `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `adspace_id` int(11) NOT NULL,
  `booking_date` date NOT NULL DEFAULT curdate(),
  `message` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `adspace_id`, `booking_date`, `message`) VALUES
(1, 1, 1, '2025-05-31', 'test');

-- --------------------------------------------------------

--
-- Структура таблиці `campaigns`
--

CREATE TABLE `campaigns` (
  `campaign_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `campaign_name` varchar(100) NOT NULL,
  `campaign_description` text NOT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `campaigns`
--

INSERT INTO `campaigns` (`campaign_id`, `client_id`, `campaign_name`, `campaign_description`, `photo`) VALUES
(1, 1, 'Літня кампанія Coca-Cola', 'Просування нових смаків у літній період 2025', 'images/campaign_coca.jpg'),
(2, 2, 'PepsiCo - Зимові знижки', 'Рекламна кампанія сезонних акцій', 'images/campaign_pepsi.jpg'),
(3, 3, 'Nestlé - Здорове харчування', 'Освітня кампанія про користь продуктів Nestlé', 'images/campaign_nestle.jpg'),
(4, 8, 'McDonald’s – Смак літа 2025', 'Кампанія з просування нових бургерів та сезонних пропозицій у літній період', 'images/campaign_mac.jpg');

-- --------------------------------------------------------

--
-- Структура таблиці `clients`
--

CREATE TABLE `clients` (
  `client_id` int(11) NOT NULL,
  `client_name` varchar(100) NOT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `client_email` varchar(255) NOT NULL,
  `client_phone` varchar(20) NOT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `clients`
--

INSERT INTO `clients` (`client_id`, `client_name`, `contact_person`, `client_email`, `client_phone`, `photo`) VALUES
(1, 'Coca-Cola Ukraine', 'Олексій Іванов', 'contact@coca-cola.ua', '+380671112233', 'images/coca_cola.jpg'),
(2, 'PepsiCo Ukraine', 'Марина Петренко', 'info@pepsico.ua', '+380501234567', 'images/pepsico.jpg'),
(3, 'Nestlé Ukraine', 'Ігор Коваленко', 'support@nestle.ua', '+380631234567', 'images/nestle.jpg'),
(4, 'Procter & Gamble Ukraine', 'Світлана Бондаренко', 'pg.ua@example.com', '+380931112244', 'images/pg.jpg'),
(8, 'McDonald\'s', 'Олег Завадський', 'support@mcdonalds.ua', '+380631234564', 'images/mac.jpg');

-- --------------------------------------------------------

--
-- Структура таблиці `employees`
--

CREATE TABLE `employees` (
  `employee_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `position` varchar(100) NOT NULL,
  `employee_email` varchar(255) NOT NULL,
  `photo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `employees`
--

INSERT INTO `employees` (`employee_id`, `first_name`, `last_name`, `position`, `employee_email`, `photo`) VALUES
(1, 'Роман', 'Олеш', 'Менеджер з роботи з клієнтами', 'romanolesh2@gmail.com', 'images/roman.jpg'),
(2, 'Любомир', 'Левицький', 'Менеджер з продажу', 'lyubomyr.levytskiy@gmail.com', 'images/lyub.jpg'),
(3, 'Анна', 'Олексюк', 'Маркетолог', 'anna.oleksiuk@gmail.com', 'images/anya.jpg'),
(4, 'Сергій', 'Корень', 'Аналітик', 'serhii.koren@gmail.com', 'images/serhii.jpg'),
(5, 'Тарас', 'Граничка', 'Керівник проектів', 'taras.granychka@gmail.com', 'images/taras.jpg');

-- --------------------------------------------------------

--
-- Структура таблиці `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `review_content` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `reviews`
--

INSERT INTO `reviews` (`review_id`, `user_id`, `review_content`) VALUES
(1, 1, 'Хороший сайт'),
(4, 2, 'TEST');

-- --------------------------------------------------------

--
-- Структура таблиці `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп даних таблиці `users`
--

INSERT INTO `users` (`user_id`, `first_name`, `last_name`, `user_email`, `password`) VALUES
(1, 'Roman', 'Olesh', 'romanolesh2@gmail.com', '$2y$10$XjkPBsMjVZaycZyd18dYUeGsGyhYbAwN99rujfacXFcL3VzW4rQdi'),
(2, 'Test Name', 'Test Surname', 'test@gmail.com', '$2y$10$MP.8xmXev3Jda/O9ohRvXuArXG25CJa9titdXfj3sVb98zk9tiqq6');

--
-- Індекси збережених таблиць
--

--
-- Індекси таблиці `adspace`
--
ALTER TABLE `adspace`
  ADD PRIMARY KEY (`adspace_id`);

--
-- Індекси таблиці `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `adspace_id` (`adspace_id`);

--
-- Індекси таблиці `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`campaign_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Індекси таблиці `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD UNIQUE KEY `client_email` (`client_email`);

--
-- Індекси таблиці `employees`
--
ALTER TABLE `employees`
  ADD PRIMARY KEY (`employee_id`),
  ADD UNIQUE KEY `employee_email` (`employee_email`);

--
-- Індекси таблиці `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Індекси таблиці `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`);

--
-- AUTO_INCREMENT для збережених таблиць
--

--
-- AUTO_INCREMENT для таблиці `adspace`
--
ALTER TABLE `adspace`
  MODIFY `adspace_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблиці `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблиці `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `campaign_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблиці `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблиці `employees`
--
ALTER TABLE `employees`
  MODIFY `employee_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT для таблиці `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблиці `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Обмеження зовнішнього ключа збережених таблиць
--

--
-- Обмеження зовнішнього ключа таблиці `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`adspace_id`) REFERENCES `adspace` (`adspace_id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `campaigns`
--
ALTER TABLE `campaigns`
  ADD CONSTRAINT `campaigns_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`) ON DELETE CASCADE;

--
-- Обмеження зовнішнього ключа таблиці `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
