SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `ea_appoINTments` (
  `id` INT(11) AUTO_INCREMENT NOT NULL,
  `book_datetime` datetime DEFAULT NULL,
  `start_datetime` datetime DEFAULT NULL,
  `end_datetime` datetime DEFAULT NULL,
  `notes` TEXT,
  `hash` TEXT,
  `is_unavailable` TINYINT(4) DEFAULT '0',
  `id_users_provider` INT(11) DEFAULT NULL,
  `id_users_customer` INT(11) DEFAULT NULL,
  `id_services` INT(11) DEFAULT NULL,
  `id_google_calendar` TEXT,
  PRIMARY KEY (`id`),
  KEY `id_users_customer` (`id_users_customer`),
  KEY `id_services` (`id_services`),
  KEY `id_users_provider` (`id_users_provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ea_roles` (
  `id` INT(11) AUTO_INCREMENT NOT NULL,
  `name` VARCHAR(256) DEFAULT NULL,
  `slug` VARCHAR(256) DEFAULT NULL,
  `is_admin` TINYINT(4) DEFAULT NULL,
  `appoINTments` INT(11) DEFAULT NULL,
  `customers` INT(11) DEFAULT NULL,
  `services` INT(11) DEFAULT NULL,
  `users` INT(11) DEFAULT NULL,
  `system_settings` INT(11) DEFAULT NULL,
  `user_settings` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ea_secretaries_providers` (
  `id_users_secretary` INT(11) NOT NULL,
  `id_users_provider` INT(11) NOT NULL,
  PRIMARY KEY (`id_users_secretary`,`id_users_provider`),
  KEY `fk_ea_secretaries_providers_1` (`id_users_secretary`),
  KEY `fk_ea_secretaries_providers_2` (`id_users_provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ea_services` (
  `id` INT(11) AUTO_INCREMENT NOT NULL,
  `name` VARCHAR(256) DEFAULT NULL,
  `duration` INT(11) DEFAULT NULL,
  `price` DECIMAL(10,2) DEFAULT NULL,
  `currency` VARCHAR(32) DEFAULT NULL,
  `description` TEXT,
  `availabilities_type` VARCHAR(32) DEFAULT 'flexible',
  `attendants_number` INT(11) DEFAULT '1',
  `id_service_categories` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `id_service_categories` (`id_service_categories`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ea_services_providers` (
  `id_users` INT(11) NOT NULL,
  `id_services` INT(11) NOT NULL,
  PRIMARY KEY (`id_users`,`id_services`),
  KEY `id_services` (`id_services`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ea_service_categories` (
  `id` INT(11) AUTO_INCREMENT NOT NULL,
  `name` VARCHAR(256) DEFAULT NULL,
  `description` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ea_settings` (
  `id` INT(11) AUTO_INCREMENT NOT NULL,
  `name` VARCHAR(512) DEFAULT NULL,
  `value` TEXT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ea_users` (
  `id` INT(11) AUTO_INCREMENT NOT NULL,
  `first_name` VARCHAR(256) DEFAULT NULL,
  `last_name` VARCHAR(512) DEFAULT NULL,
  `email` VARCHAR(512) DEFAULT NULL,
  `mobile_number` VARCHAR(128) DEFAULT NULL,
  `phone_number` VARCHAR(128) DEFAULT NULL,
  `address` VARCHAR(256) DEFAULT NULL,
  `city` VARCHAR(256) DEFAULT NULL,
  `state` VARCHAR(128) DEFAULT NULL,
  `zip_code` VARCHAR(64) DEFAULT NULL,
  `notes` TEXT,
  `id_roles` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_roles` (`id_roles`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `ea_user_settings` (
  `id_users` INT(11) NOT NULL,
  `username` VARCHAR(256) DEFAULT NULL,
  `password` VARCHAR(512) DEFAULT NULL,
  `salt` VARCHAR(512) DEFAULT NULL,
  `working_plan` TEXT,
  `notifications` TINYINT(4) DEFAULT '0',
  `google_sync` TINYINT(4) DEFAULT '0',
  `google_token` TEXT,
  `google_calendar` VARCHAR(128) DEFAULT NULL,
  `sync_past_days` INT(11) DEFAULT '5',
  `sync_future_days` INT(11) DEFAULT '5',
  `calendar_view` VARCHAR(32) DEFAULT 'default',
  PRIMARY KEY (`id_users`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `ea_appoINTments`
  ADD CONSTRAINT `ea_appoINTments_ea_users_customer` FOREIGN KEY (`id_users_customer`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ea_appoINTments_ea_services` FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ea_appoINTments_ea_users_provider` FOREIGN KEY (`id_users_provider`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ea_secretaries_providers`
  ADD CONSTRAINT `ea_secretaries_ea_users_secretary` FOREIGN KEY (`id_users_secretary`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ea_secretaries_ea_users_provider` FOREIGN KEY (`id_users_provider`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ea_services`
  ADD CONSTRAINT `ea_services_ea_service_categories` FOREIGN KEY (`id_service_categories`) REFERENCES `ea_service_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `ea_services_providers`
  ADD CONSTRAINT `ea_services_providers_ea_users_provider` FOREIGN KEY (`id_users`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `ea_services_providers_ea_services` FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ea_users`
  ADD CONSTRAINT `ea_users_ea_roles` FOREIGN KEY (`id_roles`) REFERENCES `ea_roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `ea_user_settings`
  ADD CONSTRAINT `ea_user_settings_ea_users` FOREIGN KEY (`id_users`) REFERENCES `ea_users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
