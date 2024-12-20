-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 20, 2024 at 06:17 AM
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
-- Database: `poultry`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(250) NOT NULL,
  `username` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`) VALUES
(1, 'admin', '$2y$10$7koXIjfIpkhMUsbNRQjEvueJ7DnW/PFMKokSjtRo6Pc/PQIplfwBW');

-- --------------------------------------------------------

--
-- Table structure for table `brand`
--

CREATE TABLE `brand` (
  `brand_id` int(250) NOT NULL,
  `brand_name` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `brand`
--

INSERT INTO `brand` (`brand_id`, `brand_name`) VALUES
(1, 'PIGROLAC (p)'),
(2, 'EARLYWEAN (p)'),
(4, 'GALLIMAX (ch)'),
(5, 'THUNDERBIRD (ch)'),
(6, 'SPECIAL DOG (d)'),
(7, 'VITALITY (d)'),
(8, 'Blue Buffalo (ca)'),
(9, 'Fancy Feast (ca)');

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(250) NOT NULL,
  `category_name` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'Dog Food'),
(2, 'Cat Food'),
(3, 'Chicken Feeds'),
(4, 'Pig Feeds');

-- --------------------------------------------------------

--
-- Table structure for table `product`
--

CREATE TABLE `product` (
  `product_id` int(250) NOT NULL,
  `product_name` varchar(250) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `quantity` int(250) NOT NULL,
  `status` enum('Available','Out of Stock','Low Stock') NOT NULL,
  `brand_id` int(250) NOT NULL,
  `category_id` int(250) NOT NULL,
  `supplier_id` int(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product`
--

INSERT INTO `product` (`product_id`, `product_name`, `price`, `quantity`, `status`, `brand_id`, `category_id`, `supplier_id`) VALUES
(45, 'HIGH ENERGY PUPPY', 205.00, 42, 'Available', 7, 1, 9),
(46, 'Gourmet Salmon & Tuna', 150.00, 0, 'Out of Stock', 9, 2, 10),
(47, 'Indoor Health Chicken & Turkey Recipe', 500.00, 0, 'Out of Stock', 8, 2, 10),
(48, 'CLASSIC ADULT', 185.00, 45, 'Available', 7, 1, 9),
(49, 'STARTER', 42.00, 36, 'Available', 1, 4, 11),
(50, 'BOOSTER', 120.00, 40, 'Available', 2, 4, 11),
(51, 'PLATINUM', 62.00, 48, 'Available', 5, 3, 12),
(52, 'GALLIMAX 21', 40.00, 17, 'Low Stock', 4, 3, 12),
(56, 'SAMPLE', 180.00, 30, 'Available', 1, 4, 12),
(57, 'SAMPLE 1', 80.00, 30, 'Available', 1, 4, 12),
(59, 'STARTER 2.0', 32.00, 10, 'Low Stock', 1, 4, 9);

--
-- Triggers `product`
--
DELIMITER $$
CREATE TRIGGER `update_status_on_stock_change` BEFORE UPDATE ON `product` FOR EACH ROW BEGIN
    IF NEW.quantity = 0 THEN
        SET NEW.status = 'Out of Stock';
    ELSEIF NEW.quantity BETWEEN 1 AND 20 THEN
        SET NEW.status = 'Low Stock';
    ELSE
        SET NEW.status = 'Available';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `sales_records`
--

CREATE TABLE `sales_records` (
  `sale_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `sale_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales_records`
--

INSERT INTO `sales_records` (`sale_id`, `product_id`, `quantity`, `price`, `amount`, `sale_date`) VALUES
(105, 46, 1, 150.00, 150.00, '2024-12-12 04:05:28'),
(106, 47, 1, 500.00, 500.00, '2024-12-12 04:05:28'),
(107, 49, 1, 42.00, 42.00, '2024-12-12 04:05:28'),
(108, 50, 1, 120.00, 120.00, '2024-12-12 04:05:28'),
(109, 51, 1, 62.00, 62.00, '2024-12-12 04:05:28'),
(110, 52, 1, 40.00, 40.00, '2024-12-12 04:05:28'),
(111, 49, 1, 42.00, 42.00, '2024-12-12 05:03:52'),
(112, 45, 1, 205.00, 205.00, '2024-12-12 06:18:15'),
(113, 50, 1, 120.00, 120.00, '2024-12-12 06:18:15'),
(114, 45, 1, 205.00, 205.00, '2024-12-12 06:57:43'),
(115, 47, 2, 500.00, 1000.00, '2024-12-12 06:57:43'),
(116, 49, 1, 42.00, 42.00, '2024-12-12 06:57:43'),
(117, 52, 1, 40.00, 40.00, '2024-12-12 06:57:43'),
(118, 45, 1, 205.00, 205.00, '2024-12-12 07:40:09'),
(119, 47, 26, 500.00, 13000.00, '2024-12-12 07:40:09'),
(120, 45, 1, 205.00, 205.00, '2024-12-17 19:01:32'),
(121, 48, 1, 185.00, 185.00, '2024-12-17 19:01:32'),
(122, 49, 10, 42.00, 420.00, '2024-12-17 19:01:32'),
(123, 50, 20, 120.00, 2400.00, '2024-12-17 19:01:32'),
(124, 45, 1, 205.00, 205.00, '2024-12-20 05:04:14');

-- --------------------------------------------------------

--
-- Table structure for table `supplier`
--

CREATE TABLE `supplier` (
  `supplier_id` int(250) NOT NULL,
  `supplier_name` varchar(250) NOT NULL,
  `supplier_number` varchar(250) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplier`
--

INSERT INTO `supplier` (`supplier_id`, `supplier_name`, `supplier_number`) VALUES
(9, 'Supplier 1', '0987654321'),
(10, 'Supplier 2', '0912345678'),
(11, 'Supplier 3', '0963258741'),
(12, 'Supplier 4', '0978456123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brand`
--
ALTER TABLE `brand`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `product`
--
ALTER TABLE `product`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `idx_brand_id` (`brand_id`),
  ADD KEY `idx_category_id` (`category_id`),
  ADD KEY `idx_supplier_id` (`supplier_id`);

--
-- Indexes for table `sales_records`
--
ALTER TABLE `sales_records`
  ADD PRIMARY KEY (`sale_id`),
  ADD KEY `sales_records_ibfk_1` (`product_id`);

--
-- Indexes for table `supplier`
--
ALTER TABLE `supplier`
  ADD PRIMARY KEY (`supplier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(250) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `brand`
--
ALTER TABLE `brand`
  MODIFY `brand_id` int(250) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(250) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `product`
--
ALTER TABLE `product`
  MODIFY `product_id` int(250) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT for table `sales_records`
--
ALTER TABLE `sales_records`
  MODIFY `sale_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=125;

--
-- AUTO_INCREMENT for table `supplier`
--
ALTER TABLE `supplier`
  MODIFY `supplier_id` int(250) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `sales_records`
--
ALTER TABLE `sales_records`
  ADD CONSTRAINT `sales_records_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `product` (`product_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
