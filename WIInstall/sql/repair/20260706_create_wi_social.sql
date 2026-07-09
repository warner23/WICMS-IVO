-- WICMS Installer v2.7 core table repair
-- Use this on a local/test ECMA database that installed successfully but is missing wi_social.
-- Example target database: ecma

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
