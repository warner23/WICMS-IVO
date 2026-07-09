-- WICMS clean-base identity/admin repair
-- Safe to run after a fresh ECMA/WICMS test install if the public shell still shows old WILabs/Compliance-style seed data
-- or if the first admin was created as a member role rather than the platform-owner role.

START TRANSACTION;

INSERT IGNORE INTO `wi_user_roles` (`role_id`, `role`) VALUES
(90, 'Super Admin'),
(91, 'Platform Owner');

-- The first installer-created account should be the platform owner.
-- Super Admin (90) remains a valid protected role for later admin accounts.
UPDATE `wi_members`
   SET `user_role` = CASE WHEN `user_id` = 1 OR CAST(`user_role` AS SIGNED) < 90 THEN 91 ELSE `user_role` END,
       `confirmed` = 'Y',
       `banned` = 'N'
 WHERE `user_id` = 1 OR CAST(`user_role` AS SIGNED) < 90;

DELETE FROM `wi_menu`
 WHERE LOWER(`label`) IN ('platform', 'features', 'industry pack', 'marketplace')
    OR LOWER(`link`) LIKE '%wikitchen%'
    OR LOWER(`link`) LIKE '%wicompliance%'
    OR LOWER(`link`) LIKE '%wilabs%';

INSERT INTO `wi_menu` (`id`, `label`, `link`, `parent`, `sort`, `lang`) VALUES
(1, 'Home', 'index.php', 0, 0, 'home'),
(2, 'About', 'about_us.php', 0, 1, 'about'),
(3, 'Contact', 'contact_us.php', 0, 2, 'contact')
ON DUPLICATE KEY UPDATE
`label` = VALUES(`label`),
`link` = VALUES(`link`),
`parent` = VALUES(`parent`),
`sort` = VALUES(`sort`),
`lang` = VALUES(`lang`);

UPDATE `wi_footer`
   SET `website_name` = 'WICMS'
 WHERE `footer_id` = 1
   AND (`website_name` IS NULL OR `website_name` = '' OR LOWER(`website_name`) LIKE '%wilabs%' OR LOWER(`website_name`) LIKE '%compli%');

UPDATE `wi_header`
   SET `header_content` = '',
       `header_slogan` = ''
 WHERE `header_id` = 1;

INSERT INTO `wi_trans` (`lang`, `keyword`, `translation`)
SELECT 'en', 'site_name', 'WICMS'
WHERE NOT EXISTS (SELECT 1 FROM `wi_trans` WHERE `lang` = 'en' AND `keyword` = 'site_name');

UPDATE `wi_trans`
   SET `translation` = 'WICMS'
 WHERE `lang` = 'en'
   AND `keyword` = 'site_name'
   AND (LOWER(`translation`) LIKE '%wilabs%' OR LOWER(`translation`) LIKE '%compli%' OR `translation` = '');

INSERT IGNORE INTO `wi_permissions` (`name`, `code`, `group_name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('Access Admin Area', 'admin.access', 'Admin', 'Can access the WICMS admin area', 1, NOW(), NOW()),
('View Admin Dashboard', 'dashboard.view', 'Dashboard', 'Can view the WICMS admin dashboard', 1, NOW(), NOW()),
('View Settings', 'settings.view', 'Settings', 'Can view WICMS settings', 1, NOW(), NOW()),
('Edit Settings', 'settings.edit', 'Settings', 'Can edit WICMS settings', 1, NOW(), NOW());

INSERT IGNORE INTO `wi_role_permissions` (`role_id`, `permission_id`)
SELECT r.role_id, p.id
  FROM `wi_user_roles` r
  JOIN `wi_permissions` p ON p.code IN ('admin.access', 'dashboard.view', 'settings.view', 'settings.edit')
 WHERE r.role_id IN (90, 91);

COMMIT;
