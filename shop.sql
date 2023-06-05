-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Czas generowania: 05 Cze 2023, 21:00
-- Wersja serwera: 10.4.27-MariaDB
-- Wersja PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Baza danych: `shop`
--

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Elektronika'),
(2, 'Odzież'),
(3, 'Książki'),
(4, 'Dom i ogród'),
(5, 'Sport i rekreacja'),
(6, 'Żywność'),
(7, 'Kosmetyki'),
(8, 'Budowlane'),
(9, 'Zdrowie'),
(10, 'Motoryzacja');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `city` varchar(50) NOT NULL,
  `street` varchar(100) NOT NULL,
  `postal_code` varchar(10) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `delivery_method` varchar(50) NOT NULL,
  `payment_method` varchar(50) NOT NULL,
  `status` varchar(50) NOT NULL,
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `orders`
--

INSERT INTO `orders` (`id`, `first_name`, `last_name`, `city`, `street`, `postal_code`, `phone`, `delivery_method`, `payment_method`, `status`, `user_id`) VALUES
(1, 'Wojciech', 'Sikora', 'Kościan', 'Polna', '64-000', '111222333', 'kurierska', 'pobranie', 'zrealizowany', 1),
(2, 'Wojciech', 'Sikora', 'Kościan', 'Polna', '64-000', '123123123', 'kurierska', 'pobranie', 'oczekujący', 1),
(3, 'asd', 'asd', 'asd', 'asd', 'asd', 'asd', 'kurierska', 'pobranie', 'oczekujący', 1),
(4, 'Jan', 'Kowalski', 'Leszno', 'Prosta', '64-100', '123123123', 'kurierska', 'pobranie', 'oczekujący', 6),
(5, 'TEST', 'TEST', 'TEST', 't', '11-111', '999888777', 'kurierska', 'pobranie', 'oczekujący', 6),
(6, 'Wojciech', 'Sikora', 'Kościan', 'Polna', '64-000', '123444555', 'kurierska', 'pobranie', 'oczekujący', 9),
(7, 'Wojciech', 'Sikora', 'Kościan', 'Polna', '64-000', '111222333', 'kurierska', 'pobranie', 'w trakcie realizacji', 1);

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(1, 1, 2, 1, '15.00'),
(2, 1, 3, 1, '1200.00'),
(3, 1, 4, 1, '5.00'),
(4, 2, 2, 1, '15.00'),
(5, 3, 2, 3, '15.00'),
(6, 3, 3, 1, '1200.00'),
(7, 4, 3, 1, '1200.00'),
(8, 5, 3, 1, '1200.00'),
(9, 6, 2, 1, '15.00'),
(10, 7, 5, 3, '2.00'),
(11, 7, 6, 1, '14.00'),
(12, 7, 8, 5, '31.00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `category` varchar(50) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `products`
--

INSERT INTO `products` (`id`, `name`, `category`, `description`, `image`, `price`) VALUES
(3, 'Komputer', '1', 'To co widać', 'comp.jpg', '1200.00'),
(5, 'Banan', '6', 'Taki o!', 'pexels-aleksandar-pasaric-2872755.jpg', '2.00'),
(6, 'Tabletki', '9', 'Tabletki przeciwbólowe', 'pexels-pixabay-159211.jpg', '14.00'),
(7, 'Auto', '10', 'Czerwone', 'pexels-brett-sayles-1638459.jpg', '21950.00'),
(8, 'Kamień', '8', 'Każdy potrzebuje kamienia', 'pexels-peter-döpper-2363901.jpg', '31.00');

-- --------------------------------------------------------

--
-- Struktura tabeli dla tabeli `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `account_type` enum('admin','moderator','user') NOT NULL DEFAULT 'user'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Zrzut danych tabeli `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `account_type`) VALUES
(1, 'admin', '$2y$10$74wlDuy6v0mBcsQFfuEuyuaEhdz6nFg1MptZhhbgGhheKMcD8rksO', 'admin'),
(2, 'janusz', '$2y$10$mJDz5XrH0TvmbDcUJHdWp.au/kBNJYr//X5uDQ.sX/tm5LMwqrTt2', 'admin'),
(4, 'adam', '$2y$10$hKlK9CbGxY6M0/PVr7F4BeqobZnHZJgXIUDXAeNUjaAk0x0TamYo6', 'user'),
(5, 'voyteq', '$2y$10$pUs7E2RJuusLD57bXUOl7.9LK5ZB/4Gf7gsuo8dkA2psHcVLmFOXu', 'moderator'),
(6, 'test1', '$2y$10$53AdyUbkb8Tv32.hK6QJxeajzHqVjTx6cN30Lwj9r1VddnY6puE16', 'user'),
(7, 'test2', '$2y$10$DeOs88jkpeO4nLpwLJVG4.x4jVCQtUGtgX1awN99b6y2jNpwtn4bS', 'user'),
(8, 'moderator', '$2y$10$UX53Pb4p1px4fAfcESPU7uRGvZzKOgFeOvkmBP/PnDzZImLh71yye', 'user'),
(9, 'Wojciech Sikora', '$2y$10$gWM3Wkk4OOFoYbLsWwjcT.drF58GxEEsv02YAdbYiZCuoiZVegAaq', 'user');

--
-- Indeksy dla zrzutów tabel
--

--
-- Indeksy dla tabeli `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indeksy dla tabeli `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeksy dla tabeli `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT dla zrzuconych tabel
--

--
-- AUTO_INCREMENT dla tabeli `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT dla tabeli `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT dla tabeli `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT dla tabeli `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT dla tabeli `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Ograniczenia dla zrzutów tabel
--

--
-- Ograniczenia dla tabeli `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
