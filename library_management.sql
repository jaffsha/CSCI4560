-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2024 at 04:58 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

START TRANSACTION;

-- Set character encoding
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Database: `library_management`

-- Table structure for table `books`
CREATE TABLE `books` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `copies_available` INT(11) NOT NULL,
  `checked_out_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `books`
INSERT INTO `books` (`id`, `title`, `author`, `copies_available`, `checked_out_by`) VALUES
(1, 'To Kill a Mockingbird', 'Harper Lee', 4, NULL),
(2, '1984', 'George Orwell', 3, NULL),
(3, 'The Great Gatsby', 'F. Scott Fitzgerald', 4, NULL),
(4, 'Moby Dick', 'Herman Melville', 2, NULL),
(5, 'The Catcher in the Rye', 'J.D. Salinger', 6, NULL),
(7, 'The Great Gatsby', 'F. Scott Fitzgerald', 5, NULL);

-- Table structure for table `checked_out`
CREATE TABLE `checked_out` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) NOT NULL,
  `book_id` INT(11) NOT NULL,
  `checkout_date` DATETIME DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `checked_out_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  CONSTRAINT `checked_out_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Table structure for table `users`
CREATE TABLE `users` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `is_admin` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `users`
INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `is_admin`) VALUES
(1, 'jamietest', 'jamietest@test.com', '$2y$10$Ufu/lnvO3613FE4Lapnfj.0eUSkduuHUQLckpsVYaKzRKf5TC0oZG', 1),
(2, 'usertest', 'usertest@testing.com', '$2y$10$5/ZfutWCCemhYpnOjroG..Mz02nhXJqVzosOyTGmBorgWfInwJqhW', 0);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
