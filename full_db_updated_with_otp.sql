
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3307:3307
-- Generation Time: Sep 21, 2025 at 12:29 PM
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
-- Database: `core_transaction2`
--
CREATE DATABASE IF NOT EXISTS `core_transaction2` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `core_transaction2`;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('Asset','Liability','Equity','Revenue','Expense') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `addresses`
--

CREATE TABLE `addresses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `zipcode` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT 'PH',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `period` date NOT NULL,
  `amount` decimal(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `cart_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL CHECK (`quantity` > 0),
  `unit_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wishlists`
--

CREATE TABLE `wishlists` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `description` text DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `is_active`, `description`, `image_url`, `created_at`) VALUES
(1, 'Jackets', 1, NULL, NULL, '2025-09-21 10:13:20'),
(2, 'Tees', 1, NULL, NULL, '2025-09-21 10:13:20'),
(3, 'Denim', 1, NULL, NULL, '2025-09-21 10:13:20'),
(4, 'Shorts', 1, NULL, NULL, '2025-09-21 10:13:20'),
(5, 'Tops', 1, NULL, NULL, '2025-09-21 10:13:20'),
(6, 'Dress', 1, NULL, NULL, '2025-09-21 10:13:20'),
(7, 'Jeans', 1, NULL, NULL, '2025-09-21 10:13:20'),
(8, 'Sweater', 1, NULL, NULL, '2025-09-21 10:13:20'),
(9, 'Skirts', 1, NULL, NULL, '2025-09-21 10:13:20'),
(10, 'Pants', 1, NULL, NULL, '2025-09-21 10:13:20');

-- --------------------------------------------------------

--
-- Dumping data for table `subcategories`
--

INSERT INTO `subcategories` (`id`, `category_id`, `name`, `description`, `is_active`, `created_at`) VALUES
(1, 1, 'Bomber Jackets', 'Classic bomber style jackets', 1, '2025-09-21 10:13:20'),
(2, 1, 'Leather Jackets', 'Premium leather outerwear', 1, '2025-09-21 10:13:20'),
(3, 1, 'Blazers', 'Professional and casual blazers', 1, '2025-09-21 10:13:20'),
(4, 2, 'Basic Tees', 'Essential cotton t-shirts', 1, '2025-09-21 10:13:20'),
(5, 2, 'Graphic Tees', 'T-shirts with designs and prints', 1, '2025-09-21 10:13:20'),
(6, 2, 'V-Neck Tees', 'Classic v-neck t-shirts', 1, '2025-09-21 10:13:20'),
(7, 3, 'Denim Jackets', 'Classic denim outerwear', 1, '2025-09-21 10:13:20'),
(8, 3, 'Denim Shorts', 'Casual denim shorts', 1, '2025-09-21 10:13:20'),
(9, 4, 'Casual Shorts', 'Everyday casual shorts', 1, '2025-09-21 10:13:20'),
(10, 4, 'Athletic Shorts', 'Sports and workout shorts', 1, '2025-09-21 10:13:20'),
(11, 5, 'Dress Shirts', 'Formal and semi-formal shirts', 1, '2025-09-21 10:13:20'),
(12, 5, 'Casual Shirts', 'Everyday casual shirts', 1, '2025-09-21 10:13:20'),
(13, 5, 'Polo Shirts', 'Classic polo shirts', 1, '2025-09-21 10:13:20'),
(14, 6, 'Casual Dresses', 'Everyday casual dresses', 1, '2025-09-21 10:13:20'),
(15, 6, 'Formal Dresses', 'Elegant formal dresses', 1, '2025-09-21 10:13:20'),
(16, 6, 'Maxi Dresses', 'Long flowing dresses', 1, '2025-09-21 10:13:20'),
(17, 7, 'Skinny Jeans', 'Fitted skinny style jeans', 1, '2025-09-21 10:13:20'),
(18, 7, 'Straight Jeans', 'Classic straight fit jeans', 1, '2025-09-21 10:13:20'),
(19, 7, 'High-Waisted Jeans', 'High-rise waist jeans', 1, '2025-09-21 10:13:20'),
(20, 8, 'Pullover Sweaters', 'Classic pullover style', 1, '2025-09-21 10:13:20'),
(21, 8, 'Cardigans', 'Button-up cardigan sweaters', 1, '2025-09-21 10:13:20'),
(22, 8, 'Turtleneck Sweaters', 'High-neck sweaters', 1, '2025-09-21 10:13:20'),
(23, 9, 'Mini Skirts', 'Short length skirts', 1, '2025-09-21 10:13:20'),
(24, 9, 'Midi Skirts', 'Medium length skirts', 1, '2025-09-21 10:13:20'),
(25, 9, 'Pencil Skirts', 'Fitted pencil style skirts', 1, '2025-09-21 10:13:20'),
(26, 10, 'Chinos', 'Casual cotton pants', 1, '2025-09-21 10:13:20'),
(27, 10, 'Dress Pants', 'Formal business pants', 1, '2025-09-21 10:13:20'),
(28, 10, 'Cargo Pants', 'Utility style pants', 1, '2025-09-21 10:13:20');

-- --------------------------------------------------------

--
-- Table structure for table `commissions`
--

