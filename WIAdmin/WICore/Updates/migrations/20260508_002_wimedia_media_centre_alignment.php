<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WICMS / WICOS / WIKitchenCompli
| File: 20260508_002_wimedia_media_centre_alignment.php
| Location: /root/WIAdmin/WICore/Updates/migrations/20260508_002_wimedia_media_centre_alignment.php
| Type: PHP Migration
| Layer: System Update
| Purpose Area: WIMedia Media Centre Table Alignment
| Version: 1.0.0
| Created: 2026-05-08
| Last Updated: 2026-05-08
| Status: WM-02 Production Refactor
|--------------------------------------------------------------------------
| Summary:
| Optional WIMedia schema alignment migration.
| - Adds archive to wi_media.media_type where supported
| - Adds helpful indexes for list and linked media lookups
| - Uses WIdb only
|--------------------------------------------------------------------------
*/

final class Migration_20260508_002_wimedia_media_centre_alignment
{
    private WIdb $WIdb;

    public function __construct(?WIdb $WIdb = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
    }

    /**
     * Runs the migration.
     *
     * @return bool
     */
    public function up(): bool
    {
        $this->run("ALTER TABLE `wi_media` MODIFY `media_type` ENUM('image','video','audio','document','archive','other') NOT NULL DEFAULT 'other'");
        $this->addIndex('wi_media', 'idx_wi_media_type_status', ['media_type', 'status']);
        $this->addIndex('wi_media', 'idx_wi_media_org_scope', ['org_business_id', 'org_site_id', 'org_department_id']);
        $this->addIndex('wi_media_links', 'idx_wi_media_links_entity', ['system_code', 'entity_type', 'entity_id', 'link_type']);

        return true;
    }

    /**
     * Safely runs a raw migration statement through WIdb when supported.
     *
     * @param string $sql SQL statement.
     *
     * @return void
     */
    private function run(string $sql): void
    {
        try {
            if (method_exists($this->WIdb, 'query')) {
                $this->WIdb->query($sql);
                return;
            }

            if (method_exists($this->WIdb, 'select')) {
                $this->WIdb->select($sql, []);
            }
        } catch (Throwable $e) {
            // Keep migrations idempotent for environments where a column/index already exists.
        }
    }

    /**
     * Adds an index if the environment allows the statement.
     *
     * @param string $table Table name.
     * @param string $index Index name.
     * @param array<int, string> $columns Column names.
     *
     * @return void
     */
    private function addIndex(string $table, string $index, array $columns): void
    {
        $safeColumns = array_map(static fn (string $column): string => '`' . str_replace('`', '', $column) . '`', $columns);
        $sql = 'ALTER TABLE `' . str_replace('`', '', $table) . '` ADD INDEX `' . str_replace('`', '', $index) . '` (' . implode(',', $safeColumns) . ')';

        $this->run($sql);
    }
}
