-- phpMyAdmin SQL Dump
-- version 3.5.1
-- http://www.phpmyadmin.net
--
-- Φιλοξενητής: localhost
-- Χρόνος δημιουργίας: 24 Σεπ 2013 στις 11:03:51
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
  `book_datetime` datetime DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `notes` text,
  `hash` text,
  `is_unavailable` tinyint(4) DEFAULT '0',
  `id_users_provider` bigint(20) unsigned DEFAULT NULL,
  `id_users_customer` bigint(20) unsigned DEFAULT NULL,
  `id_services` bigint(20) unsigned DEFAULT NULL,
  `id_google_calendar` text,
  PRIMARY KEY (`id`),
  KEY `id_users_customer` (`id_users_customer`),
  KEY `id_services` (`id_services`),
  KEY `id_users_provider` (`id_users_provider`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=42 ;

--
-- Άδειασμα δεδομένων του πίνακα `ea_appointments`
--

INSERT INTO `ea_appointments` (`id`, `book_datetime`, `start_datetime`, `end_datetime`, `notes`, `hash`, `is_unavailable`, `id_users_provider`, `id_users_customer`, `id_services`, `id_google_calendar`) VALUES
(21, '2013-09-13 12:02:18', '2013-09-13 15:00:00', '2013-09-13 17:00:00', '', 'be278cf3c1d617fc372d89d75d5fd26d', 0, 2, 21, 4, 'e2c7abe3eket7ip9c58lllt3c8'),
(37, '2013-09-13 13:47:54', '2013-09-14 11:30:00', '2013-09-14 13:30:00', 'Γυμναστήριο ', '3ace1513fdf92a4983b7ae719a8475b5', 1, 2, NULL, NULL, 'cqm0t14p50d0917ghkirtruuno'),
(38, '2013-09-13 13:47:54', '2013-09-14 15:00:00', '2013-09-14 18:00:00', 'Ε!Α ', '3ace1513fdf92a4983b7ae719a8475b5', 1, 2, NULL, NULL, 'vs0btdvi34t73rvkeubh77ln40'),
(39, '2013-09-13 15:39:44', '2013-09-13 17:00:00', '2013-09-13 17:20:00', 'This is a test appt.', '6fd60f567310511d8f2fb4ff4c787d5e', 0, 2, 22, 3, NULL),
(40, '2013-09-13 15:50:14', '2013-09-14 10:00:00', '2013-09-14 11:00:00', 'heart decease', '39b81301e5bb1a82f77bd23d07ec63ce', 0, 4, 23, 2, NULL),
(41, '2013-09-23 17:04:53', '2013-09-24 09:45:00', '2013-09-24 10:15:00', '', '4c782e7af14a98e03657cc64c9a4fe61', 0, 25, 26, 4, NULL);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_roles`
--

CREATE TABLE IF NOT EXISTS `ea_roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `slug` varchar(256) DEFAULT NULL,
  `is_admin` tinyint(4) DEFAULT NULL COMMENT '0',
  `appointments` int(4) DEFAULT NULL COMMENT '0',
  `customers` int(4) DEFAULT NULL COMMENT '0',
  `services` int(4) DEFAULT NULL COMMENT '0',
  `users` int(4) DEFAULT NULL COMMENT '0',
  `system_settings` int(4) DEFAULT NULL COMMENT '0',
  `user_settings` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Άδειασμα δεδομένων του πίνακα `ea_roles`
--

INSERT INTO `ea_roles` (`id`, `name`, `slug`, `is_admin`, `appointments`, `customers`, `services`, `users`, `system_settings`, `user_settings`) VALUES
(1, 'Administrator', 'admin', 1, 15, 15, 15, 15, 15, NULL),
(2, 'Provider', 'provider', 0, 15, 15, 0, 0, 0, NULL),
(3, 'Customer', 'customer', 0, 0, 0, 0, 0, 0, NULL),
(4, 'Secretary', 'secretary', 0, 15, 15, 0, 0, 15, NULL);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_secretaries_providers`
--

CREATE TABLE IF NOT EXISTS `ea_secretaries_providers` (
  `id_users_secretary` bigint(20) unsigned NOT NULL,
  `id_users_provider` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id_users_secretary`,`id_users_provider`),
  KEY `fk_ea_secretaries_providers_1` (`id_users_secretary`),
  KEY `fk_ea_secretaries_providers_2` (`id_users_provider`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Άδειασμα δεδομένων του πίνακα `ea_secretaries_providers`
--

INSERT INTO `ea_secretaries_providers` (`id_users_secretary`, `id_users_provider`) VALUES
(20, 2),
(20, 3);

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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Άδειασμα δεδομένων του πίνακα `ea_services`
--

INSERT INTO `ea_services` (`id`, `name`, `duration`, `price`, `currency`, `description`, `id_service_categories`) VALUES
(2, 'Heart Examination', 30, '40.00', 'Euro', 'Checkup for heart problems.', NULL),
(3, 'Neurological Examination', 20, '35.00', 'Euro', 'Neurological tests for the patient.', NULL),
(4, 'General Examination', 30, '30.00', 'Euro', 'General patient examination. Includes blood, pressure and eyes tests.', 2);

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
(3, 2),
(4, 2),
(2, 3),
(3, 3),
(2, 4),
(25, 4);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_service_categories`
--

CREATE TABLE IF NOT EXISTS `ea_service_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(256) DEFAULT NULL,
  `description` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Άδειασμα δεδομένων του πίνακα `ea_service_categories`
--

INSERT INTO `ea_service_categories` (`id`, `name`, `description`) VALUES
(2, 'General Services', 'Contains the general services of our company.'),
(5, 'test1', 'test1'),
(7, 'test2', 'test2');

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_settings`
--

CREATE TABLE IF NOT EXISTS `ea_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(512) DEFAULT NULL,
  `value` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

--
-- Άδειασμα δεδομένων του πίνακα `ea_settings`
--

INSERT INTO `ea_settings` (`id`, `name`, `value`) VALUES
(1, 'company_name', 'Easy!Appointmnets & Co'),
(2, 'company_working_plan', '{"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"saturday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"sunday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]}}'),
(3, 'company_email', 'info@alextselegidis.com'),
(8, 'company_link', 'http://easyappointments.org'),
(9, 'book_advance_timeout', '30'),
(15, NULL, NULL);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_users`
--

CREATE TABLE IF NOT EXISTS `ea_users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
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
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=27 ;

--
-- Άδειασμα δεδομένων του πίνακα `ea_users`
--

INSERT INTO `ea_users` (`id`, `first_name`, `last_name`, `email`, `mobile_number`, `phone_number`, `address`, `city`, `state`, `zip_code`, `notes`, `id_roles`) VALUES
(2, 'Ned', 'Janger', 'alextselegidis@gmail.com', '659875666', '785448465', 'Kloesel', 'Berlin', '', '23980', '', 2),
(3, 'Urlich', 'Setzel', 'u.setzel@piorin.com', '23908252398', '20923798723', 'Groundliche Str. 23', 'Munich', 'Bayern', '86895', '', 2),
(4, 'Brandon', 'Clod', 'b.clod@besters.org', '239072439', '858754487', 'Wellin Str 8', 'Plymouth', '', '20940', '', 2),
(18, 'Tod', 'Cliffer', 'info@alextselegidis.com', '987568857', '875986878', 'Yourd Str 98', 'Blackpool', 'MyState', '85874', 'This is a test admin record used for testing the project. All the data are not real.', 1),
(20, 'Sonia', 'Sterling', 's.sterling@reo.com', '584256658', '4265462587', '', '', '', '', '', 4),
(21, 'Alex', 'Tselegidis', 'info@alextselegidis.com', NULL, '98765465712', '', '', NULL, '', '', 3),
(22, 'John', 'Doe', 'john.doe@oizent.com', NULL, '8757595445', 'Orizend 51', 'London', NULL, '56648', 'Test customer record.', 3),
(23, 'James', 'Goern', 'james.goern@softiner.com', NULL, '98654869544', 'Ureklin 09', 'New York', NULL, '56987', NULL, 3),
(24, 'test', 'test', 'test@test.com', '233252325', '234523342', 'test', 'test', '', '', '', 1),
(25, 'Jason', 'Brandon', 'j.brandon@solyell.uk', '7899875789', '7854789897', 'Hilton Str. 52', 'Michigan', '', '87786', 'This is a test provider. All data are fictional.', 2),
(26, 'John', 'Doe', 'j.doe@doens.com', NULL, '897987657', '', '', NULL, '', NULL, 3);

-- --------------------------------------------------------

--
-- Δομή πίνακα για τον πίνακα `ea_user_settings`
--

CREATE TABLE IF NOT EXISTS `ea_user_settings` (
  `id_users` bigint(20) unsigned NOT NULL,
  `username` varchar(256) DEFAULT NULL,
  `password` varchar(512) DEFAULT NULL,
  `salt` varchar(512) DEFAULT NULL,
  `working_plan` text,
  `notifications` tinyint(4) DEFAULT '0',
  `google_sync` tinyint(4) DEFAULT '0',
  `google_token` text,
  `sync_past_days` int(11) DEFAULT '5',
  `sync_future_days` int(11) DEFAULT '5',
  PRIMARY KEY (`id_users`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Άδειασμα δεδομένων του πίνακα `ea_user_settings`
--

INSERT INTO `ea_user_settings` (`id_users`, `username`, `password`, `salt`, `working_plan`, `notifications`, `google_sync`, `google_token`, `sync_past_days`, `sync_future_days`) VALUES
(2, 'ned.janger', '6ad76c5daab92f2aaf9f9d725cb72bc2774fdb4ac2172828a8f1c6aa69e9b0d1', 'edd27f8204a0cc47c60a3cd031fe03211be2561c76b334678e0f982ef582bf6e', '{"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"saturday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"sunday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]}}', 1, 0, NULL, 5, 5),
(3, 'u.setzel', 'f00e1e6f3780859b40645be7ff8e91878ea2679eb62fbc45a8bff1243338b741', '7f8231dd21df341c651522e4091637e6a93d160decb6a7a99bd08a5dc5d947c8', '{"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"saturday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"sunday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]}}', 1, 0, NULL, 5, 5),
(4, 'b.clod', '811acf5c450e0eb2866a17cdc3701a0b1fddb98ea2065e91259e8e6ce9b678b6', 'edd27f8204a0cc47c60a3cd031fe03211be2561c76b334678e0f982ef582bf6e', '{"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"saturday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"sunday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]}}', 0, 0, NULL, 5, 5),
(18, 'admin', '9e81360f0a631fe7e49e9d051b05c581a0f17575ca043a340be4441e166de821', 'd6ac3bfb4e6d9f82ec54e606852a9afbe8697696cddd28f30423eddf98762f41', NULL, 0, 0, NULL, 5, 5),
(20, 's.sterling', '8746aff0a416b63e71046d6a6adc6e2fd9de4a1cf4de0281e5b5f60ba8ae4451', 'edd27f8204a0cc47c60a3cd031fe03211be2561c76b334678e0f982ef582bf6e', NULL, 0, 0, NULL, 5, 5),
(24, 'test', 'd1dce587f7eefdb93adceb4e8903d72036bf97d37482b9c7b1d5f08353d061f3', 'd6ac3bfb4e6d9f82ec54e606852a9afbe8697696cddd28f30423eddf98762f41', NULL, 0, 0, NULL, 5, 5),
(25, 'j.brandon', 'dc93d098ccbcaa871e4adcc2dd770d71f6fca7a24dbd635e00006b5075dc2db1', '7f8231dd21df341c651522e4091637e6a93d160decb6a7a99bd08a5dc5d947c8', '{"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"saturday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"sunday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]}}', 1, 0, NULL, 5, 5);

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
-- Περιορισμοί για πίνακα `ea_secretaries_providers`
--
ALTER TABLE `ea_secretaries_providers`
  ADD CONSTRAINT `fk_ea_secretaries_providers_1` FOREIGN KEY (`id_users_secretary`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_ea_secretaries_providers_2` FOREIGN KEY (`id_users_provider`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Περιορισμοί για πίνακα `ea_services`
--
ALTER TABLE `ea_services`
  ADD CONSTRAINT `ea_services_ibfk_1` FOREIGN KEY (`id_service_categories`) REFERENCES `ea_service_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

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

--
-- Περιορισμοί για πίνακα `ea_user_settings`
--
ALTER TABLE `ea_user_settings`
  ADD CONSTRAINT `ea_user_settings_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
