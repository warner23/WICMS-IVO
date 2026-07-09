<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WIKitchenCompli / WICOS
| File: WIMediaEventService.php
| Location: /root/WIAdmin/WICore/WIClass/Media/WIMediaEventService.php
| Type: PHP Service Class
| Layer: Shared Media Audit
| Purpose Area: wi_media_events Logging
| Version: 1.1.0
| Created: 2026-05-05
| Last Updated: 2026-05-05
| Status: Batch 1 Refactor
|--------------------------------------------------------------------------
| Summary:
| Shared WIMedia event logging service.
| - Writes media lifecycle events to wi_media_events
| - Filters data through WIMediaTableGuard
| - Uses WIdb only
| - Contains no compliance dependency
|--------------------------------------------------------------------------
*/

final class WIMediaEventService
{
    private WIdb $WIdb;

    private WIMediaTableGuard $Guard;

    private string $table = 'wi_media_events';

    /**
     * Creates the event service.
     *
     * @param WIdb|null $WIdb Optional shared WIdb instance.
     * @param WIMediaTableGuard|null $Guard Optional table guard.
     */
    public function __construct(?WIdb $WIdb = null, ?WIMediaTableGuard $Guard = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
        $this->Guard = $Guard ?? new WIMediaTableGuard($this->WIdb);
    }

    /**
     * Logs a media event when the events table is available.
     *
     * @param string $eventType Event name.
     * @param array<string, mixed> $context Event context.
     *
     * @return void
     */
    public function log(string $eventType, array $context = []): void
    {
        if (!$this->Guard->tableExists($this->table)) {
            return;
        }

        $data = [
            'media_id' => $this->positiveInt($context['media_id'] ?? null),
            'event_type' => $eventType,
            'system_code' => $this->stringOrNull($context['system_code'] ?? null),
            'entity_type' => $this->stringOrNull($context['entity_type'] ?? null),
            'entity_id' => $this->positiveInt($context['entity_id'] ?? null),
            'link_type' => $this->stringOrNull($context['link_type'] ?? null),
            'org_business_id' => $this->positiveInt($context['org_business_id'] ?? $context['business_id'] ?? null),
            'org_site_id' => $this->positiveInt($context['org_site_id'] ?? $context['site_id'] ?? null),
            'org_department_id' => $this->positiveInt($context['org_department_id'] ?? $context['department_id'] ?? null),
            'created_by_user_id' => $this->positiveInt($context['user_id'] ?? $context['created_by_user_id'] ?? null),
            'event_payload' => json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $data = $this->Guard->filter($this->table, $data);

        if ($data === []) {
            return;
        }

        $this->WIdb->insert($this->table, $data);
    }

    private function positiveInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (int)$value;

        return $value > 0 ? $value : null;
    }

    private function stringOrNull(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string)$value);

        return $value !== '' ? $value : null;
    }
}