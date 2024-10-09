SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE Database library_database;
USE library_database;

-- Table structure for table `books`
CREATE TABLE `books` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `author` VARCHAR(255) NOT NULL,
  `description` varchar(1027),
  `length` int(5),
  `genre` varchar(255),
  `image` varchar(1027),
  `copies_available` INT(11) NOT NULL,
  `checked_out_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `users` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `is_admin` TINYINT(1) DEFAULT 0,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

-- Dumping data for table `users`
INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `is_admin`) VALUES
(1, 'jamietest', 'jamietest@test.com', '$2y$10$Ufu/lnvO3613FE4Lapnfj.0eUSkduuHUQLckpsVYaKzRKf5TC0oZG', 1),
(2, 'usertest', 'usertest@testing.com', '$2y$10$5/ZfutWCCemhYpnOjroG..Mz02nhXJqVzosOyTGmBorgWfInwJqhW', 0);

-- Dumping data for table `books`
INSERT INTO `books` ( `id`, `title`, `author`, `description`, `length`, `genre`, `image`) VALUES
(1, 'A Game Of Thrones', 'George R. R. Martin', 'The first book in A Song of Ice and Fire', 720, 'Fantasy', 'https://m.media-amazon.com/images/I/714ExofeKJL._AC_UF1000,1000_QL80_.jpg'),
(2, 'A Clash of Kings', 'George R. R. Martin', 'The second book in A Song of Ice and Fire', 1040, 'Fantasy', 'https://m.media-amazon.com/images/I/71R9pRtC6AL._AC_UF1000,1000_QL80_.jpg'),
(3, 'A Storm of Swords', 'George R. R. Martin', 'The third book in A Song of Ice and Fire', 1008, 'Fantasy', 'https://m.media-amazon.com/images/I/71hzYSMbvZL._AC_UF1000,1000_QL80_.jpg'),
(4, 'A Feast for Crows', 'George R. R. Martin', 'The fourth book in A Song of Ice and Fire', 1104, 'Fantasy', 'https://m.media-amazon.com/images/I/71wX5JhAkYL._AC_UF1000,1000_QL80_.jpg'),
(5, 'A Dance with Dragons', 'George R. R. Martin', 'The fifth book in A Song of Ice and Fire', 1152, 'Fantasy', 'https://m.media-amazon.com/images/I/71CrMiWhcDL._AC_UF1000,1000_QL80_.jpg'),
(6, 'Dune', 'Frank Herbert', 'The frist book in the Dune series', 896, 'Science Fiction', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQJsO6s_O3oX-H6QDpdo5oafiXNvySAZ-z-PdoznbEYZA&s'),
(7, 'Dune Messiah', 'Frank Herbert', 'The second book in the Dune series', 352, 'Science Fiction', 'https://m.media-amazon.com/images/I/817Xh+bqwOL._AC_UF1000,1000_QL80_.jpg'),
(8, 'Children of Dune', 'Frank Herbert', 'The third book in the Dune series', 416, 'Science Fiction', 'https://m.media-amazon.com/images/I/817pAbUYBpL._AC_UF1000,1000_QL80_.jpg'),
(9, 'The Lord of the Rings', 'J. R. R. Tolkien', "The first book in The Lord of the Rings series", 1216, 'Fantasy', 'https://m.media-amazon.com/images/I/7125+5E40JL._AC_UF1000,1000_QL80_.jpg'),
(10, 'The Two Towers', 'J. R. R. Tolkien', "The second book in The Lord of the Rings series", 416, 'Fantasy', 'https://m.media-amazon.com/images/I/61JO1okOIHL._AC_UF1000,1000_QL80_.jpg'),
(11, 'The Return of the King', 'J. R. R. Tolkien', "The third book in The Lord of the Rings series", 512, 'Fantasy', 'https://m.media-amazon.com/images/I/71tDovoHA+L._AC_UF1000,1000_QL80_.jpg'),
(12, 'The Hobbit', 'J. R. R. Tolkien', "A prequel to The Lord of the Rings", 300, 'Fantasy', 'https://m.media-amazon.com/images/I/712cDO7d73L._AC_UF1000,1000_QL80_.jpg'),
(13, 'The Design of Everyday Things', 'Don Norman', 'This book explores the complex interactions between humans and everyday objects', 368, 'Technical', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRYD_w4aVsG2IymZQUpYqSsR6KpSScrXQhfh7jTSHAmqg&s'),
(14, "Don't Make Me Think", 'Steve Krug', 'Guide to help understand the principles of intuitive design', 216, 'Technical', 'https://m.media-amazon.com/images/I/51sdCuqMwWL._AC_UF1000,1000_QL80_.jpg');

COMMIT;
