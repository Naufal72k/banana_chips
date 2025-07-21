-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 03, 2025 at 04:34 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `banana_chips`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `order_code` varchar(50) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_address` text NOT NULL,
  `payment_method` varchar(20) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `shipping_cost` decimal(10,2) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `order_code`, `customer_name`, `customer_phone`, `customer_address`, `payment_method`, `subtotal`, `shipping_cost`, `total`, `notes`, `status`, `created_at`, `user_id`) VALUES
(1, 'ORD-20250701051157-DCB0E1', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '155000.00', '10000.00', '165000.00', 'a', 'pending', '2025-07-01 11:11:57', 0),
(2, 'ORD-20250701051240-81DC52', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '155000.00', '10000.00', '165000.00', 'a', 'pending', '2025-07-01 11:12:40', 0),
(3, 'ORD-20250701051257-93963D', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '155000.00', '10000.00', '165000.00', 'a', 'pending', '2025-07-01 11:12:57', 0),
(4, 'ORD-20250701051330-A2DF9A', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '155000.00', '10000.00', '165000.00', 'a', 'pending', '2025-07-01 11:13:30', 0),
(5, 'ORD-20250701051345-999A6B', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '155000.00', '10000.00', '165000.00', 'a', 'pending', '2025-07-01 11:13:45', 0),
(6, 'ORD-20250701051450-A08892', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '155000.00', '10000.00', '165000.00', 'a', 'pending', '2025-07-01 11:14:50', 0),
(7, 'ORD-20250701051626-A5E076', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '155000.00', '10000.00', '165000.00', 'a', 'pending', '2025-07-01 11:16:26', 0),
(8, 'ORD-20250701051644-C594B6', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '155000.00', '10000.00', '165000.00', 'a', 'pending', '2025-07-01 11:16:44', 0),
(9, 'ORD-20250701051658-AAAE3C', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '155000.00', '10000.00', '165000.00', 'a', 'pending', '2025-07-01 11:16:58', 0),
(10, 'ORD-20250701052009-937A13', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '27000.00', '10000.00', '37000.00', 'a', 'pending', '2025-07-01 11:20:09', 0),
(11, 'ORD-20250701052039-7F2112', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '122000.00', '10000.00', '132000.00', 'a', 'pending', '2025-07-01 11:20:39', 0),
(12, 'ORD-20250702040818-2F285A', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '27000.00', '10000.00', '37000.00', 'a', 'pending', '2025-07-02 10:08:18', 0),
(13, 'ORD-20250702081353-1A6500', 'naufal', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '28000.00', '10000.00', '38000.00', 'a', 'cancelled', '2025-07-02 14:13:53', 1),
(14, 'ORD-20250702082342-E63CE5', 'naufal', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '28000.00', '10000.00', '38000.00', 'a', 'pending', '2025-07-02 14:23:42', 0),
(15, 'ORD-20250702082858-AC13E2', 'naufal', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '28000.00', '10000.00', '38000.00', 'a', 'pending', '2025-07-02 14:28:58', 0),
(16, 'ORD-20250702085902-65E524', 'naufal ihsanul islam', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '224000.00', '10000.00', '234000.00', 'a', 'completed', '2025-07-02 14:59:02', 1),
(17, 'ORD-20250702090241-1C8B21', 'naufal', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '224000.00', '10000.00', '234000.00', 'a', 'completed', '2025-07-02 15:02:41', 1),
(18, 'ORD-20250702090256-010D94', 'naufal', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '28000.00', '10000.00', '38000.00', 'a', 'cancelled', '2025-07-02 15:02:56', 1),
(21, 'ORD-20250702114344-047543', 'naufal', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '81000.00', '10000.00', '91000.00', 'k', 'completed', '2025-07-02 17:43:44', 1),
(22, 'ORD-20250702114359-F5BB3D', 'naufal', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '56000.00', '10000.00', '66000.00', 'a', 'completed', '2025-07-02 17:43:59', 1),
(23, 'ORD-20250702114414-E1AAB2', 'naufal', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '72000.00', '10000.00', '82000.00', 'aaaa', 'completed', '2025-07-02 17:44:14', 1),
(24, 'ORD-20250702122228-443A65', 'naufal1', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '53000.00', '10000.00', '63000.00', 'n', 'shipped', '2025-07-02 18:22:28', 2),
(25, 'ORD-20250703033929-155895', 'naufal9', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '28000.00', '10000.00', '38000.00', 'n', 'pending', '2025-07-03 09:39:29', 3),
(26, 'ORD-20250703034206-EE751E', 'naufal9', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '28000.00', '10000.00', '38000.00', 'n', 'pending', '2025-07-03 09:42:06', 3),
(27, 'ORD-20250703035137-9DC2B1', 'naufal9', '0857343434', 'jln sunan giri 4 g 20', 'transfer', '24000.00', '10000.00', '34000.00', 'n', 'cancelled', '2025-07-03 09:51:37', 3);

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `product_price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_price`, `quantity`, `subtotal`) VALUES
(1, 1, 22, 'Keripik Pisang BBQ', '25000.00', 3, '75000.00'),
(2, 1, 23, 'Keripik Pisang Karamel Asin', '27000.00', 2, '54000.00'),
(3, 1, 24, 'Keripik Pisang Stroberi', '26000.00', 1, '26000.00'),
(4, 2, 22, 'Keripik Pisang BBQ', '25000.00', 3, '75000.00'),
(5, 2, 23, 'Keripik Pisang Karamel Asin', '27000.00', 2, '54000.00'),
(6, 2, 24, 'Keripik Pisang Stroberi', '26000.00', 1, '26000.00'),
(7, 3, 22, 'Keripik Pisang BBQ', '25000.00', 3, '75000.00'),
(8, 3, 23, 'Keripik Pisang Karamel Asin', '27000.00', 2, '54000.00'),
(9, 3, 24, 'Keripik Pisang Stroberi', '26000.00', 1, '26000.00'),
(10, 4, 22, 'Keripik Pisang BBQ', '25000.00', 3, '75000.00'),
(11, 4, 23, 'Keripik Pisang Karamel Asin', '27000.00', 2, '54000.00'),
(12, 4, 24, 'Keripik Pisang Stroberi', '26000.00', 1, '26000.00'),
(13, 5, 22, 'Keripik Pisang BBQ', '25000.00', 3, '75000.00'),
(14, 5, 23, 'Keripik Pisang Karamel Asin', '27000.00', 2, '54000.00'),
(15, 5, 24, 'Keripik Pisang Stroberi', '26000.00', 1, '26000.00'),
(16, 6, 22, 'Keripik Pisang BBQ', '25000.00', 3, '75000.00'),
(17, 6, 23, 'Keripik Pisang Karamel Asin', '27000.00', 2, '54000.00'),
(18, 6, 24, 'Keripik Pisang Stroberi', '26000.00', 1, '26000.00'),
(19, 7, 22, 'Keripik Pisang BBQ', '25000.00', 3, '75000.00'),
(20, 7, 23, 'Keripik Pisang Karamel Asin', '27000.00', 2, '54000.00'),
(21, 7, 24, 'Keripik Pisang Stroberi', '26000.00', 1, '26000.00'),
(22, 8, 22, 'Keripik Pisang BBQ', '25000.00', 3, '75000.00'),
(23, 8, 23, 'Keripik Pisang Karamel Asin', '27000.00', 2, '54000.00'),
(24, 8, 24, 'Keripik Pisang Stroberi', '26000.00', 1, '26000.00'),
(25, 9, 22, 'Keripik Pisang BBQ', '25000.00', 3, '75000.00'),
(26, 9, 23, 'Keripik Pisang Karamel Asin', '27000.00', 2, '54000.00'),
(27, 9, 24, 'Keripik Pisang Stroberi', '26000.00', 1, '26000.00'),
(28, 10, 14, 'Keripik Pisang Keju', '27000.00', 1, '27000.00'),
(29, 11, 15, 'Paket Hemat Keluarga', '72000.00', 1, '72000.00'),
(30, 11, 23, 'Keripik Pisang Karamel Asin', '27000.00', 1, '27000.00'),
(31, 11, 18, 'Keripik Pisang Madu', '23000.00', 1, '23000.00'),
(32, 12, 14, 'Keripik Pisang Keju', '27000.00', 1, '27000.00'),
(33, 13, 13, 'Keripik Pisang Cokelat', '28000.00', 1, '28000.00'),
(34, 14, 13, 'Keripik Pisang Cokelat', '28000.00', 1, '28000.00'),
(35, 15, 13, 'Keripik Pisang Cokelat', '28000.00', 1, '28000.00'),
(36, 16, 13, 'Keripik Pisang Cokelat', '28000.00', 8, '224000.00'),
(37, 17, 13, 'Keripik Pisang Cokelat', '28000.00', 8, '224000.00'),
(38, 18, 13, 'Keripik Pisang Cokelat', '28000.00', 1, '28000.00'),
(41, 21, 13, 'Keripik Pisang Cokelat', '28000.00', 1, '28000.00'),
(42, 21, 23, 'Keripik Pisang Karamel Asin', '27000.00', 1, '27000.00'),
(43, 21, 24, 'Keripik Pisang Stroberi', '26000.00', 1, '26000.00'),
(44, 22, 13, 'Keripik Pisang Cokelat', '28000.00', 2, '56000.00'),
(45, 23, 15, 'Paket Hemat Keluarga', '72000.00', 1, '72000.00'),
(46, 24, 16, 'Promo Akhir Pekan', '25000.00', 1, '25000.00'),
(47, 24, 13, 'Keripik Pisang Cokelat', '28000.00', 1, '28000.00'),
(48, 25, 13, 'Keripik Pisang Cokelat', '28000.00', 1, '28000.00'),
(49, 26, 13, 'Keripik Pisang Cokelat', '28000.00', 1, '28000.00'),
(50, 27, 20, 'Keripik Pisang Kelapa', '24000.00', 1, '24000.00');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` int(11) NOT NULL,
  `size` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('promo','unggulan','biasa') NOT NULL DEFAULT 'biasa',
  `category` enum('original','manis','gurih','pedas') NOT NULL DEFAULT 'original'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `size`, `description`, `image`, `created_at`, `status`, `category`) VALUES
