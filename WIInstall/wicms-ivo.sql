-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 18, 2026 at 08:47 AM
-- Server version: 8.0.45
-- PHP Version: 8.2.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `wicms-ivo`
--

-- --------------------------------------------------------

--
-- Table structure for table `wi_admin_info_box`
--

CREATE TABLE `wi_admin_info_box` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `info` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_admin_info_box`
--

INSERT INTO `wi_admin_info_box` (`id`, `name`, `info`) VALUES
(1, 'Bounce Rate', 'bounce'),
(2, 'Unique Visitors', 'visitors'),
(3, 'User Registrations', 'regUser');

-- --------------------------------------------------------

--
-- Table structure for table `wi_admin_menu`
--

CREATE TABLE `wi_admin_menu` (
  `id` int NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#',
  `parent` int NOT NULL DEFAULT '0',
  `sort` int DEFAULT NULL,
  `lang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_admin_menu`
--

INSERT INTO `wi_admin_menu` (`id`, `label`, `link`, `parent`, `sort`, `lang`) VALUES
(1, 'Administrators', 'WIAdmin.php', 0, 0, 'admins'),
(2, 'Logs', 'WILogs.php', 0, 1, 'logs'),
(3, 'Visitors', 'WIVisitors.php', 0, 2, 'visitors');

-- --------------------------------------------------------

--
-- Table structure for table `wi_admin_msg`
--

CREATE TABLE `wi_admin_msg` (
  `id` int NOT NULL,
  `msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `wtime` datetime NOT NULL,
  `user_id` int NOT NULL,
  `attachments` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `file` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_admin_todo_list`
--

CREATE TABLE `wi_admin_todo_list` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed` enum('y','n') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_contact_message`
--

CREATE TABLE `wi_contact_message` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_sent` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_css`
--

CREATE TABLE `wi_css` (
  `id` int NOT NULL,
  `href` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_css`
--

INSERT INTO `wi_css` (`id`, `href`, `rel`, `page`) VALUES
(1, 'site/css/frameworks/bootstrap4.css', 'stylesheet', 'index'),
(2, 'site/css/login_panel/css/slide.css', 'stylesheet', 'index'),
(3, 'site/css/frameworks/menus.css', 'stylesheet', 'index'),
(4, 'site/css/style.css', 'stylesheet', 'index'),
(5, 'site/css/font-awesome.css', 'stylesheet', 'index'),
(6, 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'index'),
(7, 'site/css/frameworks/bootstrap.css', 'stylesheet', 'alogin'),
(8, 'site/css/login_panel/css/slide.css', 'stylesheet', 'alogin'),
(9, 'site/css/frameworks/menus.css', 'stylesheet', 'alogin'),
(10, 'site/css/style.css', 'stylesheet', 'alogin'),
(11, 'site/css/font-awesome.css', 'stylesheet', 'alogin'),
(12, 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'alogin'),
(13, 'site/css/frameworks/bootstrap.css', 'stylesheet', 'confirm'),
(14, 'site/css/login_panel/css/slide.css', 'stylesheet', 'confirm'),
(15, 'site/css/frameworks/menus.css', 'stylesheet', 'confirm'),
(16, 'site/css/style.css', 'stylesheet', 'confirm'),
(17, 'site/css/font-awesome.css', 'stylesheet', 'confirm'),
(18, 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'confirm'),
(19, 'site/css/frameworks/bootstrap.css', 'stylesheet', 'contact_us'),
(20, 'site/css/login_panel/css/slide.css', 'stylesheet', 'contact_us'),
(21, 'site/css/frameworks/menus.css', 'stylesheet', 'contact_us'),
(22, 'site/css/style.css', 'stylesheet', 'contact_us'),
(23, 'site/css/font-awesome.css', 'stylesheet', 'contact_us'),
(24, 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'contact_us'),
(25, 'site/css/frameworks/bootstrap.css', 'stylesheet', 'profile'),
(26, 'site/css/login_panel/css/slide.css', 'stylesheet', 'profile'),
(27, 'site/css/frameworks/menus.css', 'stylesheet', 'profile'),
(28, 'site/css/style.css', 'stylesheet', 'profile'),
(29, 'site/css/font-awesome.css', 'stylesheet', 'profile'),
(30, 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'profile'),
(31, 'user/css/profile.css', 'stylesheet', 'profile'),
(32, 'site/css/frameworks/bootstrap.css', 'stylesheet', 'passwordreset'),
(33, 'site/css/login_panel/css/slide.css', 'stylesheet', 'passwordreset'),
(34, 'site/css/frameworks/menus.css', 'stylesheet', 'passwordreset'),
(35, 'site/css/style.css', 'stylesheet', 'passwordreset'),
(36, 'site/css/font-awesome.css', 'stylesheet', 'passwordreset'),
(37, 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'passwordreset'),
(38, 'site/css/frameworks/bootstrap.css', 'stylesheet', 'about_us'),
(39, 'site/css/login_panel/css/slide.css', 'stylesheet', 'about_us'),
(40, 'site/css/frameworks/menus.css', 'stylesheet', 'about_us'),
(41, 'site/css/style.css', 'stylesheet', 'about_us'),
(42, 'site/css/font-awesome.css', 'stylesheet', 'about_us'),
(43, 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'about_us');

-- --------------------------------------------------------

--
-- Table structure for table `wi_elements`
--

