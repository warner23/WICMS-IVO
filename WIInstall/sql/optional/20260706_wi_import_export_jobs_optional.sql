/*
|--------------------------------------------------------------------------
| WICMS Optional Shared Import / Export Jobs Table
|--------------------------------------------------------------------------
| Optional shared audit table for import/export features.
| This is NOT required for a plain WICMS install unless an enabled module
| uses import/export job history.
|--------------------------------------------------------------------------
*/
CREATE TABLE IF NOT EXISTS `wi_import_export_jobs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `job_type` VARCHAR(80) COLLATE utf8mb4_unicode_ci NOT NULL,
  `module_code` VARCHAR(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'shared',
  `business_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `site_id` BIGINT UNSIGNED NOT NULL DEFAULT 0,
  `org_business_id` BIGINT UNSIGNED DEFAULT NULL,
  `org_site_id` BIGINT UNSIGNED DEFAULT NULL,
  `pack_code` VARCHAR(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `profile_code` VARCHAR(120) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` VARCHAR(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'created',
  `rows_total` INT UNSIGNED NOT NULL DEFAULT 0,
  `rows_created` INT UNSIGNED NOT NULL DEFAULT 0,
  `rows_updated` INT UNSIGNED NOT NULL DEFAULT 0,
  `rows_skipped` INT UNSIGNED NOT NULL DEFAULT 0,
  `media_id` BIGINT UNSIGNED DEFAULT NULL,
  `file_name` VARCHAR(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `error_message` TEXT COLLATE utf8mb4_unicode_ci NULL,
  `meta_json` JSON NULL,
  `metadata_json` JSON NULL,
  `created_by_user_id` BIGINT UNSIGNED DEFAULT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `completed_at` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_wi_import_export_jobs_module` (`module_code`, `job_type`),
  KEY `idx_wi_import_export_jobs_scope` (`business_id`, `site_id`, `pack_code`, `profile_code`),
  KEY `idx_wi_import_export_jobs_org_scope` (`org_business_id`, `org_site_id`, `pack_code`, `profile_code`),
  KEY `idx_wi_import_export_jobs_status` (`status`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