(12, 'Keripik Pisang Karamel', 25000, '250 gr', 'Keripik pisang dengan lapisan karamel yang manis dan renyah.', 'uploads/6863303a33b6a_best1.jpg', '2025-07-01 00:47:54', 'unggulan', 'manis'),
(13, 'Keripik Pisang Cokelat', 28000, '250 gr', 'Keripik pisang dilapisi cokelat premium yang lezat.', 'uploads/686330615ac0b_best2.jpg', '2025-07-01 00:48:33', 'unggulan', 'manis'),
(14, 'Keripik Pisang Keju', 27000, '250 gr', 'Keripik pisang dengan taburan keju yang gurih dan lezat.', 'uploads/6863307ea10ad_best3.jpg', '2025-07-01 00:49:02', 'unggulan', 'gurih'),
(15, 'Paket Hemat Keluarga', 72000, '1000', 'Beli 3 pack keripik pisang (bebas pilih rasa) dan dapatkan diskon 20%.', 'uploads/686330d0f3b52_deal1.jpg', '2025-07-01 00:50:24', 'promo', 'original'),
(16, 'Promo Akhir Pekan', 25000, '500', 'Setiap pembelian 1 pack keripik pisang rasa original, dapatkan 1 pack gratis.', 'uploads/686330f719e92_deal2.jpg', '2025-07-01 00:51:03', 'promo', 'original'),
(17, 'Keripik Pisang Original', 20000, '250', 'Keripik pisang renyah tanpa tambahan rasa.', 'uploads/6863311b7b2fb_best1.jpg', '2025-07-01 00:51:39', 'biasa', 'original'),
(18, 'Keripik Pisang Madu', 23000, '250', 'Keripik pisang dengan sentuhan madu alami.', 'uploads/68633135d979f_best2.jpg', '2025-07-01 00:52:05', 'biasa', 'manis'),
(19, 'Keripik Pisang Balado', 22000, '250', 'Keripik pisang dengan bumbu balado pedas.', 'uploads/68633155a64c8_best3.jpg', '2025-07-01 00:52:37', 'biasa', 'pedas'),
(20, 'Keripik Pisang Kelapa', 24000, '250', 'Keripik pisang dengan taburan kelapa gurih.', 'uploads/6863319c00530_best1.jpg', '2025-07-01 00:53:48', 'biasa', 'original'),
(21, 'Keripik Pisang Green Tea', 26000, '250', 'Keripik pisang dengan rasa green tea yang unik.', 'uploads/686331be20de5_best2.jpg', '2025-07-01 00:54:22', 'biasa', 'manis'),
(22, 'Keripik Pisang BBQ', 25000, '250', 'Keripik pisang dengan bumbu BBQ yang gurih.', 'uploads/686331dee6ff8_best1.jpg', '2025-07-01 00:54:54', 'biasa', 'gurih'),
(23, 'Keripik Pisang Karamel Asin', 27000, '250', 'Keripik pisang dengan karamel dan sedikit garam.', 'uploads/686331f6def8f_deal1.jpg', '2025-07-01 00:55:18', 'biasa', 'original'),
(24, 'Keripik Pisang Stroberi', 26000, '250', 'Keripik pisang dengan rasa stroberi yang manis.', 'uploads/68633214d8278_deal2.jpg', '2025-07-01 00:55:48', 'biasa', 'manis'),
(25, 'pisang ijo bro', 23000, '250', 'mantep banget pokoknya ini', 'uploads/6864fac91a898_home.jpg', '2025-07-02 09:24:25', 'biasa', 'manis'),
(26, 'pisang tralala', 30000, '250gr', 'tralala tung tung sahur', 'uploads/68650cd3471c4_best2.jpg', '2025-07-02 10:41:23', 'biasa', 'gurih');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(11) NOT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `user_id`, `order_id`, `rating`, `comment`, `created_at`, `updated_at`, `status`) VALUES
(3, 1, 17, 5, 'produknya sangat enak banget, apalagi yang varian coklat, coklatnya sangat berasa, lumer banget di mulut dan sangat mengenyangkan dan bergizi bikin mama papa bangga, kalian ga bakal nyesel kok beli disini , ini pure dari pengalaman saya coba aja', '2025-07-02 09:37:15', '2025-07-02 09:37:29', 'published'),
(4, 1, 16, 5, 'sangat enak', '2025-07-02 09:37:23', '2025-07-02 09:37:28', 'published'),
(5, 1, 23, 5, 'bagus', '2025-07-02 09:44:47', '2025-07-02 09:45:04', 'published'),
(6, 1, 22, 4, 'enak', '2025-07-02 09:44:52', '2025-07-02 09:45:03', 'published'),
(7, 1, 21, 4, 'wow', '2025-07-02 09:44:57', '2025-07-02 10:42:49', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'naufal', '$2y$10$5ymS0MZx/654bL8WVtBN7.2NLBBpvHIgZseJuxVNQKUZMFR6LaPLm', 'naufal72k@gmail.com', '2025-07-02 05:35:13'),
(2, 'naufal1', '$2y$10$Rtgn0hitu1dKLycaaMNSsue3uFfEdVE8GTKIPNN5BchlLjcxAmAPO', 'naufal7k@gmail.com', '2025-07-02 10:21:39'),
(3, 'naufal9', '$2y$10$KLTqV7rSb0Ql16jZK.FueONhOJN.Cs2Gj3y7jpYEaX5uNEp.T0zsG', 'naufal9k@gmail.com', '2025-07-03 01:28:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`order_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `testimonials_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
