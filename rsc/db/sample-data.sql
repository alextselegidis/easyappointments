
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `easyappointments`
--

--
-- Dumping data for table `ea_settings`
--

INSERT INTO `ea_settings` (`id`, `name`, `value`) VALUES
(1, 'company_working_plan', '{"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"saturday":null,"sunday":null}'),
(2, 'book_advance_timeout', '30'),
(3, 'company_name', 'ABC Company'),
(4, 'company_email', 'info@abc-company.ea'),
(5, 'company_link', 'http://www.abc-company.ea');

--
-- Dumping data for table `ea_roles`
--

INSERT INTO `ea_roles` (`id`, `name`, `slug`, `is_admin`, `appointments`, `customers`, `services`, `users`, `system_settings`, `user_settings`) VALUES
(1, 'Administrator', 'admin', 1, 15, 15, 15, 15, 15, 15),
(2, 'Provider', 'provider', 0, 15, 15, 0, 0, 0, 15),
(3, 'Customer', 'customer', 0, 0, 0, 0, 0, 0, 0),
(4, 'Secretary', 'secretary', 0, 15, 15, 0, 0, 0, 15);

--
-- Dumping data for table `ea_users`
--

INSERT INTO `ea_users` (`id`, `first_name`, `last_name`, `email`, `mobile_number`, `phone_number`, `address`, `city`, `state`, `zip_code`, `notes`, `id_roles`) VALUES
(1, 'John', 'Smith', 'info@abc-company.ea', '0123 4567890', '0123 4567891', 'Tst-Address 12', 'Tst-City', 'Tst-State', '012345', 'Test administrator record for this installation. ', 1),
(2, 'George ', 'Clayton', 'g.clayton@abc-company.ea', '0123 456 7890', '0123 456 7891', 'Tst-Address 12', 'Tst-City', 'Tst-State', '012345', 'This is one of the test providers. He will handle the quick services.', 2),
(3, 'Christina', 'Nickolson', 'c.nickolson@abc-company.ea', '0123 4567890', '0123 4567891', 'Tst-Address 12', 'Tst-City', 'Tst-State', '012345', 'This provider will handle the long services.', 2),
(4, 'Nicky', 'Rowland', 'n.rowland@abc-company.ea', '0123 4567890', '0123 4567891', 'Tst-Address 12', 'Tst-City', 'Tst-State', '012345', 'This is the only secretary of the app. She will handle the data of Christina.', 4),
(5, 'John', 'Doe', 'john.doe@abc-company.ea', NULL, '0123 4567890', 'Tst-Address 12', 'Tst-City', NULL, '012345', 'This is a test customer record.', 3);

--
-- Dumping data for table `ea_user_settings`
--

INSERT INTO `ea_user_settings` (`id_users`, `username`, `password`, `salt`, `working_plan`, `notifications`, `google_sync`, `google_token`, `google_calendar`, `sync_past_days`, `sync_future_days`) VALUES
(1, 'administrator', 'fa7fc34500cbed7c3546f8b223f10797f24ecc9ccbf0c2251c21ab965ebf19bf', 'e0a9e47fbf57babcf536e98bed783a9404b95b671cdcf9e391f68989fa3ac14f', NULL, 1, 0, NULL, NULL, 5, 5),
(2, 'g.clayton', 'a86f0c41be938c36eaedb3c4869c445fe8b7192188110f57bf86b55ac2252f05', '9b56eaa06cb0cc1c3bdce616291da4f53c2cc5d3449591bacb28099346333f05', '{"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"saturday":null,"sunday":null}', 1, 0, NULL, NULL, 5, 5),
(3, 'c.nickolson', '7dcd5ed6a1cc42de678227aa8d528069761b218d181ff7ef446a49622c384782', '807349d6bacc35650205c66d664d4d414d150dec7f17dbba360752224bab73f4', '{"monday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"tuesday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"wednesday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"thursday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"friday":{"start":"09:00","end":"18:00","breaks":[{"start":"14:30","end":"15:00"}]},"saturday":null,"sunday":null}', 1, 0, NULL, NULL, 5, 5),
(4, 'n.rowland', '8cd1cc276a72f88e6d27372aecde8d87903a91fa33a6d2aceafbf2d110e4c9c2', '55b3a83cea1e3a5c4945fcb12a7621519705b3d09c4fa3408d912a832c8410a1', NULL, 1, 0, NULL, NULL, 5, 5);

--
-- Dumping data for table `ea_secretaries_providers`
--

INSERT INTO `ea_secretaries_providers` (`id_users_secretary`, `id_users_provider`) VALUES
(4, 3);


--
-- Dumping data for table `ea_service_categories`
--

INSERT INTO `ea_service_categories` (`id`, `name`, `description`) VALUES
(1, 'Quick Services', 'A collection of services that have small duration. '),
(2, 'Long Services', 'A collection of services that require more time. ');

--
-- Dumping data for table `ea_services`
--

INSERT INTO `ea_services` (`id`, `name`, `duration`, `price`, `currency`, `description`, `id_service_categories`) VALUES
(1, 'Best Quick Service ', 30, 50.00, '€', 'This is the best service and it requires only 30 minutes of your time!', 1),
(2, 'Another Q. Service', 45, 60.00, '€', 'This will be the best choice for someone that wants a balance between time and money.', 1),
(3, 'Best Long Service', 180, 100.00, '€', 'This long service will examine all the aspects of your problem. Definitely the best solution!', 2),
(4, 'Ungrouped Srv.', 45, 0.00, '', 'This category is not like the rest. It can''t be grouped. ', NULL);

--
-- Dumping data for table `ea_services_providers`
--

INSERT INTO `ea_services_providers` (`id_users`, `id_services`) VALUES
(2, 1),
(2, 2),
(3, 3),
(2, 4),
(3, 4);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
