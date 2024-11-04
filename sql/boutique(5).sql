-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 04, 2024 at 07:59 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `boutique`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int NOT NULL,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `name`, `description`) VALUES
(1, 'Watches', 'A variety of luxury and casual watches.'),
(2, 'Accessories', 'Fashion accessories to complement your style.');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `client_id` int NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `first_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `last_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone_number` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `postal_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `role` enum('user','admin','support','admins') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `profile_picture` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'default.jpg',
  `reset_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`client_id`, `email`, `first_name`, `last_name`, `password`, `phone_number`, `address`, `city`, `postal_code`, `country`, `created_at`, `role`, `profile_picture`, `reset_token`, `reset_token_expiry`) VALUES
(50, 'oussama.benyamina@laplateforme.io', 'Oussama', 'BENYAMINA', '$2y$10$A1Q5GYhKwWxN/ilWc4PuROOO5KPGqErEzuSFG/LGcNKHlcLdLdW3y', '0774666255', 'parc st Georges av marius runat', 'marignane', '13700', 'fr', '2024-10-25 13:55:26', 'admin', 'default.jpg', NULL, NULL),
(53, 'garozashvili25@gmail.com', 'Konstantine', 'GAROZASHVILI', '$2y$10$0runF38wELmASKb6l0NRQOkkrguuse2Br3nUAe/F7EfWcDTLSEzFa', '0606433652', '73 chemin de saint henri', 'marseille', '13016', 'fr', '2024-10-30 15:33:35', 'admin', '53.jpeg', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `comments_ratings`
--

CREATE TABLE `comments_ratings` (
  `id` int NOT NULL,
  `product_id` int NOT NULL,
  `client_id` int NOT NULL,
  `rating` int DEFAULT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `photo_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `comments_ratings`
--

INSERT INTO `comments_ratings` (`id`, `product_id`, `client_id`, `rating`, `comment`, `created_at`, `photo_url`) VALUES
(1, 62, 53, 5, 'valaa', '2024-10-30 15:37:32', NULL),
(2, 53, 53, 5, 'this product is good', '2024-10-31 09:48:04', 'uploads/review_photos/672344448691f.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `inventory_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity_added` int DEFAULT NULL,
  `quantity_removed` int DEFAULT NULL,
  `transaction_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int NOT NULL,
  `client_id` int NOT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Pending' COMMENT 'Delivery status',
  `total_amount` decimal(10,2) NOT NULL,
  `shipping_address_id` int DEFAULT NULL,
  `admin_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `last_updated` datetime DEFAULT NULL,
  `payment_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `order_item_id` int NOT NULL,
  `order_id` int NOT NULL,
  `product_id` int NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int NOT NULL,
  `order_id` int NOT NULL,
  `payment_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'Completed'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) NOT NULL,
  `brand` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `category` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `stock_quantity` int NOT NULL,
  `image_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `description`, `price`, `brand`, `category`, `stock_quantity`, `image_url`, `created_at`) VALUES
(52, 'Watch Model 2', 'Stylish wristwatch with modern design.', '180.00', 'Brand B', 'Modern', 15, 'assets/img/products/watch-img-2.jpg', '2024-10-02 14:12:02'),
(53, 'Watch Model 3', 'Luxury watch with a leather strap.', '250.00', 'Brand C', 'Luxury', 8, 'assets/img/products/watch-img-3.jpg', '2024-10-02 14:12:02'),
(54, 'Watch Model 4', 'Sports watch with multiple functionalities.', '120.00', 'Brand D', 'Sports', 20, 'assets/img/products/watch-img-4.jpg', '2024-10-02 14:12:02'),
(55, 'Watch Model 5', 'Casual watch suitable for everyday use.', '90.00', 'Brand E', 'Casual', 25, 'assets/img/products/watch-img-5.jpg', '2024-10-02 14:12:02'),
(56, 'Watch Model 6', 'Elegant design with a stainless steel strap.', '200.00', 'Brand F', 'Classic', 12, 'assets/img/products/watch-img-6.jpg', '2024-10-02 14:12:02'),
(57, 'Watch Model 7', 'Minimalist design for a sleek look.', '160.00', 'Brand G', 'Minimalist', 18, 'assets/img/products/watch-img-7.jpg', '2024-10-02 14:12:02'),
(58, 'Watch Model 8', 'Rugged watch suitable for outdoor activities.', '230.00', 'Brand H', 'Outdoor', 9, 'assets/img/products/watch-img-8.jpg', '2024-10-02 14:12:02'),
(59, 'Watch Model 9', 'Digital watch with LED display.', '110.00', 'Brand I', 'Digital', 30, 'assets/img/products/watch-img-9.jpg', '2024-10-02 14:12:02'),
(60, 'Watch Model 10', 'Automatic watch with self-winding movement.', '300.00', 'Brand J', 'Automatic', 5, 'assets/img/products/watch-img-10.jpg', '2024-10-02 14:12:02'),
(61, 'Watch Model 11', 'Sophisticated design with a chronograph feature.', '220.00', 'Brand K', 'Chronograph', 10, 'assets/img/products/watch-img-11.jpg', '2024-10-02 14:12:02'),
(62, 'Watch Model 12', 'Luxury watch with diamond-studded bezel.', '400.00', 'Brand L', 'Luxury', 6, 'assets/img/products/watch-img-12.jpg', '2024-10-02 14:12:02'),
(63, 'Watch Model 13', 'Sports watch with heart rate monitor.', '140.00', 'Brand M', 'Sports', 14, 'assets/img/products/watch-img-13.jpg', '2024-10-02 14:12:02'),
(64, 'Watch Model 14', 'Vintage-style watch with a leather strap.', '190.00', 'Brand N', 'Vintage', 11, 'assets/img/products/watch-img-14.jpg', '2024-10-02 14:12:02'),
(65, 'Watch Model 15', 'Analog watch with minimalist design.', '100.00', 'Brand O', 'Minimalist', 20, 'assets/img/products/watch-img-15.jpg', '2024-10-02 14:12:02'),
(66, 'Watch Model 16', 'Smartwatch with health tracking features.', '250.00', 'Brand P', 'Smart', 8, 'assets/img/products/watch-img-16.jpg', '2024-10-02 14:12:02'),
(67, 'Watch Model 17', 'Casual watch for everyday wear.', '85.00', 'Brand Q', 'Casual', 22, 'assets/img/products/watch-img-17.jpg', '2024-10-02 14:12:02'),
(68, 'Watch Model 18', 'Luxury watch with a ceramic strap.', '350.00', 'Brand R', 'Luxury', 4, 'assets/img/products/watch-img-18.jpg', '2024-10-02 14:12:02'),
(69, 'Watch Model 19', 'Classic watch with Roman numeral dial.', '170.00', 'Brand S', 'Classic', 13, 'assets/img/products/watch-img-19.jpg', '2024-10-02 14:12:02'),
(70, 'Watch Model 20', 'Digital watch with multiple time zones.', '130.00', 'Brand T', 'Digital', 17, 'assets/img/products/watch-img-20.jpg', '2024-10-02 14:12:02'),
(71, 'Watch Model 21', 'Automatic watch with open-heart design.', '320.00', 'Brand U', 'Automatic', 5, 'assets/img/products/watch-img-21.jpg', '2024-10-02 14:12:02'),
(72, 'Watch Model 22', 'Rugged sports watch for outdoor enthusiasts.', '145.00', 'Brand V', 'Sports', 19, 'assets/img/products/watch-img-22.jpg', '2024-10-02 14:12:02'),
(73, 'Watch Model 23', 'Vintage watch with a classic leather strap.', '195.00', 'Brand W', 'Vintage', 12, 'assets/img/products/watch-img-23.jpg', '2024-10-02 14:12:02'),
(74, 'Watch Model 24', 'Luxury timepiece with gold finish.', '420.00', 'Brand X', 'Luxury', 3, 'assets/img/products/watch-img-24.jpg', '2024-10-02 14:12:02'),
(75, 'Watch Model 25', 'Minimalist watch with clean dial.', '105.00', 'Brand Y', 'Minimalist', 18, 'assets/img/products/watch-img-25.jpg', '2024-10-02 14:12:02'),
(76, 'Watch Model 26', 'Chronograph watch with stopwatch functionality.', '210.00', 'Brand Z', 'Chronograph', 11, 'assets/img/products/watch-img-26.jpg', '2024-10-02 14:12:02'),
(77, 'Watch Model 27', 'Stainless steel watch with a sapphire crystal.', '260.00', 'Brand AA', 'Classic', 9, 'assets/img/products/watch-img-27.jpg', '2024-10-02 14:12:02'),
(78, 'Watch Model 28', 'Casual digital watch for everyday use.', '95.00', 'Brand BB', 'Casual', 23, 'assets/img/products/watch-img-28.jpg', '2024-10-02 14:12:02'),
(79, 'Watch Model 29', 'Smartwatch with Bluetooth connectivity.', '280.00', 'Brand CC', 'Smart', 7, 'assets/img/products/watch-img-29.jpg', '2024-10-02 14:12:02'),
(80, 'Watch Model 30', 'Outdoor watch with compass and altimeter.', '225.00', 'Brand DD', 'Outdoor', 10, 'assets/img/products/watch-img-30.jpg', '2024-10-02 14:12:02'),
(81, 'Watch Model 31', 'Classic analog watch with a leather strap.', '160.00', 'Brand EE', 'Classic', 15, 'assets/img/products/watch-img-31.jpg', '2024-10-02 14:12:02'),
(82, 'Watch Model 32', 'Digital sports watch with multiple alarms.', '125.00', 'Brand FF', 'Digital', 16, 'assets/img/products/watch-img-32.jpg', '2024-10-02 14:12:02'),
(83, 'Watch Model 33', 'Vintage-style wristwatch with a large dial.', '175.00', 'Brand GG', 'Vintage', 14, 'assets/img/products/watch-img-33.jpg', '2024-10-02 14:12:02'),
(84, 'Watch Model 34', 'Luxury chronograph with multiple subdials.', '390.00', 'Brand HH', 'Luxury', 5, 'assets/img/products/watch-img-34.jpg', '2024-10-02 14:12:02'),
(85, 'Watch Model 35', 'Minimalist watch with a monochrome design.', '110.00', 'Brand II', 'Minimalist', 21, 'assets/img/products/watch-img-35.jpg', '2024-10-02 14:12:02'),
(86, 'Watch Model 36', 'Casual quartz watch for daily use.', '80.00', 'Brand JJ', 'Casual', 26, 'assets/img/products/watch-img-36.jpg', '2024-10-02 14:12:02'),
(87, 'Watch Model 37', 'Classic automatic watch with a transparent back.', '270.00', 'Brand KK', 'Automatic', 8, 'assets/img/products/watch-img-37.jpg', '2024-10-02 14:12:02'),
(88, 'Watch Model 38', 'Sports watch with water resistance.', '155.00', 'Brand LL', 'Sports', 17, 'assets/img/products/watch-img-38.jpg', '2024-10-02 14:12:02'),
(89, 'Watch Model 39', 'Luxury watch with a diamond-studded dial.', '450.00', 'Brand MM', 'Luxury', 4, 'assets/img/products/watch-img-39.jpg', '2024-10-02 14:12:02'),
(90, 'Watch Model 40', 'Smartwatch with GPS and fitness tracking.', '300.00', 'Brand NN', 'Smart', 6, 'assets/img/products/watch-img-40.jpg', '2024-10-02 14:12:02'),
(107, 'apple watch pro 7', 'apple watch', '700.00', 'samsung', 'watches', 123, 'assets/img/products/6703a730bd1ac_images.png', '2024-10-07 11:17:36'),
(108, 'Apple Watch Ultra', 'its apple watch', '800.00', 'Aplle', 'Watches', 10, 'assets/img/products/670587d641ccc_91XlXLiUDFL.__AC_SY445_SX342_QL70_ML2_.jpg', '2024-10-08 21:28:22'),
(110, 'apple watch 3', 'APPLE WATCH 3', '200.00', 'APPLE', 'watches', 15, 'assets/img/products/670e82b5aaf2a_images.jpg', '2024-10-15 16:56:53');

-- --------------------------------------------------------

--
-- Table structure for table `product_reviews`
--

CREATE TABLE `product_reviews` (
  `review_id` int NOT NULL,
  `product_id` int NOT NULL,
  `client_id` int NOT NULL,
  `rating` int NOT NULL,
  `comment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipping_addresses`
--

CREATE TABLE `shipping_addresses` (
  `shipping_id` int NOT NULL,
  `client_id` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `city` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `postal_code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping_addresses`
--

INSERT INTO `shipping_addresses` (`shipping_id`, `client_id`, `name`, `email`, `address`, `phone`, `city`, `postal_code`, `country`, `created_at`) VALUES
(45, 53, 'Konstantine GAROZASHVILI', 'garozashvili25@gmail.com', '73 chemin de saint henri', '0606433652', 'marseille', '13016', 'fr', '2024-10-31 11:24:17'),
(46, 53, 'Konstantine GAROZASHVILI', 'garozashvili25@gmail.com', '73 chemin de saint henri', '0606433652', 'marseille', '13016', 'fr', '2024-10-31 11:28:15');

-- --------------------------------------------------------

--
-- Table structure for table `subscribers`
--

CREATE TABLE `subscribers` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subscribers`
--

