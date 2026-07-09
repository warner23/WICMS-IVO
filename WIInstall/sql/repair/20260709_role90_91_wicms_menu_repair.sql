-- WICMS clean-base role/menu/UI repair
-- Purpose: keep both admin roles valid while making the installer-created first user the platform owner.
-- Safe to run on the current test DB if you are not wiping/reinstalling yet.

START TRANSACTION;

INSERT IGNORE INTO `wi_user_roles` (`role_id`, `role`) VALUES
(90, 'Super Admin'),
(91, 'Platform Owner');

UPDATE `wi_members`
   SET `user_role` = 91,
       `confirmed` = 'Y',
       `banned` = 'N'
 WHERE `user_id` = 1;

INSERT IGNORE INTO `wi_permissions` (`name`, `code`, `group_name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
('Access Admin Area', 'admin.access', 'Admin', 'Can access the WICMS admin area', 1, NOW(), NOW()),
('View Admin Dashboard', 'dashboard.view', 'Dashboard', 'Can view the WICMS admin dashboard', 1, NOW(), NOW()),
('View Settings', 'settings.view', 'Settings', 'Can view WICMS settings', 1, NOW(), NOW()),
('Edit Settings', 'settings.edit', 'Settings', 'Can edit WICMS settings', 1, NOW(), NOW());

INSERT IGNORE INTO `wi_role_permissions` (`role_id`, `permission_id`)
SELECT r.role_id, p.id
  FROM `wi_user_roles` r
  JOIN `wi_permissions` p ON p.code NOT LIKE 'compliance.%' AND p.code NOT LIKE 'wikitchencompli.%'
 WHERE r.role_id IN (90, 91);

DELETE FROM `wi_menu`
 WHERE LOWER(`label`) IN ('platform', 'features', 'industry pack', 'marketplace')
    OR LOWER(`link`) LIKE '%wikitchen%'
    OR LOWER(`link`) LIKE '%wicompliance%'
    OR LOWER(`link`) LIKE '%wilabs%';

INSERT INTO `wi_menu` (`id`, `label`, `link`, `parent`, `sort`, `lang`) VALUES
(1,'Home','index.php',0,0,'home'),
(2,'About','about_us.php',0,1,'about'),
(3,'Contact','contact_us.php',0,2,'contact')
ON DUPLICATE KEY UPDATE
  `label` = VALUES(`label`),
  `link` = VALUES(`link`),
  `parent` = VALUES(`parent`),
  `sort` = VALUES(`sort`),
  `lang` = VALUES(`lang`);

UPDATE `wi_theme` SET `in_use` = 0;
INSERT INTO `wi_theme` (`id`, `theme`, `destination`, `in_use`)
VALUES (1,'WICMS','WITheme/WICMS/',1)
ON DUPLICATE KEY UPDATE `theme` = VALUES(`theme`), `destination` = VALUES(`destination`), `in_use` = 1;

INSERT INTO `wi_trans` (`lang`, `keyword`, `translation`)
VALUES ('en','site_name','WICMS')
ON DUPLICATE KEY UPDATE `translation` = 'WICMS';

UPDATE `wi_footer` SET `website_name` = 'WICMS' WHERE `footer_id` = 1;
UPDATE `wi_header` SET `header_content` = '', `header_slogan` = '' WHERE `header_id` = 1;

COMMIT;
