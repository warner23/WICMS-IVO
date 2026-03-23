<?php
declare(strict_types=1);

final class Migration_20260313_001_initialize_wicms_update_system extends Migration
{
    public function version(): string
    {
        return '20260313_001_initialize_wicms_update_system';
    }

    public function name(): string
    {
        return 'Initialize WICMS update system';
    }

    public function up(): void
    {
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS `wi_migrations` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `version` VARCHAR(190) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `executed_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_version` (`version`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        $this->db->exec("
            CREATE TABLE IF NOT EXISTS `wi_system_versions` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `component` VARCHAR(100) NOT NULL,
                `version` VARCHAR(50) NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_component` (`component`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
}