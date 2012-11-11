-- phpMyAdmin SQL Dump
-- version 3.5.2.2
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 27 Οκτ 2012 στις 22:47:58
-- Έκδοση διακομιστή: 5.5.27
-- Έκδοση PHP: 5.4.7

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Βάση: `easy_appointments`
--
CREATE DATABASE `easy_appointments` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `easy_appointments`;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `appointments`
--

CREATE TABLE IF NOT EXISTS `appointments` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `appointment_date` varchar(45) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `description` text,
  `id_plans` int(10) unsigned DEFAULT NULL,
  `id_users` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_appointments_plans_idx` (`id_plans`),
  KEY `fk_appointments_1_idx` (`id_users`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `blank_spaces`
--

CREATE TABLE IF NOT EXISTS `blank_spaces` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `id_plans` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_blank_spaces_plans_idx` (`id_plans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `days`
--

CREATE TABLE IF NOT EXISTS `days` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `id_plans` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_days_1_idx` (`id_plans`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `options`
--

CREATE TABLE IF NOT EXISTS `options` (
  `id` int(11) NOT NULL,
  `name` varchar(256) DEFAULT NULL,
  `value` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `plans`
--

CREATE TABLE IF NOT EXISTS `plans` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(45) DEFAULT NULL,
  `description` varchar(45) DEFAULT NULL,
  `id_users` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_plans_users_idx` (`id_users`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(128) DEFAULT NULL,
  `password` varchar(128) DEFAULT NULL,
  `first_name` varchar(128) DEFAULT NULL,
  `last_name` varchar(256) DEFAULT NULL,
  `phone` varchar(64) DEFAULT NULL,
  `cell_phone` varchar(64) DEFAULT NULL,
  `city` varchar(64) DEFAULT NULL,
  `address` varchar(128) DEFAULT NULL,
  `zip_code` varchar(32) DEFAULT NULL,
  `vat_number` varchar(32) DEFAULT NULL,
  `tax_office` varchar(128) DEFAULT NULL,
  `comments` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Περιορισμοί για άχρηστους πίνακες
--

--
-- Περιορισμοί για πίνακα `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appointments_plans` FOREIGN KEY (`id_plans`) REFERENCES `plans` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_appointments_users` FOREIGN KEY (`id_users`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Περιορισμοί για πίνακα `blank_spaces`
--
ALTER TABLE `blank_spaces`
  ADD CONSTRAINT `fk_blank_spaces_plans` FOREIGN KEY (`id_plans`) REFERENCES `plans` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Περιορισμοί για πίνακα `days`
--
ALTER TABLE `days`
  ADD CONSTRAINT `fk_days_plans` FOREIGN KEY (`id_plans`) REFERENCES `plans` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Περιορισμοί για πίνακα `plans`
--
ALTER TABLE `plans`
  ADD CONSTRAINT `fk_plans_users` FOREIGN KEY (`id_users`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
