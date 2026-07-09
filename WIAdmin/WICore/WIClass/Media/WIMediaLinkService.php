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
| File: WIMediaLinkService.php
| Location: /root/WIAdmin/WICore/WIClass/Media/WIMediaLinkService.php
| Type: PHP Service Class
| Layer: Shared Media Linking
| Purpose Area: wi_media_links Relationships
| Version: 1.2.0
| Created: 2026-05-05
| Last Updated: 2026-05-05
| Status: Production Refactor Batch 7
|--------------------------------------------------------------------------
| Summary:
| Shared service for linking media to system/entity records.
| - Owns wi_media_links relationships
| - Uses WIdb through shared repository-style services only
| - Logs linked/unlinked events through WIMediaEventService
| - Contains no compliance dependency
|--------------------------------------------------------------------------
*/

final class WIMediaLinkService
{
    private WIdb $WIdb;

    private WIMediaTableGuard $Guard;

    private WIMediaEventService $Events;

    private string $table = 'wi_media_links';

    /**
     * Creates the link service.
     *
     * @param WIdb|null $WIdb Optional shared WIdb instance.
     * @param WIMediaTableGuard|null $Guard Optional table guard.
     * @param WIMediaEventService|null $Events Optional event service.
     */
    public function __construct(?WIdb $WIdb = null, ?WIMediaTableGuard $Guard = null, ?WIMediaEventService $Events = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
        $this->Guard = $Guard ?? new WIMediaTableGuard($this->WIdb);
        $this->Events = $Events ?? new WIMediaEventService($this->WIdb, $this->Guard);
    }

    /**
     * Links a media record to a system/entity record.
     *
     * @param int $mediaId Media ID.
     * @param array<string, mixed> $context Link context.
     *
     * @return array<string, mixed>
     */
    public function linkMedia(int $mediaId, array $context): array
    {
        $context['link_type'] = $this->normaliseLinkType($context['link_type'] ?? $context['media_role'] ?? 'primary_file');
        if ($mediaId <= 0) {
            return $this->error('A valid media ID is required.');
        }

        if (!$this->Guard->tableExists($this->table)) {
            return $this->error('Media links table is not available.');
        }

        $entityType = trim((string)($context['entity_type'] ?? ''));
        $entityId = (int)($context['entity_id'] ?? 0);
        $systemCode = trim((string)($context['system_code'] ?? ''));
        $linkType = trim((string)($context['link_type'] ?? ''));

        if ($systemCode === '' || $entityType === '' || $entityId <= 0 || $linkType === '') {
            return $this->error('Media link context is incomplete.');
        }

        $existing = $this->findExistingLink($mediaId, $context);

        if ($existing > 0) {
            return $this->success('Media is already linked.', [
                'link_id' => $existing,
            ]);
        }

        $data = [
            'media_id' => $mediaId,
            'system_code' => $systemCode,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'link_type' => $linkType,
            'org_business_id' => $this->positiveInt($context['org_business_id'] ?? $context['business_id'] ?? null),
            'org_site_id' => $this->positiveInt($context['org_site_id'] ?? $context['site_id'] ?? null),
            'org_department_id' => $this->positiveInt($context['org_department_id'] ?? $context['department_id'] ?? null),
            'created_by_user_id' => $this->positiveInt($context['created_by_user_id'] ?? $context['user_id'] ?? null),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $data = $this->Guard->filter($this->table, $data);

        $ok = $this->WIdb->insert($this->table, $data);

        if (!$ok) {
            return $this->error('Failed to link media.');
        }

        $linkId = $this->lastInsertId($mediaId, $context);

        $this->Events->log('linked', array_merge($context, [
            'media_id' => $mediaId,
            'link_id' => $linkId,
        ]));

        return $this->success('Media linked successfully.', [
            'link_id' => $linkId,
        ]);
    }

    /**
     * Removes a media link from a system/entity record.
     *
     * @param int $mediaId Media ID.
     * @param array<string, mixed> $context Link context.
     *
     * @return array<string, mixed>
     */
    public function unlinkMedia(int $mediaId, array $context): array
    {
        if ($mediaId <= 0 || !$this->Guard->tableExists($this->table)) {
            return $this->error('A valid media ID is required.');
        }

        $where = [
            '`media_id` = :media_id',
            '`system_code` = :system_code',
            '`entity_type` = :entity_type',
            '`entity_id` = :entity_id',
            '`link_type` = :link_type',
        ];

        $params = [
            'media_id' => $mediaId,
            'system_code' => (string)($context['system_code'] ?? ''),
            'entity_type' => (string)($context['entity_type'] ?? ''),
            'entity_id' => (int)($context['entity_id'] ?? 0),
            'link_type' => (string)($context['link_type'] ?? ''),
        ];

        if ($this->Guard->columnExists($this->table, 'deleted_at')) {
            $ok = $this->WIdb->update(
                $this->table,
                $this->Guard->filter($this->table, [
                    'deleted_at' => date('Y-m-d H:i:s'),
                    'deleted_by_user_id' => $this->positiveInt($context['user_id'] ?? null),
                ]),
                implode(' AND ', $where),
                $params
            );
        } else {
            $ok = $this->WIdb->delete($this->table, implode(' AND ', $where), $params);
        }

        if (!$ok) {
            return $this->error('Failed to unlink media.');
        }

        $this->Events->log('unlinked', array_merge($context, [
            'media_id' => $mediaId,
        ]));

        return $this->success('Media unlinked successfully.');
    }

    /**
     * Lists media linked to a system/entity record.
     *
     * @param array<string, mixed> $context Link context.
     *
     * @return array<int, array<string, mixed>>
     */
    public function getLinkedMedia(array $context): array
    {
        if (!$this->Guard->tableExists($this->table) || !$this->Guard->tableExists('wi_media')) {
            return [];
        }

        $where = [
            'l.`system_code` = :system_code',
            'l.`entity_type` = :entity_type',
            'l.`entity_id` = :entity_id',
        ];

        $params = [
            'system_code' => (string)($context['system_code'] ?? ''),
            'entity_type' => (string)($context['entity_type'] ?? ''),
            'entity_id' => (int)($context['entity_id'] ?? 0),
        ];

        if (!empty($context['link_type'])) {
            $where[] = 'l.`link_type` = :link_type';
            $params['link_type'] = (string)$context['link_type'];
        }

        if ($this->Guard->columnExists($this->table, 'deleted_at')) {
            $where[] = 'l.`deleted_at` IS NULL';
        }

        $sql = "
            SELECT
                m.*,
                l.`id` AS `link_id`,
                l.`link_type`,
                l.`system_code`,
                l.`entity_type`,
                l.`entity_id`,
                l.`created_at` AS `linked_at`
            FROM `wi_media_links` l
            INNER JOIN `wi_media` m ON m.`id` = l.`media_id`
            WHERE " . implode(' AND ', $where) . "
            ORDER BY l.`id` DESC
        ";

        $rows = $this->WIdb->select($sql, $params);

        foreach ($rows as &$row) {
            $row['safe_view_url'] = 'WICore/WIAjax/WIMediaView.php?id=' . (int)($row['id'] ?? 0) . '&mode=view';
            $row['safe_download_url'] = 'WICore/WIAjax/WIMediaView.php?id=' . (int)($row['id'] ?? 0) . '&mode=download';
        }

        unset($row);

        return $rows;
    }

    private function findExistingLink(int $mediaId, array $context): int
    {
        $where = [
            '`media_id` = :media_id',
            '`system_code` = :system_code',
            '`entity_type` = :entity_type',
            '`entity_id` = :entity_id',
            '`link_type` = :link_type',
        ];

        $params = [
            'media_id' => $mediaId,
            'system_code' => (string)($context['system_code'] ?? ''),
            'entity_type' => (string)($context['entity_type'] ?? ''),
            'entity_id' => (int)($context['entity_id'] ?? 0),
            'link_type' => (string)($context['link_type'] ?? ''),
        ];

        if ($this->Guard->columnExists($this->table, 'deleted_at')) {
            $where[] = '`deleted_at` IS NULL';
        }

        $rows = $this->WIdb->select(
            "SELECT `id`
             FROM `wi_media_links`
             WHERE " . implode(' AND ', $where) . "
             LIMIT 1",
            $params
        );

        return (int)($rows[0]['id'] ?? 0);
    }

    private function positiveInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (int)$value;

        return $value > 0 ? $value : null;
    }

