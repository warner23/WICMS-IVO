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
| File: 20260508_001_wimedia_site_wide_hooks.php
| Location: /root/WIAdmin/WICore/Updates/migrations/20260508_001_wimedia_site_wide_hooks.php
| Type: PHP Migration
| Layer: Database Update
| Purpose Area: WIMedia Site-Wide Links / Indexes / Archive Support
| Version: 1.0.0
| Created: 2026-05-08
| Last Updated: 2026-05-08
| Status: WM-01 Production Batch
|--------------------------------------------------------------------------
| Summary:
| Aligns WIMedia database support for site-wide use. Adds archive support and
| safe indexes used by documents, evidence, profile media, checklist proof,
| training, incidents and equipment links.
|--------------------------------------------------------------------------
*/

final class Migration_20260508_001_wimedia_site_wide_hooks extends Migration
{
    public function version(): string
    {
        return '20260508_001_wimedia_site_wide_hooks';
    }

    public function name(): string
    {
        return 'WIMedia site-wide hooks and index alignment';
    }

    public function up(): void
    {
        $this->WIdb->exec("ALTER TABLE `wi_media` MODIFY `media_type` ENUM('image','video','audio','document','archive','other') NOT NULL DEFAULT 'other'");

        $this->addIndexIfMissing('wi_media', 'idx_wi_media_type_status', '`media_type`, `status`');
        $this->addIndexIfMissing('wi_media', 'idx_wi_media_org_scope', '`org_business_id`, `org_site_id`, `org_department_id`');
        $this->addIndexIfMissing('wi_media_links', 'idx_wi_media_links_context', '`system_code`, `entity_type`, `entity_id`, `link_type`');
        $this->addIndexIfMissing('wi_media_links', 'idx_wi_media_links_media', '`media_id`');
        $this->addIndexIfMissing('wi_media_events', 'idx_wi_media_events_media_type', '`media_id`, `event_type`');
    }

    private function addIndexIfMissing(string $table, string $indexName, string $columns): void
    {
        $existing = $this->WIdb->select(
            'SELECT COUNT(1) AS total FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = :table AND index_name = :index_name',
            [
                'table' => $table,
                'index_name' => $indexName,
            ]
        );

        $total = (int)($existing[0]['total'] ?? 0);

        if ($total === 0) {
            $this->WIdb->exec('ALTER TABLE `' . $table . '` ADD INDEX `' . $indexName . '` (' . $columns . ')');
        }
    }
}
