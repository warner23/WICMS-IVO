
-- -----------------------------------------------------------------------------
-- WICMS Installer v2.9 runtime baseline seed
-- Purpose: make a fresh WICMS install usable immediately after database/config
-- creation by seeding core pages, menus, metadata, CSS/JS registrations,
-- module power states, footer/header/social defaults, and plugin shell rows.
-- -----------------------------------------------------------------------------

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
   SET `website_name` = COALESCE(NULLIF(`website_name`, ''), 'WICMS')
 WHERE `footer_id` = 1;

INSERT INTO `wi_footer` (`footer_id`, `footer_content`, `footer_linking`, `website_name`)
SELECT 1, '', '', 'WICMS'
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
