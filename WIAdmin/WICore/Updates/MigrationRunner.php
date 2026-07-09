<?php
declare(strict_types=1);

/**
 * Migration Runner
 * WICMS Core
 */

final class MigrationRunner
{
    private WIdb $WIdb;
    private string $migrationPath;

    public function __construct(?string $migrationPath = null)
    {
        $this->WIdb = WIdb::getInstance();
        $this->migrationPath = $migrationPath ?? __DIR__ . '/migrations';
    }

    public function ensureMigrationTableExists(): void
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `wi_migrations` (
                `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
                `version` VARCHAR(190) NOT NULL,
                `name` VARCHAR(255) NOT NULL,
                `executed_at` DATETIME NOT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uniq_version` (`version`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ";

        $this->WIdb->exec($sql);
    }

    public function getExecutedVersions(): array
    {
        $this->ensureMigrationTableExists();

        $rows = $this->WIdb->select('SELECT `version` FROM `wi_migrations`');

        return array_map(
            static fn(array $row): string => (string) $row['version'],
            $rows
        );
    }

    public function discoverMigrations(): array
{
    if (!is_dir($this->migrationPath)) {
        return [];
    }

    $before = get_declared_classes();
    $files = glob($this->migrationPath . '/*.php');

    if ($files === false) {
        return [];
    }

    sort($files);

    $migrations = [];

    foreach ($files as $file) {
        require_once $file;

        $after = get_declared_classes();
        $newClasses = array_diff($after, $before);
        $before = $after;

        foreach ($newClasses as $className) {
            if (!is_subclass_of($className, Migration::class)) {
                continue;
            }

            $instance = new $className();
            $migrations[] = $instance;
        }
    }

    usort($migrations, static function (Migration $a, Migration $b): int {
        return strcmp($a->version(), $b->version());
    });

    return $migrations;
}
    public function runPending(): array
    {
        $executed = $this->getExecutedVersions();
        $migrations = $this->discoverMigrations();
        $ran = [];

        foreach ($migrations as $migration) {
            if (in_array($migration->version(), $executed, true)) {
                continue;
            }

            $this->runSingle($migration);
            $ran[] = [
                'version' => $migration->version(),
                'name' => $migration->name(),
            ];
        }

        return $ran;
    }

    private function runSingle(Migration $migration): void
    {
        try {
            $this->WIdb->beginTransaction();

            $migration->up();

            $this->WIdb->insert('wi_migrations', [
                'version' => $migration->version(),
                'name' => $migration->name(),
                'executed_at' => date('Y-m-d H:i:s'),
            ]);

            $this->WIdb->commit();

            WILogger::info('Migration executed', [
                'version' => $migration->version(),
                'name' => $migration->name(),
            ], 'updates');

        } catch (Throwable $e) {
            if ($this->WIdb->inTransaction()) {
                $this->WIdb->rollBack();
            }

            WILogger::error('Migration failed', [
                'version' => $migration->version(),
                'name' => $migration->name(),
                'error' => $e->getMessage(),
            ], 'updates');

            throw $e;
        }
    }
}