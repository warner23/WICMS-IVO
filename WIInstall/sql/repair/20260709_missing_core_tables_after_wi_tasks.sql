-- WICMS repair: creates the core tables that appear after wi_tasks in the clean baseline.
-- Use only if an earlier installer run completed but phpMyAdmin shows tables stopping at wi_tasks.
-- Safe on an existing database because CREATE TABLE uses IF NOT EXISTS and seed rows use INSERT IGNORE where present.

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
