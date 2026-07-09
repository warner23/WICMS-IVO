<?php


class Install
{

	private $WIdb;

	public function Installer($name, $dom, $script, $session_secure, $http, $session_regenerate, $cookieonly, $login_fingerprint, $max_login_attempts, $redirect_after_login, $encryption, $cost, $mailer,  $db_host, $db_name,  $db_pass, $db_username, $bootstrap_version, $salt, $admin_password, $admin_username, $email_address)
	{

       $Version = "3.0";

   $multi_lang = "on";

   $default_lang = "en";

   $twitter_enabled = "false";

   $facebook_enabled = "false";

   $google_enabled = "false";

    $social_callback_url = "socialauth";

   $reg_pass_reset = "passwordreset";

    $register_confirm = "confirm";

   $mail_confirm_required = "false";

    $reset_key_life = "24";

   $dat = date("Y-m-d H:i:s");
   $dateOnly = date("Y-m-d");
   $ip = getenv('REMOTE_ADDR');
   $role = "91";

   

    try{
      $this->WIdb = WIdb::createInstallServerConnection($db_host, $db_username, $db_pass);
      $this->WIdb->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }catch (PDOException $e) {
            die ( 'Connection failed: ' . $e->getMessage() );
        }

        if (!preg_match('/^[A-Za-z0-9_]+$/', $db_name)) {
            die(json_encode(array('status' => 'error', 'message' => 'Database name may only contain letters, numbers and underscores.')));
        }
		$dbname = "`" . str_replace("`", "``", $db_name) . "`";
		$this->WIdb->query("CREATE DATABASE IF NOT EXISTS $dbname CHARACTER SET utf8 COLLATE utf8_unicode_ci");
		$this->WIdb->query("use $dbname");
		$q = "CREATE TABLE IF NOT EXISTS `wi_admin_info_box` 
(
  `id` int(11) NOT NULL AUTO_INCREMENT,

  `name` varchar(255) NOT NULL,
 
 `info` varchar(255) NOT NULL,

  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Dumping data for table `wi_admin_info_box`
--


INSERT INTO `wi_admin_info_box` (`id`, `name`, `info`) VALUES

(1, 'Bounce Rate', 'bounce'),

(2, 'Unique Visitors', 'visitors'),

(3, 'User Registrations', 'regUser');

    CREATE TABLE IF NOT EXISTS `wi_admin_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL ,
  `link` varchar(100) NOT NULL DEFAULT '#',
  `parent` int(11) NOT NULL DEFAULT '0',
  `sort` int(11) DEFAULT NULL,
  `lang` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wi_admin_menu`
--

INSERT INTO `wi_admin_menu` (`id`, `label`, `link`, `parent`, `sort`, `lang`) VALUES
(1, 'Administrators', 'WIAdmin.php', 0, 0, 'admins'),
(2, 'Logs', 'WILogs.php', 0, 1, 'logs');

-- --------------------------------------------------------

--
-- Table structure for table `wi_admin_msg`
--

CREATE TABLE IF NOT EXISTS `wi_admin_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` varchar(255) NOT NULL,
  `wtime` datetime NOT NULL,
  `user_id` int(11) NOT NULL,
  `attachments` enum('y','n') NOT NULL DEFAULT 'n',
  `file` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


-- --------------------------------------------------------

--
-- Table structure for table `wi_admin_todo_list`
--

CREATE TABLE IF NOT EXISTS `wi_admin_todo_list` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `timeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `completed` enum('y','n') NOT NULL DEFAULT 'n',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;




-- --------------------------------------------------------

--
-- Table structure for table `wi_blockedusers`
--

CREATE TABLE IF NOT EXISTS `wi_blockedusers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `blocker` varchar(16) NOT NULL,
  `blockee` varchar(16) NOT NULL,
  `blockdate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_contact_message`
--

CREATE TABLE IF NOT EXISTS `wi_contact_message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `time_sent` datetime NOT NULL,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `wi_css`
--

CREATE TABLE IF NOT EXISTS `wi_css` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `href` varchar(255) NOT NULL,
  `rel` varchar(255) NOT NULL,
  `page` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wi_css`
--

INSERT INTO `wi_css` (`id`, `href`, `rel`, `page`) VALUES
(1, 'site/css/frameworks/bootstrap.css', 'stylesheet', 'index'),
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
(13, 'site/css/frameworks/bootstrap.css', 'stylesheet', 'contfirm'),
(14, 'site/css/login_panel/css/slide.css', 'stylesheet', 'contfirm'),
(15, 'site/css/frameworks/menus.css', 'stylesheet', 'contfirm'),
(16, 'site/css/style.css', 'stylesheet', 'contfirm'),
(17, 'site/css/font-awesome.css', 'stylesheet', 'contfirm'),
(18, 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'contfirm'),
(19, 'site/css/frameworks/bootstrap.css', 'stylesheet', 'contact_us'),
(20, 'site/css/login_panel/css/slide.css', 'stylesheet', 'confirm'),
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
(31, 'user/css/profile.css', 'stylesheet', 'profile');

-- --------------------------------------------------------

--
-- Table structure for table `wi_footer`
--

CREATE TABLE IF NOT EXISTS `wi_footer` (
  `footer_id` int(11) NOT NULL AUTO_INCREMENT,
  `footer_content` varchar(255) NOT NULL,
  `footer_linking` varchar(255) NOT NULL,
  `website_name` varchar(255) NOT NULL,
  PRIMARY KEY (`footer_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wi_footer`
--

INSERT INTO `wi_footer` (`footer_id`, `footer_content`, `footer_linking`, `website_name`) VALUES
(1, '', '', '$name');

-- --------------------------------------------------------

--
-- Table structure for table `wi_friends_requests`
--

CREATE TABLE IF NOT EXISTS `wi_friends_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timedate` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_header`
--

CREATE TABLE IF NOT EXISTS `wi_header` (
  `header_id` int(11) NOT NULL AUTO_INCREMENT,
  `logo` varchar(255) NOT NULL,
  `bk_header_image` varchar(255) NOT NULL,
  `header_image` varchar(255) NOT NULL,
  `header_content` varchar(255) NOT NULL,
  `header_slogan` varchar(255) NOT NULL,
  PRIMARY KEY (`header_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wi_header`
--

INSERT INTO `wi_header` (`header_id`, `logo`, `bk_header_image`, `header_image`, `header_content`, `header_slogan`) VALUES
(1, 'wi_cms_logo.jpg', '', 'bk_header', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `wi_lang`
--

CREATE TABLE IF NOT EXISTS `wi_lang` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(10) NOT NULL,
  `name` varchar(255) NOT NULL,
  `lang_flag` text NOT NULL,
  `href` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lang` (`lang`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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

CREATE TABLE IF NOT EXISTS `wi_login_attempts` (
  `id_login_attempts` int(11) NOT NULL AUTO_INCREMENT,
  `ip_addr` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `attempt_number` int(11) NOT NULL DEFAULT '1',
  `date` date NOT NULL,
  PRIMARY KEY (`id_login_attempts`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `wi_logs`
--

CREATE TABLE IF NOT EXISTS `wi_logs` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `user` varchar(45) NOT NULL,
  `opperation` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `wi_members`
--

CREATE TABLE IF NOT EXISTS `wi_members` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `username` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `confirmation_key` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `confirmed` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `password_reset_key` varchar(250) COLLATE utf8_unicode_ci NOT NULL ,
  `password_reset_confirmed` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  `password_reset_timestamp` datetime NOT NULL ,
  `register_date` date NOT NULL,
  `user_role` int(4) NOT NULL DEFAULT '1',
  `last_login` datetime NOT NULL ,
  `ip_addr` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `banned` enum('Y','N') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'N',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



--
-- Table structure for table `wi_menu`
--

CREATE TABLE IF NOT EXISTS `wi_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL ,
  `link` varchar(100) NOT NULL DEFAULT '#',
  `parent` int(11) NOT NULL DEFAULT '0',
  `sort` int(11) DEFAULT NULL,
  `lang` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wi_menu`
--

INSERT INTO `wi_menu` (`id`, `label`, `link`, `parent`, `sort`, `lang`) VALUES
(1, 'Home', 'index.php', 0, 0, 'home');

-- --------------------------------------------------------

--
-- Table structure for table `wi_meta`
--

CREATE TABLE IF NOT EXISTS `wi_meta` (
  `meta_id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  PRIMARY KEY (`meta_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wi_meta`
--

INSERT INTO `wi_meta` (`meta_id`, `page`, `name`, `content`, `author`) VALUES
(1, 'index', 'viewport', 'width=device-width, initial-scale=1', 'Jules Warner'),
(2, 'index', 'description', 'Warner-Infinity Content Management System with simplified back end', 'Jules Warner'),
(3, 'index', 'keywords', 'WI, WICMS, System, UI', 'Jules Warner'),
(4, 'index', 'author', 'warner-infinity', 'Jules Warner'),
(5, 'alogin', 'viewport', 'width=device-width, initial-scale=1', 'Jules Warner'),
(6, 'alogin', 'description', 'Warner-Infinity Content Management System', 'Jules Warner'),
(7, 'alogin', 'keywords', 'WI, WICMS, System', 'Jules Warner'),
(8, 'alogin', 'author', 'warner-infinity', 'Jules Warner'),
(9, 'confirm', 'viewport', 'width=device-width, initial-scale=1', 'Jules Warner'),
(10, 'confirm', 'description', 'Warner-Infinity Content Management System', 'Jules Warner'),
(11, 'confirm', 'keywords', 'WI, WICMS, System', 'Jules Warner'),
(12, 'confirm', 'author', 'warner-infinity', 'Jules Warner'),
(13, 'contact_us', 'viewport', 'width=device-width, initial-scale=1', 'Jules Warner'),
(14, 'contact_us', 'description', 'Warner-Infinity Content Management System', 'Jules Warner'),
(15, 'contact_us', 'keywords', 'WI, WICMS, System', 'Jules Warner'),
(16, 'contact_us', 'author', 'warner-infinity', 'Jules Warner'),
(17, 'profile', 'viewport', 'width=device-width, initial-scale=1', 'Jules Warner'),
(18, 'profile', 'description', 'Warner-Infinity Content Management System with simplified back end', 'Jules Warner'),
(19, 'profile', 'keywords', 'WI, WICMS, System, UI', 'Jules Warner'),
(20, 'profile', 'author', 'warner-infinity', 'Jules Warner');

-- --------------------------------------------------------

--
-- Table structure for table `wi_mod`
--

CREATE TABLE IF NOT EXISTS `wi_mod` (
  `mod_id` int(11) NOT NULL AUTO_INCREMENT,
  `mod_status` enum('enabled','disabled') NOT NULL DEFAULT 'disabled',
  `mod_powered` enum('power_on','power_off') NOT NULL DEFAULT 'power_off',
  `mod_type` enum('element','custom') NOT NULL DEFAULT 'element',
  `mod_author` varchar(55) DEFAULT NULL,
  `module_name` varchar(55) NOT NULL,
  `Mod_description` varchar(255) DEFAULT NULL,
  `mod_font` varchar(225) NOT NULL,
  PRIMARY KEY (`mod_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wi_mod`
--

INSERT INTO `wi_mod` (`mod_id`, `mod_status`, `mod_powered`, `mod_type`, `mod_author`, `module_name`, `Mod_description`, `mod_font`) VALUES
(1, 'enabled', 'power_off', 'element', 'Jules Warner', 'accordion', NULL, 'wi_accordion'),
(2, 'enabled', 'power_off', 'element', 'Jules Warner', 'appearing_button', NULL, 'wi_appearing_button'),
(3, 'enabled', 'power_off', 'element', 'Jules Warner', 'button', NULL, 'wi_button'),
(4, 'enabled', 'power_off', 'element', 'Jules Warner', 'carousel', NULL, 'wi_carousel'),
(5, 'enabled', 'power_off', 'element', 'Jules Warner', 'columns', NULL, ''),
(6, 'enabled', 'power_off', 'element', 'Jules Warner', 'content_box', NULL, 'wi_content_box'),
(7, 'disabled', 'power_off', 'element', 'Jules Warner', 'folded_corner', NULL, 'wi_folded_corner'),
(8, 'enabled', 'power_off', 'element', 'Jules Warner', 'google_map', NULL, 'wi_google_map'),
(9, 'disabled', 'power_off', 'element', 'Jules Warner', 'fonts', NULL, 'wi_fonts'),
(10, 'disabled', 'power_off', 'element', 'Jules Warner', 'font', NULL, 'wi_font'),
(11, 'disabled', 'power_off', 'element', 'Jules Warner', 'heading', NULL, ''),
(12, 'disabled', 'power_off', 'element', 'Jules Warner', 'image', NULL, ''),
(13, 'disabled', 'power_off', 'element', 'Jules Warner', 'image_hover', NULL, ''),
(14, 'disabled', 'power_off', 'element', 'Jules Warner', 'image_mosaic', NULL, ''),
(15, 'disabled', 'power_off', 'element', 'Jules Warner', 'latest_fbfeeds', NULL, ''),
(16, 'disabled', 'power_off', 'element', 'Jules Warner', 'list', NULL, ''),
(17, 'enabled', 'power_on', 'custom', 'Jules Warner', 'Panel', NULL, 'wi_panel'),
(18, 'enabled', 'power_off', 'custom', 'Jules Warner', 'welcome_box', NULL, 'wi_welcome_box'),
(19, 'enabled', 'power_off', 'element', 'Jules Warner', 'left_sidebar', NULL, ''),
(20, 'disabled', 'power_off', 'element', 'Jules Warner', 'loop', NULL, ''),
(21, 'disabled', 'power_off', 'element', 'Jules Warner', 'menu_button', NULL, ''),
(22, 'enabled', 'power_off', 'element', 'Jules Warner', 'panel', NULL, ''),
(23, 'enabled', 'power_off', 'element', 'Jules Warner', 'post_carousel', NULL, ''),
(24, 'enabled', 'power_off', 'element', 'Jules Warner', 'progress_bar', NULL, 'wi_progress'),
(25, 'enabled', 'power_off', 'element', 'Jules Warner', 'search_engine', NULL, ''),
(26, 'enabled', 'power_off', 'element', 'Jules Warner', 'right_sidebar', NULL, ''),
(27, 'enabled', 'power_off', 'element', 'Jules Warner', 'slider', NULL, ''),
(28, 'enabled', 'power_off', 'element', 'Jules Warner', 'subscribe', NULL, ''),
(29, 'enabled', 'power_off', 'custom', 'Jules Warner', 'top_head', NULL, 'wi_top_head'),
(30, 'enabled', 'power_off', 'element', 'Jules Warner', 'csstabs', NULL, 'wi_tabs'),
(31, 'disabled', 'power_off', 'element', 'Jules Warner', 'flags', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `wi_modules`
--

CREATE TABLE IF NOT EXISTS `wi_modules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `mod_id` int(11) DEFAULT NULL,
  `trans` varchar(255) DEFAULT NULL,
  `text` varchar(255) DEFAULT NULL,
  `trans1` varchar(255) DEFAULT NULL,
  `text1` varchar(255) DEFAULT NULL,
  `trans2` varchar(255) DEFAULT NULL,
  `text2` varchar(255) DEFAULT NULL,
  `text3` varchar(255) DEFAULT NULL,
  `trans3` varchar(255) DEFAULT NULL,
  `img` varchar(255) DEFAULT NULL,
  `img1` varchar(255) DEFAULT NULL,
  `img3` varchar(255) DEFAULT NULL,
  `text4` varchar(255) DEFAULT NULL,
  `trans4` varchar(255) DEFAULT NULL,
  `img2` varchar(255) DEFAULT NULL,
  `text5` varchar(255) DEFAULT NULL,
  `trans5` varchar(255) DEFAULT NULL,
  `text6` varchar(255) DEFAULT NULL,
  `trans6` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



CREATE TABLE IF NOT EXISTS `wi_msg` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg` text NOT NULL,
  `wtime` datetime NOT NULL,
  `chat_id` int(11) NOT NULL DEFAULT '0',
  `user_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `chat_id_id` (`chat_id`,`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



--
-- Table structure for table `wi_notifications`
--

CREATE TABLE IF NOT EXISTS `wi_notifications` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `date` datetime NOT NULL,
  `user` varchar(45) NOT NULL,
  `opperation` varchar(255) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `wi_page`
--


CREATE TABLE IF NOT EXISTS `wi_page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `panel` enum('0','1') DEFAULT '0',
  `top_head` enum('0','1') DEFAULT '0',
  `header` enum('0','1') DEFAULT '0',
  `left_sidebar` enum('0','1') NOT NULL DEFAULT '0',
  `right_sidebar` enum('0','1') NOT NULL DEFAULT '0',
  `contents` text,
  `footer` enum('0','1') DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wi_page`
--

INSERT INTO `wi_page` ( `name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`) VALUES
( 'alogin', '1', '1', '0', '0', '0', 'alogin', '1'),
( 'confirm', '1', '1', '0', '0', '0', 'confirm', '1'),
( 'index', '1', '1', '0', '0', '0', 'welcome_box', '1'),
( 'passwordreset', '1', '1', '0', '0', '0', 'passwordreset', '1'),
( 'profile', '1', '1', '0', '0', '0', 'profile', '1');

-- --------------------------------------------------------

--
-- Table structure for table `wi_plugin`
--

CREATE TABLE IF NOT EXISTS `wi_plugin` (
  `plugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin` varchar(255) NOT NULL,
  `activated` enum('true','false') NOT NULL,
  PRIMARY KEY (`plugin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_private_messages`
--

CREATE TABLE IF NOT EXISTS `wi_private_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `rec_id` int(11) NOT NULL,
  `rec_name` varchar(255) NOT NULL,
  `subject` varchar(60) NOT NULL,
  `message` varchar(255) NOT NULL,
  `opened` enum('0','1') NOT NULL,
  `recipientDelete` enum('0','1') NOT NULL,
  `senderDelete` enum('0','1') NOT NULL,
  `time_sent` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `wi_scripts`
--

CREATE TABLE IF NOT EXISTS `wi_scripts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `src` varchar(255) NOT NULL,
  `page` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
(15, 'site/js/login_panel/js/slide.js', 'profile');

-- --------------------------------------------------------

--
-- Table structure for table `wi_sidebar`
--

CREATE TABLE IF NOT EXISTS `wi_sidebar` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL ,
  `link` varchar(100) NOT NULL DEFAULT '#',
  `parent` int(11) NOT NULL DEFAULT '0',
  `sort` int(11) DEFAULT NULL,
  `lang` varchar(255) NOT NULL,
  `img` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wi_sidebar`
--

INSERT INTO `wi_sidebar` ( `label`, `link`, `parent`, `sort`, `lang`, `img`) VALUES
( 'Settings', '', 0, 0, 'settings', 'settings'),
( 'Site', 'WISite.php', 1, 0, 'Site', 'site'),
( 'Users', '', 0, 1, 'Users', 'users'),
( 'Manage User', 'WIUser.php', 3, 0, 'Manage_users', 'manage users'),
( 'Roles', 'WIRoles.php', 3, 1, 'roles', 'roles'),
( 'Menus', 'WIMenu.php', 1, 1, 'Menu', 'menu'),
( 'Header', 'WIHeader.php', 1, 2, 'Header', 'header'),
( 'Modules', '', 0, 2, 'Modules', 'modules'),
('Modules', 'WIModules.php', 8, 0, 'Modules', 'modules'),
( 'Pages', '', 0, 3, 'Pages', 'pages'),
('Pages', 'WIPages.php', 10, 0, 'Pages', 'pages'),
( 'Plugins', '', 0, 4, 'Plugins', 'plugin'),
( 'plugin', 'WIPlugin.php', 12, 0, 'Plugin', 'plugin'),
('Styling', 'WIStyling.php', 1, 3, 'Styling', 'styling'),
('Media', '', 0, 5, 'media', 'media'),
( 'Media', 'WIMedia.php', 15, 0, 'media', 'media'),
('Multi Lang', 'WIMlang.php', 1, 4, 'Multi Lang', 'multi_lang');

-- --------------------------------------------------------

--
-- Table structure for table `wi_site`
--

CREATE TABLE IF NOT EXISTS `wi_site` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `site_name` varchar(50) DEFAULT NULL,
  `site_domain` varchar(255) NOT NULL,
  `site_url` varchar(50) DEFAULT NULL,
  `favicon` text NOT NULL,
  `db_host` varchar(50) DEFAULT NULL,
  `db_username` varchar(50) DEFAULT NULL,
  `db_pass` varchar(50) DEFAULT NULL,
  `db_name` varchar(50) DEFAULT NULL,
  `db_port` int(11) NOT NULL DEFAULT '25',
  `db_type` varchar(255) NOT NULL DEFAULT 'mysql',
  `secure_session` enum('false','true') NOT NULL DEFAULT 'false',
  `http_only` enum('false','true') NOT NULL DEFAULT 'true',
  `regenerate_id` enum('false','true') NOT NULL DEFAULT 'true',
  `use_only_cookie` enum('1','0') NOT NULL DEFAULT '1',
  `login_fingerprint` enum('true','false') NOT NULL,
  `max_login_attempts` int(11) NOT NULL DEFAULT '5',
  `redirect_after_login` varchar(255) NOT NULL,
  `password_encryption` varchar(255) NOT NULL,
  `encryption_cost` int(11) NOT NULL,
  `sha512_iterations` int(11) NOT NULL,
  `password_salt` varchar(255) NOT NULL,
  `reset_key_life` int(11) NOT NULL,
  `mail_confirm_required` enum('true','false') NOT NULL,
  `register_confirm` varchar(255) NOT NULL,
  `reg_pass_reset` varchar(255) NOT NULL,
  `mailer` varchar(255) NOT NULL,
  `smpt_host` varchar(255) NOT NULL,
  `smpt_port` varchar(255) NOT NULL,
  `smpt_username` varchar(255) NOT NULL,
  `smpt_password` varchar(255) NOT NULL,
  `smpt_encryption` varchar(255) NOT NULL,
  `social_callback_url` varchar(255) NOT NULL,
  `google_enabled` enum('true','false') NOT NULL DEFAULT 'false',
  `google_id` varchar(255) NOT NULL,
  `google_secret` varchar(255) NOT NULL,
  `facebook_enabled` enum('true','false') NOT NULL DEFAULT 'false',
  `facebook_id` varchar(255) NOT NULL,
  `facebook_secret` varchar(255) NOT NULL,
  `twitter_enabled` enum('true','false') NOT NULL DEFAULT 'false',
  `twitter_key` varchar(255) NOT NULL,
  `twitter_secret` varchar(255) NOT NULL,
  `default_lang` varchar(255) NOT NULL,
  `multi_lang` enum('on','off') NOT NULL DEFAULT 'off',
  `bootstrap_version` varchar(50) NOT NULL,
  `wicms_version` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `wi_site` (`id`,`site_name`, `site_domain`, `site_url`, `favicon`, `db_host`, `db_username`, `db_pass`, `db_name`, `db_port`, `db_type`, `secure_session`, `http_only`, `regenerate_id`, `use_only_cookie`, `login_fingerprint`, `max_login_attempts`, `redirect_after_login`, `password_encryption`, `encryption_cost`, `sha512_iterations`, `password_salt`, `reset_key_life`, `mail_confirm_required`, `register_confirm`, `reg_pass_reset`, `mailer`, `smpt_host`, `smpt_port`, `smpt_username`, `smpt_password`, `smpt_encryption`, `social_callback_url`, `google_enabled`, `google_id`, `google_secret`, `facebook_enabled`, `facebook_id`, `facebook_secret`, `twitter_enabled`, `twitter_key`, `twitter_secret`, `default_lang`, `multi_lang`, `bootstrap_version`, `wicms_version`) 
VALUES
( 1,'$name', '$dom', '$script', 'wi_cms_logo.PNG', '$db_host', '$db_username', '$db_pass', '$db_name', '25', 'mysql', '$session_secure', '$http', '$session_regenerate' , '$cookieonly', '$login_fingerprint', '$max_login_attempts', '$redirect_after_login', '$encryption', '$cost', '35000', '$salt', '$reset_key_life', '$mail_confirm_required', '$register_confirm','$reg_pass_reset', '$mailer', '', '', '', '', '', '$social_callback_url',  '$google_enabled' , '', '',  '$facebook_enabled', '', '',  '$twitter_enabled', '', '', '$default_lang', '$multi_lang', '$bootstrap_version', '$Version');


-- --------------------------------------------------------

--
-- Table structure for table `wi_social`
--

CREATE TABLE IF NOT EXISTS `wi_social` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `href` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `wi_social` (`id`, `href`, `name`) VALUES
(1, '#', 'facebook'),
(2, '#', 'twitter'),
(3, '#', 'instagram')
ON DUPLICATE KEY UPDATE `href` = VALUES(`href`), `name` = VALUES(`name`);

-- --------------------------------------------------------

--
-- Table structure for table `wi_social_logins`
--

CREATE TABLE IF NOT EXISTS `wi_social_logins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `provider` varchar(50) COLLATE utf8_unicode_ci DEFAULT 'email',
  `provider_id` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL ,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `wi_tasks`
--

CREATE TABLE IF NOT EXISTS `wi_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item` varchar(255) NOT NULL,
  `percent` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wi_tasks`
--

INSERT INTO `wi_tasks` (`id`, `item`, `percent`) VALUES
(1, 'Design new theme', '20'),
(2, 'Finish mods', '30');

-- --------------------------------------------------------

--
-- Table structure for table `wi_theme`
--


CREATE TABLE IF NOT EXISTS `wi_theme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `theme` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `in_use` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


INSERT INTO `wi_theme` (`id`, `theme`, `destination`, `in_use`) VALUES
(1, 'WICMS', 'WITheme/WICMS/', 1);


--
-- Table structure for table `wi_trans`
--

CREATE TABLE IF NOT EXISTS `wi_trans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(50) NOT NULL,
  `keyword` varchar(255) NOT NULL,
  `translation` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wi_trans`
--

/*INSERT INTO `wi_trans` (`id`, `lang`, `keyword`, `translation`) VALUES
( 'en', 'site_name', 'Debate'),
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
(21, 'en', 'male', 'Male'),
(22, 'en', 'female', 'Female'),
(23, 'en', 'forgot_password', 'Forget pasword'),
(24, 'en', 'repeat_password', 'Repeat password'),
(25, 'en', 'reset_password', 'Reset password'),
(26, 'en', 'email_confirmation', 'Email Confirmation'),
(27, 'en', 'you_can_login_now', 'You can <a href=''{link}''>log in</a> now.'),
(28, 'en', 'user_with_key_doesnt_exist', 'User with this key doesn''t exist in our database.'),
(29, 'en', 'gender', 'Gender'),
(30, 'en', 'dob', 'DOB'),
(31, 'en', 'welcoem', 'Welcome'),
(32, 'en', 'panel_mini_admin', 'Admin Mini Panel'),
(33, 'en', 'view_profile_page', 'View profile page'),
(34, 'en', 'log_off', 'Log Off'),
(35, 'en', 'admin_panel', 'Admin Panel'),
(36, 'en', 'member_panel', 'Member''s Panel'),
(37, 'en', 'hello', 'Hello'),
(38, 'en', 'my_profile', 'My Profile'),
(39, 'en', 'user_roles', 'User Roles'),
(40, 'en', 'index', 'Index'),
(41, 'en', 'contact_us', 'Contact Us'),
(42, 'en', 'services', 'Services'),
(43, 'en', 'support_us', 'Support Us'),
(44, 'en', 'forum', 'Forum'),
(45, 'en', 'cafe', 'Cafe'),
(46, 'en', 'submit', 'Submit'),
(47, 'en', 'about_us', 'About Us'),
(48, 'en', 'faq', 'FAQ'),
(49, 'en', 'friending', 'Suggest to a Friend'),
(50, 'en', 'welcome_', 'Welcome to '),
(51, 'en', 'password_reset', 'Password reset'),
(52, 'en', 'reset_password', 'Reset Password'),
(53, 'en', 'phone', 'Phone'),
(54, 'en', 'note', 'Note'),
(55, 'en', 'update', 'Update'),
(56, 'en', 'address', 'Address'),
(57, 'en', 'first_name', 'First Name'),
(58, 'en', 'last_name', 'Last Name'),
(59, 'en', 'old_password', 'Old Password'),
(60, 'en', 'new_password', 'New Password'),
(61, 'en', 'your_details', 'Tour Details'),
(62, 'en', 'change_password', 'Change Password'),
(63, 'en', 'confirm_new_password', 'Confirm New Password'),
(64, 'en', 'to_change_email_username', 'If you want to change your username, email or you have registred via social network and you want to change your password now, please contact administrator.'),
(65, 'en', 'add', 'Add'),
(66, 'en', 'ban', 'Ban'),
(67, 'en', 'yes', 'Yes'),
(68, 'en', 'no', 'No'),
(69, 'en', 'edit', 'Edit'),
(70, 'en', 'next', 'Next'),
(71, 'en', 'previous', 'Previous'),
(72, 'en', 'unban', 'Unban'),
(73, 'en', 'cancel', 'Cancel'),
(74, 'en', 'delete', 'Delete'),
(75, 'en', 'details', 'Details'),
(76, 'en', 'loading', 'loading...'),
(77, 'en', 'role_name', 'Role Name'),
(78, 'en', 'user_with_role', '# of users with this role.'),
(79, 'en', 'user_banned', 'This user account is banned by administrator!'),
(80, 'en', 'field_required', 'Field cannot be empty!'),
(81, 'en', 'role_taken', 'Role already exist.'),
(82, 'en', 'email_required', 'Email is required'),
(83, 'en', 'email_wring_format', 'Please enter valid email.'),
(84, 'en', 'email_not_exist', 'This email does not exist in our database'),
(85, 'en', 'email_taken', 'User with this email is already registred.'),
(86, 'en', 'username_taken', 'This username is in use by another member.'),
(87, 'en', 'user_not_confirmed', 'Please confirm your email first.'),
(88, 'en', 'password_required', 'Password is required'),
(89, 'en', 'wrong_username_password', 'Wrong username/password combination.'),
(90, 'en', 'passwords_dont_match', 'Passwords do not match'),
(91, 'en', 'wrong_old_password', 'Wrong Old password'),
(92, 'en', 'wrong_sum', 'Wrong sum. Please check it again.'),
(93, 'en', 'brute_force', 'You exceeded maximum attempts limit for today. Try again tomorrow.'),
(94, 'en', 'success_registration_with_confirm', 'Registration successful. Please check your email.'),
(95, 'en', 'success_registration_no_confirm', 'Registration successful. You can log in now.'),
(96, 'en', 'user_added_successfully', 'User added successfully.'),
(97, 'en', 'user_updated_successfully', 'User updated successfully.'),
(98, 'en', 'user_dont_exist', 'This user doesn''t exist.'),
(99, 'en', 'leave_blank', 'Leave blank if you don''t want to change it.'),
(100, 'en', 'invalid_password_reset_key', 'Password reset key is invalid or expired'),
(101, 'en', 'password_length', 'Password must be at least 6 characters long.'),
(102, 'en', 'error_writing_to_db', 'Error writing to database. Please try again.'),
(103, 'en', 'posting', 'Posting...'),
(104, 'en', 'resetting', 'Resetting...'),
(105, 'en', 'password_updated_successfully', 'Password successfully updated.'),
(106, 'en', 'password_updated_successfully_login', 'Password successfully updated. You can <a href=''login.php''>login now</a>.'),
(107, 'en', 'password_reset_email_sent', 'Password reset email sent. Check your inbox (and spam) folder.'),
(108, 'en', 'message_couldn_be_sent', 'Message could not be sent! Please try again.'),
(109, 'en', 'updating', 'Updating...'),
(110, 'en', 'details_updated', 'Details updated successfully.'),
(111, 'en', 'are_you_sure', 'Are you sure'),
(112, 'en', 'creating_account', 'Creating Account...'),
(113, 'en', 'usergroup', 'User Group'),
(114, 'en', 'at', 'at'),
(115, 'en', 'logout', 'Log Out'),
(116, 'en', 'copyright_by', 'Copyright by'),
(117, 'en', 'search', 'Search'),
(118, 'en', 'total_records', 'total records'),
(119, 'en', 'filtered_from', 'Filtered from'),
(120, 'en', 'records', 'Records'),
(121, 'en', 'of', 'of'),
(122, 'en', 'to', 'to'),
(123, 'en', 'main_title', 'This is meant as a FREE CMS System, just because i love to create things, a simplified UI backend, built for high end security, database driven, everything you need in a nice bundle to build your own website.'),
(124, 'en', 'community', 'Community'),
(125, 'en', 'learn', 'WI Community for anyone who loves to build websites.'),
(126, 'en', 'software', 'Software Packages'),
(127, 'en', 'package', 'Themes, plugins, apps, custom packages, and so much more.'),
(128, 'en', 'it_title', 'Custom software for the commercial world.'),
(129, 'en', 'it', 'I.T'),
(130, 'es', 'email', N'Е-маил'),
(131, 'es', 'login', N'Пријава'),
(132, 'es', 'username', N'Корисничко име'),
(133, 'es', 'password', 'Лозинка'),
(134, 'es', 'male', 'Мушкарац'),
(135, 'es', 'female', 'женски'),
(136, 'es', 'your_email', 'Ваша емаил'),
(137, 'es', 'login_with', 'Логин Витх'),
(138, 'es', 'email_confirmed', 'Е-маил потврдио'),
(139, 'es', 'create_Account', 'Registruj se'),
(140, 'es', 'forgot_password', 'Zaboravili ste lozinku?'),
(141, 'es', 'repeat_password', 'Ponovite lozinku'),
(142, 'es', 'reset_password', 'ресет пассворд'),
(143, 'es', 'email_confirmation', 'Е-маил Потврда'),
(144, 'es', 'you_can_log_now', 'Можете <а хреф=''{линк}''> се пријавите </а> сада .'),
(145, 'es', 'user_with_key_doesnt_exist', 'Корисник са овим кључем не постоји у нашој бази .'),
(146, 'es', 'gender', 'Пол'),
(147, 'es', 'dob', 'ДОБ'),
(148, 'es', 'country', 'земља'),
(149, 'es', 'welcome', 'Dobrodošao'),
(150, '', '', ''),
(151, 'es', 'TO', 'у'),
(152, 'es', 'Thank_you', 'Велико хвала'),
(153, 'es', 'panel_mini_admin', 'Администратор мини панел'),
(154, 'es', 'view_profile_page', 'Погледај профил страна'),
(155, 'es', 'log_off', 'Odjavi se'),
(156, 'es', 'admin_panel', 'Админ панел'),
(157, 'es', 'member_panel', 'Панел Члана'),
(158, 'es', 'home', 'Casa'),
(159, 'es', 'users', 'usuarios'),
(160, 'es', 'my_profile', 'Mi perfil'),
(161, 'es', 'user_roles', 'Roles del usuario'),
(162, 'es', 'index', 'Índice'),
(163, 'es', 'contact_us', 'Contáctenos'),
(164, 'es', 'support_us', 'Apoyanos'),
(165, 'es', 'forum', 'Foro'),
(166, 'es', 'shop', 'tienda'),
(167, 'es', 'cafe', 'Cafetería'),
(168, 'es', 'submit', 'Enviar'),
(169, 'es', 'about_us', 'Sobre nosotros'),
(170, 'es', 'info', 'información'),
(171, 'en', 'current_topics', 'view Current Topics'),
(172, 'en', 'ok', 'Ok'),
(173, 'en', 'change_pic', 'Change Photo'),
(174, 'en', 'bio', 'Bio'),
(175, 'en', 'save', 'Save'),
(176, 'en', 'user_location', 'user Location'),
(177, 'en', 'social_info', 'Social Info'),
(178, 'en', 'friend', 'Friends'),
(179, 'en', 'profile_wall', 'Profile Wall'),
(180, 'en', 'mess', 'Messages'),
(181, 'en', 'admins', 'Administrators'),
(182, 'en', 'logs', 'Logs'),
(183, 'es', 'email', 'Е-маил'),
(184, 'en', 'showing', 'Showing...'),
(185, 'en', 'nothing_found', 'Nothing found - sorry'),
(186, 'en', 'never_logged_in', 'Never Logged In'),
(187, 'en', 'select_role', 'Select Role'),
(188, 'en', 'confirmed', 'Confirmed'),
(189, 'en', 'last_login', 'Last Login'),
(190, 'en', 'skills', 'Our Skills'),
(191, 'en', 'whatwedo', 'What We Do'),
(192, 'ru', 'email', 'Эл. адрес'),
(193, 'ru', 'login', 'Логин'),
(194, 'ru', 'username', 'Пользователь'),
(195, 'ru', 'password', 'Пароль'),
(196, 'ru', 'your_email', 'Ваш Эл. адрес'),
(197, 'ru', 'login_with', 'Войдите с'),
(198, 'ru', 'email_confirmed', 'Подтверждение Эл. адрес'),
(199, 'ru', 'create_account', 'Создать учетную запись'),
(200, 'ru', 'forgot_password', 'Забыли пароль ?'),
(201, 'ru', 'repeat_password', 'Подтверждение пароля'),
(202, 'ru', 'reset_password', 'Сбросить пароль'),
(203, 'ru', 'email_confirmation', 'Подтвердите Эл. адрес'),
(204, 'ru', 'you_can_loging_now', 'Вы можете <a href=''{link}''> залогиниться</a>.'),
(205, 'ru', 'user_with_key_doesnt_exist', 'Пользователь с данным ключом не существует.'),
(206, 'ru', 'gender', 'Пол'),
(207, 'ru', 'welcome', 'добро пожаловать'),
(208, 'ru', 'site_name', 'Debate'),
(209, 'ru', 'panel_mini_admin', 'Администратор Мини-панель'),
(210, 'ru', 'view_profile_page', 'Посмотреть профиль страницы'),
(211, 'ru', 'log_off', 'Выйти'),
(212, 'ru', 'admin_panel', 'Панель администратора'),
(213, 'ru', 'member_panel', 'Панель пользователя'),
(214, 'ru', 'home', 'Главная'),
(215, 'ru', 'users', 'пользователей'),
(216, 'ru', 'index', 'Индекс'),
(217, 'ru', 'contact_us', 'Свяжитесь с нами'),
(218, 'ru', 'forum', 'Форум'),
(219, 'ru', 'shop', 'Магазин'),
(220, 'ru', 'cafe', 'Кафе'),
(221, 'ru', 'submit', 'нажмите кнопку'),
(222, 'ru', 'about_us', 'О нас'),
(223, 'ru', 'info', 'Информация'),
(224, 'ru', 'welcome_', 'Добро пожаловать в'),
(225, 'ru', 'main_title', 'Это означает, в качестве свободного CMS системы, только потому, что я люблю создавать вещи, упрощенную бэкенд UI, построенный для высокого класса безопасности, движимый базы данных, все, что нужно в хорошем расслоении, чтобы построить свой собственный веб'),
(226, 'ru', 'community', 'сообщество'),
(227, 'ru', 'footer', 'Сделано и Разработанный WI: Warner-Бесконечность'),
(228, 'ru', 'copyright', 'авторское право'),
(229, 'ru', 'phone', 'Телефон'),
(230, 'ru', 'note', 'Заметка'),
(231, 'ru', 'uodate', 'Обновление'),
(232, 'ru', 'address', 'Адрес'),
(233, 'ru', 'first_name', 'Имя'),
(234, 'ru', 'last_name', 'Фамилия'),
(235, 'ru', 'old_password', 'Старый пароль'),
(236, 'ru', 'new_password', 'Новый пароль'),
(237, 'ru', 'your_details', 'Ваши данные'),
(238, 'ru', 'change_password', 'Изменить пароль'),
(239, 'ru', 'confirm_new_password', 'Подтвердить пароль'),
(240, 'ru', 'to_change_email_username', 'Если Вы желаете изменить Ваш e-mail, пользователя или в случае регистрации через социальные сети желаете сменить пароль, обратитесь к администратору.'),
(241, 'ru', 'password_reset', 'Сброс пароля'),
(242, 'ru', 'add', 'Добавить'),
(243, 'ru', 'action', 'Действие'),
(244, 'ru', 'role_name', 'Название роли'),
(245, 'ru', 'users_with_role', 'Пользователи с этой ролью'),
(246, 'ru', 'ban', 'Блокировать'),
(247, 'ru', 'yes', 'Да'),
(248, 'ru', 'no', 'Нет'),
(249, 'ru', 'edit', 'Изменить'),
(250, 'ru', 'next', 'Следующий'),
(251, 'ru', 'previous', 'Предыдущий'),
(252, 'ru', 'unban', 'Разблокировать'),
(253, 'ru', 'cancel', 'Отменить'),
(254, 'ru', 'delete', 'Удалить'),
(255, 'ru', 'details', 'Детали'),
(256, 'ru', 'loading', 'Загрузка..'),
(257, 'ru', 'register_date', 'Дата регистрации'),
(258, 'ru', 'last_login', 'Дата последней авторизации'),
(259, 'ru', 'confirmed', 'Подтверждено'),
(260, 'ru', 'select_role', 'Выбрать роль'),
(261, 'ru', 'change_role', 'Изменить роль'),
(262, 'ru', 'pick_user_role', 'Подтвердите роль пользователя'),
(263, 'ru', 'add_user', 'Добавить пользователя'),
(264, 'ru', 'never_logged_in', 'Никогда не авторизовывался'),
(265, 'ru', 'records_per_page', 'записей на странице'),
(266, 'ru', 'nothing_found', 'Ничего не найдено.'),
(267, 'ru', 'no_data_in_table', 'Нету данных в таблице'),
(268, 'ru', 'showing', 'Показываем...'),
(269, 'ru', 'of', 'из'),
(270, 'ru', 'records', 'записи'),
(271, 'ru', 'filtered_from', 'отобрано от'),
(272, 'ru', 'total_records', 'всего записей'),
(273, 'ru', 'search', 'Поиск'),
(274, 'ru', 'logout', 'Выйти'),
(275, 'ru', 'copyright_by', 'Все права защищены'),
(276, 'ru', 'user_baned', 'Данный пользователь заблокирован.'),
(277, 'ru', 'field_required', 'Поле не может быть пустым!'),
(278, 'ru', 'role_taken', 'Данная роль применена.'),
(279, 'ru', 'email_required', 'Требуется e-mail.'),
(280, 'ru', 'email_wrong_format', 'Неправильный формат Эл. адрес'),
(281, 'ru', 'email_not_exist', 'Эл. адрес  в базе данных не зарегистрирован.'),
(282, 'ru', 'email_taken', 'Пользователь с данным e-mail уже зарегистрирован.'),
(283, 'ru', 'username_required', 'Требуется ввести пользователя.'),
(284, 'ru', 'username_taken', 'Пользователь с таким именем уже существует.'),
(285, 'ru', 'user_not_confirmed', 'Необходимо подтвердить Ваш Эл. адрес'),
(286, 'ru', 'posting', 'Постављање...'),
(287, 'ru', 'logging_in', 'Входим...'),
(288, 'ru', 'resetting', 'Перезагрузка...'),
(289, 'ru', 'password_updated_successfully', 'Пароль успешно обновлен.'),
(290, 'ru', 'password_updated_successfully_login', 'Пароль успешно обновлен. Можно <a href=''login.php''> авторизоваться</a>.'),
(291, 'ru', 'working', 'Работаем...'),
(292, 'ru', 'password_required', 'Необходимо ввести пароль.'),
(293, 'ru', 'wrong_password_format', 'Неправильные пользователь или пароль.'),
(294, 'ru', 'passwords_dont_match', 'Пароли не совпадают.'),
(295, 'ru', 'wrong_old_password', 'Старый пароль введен неверно!'),
(296, 'ru', 'wrong_sum', 'Неправильно указана сумма. Введите еще раз.'),
(297, 'ru', 'brute_force', 'Вы превысили количество возможных попыток авторизации. Попробуйте авторизоваться завтра.'),
(298, 'ru', 'success_registration_no_confirm', 'Регистрация прошла успешно. Проверьте Ваш Эл. адрес'),
(299, 'ru', 'user_added_successfully', 'Пользователь успешно добавлен.'),
(300, 'ru', 'user_updated_successfully', 'Пользователь успешно обновлен.'),
(301, 'ru', 'user_dont_exist', 'Данный пользователь не существует.'),
(302, 'ru', 'leave_blank', 'Оставьте это поле пустым, если не хотите изменять пароль.'),
(303, 'ru', 'invalid_password_reset_key', 'Неправильный ключ для сброса пароля'),
(304, 'ru', 'password_length', 'Пароль должен быть не менее 6 символов.'),
(305, 'ru', 'error_writing_to_db', 'Ошибка записи в базу данных. Попробуйте еще раз.'),
(306, 'ru', 'password_reset_email_sent', 'Отправлено письмо для смены пароля. Проверьте Ваш Эл. адрес.'),
(307, 'ru', 'message_couldn_be_sent', 'Сообщение не может быть отправлено. Попробуйте еще раз.'),
(308, 'ru', 'updating', 'Обновление...'),
(309, 'ru', 'details_updated', 'Данные успешно обновлены.'),
(310, 'ru', 'are_you_sure', 'Вы уверены ?'),
(311, 'ru', 'creating_account', 'Создание аккаунта...'),
(312, 'rs', 'email', 'Эл. адрес'),
(313, 'rs', 'login', 'Авторизоваться'),
(314, 'rs', 'username', 'Имя пользователя'),
(315, 'rs', 'password', 'пароль'),
(316, 'rs', 'male', 'мужчина'),
(317, 'rs', 'female', 'женский'),
(318, 'rs', 'your_email', 'vaša емаил'),
(319, 'rs', 'login_with', 'Пријавите се'),
(320, 'rs', 'email_confirmed', 'емаил потврдио'),
(321, 'rs', 'create_account', 'Региструј се'),
(322, 'rs', 'forgot_password', 'Заборавили сте лозинку ?'),
(323, 'rs', 'repeat_password', 'Поновите лозинку'),
(324, 'rs', 'reset_password', 'Ресетуј шифру'),
(325, 'rs', 'email_confirmation', 'Имејл потврда'),
(326, 'rs', 'you_can_login_now', 'Можете<a href=''{link}''> лог ин</a> сада.'),
(327, 'rs', 'user_with_key_doesnt_exist', 'Корисник са овим кључем не постоји у нашој бази.'),
(328, 'rs', 'gender', 'пол'),
(329, 'rs', 'dob', 'ДОБ'),
(330, 'rs', 'country', 'земља'),
(331, 'rs', 'welcome', 'Добродошли'),
(332, 'rs', 'panel_mini_admin', 'Админ мини панел'),
(333, 'rs', 'view_profile_page', 'Погледај профил страна'),
(334, 'rs', 'log_off', 'Одјави се'),
(335, 'rs', 'admin_panel', 'табла руководиоца'),
(336, 'rs', 'member_panel', 'Корисничко панел'),
(337, 'rs', 'HELLO', 'Здраво'),
(338, 'rs', 'home', 'Дом'),
(339, 'rs', 'users', 'усерс'),
(340, 'rs', 'my_profile', 'Мој профил'),
(341, 'rs', 'user_roles', 'усер Улоге'),
(342, 'rs', 'index', 'индекс'),
(343, 'rs', 'contact_us', 'Контактирајте нас'),
(344, 'rs', 'forum', 'форум'),
(345, 'rs', 'shop', 'продавница'),
(346, 'rs', 'cafe', 'кафе'),
(347, 'rs', 'submit', 'поднети'),
(348, 'rs', 'about_us', 'О нама'),
(349, 'rs', 'info', 'инфо'),
(350, 'rs', 'welcome_', 'Добродошли у'),
(351, 'rs', 'main_title', 'Ово је замишљена као БЕСПЛАТНО ЦМС система, само зато што волим да створи ствари, поједностављено интерфејс позадински, изграђена за хигх енд безбедност, базама података, све што је потребно у лепом пакету да изградите сопствени сајт. <br /> Изградити за '),
(352, 'rs', 'view_details', 'Приказ детаља'),
(353, 'rs', 'footer', 'Направљен и Развијен од стране ВИ: Варнер-Инфинити'),
(354, 'rs', 'copyright', 'Ауторско право'),
(355, 'rs', 'phone', 'phone'),
(356, 'en', 'trans_lang', 'Lang'),
(357, 'en', 'lang_keyword', 'Keyword'),
(358, 'en', 'lang_trans', 'Translation'),
(359, 'en', 'successfully_updated_site_settings', 'successfully Updated Site Settings'),
(360, 'en', 'Pick_Category', 'Pick Category'),
(361, 'en', 'Pick_Brand', 'Pick Brand'),
(362, 'en', 'Change_cat', 'Change category'),
(363, 'en', 'brand', 'Brand'),
(364, 'en', 'Category', 'Category'),
(365, 'en', 'product_name', 'Produce Name'),
(366, 'en', 'product_desc', 'Product Description'),
(367, 'en', 'product_price', 'Product Price'),
(368, 'en', 'product_image', 'Product Image'),
(369, 'en', 'edit_css', 'Edit CSS'),
(370, 'en', 'edit_meta', 'Edit Meta'),
(371, 'en', 'edit_css_href', 'Href'),
(372, 'en', 'meta_name', 'Meta Name'),
(373, 'en', 'meta_content', 'Meta Content'),
(374, 'en', 'meta_author', 'Meta Author'),
(375, 'en', 'add_meta', 'Add Meta'),
(376, 'en', 'add_trans', 'Add Translation'),
(377, 'en', 'add_lang', 'Add Language'),
(378, 'en', 'css_name', 'Add CSS'),
(379, 'en', 'admins', 'Administrators'),
(380, 'en', 'logs', 'Logs'),
(381, 'en', 'add_todo', 'Add to do Item'),
(382, 'en', 'ToDoItem', 'to do Item'),
(383, 'en', 'choose', 'Please Choose an option'),
(384, 'en', 'Media_Lib', 'WIMedia Library'),
(385, 'en', 'upload_logo', 'Upload logo'),
(386, 'en', 'add_lang', 'Add Language'),
(387, 'en', 'country', 'country name'),
(388, 'en', 'country_code', 'country code'),
(389, 'en', 'change_lang', 'Change Lang'),
(390, 'en', 'save', 'Save'),
(391, 'en', 'updating', 'updating'),
(392, 'en', 'href', 'Href'),
(393, 'en', 'edit_meta', 'Edit Meta'),
(394, 'en', 'meta_content', 'Meta Content'),
(395, 'en', 'meta_name', 'Meta_Name'),
(396, 'en', 'meta_author', 'Meta Author'),
(397, 'en', 'edit_css', 'Edit CSS'),
(398, 'en', 'edit_js', 'Edit JS');
*/

CREATE TABLE IF NOT EXISTS `wi_track` 
(
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `ref` varchar(250) NOT NULL ,

  `agent` varchar(250) ,
  `ip` varchar(20) NOT NULL ,
  `tracking_page_code` varchar(100) ,
  `tracking_page_name` varchar(100) ,
 
 `country` varchar(100) DEFAULT NULL,
  `page_count` int(11) NOT NULL,
 
 `dt` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 
 UNIQUE KEY `id` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `wi_user_details`
--

CREATE TABLE IF NOT EXISTS `wi_user_details` (
  `id_user_details` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `first_name` varchar(35) COLLATE utf8_unicode_ci ,
  `last_name` varchar(35) COLLATE utf8_unicode_ci ,
  `phone` varchar(30) COLLATE utf8_unicode_ci ,
  `address` varchar(30) COLLATE utf8_unicode_ci ,
  `country` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `region` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bio_body` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `website` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `youtube` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `friend_array` text COLLATE utf8_unicode_ci,
  `avatar` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` enum('Available','in_chat') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'Available',
  PRIMARY KEY (`id_user_details`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


--
-- Table structure for table `wi_user_roles`
--

CREATE TABLE IF NOT EXISTS `wi_user_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `wi_user_roles`
--

INSERT INTO `wi_user_roles` (`role_id`, `role`) VALUES
(1, 'User'),
(2, 'VIP'),
(3, 'Moderator'),
(4, 'Developer'),
(5, 'Administrator'),
(6, 'Head Administrator'),
(7, 'Owner');

-- --------------------------------------------------------

--
-- Table structure for table `wi_visitors_log`
--

CREATE TABLE IF NOT EXISTS `wi_visitors_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page` varchar(100) NOT NULL,
  `ip` text NOT NULL,
  `country` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
";
$this->installCleanCoreBaselineSql();
		//$this->WIdb = null;

		$this->createConfig(array(
            'site_name' => $name,
            'site_domain' => $dom,
            'site_url' => $script,
            'session_secure' => $session_secure,
            'http_only' => $http,
            'session_regenerate' => $session_regenerate,
            'cookie_only' => $cookieonly,
            'login_fingerprint' => $login_fingerprint,
            'max_login_attempts' => $max_login_attempts,
            'redirect_after_login' => $redirect_after_login,
            'password_encryption' => $encryption,
            'password_cost' => $cost,
            'mailer' => $mailer,
            'db_host' => $db_host,
            'db_name' => $db_name,
            'db_username' => $db_username,
            'db_pass' => $db_pass,
            'bootstrap_version' => $bootstrap_version,
            'password_salt' => $salt,
            'default_lang' => $default_lang,
            'wicms_version' => $Version,
            'reset_key_life' => $reset_key_life,
            'mail_confirm_required' => $mail_confirm_required,
            'register_confirm' => $register_confirm,
            'reg_pass_reset' => $reg_pass_reset
        ));

        $this->seedInstallSiteRow(array(
            'site_name' => $name,
            'site_domain' => $dom,
            'site_url' => $script,
            'session_secure' => $session_secure,
            'http_only' => $http,
            'session_regenerate' => $session_regenerate,
            'cookie_only' => $cookieonly,
            'login_fingerprint' => $login_fingerprint,
            'max_login_attempts' => $max_login_attempts,
            'redirect_after_login' => $redirect_after_login,
            'password_encryption' => $encryption,
            'password_cost' => $cost,
            'mailer' => $mailer,
            'db_host' => $db_host,
            'db_name' => $db_name,
            'db_username' => $db_username,
            'db_pass' => $db_pass,
            'bootstrap_version' => $bootstrap_version,
            'password_salt' => $salt,
            'default_lang' => $default_lang,
            'wicms_version' => $Version,
            'reset_key_life' => $reset_key_life,
            'mail_confirm_required' => $mail_confirm_required,
            'register_confirm' => $register_confirm,
            'reg_pass_reset' => $reg_pass_reset,
            'admin_email' => $email_address
        ));

    $admin_pass = $this->hashingPassword($admin_password, $encryption, $cost, $salt, 35000);
    //echo "ad" . $admin_pass;
    // generate a confirmation key that fits the legacy wi_members.confirmation_key varchar(40)
            $key = $this->_generateKey(40);

    $admin = "INSERT INTO `wi_members` (`user_id`, `email`, `username`, `password`, `confirmation_key`, `confirmed`, `password_reset_key`, `password_reset_confirmed`,`password_reset_timestamp`,`register_date`,`user_role`,`last_login`,`ip_addr`,`banned`) VALUES
      (1, '$email_address', '$admin_username', '$admin_pass', '$key', 'Y','','N','$dat','$dateOnly','$role', '$dat', '$ip','N');
      INSERT INTO `wi_user_details` (`id_user_details`, `user_id`) VALUES
      (1, 1);";

$this->WIdb->exec($admin);

    $this->seedCoreRuntimeBaseline($name);


    $this->WIdb = null;

    @file_put_contents(dirname(dirname(dirname(__FILE__))) . '/install.lock', 'Installed on ' . date('c') . PHP_EOL);

    $results = array(
      "status" => "install_completed"
    );
    echo json_encode($results);



	}	


    private function seedCoreRuntimeBaseline(string $siteName): void
    {
        $siteName = trim($siteName) !== '' ? trim($siteName) : 'WICMS';
        $siteNameSql = str_replace("'", "''", $siteName);

        $baseline = <<<'SQL'

-- -----------------------------------------------------------------------------
-- WICMS Installer v2.9 runtime baseline seed
-- Purpose: make a fresh WICMS install usable immediately after database/config
-- creation by seeding core pages, menus, metadata, CSS/JS registrations,
-- module power states, footer/header/social defaults, and plugin shell rows.
-- -----------------------------------------------------------------------------


-- Core admin access baseline. Without this, the first admin account can log in
-- as a member but fails WIAdmin->isAdmin() / WICMSAdminPageGuard.
INSERT INTO `wi_user_roles` (`role_id`, `role`)
SELECT 90, 'Super Admin'
WHERE NOT EXISTS (SELECT 1 FROM `wi_user_roles` WHERE `role_id` = 90);

INSERT INTO `wi_user_roles` (`role_id`, `role`)
SELECT 91, 'Platform Owner'
WHERE NOT EXISTS (SELECT 1 FROM `wi_user_roles` WHERE `role_id` = 91);

-- Upgrade the installer-created first account to a true admin role.
UPDATE `wi_members`
   SET `user_role` = 91,
       `confirmed` = 'Y',
       `banned` = 'N'
 WHERE `user_id` = 1;

CREATE TABLE IF NOT EXISTS `wi_permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `group_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_wi_permissions_code` (`code`),
  KEY `idx_wi_permissions_group` (`group_name`),
  KEY `idx_wi_permissions_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

INSERT INTO `wi_permissions` (`name`, `code`, `group_name`, `description`, `is_active`) VALUES
('View Users','users.view','Users','Can view users list',1),
('Create Users','users.create','Users','Can create users',1),
('Edit Users','users.edit','Users','Can edit users',1),
('Delete Users','users.delete','Users','Can delete/deactivate users',1),
('View Roles','roles.view','Roles','Can view roles',1),
('Create Roles','roles.create','Roles','Can create roles',1),
('Edit Roles','roles.edit','Roles','Can edit roles',1),
('Delete Roles','roles.delete','Roles','Can delete custom roles',1),
('View Permissions','permissions.view','Permissions','Can view permissions',1),
('Create Permissions','permissions.create','Permissions','Can create permissions',1),
('Edit Permissions','permissions.edit','Permissions','Can edit permissions',1),
('Delete Permissions','permissions.delete','Permissions','Can delete unused permissions',1),
('View Settings','settings.view','Settings','Can view WICMS settings',1),
('Edit Settings','settings.edit','Settings','Can edit WICMS settings',1),
('View Pages','pages.view','Pages','Can view pages',1),
('Create Pages','pages.create','Pages','Can create pages',1),
('Edit Pages','pages.edit','Pages','Can edit pages',1),
('Delete Pages','pages.delete','Pages','Can delete pages',1),
('View Media','media.view','Media','Can view media',1),
('Upload Media','media.upload','Media','Can upload media',1),
('Delete Media','media.delete','Media','Can delete media',1),
('Access Admin Area','admin.access','Admin','Can access the WICMS admin area',1),
('View Admin Dashboard','dashboard.view','Dashboard','Can view the WICMS admin dashboard',1),
('View Menus','menus.view','Menus','Can view menus',1),
('Manage Menus','menus.manage','Menus','Can create/edit menus',1),
('Sort Menus','menus.sort','Menus','Can sort menus',1),
('View Plugins','plugins.view','Plugins','Can view plugins',1),
('Manage Plugins','plugins.manage','Plugins','Can manage plugin settings',1),
('Install Plugins','plugins.install','Plugins','Can install plugins',1),
('Activate Plugins','plugins.activate','Plugins','Can activate plugins',1),
('Deactivate Plugins','plugins.deactivate','Plugins','Can deactivate plugins',1),
('Uninstall Plugins','plugins.uninstall','Plugins','Can uninstall plugins after confirmation',1),
('Developer Plugin Purge','plugins.developer_purge','Plugins','Can perform local/developer purge',1),
('View Packages','packages.view','Packages','Can view packages/add-ons',1),
('Manage Packages','packages.manage','Packages','Can manage package settings',1),
('Install Packages','packages.install','Packages','Can install packages',1),
('Activate Packages','packages.activate','Packages','Can activate packages/add-ons',1),
('Deactivate Packages','packages.deactivate','Packages','Can deactivate packages/add-ons',1),
('Uninstall Packages','packages.uninstall','Packages','Can uninstall packages after confirmation',1),
('View Modules','modules.view','Modules','Can view modules',1),
('Manage Modules','modules.manage','Modules','Can manage modules',1),
('View Themes','themes.view','Themes','Can view themes',1),
('Manage Themes','themes.manage','Themes','Can manage themes',1),
('View Legal & Cookies','legal_cookies.view','Legal & Cookies','Can view consent/legal/cookie settings',1),
('Manage Legal & Cookies','legal_cookies.manage','Legal & Cookies','Can manage consent/legal/cookie settings',1),
('View Bug Reports','bug_reports.view','Bug Reports','Can view bug reports',1),
('Manage Bug Reports','bug_reports.manage','Bug Reports','Can manage bug reports',1),
('Resolve Bug Reports','bug_reports.resolve','Bug Reports','Can resolve bug reports',1),
('View Audit Logs','audit_logs.view','System','Can view system/audit logs',1),
('View System Health','system.health.view','System','Can view system health',1),
('Manage System Health','system.health.manage','System','Can manage system health/repairs',1),
('API Access','api.access','API','Can access API routes',1),
('API Read','api.read','API','Can read through API routes',1),
('API Write','api.write','API','Can write through API routes',1),
('API Admin','api.admin','API','Can perform trusted API admin actions',1),
('View Site Settings','site.view','Site','Can view website/site settings',1),
('Edit Site Settings','site.edit','Site','Can edit website/site settings',1),
('View Legal & Cookies','site.legal_cookies.view','Site','Can view legal and cookie settings',1),
('Manage Legal & Cookies','site.legal_cookies.manage','Site','Can manage legal and cookie settings',1),
('Delete Modules','modules.delete','Modules','Can uninstall/delete modules and elements',1),
('Install Themes','themes.install','Themes','Can install themes',1),
('Activate Themes','themes.activate','Themes','Can activate themes',1),
('Delete Themes','themes.delete','Themes','Can delete themes',1)
ON DUPLICATE KEY UPDATE
  `name` = VALUES(`name`),
  `group_name` = VALUES(`group_name`),
  `description` = VALUES(`description`),
  `is_active` = VALUES(`is_active`);

INSERT INTO `wi_role_permissions` (`role_id`, `permission_id`)
SELECT 90, p.`id`
  FROM `wi_permissions` p
 WHERE NOT EXISTS (
       SELECT 1 FROM `wi_role_permissions` rp
        WHERE rp.`role_id` = 90
          AND rp.`permission_id` = p.`id`
 );

INSERT INTO `wi_role_permissions` (`role_id`, `permission_id`)
SELECT 91, p.`id`
  FROM `wi_permissions` p
 WHERE NOT EXISTS (
       SELECT 1 FROM `wi_role_permissions` rp
        WHERE rp.`role_id` = 91
          AND rp.`permission_id` = p.`id`
 );

-- Core social rows used by WIWebsite->Social() and top_head.
CREATE TABLE IF NOT EXISTS `wi_social` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `href` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '#',
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `wi_social` (`id`, `href`, `name`) VALUES
(1, '#', 'facebook'),
(2, '#', 'twitter'),
(3, '#', 'instagram')
ON DUPLICATE KEY UPDATE `href` = VALUES(`href`), `name` = VALUES(`name`);

-- Core pages. These map root files to WIAdmin/WIModule/pages/* modules.
INSERT INTO `wi_page` (`name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`)
SELECT 'index', '1', '1', '0', '0', '0', 'welcome_box', '1'
WHERE NOT EXISTS (SELECT 1 FROM `wi_page` WHERE `name` = 'index');

INSERT INTO `wi_page` (`name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`)
SELECT 'alogin', '1', '1', '0', '0', '0', 'alogin', '1'
WHERE NOT EXISTS (SELECT 1 FROM `wi_page` WHERE `name` = 'alogin');

INSERT INTO `wi_page` (`name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`)
SELECT 'login', '1', '1', '0', '0', '0', 'login', '1'
WHERE NOT EXISTS (SELECT 1 FROM `wi_page` WHERE `name` = 'login');

INSERT INTO `wi_page` (`name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`)
SELECT 'register', '1', '1', '0', '0', '0', 'register', '1'
WHERE NOT EXISTS (SELECT 1 FROM `wi_page` WHERE `name` = 'register');

INSERT INTO `wi_page` (`name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`)
SELECT 'confirm', '1', '1', '0', '0', '0', 'confirm', '1'
WHERE NOT EXISTS (SELECT 1 FROM `wi_page` WHERE `name` = 'confirm');

INSERT INTO `wi_page` (`name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`)
SELECT 'passwordreset', '1', '1', '0', '0', '0', 'passwordreset', '1'
WHERE NOT EXISTS (SELECT 1 FROM `wi_page` WHERE `name` = 'passwordreset');

INSERT INTO `wi_page` (`name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`)
SELECT 'forgotpass', '1', '1', '0', '0', '0', 'passwordreset', '1'
WHERE NOT EXISTS (SELECT 1 FROM `wi_page` WHERE `name` = 'forgotpass');

INSERT INTO `wi_page` (`name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`)
SELECT 'reset-password', '1', '1', '0', '0', '0', 'passwordreset', '1'
WHERE NOT EXISTS (SELECT 1 FROM `wi_page` WHERE `name` = 'reset-password');

INSERT INTO `wi_page` (`name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`)
SELECT 'contact_us', '1', '1', '0', '0', '0', 'contact_us', '1'
WHERE NOT EXISTS (SELECT 1 FROM `wi_page` WHERE `name` = 'contact_us');

INSERT INTO `wi_page` (`name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`)
SELECT 'about_us', '1', '1', '0', '0', '0', 'about_us', '1'
WHERE NOT EXISTS (SELECT 1 FROM `wi_page` WHERE `name` = 'about_us');

INSERT INTO `wi_page` (`name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`)
SELECT 'profile', '1', '1', '0', '0', '0', 'profile', '1'
WHERE NOT EXISTS (SELECT 1 FROM `wi_page` WHERE `name` = 'profile');

-- Keep important core modules active enough for fresh runtime.
UPDATE `wi_mod`
   SET `mod_status` = 'enabled', `mod_powered` = 'power_on'
 WHERE `module_name` IN ('top_head', 'Panel', 'welcome_box');

-- Make sure page/modules that exist as files are represented in wi_mod.
INSERT INTO `wi_mod` (`mod_status`, `mod_powered`, `mod_type`, `mod_author`, `module_name`, `Mod_description`, `mod_font`)
SELECT 'enabled', 'power_on', 'custom', 'WICMS', 'login', 'Core member login page', ''
WHERE NOT EXISTS (SELECT 1 FROM `wi_mod` WHERE `module_name` = 'login');

INSERT INTO `wi_mod` (`mod_status`, `mod_powered`, `mod_type`, `mod_author`, `module_name`, `Mod_description`, `mod_font`)
SELECT 'enabled', 'power_on', 'custom', 'WICMS', 'register', 'Core member register page', ''
WHERE NOT EXISTS (SELECT 1 FROM `wi_mod` WHERE `module_name` = 'register');

INSERT INTO `wi_mod` (`mod_status`, `mod_powered`, `mod_type`, `mod_author`, `module_name`, `Mod_description`, `mod_font`)
SELECT 'enabled', 'power_on', 'custom', 'WICMS', 'contact_us', 'Core contact page', ''
WHERE NOT EXISTS (SELECT 1 FROM `wi_mod` WHERE `module_name` = 'contact_us');

INSERT INTO `wi_mod` (`mod_status`, `mod_powered`, `mod_type`, `mod_author`, `module_name`, `Mod_description`, `mod_font`)
SELECT 'enabled', 'power_on', 'custom', 'WICMS', 'about_us', 'Core about page', ''
WHERE NOT EXISTS (SELECT 1 FROM `wi_mod` WHERE `module_name` = 'about_us');

INSERT INTO `wi_mod` (`mod_status`, `mod_powered`, `mod_type`, `mod_author`, `module_name`, `Mod_description`, `mod_font`)
SELECT 'enabled', 'power_on', 'custom', 'WICMS', 'passwordreset', 'Core password reset page', ''
WHERE NOT EXISTS (SELECT 1 FROM `wi_mod` WHERE `module_name` = 'passwordreset');

-- wi_modules is a looser installed-module inventory used by newer module tools.
INSERT INTO `wi_modules` (`name`, `mod_id`)
SELECT 'top_head', (SELECT `mod_id` FROM `wi_mod` WHERE `module_name` = 'top_head' LIMIT 1)
WHERE NOT EXISTS (SELECT 1 FROM `wi_modules` WHERE `name` = 'top_head');

INSERT INTO `wi_modules` (`name`, `mod_id`)
SELECT 'welcome_box', (SELECT `mod_id` FROM `wi_mod` WHERE `module_name` = 'welcome_box' LIMIT 1)
WHERE NOT EXISTS (SELECT 1 FROM `wi_modules` WHERE `name` = 'welcome_box');

INSERT INTO `wi_modules` (`name`, `mod_id`)
SELECT 'alogin', NULL
WHERE NOT EXISTS (SELECT 1 FROM `wi_modules` WHERE `name` = 'alogin');

INSERT INTO `wi_modules` (`name`, `mod_id`)
SELECT 'login', NULL
WHERE NOT EXISTS (SELECT 1 FROM `wi_modules` WHERE `name` = 'login');

INSERT INTO `wi_modules` (`name`, `mod_id`)
SELECT 'register', NULL
WHERE NOT EXISTS (SELECT 1 FROM `wi_modules` WHERE `name` = 'register');

INSERT INTO `wi_modules` (`name`, `mod_id`)
SELECT 'profile', NULL
WHERE NOT EXISTS (SELECT 1 FROM `wi_modules` WHERE `name` = 'profile');

INSERT INTO `wi_modules` (`name`, `mod_id`)
SELECT 'contact_us', NULL
WHERE NOT EXISTS (SELECT 1 FROM `wi_modules` WHERE `name` = 'contact_us');

-- Public menu defaults. Keep this small/generic; ECMA gets its own theme/menu later.
INSERT INTO `wi_menu` (`label`, `link`, `parent`, `sort`, `lang`)
SELECT 'Home', 'index.php', 0, 0, 'home'
WHERE NOT EXISTS (SELECT 1 FROM `wi_menu` WHERE `link` = 'index.php' AND `parent` = 0);

INSERT INTO `wi_menu` (`label`, `link`, `parent`, `sort`, `lang`)
SELECT 'About', 'about_us.php', 0, 1, 'about_us'
WHERE NOT EXISTS (SELECT 1 FROM `wi_menu` WHERE `link` = 'about_us.php' AND `parent` = 0);

INSERT INTO `wi_menu` (`label`, `link`, `parent`, `sort`, `lang`)
SELECT 'Contact', 'contact_us.php', 0, 2, 'contact_us'
WHERE NOT EXISTS (SELECT 1 FROM `wi_menu` WHERE `link` = 'contact_us.php' AND `parent` = 0);

INSERT INTO `wi_menu` (`label`, `link`, `parent`, `sort`, `lang`)
SELECT 'Login', 'login.php', 0, 3, 'login'
WHERE NOT EXISTS (SELECT 1 FROM `wi_menu` WHERE `link` = 'login.php' AND `parent` = 0);

INSERT INTO `wi_menu` (`label`, `link`, `parent`, `sort`, `lang`)
SELECT 'Register', 'register.php', 0, 4, 'register'
WHERE NOT EXISTS (SELECT 1 FROM `wi_menu` WHERE `link` = 'register.php' AND `parent` = 0);

-- Global CSS registrations. WIWebsite loads global/all/* plus the current page.
-- WICMS generic fallback baseline CSS. Loaded before/alongside theme-specific CSS.
INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/wicms-baseline.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/wicms-baseline.css' AND `page` = 'global');


INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/vendor/bootstrap.min.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/frameworks/bootstrap.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/frameworks/bootstrap.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/font-awesome.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/font-awesome.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/frameworks/header.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/frameworks/header.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/frameworks/menus.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/frameworks/menus.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/frameworks/footer.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/frameworks/footer.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/login_panel/css/slide.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/login_panel/css/slide.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/style.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/style.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/system.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/system.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/WIMarketing.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/WIMarketing.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'user/css/profile.css', 'stylesheet', 'profile'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'user/css/profile.css' AND `page` = 'profile');

-- Global JavaScript registrations. These are generic WICMS public assets.
INSERT INTO `wi_scripts` (`src`, `page`)
SELECT 'site/js/wicms-baseline.js', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_scripts` WHERE `src` = 'site/js/wicms-baseline.js' AND `page` = 'global');


INSERT INTO `wi_scripts` (`src`, `page`)
SELECT 'site/js/frameworks/JQuery.js', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_scripts` WHERE `src` = 'site/js/frameworks/JQuery.js' AND `page` = 'global');

INSERT INTO `wi_scripts` (`src`, `page`)
SELECT 'site/js/frameworks/bootstrap.js', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_scripts` WHERE `src` = 'site/js/frameworks/bootstrap.js' AND `page` = 'global');

INSERT INTO `wi_scripts` (`src`, `page`)
SELECT 'site/js/login_panel/js/slide.js', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_scripts` WHERE `src` = 'site/js/login_panel/js/slide.js' AND `page` = 'global');

INSERT INTO `wi_scripts` (`src`, `page`)
SELECT 'site/js/main.js', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_scripts` WHERE `src` = 'site/js/main.js' AND `page` = 'global');

INSERT INTO `wi_scripts` (`src`, `page`)
SELECT 'site/js/WIMarketing.js', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_scripts` WHERE `src` = 'site/js/WIMarketing.js' AND `page` = 'global');

-- Page-specific JS that is not theme-based in some builds is still kept in WI_scripts
-- for backwards compatibility when a theme copy exists.
INSERT INTO `wi_scripts` (`src`, `page`)
SELECT 'site/js/login.js', 'login'
WHERE NOT EXISTS (SELECT 1 FROM `wi_scripts` WHERE `src` = 'site/js/login.js' AND `page` = 'login');

INSERT INTO `wi_scripts` (`src`, `page`)
SELECT 'site/js/register.js', 'register'
WHERE NOT EXISTS (SELECT 1 FROM `wi_scripts` WHERE `src` = 'site/js/register.js' AND `page` = 'register');

-- Global and core-page metadata.
INSERT INTO `wi_meta` (`page`, `name`, `content`, `author`)
SELECT 'global', 'viewport', 'width=device-width, initial-scale=1', 'WICMS'
WHERE NOT EXISTS (SELECT 1 FROM `wi_meta` WHERE `page` = 'global' AND `name` = 'viewport');

INSERT INTO `wi_meta` (`page`, `name`, `content`, `author`)
SELECT 'global', 'description', 'WICMS content management system', 'WICMS'
WHERE NOT EXISTS (SELECT 1 FROM `wi_meta` WHERE `page` = 'global' AND `name` = 'description');

INSERT INTO `wi_meta` (`page`, `name`, `content`, `author`)
SELECT 'login', 'description', 'Login to your WICMS account', 'WICMS'
WHERE NOT EXISTS (SELECT 1 FROM `wi_meta` WHERE `page` = 'login' AND `name` = 'description');

INSERT INTO `wi_meta` (`page`, `name`, `content`, `author`)
SELECT 'register', 'description', 'Create your WICMS account', 'WICMS'
WHERE NOT EXISTS (SELECT 1 FROM `wi_meta` WHERE `page` = 'register' AND `name` = 'description');

INSERT INTO `wi_meta` (`page`, `name`, `content`, `author`)
SELECT 'passwordreset', 'description', 'Reset your WICMS password', 'WICMS'
WHERE NOT EXISTS (SELECT 1 FROM `wi_meta` WHERE `page` = 'passwordreset' AND `name` = 'description');

-- Header/footer defaults. Keep copy generic; brand theme can change later.
UPDATE `wi_footer`
   SET `website_name` = COALESCE(NULLIF(`website_name`, ''), '__SITE_NAME__')
 WHERE `footer_id` = 1;

INSERT INTO `wi_footer` (`footer_id`, `footer_content`, `footer_linking`, `website_name`)
SELECT 1, '', '', '__SITE_NAME__'
WHERE NOT EXISTS (SELECT 1 FROM `wi_footer` WHERE `footer_id` = 1);

INSERT INTO `wi_header` (`header_id`, `logo`, `bk_header_image`, `header_image`, `header_content`, `header_slogan`)
SELECT 1, 'wi_cms_logo.jpg', '', 'bk_header', '', ''
WHERE NOT EXISTS (SELECT 1 FROM `wi_header` WHERE `header_id` = 1);

-- Plugin manager shell must exist but stays empty until plugins are installed.
CREATE TABLE IF NOT EXISTS `wi_plugin` (
  `plugin_id` int(11) NOT NULL AUTO_INCREMENT,
  `plugin` varchar(255) NOT NULL,
  `activated` enum('true','false') NOT NULL DEFAULT 'false',
  PRIMARY KEY (`plugin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

SQL;

        $baseline = str_replace('__SITE_NAME__', $siteNameSql, $baseline);
        $this->WIdb->exec($baseline);
    }


    /**
     * Install the cleaned WICMS core SQL baseline generated from Aurevia.
     *
     * The old installer contains a large legacy hard-coded SQL string. For the
     * clean base we keep the ECMA installer shell, but use WIInstall/wicms-ivo.sql
     * as the source of truth so modern core tables, permissions, profile tables,
     * legal/cookie tables and package-manager schemas are created correctly.
     */
    private function installCleanCoreBaselineSql(): void
    {
        $sqlPath = dirname(dirname(dirname(__FILE__))) . '/wicms-ivo.sql';
        if (!is_file($sqlPath)) {
            throw new RuntimeException('Missing WICMS baseline SQL: ' . $sqlPath);
        }

        $sql = (string) file_get_contents($sqlPath);
        if (trim($sql) === '') {
            throw new RuntimeException('WICMS baseline SQL is empty.');
        }

        $this->executeSqlBatch($sql, 'WIInstall/wicms-ivo.sql');
    }

    /**
     * Execute a SQL dump safely one statement at a time.
     *
     * PDO/MySQL can appear to accept a multi-statement dump but stop part-way
     * through on some localhost stacks. Running each complete statement keeps
     * the clean installer reliable and reports the exact failing area instead
     * of leaving a half-created database that only reaches tables such as
     * wi_tasks.
     */
    private function executeSqlBatch(string $sql, string $sourceLabel = 'SQL batch'): void
    {
        $statements = $this->splitSqlStatements($sql);

        foreach ($statements as $index => $statement) {
            $statement = trim($statement);
            if ($statement === '') {
                continue;
            }

            try {
                $this->WIdb->exec($statement);
            } catch (PDOException $e) {
                $preview = preg_replace('/\s+/', ' ', substr($statement, 0, 220));
                throw new RuntimeException(
                    $sourceLabel . ' failed at statement ' . ($index + 1) . ': ' . $e->getMessage() . ' | ' . $preview,
                    0,
                    $e
                );
            }
        }
    }

    /**
     * Split SQL on real statement semicolons while preserving strings/comments.
     */
    private function splitSqlStatements(string $sql): array
    {
        $statements = [];
        $buffer = '';
        $length = strlen($sql);
        $quote = null;
        $escape = false;
        $lineComment = false;
        $blockComment = false;

        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            $next = $i + 1 < $length ? $sql[$i + 1] : '';

            if ($lineComment) {
                $buffer .= $char;
                if ($char === "\n") {
                    $lineComment = false;
                }
                continue;
            }

            if ($blockComment) {
                $buffer .= $char;
                if ($char === '*' && $next === '/') {
                    $buffer .= $next;
                    $i++;
                    $blockComment = false;
                }
                continue;
            }

            if ($quote !== null) {
                $buffer .= $char;

                if ($escape) {
                    $escape = false;
                    continue;
                }

                if ($char === '\\') {
                    $escape = true;
                    continue;
                }

                if ($char === $quote) {
                    $quote = null;
                }
                continue;
            }

            if (($char === '-' && $next === '-') || $char === '#') {
                $lineComment = true;
                $buffer .= $char;
                if ($char === '-') {
                    $buffer .= $next;
                    $i++;
                }
                continue;
            }

            if ($char === '/' && $next === '*') {
                $blockComment = true;
                $buffer .= $char . $next;
                $i++;
                continue;
            }

            if ($char === "'" || $char === '"' || $char === '`') {
                $quote = $char;
                $buffer .= $char;
                continue;
            }

            if ($char === ';') {
                $statements[] = $buffer;
                $buffer = '';
                continue;
            }

            $buffer .= $char;
        }

        if (trim($buffer) !== '') {
            $statements[] = $buffer;
        }

        return $statements;
    }

    /**
     * Seed the first wi_site row after the cleaned baseline creates the table.
     */
    private function seedInstallSiteRow(array $settings): void
    {
        $rawHost = (string)($settings['db_host'] ?? 'localhost');
        $dbPort = '3306';
        if (preg_match('/^(.*);port=([0-9]+)$/', $rawHost, $match)) {
            $rawHost = $match[1];
            $dbPort = $match[2];
        }

        $sql = "INSERT INTO `wi_site` (
            `id`, `site_name`, `site_domain`, `site_url`, `favicon`,
            `db_host`, `db_username`, `db_pass`, `db_name`, `db_port`, `db_type`,
            `secure_session`, `http_only`, `regenerate_id`, `use_only_cookie`, `login_fingerprint`,
            `max_login_attempts`, `redirect_after_login`, `password_encryption`, `encryption_cost`,
            `sha512_iterations`, `password_salt`, `reset_key_life`, `mail_confirm_required`,
            `register_confirm`, `reg_pass_reset`, `mailer`, `smpt_host`, `smpt_port`,
            `smpt_username`, `smpt_password`, `smpt_encryption`, `social_callback_url`,
            `google_enabled`, `google_id`, `google_secret`, `google_map_api`, `google_charts_api_key`,
            `facebook_enabled`, `facebook_id`, `facebook_secret`, `twitter_enabled`, `twitter_key`,
            `twitter_secret`, `default_lang`, `multi_lang`, `lang_choice`, `bootstrap_version`,
            `wicms_version`, `left_sidebar`, `right_sidebar`, `contact_email`
        ) VALUES (
            1, :site_name, :site_domain, :site_url, '',
            :db_host, :db_username, :db_pass, :db_name, :db_port, 'mysql',
            :session_secure, :http_only, :session_regenerate, :cookie_only, :login_fingerprint,
            :max_login_attempts, :redirect_after_login, :password_encryption, :password_cost,
            35000, :password_salt, :reset_key_life, :mail_confirm_required,
            :register_confirm, :reg_pass_reset, :mailer, '', '',
            '', '', '', 'socialauth',
            'false', '', '', '', '',
            'false', '', '', 'false', '',
            '', :default_lang, 'on', 'wilang', :bootstrap_version,
            :wicms_version, '0', '0', :admin_email
        ) ON DUPLICATE KEY UPDATE
            `site_name` = VALUES(`site_name`),
            `site_domain` = VALUES(`site_domain`),
            `site_url` = VALUES(`site_url`),
            `db_host` = VALUES(`db_host`),
            `db_username` = VALUES(`db_username`),
            `db_pass` = VALUES(`db_pass`),
            `db_name` = VALUES(`db_name`),
            `db_port` = VALUES(`db_port`),
            `secure_session` = VALUES(`secure_session`),
            `http_only` = VALUES(`http_only`),
            `regenerate_id` = VALUES(`regenerate_id`),
            `use_only_cookie` = VALUES(`use_only_cookie`),
            `login_fingerprint` = VALUES(`login_fingerprint`),
            `max_login_attempts` = VALUES(`max_login_attempts`),
            `redirect_after_login` = VALUES(`redirect_after_login`),
            `password_encryption` = VALUES(`password_encryption`),
            `encryption_cost` = VALUES(`encryption_cost`),
            `password_salt` = VALUES(`password_salt`),
            `reset_key_life` = VALUES(`reset_key_life`),
            `mail_confirm_required` = VALUES(`mail_confirm_required`),
            `register_confirm` = VALUES(`register_confirm`),
            `reg_pass_reset` = VALUES(`reg_pass_reset`),
            `mailer` = VALUES(`mailer`),
            `default_lang` = VALUES(`default_lang`),
            `multi_lang` = VALUES(`multi_lang`),
            `lang_choice` = VALUES(`lang_choice`),
            `bootstrap_version` = VALUES(`bootstrap_version`),
            `wicms_version` = VALUES(`wicms_version`),
            `contact_email` = VALUES(`contact_email`)";

        $stmt = $this->WIdb->prepare($sql);
        $stmt->execute([
            'site_name' => (string)($settings['site_name'] ?? 'WICMS'),
            'site_domain' => (string)($settings['site_domain'] ?? 'localhost'),
            'site_url' => (string)($settings['site_url'] ?? ''),
            'db_host' => $rawHost,
            'db_username' => (string)($settings['db_username'] ?? ''),
            'db_pass' => (string)($settings['db_pass'] ?? ''),
            'db_name' => (string)($settings['db_name'] ?? ''),
            'db_port' => $dbPort,
            'session_secure' => (string)($settings['session_secure'] ?? 'false'),
            'http_only' => (string)($settings['http_only'] ?? 'true'),
            'session_regenerate' => (string)($settings['session_regenerate'] ?? 'true'),
            'cookie_only' => (string)($settings['cookie_only'] ?? '1'),
            'login_fingerprint' => (string)($settings['login_fingerprint'] ?? 'false'),
            'max_login_attempts' => (string)($settings['max_login_attempts'] ?? '5'),
            'redirect_after_login' => (string)($settings['redirect_after_login'] ?? 'WIMembers/profile.php'),
            'password_encryption' => (string)($settings['password_encryption'] ?? 'bcrypt'),
            'password_cost' => (string)($settings['password_cost'] ?? '12'),
            'password_salt' => (string)($settings['password_salt'] ?? ''),
            'reset_key_life' => (string)($settings['reset_key_life'] ?? '24'),
            'mail_confirm_required' => (string)($settings['mail_confirm_required'] ?? 'false'),
            'register_confirm' => (string)($settings['register_confirm'] ?? 'confirm'),
            'reg_pass_reset' => (string)($settings['reg_pass_reset'] ?? 'passwordreset'),
            'mailer' => (string)($settings['mailer'] ?? 'mail'),
            'default_lang' => (string)($settings['default_lang'] ?? 'en'),
            'bootstrap_version' => (string)($settings['bootstrap_version'] ?? '4'),
            'wicms_version' => (string)($settings['wicms_version'] ?? '3.0'),
            'admin_email' => (string)($settings['admin_email'] ?? ''),
        ]);
    }

	private function createConfig(array $settings)
		{
            $root = dirname(dirname(dirname(dirname(__FILE__))));
            $configDir = $root . '/WICore/WIClass';
            if (!is_dir($configDir)) {
                @mkdir($configDir, 0755, true);
            }

            $rawHost = (string)($settings['db_host'] ?? 'localhost');
            $dbPort = '3306';
            if (preg_match('/^(.*);port=([0-9]+)$/', $rawHost, $match)) {
                $rawHost = $match[1];
                $dbPort = $match[2];
            }

            $environment = isset($_POST['environment_mode']) ? strtolower((string)$_POST['environment_mode']) : 'local';
            if (!in_array($environment, array('local', 'staging', 'production'), true)) {
                $environment = 'local';
            }

            $securityKey = isset($_POST['security_key']) && trim((string)$_POST['security_key']) !== ''
                ? trim((string)$_POST['security_key'])
                : $this->installerRandomHex(32);

            $sessionPrefix = isset($_POST['session_prefix']) ? trim((string)$_POST['session_prefix']) : 'wi_session_';
            $sessionName = preg_replace('/[^A-Za-z0-9_]/', '', $sessionPrefix);
            if ($sessionName === '') {
                $sessionName = 'wi_session_';
            }
            $sessionName .= 'sid';

            $sessionTimeout = isset($_POST['session_timeout']) ? max(5, (int)$_POST['session_timeout']) * 60 : 3600;
            $appDebug = $environment === 'production' ? false : true;

            $env = array(
                'APP_ENV' => $environment,
                'APP_DEBUG' => $appDebug ? 'true' : 'false',
                'APP_KEY' => $securityKey,
                'WEBSITE_NAME' => (string)($settings['site_name'] ?? 'WICMS'),
                'WEBSITE_DOMAIN' => (string)($settings['site_domain'] ?? 'localhost'),
                'SCRIPT_URL' => (string)($settings['site_url'] ?? ''),
                'BOOTSTRAP_VERSION' => (string)($settings['bootstrap_version'] ?? '1'),
                'DB_HOST' => $rawHost,
                'DB_NAME' => (string)($settings['db_name'] ?? ''),
                'DB_USER' => (string)($settings['db_username'] ?? ''),
                'DB_PASS' => (string)($settings['db_pass'] ?? ''),
                'DB_PORT' => $dbPort,
                'DB_TYPE' => 'mysql',
                'SESSION_NAME' => $sessionName,
                'SESSION_SAMESITE' => 'Lax',
                'SESSION_IDLE_TIMEOUT' => (string)$sessionTimeout,
                'SESSION_ABSOLUTE_TIMEOUT' => '28800',
                'SESSION_SECURE' => (string)($settings['session_secure'] ?? 'false'),
                'SESSION_HTTP_ONLY' => (string)($settings['http_only'] ?? 'true'),
                'SESSION_REGENERATE_ID' => (string)($settings['session_regenerate'] ?? 'true'),
                'SESSION_USE_ONLY_COOKIES' => (string)($settings['cookie_only'] ?? '1'),
                'CSRF_TOKEN_TTL' => '7200',
                'LOGIN_MAX_LOGIN_ATTEMPTS' => (string)($settings['max_login_attempts'] ?? '5'),
                'LOGIN_FINGERPRINT' => (string)($settings['login_fingerprint'] ?? 'true'),
                'SUCCESS_LOGIN_REDIRECT' => (string)($settings['redirect_after_login'] ?? 'WIMembers/profile.php'),
                'PASSWORD_ENCRYPTION' => (string)($settings['password_encryption'] ?? 'bcrypt'),
                'PASSWORD_BCRYPT_COST' => (string)($settings['password_cost'] ?? '12'),
                'PASSWORD_SHA512_ITERATIONS' => '35000',
                'PASSWORD_SALT' => (string)($settings['password_salt'] ?? ''),
                'PASSWORD_RESET_KEY_LIFE' => (string)($settings['reset_key_life'] ?? '24'),
                'MAIL_CONFIRMATION_REQUIRED' => (string)($settings['mail_confirm_required'] ?? 'false'),
                'REGISTER_CONFIRM' => (string)($settings['register_confirm'] ?? 'confirm'),
                'REGISTER_PASSWORD_RESET' => (string)($settings['reg_pass_reset'] ?? 'passwordreset'),
                'MAILER' => (string)($settings['mailer'] ?? 'mail'),
                'SMTP_HOST' => '',
                'SMTP_PORT' => '',
                'SMTP_USERNAME' => '',
                'SMTP_PASSWORD' => '',
                'SMTP_ENCRYPTION' => '',
                'SOCIAL_CALLBACK_URI' => 'socialauth',
                'GOOGLE_ENABLED' => 'false',
                'GOOGLE_ID' => '',
                'GOOGLE_SECRET' => '',
                'FACEBOOK_ENABLED' => 'false',
                'FACEBOOK_ID' => '',
                'FACEBOOK_SECRET' => '',
                'TWITTER_ENABLED' => 'false',
                'TWITTER_KEY' => '',
                'TWITTER_SECRET' => '',
                'DEFAULT_LANGUAGE' => (string)($settings['default_lang'] ?? 'en'),
                'WICMS_VERSION' => (string)($settings['wicms_version'] ?? '2.0')
            );

            $envLines = array();
            foreach ($env as $key => $value) {
                $envLines[] = $key . '=' . $this->envQuote((string)$value);
            }
            @file_put_contents($root . '/.env', implode(PHP_EOL, $envLines) . PHP_EOL);

            $config = '<?php' . PHP_EOL;
            $config .= 'declare(strict_types=1);' . PHP_EOL . PHP_EOL;
            $config .= '/**' . PHP_EOL . ' * Generated by WICMS Installer v3.0.' . PHP_EOL . ' */' . PHP_EOL;
            $config .= 'final class WIConfig' . PHP_EOL . '{' . PHP_EOL;
            $config .= '    private static bool $loaded = false;' . PHP_EOL;
            $config .= '    private static array $env = ' . var_export($env, true) . ';' . PHP_EOL . PHP_EOL;
            $config .= '    public static function load(): void' . PHP_EOL . '    {' . PHP_EOL;
            $config .= '        if (self::$loaded) { return; }' . PHP_EOL;
            $config .= '        self::$loaded = true;' . PHP_EOL;
            $config .= '        foreach (self::$env as $key => $value) {' . PHP_EOL;
            $config .= '            $_ENV[$key] = (string)$value; $_SERVER[$key] = (string)$value;' . PHP_EOL;
            $config .= '            if (function_exists("putenv")) { putenv($key . "=" . $value); }' . PHP_EOL;
            $config .= '        }' . PHP_EOL;
            $config .= '        self::defineCoreConstants();' . PHP_EOL;
            $config .= '    }' . PHP_EOL . PHP_EOL;
            $config .= '    public static function env(string $key, mixed $default = null): mixed' . PHP_EOL . '    {' . PHP_EOL;
            $config .= '        self::load();' . PHP_EOL;
            $config .= '        $server = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);' . PHP_EOL;
            $config .= '        return self::$env[$key] ?? ($server === false ? $default : ($server ?? $default));' . PHP_EOL;
            $config .= '    }' . PHP_EOL . PHP_EOL;
            $config .= '    private static function defineCoreConstants(): void' . PHP_EOL . '    {' . PHP_EOL;
            $defs = array(
                'APP_ENV' => '(string) self::env("APP_ENV", "local")',
                'APP_DEBUG' => 'self::toBool(self::env("APP_DEBUG", true))',
                'APP_KEY' => '(string) self::env("APP_KEY", "")',
                'WEBSITE_NAME' => '(string) self::env("WEBSITE_NAME", "WICMS")',
                'WEBSITE_DOMAIN' => '(string) self::env("WEBSITE_DOMAIN", "localhost")',
                'SCRIPT_URL' => '(string) self::env("SCRIPT_URL", "")',
                'BOOTSTRAP_VERSION' => '(string) self::env("BOOTSTRAP_VERSION", "1")',
                'DB_HOST' => '(string) self::env("DB_HOST", "localhost")',
                'DB_NAME' => '(string) self::env("DB_NAME", "")',
                'DB_USER' => '(string) self::env("DB_USER", "root")',
                'DB_PASS' => '(string) self::env("DB_PASS", "")',
                'DB_PORT' => '(string) self::env("DB_PORT", "3306")',
                'DB_TYPE' => '(string) self::env("DB_TYPE", "mysql")',
                'SESSION_NAME' => '(string) self::env("SESSION_NAME", "WICMSSESSID")',
                'SESSION_SAMESITE' => '(string) self::env("SESSION_SAMESITE", "Lax")',
                'SESSION_IDLE_TIMEOUT' => '(int) self::env("SESSION_IDLE_TIMEOUT", 1800)',
                'SESSION_ABSOLUTE_TIMEOUT' => '(int) self::env("SESSION_ABSOLUTE_TIMEOUT", 28800)',
                'SESSION_SECURE' => 'self::toBool(self::env("SESSION_SECURE", false))',
                'SESSION_HTTP_ONLY' => 'self::toBool(self::env("SESSION_HTTP_ONLY", true))',
                'SESSION_REGENERATE_ID' => 'self::toBool(self::env("SESSION_REGENERATE_ID", true))',
                'SESSION_USE_ONLY_COOKIES' => 'self::toBool(self::env("SESSION_USE_ONLY_COOKIES", true))',
                'CSRF_TOKEN_TTL' => '(int) self::env("CSRF_TOKEN_TTL", 7200)',
                'LOGIN_MAX_LOGIN_ATTEMPTS' => '(int) self::env("LOGIN_MAX_LOGIN_ATTEMPTS", 5)',
                'LOGIN_FINGERPRINT' => 'self::toBool(self::env("LOGIN_FINGERPRINT", true))',
                'SUCCESS_LOGIN_REDIRECT' => 'serialize(array("default" => (string) self::env("SUCCESS_LOGIN_REDIRECT", "WIMembers/profile.php")))',
                'PASSWORD_ENCRYPTION' => '(string) self::env("PASSWORD_ENCRYPTION", "bcrypt")',
                'PASSWORD_BCRYPT_COST' => '(int) self::env("PASSWORD_BCRYPT_COST", 12)',
                'PASSWORD_SHA512_ITERATIONS' => '(int) self::env("PASSWORD_SHA512_ITERATIONS", 35000)',
                'PASSWORD_SALT' => '(string) self::env("PASSWORD_SALT", "")',
                'PASSWORD_RESET_KEY_LIFE' => '(int) self::env("PASSWORD_RESET_KEY_LIFE", 24)',
                'MAIL_CONFIRMATION_REQUIRED' => 'self::toBool(self::env("MAIL_CONFIRMATION_REQUIRED", false))',
                'REGISTER_CONFIRM' => '(string) self::env("REGISTER_CONFIRM", "confirm")',
                'REGISTER_PASSWORD_RESET' => '(string) self::env("REGISTER_PASSWORD_RESET", "passwordreset")',
                'MAILER' => '(string) self::env("MAILER", "mail")',
                'SMTP_HOST' => '(string) self::env("SMTP_HOST", "")',
                'SMTP_PORT' => '(string) self::env("SMTP_PORT", "")',
                'SMTP_USERNAME' => '(string) self::env("SMTP_USERNAME", "")',
                'SMTP_PASSWORD' => '(string) self::env("SMTP_PASSWORD", "")',
                'SMTP_ENCRYPTION' => '(string) self::env("SMTP_ENCRYPTION", "")',
                'SOCIAL_CALLBACK_URI' => '(string) self::env("SOCIAL_CALLBACK_URI", "socialauth")',
                'GOOGLE_ENABLED' => 'self::toBool(self::env("GOOGLE_ENABLED", false))',
                'GOOGLE_ID' => '(string) self::env("GOOGLE_ID", "")',
                'GOOGLE_SECRET' => '(string) self::env("GOOGLE_SECRET", "")',
                'FACEBOOK_ENABLED' => 'self::toBool(self::env("FACEBOOK_ENABLED", false))',
                'FACEBOOK_ID' => '(string) self::env("FACEBOOK_ID", "")',
                'FACEBOOK_SECRET' => '(string) self::env("FACEBOOK_SECRET", "")',
                'TWITTER_ENABLED' => 'self::toBool(self::env("TWITTER_ENABLED", false))',
                'TWITTER_KEY' => '(string) self::env("TWITTER_KEY", "")',
                'TWITTER_SECRET' => '(string) self::env("TWITTER_SECRET", "")',
                'DEFAULT_LANGUAGE' => '(string) self::env("DEFAULT_LANGUAGE", "en")',
                'WICMS_VERSION' => '(string) self::env("WICMS_VERSION", "2.0")'
            );
            foreach ($defs as $name => $expr) {
                $config .= '        self::defineIfMissing("' . $name . '", ' . $expr . ');' . PHP_EOL;
            }
            $config .= '    }' . PHP_EOL . PHP_EOL;
            $config .= '    private static function defineIfMissing(string $name, mixed $value): void { if (!defined($name)) { define($name, $value); } }' . PHP_EOL;
            $config .= '    private static function toBool(mixed $value): bool { if (is_bool($value)) { return $value; } $filtered = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE); return $filtered ?? false; }' . PHP_EOL;
            $config .= '}' . PHP_EOL . PHP_EOL;
            $config .= 'WIConfig::load();' . PHP_EOL;

            @file_put_contents($configDir . '/WIConfig.php', $config);
		}

        private function envQuote($value)
        {
            $value = str_replace(array("\\", "\n", "\r", '"'), array("\\\\", '', '', '\\"'), (string)$value);
            return '"' . $value . '"';
        }

        private function installerRandomHex($bytes)
        {
            if (function_exists('random_bytes')) {
                return bin2hex(random_bytes((int)$bytes));
            }
            return md5(uniqid('', true) . mt_rand());
        }



    public function hashingPassword($password, $encryption = 'bcrypt', $cost = 12, $salt = '', $sha512Iterations = 35000)
     {
        $password = (string)$password;
        $encryption = strtolower((string)$encryption);
        $cost = max(10, (int)$cost);

        if ($encryption === 'sha512') {
            $newPassword = $password;
            $iterations = max(1, (int)$sha512Iterations);
            for ($i = 0; $i < $iterations; $i++) {
                $newPassword = hash('sha512', (string)$salt . $newPassword . (string)$salt);
            }
            return $newPassword;
        }

        return password_hash($password, PASSWORD_BCRYPT, array('cost' => $cost));
     }

     private function _generateKey($maxLength = 40)
    {
        $maxLength = (int)$maxLength;
        if ($maxLength < 16) {
            $maxLength = 16;
        }

        if (function_exists('random_bytes')) {
            // 20 random bytes = 40 hex chars, matching the legacy confirmation_key column.
            $key = bin2hex(random_bytes((int)ceil($maxLength / 2)));
        } else {
            $key = sha1(uniqid('', true) . mt_rand());
        }

        return substr($key, 0, $maxLength);
    }



}