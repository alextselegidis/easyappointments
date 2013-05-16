-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 08 Μάη 2013 στις 17:29:41
-- Έκδοση διακομιστή: 5.5.24-log
-- Έκδοση PHP: 5.4.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Βάση: `easy_appointments`
--

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_appointments`
--

CREATE TABLE IF NOT EXISTS `ea_appointments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `notes` text,
  `id_users_provider` bigint(20) unsigned NOT NULL,
  `id_users_customer` bigint(20) unsigned NOT NULL,
  `id_services` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_users_customer` (`id_users_customer`),
  KEY `id_services` (`id_services`),
  KEY `id_users_provider` (`id_users_provider`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;

--
-- Άδειασμα δεδομένων του πίνακα `ea_appointments`
--

INSERT INTO `ea_appointments` (`id`, `start_datetime`, `end_datetime`, `notes`, `id_users_provider`, `id_users_customer`, `id_services`) VALUES
(14, '2013-04-26 12:40:00', '2013-04-26 12:40:00', '', 2, 1, 1),
(15, '2013-04-26 16:00:00', '2013-04-26 16:00:00', '', 2, 1, 1),
(16, '2013-04-26 13:00:00', '2013-04-26 13:00:00', 'Something else here ...', 2, 19, 1),
(17, '2013-04-26 14:00:00', '2013-04-26 14:00:00', '', 2, 20, 1),
(18, '2013-04-26 14:20:00', '2013-04-26 14:20:00', '', 2, 19, 1),
(19, '2013-04-26 14:20:00', '2013-04-26 14:20:00', 'Some notes ...', 2, 20, 1),
(20, '2013-04-26 14:30:00', '2013-04-26 14:30:00', 'ooo', 3, 20, 2),
(21, '2013-04-26 15:40:00', '2013-04-26 15:40:00', '', 2, 21, 1),
(22, '2013-04-26 16:40:00', '2013-04-26 16:40:00', '', 2, 21, 1),
(23, '2013-04-26 14:40:00', '2013-04-26 14:40:00', '', 2, 21, 1),
(24, '2013-05-01 18:00:00', '2013-05-01 18:00:00', '', 2, 19, 1),
(25, '2013-05-01 18:20:00', '2013-05-01 18:20:00', '', 2, 19, 1),
(26, '2013-05-01 18:40:00', '2013-05-01 18:40:00', '', 2, 19, 1),
(27, '2013-05-02 00:00:00', '2013-05-02 19:01:00', '', 2, 19, 1),
(28, '2013-05-03 13:00:00', '2013-05-03 13:00:00', '', 2, 19, 1),
(29, '2013-05-03 13:40:00', '2013-05-03 13:40:00', '', 2, 19, 1),
(30, '2013-05-03 14:20:00', '2013-05-03 14:20:00', '', 2, 19, 1),
(31, '2013-05-04 08:00:00', '2013-05-04 08:00:00', '', 3, 19, 3),
(32, '2013-05-03 00:00:00', '2013-05-03 20:45:00', '', 3, 19, 2),
(33, '2013-05-04 08:20:00', '2013-05-04 08:20:00', '', 2, 19, 1),
(34, '2013-05-04 09:20:00', '2013-05-04 09:20:00', '', 2, 19, 1),
(35, '2013-05-04 12:40:00', '2013-05-04 12:40:00', '', 2, 19, 1),
(36, '2013-05-04 13:20:00', '2013-05-04 13:20:00', '', 2, 19, 1),
(37, '2013-05-04 08:00:00', '2013-05-04 08:00:00', '', 2, 19, 1),
(38, '2013-05-03 00:00:00', '2013-05-03 23:18:00', '', 2, 19, 1),
(40, '2013-05-04 11:20:00', '2013-05-04 11:20:00', '', 2, 19, 1),
(41, '2013-05-04 12:00:00', '2013-05-04 12:00:00', '', 2, 19, 1),
(42, '2013-05-04 17:30:00', '2013-05-04 17:30:00', '', 3, 19, 2),
(43, '2013-05-04 19:00:00', '2013-05-04 19:00:00', '', 3, 19, 3),
(44, '2013-05-04 18:30:00', '2013-05-04 18:30:00', '', 4, 20, 2),
(45, '2013-05-07 11:00:00', '2013-05-07 11:00:00', 'Some notes ...', 2, 19, 1),
(46, '2013-05-07 11:20:00', '2013-05-07 11:20:00', '', 2, 19, 1),
(47, '2013-05-07 14:40:00', '2013-05-07 14:40:00', '', 2, 19, 1),
(48, '2013-05-07 15:00:00', '2013-05-07 15:00:00', '', 2, 20, 1),
(49, '2013-05-07 11:40:00', '2013-05-07 11:40:00', '', 2, 19, 1);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_roles`
--

CREATE TABLE IF NOT EXISTS `ea_roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `slug` varchar(256) DEFAULT NULL,
  `is_admin` tinyint(4) DEFAULT NULL COMMENT '0',
  `services` int(4) DEFAULT NULL COMMENT '0',
  `providers` int(4) DEFAULT NULL COMMENT '0',
  `customers` int(4) DEFAULT NULL COMMENT '0',
  `notifications` int(4) DEFAULT NULL COMMENT '0',
  `appointments` int(4) DEFAULT NULL COMMENT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Άδειασμα δεδομένων του πίνακα `ea_roles`
--

INSERT INTO `ea_roles` (`id`, `name`, `slug`, `is_admin`, `services`, `providers`, `customers`, `notifications`, `appointments`) VALUES
(1, 'Administrator', 'administrator', 1, 15, 15, 15, 15, 15),
(2, 'Provider', 'provider', 0, 0, 0, 15, 0, 15),
(3, 'Customer', 'customer', 0, 0, 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_services`
--

