<?php
declare(strict_types=1);

/**
 * Update Manager
 * WICMS Core
 */

final class UpdateManager
{
    private WIdb $WIdb;
    private MigrationRunner $runner;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->runner = new MigrationRunner();
    }

    public function ensureVersionTableExists(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `wi_system_versions` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `component` VARCHAR(100) NOT NULL,
                `version` VARCHAR(50) NOT NULL,
                `updated_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_component` (`component`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";

        $this->WIdb->exec($sql);
    }

    public function getVersion(string $component = 'core'): ?string
    {
        $this->ensureVersionTableExists();

        return $this->WIdb->selectColumn(
            'SELECT * FROM `wi_system_versions` WHERE `component` = :component LIMIT 1',
            ['component' => $component],
            'version'
        );
    }

    public function setVersion(string $version, string $component = 'core'): void
    {
        $this->ensureVersionTableExists();

        $exists = $this->WIdb->select(
            'SELECT `id` FROM `wi_system_versions` WHERE `component` = :component LIMIT 1',
            ['component' => $component]
        );

        if (count($exists) > 0) {
            $this->WIdb->update(
                'wi_system_versions',
                [
                    'version' => $version,
                    'updated_at' => date('Y-m-d H:i:s'),
                ],
                '`component` = :component',
                ['component' => $component]
            );
            return;
        }

        $this->WIdb->insert('wi_system_versions', [
            'component' => $component,
            'version' => $version,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function runCoreUpdates(string $targetVersion): array
    {
        $ran = $this->runner->runPending();
        $this->setVersion($targetVersion, 'core');

        return [
            'updated_to' => $targetVersion,
            'migrations_run' => $ran,
        ];
    }
}