INSERT INTO `subscribers` (`email`) VALUES
('garozashvili25@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `session_id` int NOT NULL,
  `user_id` int NOT NULL,
  `login_time` datetime NOT NULL,
  `logout_time` datetime DEFAULT NULL,
  `signature_time` datetime DEFAULT NULL,
  `signature_image` longblob
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_sessions`
--

INSERT INTO `user_sessions` (`session_id`, `user_id`, `login_time`, `logout_time`, `signature_time`, `signature_image`) VALUES
(44, 50, '2024-10-25 13:55:26', NULL, NULL, NULL),
(45, 50, '2024-10-25 13:55:36', '2024-10-25 13:55:56', NULL, NULL),
(46, 50, '2024-10-25 13:56:05', '2024-10-25 13:56:51', NULL, NULL),
(47, 50, '2024-10-25 13:57:00', '2024-10-25 13:57:16', '2024-10-25 13:57:12', 0x89504e470d0a1a0a0000000d4948445200000190000000c80806000000c615b7e2000000017352474200aece1ce900001c6c49444154785eed9d05b46d477d877f68700d0e0d0e058a43f106870015bc588162c5a1b886a2c10a8514089240702d146db1e22ec5bd788b1777d8df5b73dacdce3937e7ee7bb69e6fd6baebbdbc7b66cfcc3793fd3b33f39763c4220109484002126841e0182dea58450212908004241005c445200109484002ad082820adb0594902129080041410d7800424200109b422a080b4c26625094840021250405c031290800424d08a8002d20a9b9524200109484001710d4840021290402b020a482b6c5692800424200105c435200109484002ad082820adb0594902129080041410d7800424200109b422a080b4c26625094840021250405c031290800424d08a8002d20a9b9524200109484001710d4840021290402b020a482b6c5692800424200105c435200109484002ad082820adb0594902129080041410d7800424200109b422a080b4c26625094840021250405c031290800424d08a8002d20a9b9524200109484001710d4840021290402b020a482b6c5692800424200105c435200109484002ad082820adb0594902129080041410d7800424200109b422a080b4c26625094840021250405c031290800424d08a8002d20a9b9524200109484001710d4840021290402b020a482b6c5692800424200105c435200109484002ad082820adb0594902129080041410d7800424200109b422a080b4c26625094840021250405c031290800424d08a8002d20a9b9524200109484001710d4840021290402b020a482b6c5692800424200105c435200109484002ad082820adb0594902129080041410d7800424200109b422a080b4c26625094840021250405c031290800424d08a8002d20a9b9524200109484001710d4840021290402b020a482b6c5692800424200105c435200109484002ad082820adb0594902129080041410d7800424200109b422a080b4c26625094840021250405c031290800424d08a8002d20a9b9524200109484001710d4840021290402b020a482b6c5692800424200105c435200109484002ad082820adb0594902129080041410d7800424200109b422a080b4c26625094840021250405c031290800424d08a8002d20a9b9524200109484001710d4840021290402b020a482b6c5692800424200105c435200109484002ad082820adb0594902129080041410d7800424200109b422a080b4c26625094840021250405c034313b84892db949ff727396d92b726b973921f0cdd39db978004561350405c1d4310384f925b24b968251607aee8c0dd923c6188ced9a60424b01e0105643d4e7e6a33042e93e4a14b44e38349fe37c9156acddc23c9e336d3ac4f918004ba20a0807441d5673609b0d378cc12e1382c09e27144928f273947a9f89624d7f608cb85248171135040c63d3f53efdd79933c3ac941b5817cafbae7786a9243ab7b8f6f947f7f7892fb95bfffbc08cd7ba73e78fb2f81b9135040e63ec3c38cefcc490e4972fd5af35c88b30bf9a7243faefdfb2593bc39c9f192fc22c953aae32cee3f2c1290c0c8092820239fa089758ff5f40f491e50ebf7a792fc73d975fcba319e332479779233957f7f43929b24f9cec4c66d7725b095041490ad9cf64e067dbe242f4972eef27484e18149b8cff8ed921631df7d454d3cf8cc5592bca993def950094860e30414908d23ddca07727ff1e024c74df2d324774a7264925fada071c3244f4e72cadaefef528eb7b612a08396c014092820539cb5f1f4f954499e5b760ef4ea5f8b787c79872ede7d89792ea6bd1c7d358fb8c633527b2201091c858002e2a26843e038c573fc89955fc7b192fcacdc7b3c7e87879db0ec52eed9f8cc3392dcae3aeefa4d9b8e58470212188e8002321cfba9b67cb172b771ad3280af54565497ab3ccb77da75fc71f1f5b8786dd008060284c3e0efa60ac37e4b609b092820db3cfbbb1bfb314b7c2aee3a4e96047f8dfb54f7184f5a7149ced331cdfd9b2214fbd59afb65e52478d762b2bbbb5ef4f769764cf8af70afc3053f773bc74f7292322e7ecfcf89939c2809bbb2a727797b7f5db425090c4b40011996ff545abf44128e9a700ce465fad224dc657c7d8701b0434124eae149f8f8e292fd591d0efeac49be598ed69acde0a372e5247f5b8edff092efa2701ff4ea24cf2efe2d5db4e13325302801056450fc9368fc41491e527afad512047127535b7c3bd8a5dc7ac9e8108fcb57dfecdfd7d1c8714abc6f151e05f1c25911e143e4feb2fcfd8c49aed151dbab1ecb511dfe2ddf4df2e2222a3d77c1e624d00d0105a41bae7378ea59caf1142f5c2ec9093f72ff15dfea192fc738fc9e9d09c73ecdf2ae222c6fdc301c2ef1f137b97d151e05abb07a794f12ee5f4ebaa24dacbe88c5c5711ce593d573be55fb2ce2f3b94a40f9ff8431e129df2c1c5d215c1cf12150672fbe30a759d1e6b7937ca0da99bd2dc9d3927c7fc33c7c9c047a23a080f4867a520dfd55f1e3e08c9f172821493eb2c3084e5d7dc37f7d920b2df9cc97923c2cc9e11bbe2ce7988a63a87b55cf3ff61a7479519367849737794710971fad51afed47c86b72dd24572abb2eee4e9a05e1fa6c25382f2cf741e63f694bdb7a8310504006c13eea4679d9b393e0ae83b8541c6111007155597cbef97b5e865c2a739cc50e6653e502d5830e2ec7523b3d93f0f0af49f2fc245f4c424895a10abb932b56a275b52226cb8496be7db4307f41b593fbe1509db55d09ac4b40015997d4fc3fc7377a2e7cc9d9c1fd01dfee39b35f5538b2e2e5bc30e7ad7fee3f4afd2f6c101b165d8f4d72871d9ec92e83b85b7ca3273cfc58cba58a8517e6cf975dd2498ece1e510c17b058b3486094041490514e4bef9de245869515f93838aabad1d17c633f7d92d756b1acd80dd4cb4faaddc6bdcb7dc9a61c03b963601784bf0822d22cdc5960d185c513f1b796c5ddea1de82e1a3c45095f8fd101c75df5e3388ed9ae97e46bbb789e1f95406f041490de508fb62152cb12629d1d05c1106f7a3466a7172c2f6bacad16859857af2a2f7a2ea23759feacdc5dd49fc9f11849a8fea532c97de78c42a070f1cef1dc6dcbc53d636637c2d1d79877549b9c6f9f3521020ac884266bc35dc5018ee31ec2a7638dc4ce811ce43b7d83476ca853df09609984bfc73377089eb897aee3a7c1a53785bb0c7285e0633167ef754c9db1d05a646864ec372e47867b61695d096c948002b2519c9379182f28ee09b09efa7cf1d9c0426955d9bf5cee6255542f1faa2e7eb1d8229c4997e59c459cb0e8da9672f2620957f75b31e8e4b6ccfe44c6a9804c64a236d4cd034a24dceb94e771dcc4b93b5edbcbca2200e2adaa7024bcd01605f357b20b72a9bd490bab0d0d73368f813fe16230555ef8d6605986902f7c5766335807323d020ac8f4e6ac6d8fb1eac18269e18f80f31db9c8571d05e1bd4d9c2b9ce3ea85cb6a9ed3f5aea3ed38e7568fff47ffbee4965ffcffcafdcf2d677e8c37b7799ce5781490594eeb1f0c0a9f039cf81616530806c99b108765e5aae55bef81b55f6251c5375f12477d62fec8463942acb4885e4c40470abe22dc8bccf92e68941361a7fe9f800232dfd5c08b868b71f26f9ca00c935ce35c9a139ba959f83c499d30975d147c1030d7253cc936dd3f8c75551008f2cd95bf0e616628982fe3af6391c02004149041b077dee89f267959e5095e37b5e5c8096f682ecd9b056b9f434b94dac5ef38f2e2dfbed1796f6d60370498d31725b974a9448e958595da6e9ee36725b067020ac89e118eea0198e67234450e8e7a219e151ee39f59d25bbec162be8b1f08851d07c7250ac7a8a6f60f3ac33dd62b8b03a2bb90f1ced3ec7ba680cc678acf5f761d4483ad17c293dcb18427a9fffb298b03215ee78b420c2c9c0a09656219370142d5133206a7ca55917fc73d027b3779020ac8e4a770df0030eb442816771d8b51e1ebc19d4733ac08be05c4b15a5864918ef60649de3b0f1c5b310a8eb23e56ccab1113e76e2ba67d5c835440c6351fbbed0d7e0288c4351b15f1d3c0f20aafedba6739f9321e57c2952caa3cb95cb4eb57b05bfac37f1e01395ff113c12fc722815e092820bde2de686304e1c39aaa999215b34e2caf1edf30f1c49bfbc8ea8e834b570ae1c209d4f76f1bed950feb930016738724794b7108edb36ddb92c0be4c6b96e911e09ee37525fb5dbdf7c4b4222e15f1aaeafe01dc81f00d7511c38ab855a47e253b9e65ba04f832f08e7244c99d1629832d12e88d8002d21bea8d3584892e9652ec40ea8523282caab8db581452b9e2b58c573985cf90000a0f74cbf4096039b7c8aa78ae92dd70faa372049321a0804c66aaf67514816077d1cc39ce6e83785598742eca794bb8f3855516d9ee6e7e34a969a745c3de42809cee174e82151e77221609f4464001e90df59e1be2e57f58b57b20c152bdf0d2b85392b7d58eadf028e7280bbf100af5f02627e193655e04b8d7c2d28ea8c8e447b148a037020a486fa8f7d41031a8961d3b91f7fbcf8b782c1ac02a0b935c0ae6b95ca6e3db619927017c773896c4949be8031609f4464001e90d75ab86486ffa8fc511b0f9001cc8d895e0914c21dc3a010f2f59fe1bcb9c1b2621e5ab65be04883ac03d17c9bef8d32281de082820bda16ed5d0731a3e1b8b87606dc3374e2cb128dc73201e98ea72514e0c2bc2b59baba315f64955ba76d979e00bc41ab048a037020a486fa877d5d0c992e0e047b8ee6621852c9ec71f29bf2067f82bca0e84dd06475a7a25ef0af7a43f8c7936f9424e97e47f263d123b3f39020ac8f8a68cf022e47a386849d738b6225fc7fbcaefb828e7888b82432096585f1ddf90ec5147044e5fc2ecb30e9ed2511b3e56022b092820e35a1cf86d3caf0acf5dcf83bde8e117cb9dc62274f74b2bb3cd456ada0725215fb665bb08bca498f0b223d52974bbe67e14a3554046310dfb3a8179ee8793e0bfd12c8463c7b28adf535e5f7622fc9d78578463b76c1781eb97bc2038892e0c29b68b80a31d9c800232f814fc5f07f0d320d061b360598348707cb57f79595caa1c5de05888b59565bb08e0dfc3970a84e3b6db3574473b26020ac87866e3535564dd73d7ba8377393b8b4796e309723ebcb30a8878b6ca690caf72ac6ebe369eeedb931e09901b9d3c2e58dfe10b6491c02004149041b01fa5d13b575654bc14eae5dd258be0778b6fc7cb939cb67ceb5cc4b61a47efed455f04d8793cb5880711971fdb57c3b6238165041490e1d70596341c47d49341b1d3c0be1fb35cee3e786960da4b04dd470ddf657b300081e397dc2fe442c76c97046216090c4a40011914ffbec67106bc5aad1b1f2826bc58d5dcacbc2838a6b86515308f5d8865fb081075973025585b5dbef2f5f9d0f62170c46324a0800c3b2bcd8b73723b109a0293dd7b97dd06c112110f84c5b27d04c8f3c18e1411b9587569fecded43e088c74a4001196e660e2897e1f87e50f01e6727c2e5399ee57cd37c7339efd6c378b8791ab265d6085676e4b4673d683431e46cd8f651082820c32d8aa725b94d691eab2a5e10c72c4715842721efc77d6a098386eba92d0f41000b2b761e8806d107be3344276c53023b11504086591f648ffb7469fa9325aa2e3b0ff239902ceaa625dff930bdb3d5a1091c58aced581b573455edd0d361fbab082820c3ac0dfc3bee92e4b7c579903856ff5e45d8fd78b9385f789c0fd33b5b1d920017e5441a20353131ae7e3964676c5b02ee40c6b506d861609ecbddc77baad025c433c2039dd4a444d2fdc6b8ba6b6f7a24807f0f81345f5d8c2908db6f91c06809b803e97f6a0eae2eca1f5c9afd7cf126fe52b503c1be5f0b9bfee7630c2d1eaba42526b2321924ef552ecec7d037fb208195041490fe1707f9ca49f644f975e51c487e0fceb9cde1d1ff5c8ca1451c485f5459dc5dae986ee3346a91c024082820fd4f132f8c43aabc1d0442e47c1b0b1bc5a3ff7918438b572ae1fb31d3bd7a31eb1e43bfec8304d622a080ac8569a31f22cff9274a50c48798c763a36ca7f4b00794b97f46654c413e178f2fa7347bf6751f0105a4ff85f0e82404c263d7c137d01ff7df055b1c90009106b807a33c2cc93307ec8b4d4b604f0414903de1db75e55b2739acdc7b5cb0e60bb2eb07596172042e522ec83196208f39bbcf9f4f6e1476580235020a487fcb01c120081ecc8973c54ec4327f0244d1c5b2ea76552cabd794dd27b95f2c12983c0105a49f29bc5079799cae98671220cf4440fdb01faa15e69add2689bff8e240ce1742935824301b020a48f753c98be46dc5dfe37bc5df6311c6a4fbd66da16f0217286966af57e29871598e57b94502b323a080743ba5fb156f738eaff0f9b88a39ccbb053ee0d349f88579360132bf9ae4fe95a5dd9103f6c7a625d0390105a45bc4648e23ede8afca510631af2cf32240d45c3cc7ffa2445366ce9f5be29ccd6ba48e46020d020a48b74b829846d74882ad3f165896791120dd3071cc4e52c536230cc92244cdbc46e96824b0828002d2edd27871e534c859f8d72b113963b74df9f41e09e0bf7344923394302498e46a59d5e304d8d438082820ddcec3ed931c5a9a386792cf75db9c4fef98005f06ee91e4e225a2329655c4b1b248602b092820dd4e3b471c2f2c4d90d7fcf06e9bf3e91d11205e191922cf5a9e8f2739bb0e8b04b69a8002d2edf43fa2f23abf6f6982f857372c49a3ba6dd5a76f8a008241f4e41b9707923112eb2a32055a24b0f50414906e970009825e516b82b85744e1e56ec4325e02a72f810ed9355248f27590d172c73b61f66c18020a48f7dc6f51bec5d62fd1f150be6df74ddbc22e0970b78155d5654abdef17df8e2799977c9724fdf856105040fa99e6b325794309e1be6891fce75748f2ed7eba602b3b103855894d76f3da6730cb255a2ed1032c1290c012020a48bfcbe2e9496e556b12f1200f3ab9d12dfd134038ee5289f81d92e0494e795d15a7ec6e493ed37f776c5102d322a080f43f5f1c693dabd12c9ecc84f8b6f44300c73f4c70118a53d49abc519217f4d3055b91c0f4092820c3cce1458b792f475b8bf2921247e907c374696b5ac5378770fa7f541bf1d3cabf1921796b968103dd040105641314db3de33425fcc55fd7aa1384ef3a55f893f7b77ba4b5762040ac2a821d9eabf6198e0e093f628c32978e045a1050405a40db70152c7ceed87826c72bfcbb65ef042e51a2015cb8f628761a043d349decdef9fa842d26a0808c63f2af5e79393f27c9feb5eebc2309f9b3bf388e2e4eae171c133eb43250b85aa3e798e9e245fea3c98dc80e4b6064041490f14c0867f2cf4e7260ad4b3f29e6a578435bd623d074025cd422323271acb4ae5a8fa39f92c0d11250408e1651ef1fb86bb91ba937fce52a15ee034d50b4e35c9cbaec2cc83d5e2f8490b95f9257f53e9336288199135040c639c1e728e7f6576e748fdcda4f2cc75de3ec79ffbdc218e1ef8a2f47fd08f0a7e582fcc9497ede7fb76c5102f327a0808c7b8e6f5682319ebbd1cdff4ef2a824989f6ef3cbf1ee25b861dd9703546404c410815024160948a023020a48476037fc58e26671b4d514129a41440819ffd60db739e6c791771ce7cbba1f0dfd2570e5838c783ce6a9b36f7322a0804c6b36f194e68734b9cdf29124841be7c8e6bbd31ad65abd3d6109fbf2842a3707f71df5f29fe518eb5d6b3dc90f4940021b21a0806c0463ef0f394b89e64b9e8a65a972bf54acb7d899ccc1b31d535ce28835c7fad912f0f0c8de67c0062520812820d35f04841ec711915dc989960ce779490893f2ca090ef5cce588ee2a8dbee3b18f69f333263826bb2c81d9105040663395fb06c28e84901dd7aa2ed98fd718da7792bc29c9eb93bc6ce48e74589f91bd7191d0a93e14a2e7128c92e45c1609486040020ac880f03b6cfa9455dad5eb172f6cc2c5af2a9f4bf2a924ef2e21e587be88ff93240f2f0258ef3339395e5e761dec3e2c1290c0080828202398848ebb80985cbbf8495c608db6f0d47e63751cf6d824ffb5c6e737f191f3169f8deb351e8689f25393905bdec45b9b20ed3324b041020ac806614ee0510754f9d8cf93e422956736c105cfbfc414b63e0ce2707d2dc9f3cbb117c7609b2c043abc4f3976ab3f971ce447147f0e76481609486084041490114e4acf5d2213dfa5925c3ac9c5aa3b125eea275ed107ee1d3015fe58090df2e9967de5c29f68b8976fd4c741f2f1e5e2fc872d9f6d350948a027020a484fa027d6cc39ab7b91ab2639684934dbe60e859c1afce0f58d07f8aa7282248f2c1ee2cdcf7ca58468c154d728b9135b2c76777b092820db3bf7bb1939bb922b25c13aea726b544404b898ff5625185c809fbcf20ebf6e95d0e9f88dba3f2b499e1e5d6504e4ef1609486042041490094dd648ba8a0870e485ffc9e2e80b2ff1dd965f551657c7a9627dfda2da99ec572eecb96f213b207f622186c51591882d1290c008092820239c940976897b940b5617ed98e192d7849c2617aa123a1d6b8db12c0464a78f7ea0b224235c0977305cb06329864f8bc75d6b00f62312e88a8002d215d9ed7b2ef7268f29f1aa9aa32746d5e19505d88b93acba1ce7e29e2c82fcf0f78b5717ea274972c9a34149d81604e59349be501db591c911b1b14840021d1350403a06bc058fbf6c8947b5ec6e84e327840361213f47db82751801141197b357b9cccf54656f241e18bb9d5585dd0997f388cafb8a7011fac4bb96b6b3603d09340828202e89b604f029214b22e1469aebe8bd49ee592ed27fddb68135ea11ae859d0ace92384912de9d7e216a3b15ee583e5e79bc7fb08a6e4c40467646db9c57650dd47e44024725a080b82a764b809ce30fab2eb76fd1a8f8db2218788dbf76b70fede0f3ec58f07067d7c28e05a749c485008dab0a3b164c9249838baf0b22c365be450212584240017159ac4b00eff583935c734905626811119817ef140a16648b7b16ee6e10161c29571544853b96af172bb15f9663b1298cd53e4aa033020a48676867f3e0d326396c49804306f8f6b21bc122ea37331831a971d9a970bfb208fbc24e86f02fa76a8c0f6300762c78cf7364f7e122301ff59e65062bc121ac454001590bd3567ee874c573fc260d735c84e245e5629c2c88db54d8b9ec5f4c961118848634c3a7694040545e5025f5c2fb9e7b16acd0b8afe138cc102ddbb462663e560564e613dc6278f87370544518131cfd1605c73f72891c52ee3ab8f3d84be11b3df94b688375b878deef8ac516edf16f584d7111cf7fe3d9be53dada93168ff96357117c8f9b84ffe64f9c1f3982e32e84973a9ef17ce69b65d78000f043dbbce8b118e3f71c55e1cb421f104e7ee80b3f8bfe72f9cedf49e675cc4a5871aae48731313676353cab5978268e92847fc1528d8b7d8b042645400199d47475da59021cde6ec51d07df9c89c4cb4b8f9725c73cbc4c7f52fe9d50278bb28e63e05e06c2cb7bf142a62fbce49bc9b3f6f2fc21eac20cb1e34f8b042643400199cc5475d651c2ba3fb18426d96b23e4ece0058f69ecba0551c24f836fff3b15acbfb8f0ae1776071c237164c40e63aa8590f59821b3cbb2486032041490c94cd5463bca37f81b941d07e7fa3b15bcba395e219c08473e8b10ee44dfe5c278cc85e338c2ac340beb1e0f778eb7ba2a081e8c96392e7214870102a6c216094c96800232d9a96bd5718e49b0985af56d9d3379ee390868c8e52f61422c1290800496125040b66b61dcbe32473db43164ee31b01822570719082d12908004d622a080ac8569361faa0b08c28145d5e3f618a76a36701c880424b03b020ac8ee784dfdd344b7c5af0333d5e7d44c51a73e2efb2f01090c4040011900ba4d4a40021298030105640eb3e818242001090c4040011900ba4d4a40021298030105640eb3e818242001090c4040011900ba4d4a40021298030105640eb3e818242001090c4040011900ba4d4a40021298030105640eb3e818242001090c4040011900ba4d4a40021298030105640eb3e818242001090c4040011900ba4d4a40021298030105640eb3e818242001090c4040011900ba4d4a40021298030105640eb3e818242001090c4040011900ba4d4a40021298030105640eb3e818242001090c4040011900ba4d4a40021298030105640eb3e818242001090c40e0f715ff9cf6bcbf6eaa0000000049454e44ae426082),
(48, 50, '2024-10-25 13:57:24', '2024-10-25 13:57:43', '2024-10-25 13:57:38', 0x89504e470d0a1a0a0000000d4948445200000190000000c80806000000c615b7e2000000017352474200aece1ce900001af449444154785eed9d05b46d477d877fb80487e01e2c68d0e01282bb2468819060c1adb4b8164b58415aaca5a52d0442712750dc83142768b136a4344008055aa0f3adfe0feb707bdfbbe7ee7b64efb3bf59ebae97bcb767cfcc37b3f6efcefc654e168b042420010948a003819375a86315094840021290401410178104242001097422a08074c26625094840021250405c031290800424d0898002d2099b9524200109484001710d48400212904027020a48276c5692800424200105c4352001094840029d0828209db0594902129080041410d78004242001097422a08074c26625094840021250405c031290800424d0898002d2099b9524200109484001710d48400212904027020a48276c5692800424200105c4352001094840029d0828209db0594902129080041410d78004242001097422a08074c26625094840021250405c031290800424d0898002d2099b952420818110d833c9c149ee9164ef241f48725c92eb26f94492db0f641cbdeca602d2cb69b1531290c00e099c27c9bd93dc3fc91b927c38c995933c3cc969eaddef6e2272d31db633eaea0ac8a8a7dfc14b60ed08201c8f497281241f4cf2824d46f8b124574bb24f922fad1d81250e480159226c9b928004164260af763475fd24c726b943927f4c72cc2e5aba4e920f2571f73187a95040e600d1574840024b25c0ee62df24374972d524bf49f2b624ef29bbc6ee3af3d6262eb76ccf5f25c96796daeb356c4c0159c349754812583302974c72e33a72ba6609c617921c95e4ab49be39e378119eef2539b2ed56ee3a631d1fdb0d0105c4e5210109f48dc08592dcb61d35ed573b8cdf26f95492d7b4bfff7c926f74ecf033933c32c95d92bcbee33bac364540017139484002ab2670fe261037afa3256c14a74df24f251aefad5dc64efbb84792ef243943927335213971a72fb47ea280b80a242081651340306e57b11808061ff43795ab2d1e52c467ccbb3c36c9d3933cb7bcb4e6fdfe51be4f0119e5b43b68092c9500b6875b3797d93b36bbc5a5939cb389c73f27797f05f67d64c1bdc1438bb6b0975c31c9af17dcde685eaf808c66aa1da8049646801d053b8bbb25b948922b54bcc5bb921c5dde52cbeacc599b6bef176b977383248b16ab658dab17ed2820bd98063b21814113387b92db54a4f7ad2a88efbbcd00fef672afe548eaa72b18e1999bbbef3b5bccc735dacee3cd756cf6fb15f4636d9b5440d6766a1d9804164a002f298e86389a6297f183327abf25093f272cb4f5ad5f7eca128ffdcb75f772497ebe75359fd80e0105643bb47c5602e32540ce281210e22dc591d42f937cb47eb36787d1b7a0bc7724b959929fb463b36b35d7ddaf8f77ea1637720564716c7db304864ce0b2cde87ca766af20708fbc51b8bfe221450a10a2b93fd7e3c1bd34c97d4be4c8c26bccc782264b015910585f2b818111386f923bb7f80b84e3a0ea3bf9a43e3eb5d3f8d500c6f4882487573f1f96e4f903e8f360bba8800c76eaecb804764ce08665582601e1b9eb3776e231f8e158eafb3b6e61b92f784a922756932f4b72bfe5363fbed61490f1cdb9231e2f016220b00be031c5b11485e31d761a24231c726af307253922c929ea888d4ba48e1fef542f67e40ac87238db8a045641e08249aede8ea06e54c66f8ea94865cead7c9f2ed15845bfe6dd26e2f1c27ae967eb9228c563de9437799f02b204c83621812511b858e593ba5e25223c53251e249f143f18c04f5a525f96d50cde61c47a50c8b44bb0e0b797d5f8d8db5140c6be021cffd0091c5051dfc46410eb406af3c92e83f41dc467ac6b39df86f131fe211fc30d6e9e1490c14d991d1e3901eef5c6bdf6da15614d843757b7e22dc5bddfb8da8ea1e0564c6a14623c28d8750860b42c918002b244d83625810e043896229a9a0b95c8604be16e0c6231c8eb843d638c856b6bc9b5457945f318c3686e5932010564c9c06d4e02331020f88d886f3ca54813f2b5f2964238fc2d3b998ef5c0fec1ed82abc8b535c354aef7230ac87acfafa31b06812bd56fd3b74f72e1120c44e375cdbef1be24c70d63184be925e95438b2a37cb98416e3b965050414901540b7490994605ca6ec19172ddbc5cbeb78ea2b12da9400a9d9b90b9d0ba9feb3795bb15323e3af65450414901581b7d9511238b44579ef93e43e357a9211fe75bb64e9a88a021f25946d0c1a61dd3b0929551e9de445dba8eba30b20a0802c00aaaf944011384fdd8dc1cee2f2f5776489c5538ae3a96325353301181e524fbfaab92bdf7de69a3eb830020ac8c2d0fae29112e0263e32d8e26a7ba924a72bc140385e5b711a2345d379d88f6cd9800fabda6401e6d2aa1f767e9b15e7464001991b4a5f34520267ac0f1a8909f76d3b0bec1a149211723485119c737b4b3702c47790dc91f29d4acbf2ad6eafb2d6bc092820f326eafbc64080e86f3ca7c8314560dfa410c87764e5981a5a26db3ece1b91e5d3e24b8a16a2ec2d3d21a080f46422ec46af091c58c752b74cb2d7869ebea7c56b703cf54a6311e63a872482e496c3739483c1bdeb0870ae8df8b29d11504076c6cfdaeb4900032dbfed12c837317e4f46caf1c91b5a0a8da3eb98eac4f544b0d2519db9a59dff64dbcd5db23cae9edd7af3e495f6c8c63725a080b83024f07f91ccfb9568903a64ba9c5037f2bdb104c3188dc5ae9873b5dd1ebbba8970e3aafbe0c536e9dbbb125040ba92b3de50099cb6a5fde63884dd05b9a54879beb1909890f4e77cc8c83765590e01e6860bae48e342d15d7739dc3bb7a280744667c50111c07681370f7f7275eb668281b714828121dcb27c027b549e2f768294bf6a370a3e70f9ddb0c5ed105040b643cb678742e0e2496e5bd7b772c1d0c6c2f93a3b0c722a61cbb0ac9600360f2ebbc20d9a42a6e10718ebb1da4999a5750564164a3e3304027c7070ab4530ceb2a1c31c4371852b995b158c7ecde6d92b56e60ad52d8eadee6b6a977e4dd2ae7aa3800c639eece51f13e0b883388c3bb4fb30b8896ff2f1993c45da736ee39b5ce52abf7e12b840093ade56140de6fd9ca75df64a0119d8848dbcbbec32f8e8dc2b0979a62605d75af24bb1d3e04faf35edff4221629f1b05c9ac4b794692c7f7bfdbf6709a8002e27ae833010cde1c67e031758d24679bea2cc66ececd317e9336c4321c0218ca89a5c1f641795492c387d37d7b3a21a080b8165649800b943078934f6acf4a3e78f2baeffb37494e3dd5b99392708d29f118d833b80fc2323c02d8a9f076a330a7f72b77dde18dc41e470171112c9a0059692f5181610805e7dddcbab755f9ef241c4d912204e1f8c15615fcf7de13e002a8972621de83e3c68726f96cef7b6d07774940017171ec84c019921c94e43449c85d44f4301ffefd3bbe14dbc5dbea37548ce096f521f084244fade1e0dc70e71661fe93f519de3847a2808c73de378e1a01e00e0b8e8c384242182e5dc74aa7ac23268c9d5cbd3a317ace831c9955b92383bbadbfe8b1d43c90f6f21dec22d97d509e93e431bdeca59dda36010564dbc87a5fe1b9e5a9c46ff37c98d9119cbe4400a334bb04f20d5da8fe0e3ffc6515761518bcb161700ece19b865bd09b0a3bc45ed36b07790aac4b2260414903599c81a0647041c15f4a170631c02764c8906d1df3fee43c7ecc35208f04bca9b2bbafcbbed32a87b7a97c752b82fb5110564a9b817ded8aa04844b7fc85acbcee2b8fa5010cc67192701627488c7c159827431e420fbc53851acf7a81590f59a5f8eaa1e5797f0fcac5d05bacf0657d8cd467bd63ad6fa5dd93f26cff001e0f88bc2bbb88b9af2ebda51fcbe04c363a8f55a433b1dcdd52b97151741bd3ac9dd76fa42ebf7978002d2dfb9b16712181a816b95171db9c8fe3409f638cb1a135040d678721d9a04964880632abcad4ed562760e695e7c472db16d9b5a1101056445e06d56026b44808c027857e124410c102ed996111050404630c90e51020b247040ed365e91e4e005b6e3ab7b484001e9e1a4d825090c8000760ed2921cd832113c33c96307d067bb3867020ac89c81fa3a098c80c0cd5ab0eadf243945bbbceb881290110cdb216e24a080b8262420815909907efdb03292633067d7f1a3592bfbdcfa115040d66f4e1d9104164180acca2f4842fccfddebb6c745b4e33b0744400119d064d95509ac8000a9d75f5cb740f2e7a12be8834df6948002d2d389b15b12e80101761d1c5991a5809b21b901d222813f1050405c0c1290c04602a421795992dbd555b3cf4e72bc9824b0918002e29a908004a609201a7f99e4c424f74fe2c55eae8f5d1250405c1c12900004c8a0fbb40a067c5133963fc90bbe5c185b115040b622e4bf4b60fd095c3fc95bca25f72175d9d7fa8fda11ee988002b26384be40028325c0ae83dd06b9ac8e4c72d7c18ec48eaf848002b212ec362a819513b8727955611c67d771f4ca7b64070647400119dc94d96109ec88c0e95aed27d77d1def4c72f31dbdcdcaa326a0808c7afa1dfcc808ec9be41f929cb35d41fcd0babf6364081cee3c092820f3a4e9bb24d05f024f6901814faceb6671cf3587557fe76a303d53400633557654029d085c20c96b935cb16c1d2feff4162b496013020a88cb4202eb4be0f0248f48f2e524774cf2b5f51daa235b0501056415d46d53028b25b0679237b6d88e2b25797892bf4ff25f8b6dd2b78f91800232c65977cceb4c00c1785e8be938b672597d659d07ebd8564b4001592d7f5b97c0bc08ec550910f74bf2b8babbe317f37ab9ef91c066041410d78504864f8054ebcf291bc79f25f9c0f087e4088640400119c22cd947096c4ee0f2499e95843bca5f585e56b292c0d20828204b436d4312982b813f6919745f9ae42765eb3866ae6ff76512988180023203241f91408f085c38c93b5a6cc7de499e9ee4093dea9b5d19190105646413ee70074de0f9490e69311d5f6ab11d0724f9dea04763e7074f400119fc143a801110b87b92472521aafc9149fe6e046376880320a0800c6092ece268099c3dc94b2a8a1c7b07f9acfe6db4341c78ef082820bd9b123b24819c31c973931c58827170924fc845027d23a080f46d46eccfd809dc36c98b93fc4f92072479dbd88138fefe125040fa3b37f66c5c04f0aa7a4192fd931cd104e4f1494e1a1702473b34020ac8d066ccfeae1b8173d73d1dec365e57c90f7fb86e83743ceb49400159cf797554fd2770a64ab54ef243eeebc04597b4eb16090c8680023298a9b2a36b42e0726520bf56e5ae2210f05d6b32b6751ac679931cd66c50576991feffd2d2c57c320977c82bf253b3ac80acd392772c7d27f0d824cf288f2aa2c8dfdef70eaf71ff4e9b84f89a4bb43939551307ee8bff7592eb3577e9adbe8bccdb2dd798cdcc43db0ad4cc2ff2410948605302e74c42865c8eaab89b03e338973d59964be00c496edcd2c05c2dc9bd9aa7dbb96668fe5b252a7bb49431bf6d17735db4ea7041d73d67a8bff68f28206b3fc50e7085041e53b9aa7e544180af5a615fc6d8f445925c37c97d927064b8bb727c926f3461f84eed40de92e48badc2932b6dccab93bca91c1dc6c872d3312b202e0509cc9fc0534b38c859f5cc764cc2c7c7b21c0208c68dea28ea3abb69f27365833aa152c45cb69efd76d9a488fc3f71395d1e6e2b0ac870e7ce9ef78f00f9aa108f6fd63d1d0ac762e7e86c496e55768c835a7afbf3eca6b99f26f9659b176c1fd4fb74ed30308a7fb68ce4de1bbfcdf95240b609ccc725b0098187d5654e64c7258a1cb75ccb620860e43eb4bca3263689ad5ac28b0a4fb7cf37fbc7774d0bb315aed9ff5d01999d954f4a6023012e757a51922f9470b8e358cc1a21bdcb5d2a37d8ee5a6007c14ee3e3495e5342f1fdc574c9b7424001711d4860fb046e9fe4f0765c75f2244f32bdfaf601ce50835dc6839b105c6a8b63293cdb3078bfb96c1a33bcda47e6454001991749df330602d748f2ec241867ff22c9d39a8beeafc630f0058f11033647537748c27fefb98bf64865ffb1242fab3f7fb1e07ef9fa2d0828202e11096c4d808f1a5e39d7aca32adc73f5d0d99adb664f701c75be0adcbb52738bbdcc6e5e834beda792bcbea2c0158c6ecc17564b0159185a5fbc26041e51c17f1862090824a585656b0297ac9d1a2271a112dfb34c5523308f9f534ffd1d49243f93e4bd15a58f4bada5c70414901e4f8e5d5b29018e54f0a83a5d9dc57b2fc7aea783a3bd6b373bc415db87ffaa492eb6e1d1e3ca038aefcde5db2d8b64209e14a2bdb15fe0b9c66ec33220020ac88026cbae2e85c015921c59e7f00f3508f08f9823a6b7299120d920694136bad27273223b076c157845913c126f35aee79d1472497d34c951eddf1010cb40092820039d38bb3d7702e748f2caf65bf0cddb9b495f8197d598cfdc1152ec13ec286e50828a684c0a417904e3e105c5b11e4280919ba3ab7b97319cffa6fc7b927fad94f51ff05ef7b9afdd95bd500159197a1bee0901a292c9907bffda6d3c7164bf1573ff3ace011c3f61abc066c1ce62ba104b415e280483981744e36bf500596939ee23edf9f5a72a11e14dee287622efe8c95cdb8d39135040e60cd4d70d8ac043ea36408cb7f71d81819c0cb4d820b059e059768b24a7df30637cf0110744028336693e7e3ef5ccc5db11d53da632db4e572778ef836504279d8b65cd0928206b3ec10e6f53029ce3bf24c96fdabf725cf5b76bc809db03bb03821d3170dfb472414d0403816067c1f113ff4de2c7cd2e4bc2738af4e71c63dd7a03a7639a18bdbb795bbdafed42debf860c1dd21604141097c89808ec9fe4d195e2fb5975e3dc490307b0778d872328ec1657df643c1f4a726c7942211218ba771700895090d1f68e1b3ca6b071708c85d7148670bcab2c2326a0808c78f24734743eae0727796045311341fe83818d9f2cb2d819b057b0bb20c507ff3f5dd84970a705eeb088043b0cfe7fabc2a557776e2273d70af09b7e9e9d09a9428e6e360e0ce01609fc818002e262586702b88e3ea584e3756da01c57f191ed73e1122452a55c30091f764402b138eb54a711bfaf9648708c848d62bb1f778ea46ed28eb06e567691c9eb7f5691dfdc9af8914a4ed8675ef66d8504149015c2b7e98511e09c1f033991e3a4effef34a85b1b0063bbcf8d2cd6d18b1c0eb893bb9d92561dc9e8e97e0988914e41c1b318e9ddc5b417b1c6f710f38e2315db05f102889d794d1df1d2673ac551490b1cefc7a8e9bb883034a3cc8558580b0f3586521f88ee321761108065e50c453e03ebbb1201804d74d5c66398adac92547d830c81ccc4ee686538d6138c75b8a5d0b3f3b6963956c6d7bc5041490154f80cdcf85c0252a0890dfb031ec3ea125ec7bf95cde3cfb4bce5fd964397a222811c1d8184fb1f16db8c91291cd0e83a3a8496cc5ecadfef193fb95e19bacb688c6a47cbd7660081251f61609cc85800232178cbe64450438f221dd0867f93f4a72c49284e39ee5f1442cc524b6622b046496c5dd9523297e108e79147618ec6cee5351e0937772a912bb1904436fa97990f61dff8f8002e2a2182201f22fddb8921d1204c81d1dec38e67d370749ff88196127c16ff7179e1116fd6057f1e13a2a7acf8cf566790c5bc9ddca1b0b019d14762f0814b722120c6891c0c20928200b476c037322c05ae583c92d759ced73764fda6fc4631e659f8ace9eb8c962dcde18a5bd593bfc768fcd8223283edcb8ceb2c39857211715e36597c14eeb4c532f4698b82b03175b763816092c958002b254dc36d6910011d58f2b2fa2b7b68fe8f3da8e80e0b8df75781ff114244c249682b41cd82c709b9db5bcab84629248f07bb3569cf1391c0118efedcae03e6d6c67478358e035858bad45022b25a080ac14bf8def86c0695a06d7832ab32bbf85735f04311dc43f6ca71051cdee0203fb75cbc03d4bfdffa89d04b6047eb0594ce7849ae51db33cc3711c9e63fb96684cd7c1f88d50e1624be4f798b303cfc2d267964c40015932709bdb92003b04ec0d7c3029b89b12c7c1477c967248131bd27b6023c1c83d4b6117417a0e8e81b87990741f8b323c93fd97dc52d855eeb4a17304f1b1b3e0588af1eed42b6b96b1fb8c043a1350403aa3b3e202086077e04e0e3efca4cf20f5c8ee528e70bc438c05bb0b8ccb1c4b9d728b7ef13eec15c442f0b15ef4511086780491886f0cf2d347520806769349d4f77677570b98025f2981d9092820b3b3f2c9c51220cdc8936a07f0a0dd7cd8f92093869c1d06c171d391db1b7b4802417e8bc79d957b2c30762fa390ee1c633cb61522c027855d0d3614763908a4b7f12d63366c636104149085a1f5c5331240100e2bd7d457b4a3a307549a75aa93e2838f311e48ec30ce57f1179bbd1afbc424532c5e50189c975588c520788fbbc0393e9bec32e813c67e3cc6263120cbea93ed4860e10414908523b6815d10d8a38ce28736575cd27d9cb02161e056e030724f5272602f59469244dc7a4909cfcd7d18e4399ac2a63129c79758d01ffaa60d63ab59f4df074d400119f4f40dbaf39cfd4f07c2ed6e3024f8c3d0cdce82a3286c063f5dc0e8f184daab1da5fdbefa46e0204287c11bf1e072a6e9f2e34a3e888718aeb51c4d5924301a020ac868a6ba7703e58889231f04018f238e7bc836bbb1f0516677b2e87260b90aefaa1d6e2f44f4e82bb614fa3fb43b4516cdd0f78f8c800232b20977b8bb24c0ce83c8767619b8f3725321de5adcedcda54c1c49b133b1484002454001712948400212904027020a48276c5692800424200105c4352001094840029d0828209db0594902129080041410d78004242001097422a08074c26625094840021250405c031290800424d0898002d2099b9524200109484001710d48400212904027020a48276c5692800424200105c4352001094840029d0828209db0594902129080041410d78004242001097422a08074c26625094840021250405c031290800424d0898002d2099b9524200109484001710d48400212904027020a48276c5692800424200105c4352001094840029d0828209db0594902129080041410d78004242001097422a08074c26625094840021250405c031290800424d0898002d2099b9524200109484001710d48400212904027020a48276c5692800424200105c4352001094840029d0828209db0594902129080041410d78004242001097422a08074c26625094840021250405c031290800424d0898002d2099b9524200109484001710d48400212904027020a48276c5692800424200105c4352001094840029d0828209db0594902129080041410d78004242001097422a08074c26625094840021250405c031290800424d0898002d2099b9524200109484001710d48400212904027020a48276c5692800424200105c4352001094840029d0828209db0594902129080041410d78004242001097422a08074c26625094840021250405c031290800424d0898002d2099b9524200109484001710d48400212904027020a48276c5692800424200105c4352001094840029d0828209db0594902129080041410d78004242001097422a08074c26625094840021250405c031290800424d0898002d2099b9524200109484001710d4840021290402702ff0b2def58f6ce0530ca0000000049454e44ae426082),
(49, 50, '2024-10-25 13:58:16', '2024-10-25 14:26:06', NULL, NULL),
(50, 50, '2024-10-25 14:26:24', '2024-10-25 14:27:09', NULL, NULL),
(51, 50, '2024-10-25 14:27:33', NULL, NULL, NULL),
(52, 50, '2024-10-25 20:20:42', NULL, NULL, NULL),
(53, 50, '2024-10-28 15:48:12', NULL, NULL, NULL),
(54, 50, '2024-10-29 10:10:54', NULL, NULL, NULL),
(55, 50, '2024-10-29 10:14:55', '2024-10-29 11:06:55', NULL, NULL),
(56, 50, '2024-10-29 10:16:01', '2024-10-29 10:39:53', NULL, NULL),
(57, 50, '2024-10-29 10:40:01', '2024-10-29 10:46:13', NULL, NULL),
(58, 50, '2024-10-29 10:46:23', '2024-10-29 10:46:48', NULL, NULL),
(59, 50, '2024-10-29 10:46:55', '2024-10-29 10:47:01', NULL, NULL),
(60, 50, '2024-10-29 10:47:31', '2024-10-29 10:59:37', NULL, NULL),
(61, 50, '2024-10-29 10:59:48', '2024-10-29 11:02:29', NULL, NULL),
(62, 50, '2024-10-29 11:03:07', '2024-10-29 11:10:49', NULL, NULL),
(64, 50, '2024-10-29 11:11:00', '2024-10-29 11:15:43', NULL, NULL),
(65, 50, '2024-10-29 11:15:53', '2024-10-29 11:16:25', NULL, NULL),
(66, 50, '2024-10-29 11:16:35', '2024-10-29 11:16:56', NULL, NULL),
(67, 50, '2024-10-29 11:17:04', '2024-10-29 11:18:33', NULL, NULL),
(68, 50, '2024-10-29 11:18:42', '2024-10-29 11:31:37', NULL, NULL),
(69, 50, '2024-10-29 11:31:45', '2024-10-29 11:35:41', NULL, NULL),
(70, 50, '2024-10-29 11:35:49', NULL, NULL, NULL),
(80, 50, '2024-10-30 15:19:47', NULL, NULL, NULL),
(81, 53, '2024-10-30 15:33:35', '2024-11-04 00:08:07', NULL, NULL),
(82, 53, '2024-10-30 15:33:40', '2024-10-30 15:34:26', NULL, NULL),
(83, 53, '2024-10-30 15:34:30', '2024-11-04 00:08:07', NULL, NULL),
(84, 50, '2024-10-30 15:36:06', '2024-10-30 15:36:59', NULL, NULL),
(85, 53, '2024-10-30 16:14:45', '2024-11-04 00:08:07', NULL, NULL),
(86, 53, '2024-10-31 09:09:20', '2024-10-31 09:20:05', NULL, NULL),
(87, 53, '2024-10-31 09:21:27', '2024-11-04 00:08:07', NULL, NULL),
(88, 53, '2024-10-31 09:42:37', '2024-11-04 00:08:07', NULL, NULL),
(89, 53, '2024-10-31 09:50:41', '2024-11-04 00:08:07', NULL, NULL),
(90, 53, '2024-10-31 09:50:54', '2024-10-31 09:54:27', NULL, NULL),
(91, 53, '2024-10-31 09:54:33', '2024-10-31 09:57:20', NULL, NULL),
(92, 53, '2024-10-31 10:00:21', '2024-11-04 00:08:07', NULL, NULL),
(93, 53, '2024-10-31 10:17:54', '2024-11-04 00:08:07', NULL, NULL),
(94, 53, '2024-10-31 11:07:17', '2024-11-04 00:08:07', NULL, NULL),
(95, 53, '2024-10-31 11:10:40', '2024-11-04 00:08:07', NULL, NULL),
(96, 53, '2024-10-31 11:12:05', '2024-11-04 00:08:07', NULL, NULL),
(97, 53, '2024-10-31 11:17:11', '2024-11-04 00:08:07', NULL, NULL),
(98, 53, '2024-10-31 11:39:57', '2024-11-04 00:08:07', NULL, NULL),
(99, 53, '2024-10-31 11:58:16', '2024-11-04 00:08:07', NULL, NULL),
(100, 53, '2024-10-31 15:01:13', '2024-11-04 00:08:07', NULL, NULL),
(101, 53, '2024-10-31 15:10:51', '2024-11-04 00:08:07', NULL, NULL),
(102, 53, '2024-10-31 16:17:21', '2024-11-04 00:08:07', NULL, NULL),
(103, 53, '2024-10-31 16:22:42', '2024-11-04 00:08:07', NULL, NULL),
(104, 53, '2024-11-03 23:38:22', '2024-11-04 00:08:07', NULL, NULL),
(107, 53, '2024-11-04 00:08:13', '2024-11-04 00:52:27', NULL, NULL),
(108, 53, '2024-11-04 00:52:53', '2024-11-04 00:57:01', NULL, NULL),
(110, 53, '2024-11-04 01:03:23', '2024-11-04 01:31:35', NULL, NULL),
(112, 53, '2024-11-04 01:31:10', '2024-11-04 01:31:35', NULL, NULL),
(113, 53, '2024-11-04 01:31:42', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `product_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlists`
--

INSERT INTO `wishlists` (`id`, `user_id`, `product_id`, `created_at`) VALUES
(14, 53, 53, '2024-10-31 14:50:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`client_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `comments_ratings`
--
ALTER TABLE `comments_ratings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD PRIMARY KEY (`inventory_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `client_id` (`client_id`),
  ADD KEY `shipping_address_id` (`shipping_address_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`order_item_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `product_reviews`
--
ALTER TABLE `product_reviews`
  ADD PRIMARY KEY (`review_id`);

--
-- Indexes for table `shipping_addresses`
--
ALTER TABLE `shipping_addresses`
  ADD PRIMARY KEY (`shipping_id`),
  ADD KEY `client_id` (`client_id`);

--
-- Indexes for table `subscribers`
--
ALTER TABLE `subscribers`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_sessions_ibfk_1` (`user_id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_wishlist_item` (`user_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `client_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `comments_ratings`
--
ALTER TABLE `comments_ratings`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `order_item_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `product_reviews`
--
ALTER TABLE `product_reviews`
  MODIFY `review_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipping_addresses`
--
ALTER TABLE `shipping_addresses`
  MODIFY `shipping_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `session_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=114;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`shipping_address_id`) REFERENCES `shipping_addresses` (`shipping_id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`);

--
-- Constraints for table `shipping_addresses`
--
ALTER TABLE `shipping_addresses`
  ADD CONSTRAINT `shipping_addresses_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `clients` (`client_id`);

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `wishlists_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `clients` (`client_id`),
  ADD CONSTRAINT `wishlists_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