CREATE TABLE IF NOT EXISTS `ea_services` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `currency` varchar(32) DEFAULT NULL,
  `description` text,
  `id_service_categories` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_service_categories` (`id_service_categories`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Άδειασμα δεδομένων του πίνακα `ea_services`
--

INSERT INTO `ea_services` (`id`, `name`, `duration`, `price`, `currency`, `description`, `id_service_categories`) VALUES
(1, 'Γενική Εξέταση', 20, '50.00', 'euro', 'Γενική εξέταση του ασθενή.', NULL),
(2, 'Εξέταση Καρδιάς', 30, '40.00', 'euro', 'Εξέταση του ασθενή για νοσήματα καρδιάς.', NULL),
(3, 'Νευρολογική Εξέταση', 20, '35.00', 'euro', 'Νευρολογική εξέταση του ασθενή.', NULL);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_services_providers`
--

CREATE TABLE IF NOT EXISTS `ea_services_providers` (
  `id_users` bigint(20) unsigned NOT NULL,
  `id_services` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id_users`,`id_services`),
  KEY `id_services` (`id_services`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Άδειασμα δεδομένων του πίνακα `ea_services_providers`
--

INSERT INTO `ea_services_providers` (`id_users`, `id_services`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(3, 2),
(4, 2),
(2, 3),
(3, 3);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_service_categories`
--

CREATE TABLE IF NOT EXISTS `ea_service_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_settings`
--

CREATE TABLE IF NOT EXISTS `ea_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  `value` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Άδειασμα δεδομένων του πίνακα `ea_settings`
--

INSERT INTO `ea_settings` (`id`, `name`, `value`) VALUES
(1, 'business_name', 'Javation & Co'),
(2, 'business_working_hours', '{}'),
(3, 'business_email', 'alextselegidis@gmail.com');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_users`
--

CREATE TABLE IF NOT EXISTS `ea_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(256) DEFAULT NULL,
  `password` varchar(512) DEFAULT NULL,
  `first_name` varchar(256) DEFAULT NULL,
  `last_name` varchar(512) DEFAULT NULL,
  `email` varchar(512) DEFAULT NULL,
  `mobile_number` varchar(128) DEFAULT NULL,
  `phone_number` varchar(128) DEFAULT NULL,
  `address` varchar(256) DEFAULT NULL,
  `city` varchar(256) DEFAULT NULL,
  `state` varchar(128) DEFAULT NULL,
  `zip_code` varchar(64) DEFAULT NULL,
  `notes` text,
  `id_roles` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_roles` (`id_roles`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Άδειασμα δεδομένων του πίνακα `ea_users`
--

INSERT INTO `ea_users` (`id`, `username`, `password`, `first_name`, `last_name`, `email`, `mobile_number`, `phone_number`, `address`, `city`, `state`, `zip_code`, `notes`, `id_roles`) VALUES
(1, 'admin', 'admin', '', '1', 'alextselegidis@gmail.com', '123456789', '1', '', '', NULL, '', 'This is me making Easy!Appointments', 1),
(2, 'provider_1', 'provider_1', 'Γεώργιος', 'Παπαδόπουλος', 'alextselegidis@gmail.com', '1212121212', '1', '', '', NULL, '', 'This is a test provider', 2),
(3, 'provider_2', 'provider_2', 'Νίκος', 'Αναστασίου', 'prov2@test.gr', '1313133113131', '32132165146', 'Some Street 3', NULL, NULL, NULL, NULL, 2),
(4, 'provider_3', 'provider_3', 'Ηρώ', 'Καριοφύλη', 'prov3@test.gr', '239203490', '029340923', 'John Doe 3 ', NULL, NULL, NULL, NULL, 2),
(19, NULL, NULL, '', 'a', 'alextselegidis@gmail.com', NULL, 'a', '', '', NULL, '', NULL, 3),
(20, NULL, NULL, 'Alex', 'Tselegidis', 'alextselegidis@yahoo.gr', NULL, '6988589365', '', '', NULL, '', NULL, 3),
(21, NULL, NULL, '', '1', 'black-sabbath1967@hotmail.com', NULL, '1', '', '', NULL, '', NULL, 3);

--
-- Περιορισμοί για άχρηστους πίνακες
--

--
-- Περιορισμοί για πίνακα `ea_appointments`
--
ALTER TABLE `ea_appointments`
  ADD CONSTRAINT `ea_appointments_ibfk_2` FOREIGN KEY (`id_users_customer`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ea_appointments_ibfk_3` FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ea_appointments_ibfk_4` FOREIGN KEY (`id_users_provider`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `ea_services`
--
ALTER TABLE `ea_services`
  ADD CONSTRAINT `ea_services_ibfk_1` FOREIGN KEY (`id_service_categories`) REFERENCES `ea_service_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `ea_services_providers`
--
ALTER TABLE `ea_services_providers`
  ADD CONSTRAINT `ea_services_providers_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ea_services_providers_ibfk_2` FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `ea_users`
--
ALTER TABLE `ea_users`
  ADD CONSTRAINT `ea_users_ibfk_1` FOREIGN KEY (`id_roles`) REFERENCES `ea_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
