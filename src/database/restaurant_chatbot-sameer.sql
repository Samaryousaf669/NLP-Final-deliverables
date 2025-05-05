-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 05, 2025 at 11:51 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `restaurant_chatbot-sameer`
--

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(8,2) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `item_name`, `description`, `price`, `image_url`, `created_at`) VALUES
(1, 'Rice', 'chinese Rice', 4000.00, 'uploads/1746382241_Rice.jpg', '2025-04-07 06:41:30'),
(2, 'Margherita Pizza', 'Classic Italian pizza with tomatoes, mozzarella, and basil.', 8.99, 'uploads/1746382095_pizza.jpg', '2025-04-07 06:49:25'),
(3, 'Cheeseburger', 'Grilled beef patty with cheddar, lettuce, tomato, and pickles.', 6.49, 'uploads/1746381993_burger.jpg', '2025-04-07 06:49:25'),
(4, 'Salad', 'Fresh romaine lettuce with Caesar dressing, croutons, and parmesan.', 5.99, 'uploads/1746381924_salad.jpg', '2025-04-07 06:49:25'),
(5, 'Spaghetti ', 'Pasta with a rich beef and tomato sauce.', 9.49, 'uploads/1746381837_spaghetti.jpg', '2025-04-07 06:49:25'),
(6, 'Grilled Chicken Sandwich', 'Grilled chicken breast with mayo, lettuce, and tomato.', 7.25, 'uploads/1746381655_sandwich.jpg', '2025-04-07 06:49:25'),
(7, 'Tandoori Chicken', 'Spicy grilled chicken marinated in yogurt and spices.', 10.00, 'uploads/1746381593_grill chicken.jpg', '2025-04-07 06:49:25'),
(8, 'Vegetable Stir Fry', 'Mixed vegetables sautéed in a savory soy garlic sauce.', 7.75, 'uploads/1746381463_stir fry.jpg', '2025-04-07 06:49:25'),
(9, 'Chocolate Brownie', 'Rich chocolate brownie served warm.', 3.50, 'uploads/1746381324_brownie.jpg', '2025-04-07 06:49:25'),
(10, 'soft drink ', 'cola', 4.25, 'uploads/1746382556_softdrinks.jpg', '2025-04-07 06:49:25'),
(11, 'French Fries', 'Crispy golden potato fries with seasoning.', 2.99, 'uploads/1746380972_french fries.avif', '2025-04-07 06:49:25');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','preparing','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `total_amount`, `status`, `created_at`) VALUES
(1, 4, 26.97, 'completed', '2025-04-07 07:05:09'),
(2, 4, 26.97, 'cancelled', '2025-04-07 07:05:32'),
(3, 5, 5.99, 'pending', '2025-05-05 09:34:27'),
(4, 5, 6.49, 'completed', '2025-05-05 09:34:33');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price_each` decimal(8,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `menu_item_id`, `quantity`, `price_each`) VALUES
(1, 1, 2, 3, 8.99),
(2, 2, 2, 3, 8.99),
(3, 3, 4, 1, 5.99),
(4, 4, 3, 1, 6.49);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `reservation_date` date NOT NULL,
  `reservation_time` time NOT NULL,
  `people_count` int(11) NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `reservation_date`, `reservation_time`, `people_count`, `status`, `created_at`) VALUES
(1, 4, '2025-04-08', '13:18:00', 20, 'confirmed', '2025-04-07 07:18:12'),
(2, 5, '2025-06-03', '17:36:00', 2, 'confirmed', '2025-05-05 09:37:24');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `address` text NOT NULL,
  `city` varchar(100) NOT NULL,
  `role` enum('customer','admin') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `phone`, `address`, `city`, `role`, `created_at`) VALUES
(1, 'Nasir Abbas', 'patient@gmail.com', '$2y$10$6WZGPjpcKddoU7G4KiaGZ.H9G34w/7R5Xa7sDq4DjrhHIudbATHfK', '03266029050', 'dfaea', 'Multan', '', '2025-03-14 05:13:50'),
(3, 'Admin', 'admin@gmail.com', '$2y$10$fWCKMInEmjqN0iZ0xkCrn.YvN4SZjNKbL7GxSgkW8YdA4JW1fBgGe', '00000000000', 'fda', 'Lahore', 'admin', '2025-03-14 05:25:44'),
(4, 'New Customer', 'customer@gmail.com', '$2y$10$.QzHQu0UahFQNsDVzD5SoORz1C/nGzIEglm5ao/uA9tk89xrvI8na', '3176526827', 'Street jeff xxxx', 'Salvador', 'customer', '2025-04-07 07:01:44'),
(5, 'Samer Yousaf', 'samar123@gmail.com', '$2y$10$VUaRQOOgkl0ZY0TNQhdSS.BEi/U8q4WmoBqhdvcIqpGvgipBPdafS', '032************', 'abc', 'xyz', 'customer', '2025-05-05 09:32:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_item_id` (`menu_item_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`menu_item_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
