-- WICMS Clean Base SQL generated from Aurevia core-only tables

-- Generated: 2026-07-09T11:11:49+00:00

-- Purpose: clean WICMS installer baseline. Compliance/industry/plugin runtime rows are intentionally excluded.

SET FOREIGN_KEY_CHECKS=0;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";


-- --------------------------------------------------------

-- Table: `wi_admin_info_box`

CREATE TABLE IF NOT EXISTS `wi_admin_info_box` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_admin_info_box` (`id`, `name`, `info`) VALUES
(1,'Bounce Rate','bounce'),
(2,'Unique Visitors','visitors'),
(3,'User Registrations','regUser');


-- --------------------------------------------------------

-- Table: `wi_admin_menu`

CREATE TABLE IF NOT EXISTS `wi_admin_menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#',
  `parent` int NOT NULL DEFAULT '0',
  `sort` int DEFAULT NULL,
  `lang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_admin_menu_parent_sort` (`parent`,`sort`),
  KEY `idx_wi_admin_menu_lang` (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_admin_menu` (`id`, `label`, `link`, `parent`, `sort`, `lang`) VALUES
(1,'Administrators','WIAdmin.php',0,0,'admins'),
(2,'Logs','WILogs.php',0,1,'logs'),
(3,'Visitors','WIVisitors.php',0,2,'visitors');


-- --------------------------------------------------------

-- Table: `wi_admin_msg`

CREATE TABLE IF NOT EXISTS `wi_admin_msg` (
  `id` int NOT NULL AUTO_INCREMENT,
  `msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `wtime` datetime NOT NULL,
  `user_id` int NOT NULL,
  `attachments` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  `file` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_admin_todo_list`

CREATE TABLE IF NOT EXISTS `wi_admin_todo_list` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `timeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed` enum('y','n') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`),
  KEY `idx_wi_admin_todo_completed` (`completed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_contact_message`

CREATE TABLE IF NOT EXISTS `wi_contact_message` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `time_sent` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_wi_contact_message_email` (`email`),
  KEY `idx_wi_contact_message_time_sent` (`time_sent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_css`

CREATE TABLE IF NOT EXISTS `wi_css` (
  `id` int NOT NULL AUTO_INCREMENT,
  `href` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_css_page` (`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_css` (`id`, `href`, `rel`, `page`) VALUES
(1,'site/css/frameworks/bootstrap4.css','stylesheet','index'),
(7,'site/css/frameworks/bootstrap.css','stylesheet','alogin'),
(8,'site/css/login_panel/css/slide.css','stylesheet','alogin'),
(9,'site/css/frameworks/menus.css','stylesheet','alogin'),
(10,'site/css/style.css','stylesheet','alogin'),
(11,'site/css/font-awesome.css','stylesheet','alogin'),
(12,'site/css/vendor/bootstrap.min.css','stylesheet','alogin'),
(13,'site/css/frameworks/bootstrap.css','stylesheet','confirm'),
(14,'site/css/login_panel/css/slide.css','stylesheet','confirm'),
(15,'site/css/frameworks/menus.css','stylesheet','confirm'),
(16,'site/css/style.css','stylesheet','confirm'),
(17,'site/css/font-awesome.css','stylesheet','confirm'),
(18,'site/css/vendor/bootstrap.min.css','stylesheet','confirm'),
(19,'site/css/frameworks/bootstrap.css','stylesheet','contact_us'),
(20,'site/css/login_panel/css/slide.css','stylesheet','contact_us'),
(21,'site/css/frameworks/menus.css','stylesheet','contact_us'),
(22,'site/css/style.css','stylesheet','contact_us'),
(23,'site/css/font-awesome.css','stylesheet','contact_us'),
(24,'site/css/vendor/bootstrap.min.css','stylesheet','contact_us'),
(25,'site/css/frameworks/bootstrap.css','stylesheet','profile'),
(26,'site/css/login_panel/css/slide.css','stylesheet','profile'),
(27,'site/css/frameworks/menus.css','stylesheet','profile'),
(28,'site/css/style.css','stylesheet','profile'),
(29,'site/css/font-awesome.css','stylesheet','profile'),
(30,'site/css/vendor/bootstrap.min.css','stylesheet','profile'),
(31,'user/css/profile.css','stylesheet','profile'),
(32,'site/css/frameworks/bootstrap.css','stylesheet','passwordreset'),
(33,'site/css/login_panel/css/slide.css','stylesheet','passwordreset'),
(34,'site/css/frameworks/menus.css','stylesheet','passwordreset'),
(35,'site/css/style.css','stylesheet','passwordreset'),
(36,'site/css/font-awesome.css','stylesheet','passwordreset'),
(37,'site/css/vendor/bootstrap.min.css','stylesheet','passwordreset'),
(38,'site/css/frameworks/bootstrap.css','stylesheet','about_us'),
(39,'site/css/login_panel/css/slide.css','stylesheet','about_us'),
(40,'site/css/frameworks/menus.css','stylesheet','about_us'),
(41,'site/css/style.css','stylesheet','about_us'),
(42,'site/css/font-awesome.css','stylesheet','about_us'),
(43,'site/css/vendor/bootstrap.min.css','stylesheet','about_us'),
(118,'site/css/frameworks/menus.css','stylesheet','login'),
(119,'site/css/style.css','stylesheet','login'),
(120,'site/css/font-awesome.css','stylesheet','login'),
(121,'site/css/system.css','stylesheet','login'),
(123,'site/css/frameworks/bootstrap.css','stylesheet','register'),
(124,'site/css/login_panel/css/slide.css','stylesheet','register'),
(125,'site/css/frameworks/menus.css','stylesheet','register'),
(126,'site/css/style.css','stylesheet','register'),
(127,'site/css/font-awesome.css','stylesheet','register'),
(128,'site/css/vendor/bootstrap.min.css','stylesheet','register'),
(129,'site/css/system.css','stylesheet','register'),
(130,'site/css/frameworks/bootstrap4.css','stylesheet','dashboard'),
(131,'site/css/login_panel/css/slide.css','stylesheet','dashboard'),
(132,'site/css/frameworks/menus.css','stylesheet','dashboard'),
(136,'site/css/style.css','stylesheet','dashboard'),
(137,'site/css/font-awesome.css','stylesheet','dashboard'),
(138,'site/css/vendor/bootstrap.min.css','stylesheet','dashboard'),
(151,'site/css/frameworks/bootstrap4.css','stylesheet','documents'),
(152,'site/css/login_panel/css/slide.css','stylesheet','documents'),
(153,'site/css/frameworks/menus.css','stylesheet','documents'),
(154,'site/css/style.css','stylesheet','documents'),
(155,'site/css/font-awesome.css','stylesheet','documents'),
(156,'site/css/vendor/bootstrap.min.css','stylesheet','documents'),
(157,'site/css/frameworks/bootstrap4.css','stylesheet','training'),
(205,'site/css/frameworks/bootstrap4.css','stylesheet','setup'),
(206,'site/css/login_panel/css/slide.css','stylesheet','setup'),
(207,'site/css/frameworks/menus.css','stylesheet','setup'),
(209,'site/css/font-awesome.css','stylesheet','setup'),
(210,'site/css/vendor/bootstrap.min.css','stylesheet','setup'),
(217,'site/css/frameworks/bootstrap4.css','stylesheet','users'),
(218,'site/css/login_panel/css/slide.css','stylesheet','users'),
(219,'site/css/frameworks/menus.css','stylesheet','users'),
(220,'site/css/style.css','stylesheet','users'),
(221,'site/css/font-awesome.css','stylesheet','users'),
(222,'site/css/vendor/bootstrap.min.css','stylesheet','users'),
(223,'site/css/frameworks/bootstrap4.css','stylesheet','settings'),
(268,'site/css/WIMarketing.css','stylesheet','index'),
(269,'site/css/WIMarketing.css','stylesheet','about_us'),
(279,'user/css/WIMemberWorkspace.css','stylesheet','profile'),
(280,'user/css/WIMembers.css','stylesheet','profile'),
(281,'user/css/WIMembers.css?v=20260524','stylesheet','account'),
(282,'user/css/WIMembers.css?v=20260524','stylesheet','settings');
INSERT IGNORE INTO `wi_css` (`id`, `href`, `rel`, `page`) VALUES
(283,'user/css/WIMembers.css?v=20260524','stylesheet','membership'),
(284,'user/css/WIMembers.css?v=20260524','stylesheet','upgrade'),
(285,'user/css/WIMembers.css?v=20260524','stylesheet','downgrade'),
(286,'user/css/WIMembers.css?v=20260524','stylesheet','payments'),
(287,'user/css/WIMembers.css?v=20260524','stylesheet','userpayments'),
(288,'user/css/WIMembers.css?v=20260524','stylesheet','transactions'),
(289,'user/css/WIMembers.css?v=20260524','stylesheet','security'),
(290,'user/css/WIMembers.css?v=20260524','stylesheet','usersecurity'),
(291,'user/css/WIMembers.css?v=20260524','stylesheet','forms'),
(292,'user/css/WIMembers.css?v=20260524','stylesheet','support'),
(293,'user/css/WIMembers.css?v=20260524','stylesheet','delete_profile'),
(294,'site/css/font-awesome.css','stylesheet','account'),
(295,'site/css/frameworks/bootstrap.css','stylesheet','account'),
(296,'site/css/frameworks/menus.css','stylesheet','account'),
(297,'site/css/login_panel/css/slide.css','stylesheet','account'),
(298,'site/css/style.css','stylesheet','account'),
(299,'site/css/vendor/bootstrap.min.css','stylesheet','account'),
(300,'user/css/profile.css','stylesheet','account'),
(301,'user/css/WIMemberWorkspace.css','stylesheet','account'),
(318,'site/css/font-awesome.css','stylesheet','delete_profile'),
(319,'site/css/frameworks/bootstrap.css','stylesheet','delete_profile'),
(320,'site/css/frameworks/menus.css','stylesheet','delete_profile'),
(321,'site/css/login_panel/css/slide.css','stylesheet','delete_profile'),
(322,'site/css/style.css','stylesheet','delete_profile'),
(323,'site/css/vendor/bootstrap.min.css','stylesheet','delete_profile'),
(324,'user/css/profile.css','stylesheet','delete_profile'),
(325,'user/css/WIMemberWorkspace.css','stylesheet','delete_profile'),
(326,'site/css/font-awesome.css','stylesheet','downgrade'),
(327,'site/css/frameworks/bootstrap.css','stylesheet','downgrade'),
(328,'site/css/frameworks/menus.css','stylesheet','downgrade'),
(329,'site/css/login_panel/css/slide.css','stylesheet','downgrade'),
(330,'site/css/style.css','stylesheet','downgrade'),
(331,'site/css/vendor/bootstrap.min.css','stylesheet','downgrade'),
(332,'user/css/profile.css','stylesheet','downgrade'),
(333,'user/css/WIMemberWorkspace.css','stylesheet','downgrade'),
(334,'site/css/font-awesome.css','stylesheet','forms'),
(335,'site/css/frameworks/bootstrap.css','stylesheet','forms'),
(336,'site/css/frameworks/menus.css','stylesheet','forms'),
(337,'site/css/login_panel/css/slide.css','stylesheet','forms'),
(338,'site/css/style.css','stylesheet','forms'),
(339,'site/css/vendor/bootstrap.min.css','stylesheet','forms'),
(340,'user/css/profile.css','stylesheet','forms'),
(341,'user/css/WIMemberWorkspace.css','stylesheet','forms'),
(342,'site/css/font-awesome.css','stylesheet','index'),
(343,'site/css/frameworks/bootstrap.css','stylesheet','index'),
(344,'site/css/frameworks/menus.css','stylesheet','index'),
(345,'site/css/login_panel/css/slide.css','stylesheet','index'),
(346,'site/css/style.css','stylesheet','index'),
(347,'site/css/vendor/bootstrap.min.css','stylesheet','index'),
(348,'user/css/profile.css','stylesheet','index'),
(349,'user/css/WIMemberWorkspace.css','stylesheet','index'),
(358,'site/css/font-awesome.css','stylesheet','membership'),
(359,'site/css/frameworks/bootstrap.css','stylesheet','membership'),
(360,'site/css/frameworks/menus.css','stylesheet','membership'),
(361,'site/css/login_panel/css/slide.css','stylesheet','membership'),
(362,'site/css/style.css','stylesheet','membership'),
(363,'site/css/vendor/bootstrap.min.css','stylesheet','membership'),
(364,'user/css/profile.css','stylesheet','membership'),
(365,'user/css/WIMemberWorkspace.css','stylesheet','membership'),
(374,'site/css/font-awesome.css','stylesheet','payments'),
(375,'site/css/frameworks/bootstrap.css','stylesheet','payments'),
(376,'site/css/frameworks/menus.css','stylesheet','payments'),
(377,'site/css/login_panel/css/slide.css','stylesheet','payments'),
(378,'site/css/style.css','stylesheet','payments'),
(379,'site/css/vendor/bootstrap.min.css','stylesheet','payments'),
(380,'user/css/profile.css','stylesheet','payments'),
(381,'user/css/WIMemberWorkspace.css','stylesheet','payments'),
(382,'site/css/font-awesome.css','stylesheet','security'),
(383,'site/css/frameworks/bootstrap.css','stylesheet','security'),
(384,'site/css/frameworks/menus.css','stylesheet','security'),
(385,'site/css/login_panel/css/slide.css','stylesheet','security'),
(386,'site/css/style.css','stylesheet','security'),
(387,'site/css/vendor/bootstrap.min.css','stylesheet','security'),
(388,'user/css/profile.css','stylesheet','security'),
(389,'user/css/WIMemberWorkspace.css','stylesheet','security'),
(390,'site/css/font-awesome.css','stylesheet','settings'),
(391,'site/css/frameworks/bootstrap.css','stylesheet','settings'),
(392,'site/css/frameworks/menus.css','stylesheet','settings'),
(393,'site/css/login_panel/css/slide.css','stylesheet','settings'),
(394,'site/css/style.css','stylesheet','settings');
INSERT IGNORE INTO `wi_css` (`id`, `href`, `rel`, `page`) VALUES
(395,'site/css/vendor/bootstrap.min.css','stylesheet','settings'),
(396,'user/css/profile.css','stylesheet','settings'),
(397,'user/css/WIMemberWorkspace.css','stylesheet','settings'),
(398,'site/css/font-awesome.css','stylesheet','support'),
(399,'site/css/frameworks/bootstrap.css','stylesheet','support'),
(400,'site/css/frameworks/menus.css','stylesheet','support'),
(401,'site/css/login_panel/css/slide.css','stylesheet','support'),
(402,'site/css/style.css','stylesheet','support'),
(403,'site/css/vendor/bootstrap.min.css','stylesheet','support'),
(404,'user/css/profile.css','stylesheet','support'),
(405,'user/css/WIMemberWorkspace.css','stylesheet','support'),
(406,'site/css/font-awesome.css','stylesheet','training'),
(407,'site/css/frameworks/bootstrap.css','stylesheet','training'),
(408,'site/css/frameworks/menus.css','stylesheet','training'),
(409,'site/css/login_panel/css/slide.css','stylesheet','training'),
(410,'site/css/style.css','stylesheet','training'),
(411,'site/css/vendor/bootstrap.min.css','stylesheet','training'),
(412,'user/css/profile.css','stylesheet','training'),
(413,'user/css/WIMemberWorkspace.css','stylesheet','training'),
(414,'site/css/font-awesome.css','stylesheet','transactions'),
(415,'site/css/frameworks/bootstrap.css','stylesheet','transactions'),
(416,'site/css/frameworks/menus.css','stylesheet','transactions'),
(417,'site/css/login_panel/css/slide.css','stylesheet','transactions'),
(418,'site/css/style.css','stylesheet','transactions'),
(419,'site/css/vendor/bootstrap.min.css','stylesheet','transactions'),
(420,'user/css/profile.css','stylesheet','transactions'),
(421,'user/css/WIMemberWorkspace.css','stylesheet','transactions'),
(422,'site/css/font-awesome.css','stylesheet','upgrade'),
(423,'site/css/frameworks/bootstrap.css','stylesheet','upgrade'),
(424,'site/css/frameworks/menus.css','stylesheet','upgrade'),
(425,'site/css/login_panel/css/slide.css','stylesheet','upgrade'),
(426,'site/css/style.css','stylesheet','upgrade'),
(427,'site/css/vendor/bootstrap.min.css','stylesheet','upgrade'),
(428,'user/css/profile.css','stylesheet','upgrade'),
(429,'user/css/WIMemberWorkspace.css','stylesheet','upgrade'),
(430,'site/css/font-awesome.css','stylesheet','userpayments'),
(431,'site/css/frameworks/bootstrap.css','stylesheet','userpayments'),
(432,'site/css/frameworks/menus.css','stylesheet','userpayments'),
(433,'site/css/login_panel/css/slide.css','stylesheet','userpayments'),
(434,'site/css/style.css','stylesheet','userpayments'),
(435,'site/css/vendor/bootstrap.min.css','stylesheet','userpayments'),
(436,'user/css/profile.css','stylesheet','userpayments'),
(437,'user/css/WIMemberWorkspace.css','stylesheet','userpayments'),
(438,'site/css/font-awesome.css','stylesheet','usersecurity'),
(439,'site/css/frameworks/bootstrap.css','stylesheet','usersecurity'),
(440,'site/css/frameworks/menus.css','stylesheet','usersecurity'),
(441,'site/css/login_panel/css/slide.css','stylesheet','usersecurity'),
(442,'site/css/style.css','stylesheet','usersecurity'),
(443,'site/css/vendor/bootstrap.min.css','stylesheet','usersecurity'),
(444,'user/css/profile.css','stylesheet','usersecurity'),
(445,'user/css/WIMemberWorkspace.css','stylesheet','usersecurity'),
(446,'site/css/font-awesome.css','stylesheet','workspace'),
(447,'site/css/frameworks/bootstrap.css','stylesheet','workspace'),
(448,'site/css/frameworks/menus.css','stylesheet','workspace'),
(449,'site/css/login_panel/css/slide.css','stylesheet','workspace'),
(450,'site/css/style.css','stylesheet','workspace'),
(451,'site/css/vendor/bootstrap.min.css','stylesheet','workspace'),
(452,'user/css/profile.css','stylesheet','workspace'),
(453,'user/css/WIMemberWorkspace.css','stylesheet','workspace'),
(552,'site/css/WIMarketing.css','stylesheet','login'),
(554,'site/css/WIMarketing.css','stylesheet','profile'),
(555,'site/css/WIMarketing.css','stylesheet','register'),
(556,'site/css/WIMarketing.css','stylesheet','contact_us'),
(557,'site/css/WIMarketing.css','stylesheet','alogin');


-- --------------------------------------------------------

-- Table: `wi_elements`

CREATE TABLE IF NOT EXISTS `wi_elements` (
  `element_id` int NOT NULL AUTO_INCREMENT,
  `element_status` enum('enabled','disabled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disabled',
  `element_powered` enum('power_on','power_off') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'power_off',
  `element_type` enum('Common Fields','HTML Elements','Layout') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Common Fields',
  `element_author` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `element_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `element_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `element_font` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`element_id`),
  KEY `idx_wi_elements_name` (`element_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_footer`

CREATE TABLE IF NOT EXISTS `wi_footer` (
  `footer_id` int NOT NULL AUTO_INCREMENT,
  `footer_content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `footer_linking` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `website_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`footer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_footer` (`footer_id`, `footer_content`, `footer_linking`, `website_name`) VALUES
(1,'','','WICMS');


-- --------------------------------------------------------

-- Table: `wi_header`

CREATE TABLE IF NOT EXISTS `wi_header` (
  `header_id` int NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `bk_header_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `header_slogan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`header_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_header` (`header_id`, `logo`, `bk_header_image`, `header_image`, `header_content`, `header_slogan`) VALUES
(1,'wicms-logo.png','','bk_header','','');


-- --------------------------------------------------------

-- Table: `wi_lang`

CREATE TABLE IF NOT EXISTS `wi_lang` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `lang_flag` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `href` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wi_lang_lang` (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_lang` (`id`, `lang`, `name`, `lang_flag`, `href`) VALUES
(1,'en','English','en.png','?lang=en'),
(2,'rs','Serbian','rs.png','?lang=rs'),
(3,'ru','Russian','ru.png','?lang=ru'),
(4,'es','Spanish','es.png','?lang=es'),
(5,'fr','French','fr.png','?lang=fr'),
(6,'cn','China','cn.png','?lang=cn');


-- --------------------------------------------------------

-- Table: `wi_login_attempts`

CREATE TABLE IF NOT EXISTS `wi_login_attempts` (
  `id_login_attempts` int NOT NULL AUTO_INCREMENT,
  `ip_addr` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempt_number` int NOT NULL DEFAULT '1',
  `date` date NOT NULL,
  PRIMARY KEY (`id_login_attempts`),
  KEY `idx_wi_login_attempts_ip_date` (`ip_addr`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_logs`

CREATE TABLE IF NOT EXISTS `wi_logs` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `opperation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `idx_wi_logs_date` (`date`),
  KEY `idx_wi_logs_user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_media`

CREATE TABLE IF NOT EXISTS `wi_media` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alt_text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `caption` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `description_encrypted` mediumtext COLLATE utf8mb4_unicode_ci,
  `original_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `stored_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_url` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folder` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folder_blind_index` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `extension` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mime_type` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `media_type` enum('image','video','audio','document','other') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'other',
  `file_size` bigint unsigned NOT NULL DEFAULT '0',
  `width` int unsigned DEFAULT NULL,
  `height` int unsigned DEFAULT NULL,
  `duration` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `is_private` tinyint(1) NOT NULL DEFAULT '0',
  `uploaded_by` int unsigned DEFAULT NULL,
  `org_business_id` int unsigned DEFAULT NULL,
  `org_site_id` int unsigned DEFAULT NULL,
  `org_department_id` int unsigned DEFAULT NULL,
  `uploaded_by_user_id` int unsigned DEFAULT NULL,
  `storage_disk` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'local',
  `visibility` enum('public','private','restricted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'private',
  `access_scope` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'business',
  `file_hash` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sha256_hash` char(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_sensitive` tinyint(1) NOT NULL DEFAULT '0',
  `is_encrypted` tinyint(1) NOT NULL DEFAULT '0',
  `scan_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `processing_status` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ready',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by_user_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uuid` (`uuid`),
  KEY `idx_media_type` (`media_type`),
  KEY `idx_folder` (`folder`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  KEY `idx_status` (`status`),
  KEY `idx_wi_media_business_site` (`org_business_id`,`org_site_id`),
  KEY `idx_wi_media_hash` (`sha256_hash`),
  KEY `idx_wi_media_visibility` (`visibility`),
  KEY `idx_wi_media_deleted` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_members`

CREATE TABLE IF NOT EXISTS `wi_members` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(254) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `confirmation_key` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `confirmed` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `password_reset_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password_reset_confirmed` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  `password_reset_timestamp` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `register_date` date NOT NULL,
  `user_role` int NOT NULL DEFAULT '1',
  `last_login` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ip_addr` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `banned` enum('Y','N') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uk_wi_members_username` (`username`),
  UNIQUE KEY `uk_wi_members_email` (`email`),
  KEY `idx_wi_members_role` (`user_role`),
  KEY `idx_wi_members_confirmed` (`confirmed`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_menu`

CREATE TABLE IF NOT EXISTS `wi_menu` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#',
  `parent` int NOT NULL DEFAULT '0',
  `sort` int DEFAULT NULL,
  `lang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_menu_parent_sort` (`parent`,`sort`),
  KEY `idx_wi_menu_lang` (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_menu` (`id`, `label`, `link`, `parent`, `sort`, `lang`) VALUES
(1,'Home','index.php',0,0,'home'),
(2,'About','about_us.php',0,1,'about'),
(3,'Contact','contact_us.php',0,2,'contact');


-- --------------------------------------------------------

-- Table: `wi_meta`

CREATE TABLE IF NOT EXISTS `wi_meta` (
  `meta_id` int NOT NULL AUTO_INCREMENT,
  `page` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `author` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `idx_wi_meta_page` (`page`),
  KEY `idx_wi_meta_page_name` (`page`,`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_meta` (`meta_id`, `page`, `name`, `content`, `author`) VALUES
(1,'index','viewport','width=device-width, initial-scale=1','WICMS'),
(2,'index','description','Warner-Infinity Content Management System with simplified back end','WICMS'),
(3,'index','keywords','WI, WICMS, CMS, UI','WICMS'),
(4,'index','author','warner-infinity','WICMS'),
(5,'alogin','viewport','width=device-width, initial-scale=1','WICMS'),
(6,'alogin','description','Warner-Infinity Content Management System','WICMS'),
(7,'alogin','keywords','WI, WICMS, login','WICMS'),
(8,'alogin','author','warner-infinity','WICMS'),
(9,'confirm','viewport','width=device-width, initial-scale=1','WICMS'),
(10,'confirm','description','Email confirmation page','WICMS'),
(11,'confirm','keywords','WI, WICMS, confirm','WICMS'),
(12,'confirm','author','warner-infinity','WICMS'),
(13,'contact_us','viewport','width=device-width, initial-scale=1','WICMS'),
(14,'contact_us','description','Contact page','WICMS'),
(15,'contact_us','keywords','WI, WICMS, contact','WICMS'),
(16,'contact_us','author','warner-infinity','WICMS'),
(17,'profile','viewport','width=device-width, initial-scale=1','WICMS'),
(18,'profile','description','Profile page','WICMS'),
(19,'profile','keywords','WI, WICMS, profile','WICMS'),
(20,'profile','author','warner-infinity','WICMS'),
(21,'passwordreset','viewport','width=device-width, initial-scale=1','WICMS'),
(22,'passwordreset','description','Password reset page','WICMS'),
(23,'passwordreset','keywords','WI, WICMS, reset','WICMS'),
(24,'passwordreset','author','warner-infinity','WICMS'),
(25,'about_us','viewport','width=device-width, initial-scale=1','WICMS'),
(26,'about_us','description','About page','WICMS'),
(27,'about_us','keywords','WI, WICMS, about','WICMS'),
(28,'about_us','author','warner-infinity','WICMS');


-- --------------------------------------------------------

-- Table: `wi_migrations`

CREATE TABLE IF NOT EXISTS `wi_migrations` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `migration_key` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch_no` int NOT NULL DEFAULT '1',
  `applied_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wi_migrations_key` (`migration_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_mod`

CREATE TABLE IF NOT EXISTS `wi_mod` (
  `mod_id` int NOT NULL AUTO_INCREMENT,
  `mod_status` enum('enabled','disabled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'disabled',
  `mod_powered` enum('power_on','power_off') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'power_off',
  `mod_type` enum('element','custom') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'element',
  `mod_author` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_name` varchar(55) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `Mod_description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mod_font` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`mod_id`),
  KEY `idx_wi_mod_module_name` (`module_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_mod` (`mod_id`, `mod_status`, `mod_powered`, `mod_type`, `mod_author`, `module_name`, `Mod_description`, `mod_font`) VALUES
(1,'enabled','power_on','custom','WICMS','top_head','Core top head element','fa-window-maximize'),
(2,'enabled','power_on','custom','WICMS','Panel','Core panel container','fa-th-large'),
(3,'enabled','power_on','custom','WICMS','welcome_box','Core welcome page','fa-home'),
(4,'enabled','power_on','custom','WICMS','alogin','Core admin login page','fa-lock'),
(5,'enabled','power_on','custom','WICMS','login','Core member login page','fa-sign-in'),
(6,'enabled','power_on','custom','WICMS','register','Core registration page','fa-user-plus'),
(7,'enabled','power_on','custom','WICMS','contact_us','Core contact page','fa-envelope'),
(8,'enabled','power_on','custom','WICMS','about_us','Core about page','fa-info-circle'),
(9,'enabled','power_on','custom','WICMS','passwordreset','Core password reset page','fa-key'),
(10,'enabled','power_on','custom','WICMS','profile','Member profile workspace','fa-user'),
(11,'enabled','power_on','custom','WICMS','dashboard','Core dashboard page','fa-dashboard'),
(12,'enabled','power_on','custom','WICMS','documents','Core documents placeholder','fa-folder'),
(13,'enabled','power_on','custom','WICMS','training','Core training placeholder','fa-graduation-cap'),
(14,'enabled','power_on','custom','WICMS','setup','Core setup page','fa-cogs'),
(15,'enabled','power_on','custom','WICMS','users','Core users page','fa-users'),
(16,'enabled','power_on','custom','WICMS','settings','Core settings page','fa-cog'),
(17,'enabled','power_on','custom','WICMS','forms','Member forms page','fa-list-alt'),
(18,'enabled','power_on','custom','WICMS','support','Support page','fa-life-ring'),
(19,'enabled','power_on','custom','WICMS','account','Member account page','fa-user-circle'),
(20,'enabled','power_on','custom','WICMS','membership','Membership page','fa-id-card'),
(21,'enabled','power_on','custom','WICMS','payments','Payments page','fa-credit-card'),
(22,'enabled','power_on','custom','WICMS','transactions','Transactions page','fa-exchange'),
(23,'enabled','power_on','custom','WICMS','security','Security page','fa-shield'),
(24,'enabled','power_on','custom','WICMS','workspace','Member workspace page','fa-th'),
(25,'enabled','power_on','custom','WICMS','delete_profile','Delete profile page','fa-trash'),
(26,'enabled','power_on','custom','WICMS','bug_reporter','Core bug reporter component','fa-bug'),
(27,'enabled','power_on','custom','WICMS','bug_reports','Admin bug report workspace','fa-bug');


-- --------------------------------------------------------

-- Table: `wi_modules`

CREATE TABLE IF NOT EXISTS `wi_modules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `mod_id` int DEFAULT NULL,
  `trans` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans4` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `img2` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans5` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text6` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trans6` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_modules_mod_id` (`mod_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_modules` (`id`, `name`, `mod_id`) VALUES
(1,'top_head',1),
(2,'Panel',2),
(3,'welcome_box',3),
(4,'alogin',4),
(5,'login',5),
(6,'register',6),
(7,'contact_us',7),
(8,'about_us',8),
(9,'passwordreset',9),
(10,'profile',10),
(11,'dashboard',11),
(12,'documents',12),
(13,'training',13),
(14,'setup',14),
(15,'users',15),
(16,'settings',16),
(17,'forms',17),
(18,'support',18),
(19,'account',19),
(20,'membership',20),
(21,'payments',21),
(22,'transactions',22),
(23,'security',23),
(24,'workspace',24),
(25,'delete_profile',25),
(26,'bug_reporter',26),
(27,'bug_reports',27);


-- --------------------------------------------------------

-- Table: `wi_notifications`

CREATE TABLE IF NOT EXISTS `wi_notifications` (
  `ID` int NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `opperation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `idx_wi_notifications_date` (`date`),
  KEY `idx_wi_notifications_user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_page`

CREATE TABLE IF NOT EXISTS `wi_page` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `panel` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `top_head` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `header` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  `left_sidebar` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `right_sidebar` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `contents` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `footer` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wi_page_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_page` (`id`, `name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`) VALUES
(1,'alogin','1','1','0','0','0','alogin','1'),
(2,'confirm','1','1','0','0','0','confirm','1'),
(3,'index','1','1','1','0','0','welcome_box','1'),
(4,'passwordreset','1','1','0','0','0','passwordreset','1'),
(5,'profile','1','1','0','0','0','profile','1'),
(6,'contact_us','1','1','0','0','0','contact_us','1'),
(7,'about_us','1','0','0','0','0','about_us','1'),
(28,'login','0','1','0','0','0','login','1'),
(29,'register','0','1','0','0','0','register','1'),
(118,'dashboard','1','0','0','0','0','dashboard','1'),
(122,'documents','1','1','1','0','0','forms','1'),
(123,'training','1','1','0','0','0','forms','1'),
(130,'setup','1','1','0','0','0','setup','1'),
(132,'users','1','1','1','0','0','users','1'),
(133,'settings','0','0','0','0','0','settings','0'),
(139,'account','0','0','0','0','0','account','0'),
(140,'membership','0','0','0','0','0','membership','0'),
(141,'upgrade','0','0','0','0','0','membership','0'),
(142,'downgrade','0','0','0','0','0','membership','0'),
(143,'payments','0','0','0','0','0','payments','0'),
(144,'userpayments','0','0','0','0','0','payments','0'),
(145,'transactions','0','0','0','0','0','transactions','0'),
(146,'security','0','0','0','0','0','security','0'),
(147,'usersecurity','0','0','0','0','0','security','0'),
(148,'forms','0','0','0','0','0','forms','0'),
(149,'support','0','0','0','0','0','support','0'),
(150,'delete_profile','0','0','0','0','0','delete_profile','0'),
(151,'workspace','0','0','0','0','0','profile','0'),
(152,'actions','0','0','0','0','0','profile','0');


-- --------------------------------------------------------

-- Table: `wi_permissions`

CREATE TABLE IF NOT EXISTS `wi_permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wi_permissions_code` (`code`),
  KEY `idx_wi_permissions_group` (`group_name`),
  KEY `idx_wi_permissions_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_permissions` (`id`, `name`, `code`, `group_name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1,'View Users','users.view','Users','Can view users list',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(2,'Create Users','users.create','Users','Can create users',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(3,'Edit Users','users.edit','Users','Can edit users',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(4,'Delete Users','users.delete','Users','Can delete/deactivate users',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(5,'View Roles','roles.view','Roles','Can view roles',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(6,'Create Roles','roles.create','Roles','Can create roles',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(7,'Edit Roles','roles.edit','Roles','Can edit roles',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(8,'Delete Roles','roles.delete','Roles','Can delete custom roles',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(9,'View Permissions','permissions.view','Permissions','Can view permissions',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(10,'Create Permissions','permissions.create','Permissions','Can create permissions',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(11,'Edit Permissions','permissions.edit','Permissions','Can edit permissions',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(12,'Delete Permissions','permissions.delete','Permissions','Can delete unused permissions',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(13,'View Settings','settings.view','Settings','Can view WICMS settings',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(14,'Edit Settings','settings.edit','Settings','Can edit WICMS settings',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(15,'View Pages','pages.view','Pages','Can view pages',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(16,'Create Pages','pages.create','Pages','Can create pages',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(17,'Edit Pages','pages.edit','Pages','Can edit pages',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(18,'Delete Pages','pages.delete','Pages','Can delete pages',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(19,'View Media','media.view','Media','Can view media',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(20,'Upload Media','media.upload','Media','Can upload media',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(21,'Delete Media','media.delete','Media','Can delete media',1,'2026-03-16 19:24:15','2026-07-02 00:56:56'),
(23,'Access Admin Area','admin.access','Admin','Can access the WICMS admin area',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(24,'View Admin Dashboard','dashboard.view','Dashboard','Can view the WICMS admin dashboard',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(25,'View Menus','menus.view','Menus','Can view menus',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(26,'Manage Menus','menus.manage','Menus','Can create/edit menus',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(27,'Sort Menus','menus.sort','Menus','Can sort menus',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(28,'View Plugins','plugins.view','Plugins','Can view plugins',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(29,'Manage Plugins','plugins.manage','Plugins','Can manage plugin settings',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(30,'Install Plugins','plugins.install','Plugins','Can install plugins',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(31,'Activate Plugins','plugins.activate','Plugins','Can activate plugins',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(32,'Deactivate Plugins','plugins.deactivate','Plugins','Can deactivate plugins',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(33,'Uninstall Plugins','plugins.uninstall','Plugins','Can uninstall plugins after confirmation',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(34,'Developer Plugin Purge','plugins.developer_purge','Plugins','Can perform local/developer purge',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(35,'View Packages','packages.view','Packages','Can view packages/add-ons',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(36,'Manage Packages','packages.manage','Packages','Can manage package settings',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(37,'Install Packages','packages.install','Packages','Can install packages',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(38,'Activate Packages','packages.activate','Packages','Can activate packages/add-ons',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(39,'Deactivate Packages','packages.deactivate','Packages','Can deactivate packages/add-ons',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(40,'Uninstall Packages','packages.uninstall','Packages','Can uninstall packages after confirmation',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(41,'View Modules','modules.view','Modules','Can view modules',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(42,'Manage Modules','modules.manage','Modules','Can manage modules',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(43,'View Themes','themes.view','Themes','Can view themes',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(44,'Manage Themes','themes.manage','Themes','Can manage themes',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(45,'View Legal & Cookies','legal_cookies.view','Legal & Cookies','Can view consent/legal/cookie settings',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(46,'Manage Legal & Cookies','legal_cookies.manage','Legal & Cookies','Can manage consent/legal/cookie settings',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(47,'View Bug Reports','bug_reports.view','Bug Reports','Can view bug reports',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(48,'Manage Bug Reports','bug_reports.manage','Bug Reports','Can manage bug reports',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(49,'Resolve Bug Reports','bug_reports.resolve','Bug Reports','Can resolve bug reports',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(50,'View Audit Logs','audit_logs.view','System','Can view system/audit logs',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(51,'View System Health','system.health.view','System','Can view system health',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(52,'Manage System Health','system.health.manage','System','Can manage system health/repairs',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(53,'API Access','api.access','API','Can access API routes',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(54,'API Read','api.read','API','Can read through API routes',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(55,'API Write','api.write','API','Can write through API routes',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(56,'API Admin','api.admin','API','Can perform trusted API admin actions',1,'2026-07-02 00:56:56','2026-07-02 00:56:56'),
(57,'View Site Settings','site.view','Site','Can view website/site settings',1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),
(58,'Edit Site Settings','site.edit','Site','Can edit website/site settings',1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),
(59,'View Legal & Cookies','site.legal_cookies.view','Site','Can view legal and cookie settings',1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),
(60,'Manage Legal & Cookies','site.legal_cookies.manage','Site','Can manage legal and cookie settings',1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),
(61,'Delete Modules','modules.delete','Modules','Can uninstall/delete modules and elements',1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),
(62,'Install Themes','themes.install','Themes','Can install themes',1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),
(63,'Activate Themes','themes.activate','Themes','Can activate themes',1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP),
(64,'Delete Themes','themes.delete','Themes','Can delete themes',1,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP);


-- --------------------------------------------------------

-- Table: `wi_plugin`

CREATE TABLE IF NOT EXISTS `wi_plugin` (
  `plugin_id` int NOT NULL AUTO_INCREMENT,
  `plugin_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `plugin_slug` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `plugin_version` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plugin_author` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plugin_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `plugin_status` enum('enabled','disabled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'disabled',
  `plugin_installed` datetime DEFAULT CURRENT_TIMESTAMP,
  `plugin_updated` datetime DEFAULT NULL,
  `plugin_license_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`plugin_id`),
  UNIQUE KEY `plugin_slug` (`plugin_slug`),
  UNIQUE KEY `uq_wi_plugin_slug` (`plugin_slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_plugin_customers`

CREATE TABLE IF NOT EXISTS `wi_plugin_customers` (
  `customer_id` int NOT NULL AUTO_INCREMENT,
  `email` varchar(190) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `company_name` varchar(150) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`customer_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------

-- Table: `wi_plugin_download_logs`

CREATE TABLE IF NOT EXISTS `wi_plugin_download_logs` (
  `log_id` int NOT NULL AUTO_INCREMENT,
  `license_key` varchar(255) NOT NULL,
  `plugin_slug` varchar(100) NOT NULL,
  `site_url` varchar(255) DEFAULT NULL,
  `ip_address` varchar(64) DEFAULT NULL,
  `downloaded_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------

-- Table: `wi_plugin_invoices`

CREATE TABLE IF NOT EXISTS `wi_plugin_invoices` (
  `invoice_id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `invoice_number` varchar(50) NOT NULL,
  `invoice_total` decimal(10,2) NOT NULL,
  `invoice_currency` varchar(10) DEFAULT 'GBP',
  `invoice_status` enum('paid','pending','cancelled') DEFAULT 'pending',
  `invoice_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------

-- Table: `wi_plugin_licenses`

CREATE TABLE IF NOT EXISTS `wi_plugin_licenses` (
  `license_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `plugin_slug` varchar(100) NOT NULL,
  `license_key` varchar(255) NOT NULL,
  `license_type` enum('lifetime','subscription') DEFAULT 'lifetime',
  `license_status` enum('active','expired','revoked') DEFAULT 'active',
  `license_created` datetime DEFAULT CURRENT_TIMESTAMP,
  `license_expires` datetime DEFAULT NULL,
  PRIMARY KEY (`license_id`),
  UNIQUE KEY `license_key` (`license_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------

-- Table: `wi_plugin_orders`

CREATE TABLE IF NOT EXISTS `wi_plugin_orders` (
  `order_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `plugin_slug` varchar(100) NOT NULL,
  `order_price` decimal(10,2) NOT NULL,
  `order_currency` varchar(10) DEFAULT 'GBP',
  `order_status` enum('pending','paid','failed','refunded') DEFAULT 'pending',
  `payment_gateway` varchar(50) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------

-- Table: `wi_plugin_store`

CREATE TABLE IF NOT EXISTS `wi_plugin_store` (
  `store_id` int NOT NULL AUTO_INCREMENT,
  `plugin_slug` varchar(100) NOT NULL,
  `plugin_name` varchar(100) NOT NULL,
  `plugin_description` text,
  `plugin_version` varchar(20) DEFAULT NULL,
  `plugin_price` decimal(10,2) DEFAULT '0.00',
  `plugin_currency` varchar(10) DEFAULT 'GBP',
  `plugin_subscription` tinyint(1) DEFAULT '0',
  `plugin_preview` varchar(255) DEFAULT NULL,
  `plugin_download` varchar(255) DEFAULT NULL,
  `plugin_author` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------

-- Table: `wi_plugin_subscriptions`

CREATE TABLE IF NOT EXISTS `wi_plugin_subscriptions` (
  `subscription_id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `plugin_slug` varchar(100) NOT NULL,
  `subscription_price` decimal(10,2) NOT NULL,
  `subscription_currency` varchar(10) DEFAULT 'GBP',
  `subscription_period` enum('monthly','yearly') DEFAULT 'yearly',
  `subscription_status` enum('active','cancelled','expired') DEFAULT 'active',
  `subscription_start` datetime DEFAULT CURRENT_TIMESTAMP,
  `subscription_end` datetime DEFAULT NULL,
  `gateway_subscription_id` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------

-- Table: `wi_plugin_updates`

CREATE TABLE IF NOT EXISTS `wi_plugin_updates` (
  `update_id` int NOT NULL AUTO_INCREMENT,
  `plugin_slug` varchar(100) NOT NULL,
  `version` varchar(20) NOT NULL,
  `download_url` varchar(255) NOT NULL,
  `changelog` text,
  `requires_license` tinyint(1) DEFAULT '1',
  `released_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`update_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


-- --------------------------------------------------------

-- Table: `wi_role_permissions`

CREATE TABLE IF NOT EXISTS `wi_role_permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int unsigned NOT NULL,
  `permission_id` int unsigned NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wi_role_permissions` (`role_id`,`permission_id`),
  KEY `idx_wi_role_permissions_role` (`role_id`),
  KEY `idx_wi_role_permissions_permission` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_scripts`

CREATE TABLE IF NOT EXISTS `wi_scripts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `src` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `page` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_scripts_page` (`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_scripts` (`id`, `src`, `page`) VALUES
(1,'site/js/frameworks/JQuery.js','index'),
(2,'site/js/frameworks/bootstrap.js','index'),
(3,'site/js/login_panel/js/slide.js','index'),
(4,'site/js/frameworks/JQuery.js','alogin'),
(5,'site/js/frameworks/bootstrap.js','alogin'),
(6,'site/js/login_panel/js/slide.js','alogin'),
(7,'site/js/frameworks/JQuery.js','confirm'),
(8,'site/js/frameworks/bootstrap.js','confirm'),
(9,'site/js/login_panel/js/slide.js','confirm'),
(10,'site/js/frameworks/JQuery.js','contact_us'),
(11,'site/js/frameworks/bootstrap.js','contact_us'),
(12,'site/js/login_panel/js/slide.js','contact_us'),
(13,'site/js/frameworks/JQuery.js','profile'),
(14,'site/js/frameworks/bootstrap.js','profile'),
(15,'site/js/login_panel/js/slide.js','profile'),
(16,'site/js/frameworks/JQuery.js','passwordreset'),
(17,'site/js/frameworks/bootstrap.js','passwordreset'),
(18,'site/js/login_panel/js/slide.js','passwordreset'),
(19,'site/js/frameworks/JQuery.js','about_us'),
(20,'site/js/frameworks/bootstrap.js','about_us'),
(21,'site/js/login_panel/js/slide.js','about_us'),
(46,'site/js/frameworks/JQuery.js','about_us'),
(56,'site/js/frameworks/JQuery.js','register'),
(57,'site/js/frameworks/bootstrap.js','register'),
(58,'site/js/login_panel/js/slide.js','register'),
(59,'site/js/frameworks/JQuery.js','login'),
(60,'site/js/frameworks/bootstrap.js','login'),
(61,'site/js/login_panel/js/slide.js','login'),
(62,'site/js/frameworks/JQuery.js','dashboard'),
(63,'site/js/frameworks/bootstrap.js','dashboard'),
(64,'site/js/login_panel/js/slide.js','dashboard'),
(74,'site/js/frameworks/JQuery.js','documents'),
(75,'site/js/frameworks/bootstrap.js','documents'),
(76,'site/js/login_panel/js/slide.js','documents'),
(77,'site/js/frameworks/JQuery.js','training'),
(78,'site/js/frameworks/bootstrap.js','training'),
(79,'site/js/login_panel/js/slide.js','training'),
(98,'site/js/frameworks/JQuery.js','setup'),
(99,'site/js/frameworks/bootstrap.js','setup'),
(100,'site/js/login_panel/js/slide.js','setup'),
(104,'site/js/frameworks/JQuery.js','users'),
(105,'site/js/frameworks/bootstrap.js','users'),
(106,'site/js/login_panel/js/slide.js','users'),
(107,'site/js/frameworks/JQuery.js','settings'),
(108,'site/js/frameworks/bootstrap.js','settings'),
(109,'site/js/login_panel/js/slide.js','settings'),
(129,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','account'),
(130,'../../WIMembers/WICore/WIJ/WIAccount.js?v=20260524','account'),
(131,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','settings'),
(132,'../../WIMembers/WICore/WIJ/WISettings.js?v=20260524','settings'),
(133,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','membership'),
(134,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','upgrade'),
(135,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','downgrade'),
(136,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','payments'),
(137,'../../WIMembers/WICore/WIJ/WIPayments.js?v=20260524','payments'),
(138,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','userpayments'),
(139,'../../WIMembers/WICore/WIJ/WIPayments.js?v=20260524','userpayments'),
(140,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','transactions'),
(141,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','security'),
(142,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','usersecurity'),
(143,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','forms'),
(144,'../../WIMembers/WICore/WIJ/WIForms.js?v=20260524','forms'),
(145,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','support'),
(146,'../../WIMembers/WICore/WIJ/WIMembers.js?v=20260524','delete_profile');


-- --------------------------------------------------------

-- Table: `wi_secure_secrets`

CREATE TABLE IF NOT EXISTS `wi_secure_secrets` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `scope` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_key` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `cipher_text` mediumblob NOT NULL,
  `blind_index` char(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key_version` smallint unsigned NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wi_secure_secrets_scope_key` (`scope`,`item_key`),
  KEY `idx_wi_secure_secrets_blind_index` (`blind_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_sidebar`

CREATE TABLE IF NOT EXISTS `wi_sidebar` (
  `id` int NOT NULL AUTO_INCREMENT,
  `label` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `link` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#',
  `parent` int NOT NULL DEFAULT '0',
  `sort` int DEFAULT NULL,
  `lang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_sidebar_parent_sort` (`parent`,`sort`),
  KEY `idx_wi_sidebar_lang` (`lang`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_sidebar` (`id`, `label`, `link`, `parent`, `sort`, `lang`, `img`) VALUES
(1,'Settings','',0,0,'settings','settings'),
(2,'Site','WISite.php',1,0,'Site','site'),
(3,'Users','',0,1,'Users','users'),
(4,'Manage User','WIUser.php',3,0,'Manage_users','manage users'),
(5,'Roles','WIRoles.php',3,1,'roles','roles'),
(6,'Menus','WIMenu.php',1,1,'Menu','menu'),
(7,'Header','WIHeader.php',1,2,'Header','header'),
(8,'Modules','',0,2,'Modules','modules'),
(9,'Modules','WIModules.php',8,0,'Modules','modules'),
(10,'Pages','',0,3,'Pages','pages'),
(11,'Pages','WIPages.php',10,0,'Pages','pages'),
(12,'Plugins','',0,4,'Plugins','plugin'),
(13,'plugin','WIPlugin.php',12,0,'Plugin','plugin'),
(14,'Styling','WIStyling.php',1,3,'Styling','styling'),
(15,'Media','',0,5,'media','media'),
(16,'Media','WIMedia.php',15,0,'media','media'),
(17,'Multi Lang','WIMlang.php',1,4,'Multi Lang','multi_lang'),
(40,'Operations','',0,6,'Operations','modules');


-- --------------------------------------------------------

-- Table: `wi_site`

CREATE TABLE IF NOT EXISTS `wi_site` (
  `id` int NOT NULL AUTO_INCREMENT,
  `site_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_domain` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `site_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `favicon` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_host` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db_username` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db_pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `db_port` int NOT NULL DEFAULT '3306',
  `db_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mysql',
  `secure_session` enum('false','true') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `http_only` enum('false','true') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'true',
  `regenerate_id` enum('false','true') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'true',
  `use_only_cookie` enum('1','0') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `login_fingerprint` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `max_login_attempts` int NOT NULL DEFAULT '5',
  `redirect_after_login` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'WIMembers/profile.php',
  `password_encryption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bcrypt',
  `encryption_cost` int NOT NULL DEFAULT '12',
  `sha512_iterations` int NOT NULL DEFAULT '35000',
  `password_salt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `reset_key_life` int NOT NULL DEFAULT '24',
  `mail_confirm_required` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `register_confirm` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'confirm',
  `reg_pass_reset` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'passwordreset',
  `mailer` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'mailer',
  `smpt_host` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `smpt_port` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `smpt_username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `smpt_password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `smpt_encryption` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `social_callback_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'socialauth',
  `google_enabled` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `google_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_map_api` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `google_charts_api_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `facebook_enabled` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `facebook_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_enabled` enum('true','false') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `twitter_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_secret` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `default_lang` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'en',
  `multi_lang` enum('on','off') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'off',
  `lang_choice` enum('google','wilang') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'google',
  `bootstrap_version` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '4',
  `wicms_version` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'IVO-1.0',
  `left_sidebar` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `right_sidebar` enum('0','1') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `contact_email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_slideshow`

CREATE TABLE IF NOT EXISTS `wi_slideshow` (
  `id` int NOT NULL AUTO_INCREMENT,
  `no_slides` int NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_slideshow` (`id`, `no_slides`, `name`) VALUES
(1,0,'default');


-- --------------------------------------------------------

-- Table: `wi_slideshow_slides`

CREATE TABLE IF NOT EXISTS `wi_slideshow_slides` (
  `id` int NOT NULL AUTO_INCREMENT,
  `slide_id` int NOT NULL,
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-target` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-slotamount` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-masterspeed` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-lazyload` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-transition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-slide-too` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-bgfit` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-bgposition` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-bgrepeat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-x` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-y` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-speed` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-start` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-easing` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-endspeed` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `data-endeasing` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `img` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `alt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `brand_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `href` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `button` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `clas` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_slideshow_slides_slide_id` (`slide_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_social`

CREATE TABLE IF NOT EXISTS `wi_social` (
  `id` int NOT NULL AUTO_INCREMENT,
  `href` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_social` (`id`, `href`, `name`) VALUES
(1,'#','facebook'),
(2,'#','twitter'),
(3,'#','instagram');


-- --------------------------------------------------------

-- Table: `wi_social_logins`

CREATE TABLE IF NOT EXISTS `wi_social_logins` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `provider` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'email',
  `provider_id` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wi_social_logins_provider` (`provider`,`provider_id`),
  KEY `idx_wi_social_logins_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_system_versions`

CREATE TABLE IF NOT EXISTS `wi_system_versions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `component` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `version` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `applied_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wi_system_versions_component` (`component`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_system_versions` (`id`, `component`, `version`, `applied_at`) VALUES
(1,'wicms-ivo','1.0.0','2026-03-16 00:49:19');


-- --------------------------------------------------------

-- Table: `wi_tasks`

CREATE TABLE IF NOT EXISTS `wi_tasks` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `percent` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_theme`

CREATE TABLE IF NOT EXISTS `wi_theme` (
  `id` int NOT NULL AUTO_INCREMENT,
  `theme` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `destination` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `in_use` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wi_theme_theme` (`theme`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_theme` (`id`, `theme`, `destination`, `in_use`) VALUES
(1,'WICMS','WITheme/WICMS/',1),
(2,'Galaxy','WITheme/Galaxy/',0);


-- --------------------------------------------------------

-- Table: `wi_track`

CREATE TABLE IF NOT EXISTS `wi_track` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ref` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `agent` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tracking_page_code` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tracking_page_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_count` int NOT NULL DEFAULT '0',
  `dt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_track_ip` (`ip`),
  KEY `idx_wi_track_dt` (`dt`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_trans`

CREATE TABLE IF NOT EXISTS `wi_trans` (
  `id` int NOT NULL AUTO_INCREMENT,
  `lang` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `keyword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `translation` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wi_trans_lang_keyword` (`lang`,`keyword`),
  KEY `idx_wi_trans_keyword` (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_trans` (`id`, `lang`, `keyword`, `translation`) VALUES
(1,'en','site_name','WICMS'),
(2,'en','home','Home'),
(3,'en','users','Users'),
(4,'en','blog','Blog'),
(5,'en','shop','Shop'),
(6,'en','email','Email'),
(7,'en','login','Login'),
(8,'en','username','Username'),
(9,'en','password','Password'),
(10,'en','your_email','Your Email'),
(11,'en','login_with','Login with'),
(12,'en','email_confirmed','Email confirmed'),
(13,'en','create_account','Create Account'),
(14,'en','logging_in','Logging In'),
(15,'en','working','Working...'),
(16,'en','info','Info'),
(17,'en','admin','Admin'),
(18,'en','add_user','Add User'),
(19,'en','action','Action'),
(20,'en','register_date','Register Date'),
(21,'en','forgot_password','Forget password'),
(22,'en','repeat_password','Repeat password'),
(23,'en','reset_password','Reset password'),
(24,'en','email_confirmation','Email Confirmation'),
(25,'en','you_can_login_now','You can <a href=\"{link}\">log in</a> now.'),
(26,'en','my_profile','My Profile'),
(27,'en','admin_panel','Admin Panel'),
(28,'en','member_panel','Member Panel'),
(29,'en','contact_us','Contact Us'),
(30,'en','about_us','About Us'),
(31,'en','admins','Administrator'),
(32,'en','logs','Logs'),
(33,'en','visitors','Visitors');


-- --------------------------------------------------------

-- Table: `wi_user_details`

CREATE TABLE IF NOT EXISTS `wi_user_details` (
  `id_user_details` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `first_name` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(35) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bio_body` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `website` varchar(225) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `friend_array` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('Available','in_chat') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Available',
  PRIMARY KEY (`id_user_details`),
  UNIQUE KEY `uk_wi_user_details_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_user_roles`

CREATE TABLE IF NOT EXISTS `wi_user_roles` (
  `role_id` int NOT NULL AUTO_INCREMENT,
  `role` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`),
  UNIQUE KEY `uk_wi_user_roles_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_user_roles` (`role_id`, `role`) VALUES
(1,'User'),
(2,'VIP'),
(3,'Moderator'),
(4,'Developer'),
(5,'Administrator'),
(6,'Head Administrator'),
(7,'Owner'),
(90,'Super Admin'),
(91,'Platform Owner');


-- --------------------------------------------------------

-- Table: `wi_visitors_log`

CREATE TABLE IF NOT EXISTS `wi_visitors_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `ip` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `country` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `city` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_visitors_log_page` (`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_bug_reporter_settings`

CREATE TABLE IF NOT EXISTS `wi_bug_reporter_settings` (
  `setting_key` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `value_type` enum('string','int','bool','json') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'string',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_bug_reporter_settings` (`setting_key`, `setting_value`, `value_type`, `created_at`, `updated_at`) VALUES
('allow_attachments','0','bool','2026-06-22 07:38:09','2026-06-22 20:16:55'),
('enabled','1','bool','2026-06-22 07:38:09','2026-06-22 20:16:55'),
('enabled_admin','1','bool','2026-06-22 07:38:09','2026-06-22 20:16:55'),
('enabled_member','1','bool','2026-06-22 07:38:09','2026-06-22 20:16:55'),
('enabled_public','0','bool','2026-06-22 07:38:09','2026-06-22 20:16:55'),
('forwarding_enabled','0','bool','2026-06-22 07:38:09','2026-06-22 20:16:55'),
('forwarding_mode','local_only','string','2026-06-22 07:38:09','2026-06-22 20:16:55'),
('include_technical_context','1','bool','2026-06-22 07:38:09','2026-06-22 20:16:55'),
('wilabs_support_email','','string','2026-06-22 07:38:09','2026-06-22 20:16:55');


-- --------------------------------------------------------

-- Table: `wi_system_bug_reports`

CREATE TABLE IF NOT EXISTS `wi_system_bug_reports` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `business_id` bigint unsigned DEFAULT NULL,
  `site_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `user_role_id` bigint unsigned DEFAULT NULL,
  `area_type` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `module_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_key` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `action_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `severity` enum('low','medium','high','critical') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medium',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('open','in_progress','resolved','closed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'open',
  `forward_to_wilabs` tinyint(1) NOT NULL DEFAULT '0',
  `forwarding_status` enum('not_enabled','pending','sent','failed') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'not_enabled',
  `forwarding_attempts` int unsigned NOT NULL DEFAULT '0',
  `forwarded_at` datetime DEFAULT NULL,
  `forwarding_error` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `wilabs_reference` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_to_user_id` bigint unsigned DEFAULT NULL,
  `browser` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bug_reports_business_id` (`business_id`),
  KEY `idx_bug_reports_site_id` (`site_id`),
  KEY `idx_bug_reports_user_id` (`user_id`),
  KEY `idx_bug_reports_user_role_id` (`user_role_id`),
  KEY `idx_bug_reports_area_type` (`area_type`),
  KEY `idx_bug_reports_module_name` (`module_name`),
  KEY `idx_bug_reports_page_key` (`page_key`),
  KEY `idx_bug_reports_action_name` (`action_name`),
  KEY `idx_bug_reports_severity` (`severity`),
  KEY `idx_bug_reports_status` (`status`),
  KEY `idx_bug_reports_assigned_to_user_id` (`assigned_to_user_id`),
  KEY `idx_bug_reports_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_system_bug_report_messages`

CREATE TABLE IF NOT EXISTS `wi_system_bug_report_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `report_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `message_type` enum('report','comment','status_update','internal_note') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'comment',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_bug_report_messages_report_id` (`report_id`),
  KEY `idx_bug_report_messages_user_id` (`user_id`),
  KEY `idx_bug_report_messages_message_type` (`message_type`),
  KEY `idx_bug_report_messages_created_at` (`created_at`),
  CONSTRAINT `fk_bug_report_messages_report_id` FOREIGN KEY (`report_id`) REFERENCES `wi_system_bug_reports` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_consent_categories`

CREATE TABLE IF NOT EXISTS `wi_consent_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_key` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '100',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_wi_consent_category_key` (`category_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_consent_categories` (`id`, `category_key`, `label`, `description`, `is_required`, `is_enabled`, `sort_order`, `created_at`, `updated_at`) VALUES
(1,'essential','Strictly necessary','Required for login, security, sessions, CSRF protection and core site operation.',1,1,10,'2026-06-24 09:06:54','2026-06-24 08:17:40'),
(2,'preferences','Preferences','Remembers theme, accessibility, language and display choices.',0,1,20,'2026-06-24 09:06:54','2026-06-24 08:17:40'),
(3,'analytics','Analytics','Helps site owners understand how the website is used.',0,0,30,'2026-06-24 09:06:54','2026-06-24 08:17:40'),
(4,'marketing','Marketing','Advertising, remarketing, tracking pixels and ad personalisation.',0,0,40,'2026-06-24 09:06:54','2026-06-24 08:17:40'),
(5,'embedded','Embedded content','Videos, maps, social embeds and other third-party embedded content.',0,0,50,'2026-06-24 09:06:54','2026-06-24 08:17:40');


-- --------------------------------------------------------

-- Table: `wi_consent_logs`

CREATE TABLE IF NOT EXISTS `wi_consent_logs` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `consent_id` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `member_id` int DEFAULT NULL,
  `accepted_categories` text COLLATE utf8mb4_unicode_ci,
  `policy_version` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `ip_hash` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent_hash` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `source` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public_banner',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_wi_consent_logs_consent_id` (`consent_id`),
  KEY `idx_wi_consent_logs_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_consent_scripts`

CREATE TABLE IF NOT EXISTS `wi_consent_scripts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `script_key` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_key` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'body_end',
  `script_type` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'external',
  `src` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `inline_script` mediumtext COLLATE utf8mb4_unicode_ci,
  `is_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `sort_order` int NOT NULL DEFAULT '100',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_wi_consent_script_key` (`script_key`),
  KEY `idx_wi_consent_scripts_category` (`category_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_consent_settings`

CREATE TABLE IF NOT EXISTS `wi_consent_settings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `enabled` tinyint(1) NOT NULL DEFAULT '0',
  `banner_position` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bottom',
  `theme_mode` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'match_site',
  `custom_primary_color` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#2563eb',
  `custom_background_color` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#ffffff',
  `custom_text_color` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#111827',
  `show_accept_all` tinyint(1) NOT NULL DEFAULT '1',
  `show_reject_non_essential` tinyint(1) NOT NULL DEFAULT '1',
  `show_manage_choices` tinyint(1) NOT NULL DEFAULT '1',
  `show_save_preferences` tinyint(1) NOT NULL DEFAULT '1',
  `banner_title` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Your privacy choices',
  `banner_text` text COLLATE utf8mb4_unicode_ci,
  `privacy_policy_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'privacy.php',
  `cookie_policy_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cookies.php',
  `terms_url` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'terms.php',
  `policy_version` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `google_consent_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `google_consent_mode` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'basic',
  `google_default_denied` tinyint(1) NOT NULL DEFAULT '1',
  `gtm_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `gtm_container_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ga4_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `ga4_measurement_id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `marketing_remarketing_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_consent_settings` (`id`, `enabled`, `banner_position`, `theme_mode`, `custom_primary_color`, `custom_background_color`, `custom_text_color`, `show_accept_all`, `show_reject_non_essential`, `show_manage_choices`, `show_save_preferences`, `banner_title`, `banner_text`, `privacy_policy_url`, `cookie_policy_url`, `terms_url`, `policy_version`, `google_consent_enabled`, `google_consent_mode`, `google_default_denied`, `gtm_enabled`, `gtm_container_id`, `ga4_enabled`, `ga4_measurement_id`, `marketing_remarketing_enabled`, `created_at`, `updated_at`) VALUES
(1,1,'modal','match_site','#2563eb','#ffffff','#111827',1,1,1,1,'Your privacy choices','We use cookies and similar technologies to keep the site secure, remember preferences, and improve the site. You can accept all, reject non-essential, or manage your choices.','privacy.php','cookies.php','terms.php','1.0',0,'basic',1,0,'',0,'',0,'2026-06-24 09:06:54','2026-06-24 08:17:40');


-- --------------------------------------------------------

-- Table: `wi_legal_pages`

CREATE TABLE IF NOT EXISTS `wi_legal_pages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `page_key` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` mediumtext COLLATE utf8mb4_unicode_ci,
  `version` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0',
  `is_published` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_wi_legal_page_key` (`page_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_legal_pages` (`id`, `page_key`, `title`, `slug`, `content`, `version`, `is_published`, `created_at`, `updated_at`) VALUES
(1,'privacy_policy','Privacy Policy','privacy.php','','1.0',0,'2026-06-24 09:06:54',NULL),
(2,'cookie_policy','Cookie Policy','cookies.php','','1.0',0,'2026-06-24 09:06:54',NULL),
(3,'terms','Terms','terms.php','','1.0',0,'2026-06-24 09:06:54',NULL);


-- --------------------------------------------------------

-- Table: `wi_language_settings`

CREATE TABLE IF NOT EXISTS `wi_language_settings` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_unicode_ci,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wi_language_settings_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_language_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1,'enabled','on','2026-06-24 11:53:18'),
(2,'public_switcher','on','2026-06-24 11:53:18'),
(3,'admin_switcher','on','2026-06-24 11:53:18'),
(4,'default_public_lang','en','2026-06-24 11:47:17'),
(5,'default_admin_lang','en','2026-06-24 11:47:17'),
(6,'fallback_lang','en','2026-06-24 11:47:17'),
(7,'translation_engine','wilang','2026-06-24 11:47:17'),
(8,'date_format','d/m/Y','2026-06-24 11:47:17'),
(9,'time_format','H:i','2026-06-24 11:47:17'),
(10,'number_format','uk','2026-06-24 11:47:17'),
(11,'rtl_support','off','2026-06-24 11:47:17');


-- --------------------------------------------------------

-- Table: `wi_import_export_jobs`

CREATE TABLE IF NOT EXISTS `wi_import_export_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `job_type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `module_code` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `org_business_id` bigint unsigned DEFAULT NULL,
  `org_site_id` bigint unsigned DEFAULT NULL,
  `pack_code` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_code` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('queued','processing','completed','failed','rolled_back') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `rows_total` int unsigned NOT NULL DEFAULT '0',
  `rows_created` int unsigned NOT NULL DEFAULT '0',
  `rows_updated` int unsigned NOT NULL DEFAULT '0',
  `rows_skipped` int unsigned NOT NULL DEFAULT '0',
  `media_id` bigint unsigned DEFAULT NULL,
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_message` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `metadata_json` json DEFAULT NULL,
  `created_by_user_id` bigint unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_import_export_module` (`module_code`,`job_type`,`status`),
  KEY `idx_wi_import_export_scope` (`org_business_id`,`org_site_id`,`pack_code`,`profile_code`),
  KEY `idx_wi_import_export_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_media_audit`

CREATE TABLE IF NOT EXISTS `wi_media_audit` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `media_id` int unsigned NOT NULL,
  `event_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `system_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` int unsigned DEFAULT NULL,
  `link_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `org_business_id` int unsigned DEFAULT NULL,
  `org_site_id` int unsigned DEFAULT NULL,
  `org_department_id` int unsigned DEFAULT NULL,
  `created_by_user_id` int unsigned DEFAULT NULL,
  `context_json` json DEFAULT NULL,
  `ip_address_hash` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent_hash` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_media_audit_media_id` (`media_id`),
  KEY `idx_media_audit_event_type` (`event_type`),
  KEY `idx_media_audit_system_entity` (`system_code`,`entity_type`,`entity_id`),
  KEY `idx_media_audit_business_site` (`org_business_id`,`org_site_id`),
  KEY `idx_media_audit_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_media_events`

CREATE TABLE IF NOT EXISTS `wi_media_events` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `media_id` int unsigned DEFAULT NULL,
  `event_type` varchar(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `system_code` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entity_id` int unsigned DEFAULT NULL,
  `link_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `org_business_id` int unsigned DEFAULT NULL,
  `org_site_id` int unsigned DEFAULT NULL,
  `org_department_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_payload` json DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_wi_media_events_media` (`media_id`),
  KEY `idx_wi_media_events_type` (`event_type`),
  KEY `idx_wi_media_events_target` (`system_code`,`entity_type`,`entity_id`),
  KEY `idx_wi_media_events_business_site` (`org_business_id`,`org_site_id`),
  KEY `idx_wi_media_events_created` (`created_at`),
  CONSTRAINT `fk_wi_media_events_media` FOREIGN KEY (`media_id`) REFERENCES `wi_media` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_media_links`

CREATE TABLE IF NOT EXISTS `wi_media_links` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `media_id` int unsigned NOT NULL,
  `system_code` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int unsigned NOT NULL,
  `link_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `org_business_id` int unsigned DEFAULT NULL,
  `org_site_id` int unsigned DEFAULT NULL,
  `org_department_id` int unsigned DEFAULT NULL,
  `created_by_user_id` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by_user_id` int unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_media_links_media` (`media_id`),
  KEY `idx_wi_media_links_target` (`system_code`,`entity_type`,`entity_id`,`link_type`),
  KEY `idx_wi_media_links_business_site` (`org_business_id`,`org_site_id`),
  KEY `idx_wi_media_links_deleted` (`deleted_at`),
  CONSTRAINT `fk_wi_media_links_media` FOREIGN KEY (`media_id`) REFERENCES `wi_media` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_member_forms`

CREATE TABLE IF NOT EXISTS `wi_member_forms` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_code` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `form_type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'general',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `org_business_id` bigint unsigned DEFAULT NULL,
  `org_site_id` bigint unsigned DEFAULT NULL,
  `org_department_id` bigint unsigned DEFAULT NULL,
  `role_id` int unsigned DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `due_days_after_assignment` int unsigned DEFAULT NULL,
  `schema_json` json DEFAULT NULL,
  `sort_order` int unsigned NOT NULL DEFAULT '100',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wi_member_forms_code` (`form_code`),
  KEY `idx_wi_member_forms_scope` (`org_business_id`,`org_site_id`,`org_department_id`,`role_id`),
  KEY `idx_wi_member_forms_active` (`is_active`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `wi_member_forms` (`id`, `form_code`, `title`, `form_type`, `description`, `org_business_id`, `org_site_id`, `org_department_id`, `role_id`, `is_required`, `is_active`, `due_days_after_assignment`, `schema_json`, `sort_order`, `created_at`, `updated_at`) VALUES
(2,'policy_acknowledgement_general','General policy acknowledgement','acknowledgement','Acknowledge that you have read the current workplace policies assigned to your role.',NULL,NULL,NULL,NULL,1,1,3,'[{\"key\": \"policy_acknowledged\", \"type\": \"checkbox\", \"label\": \"I acknowledge the policy requirement\", \"required\": true}]',20,'2026-06-08 10:20:51','2026-06-08 10:20:51'),
(3,'health_safety_declaration','Health and safety declaration','declaration','Confirm you understand the basic health and safety responsibilities for your role.',NULL,NULL,NULL,NULL,1,1,7,'[{\"key\": \"safe_to_work\", \"type\": \"checkbox\", \"label\": \"I am fit and safe to work today\", \"required\": true}]',30,'2026-06-08 10:20:51','2026-06-08 10:20:51');


-- --------------------------------------------------------

-- Table: `wi_member_form_submissions`

CREATE TABLE IF NOT EXISTS `wi_member_form_submissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `form_id` bigint unsigned NOT NULL,
  `user_id` int NOT NULL,
  `status` enum('draft','submitted','reviewed','approved','rejected','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `answers_json` json DEFAULT NULL,
  `submitted_at` datetime DEFAULT NULL,
  `reviewed_by_user_id` int DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `review_notes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wi_member_form_user` (`form_id`,`user_id`),
  KEY `idx_wi_member_form_submissions_user` (`user_id`,`status`),
  KEY `idx_wi_member_form_submissions_status` (`status`,`submitted_at`),
  CONSTRAINT `fk_wi_member_form_submissions_form` FOREIGN KEY (`form_id`) REFERENCES `wi_member_forms` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_installed_packs`

CREATE TABLE IF NOT EXISTS `wi_installed_packs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pack_code` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pack_name` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `pack_type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'industry',
  `version` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1.0.0',
  `pack_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'installed',
  `is_system` tinyint(1) NOT NULL DEFAULT '0',
  `is_context_selectable` tinyint(1) NOT NULL DEFAULT '1',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `metadata_json` json DEFAULT NULL,
  `installed_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `plugin_type` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operations_plugin',
  `is_industry_pack` tinyint(1) NOT NULL DEFAULT '0',
  `industry_group` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `plugin_code` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `setup_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `setup_profile_source` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_wi_installed_pack_code` (`pack_code`),
  KEY `idx_wi_installed_packs_type_status` (`pack_type`,`pack_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_pack_profiles`

CREATE TABLE IF NOT EXISTS `wi_pack_profiles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pack_code` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_code` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_name` varchar(190) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_group` varchar(120) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `profile_status` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `default_scope` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'business',
  `sort_order` int unsigned NOT NULL DEFAULT '100',
  `lens_json` json DEFAULT NULL,
  `setup_sections_json` json DEFAULT NULL,
  `metadata_json` json DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `preset_code` varchar(160) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `setup_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_wi_pack_profile` (`pack_code`,`profile_code`),
  KEY `idx_wi_pack_profiles_status` (`pack_code`,`profile_status`,`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_package_dependency_registry`

CREATE TABLE IF NOT EXISTS `wi_package_dependency_registry` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `package_code` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dependency_code` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dependency_type` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'required',
  `metadata_json` json DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wi_package_dependency_registry` (`package_code`,`dependency_code`,`dependency_type`),
  KEY `idx_wi_package_dependency_registry_dependency` (`dependency_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_package_manifest_registry`

CREATE TABLE IF NOT EXISTS `wi_package_manifest_registry` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `folder_name` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `folder_slug` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `canonical_code` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `legacy_codes_json` json DEFAULT NULL,
  `display_name` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `package_type` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'operational_plugin',
  `is_industry_pack` tinyint(1) NOT NULL DEFAULT '0',
  `setup_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `show_in_setup_wizard` tinyint(1) NOT NULL DEFAULT '0',
  `industry_group` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `dependencies_json` json DEFAULT NULL,
  `optional_integrations_json` json DEFAULT NULL,
  `provides_json` json DEFAULT NULL,
  `capabilities_json` json DEFAULT NULL,
  `feature_flags_json` json DEFAULT NULL,
  `sidebar_json` json DEFAULT NULL,
  `features_json` json DEFAULT NULL,
  `package_tiers_json` json DEFAULT NULL,
  `install_map_json` json DEFAULT NULL,
  `setup_contract_json` json DEFAULT NULL,
  `manifest_json` json DEFAULT NULL,
  `source_files_json` json DEFAULT NULL,
  `status` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `last_scanned_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wi_package_manifest_registry_code` (`canonical_code`),
  KEY `idx_wi_package_manifest_registry_folder` (`folder_slug`),
  KEY `idx_wi_package_manifest_registry_type` (`package_type`),
  KEY `idx_wi_package_manifest_registry_setup` (`is_industry_pack`,`show_in_setup_wizard`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- --------------------------------------------------------

-- Table: `wi_package_sidebar_registry`

CREATE TABLE IF NOT EXISTS `wi_package_sidebar_registry` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `package_code` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(120) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#',
  `parent_label` varchar(120) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `top_level` tinyint(1) NOT NULL DEFAULT '0',
  `standalone_workspace` tinyint(1) NOT NULL DEFAULT '0',
  `show_when_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int NOT NULL DEFAULT '100',
  `metadata_json` json DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wi_package_sidebar_registry` (`package_code`,`label`),
  KEY `idx_wi_package_sidebar_registry_package` (`package_code`),
  KEY `idx_wi_package_sidebar_registry_parent` (`parent_label`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- Grant all core WICMS permissions to Super Admin and Platform Owner.

INSERT IGNORE INTO `wi_role_permissions` (`role_id`, `permission_id`)
SELECT 90, `id` FROM `wi_permissions` WHERE `code` NOT LIKE 'compliance.%' AND `code` NOT LIKE 'wikitchencompli.%';

INSERT IGNORE INTO `wi_role_permissions` (`role_id`, `permission_id`)
SELECT 91, `id` FROM `wi_permissions` WHERE `code` NOT LIKE 'compliance.%' AND `code` NOT LIKE 'wikitchencompli.%';

SET FOREIGN_KEY_CHECKS=1;
