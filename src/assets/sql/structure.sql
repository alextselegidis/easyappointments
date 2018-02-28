SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS `ea_appointments` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `book_datetime` DATETIME,
    `start_datetime` DATETIME,
    `end_datetime` DATETIME,
    `notes` TEXT,
    `hash` TEXT,
    `is_unavailable` TINYINT(4) DEFAULT '0',
    `id_users_provider` BIGINT(20) UNSIGNED,
    `id_users_customer` BIGINT(20) UNSIGNED,
    `id_services` BIGINT(20) UNSIGNED,
    `id_google_calendar` TEXT,
    PRIMARY KEY (`id`),
    KEY `id_users_customer` (`id_users_customer`),
    KEY `id_services` (`id_services`),
    KEY `id_users_provider` (`id_users_provider`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;


CREATE TABLE `ea_migrations` (
    `version` INT(11) NOT NULL
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;

CREATE TABLE IF NOT EXISTS `ea_roles` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(256),
    `slug` VARCHAR(256),
    `is_admin` TINYINT(4),
    `appointments` INT(4),
    `customers` INT(4),
    `services` INT(4),
    `users` INT(4),
    `system_settings` INT(4),
    `user_settings` INT(11),
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS `ea_secretaries_providers` (
    `id_users_secretary` BIGINT(20) UNSIGNED NOT NULL,
    `id_users_provider` BIGINT(20) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_users_secretary`, `id_users_provider`),
    KEY `fk_ea_secretaries_providers_1` (`id_users_secretary`),
    KEY `fk_ea_secretaries_providers_2` (`id_users_provider`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS `ea_services` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(256),
    `duration` INT(11),
    `price` DECIMAL(10, 2),
    `currency` VARCHAR(32),
    `description` TEXT,
    `availabilities_type` VARCHAR(32) DEFAULT 'flexible',
    `attendants_number` INT(11) DEFAULT '1',
    `id_service_categories` BIGINT(20) UNSIGNED,
    PRIMARY KEY (`id`),
    KEY `id_service_categories` (`id_service_categories`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS `ea_services_providers` (
    `id_users` BIGINT(20) UNSIGNED NOT NULL,
    `id_services` BIGINT(20) UNSIGNED NOT NULL,
    PRIMARY KEY (`id_users`, `id_services`),
    KEY `id_services` (`id_services`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS `ea_service_categories` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(256),
    `description` TEXT,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS `ea_settings` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(512),
    `value` LONGTEXT,
    PRIMARY KEY (`id`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS `ea_users` (
    `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
    `first_name` VARCHAR(256),
    `last_name` VARCHAR(512),
    `email` VARCHAR(512),
    `mobile_number` VARCHAR(128),
    `phone_number` VARCHAR(128),
    `address` VARCHAR(256),
    `city` VARCHAR(256),
    `state` VARCHAR(128),
    `zip_code` VARCHAR(64),
    `notes` TEXT,
    `id_roles` BIGINT(20) UNSIGNED NOT NULL,
    PRIMARY KEY (`id`),
    KEY `id_roles` (`id_roles`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;


CREATE TABLE IF NOT EXISTS `ea_user_settings` (
    `id_users` BIGINT(20) UNSIGNED NOT NULL,
    `username` VARCHAR(256),
    `password` VARCHAR(512),
    `salt` VARCHAR(512),
    `working_plan` TEXT,
    `notifications` TINYINT(4) DEFAULT '0',
    `google_sync` TINYINT(4) DEFAULT '0',
    `google_token` TEXT,
    `google_calendar` VARCHAR(128),
    `sync_past_days` INT(11) DEFAULT '5',
    `sync_future_days` INT(11) DEFAULT '5',
    `calendar_view` VARCHAR(32) DEFAULT 'default',
    PRIMARY KEY (`id_users`)
)
    ENGINE = InnoDB
    DEFAULT CHARSET = utf8;


ALTER TABLE `ea_appointments`
    ADD CONSTRAINT `ea_appointments_ibfk_2` FOREIGN KEY (`id_users_customer`) REFERENCES `ea_users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    ADD CONSTRAINT `ea_appointments_ibfk_3` FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    ADD CONSTRAINT `ea_appointments_ibfk_4` FOREIGN KEY (`id_users_provider`) REFERENCES `ea_users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;


ALTER TABLE `ea_secretaries_providers`
    ADD CONSTRAINT `fk_ea_secretaries_providers_1` FOREIGN KEY (`id_users_secretary`) REFERENCES `ea_users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    ADD CONSTRAINT `fk_ea_secretaries_providers_2` FOREIGN KEY (`id_users_provider`) REFERENCES `ea_users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;


ALTER TABLE `ea_services`
    ADD CONSTRAINT `ea_services_ibfk_1` FOREIGN KEY (`id_service_categories`) REFERENCES `ea_service_categories` (`id`)
    ON DELETE SET NULL
    ON UPDATE CASCADE;


ALTER TABLE `ea_services_providers`
    ADD CONSTRAINT `ea_services_providers_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `ea_users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
    ADD CONSTRAINT `ea_services_providers_ibfk_2` FOREIGN KEY (`id_services`) REFERENCES `ea_services` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;


ALTER TABLE `ea_users`
    ADD CONSTRAINT `ea_users_ibfk_1` FOREIGN KEY (`id_roles`) REFERENCES `ea_roles` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;


ALTER TABLE `ea_user_settings`
    ADD CONSTRAINT `ea_user_settings_ibfk_1` FOREIGN KEY (`id_users`) REFERENCES `ea_users` (`id`)
    ON DELETE CASCADE
    ON UPDATE CASCADE;

