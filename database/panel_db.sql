-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 16, 2023 at 12:04 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `panel_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_rows`
--

CREATE TABLE `data_rows` (
  `id` int(10) UNSIGNED NOT NULL,
  `data_type_id` int(10) UNSIGNED NOT NULL,
  `field` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `required` tinyint(1) NOT NULL DEFAULT 0,
  `browse` tinyint(1) NOT NULL DEFAULT 1,
  `read` tinyint(1) NOT NULL DEFAULT 1,
  `edit` tinyint(1) NOT NULL DEFAULT 1,
  `add` tinyint(1) NOT NULL DEFAULT 1,
  `delete` tinyint(1) NOT NULL DEFAULT 1,
  `details` text DEFAULT NULL,
  `order` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_rows`
--

INSERT INTO `data_rows` (`id`, `data_type_id`, `field`, `type`, `display_name`, `required`, `browse`, `read`, `edit`, `add`, `delete`, `details`, `order`) VALUES
(1, 1, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, NULL, 1),
(2, 1, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, NULL, 2),
(3, 1, 'email', 'text', 'Email', 1, 1, 1, 1, 1, 1, NULL, 3),
(4, 1, 'password', 'password', 'Password', 1, 0, 0, 1, 1, 0, NULL, 4),
(5, 1, 'remember_token', 'text', 'Remember Token', 0, 0, 0, 0, 0, 0, NULL, 5),
(6, 1, 'created_at', 'timestamp', 'Created At', 0, 1, 1, 0, 0, 0, NULL, 6),
(7, 1, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, NULL, 7),
(8, 1, 'avatar', 'image', 'Avatar', 0, 1, 1, 1, 1, 1, NULL, 8),
(9, 1, 'user_belongsto_role_relationship', 'relationship', 'Role', 0, 1, 1, 1, 1, 0, '{\"model\":\"TCG\\\\Voyager\\\\Models\\\\Role\",\"table\":\"roles\",\"type\":\"belongsTo\",\"column\":\"role_id\",\"key\":\"id\",\"label\":\"display_name\",\"pivot_table\":\"roles\",\"pivot\":0}', 10),
(10, 1, 'user_belongstomany_role_relationship', 'relationship', 'Roles', 0, 1, 1, 1, 1, 0, '{\"model\":\"TCG\\\\Voyager\\\\Models\\\\Role\",\"table\":\"roles\",\"type\":\"belongsToMany\",\"column\":\"id\",\"key\":\"id\",\"label\":\"display_name\",\"pivot_table\":\"user_roles\",\"pivot\":\"1\",\"taggable\":\"0\"}', 11),
(11, 1, 'settings', 'hidden', 'Settings', 0, 0, 0, 0, 0, 0, NULL, 12),
(12, 2, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, NULL, 1),
(13, 2, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, NULL, 2),
(14, 2, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, NULL, 3),
(15, 2, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, NULL, 4),
(16, 3, 'id', 'number', 'ID', 1, 0, 0, 0, 0, 0, NULL, 1),
(17, 3, 'name', 'text', 'Name', 1, 1, 1, 1, 1, 1, NULL, 2),
(18, 3, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, NULL, 3),
(19, 3, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, NULL, 4),
(20, 3, 'display_name', 'text', 'Display Name', 1, 1, 1, 1, 1, 1, NULL, 5),
(21, 1, 'role_id', 'text', 'Role', 1, 1, 1, 1, 1, 1, NULL, 9),
(28, 5, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(29, 5, 'title', 'text', 'Title', 0, 1, 1, 1, 1, 1, '{}', 2),
(30, 5, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1\",\"options\":{\"0\":\"In Active\",\"1\":\"Active\"}}', 3),
(31, 5, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, '{}', 4),
(32, 5, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 5),
(33, 5, 'deleted_at', 'timestamp', 'Deleted At', 0, 0, 0, 0, 0, 0, '{}', 6),
(34, 6, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(35, 6, 'title', 'text', 'Title', 0, 1, 1, 1, 1, 1, '{}', 3),
(36, 6, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1\",\"options\":{\"0\":\"In Active\",\"1\":\"Active\"}}', 4),
(37, 6, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, '{}', 5),
(38, 6, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 6),
(39, 6, 'deleted_at', 'timestamp', 'Deleted At', 0, 0, 0, 0, 0, 0, '{}', 8),
(40, 7, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(41, 7, 'plan_type_id', 'text', 'Plan Type Id', 0, 0, 0, 1, 1, 0, '{}', 3),
(42, 7, 'description', 'text', 'Description', 0, 1, 1, 1, 1, 1, '{}', 4),
(43, 7, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1\",\"options\":{\"0\":\"In Active\",\"1\":\"Active\"}}', 5),
(44, 7, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, '{}', 6),
(45, 7, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 7),
(46, 7, 'deleted_at', 'timestamp', 'Deleted At', 0, 0, 0, 0, 0, 0, '{}', 8),
(47, 7, 'plan_belongsto_plan_type_relationship', 'relationship', 'Plan Type', 0, 1, 1, 1, 1, 1, '{\"model\":\"App\\\\PlanType\",\"table\":\"plan_types\",\"type\":\"belongsTo\",\"column\":\"plan_type_id\",\"key\":\"id\",\"label\":\"title\",\"pivot_table\":\"data_rows\",\"pivot\":\"0\",\"taggable\":\"0\"}', 2),
(48, 8, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(49, 8, 'title', 'text', 'Title', 0, 1, 1, 1, 1, 1, '{}', 2),
(50, 8, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1\",\"options\":{\"0\":\"In Active\",\"1\":\"Active\"}}', 3),
(51, 8, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, '{}', 4),
(52, 8, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 5),
(53, 8, 'deleted_at', 'timestamp', 'Deleted At', 0, 0, 0, 0, 0, 0, '{}', 6),
(56, 9, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(57, 9, 'title', 'text', 'Title', 0, 1, 1, 1, 1, 1, '{}', 2),
(58, 9, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1\",\"options\":{\"0\":\"In Active\",\"1\":\"Active\"}}', 3),
(59, 9, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, '{}', 4),
(60, 9, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 5),
(61, 9, 'deleted_at', 'timestamp', 'Deleted At', 0, 0, 0, 0, 0, 0, '{}', 6),
(62, 10, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(63, 10, 'bank_id', 'text', 'Bank Id', 0, 1, 1, 1, 1, 1, '{}', 2),
(64, 10, 'title', 'text', 'Title', 0, 1, 1, 1, 1, 1, '{}', 4),
(65, 10, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1\",\"options\":{\"0\":\"In Active\",\"1\":\"Active\"}}', 5),
(66, 10, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, '{}', 6),
(67, 10, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 7),
(68, 10, 'deleted_at', 'timestamp', 'Deleted At', 0, 0, 0, 0, 0, 0, '{}', 8),
(69, 10, 'bank_district_belongsto_bank_relationship', 'relationship', 'Bank', 0, 1, 1, 1, 1, 1, '{\"model\":\"App\\\\Bank\",\"table\":\"banks\",\"type\":\"belongsTo\",\"column\":\"bank_id\",\"key\":\"id\",\"label\":\"title\",\"pivot_table\":\"bank_districts\",\"pivot\":\"0\",\"taggable\":\"0\"}', 3),
(70, 11, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(71, 11, 'title', 'text', 'Title', 0, 1, 1, 1, 1, 1, '{}', 2),
(72, 11, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1\",\"options\":{\"0\":\"In Active\",\"1\":\"Active\"}}', 4),
(73, 11, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, '{}', 5),
(74, 11, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 6),
(75, 11, 'deleted_at', 'timestamp', 'Deleted At', 0, 0, 0, 0, 0, 0, '{}', 7),
(76, 11, 'bank_district_id', 'text', 'Bank District Id', 0, 1, 1, 1, 1, 1, '{}', 8),
(77, 11, 'bank_branch_name_belongsto_bank_district_relationship', 'relationship', 'Bank Districts', 0, 1, 1, 1, 1, 1, '{\"model\":\"App\\\\BankDistrict\",\"table\":\"bank_districts\",\"type\":\"belongsTo\",\"column\":\"bank_district_id\",\"key\":\"id\",\"label\":\"title\",\"pivot_table\":\"bank_branch_names\",\"pivot\":\"0\",\"taggable\":\"0\"}', 3),
(78, 12, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(79, 12, 'title', 'text', 'Title', 0, 1, 1, 1, 1, 1, '{}', 2),
(80, 12, 'digits', 'text', 'Digits', 0, 1, 1, 1, 1, 1, '{}', 3),
(81, 12, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1\",\"options\":{\"0\":\"In Active\",\"1\":\"Active\"}}', 4),
(82, 12, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, '{}', 5),
(83, 12, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 6),
(84, 12, 'deleted_at', 'timestamp', 'Deleted At', 0, 0, 0, 0, 0, 0, '{}', 7),
(85, 13, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(86, 13, 'title', 'text', 'Title', 0, 1, 1, 1, 1, 1, '{}', 2),
(87, 13, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1\",\"options\":{\"0\":\"In Active\",\"1\":\"Active\"}}', 3),
(88, 13, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, '{}', 4),
(89, 13, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 5),
(90, 13, 'deleted_at', 'timestamp', 'Deleted At', 0, 0, 0, 0, 0, 0, '{}', 6),
(91, 14, 'id', 'text', 'Id', 1, 0, 0, 0, 0, 0, '{}', 1),
(92, 14, 'title', 'text', 'Title', 0, 1, 1, 1, 1, 1, '{}', 2),
(93, 14, 'start_codes', 'text', 'Start Codes', 0, 1, 1, 1, 1, 1, '{}', 3),
(94, 14, 'status', 'select_dropdown', 'Status', 0, 1, 1, 1, 1, 1, '{\"default\":\"1\",\"options\":{\"0\":\"In Active\",\"1\":\"Active\"}}', 4),
(95, 14, 'created_at', 'timestamp', 'Created At', 0, 0, 0, 0, 0, 0, '{}', 5),
(96, 14, 'updated_at', 'timestamp', 'Updated At', 0, 0, 0, 0, 0, 0, '{}', 6),
(97, 14, 'deleted_at', 'timestamp', 'Deleted At', 0, 0, 0, 0, 0, 0, '{}', 7),
(98, 6, 'plan_type_belongsto_operator_relationship', 'relationship', 'Operator', 0, 1, 1, 1, 1, 1, '{\"model\":\"App\\\\Operator\",\"table\":\"operators\",\"type\":\"belongsTo\",\"column\":\"operator_id\",\"key\":\"id\",\"label\":\"title\",\"pivot_table\":\"bank_branch_names\",\"pivot\":\"0\",\"taggable\":\"0\"}', 2),
(99, 6, 'operator_id', 'text', 'Operator Id', 0, 1, 1, 1, 1, 1, '{}', 7);

-- --------------------------------------------------------

--
-- Table structure for table `data_types`
--

CREATE TABLE `data_types` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `display_name_singular` varchar(255) NOT NULL,
  `display_name_plural` varchar(255) NOT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `model_name` varchar(255) DEFAULT NULL,
  `policy_name` varchar(255) DEFAULT NULL,
  `controller` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `generate_permissions` tinyint(1) NOT NULL DEFAULT 0,
  `server_side` tinyint(4) NOT NULL DEFAULT 0,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `data_types`
--

INSERT INTO `data_types` (`id`, `name`, `slug`, `display_name_singular`, `display_name_plural`, `icon`, `model_name`, `policy_name`, `controller`, `description`, `generate_permissions`, `server_side`, `details`, `created_at`, `updated_at`) VALUES
(1, 'users', 'users', 'User', 'Users', 'voyager-person', 'TCG\\Voyager\\Models\\User', 'TCG\\Voyager\\Policies\\UserPolicy', 'TCG\\Voyager\\Http\\Controllers\\VoyagerUserController', '', 1, 0, NULL, '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(2, 'menus', 'menus', 'Menu', 'Menus', 'voyager-list', 'TCG\\Voyager\\Models\\Menu', NULL, '', '', 1, 0, NULL, '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(3, 'roles', 'roles', 'Role', 'Roles', 'voyager-lock', 'TCG\\Voyager\\Models\\Role', NULL, 'TCG\\Voyager\\Http\\Controllers\\VoyagerRoleController', '', 1, 0, NULL, '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(5, 'levels', 'levels', 'Level', 'Levels', NULL, 'App\\Level', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null}', '2023-09-24 02:41:06', '2023-09-24 02:41:06'),
(6, 'plan_types', 'plan-types', 'Plan Type', 'Plan Types', NULL, 'App\\PlanType', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2023-09-24 09:10:44', '2023-09-26 08:12:55'),
(7, 'plans', 'plans', 'Plan', 'Plans', NULL, 'App\\Plan', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2023-09-24 09:15:26', '2023-09-24 09:19:36'),
(8, 'companies', 'companies', 'Company', 'Companies', NULL, 'App\\Company', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null}', '2023-09-24 09:41:42', '2023-09-24 09:41:42'),
(9, 'banks', 'banks', 'Bank', 'Banks', NULL, 'App\\Bank', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null}', '2023-09-24 10:21:48', '2023-09-24 10:21:48'),
(10, 'bank_districts', 'bank-districts', 'Bank District', 'Bank Districts', NULL, 'App\\BankDistrict', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2023-09-24 10:24:03', '2023-09-24 10:25:06'),
(11, 'bank_branch_names', 'bank-branch-names', 'Bank Branch Name', 'Bank Branch Names', NULL, 'App\\BankBranchName', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2023-09-24 10:27:18', '2023-09-24 10:28:19'),
(12, 'services', 'services', 'Service', 'Services', NULL, 'App\\Service', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null}', '2023-09-25 02:27:17', '2023-09-25 02:27:17'),
(13, 'forms', 'forms', 'Form', 'Forms', NULL, 'App\\Form', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null,\"scope\":null}', '2023-09-25 05:17:25', '2023-09-25 05:18:23'),
(14, 'operators', 'operators', 'Operator', 'Operators', NULL, 'App\\Operator', NULL, NULL, NULL, 1, 0, '{\"order_column\":null,\"order_display_column\":null,\"order_direction\":\"asc\",\"default_search_key\":null}', '2023-09-25 08:01:22', '2023-09-25 08:01:22');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'admin', '2023-03-10 03:02:17', '2023-03-10 03:02:17');

-- --------------------------------------------------------

--
-- Table structure for table `menu_items`
--

CREATE TABLE `menu_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `menu_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `target` varchar(255) NOT NULL DEFAULT '_self',
  `icon_class` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `order` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `route` varchar(255) DEFAULT NULL,
  `parameters` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_items`
--

INSERT INTO `menu_items` (`id`, `menu_id`, `title`, `url`, `target`, `icon_class`, `color`, `parent_id`, `order`, `created_at`, `updated_at`, `route`, `parameters`) VALUES
(1, 1, 'Dashboard', '', '_self', 'voyager-boat', NULL, NULL, 1, '2023-03-10 03:02:17', '2023-03-10 03:02:17', 'voyager.dashboard', NULL),
(2, 1, 'Media', '', '_self', 'voyager-images', NULL, NULL, 5, '2023-03-10 03:02:17', '2023-03-10 03:02:17', 'voyager.media.index', NULL),
(3, 1, 'Users', '', '_self', 'voyager-person', NULL, NULL, 3, '2023-03-10 03:02:17', '2023-03-10 03:02:17', 'voyager.users.index', NULL),
(4, 1, 'Roles', '', '_self', 'voyager-lock', NULL, NULL, 2, '2023-03-10 03:02:17', '2023-03-10 03:02:17', 'voyager.roles.index', NULL),
(5, 1, 'Tools', '', '_self', 'voyager-tools', NULL, NULL, 9, '2023-03-10 03:02:17', '2023-03-10 03:02:17', NULL, NULL),
(6, 1, 'Menu Builder', '', '_self', 'voyager-list', NULL, 5, 10, '2023-03-10 03:02:17', '2023-03-10 03:02:17', 'voyager.menus.index', NULL),
(7, 1, 'Database', '', '_self', 'voyager-data', NULL, 5, 11, '2023-03-10 03:02:17', '2023-03-10 03:02:17', 'voyager.database.index', NULL),
(8, 1, 'Compass', '', '_self', 'voyager-compass', NULL, 5, 12, '2023-03-10 03:02:17', '2023-03-10 03:02:17', 'voyager.compass.index', NULL),
(9, 1, 'BREAD', '', '_self', 'voyager-bread', NULL, 5, 13, '2023-03-10 03:02:17', '2023-03-10 03:02:17', 'voyager.bread.index', NULL),
(10, 1, 'Settings', '', '_self', 'voyager-settings', NULL, NULL, 14, '2023-03-10 03:02:17', '2023-03-10 03:02:17', 'voyager.settings.index', NULL),
(12, 1, 'Levels', '', '_self', NULL, NULL, NULL, 15, '2023-09-24 02:41:06', '2023-09-24 02:41:06', 'voyager.levels.index', NULL),
(13, 1, 'Plan Types', '', '_self', NULL, NULL, NULL, 16, '2023-09-24 09:10:44', '2023-09-24 09:10:44', 'voyager.plan-types.index', NULL),
(14, 1, 'Plans', '', '_self', NULL, NULL, NULL, 17, '2023-09-24 09:15:27', '2023-09-24 09:15:27', 'voyager.plans.index', NULL),
(15, 1, 'Companies', '', '_self', NULL, NULL, NULL, 18, '2023-09-24 09:41:42', '2023-09-24 09:41:42', 'voyager.companies.index', NULL),
(16, 1, 'Banks', '', '_self', NULL, NULL, NULL, 19, '2023-09-24 10:21:48', '2023-09-24 10:21:48', 'voyager.banks.index', NULL),
(17, 1, 'Bank Districts', '', '_self', NULL, NULL, NULL, 20, '2023-09-24 10:24:03', '2023-09-24 10:24:03', 'voyager.bank-districts.index', NULL),
(18, 1, 'Bank Branch Names', '', '_self', NULL, NULL, NULL, 21, '2023-09-24 10:27:18', '2023-09-24 10:27:18', 'voyager.bank-branch-names.index', NULL),
(19, 1, 'Services', '', '_self', NULL, NULL, NULL, 22, '2023-09-25 02:27:17', '2023-09-25 02:27:17', 'voyager.services.index', NULL),
(20, 1, 'Forms', '', '_self', NULL, NULL, NULL, 23, '2023-09-25 05:17:25', '2023-09-25 05:17:25', 'voyager.forms.index', NULL),
(21, 1, 'Operators', '', '_self', NULL, NULL, NULL, 24, '2023-09-25 08:01:22', '2023-09-25 08:01:22', 'voyager.operators.index', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2016_01_01_000000_add_voyager_user_fields', 1),
(4, '2016_01_01_000000_create_data_types_table', 1),
(5, '2016_05_19_173453_create_menu_table', 1),
(6, '2016_10_21_190000_create_roles_table', 1),
(7, '2016_10_21_190000_create_settings_table', 1),
(8, '2016_11_30_135954_create_permission_table', 1),
(9, '2016_11_30_141208_create_permission_role_table', 1),
(10, '2016_12_26_201236_data_types__add__server_side', 1),
(11, '2017_01_13_000000_add_route_to_menu_items_table', 1),
(12, '2017_01_14_005015_create_translations_table', 1),
(13, '2017_01_15_000000_make_table_name_nullable_in_permissions_table', 1),
(14, '2017_03_06_000000_add_controller_to_data_types_table', 1),
(15, '2017_04_21_000000_add_order_to_data_rows_table', 1),
(16, '2017_07_05_210000_add_policyname_to_data_types_table', 1),
(17, '2017_08_05_000000_add_group_to_settings_table', 1),
(18, '2017_11_26_013050_add_user_role_relationship', 1),
(19, '2017_11_26_015000_create_user_roles_table', 1),
(20, '2018_03_11_000000_add_user_settings', 1),
(21, '2018_03_14_000000_add_details_to_data_types_table', 1),
(22, '2018_03_16_000000_make_settings_value_nullable', 1),
(23, '2019_08_19_000000_create_failed_jobs_table', 1),
(24, '2019_12_14_000001_create_personal_access_tokens_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `table_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `key`, `table_name`, `created_at`, `updated_at`) VALUES
(1, 'browse_admin', NULL, '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(2, 'browse_bread', NULL, '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(3, 'browse_database', NULL, '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(4, 'browse_media', NULL, '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(5, 'browse_compass', NULL, '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(6, 'browse_menus', 'menus', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(7, 'read_menus', 'menus', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(8, 'edit_menus', 'menus', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(9, 'add_menus', 'menus', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(10, 'delete_menus', 'menus', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(11, 'browse_roles', 'roles', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(12, 'read_roles', 'roles', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(13, 'edit_roles', 'roles', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(14, 'add_roles', 'roles', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(15, 'delete_roles', 'roles', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(16, 'browse_users', 'users', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(17, 'read_users', 'users', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(18, 'edit_users', 'users', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(19, 'add_users', 'users', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(20, 'delete_users', 'users', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(21, 'browse_settings', 'settings', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(22, 'read_settings', 'settings', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(23, 'edit_settings', 'settings', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(24, 'add_settings', 'settings', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(25, 'delete_settings', 'settings', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(31, 'browse_levels', 'levels', '2023-09-24 02:41:06', '2023-09-24 02:41:06'),
(32, 'read_levels', 'levels', '2023-09-24 02:41:06', '2023-09-24 02:41:06'),
(33, 'edit_levels', 'levels', '2023-09-24 02:41:06', '2023-09-24 02:41:06'),
(34, 'add_levels', 'levels', '2023-09-24 02:41:06', '2023-09-24 02:41:06'),
(35, 'delete_levels', 'levels', '2023-09-24 02:41:06', '2023-09-24 02:41:06'),
(36, 'browse_plan_types', 'plan_types', '2023-09-24 09:10:44', '2023-09-24 09:10:44'),
(37, 'read_plan_types', 'plan_types', '2023-09-24 09:10:44', '2023-09-24 09:10:44'),
(38, 'edit_plan_types', 'plan_types', '2023-09-24 09:10:44', '2023-09-24 09:10:44'),
(39, 'add_plan_types', 'plan_types', '2023-09-24 09:10:44', '2023-09-24 09:10:44'),
(40, 'delete_plan_types', 'plan_types', '2023-09-24 09:10:44', '2023-09-24 09:10:44'),
(41, 'browse_plans', 'plans', '2023-09-24 09:15:27', '2023-09-24 09:15:27'),
(42, 'read_plans', 'plans', '2023-09-24 09:15:27', '2023-09-24 09:15:27'),
(43, 'edit_plans', 'plans', '2023-09-24 09:15:27', '2023-09-24 09:15:27'),
(44, 'add_plans', 'plans', '2023-09-24 09:15:27', '2023-09-24 09:15:27'),
(45, 'delete_plans', 'plans', '2023-09-24 09:15:27', '2023-09-24 09:15:27'),
(46, 'browse_companies', 'companies', '2023-09-24 09:41:42', '2023-09-24 09:41:42'),
(47, 'read_companies', 'companies', '2023-09-24 09:41:42', '2023-09-24 09:41:42'),
(48, 'edit_companies', 'companies', '2023-09-24 09:41:42', '2023-09-24 09:41:42'),
(49, 'add_companies', 'companies', '2023-09-24 09:41:42', '2023-09-24 09:41:42'),
(50, 'delete_companies', 'companies', '2023-09-24 09:41:42', '2023-09-24 09:41:42'),
(51, 'browse_banks', 'banks', '2023-09-24 10:21:48', '2023-09-24 10:21:48'),
(52, 'read_banks', 'banks', '2023-09-24 10:21:48', '2023-09-24 10:21:48'),
(53, 'edit_banks', 'banks', '2023-09-24 10:21:48', '2023-09-24 10:21:48'),
(54, 'add_banks', 'banks', '2023-09-24 10:21:48', '2023-09-24 10:21:48'),
(55, 'delete_banks', 'banks', '2023-09-24 10:21:48', '2023-09-24 10:21:48'),
(56, 'browse_bank_districts', 'bank_districts', '2023-09-24 10:24:03', '2023-09-24 10:24:03'),
(57, 'read_bank_districts', 'bank_districts', '2023-09-24 10:24:03', '2023-09-24 10:24:03'),
(58, 'edit_bank_districts', 'bank_districts', '2023-09-24 10:24:03', '2023-09-24 10:24:03'),
(59, 'add_bank_districts', 'bank_districts', '2023-09-24 10:24:03', '2023-09-24 10:24:03'),
(60, 'delete_bank_districts', 'bank_districts', '2023-09-24 10:24:03', '2023-09-24 10:24:03'),
(61, 'browse_bank_branch_names', 'bank_branch_names', '2023-09-24 10:27:18', '2023-09-24 10:27:18'),
(62, 'read_bank_branch_names', 'bank_branch_names', '2023-09-24 10:27:18', '2023-09-24 10:27:18'),
(63, 'edit_bank_branch_names', 'bank_branch_names', '2023-09-24 10:27:18', '2023-09-24 10:27:18'),
(64, 'add_bank_branch_names', 'bank_branch_names', '2023-09-24 10:27:18', '2023-09-24 10:27:18'),
(65, 'delete_bank_branch_names', 'bank_branch_names', '2023-09-24 10:27:18', '2023-09-24 10:27:18'),
(66, 'browse_services', 'services', '2023-09-25 02:27:17', '2023-09-25 02:27:17'),
(67, 'read_services', 'services', '2023-09-25 02:27:17', '2023-09-25 02:27:17'),
(68, 'edit_services', 'services', '2023-09-25 02:27:17', '2023-09-25 02:27:17'),
(69, 'add_services', 'services', '2023-09-25 02:27:17', '2023-09-25 02:27:17'),
(70, 'delete_services', 'services', '2023-09-25 02:27:17', '2023-09-25 02:27:17'),
(71, 'browse_forms', 'forms', '2023-09-25 05:17:25', '2023-09-25 05:17:25'),
(72, 'read_forms', 'forms', '2023-09-25 05:17:25', '2023-09-25 05:17:25'),
(73, 'edit_forms', 'forms', '2023-09-25 05:17:25', '2023-09-25 05:17:25'),
(74, 'add_forms', 'forms', '2023-09-25 05:17:25', '2023-09-25 05:17:25'),
(75, 'delete_forms', 'forms', '2023-09-25 05:17:25', '2023-09-25 05:17:25'),
(76, 'browse_operators', 'operators', '2023-09-25 08:01:22', '2023-09-25 08:01:22'),
(77, 'read_operators', 'operators', '2023-09-25 08:01:22', '2023-09-25 08:01:22'),
(78, 'edit_operators', 'operators', '2023-09-25 08:01:22', '2023-09-25 08:01:22'),
(79, 'add_operators', 'operators', '2023-09-25 08:01:22', '2023-09-25 08:01:22'),
(80, 'delete_operators', 'operators', '2023-09-25 08:01:22', '2023-09-25 08:01:22');

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permission_role`
--

INSERT INTO `permission_role` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 1),
(25, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(51, 1),
(52, 1),
(53, 1),
(54, 1),
(55, 1),
(56, 1),
(57, 1),
(58, 1),
(59, 1),
(60, 1),
(61, 1),
(62, 1),
(63, 1),
(64, 1),
(65, 1),
(66, 1),
(67, 1),
(68, 1),
(69, 1),
(70, 1),
(71, 1),
(72, 1),
(73, 1),
(74, 1),
(75, 1),
(76, 1),
(77, 1),
(78, 1),
(79, 1),
(80, 1);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `display_name`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'Administrator', '2023-03-10 03:02:17', '2023-03-10 03:02:17'),
(2, 'Super Admin', 'Super Admin', '2023-03-10 03:02:17', '2023-09-23 09:04:30'),
(3, 'Admins', 'Admins', '2023-09-23 09:05:08', '2023-09-23 09:05:08'),
(4, 'Resellers', 'Resellers', '2023-09-23 09:05:25', '2023-09-23 09:05:25'),
(5, 'Partner', 'Partner', '2023-09-25 04:30:52', '2023-09-25 04:30:52');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `details` text DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `order` int(11) NOT NULL DEFAULT 1,
  `group` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `display_name`, `value`, `details`, `type`, `order`, `group`) VALUES
(1, 'site.title', 'Site Title', 'Site Title', '', 'text', 1, 'Site'),
(2, 'site.description', 'Site Description', 'Site Description', '', 'text', 2, 'Site'),
(3, 'site.logo', 'Site Logo', 'settings\\September2023\\e0Gbn22rvBB1R6yLXXS0.png', '', 'image', 3, 'Site'),
(4, 'site.google_analytics_tracking_id', 'Google Analytics Tracking ID', NULL, '', 'text', 4, 'Site'),
(5, 'admin.bg_image', 'Admin Background Image', '', '', 'image', 5, 'Admin'),
(6, 'admin.title', 'Admin Title', 'Voyager', '', 'text', 1, 'Admin'),
(7, 'admin.description', 'Admin Description', 'Welcome to Voyager. The Missing Admin for Laravel', '', 'text', 2, 'Admin'),
(8, 'admin.loader', 'Admin Loader', '', '', 'image', 3, 'Admin'),
(9, 'admin.icon_image', 'Admin Icon Image', '', '', 'image', 4, 'Admin'),
(10, 'admin.google_analytics_client_id', 'Google Analytics Client ID (used for admin dashboard)', NULL, '', 'text', 1, 'Admin'),
(12, 'site.favicon', 'Favicon', 'settings\\September2023\\mfULYpNv2WTb55MGElPE.png', NULL, 'image', 6, 'Site');

-- --------------------------------------------------------

--
-- Table structure for table `translations`
--

CREATE TABLE `translations` (
  `id` int(10) UNSIGNED NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `column_name` varchar(255) NOT NULL,
  `foreign_key` int(10) UNSIGNED NOT NULL,
  `locale` varchar(255) NOT NULL,
  `value` text NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `avatar` varchar(255) DEFAULT 'users/default.png',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `settings` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `ref_key` varchar(255) DEFAULT NULL,
  `contact_no` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `username`, `email`, `avatar`, `email_verified_at`, `password`, `remember_token`, `settings`, `created_at`, `updated_at`, `status`, `ref_key`, `contact_no`) VALUES
(1, 1, 'Muhammad Junaid', 'jilyas1998', 'junaidilyas1998@gmail.com', 'users/default.png', NULL, '$2a$12$/Me050bOAzLpTQ5wxukwOOywVE8oTl3mjOTr81G6lH0LVg7eN1S22', NULL, NULL, '2023-03-10 03:03:37', '2023-09-25 10:55:27', 1, 'pzsud8y3hpdyj3oj2jk470zzn', '09999999999'),
(2, 2, 'Muhammad Junaid', 'jinu63', 'jinuilyas63@hotmail.com', 'users/default.png', NULL, '$2a$12$9/DV00Fyms/r8FjIsFtTaux/e3s5c/qkDweDut0NtiBJFgEo7BnhG', NULL, '{\"locale\":\"en\"}', '2023-09-23 09:07:05', '2023-09-23 09:07:05', 1, 'zdllbfv222jvlky2g561ovf6x', '03311-362793'),
(5, 3, 'rajab', 'rajab2332', 'rajab@hotmail.com', 'users/default.png', NULL, '$2y$10$jDmzxUa8NHUMBGIKoqbpo.m7n5v3YowwC60O6Ui.OkFn.SjmVpVH6', NULL, NULL, '2023-09-24 03:44:57', '2023-09-24 03:44:57', 1, 'BsRBX7tmOFLjAjOPepR2BNoV4', '123311331'),
(6, 4, 'rajab', 'rajab245', 'rajab@outlook.com', 'users/default.png', NULL, '$2y$10$WGMmIYSALA3h7YzTTrqEtuJ8sWu12vcVjc6efBE6Fjvzc80fUUbte', NULL, NULL, '2023-09-24 03:45:46', '2023-09-24 07:38:09', 1, 'zyCuXPZBqREMsi7dff6foMrvl', '21332312'),
(7, 5, 'Basit', 'basit', 'basit@gmail.com', 'users/default.png', NULL, '$2y$10$7dEd9r9Wl3nvRwZsVld6F.B0UawezsiC0MjM45Bpvi1wShgmEgdR.', NULL, NULL, '2023-09-25 04:59:42', '2023-09-27 13:33:31', 1, 'a5NOgtlauArfjtkZWY8LhRnG7', '2312312132'),
(8, 4, 'Afzal', 'afzalhingoro', 'afzal.hingoro@gmail.com', 'users/default.png', NULL, '$2y$10$7dEd9r9Wl3nvRwZsVld6F.B0UawezsiC0MjM45Bpvi1wShgmEgdR.', NULL, NULL, '2023-09-26 12:40:00', '2023-09-27 10:58:39', 1, '5t8B9Zn4GJuS8ZTdJESeSYyuT', '12321232112'),
(9, 4, 'Muhammad Junaid', 'jinu', 'juniad@gmail.com', 'users/default.png', NULL, '$2y$10$ko8G4y/sbV1AwJOhTtR56eDBdkAnOFb7x1NFbOMuANB.iG03piDKC', NULL, NULL, '2023-09-26 15:48:58', '2023-09-27 13:33:52', 1, 'q1K2hSrXnWUe1t0MJb1DAAdjp', '32131132312'),
(10, 2, 'Azhar Alam', 'azhar455', 'azhar@optimizedbodyandmind.co.uk', 'users/default.png', NULL, '$2y$10$87AO3C9PvuOLLi6I7Cf0ZO391OPJLb4SED3xiXKom2X7yCLHHoBqS', NULL, NULL, '2023-10-16 10:28:24', '2023-10-16 10:28:24', 1, 'qUQMUMeHMWfpbYhbzM6F9B8kw', '32111111111');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--

CREATE TABLE `user_roles` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_rows`
--
ALTER TABLE `data_rows`
  ADD PRIMARY KEY (`id`),
  ADD KEY `data_rows_data_type_id_foreign` (`data_type_id`);

--
-- Indexes for table `data_types`
--
ALTER TABLE `data_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `data_types_name_unique` (`name`),
  ADD UNIQUE KEY `data_types_slug_unique` (`slug`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `menus_name_unique` (`name`);

--
-- Indexes for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_items_menu_id_foreign` (`menu_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `permissions_key_index` (`key`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `permission_role_permission_id_index` (`permission_id`),
  ADD KEY `permission_role_role_id_index` (`role_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `translations_table_name_column_name_foreign_key_locale_unique` (`table_name`,`column_name`,`foreign_key`,`locale`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- Indexes for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD PRIMARY KEY (`user_id`,`role_id`),
  ADD KEY `user_roles_user_id_index` (`user_id`),
  ADD KEY `user_roles_role_id_index` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_rows`
--
ALTER TABLE `data_rows`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `data_types`
--
ALTER TABLE `data_types`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu_items`
--
ALTER TABLE `menu_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `translations`
--
ALTER TABLE `translations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `data_rows`
--
ALTER TABLE `data_rows`
  ADD CONSTRAINT `data_rows_data_type_id_foreign` FOREIGN KEY (`data_type_id`) REFERENCES `data_types` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `menu_items`
--
ALTER TABLE `menu_items`
  ADD CONSTRAINT `menu_items_menu_id_foreign` FOREIGN KEY (`menu_id`) REFERENCES `menus` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`);

--
-- Constraints for table `user_roles`
--
ALTER TABLE `user_roles`
  ADD CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
