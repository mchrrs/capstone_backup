-- Database Initialization
-- phpMyAdmin SQL Dump
-- Version: 5.2.0
-- Generated on: Nov 21, 2022 at 09:26 PM
-- Server Version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Set UTF-8 Character Encoding
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: `home_db`

-- --------------------------------------------------------
-- Table: `admins`
CREATE TABLE `admins` (
    `id` VARCHAR(20) NOT NULL PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL -- Support longer hashed passwords
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `admins` (`id`, `name`, `password`) 
VALUES ('BcjKNX58e4x7bIqIvxG7', 'admin', '6216f8a75fd5bb3d5f22b6f9958cdede3fc086c2');

-- --------------------------------------------------------
-- Table: `users`
CREATE TABLE `users` (
    `id` VARCHAR(20) NOT NULL PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `number` VARCHAR(15) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL -- Secure hashed passwords
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: `property`
CREATE TABLE `property` (
    `id` varchar(20) NOT NULL,
    `user_id` varchar(20) NOT NULL,
    `property_name` varchar(50) NOT NULL,
    `address` varchar(100) NOT NULL,
    `price` varchar(10) NOT NULL,
    `type` varchar(10) NOT NULL,
    `offer` varchar(10) NOT NULL,
    `status` varchar(50) NOT NULL,
    `furnished` varchar(50) NOT NULL,
    `bedroom` varchar(10) NOT NULL,
    `bathroom` varchar(10) NOT NULL,
    `carpet` varchar(10) NOT NULL,
    `age` varchar(2) NOT NULL,
    `total_floors` varchar(2) NOT NULL,
    `room_floor` varchar(2) NOT NULL,
    `image_01` VARCHAR(255),
    `image_02` VARCHAR(255),
    `image_03` VARCHAR(255),
    `image_04` VARCHAR(255),
    `image_05` VARCHAR(255),
    `description` TEXT,
    `date` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: `qr_payments`
CREATE TABLE `qr_payments` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` VARCHAR(20) NOT NULL,
    `payment_amount` DECIMAL(10, 2) NOT NULL,
    `payment_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `qr_code` VARCHAR(255) NOT NULL,
    `status` ENUM('pending', 'completed', 'failed') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: `messages`
CREATE TABLE `messages` (
    `id` VARCHAR(20) NOT NULL PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `number` VARCHAR(15) NOT NULL,
    `message` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: `transactions`
CREATE TABLE `transactions` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` VARCHAR(20) NOT NULL,
    `property_id` VARCHAR(20) NOT NULL,
    `transaction_date` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `amount` DECIMAL(10, 2) NOT NULL,
    `status` ENUM('pending', 'completed', 'cancelled') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table: `complaints`
CREATE TABLE `complaints` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` VARCHAR(20) NOT NULL,
    `complaint_type` ENUM('Maintenance', 'Noise', 'Cleanliness', 'Safety', 'Utility', 'Payment', 'General') NOT NULL,
    `description` TEXT NOT NULL,
    `status` ENUM('Pending', 'In Progress', 'Resolved') DEFAULT 'Pending',
    `submitted_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- --------------------------------------------------------
-- Updated Table: `occupied_properties`
CREATE TABLE `occupied_properties` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `property_name` VARCHAR(255) NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `occupants` INT NOT NULL,
    `contract` VARCHAR(255),
    `number` VARCHAR(15) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `status` VARCHAR(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

COMMIT;