CREATE TABLE `wi_elements` (
  `element_id` int NOT NULL,
  `element_status` enum('enabled','disabled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disabled',
  `element_powered` enum('power_on','power_off') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'power_off',
  `element_type` enum('Common Fields','HTML Elements','Layout') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Common Fields',
  `element_author` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `element_name` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `element_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `element_font` varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_footer`
--

CREATE TABLE `wi_footer` (
  `footer_id` int NOT NULL,
  `footer_content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `footer_linking` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `website_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_footer`
--

INSERT INTO `wi_footer` (`footer_id`, `footer_content`, `footer_linking`, `website_name`) VALUES
(1, '', '', 'WICMS');

-- --------------------------------------------------------

--
-- Table structure for table `wi_header`
--

CREATE TABLE `wi_header` (
  `header_id` int NOT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bk_header_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_slogan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_header`
--

INSERT INTO `wi_header` (`header_id`, `logo`, `bk_header_image`, `header_image`, `header_content`, `header_slogan`) VALUES
(1, 'wicms-logo.png', '', 'bk_header', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `wi_lang`
--

CREATE TABLE `wi_lang` (
  `id` int NOT NULL,
  `lang` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lang_flag` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `href` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_lang`
--

INSERT INTO `wi_lang` (`id`, `lang`, `name`, `lang_flag`, `href`) VALUES
(1, 'en', 'English', 'en.png', '?lang=en'),
(2, 'rs', 'Serbian', 'rs.png', '?lang=rs'),
(3, 'ru', 'Russian', 'ru.png', '?lang=ru'),
(4, 'es', 'Spanish', 'es.png', '?lang=es'),
(5, 'fr', 'French', 'fr.png', '?lang=fr'),
(6, 'cn', 'China', 'cn.png', '?lang=cn');

-- --------------------------------------------------------

--
-- Table structure for table `wi_login_attempts`
--

CREATE TABLE `wi_login_attempts` (
  `id_login_attempts` int NOT NULL,
  `ip_addr` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempt_number` int NOT NULL DEFAULT '1',
  `date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_login_attempts`
--

INSERT INTO `wi_login_attempts` (`id_login_attempts`, `ip_addr`, `attempt_number`, `date`) VALUES
(1, '::1', 1, '2026-03-16');

-- --------------------------------------------------------

--
-- Table structure for table `wi_logs`
--

CREATE TABLE `wi_logs` (
  `ID` int NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `opperation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_logs`
--

INSERT INTO `wi_logs` (`ID`, `date`, `user`, `opperation`) VALUES
(1, '2026-03-16 01:03:25', 'admin_warner', 'Added new user'),
(2, '2026-03-16 01:05:16', 'admin_warner', 'Successfully logged in user'),
(3, '2026-03-16 01:07:04', 'admin_warner', 'Successfully logged in user'),
(4, '2026-03-16 15:01:44', 'admin_warner', 'Successfully logged in user'),
(5, '2026-03-17 12:10:26', 'admin_warner', 'Successfully logged in user'),
(6, '2026-03-17 23:04:27', 'admin_warner', 'Successfully logged in user');

-- --------------------------------------------------------

--
-- Table structure for table `wi_media`
--

CREATE TABLE `wi_media` (
  `id` int UNSIGNED NOT NULL,
  `uuid` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caption` text COLLATE utf8mb4_unicode_ci,
  `description` text COLLATE utf8mb4_unicode_ci,
  `original_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stored_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folder` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_type` enum('image','video','audio','document','other') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `file_size` bigint UNSIGNED NOT NULL DEFAULT '0',
  `width` int UNSIGNED DEFAULT NULL,
  `height` int UNSIGNED DEFAULT NULL,
  `duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `uploaded_by` int UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_members`
--

CREATE TABLE `wi_members` (
  `user_id` int NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `confirmation_key` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `confirmed` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `password_reset_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password_reset_confirmed` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `password_reset_timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `register_date` date NOT NULL,
  `user_role` int NOT NULL DEFAULT '1',
  `last_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_addr` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `banned` enum('Y','N') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_members`
--

INSERT INTO `wi_members` (`user_id`, `email`, `username`, `password`, `confirmation_key`, `confirmed`, `password_reset_key`, `password_reset_confirmed`, `password_reset_timestamp`, `register_date`, `user_role`, `last_login`, `ip_addr`, `banned`) VALUES
(1, 'warner23@hotmail.com', 'admin_warner', '$2y$12$SHoVv385asvEjPS9gLOe5OILttjE9CDPFKQP1QZK0eMCOtO79us3y', 'd10780fed6e9860b6faa0aa6ed77bd0f685b4d959068d3cb0d7f5b0c18a0f6fa', 'Y', '', 'N', '2026-03-16 01:03:25', '2026-03-16', 7, '2026-03-17 23:04:26', '::1', 'N');

-- --------------------------------------------------------

--
-- Table structure for table `wi_menu`
--

CREATE TABLE `wi_menu` (
  `id` int NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#',
  `parent` int NOT NULL DEFAULT '0',
  `sort` int DEFAULT NULL,
  `lang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_menu`
--

INSERT INTO `wi_menu` (`id`, `label`, `link`, `parent`, `sort`, `lang`) VALUES
(1, 'Home', 'index.php', 0, 0, 'home'),
(2, 'About', 'about_us.php', 0, 1, 'about'),
(3, 'Contact', 'contact_us.php', 0, 2, 'contact');

-- --------------------------------------------------------

--
-- Table structure for table `wi_meta`
--

CREATE TABLE `wi_meta` (
  `meta_id` int NOT NULL,
  `page` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_meta`
--

INSERT INTO `wi_meta` (`meta_id`, `page`, `name`, `content`, `author`) VALUES
(1, 'index', 'viewport', 'width=device-width, initial-scale=1', 'WICMS'),
(2, 'index', 'description', 'Warner-Infinity Content Management System with simplified back end', 'WICMS'),
(3, 'index', 'keywords', 'WI, WICMS, CMS, UI', 'WICMS'),
(4, 'index', 'author', 'warner-infinity', 'WICMS'),
(5, 'alogin', 'viewport', 'width=device-width, initial-scale=1', 'WICMS'),
(6, 'alogin', 'description', 'Warner-Infinity Content Management System', 'WICMS'),
(7, 'alogin', 'keywords', 'WI, WICMS, login', 'WICMS'),
(8, 'alogin', 'author', 'warner-infinity', 'WICMS'),
(9, 'confirm', 'viewport', 'width=device-width, initial-scale=1', 'WICMS'),
(10, 'confirm', 'description', 'Email confirmation page', 'WICMS'),
(11, 'confirm', 'keywords', 'WI, WICMS, confirm', 'WICMS'),
(12, 'confirm', 'author', 'warner-infinity', 'WICMS'),
(13, 'contact_us', 'viewport', 'width=device-width, initial-scale=1', 'WICMS'),
(14, 'contact_us', 'description', 'Contact page', 'WICMS'),
(15, 'contact_us', 'keywords', 'WI, WICMS, contact', 'WICMS'),
(16, 'contact_us', 'author', 'warner-infinity', 'WICMS'),
(17, 'profile', 'viewport', 'width=device-width, initial-scale=1', 'WICMS'),
(18, 'profile', 'description', 'Profile page', 'WICMS'),
(19, 'profile', 'keywords', 'WI, WICMS, profile', 'WICMS'),
(20, 'profile', 'author', 'warner-infinity', 'WICMS'),
(21, 'passwordreset', 'viewport', 'width=device-width, initial-scale=1', 'WICMS'),
(22, 'passwordreset', 'description', 'Password reset page', 'WICMS'),
(23, 'passwordreset', 'keywords', 'WI, WICMS, reset', 'WICMS'),
(24, 'passwordreset', 'author', 'warner-infinity', 'WICMS'),
(25, 'about_us', 'viewport', 'width=device-width, initial-scale=1', 'WICMS'),
(26, 'about_us', 'description', 'About page', 'WICMS'),
(27, 'about_us', 'keywords', 'WI, WICMS, about', 'WICMS'),
(28, 'about_us', 'author', 'warner-infinity', 'WICMS');

-- --------------------------------------------------------

--
-- Table structure for table `wi_migrations`
--

CREATE TABLE `wi_migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `migration_key` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_no` int NOT NULL DEFAULT '1',
  `applied_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_mod`
--

CREATE TABLE `wi_mod` (
  `mod_id` int NOT NULL,
  `mod_status` enum('enabled','disabled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disabled',
  `mod_powered` enum('power_on','power_off') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'power_off',
  `mod_type` enum('element','custom') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'element',
  `mod_author` varchar(55) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_name` varchar(55) COLLATE utf8mb4_unicode_ci NOT NULL,
  `Mod_description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mod_font` varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_modules`
--

CREATE TABLE `wi_modules` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mod_id` int DEFAULT NULL,
  `trans` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img1` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans4` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img2` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text5` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans5` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text6` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans6` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_notifications`
--

CREATE TABLE `wi_notifications` (
  `ID` int NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `opperation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_page`
--

CREATE TABLE `wi_page` (
  `id` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `panel` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `top_head` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `header` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `left_sidebar` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `right_sidebar` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `contents` mediumtext COLLATE utf8mb4_unicode_ci,
  `footer` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_page`
--

INSERT INTO `wi_page` (`id`, `name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`) VALUES
(1, 'alogin', '1', '1', '0', '0', '0', 'alogin', '1'),
(2, 'confirm', '1', '1', '0', '0', '0', 'confirm', '1'),
(3, 'index', '1', '1', '1', '0', '0', 'welcome_box', '1'),
(4, 'passwordreset', '1', '1', '0', '0', '0', 'passwordreset', '1'),
(5, 'profile', '1', '1', '0', '0', '0', 'profile', '1'),
(6, 'contact_us', '1', '1', '0', '0', '0', 'contact_us', '1'),
(7, 'about_us', '1', '0', '0', '0', '0', 'about_us', '1');

-- --------------------------------------------------------

--
-- Table structure for table `wi_permissions`
--

CREATE TABLE `wi_permissions` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_permissions`
--

INSERT INTO `wi_permissions` (`id`, `name`, `code`, `group_name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'View Users', 'users.view', 'Users', 'Can view users list', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(2, 'Create Users', 'users.create', 'Users', 'Can create new users', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(3, 'Edit Users', 'users.edit', 'Users', 'Can edit existing users', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(4, 'Delete Users', 'users.delete', 'Users', 'Can delete users', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(5, 'View Roles', 'roles.view', 'Roles', 'Can view roles list', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(6, 'Create Roles', 'roles.create', 'Roles', 'Can create new roles', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(7, 'Edit Roles', 'roles.edit', 'Roles', 'Can edit existing roles', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(8, 'Delete Roles', 'roles.delete', 'Roles', 'Can delete roles', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(9, 'View Permissions', 'permissions.view', 'Permissions', 'Can view permissions list', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(10, 'Create Permissions', 'permissions.create', 'Permissions', 'Can create new permissions', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(11, 'Edit Permissions', 'permissions.edit', 'Permissions', 'Can edit existing permissions', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(12, 'Delete Permissions', 'permissions.delete', 'Permissions', 'Can delete permissions', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(13, 'View Settings', 'settings.view', 'Settings', 'Can view settings', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(14, 'Edit Settings', 'settings.edit', 'Settings', 'Can edit settings', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(15, 'View Pages', 'pages.view', 'Pages', 'Can view pages', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(16, 'Create Pages', 'pages.create', 'Pages', 'Can create pages', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(17, 'Edit Pages', 'pages.edit', 'Pages', 'Can edit pages', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(18, 'Delete Pages', 'pages.delete', 'Pages', 'Can delete pages', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(19, 'View Media', 'media.view', 'Media', 'Media library access', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(20, 'Upload Media', 'media.upload', 'Media', 'Can upload media', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15'),
(21, 'Delete Media', 'media.delete', 'Media', 'Can delete media', 1, '2026-03-16 19:24:15', '2026-03-16 19:24:15');

-- --------------------------------------------------------

--
-- Table structure for table `wi_plugin`
--

CREATE TABLE `wi_plugin` (
  `plugin_id` int NOT NULL,
  `plugin_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `plugin_slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `plugin_version` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `plugin_author` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plugin_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `plugin_status` enum('enabled','disabled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'disabled',
  `plugin_installed` datetime DEFAULT CURRENT_TIMESTAMP,
  `plugin_updated` datetime DEFAULT NULL,
  `plugin_license_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_plugin_customers`
--

CREATE TABLE `wi_plugin_customers` (
  `customer_id` int NOT NULL,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_plugin_download_logs`
--

CREATE TABLE `wi_plugin_download_logs` (
  `log_id` int NOT NULL,
  `license_key` varchar(255) NOT NULL,
  `plugin_slug` varchar(100) NOT NULL,
  `site_url` varchar(255) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `downloaded_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_plugin_invoices`
--

CREATE TABLE `wi_plugin_invoices` (
  `invoice_id` int NOT NULL,
  `order_id` int NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `invoice_total` decimal(10,2) NOT NULL,
  `invoice_currency` varchar(10) DEFAULT 'GBP',
  `invoice_status` enum('paid','pending','cancelled') DEFAULT 'pending',
  `invoice_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_plugin_licenses`
--

CREATE TABLE `wi_plugin_licenses` (
  `license_id` int NOT NULL,
  `user_id` int NOT NULL,
  `plugin_slug` varchar(100) NOT NULL,
  `license_key` varchar(255) NOT NULL,
  `license_type` enum('lifetime','subscription') DEFAULT 'lifetime',
  `license_status` enum('active','expired','revoked') DEFAULT 'active',
  `license_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `license_expires` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_plugin_orders`
--

CREATE TABLE `wi_plugin_orders` (
  `order_id` int NOT NULL,
  `user_id` int NOT NULL,
  `plugin_slug` varchar(100) NOT NULL,
  `order_price` decimal(10,2) NOT NULL,
  `order_currency` varchar(10) DEFAULT 'GBP',
  `order_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_gateway` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_plugin_store`
--

CREATE TABLE `wi_plugin_store` (
  `store_id` int NOT NULL,
  `plugin_slug` varchar(100) NOT NULL,
  `plugin_name` varchar(100) NOT NULL,
  `plugin_description` text,
  `plugin_version` varchar(20) DEFAULT NULL,
  `plugin_price` decimal(10,2) DEFAULT '0.00',
  `plugin_currency` varchar(10) DEFAULT 'GBP',
  `plugin_subscription` tinyint(1) DEFAULT '0',
  `plugin_preview` varchar(255) DEFAULT NULL,
  `plugin_download` varchar(255) DEFAULT NULL,
  `plugin_author` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_plugin_subscriptions`
--

CREATE TABLE `wi_plugin_subscriptions` (
  `subscription_id` int NOT NULL,
  `user_id` int NOT NULL,
  `plugin_slug` varchar(100) NOT NULL,
  `subscription_price` decimal(10,2) NOT NULL,
  `subscription_currency` varchar(10) DEFAULT 'GBP',
  `subscription_period` enum('monthly','yearly') DEFAULT 'yearly',
  `subscription_status` enum('active','cancelled','expired') DEFAULT 'active',
  `subscription_start` datetime DEFAULT CURRENT_TIMESTAMP,
  `subscription_end` datetime DEFAULT NULL,
  `gateway_subscription_id` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_plugin_updates`
--

CREATE TABLE `wi_plugin_updates` (
  `update_id` int NOT NULL,
  `plugin_slug` varchar(100) NOT NULL,
  `version` varchar(20) NOT NULL,
  `download_url` varchar(255) NOT NULL,
  `changelog` text,
  `requires_license` tinyint(1) DEFAULT '1',
  `released_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_role_permissions`
--

CREATE TABLE `wi_role_permissions` (
  `id` int UNSIGNED NOT NULL,
  `role_id` int UNSIGNED NOT NULL,
  `permission_id` int UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_scripts`
--

CREATE TABLE `wi_scripts` (
  `id` int NOT NULL,
  `src` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `page` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_scripts`
--

INSERT INTO `wi_scripts` (`id`, `src`, `page`) VALUES
(1, 'site/js/frameworks/JQuery.js', 'index'),
(2, 'site/js/frameworks/bootstrap.js', 'index'),
(3, 'site/js/login_panel/js/slide.js', 'index'),
(4, 'site/js/frameworks/JQuery.js', 'alogin'),
(5, 'site/js/frameworks/bootstrap.js', 'alogin'),
(6, 'site/js/login_panel/js/slide.js', 'alogin'),
(7, 'site/js/frameworks/JQuery.js', 'confirm'),
(8, 'site/js/frameworks/bootstrap.js', 'confirm'),
(9, 'site/js/login_panel/js/slide.js', 'confirm'),
(10, 'site/js/frameworks/JQuery.js', 'contact_us'),
(11, 'site/js/frameworks/bootstrap.js', 'contact_us'),
(12, 'site/js/login_panel/js/slide.js', 'contact_us'),
(13, 'site/js/frameworks/JQuery.js', 'profile'),
(14, 'site/js/frameworks/bootstrap.js', 'profile'),
(15, 'site/js/login_panel/js/slide.js', 'profile'),
(16, 'site/js/frameworks/JQuery.js', 'passwordreset'),
(17, 'site/js/frameworks/bootstrap.js', 'passwordreset'),
(18, 'site/js/login_panel/js/slide.js', 'passwordreset'),
(19, 'site/js/frameworks/JQuery.js', 'about_us'),
(20, 'site/js/frameworks/bootstrap.js', 'about_us'),
(21, 'site/js/login_panel/js/slide.js', 'about_us');

-- --------------------------------------------------------

--
-- Table structure for table `wi_secure_secrets`
--

CREATE TABLE `wi_secure_secrets` (
  `id` bigint UNSIGNED NOT NULL,
  `scope` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_key` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cipher_text` mediumblob NOT NULL,
  `blind_index` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key_version` smallint UNSIGNED NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_sidebar`
--

CREATE TABLE `wi_sidebar` (
  `id` int NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#',
  `parent` int NOT NULL DEFAULT '0',
  `sort` int DEFAULT NULL,
  `lang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_sidebar`
--

INSERT INTO `wi_sidebar` (`id`, `label`, `link`, `parent`, `sort`, `lang`, `img`) VALUES
(1, 'Settings', '', 0, 0, 'settings', 'settings'),
(2, 'Site', 'WISite.php', 1, 0, 'Site', 'site'),
(3, 'Users', '', 0, 1, 'Users', 'users'),
(4, 'Manage User', 'WIUser.php', 3, 0, 'Manage_users', 'manage users'),
(5, 'Roles', 'WIRoles.php', 3, 1, 'roles', 'roles'),
(6, 'Menus', 'WIMenu.php', 1, 1, 'Menu', 'menu'),
(7, 'Header', 'WIHeader.php', 1, 2, 'Header', 'header'),
(8, 'Modules', '', 0, 2, 'Modules', 'modules'),
(9, 'Modules', 'WIModules.php', 8, 0, 'Modules', 'modules'),
(10, 'Pages', '', 0, 3, 'Pages', 'pages'),
(11, 'Pages', 'WIPages.php', 10, 0, 'Pages', 'pages'),
(12, 'Plugins', '', 0, 4, 'Plugins', 'plugin'),
(13, 'plugin', 'WIPlugin.php', 12, 0, 'Plugin', 'plugin'),
(14, 'Styling', 'WIStyling.php', 1, 3, 'Styling', 'styling'),
(15, 'Media', '', 0, 5, 'media', 'media'),
(16, 'Media', 'WIMedia.php', 15, 0, 'media', 'media'),
(17, 'Multi Lang', 'WIMlang.php', 1, 4, 'Multi Lang', 'multi_lang'),
(18, 'Permissions', 'WIPermissions.php', 3, 2, 'permissions', 'permissions');

-- --------------------------------------------------------

--
-- Table structure for table `wi_site`
--

CREATE TABLE `wi_site` (
  `id` int NOT NULL,
  `site_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_domain` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_host` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db_username` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db_pass` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db_port` int NOT NULL DEFAULT '3306',
  `db_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mysql',
  `secure_session` enum('false','true') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `http_only` enum('false','true') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'true',
  `regenerate_id` enum('false','true') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'true',
  `use_only_cookie` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `login_fingerprint` enum('true','false') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `max_login_attempts` int NOT NULL DEFAULT '5',
  `redirect_after_login` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'WIMembers/profile.php',
  `password_encryption` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bcrypt',
  `encryption_cost` int NOT NULL DEFAULT '12',
  `sha512_iterations` int NOT NULL DEFAULT '35000',
  `password_salt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `reset_key_life` int NOT NULL DEFAULT '24',
  `mail_confirm_required` enum('true','false') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `register_confirm` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirm',
  `reg_pass_reset` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'passwordreset',
  `mailer` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mailer',
  `smpt_host` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `smpt_port` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `smpt_username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `smpt_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `smpt_encryption` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `social_callback_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'socialauth',
  `google_enabled` enum('true','false') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_map_api` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `google_charts_api_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `facebook_enabled` enum('true','false') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `facebook_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_enabled` enum('true','false') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `twitter_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_secret` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_lang` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `multi_lang` enum('on','off') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `lang_choice` enum('google','wilang') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'google',
  `bootstrap_version` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '4',
  `wicms_version` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IVO-1.0',
  `left_sidebar` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `right_sidebar` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `contact_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_site`
--

INSERT INTO `wi_site` (`id`, `site_name`, `site_domain`, `site_url`, `favicon`, `db_host`, `db_username`, `db_pass`, `db_name`, `db_port`, `db_type`, `secure_session`, `http_only`, `regenerate_id`, `use_only_cookie`, `login_fingerprint`, `max_login_attempts`, `redirect_after_login`, `password_encryption`, `encryption_cost`, `sha512_iterations`, `password_salt`, `reset_key_life`, `mail_confirm_required`, `register_confirm`, `reg_pass_reset`, `mailer`, `smpt_host`, `smpt_port`, `smpt_username`, `smpt_password`, `smpt_encryption`, `social_callback_url`, `google_enabled`, `google_id`, `google_secret`, `google_map_api`, `google_charts_api_key`, `facebook_enabled`, `facebook_id`, `facebook_secret`, `twitter_enabled`, `twitter_key`, `twitter_secret`, `default_lang`, `multi_lang`, `lang_choice`, `bootstrap_version`, `wicms_version`, `left_sidebar`, `right_sidebar`, `contact_email`) VALUES
(1, 'WICMS', 'localhost', 'http://localhost', 'favicon.ico', 'localhost', 'wicms-ivo', 't4yl0r22??@@1', 'wicms-ivo', 3306, 'mysql', 'true', 'true', 'true', '1', 'false', 5, 'WIMembers/profile.php', 'bcrypt', 12, 35000, '', 24, 'false', 'confirm', 'passwordreset', 'mailer', '', '', '', '', '', 'socialauth', 'false', NULL, NULL, '', '', 'false', NULL, NULL, 'false', NULL, NULL, 'en', 'on', 'google', '4', 'IVO-1.0', '0', '0', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `wi_slideshow`
--

CREATE TABLE `wi_slideshow` (
  `id` int NOT NULL,
  `no_slides` int NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_slideshow`
--

INSERT INTO `wi_slideshow` (`id`, `no_slides`, `name`) VALUES
(1, 0, 'default');

-- --------------------------------------------------------

--
-- Table structure for table `wi_slideshow_slides`
--

CREATE TABLE `wi_slideshow_slides` (
  `id` int NOT NULL,
  `slide_id` int NOT NULL,
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-target` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-slotamount` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-masterspeed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-lazyload` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-transition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-slide-too` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-bgfit` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-bgposition` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-bgrepeat` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-x` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-y` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-speed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-start` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-easing` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-endspeed` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-endeasing` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_desc` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `href` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `button` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `clas` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_social`
--

CREATE TABLE `wi_social` (
  `id` int NOT NULL,
  `href` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_social_logins`
--

CREATE TABLE `wi_social_logins` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `provider` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'email',
  `provider_id` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_system_versions`
--

CREATE TABLE `wi_system_versions` (
  `id` bigint UNSIGNED NOT NULL,
  `component` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `applied_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_system_versions`
--

INSERT INTO `wi_system_versions` (`id`, `component`, `version`, `applied_at`) VALUES
(1, 'wicms-ivo', '1.0.0', '2026-03-16 00:49:19');

-- --------------------------------------------------------

--
-- Table structure for table `wi_tasks`
--

CREATE TABLE `wi_tasks` (
  `id` int NOT NULL,
  `item` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `percent` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_theme`
--

CREATE TABLE `wi_theme` (
  `id` int NOT NULL,
  `theme` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `in_use` int NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_theme`
--

INSERT INTO `wi_theme` (`id`, `theme`, `destination`, `in_use`) VALUES
(1, 'WICMS', 'WITheme/WICMS/', 1),
(2, 'Galaxy', 'WITheme/Galaxy/', 0);

-- --------------------------------------------------------

--
-- Table structure for table `wi_track`
--

CREATE TABLE `wi_track` (
  `id` int NOT NULL,
  `ref` varchar(250) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(45) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tracking_page_code` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_page_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_count` int NOT NULL DEFAULT '0',
  `dt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_trans`
--

CREATE TABLE `wi_trans` (
  `id` int NOT NULL,
  `lang` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `translation` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_trans`
--

INSERT INTO `wi_trans` (`id`, `lang`, `keyword`, `translation`) VALUES
(1, 'en', 'site_name', 'WICMS'),
(2, 'en', 'home', 'Home'),
(3, 'en', 'users', 'Users'),
(4, 'en', 'blog', 'Blog'),
(5, 'en', 'shop', 'Shop'),
(6, 'en', 'email', 'Email'),
(7, 'en', 'login', 'Login'),
(8, 'en', 'username', 'Username'),
(9, 'en', 'password', 'Password'),
(10, 'en', 'your_email', 'Your Email'),
(11, 'en', 'login_with', 'Login with'),
(12, 'en', 'email_confirmed', 'Email confirmed'),
(13, 'en', 'create_account', 'Create Account'),
(14, 'en', 'logging_in', 'Logging In'),
(15, 'en', 'working', 'Working...'),
(16, 'en', 'info', 'Info'),
(17, 'en', 'admin', 'Admin'),
(18, 'en', 'add_user', 'Add User'),
(19, 'en', 'action', 'Action'),
(20, 'en', 'register_date', 'Register Date'),
(21, 'en', 'forgot_password', 'Forget password'),
(22, 'en', 'repeat_password', 'Repeat password'),
(23, 'en', 'reset_password', 'Reset password'),
(24, 'en', 'email_confirmation', 'Email Confirmation'),
(25, 'en', 'you_can_login_now', 'You can <a href=\"{link}\">log in</a> now.'),
(26, 'en', 'my_profile', 'My Profile'),
(27, 'en', 'admin_panel', 'Admin Panel'),
(28, 'en', 'member_panel', 'Member Panel'),
(29, 'en', 'contact_us', 'Contact Us'),
(30, 'en', 'about_us', 'About Us'),
(31, 'en', 'admins', 'Administrator'),
(32, 'en', 'logs', 'Logs'),
(33, 'en', 'visitors', 'Visitors');

-- --------------------------------------------------------

--
-- Table structure for table `wi_user_details`
--

CREATE TABLE `wi_user_details` (
  `id_user_details` int NOT NULL,
  `user_id` int NOT NULL,
  `first_name` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(35) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio_body` text COLLATE utf8mb4_unicode_ci,
  `website` varchar(225) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `friend_array` mediumtext COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Available','in_chat') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Available'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_user_details`
--

INSERT INTO `wi_user_details` (`id_user_details`, `user_id`, `first_name`, `last_name`, `phone`, `address`, `country`, `region`, `city`, `bio_body`, `website`, `youtube`, `facebook`, `twitter`, `friend_array`, `avatar`, `status`) VALUES
(1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Available');

-- --------------------------------------------------------

--
-- Table structure for table `wi_user_roles`
--

CREATE TABLE `wi_user_roles` (
  `role_id` int NOT NULL,
  `role` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `wi_user_roles`
--

INSERT INTO `wi_user_roles` (`role_id`, `role`) VALUES
(5, 'Administrator'),
(4, 'Developer'),
(6, 'Head Administrator'),
(3, 'Moderator'),
(7, 'Owner'),
(1, 'User'),
(2, 'VIP');

-- --------------------------------------------------------

--
-- Table structure for table `wi_visitors_log`
--

CREATE TABLE `wi_visitors_log` (
  `id` int NOT NULL,
  `page` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `wi_admin_info_box`
--
ALTER TABLE `wi_admin_info_box`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_admin_menu`
--
ALTER TABLE `wi_admin_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wi_admin_menu_parent_sort` (`parent`,`sort`),
  ADD KEY `idx_wi_admin_menu_lang` (`lang`);

--
-- Indexes for table `wi_admin_todo_list`
--
ALTER TABLE `wi_admin_todo_list`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wi_admin_todo_completed` (`completed`);

--
-- Indexes for table `wi_contact_message`
--
ALTER TABLE `wi_contact_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wi_contact_message_email` (`email`),
  ADD KEY `idx_wi_contact_message_time_sent` (`time_sent`);

--
-- Indexes for table `wi_css`
--
ALTER TABLE `wi_css`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wi_css_page` (`page`);

--
-- Indexes for table `wi_elements`
--
ALTER TABLE `wi_elements`
  ADD PRIMARY KEY (`element_id`),
  ADD KEY `idx_wi_elements_name` (`element_name`);

--
-- Indexes for table `wi_footer`
--
ALTER TABLE `wi_footer`
  ADD PRIMARY KEY (`footer_id`);

--
-- Indexes for table `wi_header`
--
ALTER TABLE `wi_header`
  ADD PRIMARY KEY (`header_id`);

--
-- Indexes for table `wi_lang`
--
ALTER TABLE `wi_lang`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_wi_lang_lang` (`lang`);

--
-- Indexes for table `wi_login_attempts`
--
ALTER TABLE `wi_login_attempts`
  ADD PRIMARY KEY (`id_login_attempts`),
  ADD KEY `idx_wi_login_attempts_ip_date` (`ip_addr`,`date`);

--
-- Indexes for table `wi_logs`
--
ALTER TABLE `wi_logs`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_wi_logs_date` (`date`),
  ADD KEY `idx_wi_logs_user` (`user`);

--
-- Indexes for table `wi_media`
--
ALTER TABLE `wi_media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`),
  ADD KEY `idx_media_type` (`media_type`),
  ADD KEY `idx_folder` (`folder`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`),
  ADD KEY `idx_status` (`status`);

--
-- Indexes for table `wi_members`
--
ALTER TABLE `wi_members`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `uk_wi_members_username` (`username`),
  ADD UNIQUE KEY `uk_wi_members_email` (`email`),
  ADD KEY `idx_wi_members_role` (`user_role`),
  ADD KEY `idx_wi_members_confirmed` (`confirmed`);

--
-- Indexes for table `wi_menu`
--
ALTER TABLE `wi_menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wi_menu_parent_sort` (`parent`,`sort`),
  ADD KEY `idx_wi_menu_lang` (`lang`);

--
-- Indexes for table `wi_meta`
--
ALTER TABLE `wi_meta`
  ADD PRIMARY KEY (`meta_id`),
  ADD KEY `idx_wi_meta_page` (`page`),
  ADD KEY `idx_wi_meta_page_name` (`page`,`name`);

--
-- Indexes for table `wi_migrations`
--
ALTER TABLE `wi_migrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_wi_migrations_key` (`migration_key`);

--
-- Indexes for table `wi_mod`
--
ALTER TABLE `wi_mod`
  ADD PRIMARY KEY (`mod_id`),
  ADD KEY `idx_wi_mod_module_name` (`module_name`);

--
-- Indexes for table `wi_modules`
--
ALTER TABLE `wi_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wi_modules_mod_id` (`mod_id`);

--
-- Indexes for table `wi_notifications`
--
ALTER TABLE `wi_notifications`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `idx_wi_notifications_date` (`date`),
  ADD KEY `idx_wi_notifications_user` (`user`);

--
-- Indexes for table `wi_page`
--
ALTER TABLE `wi_page`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_wi_page_name` (`name`);

--
-- Indexes for table `wi_permissions`
--
ALTER TABLE `wi_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_wi_permissions_code` (`code`),
  ADD KEY `idx_wi_permissions_group` (`group_name`),
  ADD KEY `idx_wi_permissions_active` (`is_active`);

--
-- Indexes for table `wi_plugin`
--
ALTER TABLE `wi_plugin`
  ADD PRIMARY KEY (`plugin_id`),
  ADD UNIQUE KEY `plugin_slug` (`plugin_slug`);

--
-- Indexes for table `wi_plugin_customers`
--
ALTER TABLE `wi_plugin_customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `wi_plugin_download_logs`
--
ALTER TABLE `wi_plugin_download_logs`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `wi_plugin_invoices`
--
ALTER TABLE `wi_plugin_invoices`
  ADD PRIMARY KEY (`invoice_id`);

--
-- Indexes for table `wi_plugin_licenses`
--
ALTER TABLE `wi_plugin_licenses`
  ADD PRIMARY KEY (`license_id`),
  ADD UNIQUE KEY `license_key` (`license_key`);

--
-- Indexes for table `wi_plugin_orders`
--
ALTER TABLE `wi_plugin_orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `wi_plugin_store`
--
ALTER TABLE `wi_plugin_store`
  ADD PRIMARY KEY (`store_id`);

--
-- Indexes for table `wi_plugin_subscriptions`
--
ALTER TABLE `wi_plugin_subscriptions`
  ADD PRIMARY KEY (`subscription_id`);

--
-- Indexes for table `wi_plugin_updates`
--
ALTER TABLE `wi_plugin_updates`
  ADD PRIMARY KEY (`update_id`);

--
-- Indexes for table `wi_role_permissions`
--
ALTER TABLE `wi_role_permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_wi_role_permissions` (`role_id`,`permission_id`),
  ADD KEY `idx_wi_role_permissions_role` (`role_id`),
  ADD KEY `idx_wi_role_permissions_permission` (`permission_id`);

--
-- Indexes for table `wi_scripts`
--
ALTER TABLE `wi_scripts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wi_scripts_page` (`page`);

--
-- Indexes for table `wi_secure_secrets`
--
ALTER TABLE `wi_secure_secrets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_wi_secure_secrets_scope_key` (`scope`,`item_key`),
  ADD KEY `idx_wi_secure_secrets_blind_index` (`blind_index`);

--
-- Indexes for table `wi_sidebar`
--
ALTER TABLE `wi_sidebar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wi_sidebar_parent_sort` (`parent`,`sort`),
  ADD KEY `idx_wi_sidebar_lang` (`lang`);

--
-- Indexes for table `wi_site`
--
ALTER TABLE `wi_site`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_slideshow`
--
ALTER TABLE `wi_slideshow`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_slideshow_slides`
--
ALTER TABLE `wi_slideshow_slides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wi_slideshow_slides_slide_id` (`slide_id`);

--
-- Indexes for table `wi_social`
--
ALTER TABLE `wi_social`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_social_logins`
--
ALTER TABLE `wi_social_logins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_wi_social_logins_provider` (`provider`,`provider_id`),
  ADD KEY `idx_wi_social_logins_user_id` (`user_id`);

--
-- Indexes for table `wi_system_versions`
--
ALTER TABLE `wi_system_versions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_wi_system_versions_component` (`component`);

--
-- Indexes for table `wi_tasks`
--
ALTER TABLE `wi_tasks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `wi_theme`
--
ALTER TABLE `wi_theme`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_wi_theme_theme` (`theme`);

--
-- Indexes for table `wi_track`
--
ALTER TABLE `wi_track`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wi_track_ip` (`ip`),
  ADD KEY `idx_wi_track_dt` (`dt`);

--
-- Indexes for table `wi_trans`
--
ALTER TABLE `wi_trans`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_wi_trans_lang_keyword` (`lang`,`keyword`),
  ADD KEY `idx_wi_trans_keyword` (`keyword`);

--
-- Indexes for table `wi_user_details`
--
ALTER TABLE `wi_user_details`
  ADD PRIMARY KEY (`id_user_details`),
  ADD UNIQUE KEY `uk_wi_user_details_user_id` (`user_id`);

--
-- Indexes for table `wi_user_roles`
--
ALTER TABLE `wi_user_roles`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `uk_wi_user_roles_role` (`role`);

--
-- Indexes for table `wi_visitors_log`
--
ALTER TABLE `wi_visitors_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_wi_visitors_log_page` (`page`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `wi_admin_info_box`
--
ALTER TABLE `wi_admin_info_box`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wi_admin_menu`
--
ALTER TABLE `wi_admin_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wi_admin_todo_list`
--
ALTER TABLE `wi_admin_todo_list`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_contact_message`
--
ALTER TABLE `wi_contact_message`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_css`
--
ALTER TABLE `wi_css`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `wi_elements`
--
ALTER TABLE `wi_elements`
  MODIFY `element_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_footer`
--
ALTER TABLE `wi_footer`
  MODIFY `footer_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wi_header`
--
ALTER TABLE `wi_header`
  MODIFY `header_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wi_lang`
--
ALTER TABLE `wi_lang`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wi_login_attempts`
--
ALTER TABLE `wi_login_attempts`
  MODIFY `id_login_attempts` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wi_logs`
--
ALTER TABLE `wi_logs`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `wi_media`
--
ALTER TABLE `wi_media`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_members`
--
ALTER TABLE `wi_members`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wi_menu`
--
ALTER TABLE `wi_menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `wi_meta`
--
ALTER TABLE `wi_meta`
  MODIFY `meta_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `wi_migrations`
--
ALTER TABLE `wi_migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_mod`
--
ALTER TABLE `wi_mod`
  MODIFY `mod_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_modules`
--
ALTER TABLE `wi_modules`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_notifications`
--
ALTER TABLE `wi_notifications`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_page`
--
ALTER TABLE `wi_page`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wi_permissions`
--
ALTER TABLE `wi_permissions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `wi_plugin`
--
ALTER TABLE `wi_plugin`
  MODIFY `plugin_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_plugin_customers`
--
ALTER TABLE `wi_plugin_customers`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_plugin_download_logs`
--
ALTER TABLE `wi_plugin_download_logs`
  MODIFY `log_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_plugin_invoices`
--
ALTER TABLE `wi_plugin_invoices`
  MODIFY `invoice_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_plugin_licenses`
--
ALTER TABLE `wi_plugin_licenses`
  MODIFY `license_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_plugin_orders`
--
ALTER TABLE `wi_plugin_orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_plugin_store`
--
ALTER TABLE `wi_plugin_store`
  MODIFY `store_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_plugin_subscriptions`
--
ALTER TABLE `wi_plugin_subscriptions`
  MODIFY `subscription_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_plugin_updates`
--
ALTER TABLE `wi_plugin_updates`
  MODIFY `update_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_role_permissions`
--
ALTER TABLE `wi_role_permissions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_scripts`
--
ALTER TABLE `wi_scripts`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `wi_secure_secrets`
--
ALTER TABLE `wi_secure_secrets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_sidebar`
--
ALTER TABLE `wi_sidebar`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `wi_site`
--
ALTER TABLE `wi_site`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wi_slideshow`
--
ALTER TABLE `wi_slideshow`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wi_slideshow_slides`
--
ALTER TABLE `wi_slideshow_slides`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_social`
--
ALTER TABLE `wi_social`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_social_logins`
--
ALTER TABLE `wi_social_logins`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_system_versions`
--
ALTER TABLE `wi_system_versions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wi_tasks`
--
ALTER TABLE `wi_tasks`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_theme`
--
ALTER TABLE `wi_theme`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `wi_track`
--
ALTER TABLE `wi_track`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `wi_trans`
--
ALTER TABLE `wi_trans`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `wi_user_details`
--
ALTER TABLE `wi_user_details`
  MODIFY `id_user_details` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `wi_user_roles`
--
ALTER TABLE `wi_user_roles`
  MODIFY `role_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `wi_visitors_log`
--
ALTER TABLE `wi_visitors_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `wi_members`
--
ALTER TABLE `wi_members`
  ADD CONSTRAINT `fk_wi_members_user_role` FOREIGN KEY (`user_role`) REFERENCES `wi_user_roles` (`role_id`);

--
-- Constraints for table `wi_modules`
--
ALTER TABLE `wi_modules`
  ADD CONSTRAINT `fk_wi_modules_mod` FOREIGN KEY (`mod_id`) REFERENCES `wi_mod` (`mod_id`) ON DELETE SET NULL;

--
-- Constraints for table `wi_slideshow_slides`
--
ALTER TABLE `wi_slideshow_slides`
  ADD CONSTRAINT `fk_wi_slideshow_slides_slideshow` FOREIGN KEY (`slide_id`) REFERENCES `wi_slideshow` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `wi_social_logins`
--
ALTER TABLE `wi_social_logins`
  ADD CONSTRAINT `fk_wi_social_logins_user` FOREIGN KEY (`user_id`) REFERENCES `wi_members` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `wi_user_details`
--
ALTER TABLE `wi_user_details`
  ADD CONSTRAINT `fk_wi_user_details_user` FOREIGN KEY (`user_id`) REFERENCES `wi_members` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