    private function success(string $message, array $extra = []): array
    {
        return array_merge([
            'success' => true,
            'status' => 'success',
            'message' => $message,
        ], $extra);
    }

    private function error(string $message): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'message' => $message,
        ];
    }


    /**
     * Resolves last inserted link ID.
     *
     * @param int $mediaId Media ID.
     * @param array<string, mixed> $context Context.
     *
     * @return int
     */
    private function lastInsertId(int $mediaId, array $context): int
    {
        if (method_exists($this->WIdb, 'lastInsertId')) {
            return (int)$this->WIdb->lastInsertId();
        }

        return $this->findExistingLink($mediaId, $context);
    }

    /**
     * Normalises a media link type / media role.
     *
     * @param mixed $value Raw link type value.
     *
     * @return string
     */
    private function normaliseLinkType(mixed $value): string
    {
        $value = trim((string)$value);

        return $value !== '' ? $value : 'primary_file';
    }

    /**
     * Normalises link service responses for UI callers.
     *
     * @param array<string, mixed> $response Raw response.
     *
     * @return array<string, mixed>
     */
    private function normaliseResponse(array $response): array
    {
        $response['success'] = (bool)($response['success'] ?? (($response['status'] ?? '') === 'success'));
        $response['status'] = (string)($response['status'] ?? ($response['success'] ? 'success' : 'error'));

        if (isset($response['media']) && is_array($response['media']) && !isset($response['items'])) {
            $response['items'] = $response['media'];
        }

        if (isset($response['items']) && is_array($response['items']) && !isset($response['media'])) {
            $response['media'] = $response['items'];
        }

        return $response;
    }
}
