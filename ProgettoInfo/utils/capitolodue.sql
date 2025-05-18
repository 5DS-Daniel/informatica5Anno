-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Creato il: Feb 24, 2025 alle 21:43
-- Versione del server: 10.4.32-MariaDB
-- Versione PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `capitolodue`
--
CREATE DATABASE IF NOT EXISTS `capitolodue` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `capitolodue`;

-- --------------------------------------------------------

--
-- Struttura della tabella `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `descrizione` text NOT NULL,
  `prezzo` decimal(10,2) NOT NULL,
  `immagine` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `products`
--

INSERT INTO `products` (`id`, `user_id`, `nome`, `descrizione`, `prezzo`, `immagine`) VALUES
(1, 17, 'Smartphone XYZ', 'Smartphone con schermo OLED e fotocamera 108MP.', 599.99, 'https://via.placeholder.com/200'),
(2, 17, 'Laptop UltraBook', 'Notebook leggero e potente con processore Intel i7 e 16GB di RAM.', 999.99, 'https://via.placeholder.com/200'),
(3, 17, 'Auricolari Wireless Pro', 'Auricolari con cancellazione attiva del rumore e autonomia di 24 ore.', 149.99, 'https://via.placeholder.com/200'),
(4, 17, 'Smartwatch FitBand', 'Smartwatch con monitoraggio del battito cardiaco e GPS.', 199.99, 'https://via.placeholder.com/200'),
(5, 17, 'Monitor 4K UltraHD', 'Monitor da 27 pollici con risoluzione 4K UltraHD e HDR.', 349.99, 'https://via.placeholder.com/200');

-- --------------------------------------------------------

--
-- Struttura della tabella `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `profile_pic` varchar(255) NOT NULL DEFAULT 'default.png',
  `ruolo` enum('utente','admin') NOT NULL DEFAULT 'utente',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dump dei dati per la tabella `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `created_at`, `profile_pic`, `ruolo`) VALUES
(15, 'ziotomilboss', 'danielfranchini@virgilio.ru', '$2y$10$F9MdrajN6Zu3kKbBE4bvkeCYGBKvmdEHWtW8W/X9uNfIQzf2ys2O2', '2025-02-24 19:18:32', 'default.png', 'utente'),
(17, 'SIUUUUUUUM', 'danielfranchini@virgilio.ko', '$2y$10$EEyv/mTYmUV9FRtPwSmft.km9W2c7Oi/xh3uxa39vqVc6qr0N9Hau', '2025-02-24 19:51:58', 'default.png', 'admin');

--
-- Trigger `users`
--
DELIMITER $$
CREATE TRIGGER `check_email_before_insert` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
    IF NEW.email NOT REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'L''email non è valida.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_email_before_update` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
    IF NEW.email NOT REGEXP '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,}$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Email non è valida.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_username_before_insert` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
    IF LENGTH(NEW.username) < 3 OR LENGTH(NEW.username) > 20 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Il nome utente deve avere tra 3 e 20 caratteri.';
    END IF;
    
    IF NEW.username NOT REGEXP '^[a-zA-Z0-9_]+$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Il nome utente può contenere solo lettere, numeri e underscore.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `check_username_before_update` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
    IF LENGTH(NEW.username) < 3 OR LENGTH(NEW.username) > 20 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Il nome utente deve avere tra 3 e 20 caratteri.';
    END IF;
    
    IF NEW.username NOT REGEXP '^[a-zA-Z0-9_]+$' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Il nome utente può contenere solo lettere, numeri e underscore.';
    END IF;
END
$$
DELIMITER ;

--
-- Limiti per le tabelle scaricate
--

--
-- Limiti per la tabella `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
