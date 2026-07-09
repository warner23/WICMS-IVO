-- WICMS / ECMA repair: admin login + baseline styling rows
-- Safe to run more than once on the ECMA database.
-- Use this if v2.9 installed successfully but the first admin cannot enter WIAdmin.

INSERT INTO `wi_user_roles` (`role_id`, `role`)
SELECT 90, 'Super Admin'
WHERE NOT EXISTS (SELECT 1 FROM `wi_user_roles` WHERE `role_id` = 90);

INSERT INTO `wi_user_roles` (`role_id`, `role`)
SELECT 91, 'Platform Owner'
WHERE NOT EXISTS (SELECT 1 FROM `wi_user_roles` WHERE `role_id` = 91);

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

-- Runtime public CSS/JS/meta rows. These are generic WICMS baseline rows.
CREATE TABLE IF NOT EXISTS `wi_css` (
  `id` int(4) NOT NULL AUTO_INCREMENT,
  `href` varchar(255) NOT NULL,
  `rel` varchar(255) NOT NULL DEFAULT 'stylesheet',
  `page` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_css_page` (`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `wi_scripts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `src` varchar(255) NOT NULL,
  `page` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_scripts_page` (`page`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/wicms-baseline.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/wicms-baseline.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/frameworks/menus.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/frameworks/menus.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/frameworks/header.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/frameworks/header.css' AND `page` = 'global');

INSERT INTO `wi_css` (`href`, `rel`, `page`)
SELECT 'site/css/frameworks/footer.css', 'stylesheet', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_css` WHERE `href` = 'site/css/frameworks/footer.css' AND `page` = 'global');

INSERT INTO `wi_scripts` (`src`, `page`)
SELECT 'site/js/wicms-baseline.js', 'global'
WHERE NOT EXISTS (SELECT 1 FROM `wi_scripts` WHERE `src` = 'site/js/wicms-baseline.js' AND `page` = 'global');
