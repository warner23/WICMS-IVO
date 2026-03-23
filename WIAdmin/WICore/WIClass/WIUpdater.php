<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/Updates/Migration.php';
require_once dirname(__DIR__) . '/Updates/MigrationRunner.php';
require_once dirname(__DIR__) . '/Updates/UpdateManager.php';

/**
 * WICMS Updater Service
 */

final class WIUpdater
{
    private UpdateManager $manager;

    public function __construct()
    {
        $this->manager = new UpdateManager();
    }

    public function currentVersion(): ?string
    {
        return $this->manager->getVersion('core');
    }

    public function updateTo(string $version): array
    {
        return $this->manager->runCoreUpdates($version);
    }
}