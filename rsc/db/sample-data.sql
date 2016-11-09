SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


INSERT INTO `ea_roles` (`id`, `name`, `slug`, `is_admin`, `appointments`, `customers`, `services`, `users`, `system_settings`, `user_settings`) VALUES
(1, 'Administrator', 'admin', 1, 15, 15, 15, 15, 15, 15),
(2, 'Provider', 'provider', 0, 15, 15, 0, 0, 0, 15),
(3, 'Customer', 'customer', 0, 0, 0, 0, 0, 0, 0),
(4, 'Secretary', 'secretary', 0, 15, 15, 0, 0, 0, 15);

INSERT INTO `ea_secretaries_providers` (`id_users_secretary`, `id_users_provider`) VALUES
(87, 85);

INSERT INTO `ea_services` (`id`, `name`, `duration`, `price`, `currency`, `description`, `availabilities_type`, `attendants_number`, `id_service_categories`) VALUES
(13, 'Test Service', 30, '50.00', 'Euro', 'This is a test service automatically inserted by the installer.', 'flexible', 1, NULL);

INSERT INTO `ea_services_providers` (`id_users`, `id_services`) VALUES
(85, 13);

INSERT INTO `ea_service_categories` (`id`, `name`, `description`) VALUES
(24, 'Test Category', 'This is a test service category.');

INSERT INTO `ea_settings` (`id`, `name`, `value`) VALUES
(16, 'company_working_plan', '{"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"saturday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]},"sunday":{"start":"09:00","end":"18:00","breaks":[{"start":"11:20","end":"11:30"},{"start":"14:30","end":"15:00"}]}}'),
(17, 'book_advance_timeout', '30'),
(18, 'google_analytics_code', ''),
(19, 'customer_notifications', '1'),
(20, 'date_format', 'DMY'),
(21, 'require_captcha', '0'),
(22, 'company_name', 'JD & Co'),
(23, 'company_email', 'john@doe12345.com'),
(24, 'company_link', 'http://doe12345.com');

INSERT INTO `ea_users` (`id`, `first_name`, `last_name`, `email`, `mobile_number`, `phone_number`, `address`, `city`, `state`, `zip_code`, `notes`, `id_roles`) VALUES
(84, 'John', 'Doe', 'john@doe12345.com', '123123123', '123123123', 'Some Str 123', 'Some City', 'Some State', '12345', 'This is a test administrator.', 1),
(85, 'Jane', 'Doe', 'jane@doe12345.com', '0123456789', '0123456789', 'Some Str 123', 'Some City', 'Some State', '12345', 'This is a test provider', 2),
(86, 'Chris', 'Doe', 'chris@doe12345.com', NULL, '123123123', 'Some Str 123', 'Some City', NULL, '12345', 'This is a test customer.', 3),
(87, 'Chloe', 'Doe', 'chloe@doe12345.com', '123123123', '123123123', 'Some Str 123', 'Some City', 'Some State', '12345', 'This is a test secretary.', 4);

INSERT INTO `ea_user_settings` (`id_users`, `username`, `password`, `salt`, `working_plan`, `notifications`, `google_sync`, `google_token`, `google_calendar`, `sync_past_days`, `sync_future_days`, `calendar_view`) VALUES
(84, 'administrator', 'c1871d3852f8b78503c5e4f585f3979d0580b4d0339533ae5b55abbe3dba7a4b', '703006e3a7c61db01c943eda81b50ac4ca567e1e61fd677b6a8b431c8bd95eeb', NULL, 0, 0, NULL, NULL, 5, 5, 'default'),
(85, 'jane@doe12345.com', '29cc4447507f0ea740a5d666944e997059b02cc0a968fa3f29eb5574cc1df2e7', 'f7b509c4b37f734ed1c3c65466b62843979bf4a354c3c3a1ab4752f9399df95c', '{"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"saturday":null,"sunday":null}', 0, 0, NULL, NULL, 5, 5, 'default'),
(87, 'chloe@doe12345.com', '2ad09a6c7b100048a48c380290206c35d953ed224c706d6f61d2198a9413c115', '8701d60910fb5994d4700f8057cebb16e94326d397241c7baac7c9b9c61012e6', NULL, 0, 0, NULL, NULL, 5, 5, 'default');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