CREATE TABLE `commissions` (
  `id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `commission_rate` decimal(5,2) NOT NULL,
  `commission_amount` decimal(10,2) NOT NULL,
  `settled` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_events`
--

CREATE TABLE `contact_events` (
  `id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `channel` enum('Click','Call') NOT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `couriers`
--

CREATE TABLE `couriers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `contact_no` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customer_addresses`
--

CREATE TABLE `customer_addresses` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `address_id` int(11) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deliveries`
--

CREATE TABLE `deliveries` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `carrier` varchar(100) DEFAULT 'In-house',
  `tracking_number` varchar(100) DEFAULT NULL,
  `status` enum('Pending','Dispatched','In Transit','Delivered','Failed','Cancelled') DEFAULT 'Pending',
  `scheduled_date` date DEFAULT NULL,
  `delivered_at` datetime DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_events`
--

CREATE TABLE `delivery_events` (
  `id` int(11) NOT NULL,
  `delivery_id` int(11) NOT NULL,
  `event_time` datetime DEFAULT current_timestamp(),
  `event_type` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `actor` varchar(100) DEFAULT 'system'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `disbursements`
--

CREATE TABLE `disbursements` (
  `id` int(11) NOT NULL,
  `vendor_bill_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` date NOT NULL,
  `method` enum('Cash','Bank','Check') DEFAULT 'Bank'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` int(11) NOT NULL,
  `doc_type` varchar(50) NOT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `status` enum('Draft','For Approval','Approved','Rejected') DEFAULT 'Draft',
  `created_by` varchar(100) DEFAULT 'system',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `document_approvals`
--

CREATE TABLE `document_approvals` (
  `id` int(11) NOT NULL,
  `document_id` int(11) NOT NULL,
  `approver` varchar(100) NOT NULL,
  `action` enum('Approved','Rejected') DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `acted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `license_no` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fleet_vehicles`
--

CREATE TABLE `fleet_vehicles` (
  `id` int(11) NOT NULL,
  `plate_no` varchar(20) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `capacity_kg` int(11) DEFAULT NULL,
  `status` enum('Active','Maintenance','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fleet_routes`
--

CREATE TABLE `fleet_routes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `route_name` varchar(255) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `start_location` varchar(255) DEFAULT NULL,
  `end_location` varchar(255) DEFAULT NULL,
  `distance_km` decimal(10,2) DEFAULT NULL,
  `estimated_time` time DEFAULT NULL,
  `status` enum('planned','active','completed','cancelled') DEFAULT 'planned',
  `scheduled_date` datetime DEFAULT NULL,
  `completed_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_vehicle` (`vehicle_id`),
  KEY `idx_driver` (`driver_id`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipts`
--

CREATE TABLE `goods_receipts` (
  `id` int(11) NOT NULL,
  `purchase_order_id` int(11) DEFAULT NULL,
  `warehouse_id` int(11) NOT NULL,
  `receipt_date` datetime DEFAULT current_timestamp(),
  `received_by` varchar(100) DEFAULT 'system',
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `goods_receipt_items`
--

CREATE TABLE `goods_receipt_items` (
  `id` int(11) NOT NULL,
  `goods_receipt_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) DEFAULT NULL,
  `bin_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_balances`
--

CREATE TABLE `inventory_balances` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `bin_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_logs`
--

CREATE TABLE `inventory_logs` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `delta` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `changed_by` varchar(100) DEFAULT 'admin',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` enum('Open','Paid','Voided') DEFAULT 'Open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `invoice_items`
--

CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_entries`
--

CREATE TABLE `journal_entries` (
  `id` int(11) NOT NULL,
  `entry_date` date NOT NULL,
  `memo` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `journal_lines`
--

CREATE TABLE `journal_lines` (
  `id` int(11) NOT NULL,
  `journal_entry_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit` decimal(12,2) DEFAULT 0.00,
  `credit` decimal(12,2) DEFAULT 0.00,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('Just Placed','Processing','Shipped','At Delivery Hub','Delivered','Cancelled') DEFAULT 'Just Placed',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_history`
--

CREATE TABLE `order_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `action` enum('created','updated_status','updated_payment','added_item','removed_item','deleted') NOT NULL,
  `old_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_value`)),
  `new_value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_value`)),
  `note` varchar(255) DEFAULT NULL,
  `actor` varchar(100) DEFAULT 'system',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `method` enum('COD','Card','GCash','Bank') DEFAULT 'COD',
  `amount` decimal(10,2) NOT NULL,
  `status` enum('Pending','Authorized','Captured','Failed','Refunded') DEFAULT 'Pending',
  `payment_ref` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments_ar`
--

CREATE TABLE `payments_ar` (
  `id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `amount` decimal(12,2) NOT NULL,
  `payment_date` date NOT NULL,
  `method` enum('Cash','Bank','Card','GCash') DEFAULT 'Cash'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `selling_price` decimal(10,2) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `subcategory_id` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `vendor_id` int(11) DEFAULT NULL,
  `min_stock_level` int(11) DEFAULT 10,
  `status` enum('active','inactive','pending','out_of_stock','phase_out','unavailable') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `category_id`, `subcategory_id`, `image_url`, `stock_quantity`, `vendor_id`, `min_stock_level`, `created_at`) VALUES
(1, 'Classic Cotton Tee', 'Soft cotton t-shirt for everyday wear', 499.00, 2, 4, 'uploads/products/default.jpg', 120, 1, 10, '2025-09-21 10:13:20'),
(2, 'Slim Fit Jeans', 'Denim jeans with a modern slim fit', 1299.00, 7, 18, 'uploads/products/default.jpg', 80, 1, 10, '2025-09-21 10:13:20'),
(3, 'City Break Jacket', 'Wind-resistant bomber jacket designed for city commutes', 2799.00, 1, 1, 'uploads/products/default.jpg', 45, 1, 8, '2025-09-21 10:13:20'),
(4, 'Studio Denim Jacket', 'Vintage-wash denim jacket with stretch comfort', 2499.00, 3, 7, 'uploads/products/default.jpg', 60, 1, 6, '2025-09-21 10:13:20'),
(5, 'Weekend Shorts', 'Breathable chino shorts ideal for casual plans', 899.00, 4, 9, 'uploads/products/default.jpg', 90, 1, 12, '2025-09-21 10:13:20'),
(6, 'Layered Knit Top', 'Lightweight knit top built for effortless layering', 1199.00, 5, 12, 'uploads/products/default.jpg', 110, 1, 10, '2025-09-21 10:13:20'),
(7, 'Evening Wrap Dress', 'Wrap-style midi dress with satin sheen for events', 1999.00, 6, 15, 'uploads/products/default.jpg', 40, 1, 5, '2025-09-21 10:13:20'),
(8, 'Alpine Sweater', 'Cable-knit sweater crafted with blended wool for warmth', 1899.00, 8, 20, 'uploads/products/default.jpg', 70, 1, 7, '2025-09-21 10:13:20'),
(9, 'Pleated City Skirt', 'High-waist pleated skirt with built-in lining', 1399.00, 9, 24, 'uploads/products/default.jpg', 55, 1, 6, '2025-09-21 10:13:20'),
(10, 'Tailored Work Pants', 'Slim tapered pants with stretch waistband for comfort', 1599.00, 10, 27, 'uploads/products/default.jpg', 65, 1, 8, '2025-09-21 10:13:20'),
(11, 'Levi''s 501 Original Fit Jeans', 'Iconic straight leg jeans with button fly closure', 3499.00, 7, 18, 'uploads/products/levis_501.jpg', 150, 2, 12, '2025-09-22 09:15:00'),
(12, 'Levi''s Trucker Jacket', 'Mid-weight denim jacket with signature stitching', 4299.00, 3, 7, 'uploads/products/levis_trucker.jpg', 90, 2, 6, '2025-09-22 09:15:00'),
(13, 'Levi''s Graphic Tee', 'Soft jersey tee with Levi''s heritage logo', 1499.00, 2, 5, 'uploads/products/levis_graphic_tee.jpg', 200, 2, 20, '2025-09-22 09:15:00'),
(14, 'H&M Linen Blend Shirt', 'Relaxed fit button-down shirt in breathable linen blend', 1299.00, 5, 11, 'uploads/products/hm_linen_shirt.jpg', 180, 3, 15, '2025-09-22 09:20:00'),
(15, 'H&M High Waist Mom Jeans', 'High waist ankle-length jeans with light wash', 1590.00, 7, 19, 'uploads/products/hm_mom_jeans.jpg', 120, 3, 12, '2025-09-22 09:20:00'),
(16, 'H&M Pleated Midi Skirt', 'Flowing midi skirt with soft pleats and elastic waist', 1490.00, 9, 24, 'uploads/products/hm_pleated_skirt.jpg', 80, 3, 10, '2025-09-22 09:20:00'),
(17, 'Zara Satin Slip Dress', 'Midi slip dress with adjustable straps and bias cut', 2995.00, 6, 15, 'uploads/products/zara_satin_slip.jpg', 70, 4, 8, '2025-09-22 09:25:00'),
(18, 'Zara Faux Leather Biker Jacket', 'Cropped biker jacket with asymmetric zipper', 3995.00, 1, 2, 'uploads/products/zara_biker.jpg', 50, 4, 6, '2025-09-22 09:25:00'),
(19, 'Zara Knit Polo', 'Short-sleeve knit polo shirt with ribbed edges', 2295.00, 5, 13, 'uploads/products/zara_knit_polo.jpg', 110, 4, 12, '2025-09-22 09:25:00'),
(20, 'Mango Tweed Blazer', 'Double-breasted tweed blazer with gold buttons', 4595.00, 1, 3, 'uploads/products/mango_tweed_blazer.jpg', 60, 5, 6, '2025-09-22 09:30:00'),
(21, 'DBTK Vintage Band Tee', 'Classic band t-shirt with vintage wash', 899.00, 2, 5, 'uploads/products/dbtk_band_tee.jpg', 75, 2, 10, '2025-09-22 09:35:00'),
(22, 'DBTK Distressed Jeans', 'Heavily distressed skinny jeans with raw edges', 1999.00, 7, 17, 'uploads/products/dbtk_distressed_jeans.jpg', 45, 2, 8, '2025-09-22 09:35:00'),
(23, 'Calvin Klein Underwear Set', 'Premium cotton underwear set with logo waistband', 1299.00, 2, 4, 'uploads/products/ck_underwear.jpg', 100, 3, 15, '2025-09-22 09:40:00'),
(24, 'Calvin Klein Leather Jacket', 'Genuine leather jacket with quilted lining', 8999.00, 1, 2, 'uploads/products/ck_leather_jacket.jpg', 25, 3, 5, '2025-09-22 09:40:00'),
(25, 'Nike Air Max Sneakers', 'Classic Air Max sneakers with visible air unit', 6999.00, 2, 4, 'uploads/products/nike_air_max.jpg', 80, 4, 12, '2025-09-22 09:45:00'),
(26, 'Nike Dri-FIT Training Tee', 'Moisture-wicking training t-shirt', 1299.00, 2, 4, 'uploads/products/nike_dri_fit.jpg', 120, 4, 15, '2025-09-22 09:45:00'),
(27, 'Adidas Originals Hoodie', 'Classic three-stripe hoodie with kangaroo pocket', 2999.00, 8, 20, 'uploads/products/adidas_hoodie.jpg', 60, 5, 10, '2025-09-22 09:50:00'),
(28, 'Adidas Stan Smith Sneakers', 'Iconic white leather sneakers with green heel tab', 4999.00, 2, 4, 'uploads/products/adidas_stan_smith.jpg', 90, 5, 12, '2025-09-22 09:50:00'),
(29, 'H&M Basic Cotton Dress', 'Simple and elegant cotton midi dress', 1299.00, 6, 14, 'uploads/products/hm_basic_dress.jpg', 85, 6, 12, '2025-09-22 09:55:00'),
(30, 'H&M Oversized Sweater', 'Comfortable oversized knit sweater', 1599.00, 8, 20, 'uploads/products/hm_oversized_sweater.jpg', 70, 6, 10, '2025-09-22 09:55:00'),
(31, 'Zara Blazer Dress', 'Sophisticated blazer-style dress', 3999.00, 6, 15, 'uploads/products/zara_blazer_dress.jpg', 45, 7, 8, '2025-09-22 10:00:00'),
(32, 'Zara High-Waist Pants', 'Elegant high-waist wide-leg pants', 2299.00, 10, 27, 'uploads/products/zara_high_waist_pants.jpg', 60, 7, 10, '2025-09-22 10:00:00'),
(33, 'Uniqlo AIRism Crew Neck', 'Breathable AIRism t-shirt with quick-dry technology', 590.00, 2, 4, 'uploads/products/uniqlo_airism.jpg', 250, 8, 30, '2025-09-22 10:05:00'),
(34, 'Uniqlo Ultra Light Down Jacket', 'Packable lightweight down jacket', 3490.00, 1, 1, 'uploads/products/uniqlo_ul_down.jpg', 140, 8, 15, '2025-09-22 10:05:00'),
(35, 'Bench Graphic Hoodie', 'Streetwear graphic hoodie with bold design', 1299.00, 8, 20, 'uploads/products/bench_hoodie.jpg', 80, 9, 12, '2025-09-22 10:10:00'),
(36, 'Bench Cargo Shorts', 'Utility cargo shorts with multiple pockets', 899.00, 4, 9, 'uploads/products/bench_cargo_shorts.jpg', 120, 9, 15, '2025-09-22 10:10:00'),
(37, 'Penshoppe Denim Jacket', 'Classic denim jacket with vintage wash', 1799.00, 3, 7, 'uploads/products/penshoppe_denim_jacket.jpg', 65, 10, 10, '2025-09-22 10:15:00'),
(38, 'Penshoppe Ripped Jeans', 'Distressed ripped skinny jeans', 1499.00, 7, 17, 'uploads/products/penshoppe_ripped_jeans.jpg', 90, 10, 12, '2025-09-22 10:15:00');

-- --------------------------------------------------------

--
-- Table structure for table `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `status` enum('Draft','Sent','Partially Received','Received','Cancelled') DEFAULT 'Draft',
  `expected_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` int(11) NOT NULL,
  `purchase_order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL,
  `received_quantity` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restock_orders`
--

CREATE TABLE `restock_orders` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `status` enum('Pending','Ordered','Received','Cancelled') DEFAULT 'Pending',
  `expected_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `restock_order_items`
--

CREATE TABLE `restock_order_items` (
  `id` int(11) NOT NULL,
  `restock_order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `status` enum('Pending','Received','Cancelled') DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `returns_inbound`
--

CREATE TABLE `returns_inbound` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `product_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `disposition` enum('Sellable','Damaged','Repair','Scrap') DEFAULT 'Sellable',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `rating` tinyint(4) NOT NULL,
  `comment` varchar(500) DEFAULT NULL,
  `review_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `route_stops`
--

CREATE TABLE `route_stops` (
  `id` int(11) NOT NULL,
  `shipment_id` int(11) NOT NULL,
  `sequence_no` int(11) NOT NULL,
  `address` text DEFAULT NULL,
  `planned_time` datetime DEFAULT NULL,
  `actual_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipments`
--

CREATE TABLE `shipments` (
  `id` int(11) NOT NULL,
  `shipment_no` varchar(50) DEFAULT NULL,
  `status` enum('Planning','Dispatched','In Transit','Delivered','Cancelled') DEFAULT 'Planning',
  `carrier` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shipment_deliveries`
--

CREATE TABLE `shipment_deliveries` (
  `shipment_id` int(11) NOT NULL,
  `delivery_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shops`
--

CREATE TABLE `shops` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `owner_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `shop_products`
--

CREATE TABLE `shop_products` (
  `shop_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfers`
--

CREATE TABLE `stock_transfers` (
  `id` int(11) NOT NULL,
  `from_warehouse_id` int(11) DEFAULT NULL,
  `to_warehouse_id` int(11) DEFAULT NULL,
  `product_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `from_bin_id` int(11) DEFAULT NULL,
  `to_bin_id` int(11) DEFAULT NULL,
  `status` enum('Draft','In Transit','Completed','Cancelled') DEFAULT 'Draft',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `stock_transfer_items`
--

CREATE TABLE `stock_transfer_items` (
  `id` int(11) NOT NULL,
  `transfer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_packages`
--

CREATE TABLE `subscription_packages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `billing_cycle` enum('Monthly','Quarterly','Yearly') DEFAULT 'Monthly',
  `features` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`features`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `full_name` varchar(150) DEFAULT NULL,
  `phone` varchar(30) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `role` enum('admin','manager','staff','customer','seller') DEFAULT 'customer',
  `vendor_id` int(11) DEFAULT NULL,
  `otp_code` varchar(6) DEFAULT NULL,
  `otp_expires_at` timestamp NULL DEFAULT NULL,
  `otp_attempts` int(11) DEFAULT 0,
  `last_otp_sent` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `admin_accounts`
--

CREATE TABLE `admin_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `department` enum('CT2','CT3') NOT NULL,
  `role` varchar(50) DEFAULT 'admin',
  `email` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendors`
--

CREATE TABLE `vendors` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendors`
--

INSERT INTO `vendors` (`id`, `name`, `email`, `phone`, `created_at`) VALUES
(1, 'RAEVOR', 'contact@raevor.com', '+63-917-000-0001', '2025-09-21 10:13:20'),
(2, 'DBTK', 'info@dbtk.com', '+63-917-000-0002', '2025-09-21 10:13:20'),
(3, 'Calvin Klein', 'contact@calvinklein.com', '+63-917-000-0003', '2025-09-21 10:13:20'),
(4, 'Nike', 'info@nike.com', '+63-917-000-0004', '2025-09-21 10:13:20'),
(5, 'Adidas', 'contact@adidas.com', '+63-917-000-0005', '2025-09-21 10:13:20'),
(6, 'H&M', 'info@hm.com', '+63-917-000-0006', '2025-09-21 10:13:20'),
(7, 'Zara', 'contact@zara.com', '+63-917-000-0007', '2025-09-21 10:13:20'),
(8, 'Uniqlo', 'info@uniqlo.com', '+63-917-000-0008', '2025-09-21 10:13:20'),
(9, 'Bench', 'care@bench.com.ph', '+63-917-777-7000', '2025-09-21 10:20:00'),
(10, 'Penshoppe', 'hello@penshoppe.com', '+63-917-555-3344', '2025-09-21 10:20:00'),
(11, 'Forever 21 Philippines', 'service@forever21.com', '+63-917-333-2121', '2025-09-21 10:20:00'),
(12, 'Lovito', 'hello@lovito.com', '+65-6655-2121', '2025-09-21 10:20:00'),
(13, 'Oxgn', 'support@oxgnfashion.com', '+63-917-456-7878', '2025-09-21 10:20:00'),
(14, 'Zalora Philippines', 'partners@zalora.com', '+63-2-858-07777', '2025-09-21 10:20:00'),
(15, 'Trival', 'info@trival.ph', '+63-917-999-8888', '2025-09-21 10:25:00'),
(16, 'Sample Vendor Store', 'info@samplevendor.com', '+63-917-111-1111', '2025-09-21 10:30:00'),
(17, 'Kim Store', 'kim@store.com', '+63-917-222-2222', '2025-09-21 10:30:00')
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`), `phone` = VALUES(`phone`);

-- --------------------------------------------------------

--
-- Table structure for table `vendor_bills`
--

CREATE TABLE `vendor_bills` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `bill_date` date NOT NULL,
  `due_date` date DEFAULT NULL,
  `total` decimal(12,2) NOT NULL,
  `status` enum('Open','Paid','Voided') DEFAULT 'Open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_bill_items`
--

CREATE TABLE `vendor_bill_items` (
  `id` int(11) NOT NULL,
  `vendor_bill_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` int(11) NOT NULL,
  `unit_cost` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_contacts`
--

CREATE TABLE `vendor_contacts` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `contact_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_subscriptions`
--

CREATE TABLE `vendor_subscriptions` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  `status` enum('Active','Paused','Cancelled') DEFAULT 'Active',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_delivery_daily`
-- (See below for the actual view)
--
CREATE TABLE `v_delivery_daily` (
`period` date
,`pending` decimal(22,0)
,`dispatched` decimal(22,0)
,`in_transit` decimal(22,0)
,`delivered` decimal(22,0)
,`failed` decimal(22,0)
,`cancelled` decimal(22,0)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_inventory_low_stock`
-- (See below for the actual view)
--
CREATE TABLE `v_inventory_low_stock` (
`id` int(11)
,`name` varchar(255)
,`stock_quantity` int(11)
,`min_stock_level` int(11)
,`category_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_order_status_daily`
-- (See below for the actual view)
--
CREATE TABLE `v_order_status_daily` (
`period` date
,`status` enum('Just Placed','Processing','Shipped','At Delivery Hub','Delivered','Cancelled')
,`count` bigint(21)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_sales_daily`
-- (See below for the actual view)
--
CREATE TABLE `v_sales_daily` (
`period` date
,`orders` bigint(21)
,`units` decimal(32,0)
,`gross` decimal(42,2)
,`net` decimal(42,2)
,`aov` decimal(14,6)
);

-- --------------------------------------------------------

--
-- Table structure for table `warehouses`
--

CREATE TABLE `warehouses` (
  `id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `warehouse_bins`
--

CREATE TABLE `warehouse_bins` (
  `id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure for view `v_delivery_daily`
--
DROP TABLE IF EXISTS `v_delivery_daily`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_delivery_daily`  AS SELECT cast(`d`.`created_at` as date) AS `period`, sum(case when `d`.`status` = 'Pending' then 1 else 0 end) AS `pending`, sum(case when `d`.`status` = 'Dispatched' then 1 else 0 end) AS `dispatched`, sum(case when `d`.`status` = 'In Transit' then 1 else 0 end) AS `in_transit`, sum(case when `d`.`status` = 'Delivered' then 1 else 0 end) AS `delivered`, sum(case when `d`.`status` = 'Failed' then 1 else 0 end) AS `failed`, sum(case when `d`.`status` = 'Cancelled' then 1 else 0 end) AS `cancelled` FROM `deliveries` AS `d` GROUP BY cast(`d`.`created_at` as date) ;

-- --------------------------------------------------------

--
-- Structure for view `v_inventory_low_stock`
--
DROP TABLE IF EXISTS `v_inventory_low_stock`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_inventory_low_stock`  AS SELECT `p`.`id` AS `id`, `p`.`name` AS `name`, `p`.`stock_quantity` AS `stock_quantity`, `p`.`min_stock_level` AS `min_stock_level`, `c`.`name` AS `category_name` FROM (`products` `p` left join `categories` `c` on(`c`.`id` = `p`.`category_id`)) WHERE `p`.`stock_quantity` <= `p`.`min_stock_level` ORDER BY `p`.`stock_quantity` ASC, `p`.`name` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_order_status_daily`
--
DROP TABLE IF EXISTS `v_order_status_daily`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_order_status_daily`  AS SELECT cast(`o`.`created_at` as date) AS `period`, `o`.`status` AS `status`, count(0) AS `count` FROM `orders` AS `o` GROUP BY cast(`o`.`created_at` as date), `o`.`status` ;

-- --------------------------------------------------------

--
-- Structure for view `v_sales_daily`
--
DROP TABLE IF EXISTS `v_sales_daily`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `v_sales_daily`  AS SELECT cast(`o`.`created_at` as date) AS `period`, count(distinct `o`.`id`) AS `orders`, coalesce(sum(`oi`.`quantity`),0) AS `units`, coalesce(sum(`oi`.`quantity` * `oi`.`price`),0) AS `gross`, coalesce(sum(case when `o`.`status` <> 'Cancelled' then `oi`.`quantity` * `oi`.`price` else 0 end),0) AS `net`, coalesce(avg(`o`.`total_amount`),0) AS `aov` FROM (`orders` `o` left join `order_items` `oi` on(`oi`.`order_id` = `o`.`id`)) GROUP BY cast(`o`.`created_at` as date) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `addresses`
--
ALTER TABLE `addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_budget_period` (`account_id`,`period`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_carts_customer` (`customer_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_cart_product` (`cart_id`,`product_id`),
  ADD KEY `cart_id` (`cart_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_user_product` (`user_id`,`product_id`),
  ADD KEY `fk_wishlist_product` (`product_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `commissions`
--
ALTER TABLE `commissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_commissions_order_item` (`order_item_id`),
  ADD KEY `fk_commissions_vendor` (`vendor_id`);

--
-- Indexes for table `contact_events`
--
ALTER TABLE `contact_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_contact_events_product` (`product_id`),
  ADD KEY `fk_contact_events_vendor` (`vendor_id`),
  ADD KEY `fk_contact_events_customer` (`customer_id`);

--
-- Indexes for table `couriers`
--
ALTER TABLE `couriers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_customer_addresses_customer` (`customer_id`),
  ADD KEY `fk_customer_addresses_address` (`address_id`);

--
-- Indexes for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_deliveries_created_status` (`created_at`,`status`);

--
-- Indexes for table `delivery_events`
--
ALTER TABLE `delivery_events`
  ADD PRIMARY KEY (`id`),
  ADD KEY `delivery_id` (`delivery_id`);

--
-- Indexes for table `disbursements`
--
ALTER TABLE `disbursements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_disb_vb` (`vendor_bill_id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `document_approvals`
--
ALTER TABLE `document_approvals`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_da_document` (`document_id`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fleet_vehicles`
--
ALTER TABLE `fleet_vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plate_no` (`plate_no`);

--
-- Indexes for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gr_po` (`purchase_order_id`),
  ADD KEY `fk_gr_wh` (`warehouse_id`);

--
-- Indexes for table `goods_receipt_items`
--
ALTER TABLE `goods_receipt_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_gri_gr` (`goods_receipt_id`),
  ADD KEY `fk_gri_product` (`product_id`),
  ADD KEY `fk_gri_bin` (`bin_id`);

--
-- Indexes for table `inventory_balances`
--
ALTER TABLE `inventory_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_inventory_balances` (`product_id`,`warehouse_id`,`bin_id`),
  ADD KEY `fk_ib_warehouse` (`warehouse_id`),
  ADD KEY `fk_ib_bin` (`bin_id`);

--
-- Indexes for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inv_order` (`order_id`),
  ADD KEY `fk_inv_customer` (`customer_id`);

--
-- Indexes for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_inv_items_inv` (`invoice_id`),
  ADD KEY `fk_inv_items_product` (`product_id`);

--
-- Indexes for table `journal_entries`
--
ALTER TABLE `journal_entries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `journal_lines`
--
ALTER TABLE `journal_lines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_jl_je` (`journal_entry_id`),
  ADD KEY `fk_jl_account` (`account_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_orders_created_status` (`created_at`,`status`);

--
-- Indexes for table `order_history`
--
ALTER TABLE `order_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `payments_ar`
--
ALTER TABLE `payments_ar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pay_ar_invoice` (`invoice_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`),
  ADD KEY `subcategory_id` (`subcategory_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_id` (`purchase_order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `restock_orders`
--
ALTER TABLE `restock_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `restock_order_items`
--
ALTER TABLE `restock_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `restock_order_id` (`restock_order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `returns_inbound`
--
ALTER TABLE `returns_inbound`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_ri_order` (`order_id`),
  ADD KEY `fk_ri_product` (`product_id`),
  ADD KEY `fk_ri_wh` (`warehouse_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_reviews_order_item` (`order_item_id`),
  ADD KEY `fk_reviews_product` (`product_id`),
  ADD KEY `fk_reviews_customer` (`customer_id`);

--
-- Indexes for table `route_stops`
--
ALTER TABLE `route_stops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rs_shipment` (`shipment_id`);

--
-- Indexes for table `shipments`
--
ALTER TABLE `shipments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shipment_no` (`shipment_no`);

--
-- Indexes for table `shipment_deliveries`
--
ALTER TABLE `shipment_deliveries`
  ADD PRIMARY KEY (`shipment_id`,`delivery_id`),
  ADD KEY `fk_sd_delivery` (`delivery_id`);

--
-- Indexes for table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shop_products`
--
ALTER TABLE `shop_products`
  ADD PRIMARY KEY (`shop_id`,`product_id`),
  ADD KEY `fk_shop_products_product` (`product_id`);

--
-- Indexes for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_st_from_wh` (`from_warehouse_id`),
  ADD KEY `fk_st_to_wh` (`to_warehouse_id`),
  ADD KEY `fk_st_from_bin` (`from_bin_id`),
  ADD KEY `fk_st_to_bin` (`to_bin_id`);

--
-- Indexes for table `stock_transfer_items`
--
ALTER TABLE `stock_transfer_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_sti_transfer` (`transfer_id`),
  ADD KEY `fk_sti_product` (`product_id`);

--
-- Indexes for table `subscription_packages`
--
ALTER TABLE `subscription_packages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `idx_users_otp_code` (`otp_code`),
  ADD KEY `idx_users_otp_expires` (`otp_expires_at`);

--
-- Indexes for table `vendors`
--
ALTER TABLE `vendors`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_bills`
--
ALTER TABLE `vendor_bills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vb_vendor` (`vendor_id`);

--
-- Indexes for table `vendor_bill_items`
--
ALTER TABLE `vendor_bill_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vbi_vb` (`vendor_bill_id`),
  ADD KEY `fk_vbi_product` (`product_id`);

--
-- Indexes for table `vendor_contacts`
--
ALTER TABLE `vendor_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vendor_contacts_vendor` (`vendor_id`);

--
-- Indexes for table `vendor_subscriptions`
--
ALTER TABLE `vendor_subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_vs_vendor` (`vendor_id`),
  ADD KEY `fk_vs_package` (`package_id`);

--
-- Indexes for table `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `warehouse_bins`
--
ALTER TABLE `warehouse_bins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_wh_bin` (`warehouse_id`,`code`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `addresses`
--
ALTER TABLE `addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wishlists`
--
ALTER TABLE `wishlists`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `commissions`
--
ALTER TABLE `commissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_events`
--
ALTER TABLE `contact_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `couriers`
--
ALTER TABLE `couriers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `delivery_events`
--
ALTER TABLE `delivery_events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `disbursements`
--
ALTER TABLE `disbursements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `document_approvals`
--
ALTER TABLE `document_approvals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fleet_vehicles`
--
ALTER TABLE `fleet_vehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `goods_receipt_items`
--
ALTER TABLE `goods_receipt_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_balances`
--
ALTER TABLE `inventory_balances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `invoice_items`
--
ALTER TABLE `invoice_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_entries`
--
ALTER TABLE `journal_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `journal_lines`
--
ALTER TABLE `journal_lines`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_history`
--
ALTER TABLE `order_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments_ar`
--
ALTER TABLE `payments_ar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restock_orders`
--
ALTER TABLE `restock_orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `restock_order_items`
--
ALTER TABLE `restock_order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `returns_inbound`
--
ALTER TABLE `returns_inbound`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `route_stops`
--
ALTER TABLE `route_stops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shipments`
--
ALTER TABLE `shipments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shops`
--
ALTER TABLE `shops`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `stock_transfer_items`
--
ALTER TABLE `stock_transfer_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscription_packages`
--
ALTER TABLE `subscription_packages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendors`
--
ALTER TABLE `vendors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `vendor_bills`
--
ALTER TABLE `vendor_bills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_bill_items`
--
ALTER TABLE `vendor_bill_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_contacts`
--
ALTER TABLE `vendor_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_subscriptions`
--
ALTER TABLE `vendor_subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `warehouse_bins`
--
ALTER TABLE `warehouse_bins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `budgets`
--
ALTER TABLE `budgets`
  ADD CONSTRAINT `fk_budget_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `fk_carts_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `fk_cart_items_cart` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_cart_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `wishlists`
--
ALTER TABLE `wishlists`
  ADD CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `fk_subcategories_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `commissions`
--
ALTER TABLE `commissions`
  ADD CONSTRAINT `fk_commissions_order_item` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_commissions_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contact_events`
--
ALTER TABLE `contact_events`
  ADD CONSTRAINT `fk_contact_events_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_contact_events_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_contact_events_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `customer_addresses`
--
ALTER TABLE `customer_addresses`
  ADD CONSTRAINT `fk_customer_addresses_address` FOREIGN KEY (`address_id`) REFERENCES `addresses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_customer_addresses_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deliveries`
--
ALTER TABLE `deliveries`
  ADD CONSTRAINT `fk_deliveries_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `delivery_events`
--
ALTER TABLE `delivery_events`
  ADD CONSTRAINT `fk_delivery_events_delivery` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `disbursements`
--
ALTER TABLE `disbursements`
  ADD CONSTRAINT `fk_disb_vb` FOREIGN KEY (`vendor_bill_id`) REFERENCES `vendor_bills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `document_approvals`
--
ALTER TABLE `document_approvals`
  ADD CONSTRAINT `fk_da_document` FOREIGN KEY (`document_id`) REFERENCES `documents` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `goods_receipts`
--
ALTER TABLE `goods_receipts`
  ADD CONSTRAINT `fk_gr_po` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_gr_wh` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `goods_receipt_items`
--
ALTER TABLE `goods_receipt_items`
  ADD CONSTRAINT `fk_gri_bin` FOREIGN KEY (`bin_id`) REFERENCES `warehouse_bins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_gri_gr` FOREIGN KEY (`goods_receipt_id`) REFERENCES `goods_receipts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_gri_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `inventory_balances`
--
ALTER TABLE `inventory_balances`
  ADD CONSTRAINT `fk_ib_bin` FOREIGN KEY (`bin_id`) REFERENCES `warehouse_bins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ib_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ib_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `inventory_logs`
--
ALTER TABLE `inventory_logs`
  ADD CONSTRAINT `fk_inventory_logs_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `fk_inv_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_inv_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `invoice_items`
--
ALTER TABLE `invoice_items`
  ADD CONSTRAINT `fk_inv_items_inv` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_inv_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `journal_lines`
--
ALTER TABLE `journal_lines`
  ADD CONSTRAINT `fk_jl_account` FOREIGN KEY (`account_id`) REFERENCES `accounts` (`id`),
  ADD CONSTRAINT `fk_jl_je` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `order_history`
--
ALTER TABLE `order_history`
  ADD CONSTRAINT `fk_order_history_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments_ar`
--
ALTER TABLE `payments_ar`
  ADD CONSTRAINT `fk_pay_ar_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_products_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_products_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `fk_product_images_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `fk_po_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `fk_poi_po` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_poi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `restock_orders`
--
ALTER TABLE `restock_orders`
  ADD CONSTRAINT `fk_restock_orders_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `restock_order_items`
--
ALTER TABLE `restock_order_items`
  ADD CONSTRAINT `fk_roi_order` FOREIGN KEY (`restock_order_id`) REFERENCES `restock_orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_roi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `returns_inbound`
--
ALTER TABLE `returns_inbound`
  ADD CONSTRAINT `fk_ri_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ri_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_ri_wh` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`);

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_customer` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_order_item` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `route_stops`
--
ALTER TABLE `route_stops`
  ADD CONSTRAINT `fk_rs_shipment` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shipment_deliveries`
--
ALTER TABLE `shipment_deliveries`
  ADD CONSTRAINT `fk_sd_delivery` FOREIGN KEY (`delivery_id`) REFERENCES `deliveries` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_sd_shipment` FOREIGN KEY (`shipment_id`) REFERENCES `shipments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shop_products`
--
ALTER TABLE `shop_products`
  ADD CONSTRAINT `fk_shop_products_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_shop_products_shop` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `stock_transfers`
--
ALTER TABLE `stock_transfers`
  ADD CONSTRAINT `fk_st_from_bin` FOREIGN KEY (`from_bin_id`) REFERENCES `warehouse_bins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_st_from_wh` FOREIGN KEY (`from_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_st_to_bin` FOREIGN KEY (`to_bin_id`) REFERENCES `warehouse_bins` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_st_to_wh` FOREIGN KEY (`to_warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `stock_transfer_items`
--
ALTER TABLE `stock_transfer_items`
  ADD CONSTRAINT `fk_sti_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  ADD CONSTRAINT `fk_sti_transfer` FOREIGN KEY (`transfer_id`) REFERENCES `stock_transfers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_bills`
--
ALTER TABLE `vendor_bills`
  ADD CONSTRAINT `fk_vb_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vendor_bill_items`
--
ALTER TABLE `vendor_bill_items`
  ADD CONSTRAINT `fk_vbi_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_vbi_vb` FOREIGN KEY (`vendor_bill_id`) REFERENCES `vendor_bills` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_contacts`
--
ALTER TABLE `vendor_contacts`
  ADD CONSTRAINT `fk_vendor_contacts_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `vendor_subscriptions`
--
ALTER TABLE `vendor_subscriptions`
  ADD CONSTRAINT `fk_vs_package` FOREIGN KEY (`package_id`) REFERENCES `subscription_packages` (`id`),
  ADD CONSTRAINT `fk_vs_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `vendors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warehouse_bins`
--
ALTER TABLE `warehouse_bins`
  ADD CONSTRAINT `fk_bins_warehouse` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE;
--
-- Database: `hr_core`
--
CREATE DATABASE IF NOT EXISTS `hr_core` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `hr_core`;

-- --------------------------------------------------------

--
-- Table structure for table `competencies`
--

CREATE TABLE `competencies` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('technical','behavioral','leadership','functional','soft_skills') NOT NULL,
  `levels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`levels`)),
  `is_core` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competencies`
--

INSERT INTO `competencies` (`id`, `name`, `description`, `category`, `levels`, `is_core`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Team Leadership', 'The ability to guide, motivate, and develop team members to achieve organizational goals. Includes setting direction, providing support, managing performance, resolving conflicts, and fostering a positive team culture.', 'leadership', '[{\"name\":\"Advanced\",\"description\":\"Experienced leader who can manage diverse teams and complex projects Skills: Strategic leadership, Change management, Talent development, Cross-functional collaboration, Innovation leadership\",\"skills\":[\"Strategic leadership\",\" Change management\",\" Talent development\",\" Cross-functional collaboration\",\" Innovation leadership\"]}]', 1, 1, '2025-09-20 05:55:58', '2025-09-20 05:55:58');

-- --------------------------------------------------------

--
-- Table structure for table `competency_assessments`
--

CREATE TABLE `competency_assessments` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `competency_id` int(11) NOT NULL,
  `current_level` enum('beginner','intermediate','advanced','expert') NOT NULL,
  `target_level` enum('beginner','intermediate','advanced','expert') DEFAULT NULL,
  `rating` int(11) DEFAULT NULL CHECK (`rating` >= 1 and `rating` <= 5),
  `evidence` text DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `development_areas` text DEFAULT NULL,
  `assessment_type` enum('self','manager','peer','360','hr') NOT NULL,
  `assessor_id` int(11) NOT NULL,
  `assessment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `competency_assessments`
--

INSERT INTO `competency_assessments` (`id`, `employee_id`, `competency_id`, `current_level`, `target_level`, `rating`, `evidence`, `strengths`, `development_areas`, `assessment_type`, `assessor_id`, `assessment_date`) VALUES
(1, 6, 1, 'beginner', 'advanced', 3, 'I have been working on improving my customer service skills over the past month. I successfully handled 15 customer complaints this week with a 90% resolution rate. I\'ve been practicing active listening techniques and have received positive feedback from customers. I also completed the customer service training module and applied the new techniques in my daily interactions.', '- Good listening skills and empathy\r\n- Willingness to learn and improve\r\n- Positive attitude with customers\r\n- Basic product knowledge\r\n- Punctuality and reliability', '- Advanced conflict resolution techniques\r\n- Product knowledge for complex technical issues\r\n- Upselling and cross-selling skills\r\n- Handling difficult customers\r\n- Time management during peak hours', 'manager', 1, '2025-09-19 16:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `category` enum('technical','soft_skills','compliance','leadership','safety','product','process') NOT NULL,
  `level` enum('beginner','intermediate','advanced','expert') NOT NULL,
  `duration` int(11) DEFAULT NULL,
  `format` enum('online','in_person','blended','self_paced','instructor_led') NOT NULL,
  `instructor_name` varchar(100) DEFAULT NULL,
  `instructor_email` varchar(100) DEFAULT NULL,
  `content` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`content`)),
  `status` enum('draft','published','archived','suspended') DEFAULT 'draft',
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `title`, `description`, `category`, `level`, `duration`, `format`, `instructor_name`, `instructor_email`, `content`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(2, 'Introduction to Project Management', 'Learn the fundamentals of project management including planning, execution, monitoring, and closing projects. This course covers essential PM methodologies, tools, and best practices.', 'leadership', 'beginner', 120, 'online', 'Sarah Johnson', 'sarah.johnson@company.com', '{\"modules\":[{\"title\":\"Project Management Fundamentals\",\"description\":\"Understanding what project management is and why it matters\",\"duration\":\"30\",\"objectives\":[\"Define project management and its importance\",\"Understand the project lifecycle\",\"Identify key project management roles\"]},{\"title\":\"Project Planning and Initiation\",\"description\":\"How to properly plan and initiate a project\",\"duration\":\"45\",\"objectives\":[\"Create project charters and scope statements\",\"Develop work breakdown structures\",\"Identify and manage project stakeholders\"]},{\"title\":\"Project Execution and Monitoring\",\"description\":\"Managing project execution and tracking progress\",\"duration\":\"45\",\"objectives\":[\"Implement project plans effectively\",\"Monitor project progress and performance\",\"Manage project risks and issues\"]}],\"prerequisites\":\"Basic understanding of business operations\",\"learning_outcomes\":\"Students will be able to plan, execute, and monitor projects effectively using industry-standard methodologies.\",\"resources\":\"Project management software access, templates, and case studies\"}', 'published', 1, '2025-09-20 10:18:03', '2025-09-20 10:18:03'),
(3, 'Advanced Excel for Data Analysis', 'Master advanced Excel features for data analysis, including pivot tables, advanced formulas, data visualization, and automation techniques.', 'technical', 'intermediate', 180, 'blended', 'Michael Chen', 'michael.chen@company.com', '{\"modules\":[{\"title\":\"Advanced Formulas and Functions\",\"description\":\"Master complex Excel formulas and functions for data manipulation\",\"duration\":\"60\",\"objectives\":[\"Use VLOOKUP, INDEX, and MATCH functions\",\"Create nested formulas and array formulas\",\"Implement conditional logic with IF statements\"]},{\"title\":\"Pivot Tables and Data Analysis\",\"description\":\"Create and customize pivot tables for data analysis\",\"duration\":\"60\",\"objectives\":[\"Create and format pivot tables\",\"Use pivot charts for data visualization\",\"Apply filters and slicers effectively\"]},{\"title\":\"Data Visualization and Dashboards\",\"description\":\"Create compelling charts and interactive dashboards\",\"duration\":\"60\",\"objectives\":[\"Design effective charts and graphs\",\"Create interactive dashboards\",\"Use conditional formatting for data insights\"]}],\"prerequisites\":\"Basic Excel knowledge and familiarity with spreadsheets\",\"learning_outcomes\":\"Students will be able to perform complex data analysis and create professional reports using Excel.\",\"resources\":\"Excel 2019 or later, sample datasets, practice exercises\"}', 'published', 1, '2025-09-20 10:18:03', '2025-09-20 10:18:03'),
(4, 'Workplace Safety and Emergency Procedures', 'Essential safety training covering workplace hazards, emergency procedures, first aid basics, and compliance with safety regulations.', 'safety', 'beginner', 90, 'in_person', 'David Rodriguez', 'david.rodriguez@company.com', '{\"modules\":[{\"title\":\"Workplace Hazard Identification\",\"description\":\"Learn to identify common workplace hazards and risks\",\"duration\":\"30\",\"objectives\":[\"Identify physical, chemical, and biological hazards\\r\",\"Understand hazard assessment procedures\\r\",\"Recognize warning signs and safety equipment\"]},{\"title\":\"Emergency Response Procedures\",\"description\":\"Proper procedures for various emergency situations\",\"duration\":\"30\",\"objectives\":[\"Follow fire evacuation procedures\\r\",\"Respond to medical emergencies\\r\",\"Use emergency communication systems\"]},{\"title\":\"First Aid and CPR Basics\",\"description\":\"Basic first aid and CPR techniques for workplace emergencies\",\"duration\":\"30\",\"objectives\":[\"Perform basic first aid procedures\\r\",\"Administer CPR when necessary\\r\",\"Use automated external defibrillators (AED)\"]}],\"prerequisites\":\"None - suitable for all employees\",\"learning_outcomes\":\"Students will be able to identify hazards, respond to emergencies, and provide basic first aid.\",\"resources\":\"First aid supplies, safety equipment, emergency procedure manuals\"}', 'published', 1, '2025-09-20 10:18:03', '2025-09-20 11:16:28'),
(5, 'Effective Communication Skills', 'Develop strong communication skills for professional success, including verbal, written, and non-verbal communication techniques.', 'soft_skills', 'intermediate', 150, 'online', 'Jennifer Williams', 'jennifer.williams@company.com', '{\"modules\":[{\"title\":\"Verbal Communication Excellence\",\"description\":\"Master the art of clear and effective verbal communication\",\"duration\":\"50\",\"objectives\":[\"Speak clearly and confidently\",\"Use appropriate tone and pace\",\"Handle difficult conversations effectively\"]},{\"title\":\"Written Communication Mastery\",\"description\":\"Write professional emails, reports, and documents\",\"duration\":\"50\",\"objectives\":[\"Write clear and concise business emails\",\"Structure professional reports\",\"Use proper grammar and formatting\"]},{\"title\":\"Non-verbal Communication and Body Language\",\"description\":\"Understand and use body language effectively\",\"duration\":\"50\",\"objectives\":[\"Read and interpret body language\",\"Use positive body language in presentations\",\"Build rapport through non-verbal cues\"]}],\"prerequisites\":\"Basic English proficiency\",\"learning_outcomes\":\"Students will communicate more effectively in professional settings.\",\"resources\":\"Communication templates, practice exercises, video examples\"}', 'published', 1, '2025-09-20 10:18:03', '2025-09-20 10:18:03'),
(6, 'Cybersecurity Awareness Training', 'Essential cybersecurity training to protect against common threats, including phishing, malware, and data breaches.', 'compliance', 'beginner', 75, 'self_paced', 'Alex Thompson', 'alex.thompson@company.com', '{\"modules\":[{\"title\":\"Understanding Cyber Threats\",\"description\":\"Learn about common cybersecurity threats and attack vectors\",\"duration\":\"25\",\"objectives\":[\"Identify common cyber threats\",\"Understand social engineering attacks\",\"Recognize phishing attempts\"]},{\"title\":\"Password Security and Authentication\",\"description\":\"Best practices for password management and authentication\",\"duration\":\"25\",\"objectives\":[\"Create strong passwords\",\"Use multi-factor authentication\",\"Manage password security effectively\"]},{\"title\":\"Data Protection and Privacy\",\"description\":\"Protecting sensitive data and maintaining privacy\",\"duration\":\"25\",\"objectives\":[\"Understand data classification\",\"Follow data handling procedures\",\"Report security incidents\"]}],\"prerequisites\":\"Basic computer literacy\",\"learning_outcomes\":\"Students will be able to identify and prevent common cybersecurity threats.\",\"resources\":\"Security policies, incident reporting forms, practice scenarios\"}', 'published', 1, '2025-09-20 10:18:03', '2025-09-20 10:18:03'),
(7, 'Leadership and Team Management', 'Develop essential leadership skills including team building, motivation, conflict resolution, and performance management.', 'leadership', 'advanced', 240, 'instructor_led', 'Dr. Maria Garcia', 'maria.garcia@company.com', '{\"modules\":[{\"title\":\"Leadership Fundamentals\",\"description\":\"Core principles of effective leadership\",\"duration\":\"60\",\"objectives\":[\"Understand different leadership styles\",\"Develop your personal leadership philosophy\",\"Build emotional intelligence\"]},{\"title\":\"Team Building and Motivation\",\"description\":\"Strategies for building high-performing teams\",\"duration\":\"60\",\"objectives\":[\"Build and maintain effective teams\",\"Motivate team members effectively\",\"Foster collaboration and trust\"]},{\"title\":\"Conflict Resolution and Performance Management\",\"description\":\"Handling conflicts and managing team performance\",\"duration\":\"60\",\"objectives\":[\"Resolve conflicts constructively\",\"Conduct performance reviews\",\"Provide effective feedback\"]},{\"title\":\"Strategic Thinking and Decision Making\",\"description\":\"Advanced leadership skills for strategic decision making\",\"duration\":\"60\",\"objectives\":[\"Think strategically about business challenges\",\"Make informed decisions under pressure\",\"Lead organizational change\"]}],\"prerequisites\":\"Management experience or supervisory role\",\"learning_outcomes\":\"Students will be able to lead teams effectively and drive organizational success.\",\"resources\":\"Leadership assessments, case studies, team exercises\"}', 'published', 1, '2025-09-20 10:18:03', '2025-09-20 10:18:03'),
(8, 'Customer Service Excellence', 'Master the art of exceptional customer service, including handling difficult customers, building relationships, and exceeding expectations.', 'soft_skills', 'intermediate', 120, 'blended', 'Lisa Anderson', 'lisa.anderson@company.com', '{\"modules\":[{\"title\":\"Customer Service Fundamentals\",\"description\":\"Core principles of excellent customer service\",\"duration\":\"40\",\"objectives\":[\"Understand customer needs and expectations\",\"Apply service excellence principles\",\"Build positive customer relationships\"]},{\"title\":\"Handling Difficult Customers\",\"description\":\"Techniques for managing challenging customer interactions\",\"duration\":\"40\",\"objectives\":[\"De-escalate tense situations\",\"Resolve customer complaints effectively\",\"Turn negative experiences into positive ones\"]},{\"title\":\"Communication and Problem Solving\",\"description\":\"Advanced communication skills for customer service\",\"duration\":\"40\",\"objectives\":[\"Communicate clearly and empathetically\",\"Solve customer problems efficiently\",\"Follow up effectively\"]}],\"prerequisites\":\"Basic communication skills\",\"learning_outcomes\":\"Students will provide exceptional customer service and handle challenging situations professionally.\",\"resources\":\"Customer service scripts, role-play scenarios, feedback forms\"}', 'published', 1, '2025-09-20 10:18:03', '2025-09-20 10:18:03'),
(9, 'Financial Analysis and Reporting', 'Learn to analyze financial data, create reports, and make data-driven business decisions using financial metrics and tools.', 'technical', 'advanced', 200, 'online', 'Robert Kim', 'robert.kim@company.com', '{\"modules\":[{\"title\":\"Financial Statement Analysis\",\"description\":\"Understanding and analyzing financial statements\",\"duration\":\"50\",\"objectives\":[\"Read and interpret financial statements\",\"Calculate key financial ratios\",\"Identify financial trends and patterns\"]},{\"title\":\"Budgeting and Forecasting\",\"description\":\"Creating budgets and financial forecasts\",\"duration\":\"50\",\"objectives\":[\"Develop comprehensive budgets\",\"Create financial forecasts\",\"Monitor budget performance\"]},{\"title\":\"Financial Reporting and Dashboards\",\"description\":\"Creating effective financial reports and dashboards\",\"duration\":\"50\",\"objectives\":[\"Design financial reports\",\"Create executive dashboards\",\"Present financial data effectively\"]},{\"title\":\"Investment Analysis and Decision Making\",\"description\":\"Evaluating investments and making financial decisions\",\"duration\":\"50\",\"objectives\":[\"Evaluate investment opportunities\",\"Use financial models for decision making\",\"Assess risk and return\"]}],\"prerequisites\":\"Basic accounting knowledge and Excel proficiency\",\"learning_outcomes\":\"Students will be able to analyze financial data and create comprehensive financial reports.\",\"resources\":\"Financial analysis tools, sample datasets, reporting templates\"}', 'published', 1, '2025-09-20 10:18:03', '2025-09-20 10:18:03');

-- --------------------------------------------------------

--
-- Table structure for table `course_enrollments`
--

CREATE TABLE `course_enrollments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `status` enum('enrolled','in_progress','completed','dropped','failed') DEFAULT 'enrolled',
  `enrollment_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `enrolled_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `completion_date` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `progress` int(11) DEFAULT 0,
  `score` decimal(5,2) DEFAULT NULL,
  `enrollment_notes` text DEFAULT NULL,
  `enrolled_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_enrollments`
--

INSERT INTO `course_enrollments` (`id`, `user_id`, `course_id`, `status`, `enrollment_date`, `enrolled_at`, `completion_date`, `completed_at`, `progress`, `score`, `enrollment_notes`, `enrolled_by`, `created_at`, `updated_at`) VALUES
(1, 4, 2, 'enrolled', '2025-09-20 10:27:13', '2025-09-20 11:15:24', NULL, NULL, 0, NULL, NULL, NULL, '2025-09-20 11:15:24', '2025-09-20 11:15:24'),
(2, 4, 3, 'completed', '2025-09-20 10:33:26', '2025-09-20 11:15:24', NULL, '2025-09-20 12:02:57', 100, 100.00, NULL, NULL, '2025-09-20 11:15:24', '2025-09-20 12:02:57'),
(3, 4, 4, 'enrolled', '2025-09-20 10:42:52', '2025-09-20 11:15:24', NULL, NULL, 0, NULL, NULL, NULL, '2025-09-20 11:15:24', '2025-09-20 11:15:24'),
(4, 4, 5, 'enrolled', '2025-09-20 10:56:01', '2025-09-20 11:15:24', NULL, NULL, 0, NULL, NULL, NULL, '2025-09-20 11:15:24', '2025-09-20 11:15:24'),
(5, 6, 4, 'completed', '2025-09-20 11:15:42', '2025-09-20 11:15:42', NULL, '2025-09-20 05:53:43', 100, 100.00, '', 1, '2025-09-20 11:15:42', '2025-09-20 11:53:43'),
(6, 5, 5, 'completed', '2025-09-20 11:17:47', '2025-09-20 11:17:47', NULL, '2025-09-20 11:11:19', 100, 100.00, '', 1, '2025-09-20 11:17:47', '2025-09-20 17:11:19');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `code` varchar(20) NOT NULL,
  `head_id` int(11) DEFAULT NULL,
  `budget` decimal(15,2) DEFAULT NULL,
  `location` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`, `description`, `code`, `head_id`, `budget`, `location`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Human Resources', 'Human Resources Department', 'HR', 2, 500000.00, 'Main Office', 1, '2025-09-19 14:29:34', '2025-09-19 14:29:34'),
(2, 'Information Technology', 'IT Department', 'IT', 3, 750000.00, 'Tech Building', 1, '2025-09-19 14:29:34', '2025-09-19 14:29:34'),
(3, 'Finance', 'Finance Department', 'FIN', NULL, 400000.00, 'Main Office', 1, '2025-09-19 14:29:34', '2025-09-19 14:29:34'),
(4, 'Marketing', 'Marketing Department', 'MKT', NULL, 300000.00, 'Main Office', 1, '2025-09-19 14:29:34', '2025-09-19 14:29:34'),
(5, 'Operations', 'Operations Department', 'OPS', NULL, 600000.00, 'Warehouse', 1, '2025-09-19 14:29:34', '2025-09-19 14:29:34');

-- --------------------------------------------------------

--
-- Table structure for table `employee_benefits`
--

CREATE TABLE `employee_benefits` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `benefit_type` varchar(100) NOT NULL,
  `benefit_name` varchar(100) NOT NULL,
  `coverage_amount` decimal(10,2) DEFAULT NULL,
  `employee_contribution` decimal(10,2) DEFAULT 0.00,
  `company_contribution` decimal(10,2) DEFAULT 0.00,
  `effective_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_benefits`
--

INSERT INTO `employee_benefits` (`id`, `employee_id`, `benefit_type`, `benefit_name`, `coverage_amount`, `employee_contribution`, `company_contribution`, `effective_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Health Insurance', 'Comprehensive Health Coverage', 500000.00, 2000.00, 3000.00, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 1, 'Life Insurance', 'Group Life Insurance', 1000000.00, 500.00, 1000.00, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 2, 'Health Insurance', 'Comprehensive Health Coverage', 500000.00, 2000.00, 3000.00, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(4, 2, 'Life Insurance', 'Group Life Insurance', 1000000.00, 500.00, 1000.00, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(5, 3, 'Health Insurance', 'Comprehensive Health Coverage', 500000.00, 2000.00, 3000.00, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(6, 4, 'Health Insurance', 'Comprehensive Health Coverage', 500000.00, 2000.00, 3000.00, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(7, 5, 'Health Insurance', 'Basic Health Coverage', 200000.00, 1000.00, 1500.00, '2025-09-20', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(8, 6, 'Health Insurance', 'Basic Health Coverage', 200000.00, 1000.00, 1500.00, '2025-09-20', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(9, 7, 'Health Insurance', 'Comprehensive Health Coverage', 500000.00, 2000.00, 3000.00, '2025-09-21', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `document_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `is_confidential` tinyint(1) DEFAULT 0,
  `uploaded_by` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expiry_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_emergency_contacts`
--

CREATE TABLE `employee_emergency_contacts` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `contact_name` varchar(100) NOT NULL,
  `relationship` varchar(50) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_emergency_contacts`
--

INSERT INTO `employee_emergency_contacts` (`id`, `employee_id`, `contact_name`, `relationship`, `phone`, `email`, `address`, `is_primary`, `created_at`, `updated_at`) VALUES
(1, 1, 'Jane Admin', 'Spouse', '09171234567', 'jane.admin@email.com', '123 Main St, Quezon City', 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 1, 'John Admin Sr', 'Father', '09171234568', 'john.sr@email.com', '456 Oak St, Manila', 0, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 2, 'Maria Manager', 'Spouse', '09171234569', 'maria.manager@email.com', '789 Pine St, Makati', 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(4, 3, 'Robert Manager', 'Brother', '09171234570', 'robert.manager@email.com', '321 Elm St, Taguig', 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(5, 4, 'Sarah Developer', 'Mother', '09171234571', 'sarah.dev@email.com', '654 Maple St, Pasig', 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(6, 5, 'Michael Tablac', 'Father', '09951808635', 'michael.tablac@email.com', 'Insurance St, QC', 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(7, 6, 'Ana Tablac', 'Mother', '09951808636', 'ana.tablac@email.com', 'Insurance St, QC', 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(8, 7, 'Lisa Johnson', 'Sister', '09171234572', 'lisa.johnson@email.com', '987 Cedar St, Mandaluyong', 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `employee_salaries`
--

CREATE TABLE `employee_salaries` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `structure_id` int(11) NOT NULL,
  `basic_salary` decimal(10,2) NOT NULL,
  `effective_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_salaries`
--

INSERT INTO `employee_salaries` (`id`, `employee_id`, `structure_id`, `basic_salary`, `effective_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 80000.00, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 2, 2, 70000.00, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 3, 1, 65000.00, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(4, 4, 1, 55000.00, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(5, 5, 3, 6500.00, '2025-09-20', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(6, 6, 3, 6500.00, '2025-09-20', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(7, 7, 1, 42500.00, '2025-09-21', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `employee_skills`
--

CREATE TABLE `employee_skills` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `skill_name` varchar(100) NOT NULL,
  `skill_level` enum('Beginner','Intermediate','Advanced','Expert') NOT NULL,
  `years_experience` int(11) DEFAULT NULL,
  `certification` varchar(255) DEFAULT NULL,
  `certification_date` date DEFAULT NULL,
  `is_verified` tinyint(1) DEFAULT 0,
  `verified_by` int(11) DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_skills`
--

INSERT INTO `employee_skills` (`id`, `employee_id`, `skill_name`, `skill_level`, `years_experience`, `certification`, `certification_date`, `is_verified`, `verified_by`, `verified_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'Project Management', 'Expert', 8, 'PMP Certification', '2023-06-15', 1, 1, '2025-01-15 10:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 1, 'Leadership', 'Advanced', 5, 'Leadership Excellence Program', '2024-03-20', 1, 1, '2025-01-15 10:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 2, 'HR Management', 'Expert', 10, 'SHRM-CP Certification', '2022-09-10', 1, 1, '2025-01-15 10:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(4, 2, 'Recruitment', 'Advanced', 7, 'Certified Talent Acquisition Professional', '2023-11-05', 1, 1, '2025-01-15 10:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(5, 3, 'Team Management', 'Advanced', 6, 'Management Development Program', '2024-01-15', 1, 1, '2025-01-15 10:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(6, 4, 'PHP Programming', 'Expert', 5, 'Zend Certified PHP Engineer', '2023-08-20', 1, 1, '2025-01-15 10:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(7, 4, 'JavaScript', 'Advanced', 4, 'JavaScript Developer Certification', '2024-02-10', 1, 1, '2025-01-15 10:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(8, 4, 'Database Management', 'Advanced', 4, 'MySQL Database Administrator', '2023-12-05', 1, 1, '2025-01-15 10:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(9, 5, 'Customer Service', 'Intermediate', 2, 'Customer Service Excellence', '2024-06-01', 1, 2, '2025-09-20 14:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(10, 6, 'Administrative Support', 'Intermediate', 1, 'Office Administration Certificate', '2024-08-15', 1, 2, '2025-09-20 14:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(11, 7, 'Software Development', 'Advanced', 3, 'Full Stack Developer Certification', '2024-04-20', 1, 1, '2025-09-21 09:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(12, 7, 'Agile Methodology', 'Intermediate', 2, 'Certified Scrum Master', '2024-07-10', 1, 1, '2025-09-21 09:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `requires_receipt` tinyint(1) DEFAULT 1,
  `max_amount` decimal(10,2) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_categories`
--

INSERT INTO `expense_categories` (`id`, `name`, `code`, `description`, `requires_receipt`, `max_amount`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Meals and Entertainment', 'MEAL', 'Business meals and client entertainment', 1, 2000.00, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 'Transportation', 'TRANS', 'Business travel and transportation', 1, 5000.00, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 'Office Supplies', 'SUPPLY', 'Office materials and supplies', 1, 1000.00, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(4, 'Training and Development', 'TRAIN', 'Professional development and training', 1, 10000.00, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(5, 'Communication', 'COMM', 'Phone, internet, and communication expenses', 1, 2000.00, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(6, 'Equipment', 'EQUIP', 'Office equipment and tools', 1, 50000.00, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `expense_claims`
--

CREATE TABLE `expense_claims` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `claim_number` varchar(50) NOT NULL,
  `claim_date` date NOT NULL,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `status` enum('draft','submitted','approved','rejected','paid') DEFAULT 'draft',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_claims`
--

INSERT INTO `expense_claims` (`id`, `employee_id`, `claim_number`, `claim_date`, `total_amount`, `status`, `submitted_at`, `approved_by`, `approved_at`, `rejection_reason`, `payment_date`, `payment_method`, `notes`, `created_at`, `updated_at`) VALUES
(1, 4, 'EXP-2025-001', '2025-09-15', 1500.00, 'approved', '2025-09-15 10:00:00', 1, '2025-09-16 14:00:00', NULL, NULL, NULL, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 5, 'EXP-2025-002', '2025-09-18', 2500.00, 'approved', '2025-09-18 11:00:00', 1, '2025-09-19 09:00:00', NULL, NULL, NULL, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 6, 'EXP-2025-003', '2025-09-20', 800.00, '', '2025-09-20 15:00:00', NULL, NULL, NULL, NULL, NULL, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `expense_items`
--

CREATE TABLE `expense_items` (
  `id` int(11) NOT NULL,
  `claim_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `expense_date` date NOT NULL,
  `description` text NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `receipt_path` varchar(255) DEFAULT NULL,
  `is_billable` tinyint(1) DEFAULT 0,
  `client_code` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `expense_items`
--

INSERT INTO `expense_items` (`id`, `claim_id`, `category_id`, `expense_date`, `description`, `amount`, `receipt_path`, `is_billable`, `client_code`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-09-10', 'Client lunch meeting', 800.00, 'receipts/meal_001.jpg', 1, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 1, 2, '2025-09-10', 'Taxi fare to client office', 200.00, 'receipts/taxi_001.jpg', 1, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 1, 3, '2025-09-12', 'Office supplies for project', 500.00, 'receipts/supplies_001.jpg', 0, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(4, 2, 2, '2025-09-15', 'Flight to Manila for training', 2000.00, 'receipts/flight_001.jpg', 1, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(5, 2, 1, '2025-09-15', 'Meals during training', 500.00, 'receipts/meal_002.jpg', 1, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(6, 3, 4, '2025-09-18', 'Online course subscription', 800.00, 'receipts/course_001.jpg', 0, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `hr_metrics`
--

CREATE TABLE `hr_metrics` (
  `id` int(11) NOT NULL,
  `metric_name` varchar(100) NOT NULL,
  `metric_type` enum('headcount','turnover','attendance','performance','compensation','recruitment') NOT NULL,
  `metric_value` decimal(15,4) NOT NULL,
  `measurement_date` date NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_metrics`
--

INSERT INTO `hr_metrics` (`id`, `metric_name`, `metric_type`, `metric_value`, `measurement_date`, `department_id`, `created_at`) VALUES
(1, 'Total Headcount', 'headcount', 7.0000, '2025-09-21', NULL, '2025-09-21 09:02:58'),
(2, 'Headcount - HR', 'headcount', 2.0000, '2025-09-21', 1, '2025-09-21 09:02:58'),
(3, 'Headcount - IT', 'headcount', 2.0000, '2025-09-21', 2, '2025-09-21 09:02:58'),
(4, 'Headcount - Finance', 'headcount', 1.0000, '2025-09-21', 3, '2025-09-21 09:02:58'),
(5, 'Headcount - Marketing', 'headcount', 1.0000, '2025-09-21', 4, '2025-09-21 09:02:58'),
(6, 'Headcount - Operations', 'headcount', 1.0000, '2025-09-21', 5, '2025-09-21 09:02:58'),
(7, 'Average Attendance Rate', 'attendance', 95.5000, '2025-09-21', NULL, '2025-09-21 09:02:58'),
(8, 'Turnover Rate', 'turnover', 5.2000, '2025-09-21', NULL, '2025-09-21 09:02:58'),
(9, 'Average Performance Rating', 'performance', 4.2000, '2025-09-21', NULL, '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `hr_reports`
--

CREATE TABLE `hr_reports` (
  `id` int(11) NOT NULL,
  `report_name` varchar(100) NOT NULL,
  `report_type` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `query` longtext NOT NULL,
  `parameters` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`parameters`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hr_reports`
--

INSERT INTO `hr_reports` (`id`, `report_name`, `report_type`, `description`, `query`, `parameters`, `is_active`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Employee Directory', 'directory', 'Complete employee directory with contact information', 'SELECT u.*, d.name as department_name FROM users u LEFT JOIN departments d ON u.department_id = d.id WHERE u.status = \"active\"', '[]', 1, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 'Leave Summary Report', 'leave', 'Summary of leave balances and usage by employee', 'SELECT u.first_name, u.last_name, lt.name as leave_type, lb.allocated_days, lb.used_days, lb.remaining_days FROM users u JOIN leave_balances lb ON u.id = lb.employee_id JOIN leave_types lt ON lb.leave_type_id = lt.id WHERE lb.year = ?', '[{\"name\": \"year\", \"type\": \"number\", \"required\": true}]', 1, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 'Payroll Summary Report', 'payroll', 'Monthly payroll summary by department', 'SELECT d.name as department, COUNT(ps.employee_id) as employee_count, SUM(ps.gross_pay) as total_gross, SUM(ps.total_deductions) as total_deductions, SUM(ps.net_pay) as total_net FROM payroll_summaries ps JOIN users u ON ps.employee_id = u.id JOIN departments d ON u.department_id = d.id WHERE ps.payroll_run_id = ? GROUP BY d.id, d.name', '[{\"name\": \"payroll_run_id\", \"type\": \"number\", \"required\": true}]', 1, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `job_applications`
--

CREATE TABLE `job_applications` (
  `id` int(11) NOT NULL,
  `job_posting_id` int(11) NOT NULL,
  `candidate_first_name` varchar(50) NOT NULL,
  `candidate_last_name` varchar(50) NOT NULL,
  `candidate_email` varchar(100) NOT NULL,
  `candidate_phone` varchar(20) DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `cover_letter_path` varchar(255) DEFAULT NULL,
  `status` enum('applied','screening','interview','offer','hired','rejected','withdrawn') DEFAULT 'applied',
  `applied_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `source` varchar(50) DEFAULT NULL,
  `referral_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_applications`
--

INSERT INTO `job_applications` (`id`, `job_posting_id`, `candidate_first_name`, `candidate_last_name`, `candidate_email`, `candidate_phone`, `resume_path`, `cover_letter_path`, `status`, `applied_date`, `source`, `referral_id`, `notes`) VALUES
(1, 1, 'gilbert', 'tablac', 'tablacgilbert08@gmail.com', '09951808634', 'uploads/applications/resume_1758304266_68cd980a6c2d6.pdf', 'uploads/applications/cover_1758304266_68cd980a6c71b.docx', 'hired', '2025-09-19 17:51:06', 'linkedin', NULL, 'Cover letter: g'),
(2, 1, 'sasa', 'tablac', 'sasa@gamail.com', '09951808634', 'uploads/applications/resume_1758344029_68ce335dd5463.pdf', 'uploads/applications/cover_1758344029_68ce335dd5f89.pdf', 'hired', '2025-09-20 04:53:49', 'job_board', NULL, 'Cover letter: a'),
(3, 2, 'Mike', 'Johnson', 'mike.johnson@company.com', '5550103', 'uploads/applications/resume_1758419116_68cf58ac66ba6.pdf', 'uploads/applications/cover_1758419116_68cf58ac670d0.docx', 'hired', '2025-09-21 01:45:16', 'linkedin', NULL, 'Cover letter: good offer');

-- --------------------------------------------------------

--
-- Table structure for table `job_postings`
--

CREATE TABLE `job_postings` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `department_id` int(11) NOT NULL,
  `requirements` text DEFAULT NULL,
  `responsibilities` text DEFAULT NULL,
  `employment_type` enum('Full-time','Part-time','Contract','Intern','Temporary') NOT NULL,
  `location` varchar(100) NOT NULL,
  `salary_min` decimal(10,2) DEFAULT NULL,
  `salary_max` decimal(10,2) DEFAULT NULL,
  `benefits` text DEFAULT NULL,
  `status` enum('draft','published','paused','closed','cancelled') DEFAULT 'draft',
  `posted_date` date DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `hiring_manager_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `job_postings`
--

INSERT INTO `job_postings` (`id`, `title`, `description`, `department_id`, `requirements`, `responsibilities`, `employment_type`, `location`, `salary_min`, `salary_max`, `benefits`, `status`, `posted_date`, `closing_date`, `hiring_manager_id`, `created_at`, `updated_at`) VALUES
(1, 'staff', 'g', 1, 'g', 'g', 'Full-time', 'ph', 5000.00, 8000.00, 'g', 'published', '2025-09-19', '2025-11-20', 1, '2025-09-19 17:50:21', '2025-09-19 17:50:21'),
(2, 'Software Developer', 'We are looking for a skilled Software Developer to join our IT team. The ideal candidate will have experience in web development, database management, and software architecture. You will be responsible for developing, testing, and maintaining software applications that support our business operations.\r\n\r\nKey Responsibilities:\r\n• Design and develop web applications using modern technologies\r\n• Write clean, maintainable, and efficient code\r\n• Collaborate with cross-functional teams to define and implement new features\r\n• Debug and troubleshoot software issues\r\n• Participate in code reviews and technical discussions\r\n• Stay updated with latest industry trends and technologies\r\n\r\nWhat We Offer:\r\n• Competitive salary package in PHP\r\n• Health and dental insurance\r\n• Professional development opportunities\r\n• Flexible working arrangements\r\n• Modern office environment\r\n• Team building activities', 1, '• Bachelor\'s degree in Computer Science or related field\r\n• 2-3 years of experience in software development\r\n• Proficiency in PHP, JavaScript, HTML, CSS\r\n• Experience with MySQL or similar databases\r\n• Knowledge of version control systems (Git)\r\n• Strong problem-solving and analytical skills\r\n• Good communication and teamwork abilities\r\n• Experience with frameworks like Laravel or CodeIgniter is a plus', '• Develop and maintain web applications\r\n• Write and maintain clean, efficient code\r\n• Collaborate with team members on project development\r\n• Test and debug applications\r\n• Document code and technical specifications\r\n• Participate in agile development processes\r\n• Provide technical support when needed', 'Full-time', 'Makati City, Metro Manila', 35000.00, 50000.00, 'Health Insurance, Dental Coverage, 13th Month Pay, Performance Bonus, Learning Allowance', 'published', '2025-09-21', '2025-10-21', 1, '2025-09-21 01:40:12', '2025-09-21 07:24:48'),
(3, 'HR Specialist', 'Join our Human Resources team as an HR Specialist and help us build a great workplace culture. This role involves recruitment, employee relations, and HR administration. You will work closely with management and employees to ensure smooth HR operations.\r\n\r\nKey Responsibilities:\r\n• Manage recruitment process from job posting to onboarding\r\n• Conduct interviews and coordinate hiring activities\r\n• Handle employee relations and conflict resolution\r\n• Maintain employee records and HR documentation\r\n• Assist with performance management processes\r\n• Support HR policies and procedures implementation\r\n• Coordinate training and development programs\r\n\r\nWhat We Offer:\r\n• Competitive salary in PHP\r\n• Comprehensive benefits package\r\n• Career growth opportunities\r\n• Work-life balance\r\n• Professional development support\r\n• Collaborative team environment', 1, '• Bachelor\'s degree in Human Resources, Psychology, or related field\r\n• 1-2 years of HR experience\r\n• Knowledge of Philippine labor laws\r\n• Strong interpersonal and communication skills\r\n• Proficiency in MS Office applications\r\n• Experience with HRIS systems is preferred\r\n• Excellent organizational and time management skills\r\n• Ability to maintain confidentiality', '• Manage recruitment and selection process\r\n• Conduct employee orientation and onboarding\r\n• Handle employee relations matters\r\n• Maintain accurate employee records\r\n• Assist with payroll and benefits administration\r\n• Support performance management activities\r\n• Coordinate training programs\r\n• Ensure compliance with labor laws', '', 'Quezon City, Metro Manila', 28000.00, 40000.00, 'Health Insurance, Dental Coverage, 13th Month Pay, Performance Bonus, Transportation Allowance', 'published', '2025-09-21', '2025-10-16', 1, '2025-09-21 01:40:12', '2025-09-21 01:40:12'),
(4, 'Marketing Coordinator', 'We are seeking a creative and dynamic Marketing Coordinator to join our marketing team. This role involves developing marketing campaigns, managing social media presence, and coordinating promotional activities. You will work with various teams to create engaging content and drive brand awareness.\r\n\r\nKey Responsibilities:\r\n• Develop and execute marketing campaigns\r\n• Manage social media accounts and content creation\r\n• Coordinate with external vendors and agencies\r\n• Analyze marketing data and campaign performance\r\n• Create marketing materials and promotional content\r\n• Organize events and promotional activities\r\n• Support sales team with marketing materials\r\n• Monitor industry trends and competitor activities\r\n\r\nWhat We Offer:\r\n• Competitive salary package in PHP\r\n• Creative and dynamic work environment\r\n• Opportunities for professional growth\r\n• Flexible work arrangements\r\n• Health and wellness benefits\r\n• Team collaboration and innovation', 1, '• Bachelor\'s degree in Marketing, Communications, or related field\r\n• 1-2 years of marketing experience\r\n• Strong creative and analytical skills\r\n• Proficiency in social media platforms\r\n• Experience with digital marketing tools\r\n• Knowledge of graphic design software (Photoshop, Canva)\r\n• Excellent written and verbal communication skills\r\n• Ability to work in a fast-paced environment\r\n• Experience with content management systems', '• Plan and execute marketing campaigns\r\n• Create engaging content for various channels\r\n• Manage social media presence and engagement\r\n• Coordinate with design and content teams\r\n• Analyze campaign performance and ROI\r\n• Organize and manage marketing events\r\n• Support lead generation activities\r\n• Maintain brand consistency across all channels', '', 'Taguig City, Metro Manila', 25000.00, 38000.00, 'Health Insurance, Dental Coverage, 13th Month Pay, Performance Bonus, Communication Allowance', 'published', '2025-09-21', '2025-10-11', 1, '2025-09-21 01:40:12', '2025-09-21 01:40:12');

-- --------------------------------------------------------

--
-- Table structure for table `leave_balances`
--

CREATE TABLE `leave_balances` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `year` int(11) NOT NULL,
  `allocated_days` decimal(5,2) DEFAULT 0.00,
  `used_days` decimal(5,2) DEFAULT 0.00,
  `remaining_days` decimal(5,2) DEFAULT 0.00,
  `carry_forward_days` decimal(5,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_balances`
--

INSERT INTO `leave_balances` (`id`, `employee_id`, `leave_type_id`, `year`, `allocated_days`, `used_days`, `remaining_days`, `carry_forward_days`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 2025, 15.00, 2.00, 13.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(2, 1, 2, 2025, 15.00, 1.00, 14.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(3, 2, 1, 2025, 15.00, 5.00, 10.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(4, 2, 2, 2025, 15.00, 3.00, 12.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(5, 3, 1, 2025, 15.00, 0.00, 15.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(6, 3, 2, 2025, 15.00, 2.00, 13.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(7, 4, 1, 2025, 15.00, 8.00, 7.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(8, 4, 2, 2025, 15.00, 1.00, 14.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(9, 5, 1, 2025, 15.00, 3.00, 12.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(10, 5, 2, 2025, 15.00, 0.00, 15.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(11, 6, 1, 2025, 15.00, 1.00, 14.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(12, 6, 2, 2025, 15.00, 4.00, 11.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(13, 7, 1, 2025, 15.00, 0.00, 15.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(14, 7, 2, 2025, 15.00, 0.00, 15.00, 0.00, '2025-09-21 09:02:57', '2025-09-21 09:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `leave_type_id` int(11) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `total_days` decimal(5,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled','taken') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `emergency_contact` varchar(100) DEFAULT NULL,
  `emergency_phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_requests`
--

INSERT INTO `leave_requests` (`id`, `employee_id`, `leave_type_id`, `start_date`, `end_date`, `total_days`, `reason`, `status`, `requested_at`, `approved_by`, `approved_at`, `rejection_reason`, `emergency_contact`, `emergency_phone`, `created_at`, `updated_at`) VALUES
(1, 4, 1, '2025-09-25', '2025-09-27', 3.00, 'Family vacation', 'approved', '2025-09-21 09:02:58', 1, '2025-09-20 10:00:00', NULL, NULL, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 5, 2, '2025-09-22', '2025-09-22', 1.00, 'Medical checkup', 'approved', '2025-09-21 09:02:58', 1, '2025-09-21 09:00:00', NULL, NULL, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 6, 1, '2025-10-01', '2025-10-03', 3.00, 'Personal matters', 'pending', '2025-09-21 09:02:58', NULL, NULL, NULL, NULL, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `leave_types`
--

CREATE TABLE `leave_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `annual_quota` int(11) DEFAULT 0,
  `max_consecutive_days` int(11) DEFAULT NULL,
  `requires_approval` tinyint(1) DEFAULT 1,
  `requires_medical_certificate` tinyint(1) DEFAULT 0,
  `is_paid` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leave_types`
--

INSERT INTO `leave_types` (`id`, `name`, `code`, `description`, `annual_quota`, `max_consecutive_days`, `requires_approval`, `requires_medical_certificate`, `is_paid`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Vacation Leave', 'VL', 'Annual vacation leave for rest and recreation', 15, 5, 1, 0, 1, 1, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(2, 'Sick Leave', 'SL', 'Leave for illness and medical appointments', 15, 3, 1, 1, 1, 1, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(3, 'Emergency Leave', 'EL', 'Leave for family emergencies and urgent matters', 3, 1, 1, 0, 1, 1, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(4, 'Maternity Leave', 'ML', 'Leave for childbirth and recovery', 105, 105, 1, 1, 1, 1, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(5, 'Paternity Leave', 'PL', 'Leave for new fathers', 7, 7, 1, 0, 1, 1, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(6, 'Bereavement Leave', 'BL', 'Leave for death of immediate family members', 3, 3, 1, 0, 1, 1, '2025-09-21 09:02:57', '2025-09-21 09:02:57'),
(7, 'Personal Leave', 'PRL', 'Unpaid personal leave', 0, 30, 1, 0, 0, 1, '2025-09-21 09:02:57', '2025-09-21 09:02:57');

-- --------------------------------------------------------

--
-- Table structure for table `onboarding`
--

CREATE TABLE `onboarding` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `status` enum('pending','in_progress','completed','cancelled') DEFAULT 'pending',
  `start_date` date NOT NULL,
  `completion_date` date DEFAULT NULL,
  `buddy_id` int(11) DEFAULT NULL,
  `mentor_id` int(11) DEFAULT NULL,
  `checklist` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`checklist`)),
  `orientation_date` date DEFAULT NULL,
  `orientation_location` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `onboarding`
--

INSERT INTO `onboarding` (`id`, `employee_id`, `status`, `start_date`, `completion_date`, `buddy_id`, `mentor_id`, `checklist`, `orientation_date`, `orientation_location`, `notes`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 4, 'pending', '2025-09-19', NULL, NULL, NULL, '[{\"task\":\"Complete new hire paperwork\",\"description\":\"Fill out all required employment forms\",\"category\":\"documentation\",\"due_date\":\"2025-09-22\",\"status\":\"completed\",\"priority\":\"high\",\"completed_date\":\"2025-09-19 16:33:52\"},{\"task\":\"IT setup and access\",\"description\":\"Set up computer, email, and system access\",\"category\":\"access\",\"due_date\":\"2025-09-20\",\"status\":\"completed\",\"priority\":\"high\",\"completed_date\":\"2025-09-19 16:34:00\"},{\"task\":\"Office tour and introduction\",\"description\":\"Tour office facilities and meet team members\",\"category\":\"orientation\",\"due_date\":\"2025-09-19\",\"status\":\"completed\",\"priority\":\"medium\",\"completed_date\":\"2025-09-19 16:34:02\"},{\"task\":\"Complete safety training\",\"description\":\"Attend mandatory safety orientation\",\"category\":\"training\",\"due_date\":\"2025-09-24\",\"status\":\"completed\",\"priority\":\"high\",\"completed_date\":\"2025-09-19 16:34:04\"},{\"task\":\"Meet with direct manager\",\"description\":\"One-on-one meeting with immediate supervisor\",\"category\":\"orientation\",\"due_date\":\"2025-09-21\",\"status\":\"completed\",\"priority\":\"medium\",\"completed_date\":\"2025-09-19 16:34:06\"}]', '2025-09-19', 'MAIN', 'ADD', 1, '2025-09-19 14:33:38', '2025-09-19 14:34:06'),
(4, 6, 'completed', '2025-09-20', NULL, NULL, NULL, '[{\"task\":\"Complete paperwork\",\"description\":\"Fill out all required employment forms\",\"status\":\"completed\",\"category\":\"Administrative\",\"due_date\":\"2025-09-23\",\"priority\":\"high\"},{\"task\":\"IT setup\",\"description\":\"Configure computer, email, and system access\",\"status\":\"completed\",\"category\":\"Technical\",\"due_date\":\"2025-09-21\",\"priority\":\"high\"},{\"task\":\"Office tour\",\"description\":\"Familiarize with office layout and facilities\",\"status\":\"completed\",\"category\":\"Orientation\",\"due_date\":\"2025-09-20\",\"priority\":\"medium\"},{\"task\":\"Meet team\",\"description\":\"Introduction to team members and key contacts\",\"status\":\"completed\",\"category\":\"Social\",\"due_date\":\"2025-09-22\",\"priority\":\"medium\"},{\"task\":\"System access\",\"description\":\"Set up access to all required systems and tools\",\"status\":\"completed\",\"category\":\"Technical\",\"due_date\":\"2025-09-21\",\"priority\":\"high\"}]', NULL, NULL, 'Auto-created from recruitment application', 1, '2025-09-20 05:01:35', '2025-09-20 05:12:17'),
(5, 5, 'completed', '2025-09-20', NULL, 6, 2, '[{\"task\":\"Complete new hire paperwork\",\"description\":\"Fill out all required employment forms\",\"status\":\"completed\",\"category\":\"documentation\",\"due_date\":\"2025-09-23\",\"priority\":\"high\"},{\"task\":\"IT setup and access\",\"description\":\"Set up computer, email, and system access\",\"status\":\"completed\",\"category\":\"access\",\"due_date\":\"2025-09-21\",\"priority\":\"high\"},{\"task\":\"Office tour and introduction\",\"description\":\"Tour office facilities and meet team members\",\"status\":\"completed\",\"category\":\"orientation\",\"due_date\":\"2025-09-20\",\"priority\":\"medium\"},{\"task\":\"Complete safety training\",\"description\":\"Attend mandatory safety orientation\",\"status\":\"completed\",\"category\":\"training\",\"due_date\":\"2025-09-25\",\"priority\":\"high\"},{\"task\":\"Meet with direct manager\",\"description\":\"One-on-one meeting with immediate supervisor\",\"status\":\"completed\",\"category\":\"orientation\",\"due_date\":\"2025-09-22\",\"priority\":\"medium\"}]', '2025-09-27', 'MAIN', NULL, 1, '2025-09-20 05:05:42', '2025-09-20 05:11:43'),
(6, 7, 'in_progress', '2025-09-21', NULL, NULL, 2, '[{\"task\":\"Access Employee Self-Service Portal\",\"description\":\"Log in to the HR system using provided credentials and change temporary password\",\"status\":\"completed\",\"category\":\"System Access\",\"due_date\":\"2025-09-21\",\"priority\":\"high\"},{\"task\":\"Complete paperwork\",\"description\":\"Fill out all required employment forms\",\"status\":\"completed\",\"category\":\"Administrative\",\"due_date\":\"2025-09-24\",\"priority\":\"high\"},{\"task\":\"IT setup\",\"description\":\"Configure computer, email, and system access\",\"status\":\"completed\",\"category\":\"Technical\",\"due_date\":\"2025-09-22\",\"priority\":\"high\"},{\"task\":\"Office tour\",\"description\":\"Familiarize with office layout and facilities\",\"status\":\"completed\",\"category\":\"Orientation\",\"due_date\":\"2025-09-21\",\"priority\":\"medium\"},{\"task\":\"Meet team\",\"description\":\"Introduction to team members and key contacts\",\"status\":\"completed\",\"category\":\"Social\",\"due_date\":\"2025-09-23\",\"priority\":\"medium\"},{\"task\":\"System access\",\"description\":\"Set up access to all required systems and tools\",\"status\":\"completed\",\"category\":\"Technical\",\"due_date\":\"2025-09-22\",\"priority\":\"high\"},{\"task\":\"Explore Learning & Development\",\"description\":\"Browse available courses and training materials in the self-service portal\",\"status\":\"completed\",\"category\":\"Training\",\"due_date\":\"2025-09-28\",\"priority\":\"low\"}]', NULL, NULL, 'Auto-created from recruitment application', 1, '2025-09-21 01:46:55', '2025-09-21 06:49:17');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_items`
--

CREATE TABLE `payroll_items` (
  `id` int(11) NOT NULL,
  `payroll_run_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `item_type` enum('Earning','Deduction','Tax','Benefit') NOT NULL,
  `item_code` varchar(50) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `is_taxable` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payroll_runs`
--

CREATE TABLE `payroll_runs` (
  `id` int(11) NOT NULL,
  `pay_period_id` int(11) NOT NULL,
  `run_date` date NOT NULL,
  `status` enum('draft','calculated','approved','processed','cancelled') DEFAULT 'draft',
  `total_employees` int(11) DEFAULT 0,
  `total_gross` decimal(15,2) DEFAULT 0.00,
  `total_deductions` decimal(15,2) DEFAULT 0.00,
  `total_net` decimal(15,2) DEFAULT 0.00,
  `processed_by` int(11) DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_runs`
--

INSERT INTO `payroll_runs` (`id`, `pay_period_id`, `run_date`, `status`, `total_employees`, `total_gross`, `total_deductions`, `total_net`, `processed_by`, `processed_at`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-09-16', 'processed', 7, 350000.00, 70000.00, 280000.00, 1, '2025-09-16 10:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 2, '2025-09-30', 'calculated', 7, 350000.00, 70000.00, 280000.00, NULL, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `payroll_summaries`
--

CREATE TABLE `payroll_summaries` (
  `id` int(11) NOT NULL,
  `payroll_run_id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `basic_salary` decimal(10,2) DEFAULT 0.00,
  `overtime_pay` decimal(10,2) DEFAULT 0.00,
  `allowances` decimal(10,2) DEFAULT 0.00,
  `gross_pay` decimal(10,2) DEFAULT 0.00,
  `tax_deduction` decimal(10,2) DEFAULT 0.00,
  `sss_deduction` decimal(10,2) DEFAULT 0.00,
  `philhealth_deduction` decimal(10,2) DEFAULT 0.00,
  `pagibig_deduction` decimal(10,2) DEFAULT 0.00,
  `other_deductions` decimal(10,2) DEFAULT 0.00,
  `total_deductions` decimal(10,2) DEFAULT 0.00,
  `net_pay` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payroll_summaries`
--

INSERT INTO `payroll_summaries` (`id`, `payroll_run_id`, `employee_id`, `basic_salary`, `overtime_pay`, `allowances`, `gross_pay`, `tax_deduction`, `sss_deduction`, `philhealth_deduction`, `pagibig_deduction`, `other_deductions`, `total_deductions`, `net_pay`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 80000.00, 0.00, 5000.00, 85000.00, 17000.00, 3200.00, 1200.00, 1000.00, 0.00, 22400.00, 62600.00, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 1, 2, 70000.00, 0.00, 3000.00, 73000.00, 14600.00, 2800.00, 1100.00, 900.00, 0.00, 19400.00, 53600.00, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 1, 3, 65000.00, 0.00, 2000.00, 67000.00, 13400.00, 2600.00, 1000.00, 800.00, 0.00, 17800.00, 49200.00, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(4, 1, 4, 55000.00, 0.00, 1000.00, 56000.00, 11200.00, 2200.00, 800.00, 700.00, 0.00, 14900.00, 41100.00, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(5, 1, 5, 6500.00, 0.00, 0.00, 6500.00, 0.00, 500.00, 200.00, 100.00, 0.00, 800.00, 5700.00, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(6, 1, 6, 6500.00, 0.00, 0.00, 6500.00, 0.00, 500.00, 200.00, 100.00, 0.00, 800.00, 5700.00, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(7, 1, 7, 42500.00, 0.00, 0.00, 42500.00, 8500.00, 1700.00, 600.00, 500.00, 0.00, 11300.00, 31200.00, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `pay_periods`
--

CREATE TABLE `pay_periods` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `pay_date` date NOT NULL,
  `status` enum('open','closed','processed') DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pay_periods`
--

INSERT INTO `pay_periods` (`id`, `name`, `start_date`, `end_date`, `pay_date`, `status`, `created_at`, `updated_at`) VALUES
(1, 'September 1-15, 2025', '2025-09-01', '2025-09-15', '2025-09-16', 'processed', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 'September 16-30, 2025', '2025-09-16', '2025-09-30', '2025-10-01', 'open', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 'October 1-15, 2025', '2025-10-01', '2025-10-15', '2025-10-16', 'open', '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `performance_reviews`
--

CREATE TABLE `performance_reviews` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `period_year` int(11) NOT NULL,
  `period_quarter` int(11) DEFAULT NULL,
  `status` enum('draft','in_progress','under_review','completed','cancelled') DEFAULT 'draft',
  `goals` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`goals`)),
  `competencies` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`competencies`)),
  `overall_rating` decimal(3,2) DEFAULT NULL,
  `self_assessment` text DEFAULT NULL,
  `manager_review` text DEFAULT NULL,
  `hr_review` text DEFAULT NULL,
  `development_plan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`development_plan`)),
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance_reviews`
--

INSERT INTO `performance_reviews` (`id`, `employee_id`, `period_year`, `period_quarter`, `status`, `goals`, `competencies`, `overall_rating`, `self_assessment`, `manager_review`, `hr_review`, `development_plan`, `created_by`, `created_at`, `updated_at`) VALUES
(4, 6, 2025, NULL, 'completed', '[{\"title\":\"Increase Customer Satisfaction\",\"priority\":\"high\",\"description\":\"ieve customer satisfaction rating of 4.5\\/5.0 by implementing new feedback collection system and improving response times to customer inquiries. Target completion: Q4 2025.\"}]', '[{\"name\":\"Team Leadership\",\"level\":\"expert\",\"rating\":\"5\"}]', 5.00, 'I am pleased to share my performance highlights for the 2025 review period. I successfully exceeded my customer satisfaction target by achieving a 4.6/5.0 rating, which was above our goal of 4.5/5.0. This improvement was largely due to implementing the new customer feedback system I proposed and led.\r\n\r\nKey Achievements:\r\n- Increased customer satisfaction scores from 3.8 to 4.6 (21% improvement)\r\n- Led the implementation of automated feedback collection system\r\n- Reduced average response time to customer inquiries from 4 hours to 2.5 hours\r\n- Successfully mentored two junior team members - both received promotions\r\n- Completed Leadership Development Program with 95% average score\r\n- Delivered 12 successful client presentations with 100% positive feedback\r\n\r\nAreas of Growth:\r\n- Improved my data analysis skills by completing advanced Excel training\r\n- Enhanced my conflict resolution abilities through challenging team situations\r\n- Developed better time management techniques using new project management tools\r\n- Gained confidence in public speaking through regular practice sessions\r\n\r\nAreas for Continued Development:\r\n- I want to improve my strategic thinking and long-term planning abilities\r\n- Need to enhance my technical skills in data visualization and reporting\r\n- Would like to develop more confidence when presenting to senior executives\r\n- Plan to learn more about cross-departmental collaboration and process optimization\r\n\r\nI am committed to continuing my professional growth and contributing to our team\'s success. I appreciate the support and constructive feedback I\'ve received throughout this year, and I look forward to taking on new challenges in the coming year.', 'Strengths Observed:\r\n- Outstanding customer service skills with genuine commitment to client success\r\n- Strong leadership abilities evidenced by successful mentoring of junior staff\r\n- Excellent problem-solving skills and proactive approach to process improvement\r\n- Reliable and dependable team member who consistently delivers quality work\r\n- Positive attitude and communication skills that enhance team morale\r\n- Strong analytical thinking and attention to detail\r\n\r\nPerformance Highlights:\r\n- Exceeded customer satisfaction target by 0.1 points (4.6 vs 4.5 target)\r\n- Successfully completed all development goals ahead of schedule\r\n- Mentored two team members who both received promotions within 6 months\r\n- Led cross-functional project that improved team efficiency by 25%\r\n- Reduced customer response time by 37.5% through process optimization\r\n- Maintained 100% on-time delivery rate for all assigned projects\r\n\r\nAreas for Continued Development:\r\n- Continue building strategic thinking skills through exposure to higher-level projects\r\n- Develop more confidence in presenting to senior management audiences\r\n- Consider taking on more complex data analysis projects to build technical expertise\r\n- Explore opportunities to lead larger, more complex initiatives\r\n\r\nRecommendations:\r\n- Sarah is ready for increased responsibilities and more complex projects\r\n- Strong candidate for the next available promotion opportunity\r\n- Continue providing opportunities for leadership development\r\n- Excellent candidate for succession planning and high-potential programs\r\n- Consider her for special projects and cross-departmental initiatives\r\n\r\nOverall Performance Rating: Exceeds Expectations (4.2/5.0)', '- All performance goals were met or exceeded with measurable results\r\n- Competency assessments show consistent growth across all evaluated areas\r\n- 360-degree feedback from colleagues and stakeholders is overwhelmingly positive\r\n- No performance issues, concerns, or areas of improvement identified\r\n- Strong alignment with company values and culture\r\n', '[{\"action\":\"Complete Leadership Certification\",\"target_date\":\"2025-09-20\"}]', 1, '2025-09-20 06:38:28', '2025-09-20 16:02:01'),
(5, 5, 2025, NULL, 'completed', '[{\"title\":\"Increase Customer Satisfaction\",\"priority\":\"high\",\"description\":\"g\"}]', '[{\"name\":\"Team Leadership\",\"level\":\"intermediate\",\"rating\":\"5\"}]', 5.00, 'g', 'g', 'g', '[{\"action\":\"Complete Leadership Certification\",\"target_date\":\"2025-09-21\"}]', 1, '2025-09-21 06:46:36', '2025-09-21 06:47:50');

-- --------------------------------------------------------

--
-- Table structure for table `recognition`
--

CREATE TABLE `recognition` (
  `id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `giver_id` int(11) NOT NULL,
  `type` enum('peer_recognition','manager_recognition','achievement','milestone','innovation','teamwork','leadership','customer_service') NOT NULL,
  `category` enum('excellence','innovation','collaboration','leadership','customer_focus','safety','efficiency','mentoring') NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text NOT NULL,
  `points` int(11) DEFAULT 10,
  `status` enum('pending','approved','rejected','cancelled') DEFAULT 'pending',
  `visibility` enum('private','team','department','organization') DEFAULT 'team',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recognition`
--

INSERT INTO `recognition` (`id`, `recipient_id`, `giver_id`, `type`, `category`, `title`, `description`, `points`, `status`, `visibility`, `created_at`, `updated_at`) VALUES
(3, 6, 1, 'teamwork', 'excellence', 'OUTSTANDING', 'GOOD WORK', 10, 'approved', 'team', '2025-09-20 15:58:09', '2025-09-20 15:58:36'),
(4, 6, 1, 'leadership', 'excellence', 'LEADERSHIP', 'TEAM WORK', 10, 'approved', 'team', '2025-09-20 18:49:05', '2025-09-20 18:49:05'),
(5, 5, 1, 'teamwork', 'leadership', 'LEADERSHIP', 'TEAM WORK', 10, 'approved', 'team', '2025-09-20 18:50:35', '2025-09-20 18:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `salary_adjustments`
--

CREATE TABLE `salary_adjustments` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `adjustment_type` enum('Increase','Decrease','Promotion','Demotion','Market Adjustment') NOT NULL,
  `old_salary` decimal(10,2) DEFAULT NULL,
  `new_salary` decimal(10,2) NOT NULL,
  `adjustment_amount` decimal(10,2) NOT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `effective_date` date NOT NULL,
  `reason` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `salary_components`
--

CREATE TABLE `salary_components` (
  `id` int(11) NOT NULL,
  `structure_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` enum('Basic','Allowance','Bonus','Deduction','Benefit') NOT NULL,
  `is_taxable` tinyint(1) DEFAULT 1,
  `is_mandatory` tinyint(1) DEFAULT 0,
  `calculation_type` enum('Fixed','Percentage','Formula') DEFAULT 'Fixed',
  `calculation_value` decimal(10,2) DEFAULT NULL,
  `formula` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salary_components`
--

INSERT INTO `salary_components` (`id`, `structure_id`, `name`, `type`, `is_taxable`, `is_mandatory`, `calculation_type`, `calculation_value`, `formula`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Basic Salary', 'Basic', 1, 1, 'Fixed', NULL, NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 1, 'Transportation Allowance', 'Allowance', 1, 0, 'Fixed', 2000.00, NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 1, 'Meal Allowance', 'Allowance', 1, 0, 'Fixed', 1500.00, NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(4, 1, 'Communication Allowance', 'Allowance', 1, 0, 'Fixed', 1000.00, NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(5, 1, 'SSS Contribution', 'Deduction', 0, 1, 'Percentage', 11.00, NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(6, 1, 'PhilHealth Contribution', 'Deduction', 0, 1, 'Percentage', 3.00, NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(7, 1, 'Pag-IBIG Contribution', 'Deduction', 0, 1, 'Fixed', 100.00, NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(8, 2, 'Basic Salary', 'Basic', 1, 1, 'Fixed', NULL, NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(9, 2, 'Executive Allowance', 'Allowance', 1, 0, 'Fixed', 10000.00, NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(10, 2, 'Performance Bonus', 'Bonus', 1, 0, 'Percentage', 20.00, NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `salary_structures`
--

CREATE TABLE `salary_structures` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `effective_date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `salary_structures`
--

INSERT INTO `salary_structures` (`id`, `name`, `description`, `is_active`, `effective_date`, `created_at`, `updated_at`) VALUES
(1, 'Standard Salary Structure', 'Standard salary structure for all employees', 1, '2025-01-01', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 'Executive Salary Structure', 'Enhanced salary structure for executive positions', 1, '2025-01-01', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 'Contract Salary Structure', 'Salary structure for contract employees', 1, '2025-01-01', '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `break_duration` int(11) DEFAULT 0,
  `is_overnight` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shifts`
--

INSERT INTO `shifts` (`id`, `name`, `code`, `start_time`, `end_time`, `break_duration`, `is_overnight`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Regular Day Shift', 'DAY', '08:00:00', '17:00:00', 60, 0, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 'Regular Night Shift', 'NIGHT', '22:00:00', '06:00:00', 60, 1, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 'Morning Shift', 'MORNING', '06:00:00', '14:00:00', 30, 0, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(4, 'Afternoon Shift', 'AFTERNOON', '14:00:00', '22:00:00', 30, 0, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(5, 'Flexible Hours', 'FLEX', '09:00:00', '18:00:00', 60, 0, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `shift_assignments`
--

CREATE TABLE `shift_assignments` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `shift_id` int(11) NOT NULL,
  `effective_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shift_assignments`
--

INSERT INTO `shift_assignments` (`id`, `employee_id`, `shift_id`, `effective_date`, `end_date`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 2, 1, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 3, 1, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(4, 4, 5, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(5, 5, 1, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(6, 6, 1, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(7, 7, 1, '2025-01-01', NULL, 1, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `succession_candidates`
--

CREATE TABLE `succession_candidates` (
  `id` int(11) NOT NULL,
  `succession_plan_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `status` enum('potential','shortlisted','in_development','ready','not_suitable') DEFAULT 'potential',
  `readiness_level` enum('low','medium','high','ready') DEFAULT 'low',
  `development_notes` text DEFAULT NULL,
  `assessment_score` decimal(3,1) DEFAULT NULL,
  `added_by` int(11) NOT NULL,
  `added_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `succession_candidates`
--

INSERT INTO `succession_candidates` (`id`, `succession_plan_id`, `candidate_id`, `status`, `readiness_level`, `development_notes`, `assessment_score`, `added_by`, `added_at`, `updated_at`) VALUES
(2, 1, 6, 'ready', 'ready', 'good', 5.0, 1, '2025-09-20 09:08:48', '2025-09-20 18:22:12');

-- --------------------------------------------------------

--
-- Table structure for table `succession_plans`
--

CREATE TABLE `succession_plans` (
  `id` int(11) NOT NULL,
  `position_title` varchar(100) NOT NULL,
  `department_id` int(11) NOT NULL,
  `current_incumbent_id` int(11) DEFAULT NULL,
  `status` enum('draft','active','paused','completed','cancelled') DEFAULT 'draft',
  `criticality` enum('low','medium','high','critical') DEFAULT 'medium',
  `requirements` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`requirements`)),
  `succession_timeline` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`succession_timeline`)),
  `risk_assessment` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`risk_assessment`)),
  `development_plans` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`development_plans`)),
  `created_by` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `succession_plans`
--

INSERT INTO `succession_plans` (`id`, `position_title`, `department_id`, `current_incumbent_id`, `status`, `criticality`, `requirements`, `succession_timeline`, `risk_assessment`, `development_plans`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'Manager', 1, 6, 'active', 'high', '[{\"requirement\":\" Bachelor\'s degree\",\"priority\":\"essential\",\"description\":\"\"}]', '[{\"phase\":\"Preparation\",\"duration\":\"6 months\",\"description\":\"Identify and develop internal candidates through assessment and training\",\"milestones\":[\"andidate identification\",\"  Skills assessment\",\"  Development plan creation\",\"  Training program enrollment\"]}]', '[{\"risk\":\"Key person dependency on current incumbent\",\"impact\":\"high\",\"probability\":\"medium\",\"mitigation\":\"Cross-train team members, document processes, create knowledge base\"}]', '[{\"plan\":\"    header(\'Location: ..\\/..\\/auth\\/login.php\');\",\"timeline\":\" 12 months\",\"resources\":\"External leadership coach, internal mentoring, leadership courses\",\"success_metrics\":\"360-degree feedback improvement, team engagement scores, leadership competency assessment\"}]', 1, '2025-09-20 08:25:38', '2025-09-20 09:11:15');

-- --------------------------------------------------------

--
-- Table structure for table `timesheets`
--

CREATE TABLE `timesheets` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `week_start_date` date NOT NULL,
  `week_end_date` date NOT NULL,
  `total_hours` decimal(5,2) DEFAULT 0.00,
  `regular_hours` decimal(5,2) DEFAULT 0.00,
  `overtime_hours` decimal(5,2) DEFAULT 0.00,
  `status` enum('draft','submitted','approved','rejected') DEFAULT 'draft',
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timesheets`
--

INSERT INTO `timesheets` (`id`, `employee_id`, `week_start_date`, `week_end_date`, `total_hours`, `regular_hours`, `overtime_hours`, `status`, `submitted_at`, `approved_by`, `approved_at`, `created_at`, `updated_at`) VALUES
(1, 1, '2025-09-16', '2025-09-22', 40.00, 40.00, 0.00, 'approved', '2025-09-20 17:00:00', 1, '2025-09-21 09:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(2, 2, '2025-09-16', '2025-09-22', 40.00, 40.00, 0.00, 'approved', '2025-09-20 17:00:00', 1, '2025-09-21 09:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(3, 3, '2025-09-16', '2025-09-22', 40.00, 40.00, 0.00, 'approved', '2025-09-20 17:00:00', 1, '2025-09-21 09:00:00', '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(4, 4, '2025-09-16', '2025-09-22', 40.00, 40.00, 0.00, 'submitted', '2025-09-20 17:00:00', NULL, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58'),
(5, 5, '2025-09-16', '2025-09-22', 40.00, 40.00, 0.00, 'draft', NULL, NULL, NULL, '2025-09-21 09:02:58', '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `timesheet_entries`
--

CREATE TABLE `timesheet_entries` (
  `id` int(11) NOT NULL,
  `timesheet_id` int(11) NOT NULL,
  `work_date` date NOT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `break_duration` int(11) DEFAULT 0,
  `total_hours` decimal(5,2) DEFAULT 0.00,
  `overtime_hours` decimal(5,2) DEFAULT 0.00,
  `project_code` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `time_logs`
--

CREATE TABLE `time_logs` (
  `id` int(11) NOT NULL,
  `employee_id` int(11) NOT NULL,
  `log_date` date NOT NULL,
  `log_time` datetime NOT NULL,
  `type` enum('IN','OUT','BREAK_START','BREAK_END','OVERTIME_START','OVERTIME_END') NOT NULL,
  `location` varchar(100) DEFAULT NULL,
  `device_id` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `time_logs`
--

INSERT INTO `time_logs` (`id`, `employee_id`, `log_date`, `log_time`, `type`, `location`, `device_id`, `ip_address`, `notes`, `created_at`) VALUES
(1, 1, '2025-09-21', '2025-09-21 08:00:00', 'IN', 'Main Office', 'DEV001', NULL, NULL, '2025-09-21 09:02:58'),
(2, 1, '2025-09-21', '2025-09-21 12:00:00', 'BREAK_START', 'Main Office', 'DEV001', NULL, NULL, '2025-09-21 09:02:58'),
(3, 1, '2025-09-21', '2025-09-21 13:00:00', 'BREAK_END', 'Main Office', 'DEV001', NULL, NULL, '2025-09-21 09:02:58'),
(4, 1, '2025-09-21', '2025-09-21 17:00:00', 'OUT', 'Main Office', 'DEV001', NULL, NULL, '2025-09-21 09:02:58'),
(5, 2, '2025-09-21', '2025-09-21 08:05:00', 'IN', 'Main Office', 'DEV002', NULL, NULL, '2025-09-21 09:02:58'),
(6, 2, '2025-09-21', '2025-09-21 12:00:00', 'BREAK_START', 'Main Office', 'DEV002', NULL, NULL, '2025-09-21 09:02:58'),
(7, 2, '2025-09-21', '2025-09-21 13:00:00', 'BREAK_END', 'Main Office', 'DEV002', NULL, NULL, '2025-09-21 09:02:58'),
(8, 2, '2025-09-21', '2025-09-21 17:05:00', 'OUT', 'Main Office', 'DEV002', NULL, NULL, '2025-09-21 09:02:58');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `manager_id` int(11) DEFAULT NULL,
  `employment_type` enum('Full-time','Part-time','Contract','Intern') DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `salary` decimal(10,2) DEFAULT NULL,
  `status` enum('active','inactive','terminated') DEFAULT 'active',
  `role` enum('admin','hr_manager','manager','employee') DEFAULT 'employee',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `employee_id`, `email`, `password`, `first_name`, `last_name`, `middle_name`, `phone`, `address`, `date_of_birth`, `gender`, `position`, `department_id`, `manager_id`, `employment_type`, `start_date`, `salary`, `status`, `role`, `created_at`, `updated_at`) VALUES
(1, 'EMP001', 'admin@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin', 'User', NULL, NULL, NULL, NULL, NULL, 'System Administrator', 1, NULL, 'Full-time', '2023-01-01', 80000.00, 'active', 'admin', '2025-09-19 14:29:34', '2025-09-21 10:18:10'),
(2, 'EMP002', 'hr.manager@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'HR', 'Manager', NULL, NULL, NULL, NULL, NULL, 'HR Manager', 1, NULL, 'Full-time', '2023-01-01', 70000.00, 'active', 'hr_manager', '2025-09-19 14:29:34', '2025-09-19 14:29:34'),
(3, 'EMP003', 'manager@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Manager', NULL, NULL, NULL, NULL, NULL, 'Department Manager', 2, NULL, 'Full-time', '2023-01-01', 65000.00, 'active', 'manager', '2025-09-19 14:29:34', '2025-09-19 14:29:34'),
(4, 'EMP004', 'employee@company.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'JaneY', 'FER', 'G', '5550103', '789 Pine St', NULL, NULL, 'Software Developer', 2, NULL, 'Full-time', '2023-01-01', 55000.00, 'active', 'employee', '2025-09-19 14:29:34', '2025-09-20 16:47:47'),
(5, 'EMP20250001', 'sasa@gamail.com', '$2y$10$M8xXd4WCVznwIdGuJhuvoOqGp1cKzg70g1cwkCu5nAdPYuMl0PHhO', 'sasa', 'tablac', NULL, '09951808634', 'qc\r\nqweqwe', NULL, NULL, 'staff', 1, NULL, 'Full-time', '2025-09-20', 6500.00, 'active', 'employee', '2025-09-20 04:55:05', '2025-09-20 17:12:37'),
(6, 'EMP20250002', 'tablacgilbert08@gmail.com', '$2y$10$cW0FlpxPDLtDMNFYQ5eiVenID7sOF1AQW6vYjEHK2T2Phv87wrLw.', 'gilbert', 'tablac', 'A', '09951808634', '#63 insurance st. Brgy. Sangandaan proj.\r\nqweqwe', NULL, NULL, 'staff', 1, NULL, 'Full-time', '2025-09-20', 6500.00, 'active', 'employee', '2025-09-20 05:01:35', '2025-09-21 10:18:25'),
(7, 'EMP20250003', 'mike.johnson@company.com', '$2y$10$D3UWeg41.dGCvFNU7FSHp.n20IKKUPcAdYpYO.ZMxOVX8zIrrNjA6', 'Mike', 'Johnson', NULL, '5550103', NULL, NULL, NULL, 'Software Developer', 1, NULL, '', '2025-09-21', 42500.00, 'active', 'employee', '2025-09-21 01:46:55', '2025-09-21 01:46:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `competencies`
--
ALTER TABLE `competencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `competency_assessments`
--
ALTER TABLE `competency_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `competency_id` (`competency_id`),
  ADD KEY `assessor_id` (`assessor_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_course` (`user_id`,`course_id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `fk_enrolled_by` (`enrolled_by`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `head_id` (`head_id`);

--
-- Indexes for table `employee_benefits`
--
ALTER TABLE `employee_benefits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `uploaded_by` (`uploaded_by`);

--
-- Indexes for table `employee_emergency_contacts`
--
ALTER TABLE `employee_emergency_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `structure_id` (`structure_id`);

--
-- Indexes for table `employee_skills`
--
ALTER TABLE `employee_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `verified_by` (`verified_by`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `expense_claims`
--
ALTER TABLE `expense_claims`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `claim_number` (`claim_number`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `expense_items`
--
ALTER TABLE `expense_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `claim_id` (`claim_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `hr_metrics`
--
ALTER TABLE `hr_metrics`
  ADD PRIMARY KEY (`id`),
  ADD KEY `metric_type` (`metric_type`),
  ADD KEY `measurement_date` (`measurement_date`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `hr_reports`
--
ALTER TABLE `hr_reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_posting_id` (`job_posting_id`),
  ADD KEY `referral_id` (`referral_id`);

--
-- Indexes for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `hiring_manager_id` (`hiring_manager_id`);

--
-- Indexes for table `leave_balances`
--
ALTER TABLE `leave_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_leave_year` (`employee_id`,`leave_type_id`,`year`),
  ADD KEY `leave_type_id` (`leave_type_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `leave_type_id` (`leave_type_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `leave_types`
--
ALTER TABLE `leave_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `onboarding`
--
ALTER TABLE `onboarding`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `buddy_id` (`buddy_id`),
  ADD KEY `mentor_id` (`mentor_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `payroll_items`
--
ALTER TABLE `payroll_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payroll_run_id` (`payroll_run_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pay_period_id` (`pay_period_id`),
  ADD KEY `processed_by` (`processed_by`);

--
-- Indexes for table `payroll_summaries`
--
ALTER TABLE `payroll_summaries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_payroll` (`payroll_run_id`,`employee_id`),
  ADD KEY `employee_id` (`employee_id`);

--
-- Indexes for table `pay_periods`
--
ALTER TABLE `pay_periods`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `performance_reviews`
--
ALTER TABLE `performance_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `recognition`
--
ALTER TABLE `recognition`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipient_id` (`recipient_id`),
  ADD KEY `giver_id` (`giver_id`);

--
-- Indexes for table `salary_adjustments`
--
ALTER TABLE `salary_adjustments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `salary_components`
--
ALTER TABLE `salary_components`
  ADD PRIMARY KEY (`id`),
  ADD KEY `structure_id` (`structure_id`);

--
-- Indexes for table `salary_structures`
--
ALTER TABLE `salary_structures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `shift_assignments`
--
ALTER TABLE `shift_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `shift_id` (`shift_id`);

--
-- Indexes for table `succession_candidates`
--
ALTER TABLE `succession_candidates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_candidate_plan` (`succession_plan_id`,`candidate_id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `added_by` (`added_by`);

--
-- Indexes for table `succession_plans`
--
ALTER TABLE `succession_plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `current_incumbent_id` (`current_incumbent_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Indexes for table `timesheets`
--
ALTER TABLE `timesheets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_week` (`employee_id`,`week_start_date`),
  ADD KEY `approved_by` (`approved_by`);

--
-- Indexes for table `timesheet_entries`
--
ALTER TABLE `timesheet_entries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `timesheet_id` (`timesheet_id`);

--
-- Indexes for table `time_logs`
--
ALTER TABLE `time_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `employee_id` (`employee_id`),
  ADD KEY `log_date` (`log_date`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_id` (`employee_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `department_id` (`department_id`),
  ADD KEY `manager_id` (`manager_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `competencies`
--
ALTER TABLE `competencies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `competency_assessments`
--
ALTER TABLE `competency_assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `employee_benefits`
--
ALTER TABLE `employee_benefits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_emergency_contacts`
--
ALTER TABLE `employee_emergency_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `employee_skills`
--
ALTER TABLE `employee_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `expense_claims`
--
ALTER TABLE `expense_claims`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expense_items`
--
ALTER TABLE `expense_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hr_metrics`
--
ALTER TABLE `hr_metrics`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `hr_reports`
--
ALTER TABLE `hr_reports`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job_applications`
--
ALTER TABLE `job_applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `job_postings`
--
ALTER TABLE `job_postings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `leave_balances`
--
ALTER TABLE `leave_balances`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `leave_types`
--
ALTER TABLE `leave_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `onboarding`
--
ALTER TABLE `onboarding`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `payroll_items`
--
ALTER TABLE `payroll_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `payroll_summaries`
--
ALTER TABLE `payroll_summaries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pay_periods`
--
ALTER TABLE `pay_periods`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `performance_reviews`
--
ALTER TABLE `performance_reviews`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `recognition`
--
ALTER TABLE `recognition`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `salary_adjustments`
--
ALTER TABLE `salary_adjustments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `salary_components`
--
ALTER TABLE `salary_components`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `salary_structures`
--
ALTER TABLE `salary_structures`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shift_assignments`
--
ALTER TABLE `shift_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `succession_candidates`
--
ALTER TABLE `succession_candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `succession_plans`
--
ALTER TABLE `succession_plans`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `timesheets`
--
ALTER TABLE `timesheets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `timesheet_entries`
--
ALTER TABLE `timesheet_entries`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `time_logs`
--
ALTER TABLE `time_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `competency_assessments`
--
ALTER TABLE `competency_assessments`
  ADD CONSTRAINT `competency_assessments_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `competency_assessments_ibfk_2` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`),
  ADD CONSTRAINT `competency_assessments_ibfk_3` FOREIGN KEY (`assessor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `course_enrollments`
--
ALTER TABLE `course_enrollments`
  ADD CONSTRAINT `course_enrollments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `course_enrollments_ibfk_2` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`),
  ADD CONSTRAINT `fk_enrolled_by` FOREIGN KEY (`enrolled_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `departments`
--
ALTER TABLE `departments`
  ADD CONSTRAINT `departments_ibfk_1` FOREIGN KEY (`head_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `employee_benefits`
--
ALTER TABLE `employee_benefits`
  ADD CONSTRAINT `employee_benefits_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD CONSTRAINT `employee_documents_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_documents_ibfk_2` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_emergency_contacts`
--
ALTER TABLE `employee_emergency_contacts`
  ADD CONSTRAINT `employee_emergency_contacts_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_salaries`
--
ALTER TABLE `employee_salaries`
  ADD CONSTRAINT `employee_salaries_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_salaries_ibfk_2` FOREIGN KEY (`structure_id`) REFERENCES `salary_structures` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_skills`
--
ALTER TABLE `employee_skills`
  ADD CONSTRAINT `employee_skills_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `employee_skills_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expense_claims`
--
ALTER TABLE `expense_claims`
  ADD CONSTRAINT `expense_claims_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expense_claims_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `expense_items`
--
ALTER TABLE `expense_items`
  ADD CONSTRAINT `expense_items_ibfk_1` FOREIGN KEY (`claim_id`) REFERENCES `expense_claims` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expense_items_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `expense_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_metrics`
--
ALTER TABLE `hr_metrics`
  ADD CONSTRAINT `hr_metrics_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_reports`
--
ALTER TABLE `hr_reports`
  ADD CONSTRAINT `hr_reports_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `job_applications`
--
ALTER TABLE `job_applications`
  ADD CONSTRAINT `job_applications_ibfk_1` FOREIGN KEY (`job_posting_id`) REFERENCES `job_postings` (`id`),
  ADD CONSTRAINT `job_applications_ibfk_2` FOREIGN KEY (`referral_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `job_postings`
--
ALTER TABLE `job_postings`
  ADD CONSTRAINT `job_postings_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `job_postings_ibfk_2` FOREIGN KEY (`hiring_manager_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `leave_balances`
--
ALTER TABLE `leave_balances`
  ADD CONSTRAINT `leave_balances_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_balances_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_requests_ibfk_2` FOREIGN KEY (`leave_type_id`) REFERENCES `leave_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_requests_ibfk_3` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `onboarding`
--
ALTER TABLE `onboarding`
  ADD CONSTRAINT `onboarding_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `onboarding_ibfk_2` FOREIGN KEY (`buddy_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `onboarding_ibfk_3` FOREIGN KEY (`mentor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `onboarding_ibfk_4` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `payroll_items`
--
ALTER TABLE `payroll_items`
  ADD CONSTRAINT `payroll_items_ibfk_1` FOREIGN KEY (`payroll_run_id`) REFERENCES `payroll_runs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payroll_items_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payroll_runs`
--
ALTER TABLE `payroll_runs`
  ADD CONSTRAINT `payroll_runs_ibfk_1` FOREIGN KEY (`pay_period_id`) REFERENCES `pay_periods` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payroll_runs_ibfk_2` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `payroll_summaries`
--
ALTER TABLE `payroll_summaries`
  ADD CONSTRAINT `payroll_summaries_ibfk_1` FOREIGN KEY (`payroll_run_id`) REFERENCES `payroll_runs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payroll_summaries_ibfk_2` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `performance_reviews`
--
ALTER TABLE `performance_reviews`
  ADD CONSTRAINT `performance_reviews_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `performance_reviews_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `recognition`
--
ALTER TABLE `recognition`
  ADD CONSTRAINT `recognition_ibfk_1` FOREIGN KEY (`recipient_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `recognition_ibfk_2` FOREIGN KEY (`giver_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `salary_adjustments`
--
ALTER TABLE `salary_adjustments`
  ADD CONSTRAINT `salary_adjustments_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `salary_adjustments_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `salary_components`
--
ALTER TABLE `salary_components`
  ADD CONSTRAINT `salary_components_ibfk_1` FOREIGN KEY (`structure_id`) REFERENCES `salary_structures` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `shift_assignments`
--
ALTER TABLE `shift_assignments`
  ADD CONSTRAINT `shift_assignments_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shift_assignments_ibfk_2` FOREIGN KEY (`shift_id`) REFERENCES `shifts` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `succession_candidates`
--
ALTER TABLE `succession_candidates`
  ADD CONSTRAINT `succession_candidates_ibfk_1` FOREIGN KEY (`succession_plan_id`) REFERENCES `succession_plans` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `succession_candidates_ibfk_2` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `succession_candidates_ibfk_3` FOREIGN KEY (`added_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `succession_plans`
--
ALTER TABLE `succession_plans`
  ADD CONSTRAINT `succession_plans_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `succession_plans_ibfk_2` FOREIGN KEY (`current_incumbent_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `succession_plans_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `timesheets`
--
ALTER TABLE `timesheets`
  ADD CONSTRAINT `timesheets_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `timesheets_ibfk_2` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `timesheet_entries`
--
ALTER TABLE `timesheet_entries`
  ADD CONSTRAINT `timesheet_entries_ibfk_1` FOREIGN KEY (`timesheet_id`) REFERENCES `timesheets` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `time_logs`
--
ALTER TABLE `time_logs`
  ADD CONSTRAINT `time_logs_ibfk_1` FOREIGN KEY (`employee_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`manager_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
-- =====================
-- Admin demo sample data (core_transaction2)
-- =====================
USE `core_transaction2`;

-- =====================
-- Sample seller user for RAEVOR (vendor_id = 1)
-- =====================
-- Username: seller, Password: seller123
INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `address`, `avatar_url`, `is_verified`, `role`, `vendor_id`, `created_at`) VALUES
(1, 'seller', '$2y$10$ZRJX5Ho7SLRDYKOx/W3Tn.Gvy1p2KgSkYJDfpxxBrx1/U7g5jTjZy', 'seller@raevor.com', 'RAEVOR Seller', '+63-917-000-0001', NULL, NULL, 1, 'seller', 1, CURRENT_TIMESTAMP)
ON DUPLICATE KEY UPDATE 
  `password` = VALUES(`password`),
  `email` = VALUES(`email`),
  `role` = VALUES(`role`),
  `vendor_id` = VALUES(`vendor_id`);

-- Shops (stores)
INSERT IGNORE INTO `shops` (`id`,`name`,`owner_name`,`email`,`phone`,`address`,`status`,`created_at`) VALUES
(1,'Downtown Boutique','Ana Reyes','downtown@example.com','09170000001','123 Main St, Quezon City','Active','2025-09-15 09:00:00'),
(2,'City Sports Outlet','Mike Johnson','citysports@example.com','09170000002','456 City Ave, Manila','Active','2025-09-15 09:30:00'),
(3,'FashionHub Outlet','Sarah Lee','outlet@fashionhub.test','09170000003','789 Mall Rd, Makati','Active','2025-09-15 10:00:00'),
(4,'Levi''s Flagship BGC','Levi Operations Team','store.ph@levis.com','09179005000','One Bonifacio High Street, Taguig','Active','2025-09-15 11:00:00'),
(5,'H&M Mega Fashion Hall','H&M Philippines','customerservice@hm.com','09171110000','SM Megamall, Mandaluyong','Active','2025-09-15 11:30:00'),
(6,'Zara Ayala Makati','Zara Asia','contact.ph@zara.com','09178149900','Greenbelt 5, Makati','Active','2025-09-15 12:00:00'),
(7,'Mango Power Plant','Mango Studio','hello.ph@mango.com','09176027000','Power Plant Mall, Makati','Active','2025-09-15 12:30:00'),
(8,'Uniqlo Glorietta Flagship','Uniqlo Philippines','care@uniqlo.com','09178888800','Glorietta 5, Makati','Active','2025-09-15 13:00:00'),
(9,'Bench Lifestyle Store','Bench','care@bench.com.ph','09177777000','Trinoma Mall, Quezon City','Active','2025-09-15 13:30:00'),
(10,'Penshoppe Style Lab','Penshoppe','hello@penshoppe.com','09175553344','SM Mall of Asia, Pasay','Active','2025-09-15 14:00:00'),
(11,'DBTK Collectives','Don''t Blame The Kids','crew@dbtkco.com','09175557722','213 Kamagong St, Makati','Active','2025-09-15 14:30:00'),
(12,'Forever 21 Central','Forever 21 Philippines','service@forever21.com','09173332121','SM Makati, Makati City','Active','2025-09-15 15:00:00'),
(13,'Lovito Pop-up','Lovito','hello@lovito.com','09176001010','UP Town Center, Quezon City','Active','2025-09-15 15:30:00'),
(14,'Oxgn Urban Hub','Oxgn','support@oxgnfashion.com','09174567878','SM North EDSA, Quezon City','Active','2025-09-15 16:00:00'),
(15,'Zalora Online Warehouse','Zalora Philippines','partners@zalora.com','0285807777','C-5 Road, Taguig','Active','2025-09-15 16:30:00'),
(16,'Calvin Klein Greenbelt','Calvin Klein','store.ph@calvinklein.com','09179996666','Greenbelt 4, Makati','Active','2025-09-15 17:00:00'),
(17,'Trival Sports Hub','Trival','store@trival.ph','09179998888','BGC Central Square, Taguig','Active','2025-09-15 17:30:00');

-- Map products to shops
INSERT IGNORE INTO `shop_products` (`shop_id`,`product_id`) VALUES
(1,(SELECT id FROM products WHERE name='Classic Cotton Tee' LIMIT 1)),
(1,(SELECT id FROM products WHERE name='Evening Wrap Dress' LIMIT 1)),
(1,(SELECT id FROM products WHERE name='Pleated City Skirt' LIMIT 1)),
(2,(SELECT id FROM products WHERE name='City Break Jacket' LIMIT 1)),
(2,(SELECT id FROM products WHERE name='Weekend Shorts' LIMIT 1)),
(2,(SELECT id FROM products WHERE name='Tailored Work Pants' LIMIT 1)),
(3,(SELECT id FROM products WHERE name='Classic Cotton Tee' LIMIT 1)),
(3,(SELECT id FROM products WHERE name='Studio Denim Jacket' LIMIT 1)),
(3,(SELECT id FROM products WHERE name='Alpine Sweater' LIMIT 1)),
(4,(SELECT id FROM products WHERE name="Levi's 501 Original Fit Jeans" LIMIT 1)),
(4,(SELECT id FROM products WHERE name="Levi's Trucker Jacket" LIMIT 1)),
(4,(SELECT id FROM products WHERE name="Levi's Graphic Tee" LIMIT 1)),
(4,(SELECT id FROM products WHERE name="Levi's Vintage T-Shirt" LIMIT 1)),
(5,(SELECT id FROM products WHERE name='H&M Linen Blend Shirt' LIMIT 1)),
(5,(SELECT id FROM products WHERE name='H&M High Waist Mom Jeans' LIMIT 1)),
(5,(SELECT id FROM products WHERE name='H&M Pleated Midi Skirt' LIMIT 1)),
(5,(SELECT id FROM products WHERE name='H&M High-Waist Jeans' LIMIT 1)),
(5,(SELECT id FROM products WHERE name='H&M Silk Blouse' LIMIT 1)),
(6,(SELECT id FROM products WHERE name='Zara Satin Slip Dress' LIMIT 1)),
(6,(SELECT id FROM products WHERE name='Zara Faux Leather Biker Jacket' LIMIT 1)),
(6,(SELECT id FROM products WHERE name='Zara Knit Polo' LIMIT 1)),
(6,(SELECT id FROM products WHERE name='Zara Oversized Hoodie' LIMIT 1)),
(6,(SELECT id FROM products WHERE name='Zara Business Blazer' LIMIT 1)),
(7,(SELECT id FROM products WHERE name='Mango Tweed Blazer' LIMIT 1)),
(7,(SELECT id FROM products WHERE name='Mango Wide-leg Trousers' LIMIT 1)),
(7,(SELECT id FROM products WHERE name='Mango Crochet Knit Top' LIMIT 1)),
(7,(SELECT id FROM products WHERE name='Mango Minimalist Watch' LIMIT 1)),
(7,(SELECT id FROM products WHERE name='Mango Leather Handbag' LIMIT 1)),
(8,(SELECT id FROM products WHERE name='Uniqlo AIRism Crew Neck' LIMIT 1)),
(8,(SELECT id FROM products WHERE name='Uniqlo Ultra Light Down Jacket' LIMIT 1)),
(8,(SELECT id FROM products WHERE name='Uniqlo Smart Ankle Pants' LIMIT 1)),
(8,(SELECT id FROM products WHERE name='Uniqlo Comfort Sneakers' LIMIT 1)),
(8,(SELECT id FROM products WHERE name='Uniqlo Slim Jeans' LIMIT 1)),
(9,(SELECT id FROM products WHERE name='Bench Graphic Hoodie' LIMIT 1)),
(9,(SELECT id FROM products WHERE name='Bench Relaxed Tee Dress' LIMIT 1)),
(9,(SELECT id FROM products WHERE name='Bench Straight Cut Jeans' LIMIT 1)),
(9,(SELECT id FROM products WHERE name='Bench Urban Backpack' LIMIT 1)),
(9,(SELECT id FROM products WHERE name='Bench Canvas Sneakers' LIMIT 1)),
(10,(SELECT id FROM products WHERE name='Penshoppe Varsity Jacket' LIMIT 1)),
(10,(SELECT id FROM products WHERE name='Penshoppe Boxy Tee' LIMIT 1)),
(10,(SELECT id FROM products WHERE name='Penshoppe Cargo Joggers' LIMIT 1)),
(10,(SELECT id FROM products WHERE name='Penshoppe Street Hoodie' LIMIT 1)),
(11,(SELECT id FROM products WHERE name='DBTK Coach Jacket' LIMIT 1)),
(11,(SELECT id FROM products WHERE name='DBTK Statement Tee' LIMIT 1)),
(11,(SELECT id FROM products WHERE name='DBTK Tech Cargo Shorts' LIMIT 1)),
(11,(SELECT id FROM products WHERE name='Don''t Blame the Kids Baseball Cap' LIMIT 1)),
(12,(SELECT id FROM products WHERE name='Forever 21 Satin Bomber' LIMIT 1)),
(12,(SELECT id FROM products WHERE name='Forever 21 Crop Knit' LIMIT 1)),
(12,(SELECT id FROM products WHERE name='Forever 21 Flared Jeans' LIMIT 1)),
(12,(SELECT id FROM products WHERE name='Forever 21 Trendy Sunglasses' LIMIT 1)),
(12,(SELECT id FROM products WHERE name='Forever 21 Party Dress' LIMIT 1)),
(13,(SELECT id FROM products WHERE name='Lovito Puff Sleeve Dress' LIMIT 1)),
(13,(SELECT id FROM products WHERE name='Lovito Ribbed Crop Top' LIMIT 1)),
(13,(SELECT id FROM products WHERE name='Lovito High Waist Shorts' LIMIT 1)),
(13,(SELECT id FROM products WHERE name='Lovito Floral Blouse' LIMIT 1)),
(13,(SELECT id FROM products WHERE name='Lovito Midi Skirt' LIMIT 1)),
(14,(SELECT id FROM products WHERE name='Oxgn Nylon Windbreaker' LIMIT 1)),
(14,(SELECT id FROM products WHERE name='Oxgn Relaxed Denim Pants' LIMIT 1)),
(14,(SELECT id FROM products WHERE name='Oxgn Oversized Tee' LIMIT 1)),
(14,(SELECT id FROM products WHERE name='Oxgn Windbreaker' LIMIT 1)),
(15,(SELECT id FROM products WHERE name='Zalora Basic Blazer' LIMIT 1)),
(15,(SELECT id FROM products WHERE name='Zalora Pleated Trousers' LIMIT 1)),
(15,(SELECT id FROM products WHERE name='Zalora Wrap Top' LIMIT 1)),
(15,(SELECT id FROM products WHERE name='Zalora Summer Dress' LIMIT 1)),
(15,(SELECT id FROM products WHERE name='Zalora Casual Shirt' LIMIT 1)),

-- Calvin Klein shop products (shop_id: 16)
(16,(SELECT id FROM products WHERE name='Calvin Klein Logo T-Shirt' LIMIT 1)),
(16,(SELECT id FROM products WHERE name='Calvin Klein Slim Fit Jeans' LIMIT 1)),
(16,(SELECT id FROM products WHERE name='Calvin Klein Bralette' LIMIT 1)),
(16,(SELECT id FROM products WHERE name='Calvin Klein Boxer Briefs' LIMIT 1)),
(16,(SELECT id FROM products WHERE name='Calvin Klein Hoodie' LIMIT 1)),
(16,(SELECT id FROM products WHERE name='Calvin Klein Jacket' LIMIT 1)),

-- Additional DBTK products to existing DBTK shop (shop_id: 11)
(11,(SELECT id FROM products WHERE name='DBTK Graphic Hoodie' LIMIT 1)),
(11,(SELECT id FROM products WHERE name='DBTK Bucket Hat' LIMIT 1)),
(11,(SELECT id FROM products WHERE name='DBTK Cargo Pants' LIMIT 1)),
(11,(SELECT id FROM products WHERE name='DBTK Tank Top' LIMIT 1)),
(11,(SELECT id FROM products WHERE name='DBTK Sweatpants' LIMIT 1)),

-- Trival shop products (shop_id: 17)
(17,(SELECT id FROM products WHERE name='Trival Athletic Shorts' LIMIT 1)),
(17,(SELECT id FROM products WHERE name='Trival Sport Bra' LIMIT 1)),
(17,(SELECT id FROM products WHERE name='Trival Running Tee' LIMIT 1)),
(17,(SELECT id FROM products WHERE name='Trival Track Jacket' LIMIT 1)),
(17,(SELECT id FROM products WHERE name='Trival Yoga Pants' LIMIT 1)),
(17,(SELECT id FROM products WHERE name='Trival Windbreaker' LIMIT 1)),
(17,(SELECT id FROM products WHERE name='Trival Cross Training Shoes' LIMIT 1));

-- Customers (password is bcrypt hash for the word 'password')
INSERT IGNORE INTO `users` (`username`,`password`,`email`,`role`,`created_at`) VALUES
('areyes','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','areyes@example.com','customer','2025-09-18 08:00:00'),
('bsantos','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','bsantos@example.com','customer','2025-09-19 08:00:00'),
('cdelacruz','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','cdelacruz@example.com','customer','2025-09-20 08:00:00'),
('mjordan','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','mjordan@example.com','customer','2025-09-21 08:00:00'),
('lisa','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','lisa@example.com','customer','2025-09-22 08:00:00');

-- Additional sample account
INSERT IGNORE INTO `users` (`username`,`password`,`email`,`role`,`created_at`) VALUES
('sample1','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','sample1@example.com','customer','2025-09-23 08:00:00');

-- Cache product and user IDs
SET @p_tee := (SELECT id FROM products WHERE name='Classic Cotton Tee' LIMIT 1);
SET @p_pants := (SELECT id FROM products WHERE name='Tailored Work Pants' LIMIT 1);
SET @p_jacket := (SELECT id FROM products WHERE name='City Break Jacket' LIMIT 1);
SET @p_dress := (SELECT id FROM products WHERE name='Evening Wrap Dress' LIMIT 1);
SET @p_skirt := (SELECT id FROM products WHERE name='Pleated City Skirt' LIMIT 1);
SET @p_sweater := (SELECT id FROM products WHERE name='Alpine Sweater' LIMIT 1);
SET @p_denim_jacket := (SELECT id FROM products WHERE name='Studio Denim Jacket' LIMIT 1);
SET @p_levis_501 := (SELECT id FROM products WHERE name="Levi's 501 Original Fit Jeans" LIMIT 1);
SET @p_hm_linen := (SELECT id FROM products WHERE name='H&M Linen Blend Shirt' LIMIT 1);
SET @p_zara_slip := (SELECT id FROM products WHERE name='Zara Satin Slip Dress' LIMIT 1);
SET @p_mango_blazer := (SELECT id FROM products WHERE name='Mango Tweed Blazer' LIMIT 1);
SET @p_uniqlo_airism := (SELECT id FROM products WHERE name='Uniqlo AIRism Crew Neck' LIMIT 1);
SET @p_bench_hoodie := (SELECT id FROM products WHERE name='Bench Graphic Hoodie' LIMIT 1);
SET @p_levis_classic := (SELECT id FROM products WHERE name="Levi's Classic Denim Jacket" LIMIT 1);
SET @p_penshoppe_tee := (SELECT id FROM products WHERE name='Penshoppe Essential Tee' LIMIT 1);
SET @p_hm_highwaist := (SELECT id FROM products WHERE name='H&M High-Waist Jeans' LIMIT 1);
SET @p_zara_hoodie := (SELECT id FROM products WHERE name='Zara Oversized Hoodie' LIMIT 1);
SET @p_zalora_dress := (SELECT id FROM products WHERE name='Zalora Summer Dress' LIMIT 1);
SET @p_lovito_blouse := (SELECT id FROM products WHERE name='Lovito Floral Blouse' LIMIT 1);

SET @price_tee := (SELECT price FROM products WHERE id=@p_tee);
SET @price_pants := (SELECT price FROM products WHERE id=@p_pants);
SET @price_jacket := (SELECT price FROM products WHERE id=@p_jacket);
SET @price_dress := (SELECT price FROM products WHERE id=@p_dress);
SET @price_skirt := (SELECT price FROM products WHERE id=@p_skirt);
SET @price_sweater := (SELECT price FROM products WHERE id=@p_sweater);
SET @price_denim_jacket := (SELECT price FROM products WHERE id=@p_denim_jacket);

SET @u_areyes := (SELECT id FROM users WHERE username='areyes' LIMIT 1);
SET @u_bsantos := (SELECT id FROM users WHERE username='bsantos' LIMIT 1);
SET @u_cdelacruz := (SELECT id FROM users WHERE username='cdelacruz' LIMIT 1);
SET @u_mjordan := (SELECT id FROM users WHERE username='mjordan' LIMIT 1);
SET @u_lisa := (SELECT id FROM users WHERE username='lisa' LIMIT 1);

-- Orders (Cancelled) for admin demo
INSERT IGNORE INTO `orders` (`user_id`,`total_amount`,`status`,`created_at`) VALUES
(@u_areyes, @price_jacket + @price_tee, 'Cancelled', '2025-09-18 10:00:00'),
(@u_bsantos, @price_pants * 2, 'Cancelled', '2025-09-19 11:00:00'),
(@u_cdelacruz, @price_dress + @price_skirt, 'Cancelled', '2025-09-20 12:00:00'),
(@u_mjordan, @price_jacket + @price_sweater, 'Cancelled', '2025-09-21 13:00:00'),
(@u_lisa, @price_tee * 2 + @price_skirt, 'Cancelled', '2025-09-22 14:00:00');

-- Resolve order IDs
SET @o1 := (SELECT id FROM orders WHERE user_id=@u_areyes AND created_at='2025-09-18 10:00:00' LIMIT 1);
SET @o2 := (SELECT id FROM orders WHERE user_id=@u_bsantos AND created_at='2025-09-19 11:00:00' LIMIT 1);
SET @o3 := (SELECT id FROM orders WHERE user_id=@u_cdelacruz AND created_at='2025-09-20 12:00:00' LIMIT 1);
SET @o4 := (SELECT id FROM orders WHERE user_id=@u_mjordan AND created_at='2025-09-21 13:00:00' LIMIT 1);
SET @o5 := (SELECT id FROM orders WHERE user_id=@u_lisa AND created_at='2025-09-22 14:00:00' LIMIT 1);

-- Order items
INSERT IGNORE INTO `order_items` (`order_id`,`product_id`,`quantity`,`price`) VALUES
(@o1, @p_jacket, 1, @price_jacket),
(@o1, @p_tee, 1, @price_tee),
(@o2, @p_pants, 2, @price_pants),
(@o3, @p_dress, 1, @price_dress),
(@o3, @p_skirt, 1, @price_skirt),
(@o4, @p_jacket, 1, @price_jacket),
(@o4, @p_sweater, 1, @price_sweater),
(@o5, @p_tee, 2, @price_tee),
(@o5, @p_skirt, 1, @price_skirt);

-- History: some progressed to Processing before cancellation
INSERT IGNORE INTO `order_history` (`order_id`,`action`,`old_value`,`new_value`,`note`,`actor`,`created_at`) VALUES
(@o2,'updated_status','{"status":"Just Placed"}','{"status":"Processing"}','Auto-progressed','system','2025-09-19 11:05:00'),
(@o3,'updated_status','{"status":"Just Placed"}','{"status":"Processing"}','Auto-progressed','system','2025-09-20 12:05:00'),
(@o4,'updated_status','{"status":"Just Placed"}','{"status":"Processing"}','Auto-progressed','system','2025-09-21 13:05:00');

-- Cancellation reasons
INSERT IGNORE INTO `order_history` (`order_id`,`action`,`old_value`,`new_value`,`note`,`actor`,`created_at`) VALUES
(@o1,'updated_status','{"status":"Just Placed"}','{"status":"Cancelled"}','Wrong size ordered','customer','2025-09-18 10:10:00'),
(@o2,'updated_status','{"status":"Processing"}','{"status":"Cancelled"}','Customer changed mind','customer','2025-09-19 11:20:00'),
(@o3,'updated_status','{"status":"Processing"}','{"status":"Cancelled"}','Duplicate order','customer','2025-09-20 12:20:00'),
(@o4,'updated_status','{"status":"Processing"}','{"status":"Cancelled"}','Delayed shipment','customer','2025-09-21 13:20:00'),
(@o5,'updated_status','{"status":"Just Placed"}','{"status":"Cancelled"}','Found cheaper elsewhere','customer','2025-09-22 14:20:00');

-- Sample admin accounts for CT2 and CT3 departments
INSERT INTO `admin_accounts` (`username`, `password_hash`, `full_name`, `department`, `role`, `email`, `is_active`) VALUES
('ct2_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CT2 Administrator', 'CT2', 'admin', 'ct2@raevor.com', 1),
('ct3_admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'CT3 Administrator', 'CT3', 'admin', 'ct3@raevor.com', 1),
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super Administrator', 'CT3', 'super_admin', 'admin@raevor.com', 1);

-- Note: The password hash above is for 'password' - change these in production!

-- Deliveries and events for stage computation
INSERT IGNORE INTO `deliveries` (`order_id`,`carrier`,`tracking_number`,`status`,`scheduled_date`,`delivered_at`,`address`,`notes`,`created_at`) VALUES
(@o4, 'J&T', 'JT-20250921-0001', 'In Transit', NULL, NULL, NULL, NULL, '2025-09-21 13:10:00'),
(@o5, 'LBC', 'LBC-20250922-0001', 'In Transit', NULL, NULL, NULL, NULL, '2025-09-22 14:10:00');

SET @d4 := (SELECT id FROM deliveries WHERE order_id=@o4 LIMIT 1);
SET @d5 := (SELECT id FROM deliveries WHERE order_id=@o5 LIMIT 1);

INSERT IGNORE INTO `delivery_events` (`delivery_id`,`event_time`,`event_type`,`details`,`actor`) VALUES
(@d4, '2025-09-21 15:00:00', 'Dispatched', 'Parcel picked up by courier', 'system'),
(@d5, '2025-09-22 16:00:00', 'Arrived at hub', 'Parcel arrived at delivery hub', 'system');

-- 
-- Additional Enhanced Financial and Accounting Tables
-- 

-- 
-- Table structure for table `assets`
-- 

CREATE TABLE IF NOT EXISTS `assets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_name` varchar(255) NOT NULL,
  `asset_type` varchar(100) NOT NULL,
  `asset_code` varchar(50) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `category` varchar(100) DEFAULT 'General',
  `description` text DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(15,2) DEFAULT 0.00,
  `current_value` decimal(15,2) DEFAULT NULL,
  `status` enum('Active','Inactive','Disposed','Maintenance') DEFAULT 'Active',
  `location` varchar(255) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `approved_by` varchar(150) DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_assets_serial` (`serial_number`),
  KEY `idx_asset_type` (`asset_type`),
  KEY `idx_asset_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `asset_assignments`
-- 

CREATE TABLE IF NOT EXISTS `asset_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `employee_name` varchar(150) NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('Active','Returned') DEFAULT 'Active',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `return_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_asg_asset` (`asset_id`),
  CONSTRAINT `fk_asg_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `asset_requests`
-- 

CREATE TABLE IF NOT EXISTS `asset_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) UNSIGNED DEFAULT NULL,
  `asset_id` int(11) DEFAULT NULL,
  `requester_name` varchar(150) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_req_asset` (`asset_id`),
  KEY `idx_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `asset_maintenance`
-- 

CREATE TABLE IF NOT EXISTS `asset_maintenance` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(11) NOT NULL,
  `maintenance_type` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `cost` decimal(12,2) DEFAULT 0.00,
  `performed_by` varchar(150) NOT NULL,
  `maintenance_date` date NOT NULL,
  `status` enum('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_maint_asset` (`asset_id`),
  KEY `idx_maint_date` (`maintenance_date`),
  CONSTRAINT `fk_maint_asset` FOREIGN KEY (`asset_id`) REFERENCES `assets`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for table `chart_of_accounts`
-- 

CREATE TABLE IF NOT EXISTS `chart_of_accounts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `account_code` varchar(20) NOT NULL UNIQUE,
  `account_name` varchar(255) NOT NULL,
  `account_type` enum('Asset','Liability','Equity','Revenue','Expense') NOT NULL,
  `parent_account_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_account_type` (`account_type`),
  KEY `idx_parent_account` (`parent_account_id`),
  CONSTRAINT `fk_coa_parent` FOREIGN KEY (`parent_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `journal_entries_v2`
-- 

CREATE TABLE IF NOT EXISTS `journal_entries_v2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_number` varchar(50) NOT NULL UNIQUE,
  `entry_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `total_debit` decimal(15,2) DEFAULT 0.00,
  `total_credit` decimal(15,2) DEFAULT 0.00,
  `status` enum('Draft','Posted','Reversed') DEFAULT 'Draft',
  `created_by` varchar(100) DEFAULT NULL,
  `posted_by` varchar(100) DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_entry_date_v2` (`entry_date`),
  KEY `idx_status_v2` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `journal_entry_lines_v2`
-- 

CREATE TABLE IF NOT EXISTS `journal_entry_lines_v2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `journal_entry_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit_amount` decimal(15,2) DEFAULT 0.00,
  `credit_amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `line_number` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_journal_entry_v2` (`journal_entry_id`),
  KEY `idx_account_v2` (`account_id`),
  CONSTRAINT `fk_jel_v2_journal` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries_v2` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_jel_v2_account` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `general_ledger_v2`
-- 

CREATE TABLE IF NOT EXISTS `general_ledger_v2` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entry_date` date NOT NULL,
  `reference` varchar(100) NOT NULL,
  `account_id` int(11) NOT NULL,
  `debit_amount` decimal(15,2) DEFAULT 0.00,
  `credit_amount` decimal(15,2) DEFAULT 0.00,
  `description` text DEFAULT NULL,
  `source_module` varchar(50) DEFAULT NULL,
  `source_reference` varchar(100) DEFAULT NULL,
  `status` enum('Draft','Posted','Reversed') DEFAULT 'Draft',
  `posted_by` varchar(100) DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_gl_v2_entry_date` (`entry_date`),
  KEY `idx_gl_v2_account` (`account_id`),
  KEY `idx_gl_v2_reference` (`reference`),
  KEY `idx_gl_v2_source` (`source_module`, `source_reference`),
  CONSTRAINT `fk_gl_v2_account` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `budget_periods`
-- 

CREATE TABLE IF NOT EXISTS `budget_periods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `period_name` varchar(100) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `status` enum('Draft','Active','Closed') DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_period_dates` (`start_date`, `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `budget_allocations`
-- 

CREATE TABLE IF NOT EXISTS `budget_allocations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `budget_period_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `allocated_amount` decimal(15,2) NOT NULL,
  `committed_amount` decimal(15,2) DEFAULT 0.00,
  `actual_amount` decimal(15,2) DEFAULT 0.00,
  `status` enum('Draft','Approved','Active','Closed') DEFAULT 'Draft',
  `created_by` varchar(100) DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_budget_period` (`budget_period_id`),
  KEY `idx_account` (`account_id`),
  KEY `idx_department` (`department_id`),
  CONSTRAINT `fk_ba_period` FOREIGN KEY (`budget_period_id`) REFERENCES `budget_periods` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ba_account_coa` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `budget_requests`
-- 

CREATE TABLE IF NOT EXISTS `budget_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `request_number` varchar(50) NOT NULL UNIQUE,
  `account_id` int(11) NOT NULL,
  `department_id` int(11) DEFAULT NULL,
  `requested_amount` decimal(15,2) NOT NULL,
  `purpose` text DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected','Cancelled') DEFAULT 'Pending',
  `requested_by` varchar(100) DEFAULT NULL,
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_br_account` (`account_id`),
  KEY `idx_br_department` (`department_id`),
  KEY `idx_br_status` (`status`),
  CONSTRAINT `fk_br_account_coa` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `budget_commitments`
-- 

CREATE TABLE IF NOT EXISTS `budget_commitments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `budget_allocation_id` int(11) NOT NULL,
  `commitment_reference` varchar(100) NOT NULL,
  `committed_amount` decimal(15,2) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Committed','Consumed','Released') DEFAULT 'Committed',
  `committed_by` varchar(100) DEFAULT NULL,
  `consumed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_budget_allocation` (`budget_allocation_id`),
  KEY `idx_bc_reference` (`commitment_reference`),
  CONSTRAINT `fk_bc_allocation` FOREIGN KEY (`budget_allocation_id`) REFERENCES `budget_allocations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `customers`
-- 

CREATE TABLE IF NOT EXISTS `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(50) NOT NULL UNIQUE,
  `customer_name` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `credit_limit` decimal(15,2) DEFAULT 0.00,
  `payment_terms` int(11) DEFAULT 30,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_customer_code` (`customer_code`),
  KEY `idx_customer_name` (`customer_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `sales_invoices`
-- 

CREATE TABLE IF NOT EXISTS `sales_invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(50) NOT NULL UNIQUE,
  `customer_id` int(11) NOT NULL,
  `invoice_date` date NOT NULL,
  `due_date` date NOT NULL,
  `subtotal` decimal(15,2) NOT NULL,
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL,
  `paid_amount` decimal(15,2) DEFAULT 0.00,
  `balance_amount` decimal(15,2) NOT NULL,
  `status` enum('Draft','Sent','Paid','Overdue','Cancelled') DEFAULT 'Draft',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_si_customer` (`customer_id`),
  KEY `idx_si_invoice_date` (`invoice_date`),
  KEY `idx_si_due_date` (`due_date`),
  KEY `idx_si_status` (`status`),
  CONSTRAINT `fk_si_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `sales_invoice_items`
-- 

CREATE TABLE IF NOT EXISTS `sales_invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `line_total` decimal(15,2) NOT NULL,
  `tax_rate` decimal(5,2) DEFAULT 0.00,
  `tax_amount` decimal(15,2) DEFAULT 0.00,
  PRIMARY KEY (`id`),
  KEY `idx_sii_invoice` (`invoice_id`),
  CONSTRAINT `fk_sii_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `customer_payments`
-- 

CREATE TABLE IF NOT EXISTS `customer_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_number` varchar(50) NOT NULL UNIQUE,
  `customer_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `payment_method` enum('Cash','Bank Transfer','Check','Credit Card','GCash','PayMaya') DEFAULT 'Cash',
  `reference_number` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Pending','Confirmed','Reversed') DEFAULT 'Pending',
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_cp_customer` (`customer_id`),
  KEY `idx_cp_payment_date` (`payment_date`),
  KEY `idx_cp_status` (`status`),
  CONSTRAINT `fk_cp_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `payment_allocations`
-- 

CREATE TABLE IF NOT EXISTS `payment_allocations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `allocated_amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_pa_payment` (`payment_id`),
  KEY `idx_pa_invoice` (`invoice_id`),
  CONSTRAINT `fk_pa_payment` FOREIGN KEY (`payment_id`) REFERENCES `customer_payments` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pa_invoice` FOREIGN KEY (`invoice_id`) REFERENCES `sales_invoices` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `vendor_payments`
-- 

CREATE TABLE IF NOT EXISTS `vendor_payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_number` varchar(50) NOT NULL UNIQUE,
  `vendor_id` int(11) NOT NULL,
  `payment_date` date NOT NULL,
  `payment_amount` decimal(15,2) NOT NULL,
  `payment_method` enum('Cash','Bank Transfer','Check','Wire Transfer') DEFAULT 'Bank Transfer',
  `check_number` varchar(50) DEFAULT NULL,
  `reference_number` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Pending','Approved','Paid','Cancelled') DEFAULT 'Pending',
  `approved_by` varchar(100) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 
-- Table structure for table `vendor_payment_allocations`
-- 

CREATE TABLE IF NOT EXISTS `vendor_payment_allocations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `payment_id` int(11) NOT NULL,
  `invoice_id` int(11) NOT NULL,
  `allocated_amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_vpa_payment` (`payment_id`),
  KEY `idx_vpa_invoice` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add missing FKs after both sides exist
ALTER TABLE `vendor_payment_allocations`
  ADD CONSTRAINT `fk_vpa_payment_v2` FOREIGN KEY (`payment_id`) REFERENCES `vendor_payments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_vpa_invoice_v2` FOREIGN KEY (`invoice_id`) REFERENCES `vendor_bills` (`id`) ON DELETE CASCADE;

-- Views (v2) - use _v2 to avoid collisions
DROP VIEW IF EXISTS `account_balances_v2`;
CREATE VIEW `account_balances_v2` AS
SELECT 
  coa.`id`,
  coa.`account_code`,
  coa.`account_name`,
  coa.`account_type`,
  COALESCE(SUM(gl.`debit_amount`), 0) AS `total_debits`,
  COALESCE(SUM(gl.`credit_amount`), 0) AS `total_credits`,
  CASE 
    WHEN coa.`account_type` IN ('Asset','Expense') THEN COALESCE(SUM(gl.`debit_amount`), 0) - COALESCE(SUM(gl.`credit_amount`), 0)
    ELSE COALESCE(SUM(gl.`credit_amount`), 0) - COALESCE(SUM(gl.`debit_amount`), 0)
  END AS `balance`
FROM `chart_of_accounts` coa
LEFT JOIN `general_ledger_v2` gl ON coa.`id` = gl.`account_id` AND gl.`status` = 'Posted'
GROUP BY coa.`id`, coa.`account_code`, coa.`account_name`, coa.`account_type`;

DROP VIEW IF EXISTS `aging_report_v2`;
CREATE VIEW `aging_report_v2` AS
SELECT 
  si.`id` AS `invoice_id`,
  si.`invoice_number`,
  c.`customer_name`,
  si.`invoice_date`,
  si.`due_date`,
  si.`balance_amount`,
  CASE WHEN DATEDIFF(CURDATE(), si.`due_date`) <= 0 THEN si.`balance_amount` ELSE 0 END AS `current_amount`,
  CASE WHEN DATEDIFF(CURDATE(), si.`due_date`) BETWEEN 1 AND 30 THEN si.`balance_amount` ELSE 0 END AS `days_1_30`,
  CASE WHEN DATEDIFF(CURDATE(), si.`due_date`) BETWEEN 31 AND 60 THEN si.`balance_amount` ELSE 0 END AS `days_31_60`,
  CASE WHEN DATEDIFF(CURDATE(), si.`due_date`) BETWEEN 61 AND 90 THEN si.`balance_amount` ELSE 0 END AS `days_61_90`,
  CASE WHEN DATEDIFF(CURDATE(), si.`due_date`) > 90 THEN si.`balance_amount` ELSE 0 END AS `days_over_90`
FROM `sales_invoices` si
JOIN `customers` c ON si.`customer_id` = c.`id`
WHERE si.`status` IN ('Sent','Overdue') AND si.`balance_amount` > 0;

-- Stored Procedures (v2) - renamed to avoid collisions
DELIMITER //
DROP PROCEDURE IF EXISTS `sp_create_journal_entry_v2` //
CREATE PROCEDURE `sp_create_journal_entry_v2`(
  IN p_entry_date DATE,
  IN p_description TEXT,
  IN p_created_by VARCHAR(100)
)
BEGIN
  DECLARE v_entry_id INT;
  DECLARE v_entry_number VARCHAR(50);
  SET v_entry_number = CONCAT('JE-', DATE_FORMAT(p_entry_date, '%Y%m%d'), '-', LPAD(FLOOR(RAND()*9999), 4, '0'));
  INSERT INTO `journal_entries_v2` (`entry_number`,`entry_date`,`description`,`created_by`)
  VALUES (v_entry_number, p_entry_date, p_description, p_created_by);
  SET v_entry_id = LAST_INSERT_ID();
  SELECT v_entry_id AS `journal_entry_id`, v_entry_number AS `entry_number`;
END //

DROP PROCEDURE IF EXISTS `sp_post_journal_entry_v2` //
CREATE PROCEDURE `sp_post_journal_entry_v2`(
  IN p_entry_id INT,
  IN p_posted_by VARCHAR(100)
)
BEGIN
  DECLARE v_total_debit DECIMAL(15,2) DEFAULT 0;
  DECLARE v_total_credit DECIMAL(15,2) DEFAULT 0;
  SELECT COALESCE(SUM(`debit_amount`),0), COALESCE(SUM(`credit_amount`),0)
    INTO v_total_debit, v_total_credit
    FROM `journal_entry_lines_v2`
    WHERE `journal_entry_id` = p_entry_id;
  IF v_total_debit <> v_total_credit THEN
    SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Debits and credits must be equal';
  END IF;
  UPDATE `journal_entries_v2`
    SET `status`='Posted', `total_debit`=v_total_debit, `total_credit`=v_total_credit, `posted_by`=p_posted_by, `posted_at`=NOW()
    WHERE `id`=p_entry_id;
  INSERT INTO `general_ledger_v2` (`entry_date`,`reference`,`account_id`,`debit_amount`,`credit_amount`,`description`,`source_module`,`source_reference`,`status`,`posted_by`,`posted_at`)
  SELECT je.`entry_date`, je.`entry_number`, jel.`account_id`, jel.`debit_amount`, jel.`credit_amount`, jel.`description`, 'Journal Entry', je.`entry_number`, 'Posted', p_posted_by, NOW()
  FROM `journal_entries_v2` je
  JOIN `journal_entry_lines_v2` jel ON je.`id` = jel.`journal_entry_id`
  WHERE je.`id` = p_entry_id;
END //
DELIMITER ;

COMMIT;

-- ============================
-- HR compatibility views (added after merge)
-- ============================

-- Create HR compatibility view in hr_core database
USE `hr_core`;
DROP VIEW IF EXISTS `hr_users_compat`;
CREATE VIEW `hr_users_compat` AS
SELECT 
  u.`id`,
  CASE
    WHEN COALESCE(NULLIF(TRIM(u.`employee_id`), ''), NULL) IS NOT NULL THEN u.`employee_id`
    ELSE CONCAT('EMP', LPAD(u.`id`, 6, '0'))
  END AS `employee_id`,
  u.`email`,
  u.`password`,
  u.`first_name`,
  u.`last_name`,
  u.`middle_name`,
  u.`phone`,
  u.`address`,
  u.`date_of_birth`,
  u.`gender`,
  u.`position` AS `position`,
  u.`department_id`,
  u.`manager_id`,
  u.`employment_type`,
  u.`start_date`,
  u.`salary`,
  u.`status`,
  u.`role`,
  u.`created_at`,
  u.`updated_at`
FROM `users` u;

-- Convenience view in app DB: select HR users from hr_core
USE `core_transaction2`;
DROP VIEW IF EXISTS `hr_users_compat`;
CREATE VIEW `hr_users_compat` AS
SELECT * FROM `hr_core`.`hr_users_compat`;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
