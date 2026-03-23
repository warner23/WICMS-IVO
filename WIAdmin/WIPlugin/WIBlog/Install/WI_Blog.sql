-- =========================================================
-- WIBlog SQL
-- Location: WIPlugin/WIBlog/Install/WI_Blog.sql
-- =========================================================

CREATE TABLE IF NOT EXISTS `wi_blog_posts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `author_id` INT NOT NULL DEFAULT 0,
  `type` ENUM('blog_slider','blog_video','blog_image','blog_audio','NoMedia','blog_youtube') NOT NULL DEFAULT 'NoMedia',
  `status` ENUM('draft','published','scheduled','private') NOT NULL DEFAULT 'draft',
  `day` VARCHAR(50) NOT NULL DEFAULT '',
  `month` VARCHAR(50) NOT NULL DEFAULT '',
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `href` VARCHAR(255) NOT NULL DEFAULT '',
  `excerpt` TEXT,
  `content` LONGTEXT,
  `image` VARCHAR(255) NOT NULL DEFAULT '',
  `image2` VARCHAR(255) NOT NULL DEFAULT '',
  `image3` VARCHAR(255) NOT NULL DEFAULT '',
  `video` VARCHAR(255) NOT NULL DEFAULT '',
  `audio` VARCHAR(255) NOT NULL DEFAULT '',
  `youtube` VARCHAR(255) NOT NULL DEFAULT '',
  `user` VARCHAR(255) NOT NULL DEFAULT '',
  `tags` VARCHAR(255) NOT NULL DEFAULT '',
  `button_name` VARCHAR(255) NOT NULL DEFAULT '',
  `meta_title` VARCHAR(255) DEFAULT NULL,
  `meta_description` TEXT DEFAULT NULL,
  `meta_keywords` TEXT DEFAULT NULL,
  `featured` TINYINT(1) NOT NULL DEFAULT 0,
  `allow_comments` TINYINT(1) NOT NULL DEFAULT 1,
  `views` INT NOT NULL DEFAULT 0,
  `likes` INT NOT NULL DEFAULT 0,
  `revision` INT NOT NULL DEFAULT 1,
  `scheduled_date` DATETIME DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wi_blog_categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(150) NOT NULL,
  `slug` VARCHAR(150) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `color` VARCHAR(20) DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wi_blog_post_categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `post_id` INT NOT NULL,
  `category_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wi_blog_comments` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `post_id` INT NOT NULL,
  `user_id` INT NOT NULL DEFAULT 0,
  `author_name` VARCHAR(150) NOT NULL DEFAULT '',
  `author_email` VARCHAR(190) NOT NULL DEFAULT '',
  `content` TEXT NOT NULL,
  `status` ENUM('pending','approved','spam','deleted') NOT NULL DEFAULT 'pending',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `wi_blog_settings` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(150) NOT NULL,
  `setting_value` LONGTEXT,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;