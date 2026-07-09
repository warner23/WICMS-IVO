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
| File: WIMediaAttachmentContract.php
| Location: /root/WIAdmin/WICore/WIClass/WIMediaAttachmentContract.php
| Type: PHP Class
| Layer: Shared Admin Backend Contract
| Purpose Area: WIMedia Entity Attachment Bridge
| Version: 1.0.0
| Created: 2026-05-11
| Last Updated: 2026-05-11
| Status: WM-02 Production Batch
|--------------------------------------------------------------------------
| Summary:
| Shared backend media attachment contract.
| - Uses WIdb only
| - Does not upload files
| - Links existing media rows to target entities
| - Lists linked media for a target entity
| - Soft-detaches links where possible
| - Designed for WIMediaHooks, Documents, Equipment, Checklist Evidence,
|   Training, Incidents and Audits
|--------------------------------------------------------------------------
*/

final class WIMediaAttachmentContract
{
    private WIdb $WIdb;

    private string $linksTable = 'wi_media_links';
    private string $mediaTable = 'wi_media';
    private string $auditTable = 'wi_media_audit';

    public function __construct(?WIdb $WIdb = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
    }

    public function attach(array $payload): array
    {
        $context = $this->normaliseContext($payload);
        $mediaId = (int) ($payload['media_id'] ?? 0);

        if ($mediaId <= 0) {
            return $this->error('A valid media_id is required.');
        }

        if (!$this->isAttachableContext($context)) {
            return $this->error('A valid media attachment context is required.');
        }

        if (!$this->mediaExists($mediaId)) {
            return $this->error('The selected media item could not be found.');
        }

        $existing = $this->findExistingLinkId($mediaId, $context);

        if ($existing > 0) {
            return [
                'success' => true,
                'status'  => 'success',
                'message' => 'Media is already attached.',
                'data'    => [
                    'link_id'  => $existing,
                    'media_id' => $mediaId,
                    'context'  => $context,
                ],
                'errors'  => [],
            ];
        }

        $linkId = $this->insertLink($mediaId, $context);

        if ($linkId <= 0) {
            return $this->error('Media could not be attached.');
        }

        $this->audit($mediaId, 'linked', $context);

        return [
            'success' => true,
            'status'  => 'success',
            'message' => 'Media attached.',
            'data'    => [
                'link_id'  => $linkId,
                'media_id' => $mediaId,
                'context'  => $context,
            ],
            'errors'  => [],
        ];
    }

    public function detach(array $payload): array
    {
        $context = $this->normaliseContext($payload);
        $mediaId = (int) ($payload['media_id'] ?? 0);

        if ($mediaId <= 0) {
            return $this->error('A valid media_id is required.');
        }

        if (!$this->isAttachableContext($context)) {
            return $this->error('A valid media attachment context is required.');
        }

        $linkId = $this->findExistingLinkId($mediaId, $context);

        if ($linkId <= 0) {
            return $this->error('No matching media link was found.');
        }

        $detached = $this->softDetach($linkId);

        if (!$detached) {
            return $this->error('Media could not be detached.');
        }

        $this->audit($mediaId, 'unlinked', $context);

        return [
            'success' => true,
            'status'  => 'success',
            'message' => 'Media detached.',
            'data'    => [
                'link_id'  => $linkId,
                'media_id' => $mediaId,
                'context'  => $context,
            ],
            'errors'  => [],
        ];
    }

    public function linked(array $payload): array
    {
        $context = $this->normaliseContext($payload);

        if (!$this->isAttachableContext($context)) {
            return [
                'success' => true,
                'status'  => 'success',
                'message' => 'No media context selected.',
                'data'    => [
                    'items'   => [],
                    'context' => $context,
                ],
                'errors'  => [],
            ];
        }

        $items = $this->fetchLinkedMedia($context);

        return [
            'success' => true,
            'status'  => 'success',
            'message' => 'Linked media loaded.',
            'data'    => [
                'items'   => $items,
                'count'   => count($items),
                'context' => $context,
            ],
            'errors'  => [],
        ];
    }

    public function normaliseContext(array $payload): array
    {
        return [
            'system_code'       => $this->cleanKey((string) ($payload['system_code'] ?? 'wicms')),
            'entity_type'       => $this->cleanKey((string) ($payload['entity_type'] ?? $payload['target_type'] ?? '')),
            'entity_id'         => (int) ($payload['entity_id'] ?? $payload['target_id'] ?? 0),
            'link_type'         => $this->cleanKey((string) ($payload['link_type'] ?? $payload['context'] ?? 'evidence')),
            'org_business_id'   => (int) ($payload['org_business_id'] ?? $payload['business_id'] ?? 0),
            'org_site_id'       => (int) ($payload['org_site_id'] ?? $payload['site_id'] ?? 0),
            'org_department_id' => (int) ($payload['org_department_id'] ?? $payload['department_id'] ?? 0),
            'created_by_user_id'=> $this->currentUserId(),
        ];
    }

    private function isAttachableContext(array $context): bool
    {
        return $context['system_code'] !== ''
            && $context['entity_type'] !== ''
            && (int) $context['entity_id'] > 0
            && $context['link_type'] !== '';
    }

    private function cleanKey(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_\-:.]/', '_', $value) ?: '';

        return trim($value, '_');
    }

    private function mediaExists(int $mediaId): bool
    {
        $sql = "SELECT id FROM {$this->mediaTable} WHERE id = :id LIMIT 1";
        $row = $this->fetchOne($sql, [':id' => $mediaId]);

        return !empty($row);
    }

    private function findExistingLinkId(int $mediaId, array $context): int
    {
        $sql = "
            SELECT id
            FROM {$this->linksTable}
            WHERE media_id = :media_id
              AND system_code = :system_code
              AND entity_type = :entity_type
              AND entity_id = :entity_id
              AND link_type = :link_type
              AND (deleted_at IS NULL OR deleted_at = '0000-00-00 00:00:00')
            LIMIT 1
        ";

        $row = $this->fetchOne($sql, [
            ':media_id'    => $mediaId,
            ':system_code' => $context['system_code'],
            ':entity_type' => $context['entity_type'],
            ':entity_id'   => $context['entity_id'],
            ':link_type'   => $context['link_type'],
        ]);

        return (int) ($row['id'] ?? 0);
    }

    private function insertLink(int $mediaId, array $context): int
    {
        $data = [
            'media_id'           => $mediaId,
            'system_code'        => $context['system_code'],
            'entity_type'        => $context['entity_type'],
            'entity_id'          => $context['entity_id'],
            'link_type'          => $context['link_type'],
            'org_business_id'    => $context['org_business_id'] ?: null,
            'org_site_id'        => $context['org_site_id'] ?: null,
            'org_department_id'  => $context['org_department_id'] ?: null,
            'created_by_user_id' => $context['created_by_user_id'] ?: null,
            'created_at'         => date('Y-m-d H:i:s'),
        ];

        if (method_exists($this->WIdb, 'insert')) {
            $result = $this->WIdb->insert($this->linksTable, $data);

            if (is_numeric($result)) {
                return (int) $result;
            }

            if (method_exists($this->WIdb, 'lastInsertId')) {
                return (int) $this->WIdb->lastInsertId();
            }
        }

        return 0;
    }

    private function softDetach(int $linkId): bool
    {
        $data = [
            'deleted_at' => date('Y-m-d H:i:s'),
        ];

        if (method_exists($this->WIdb, 'update')) {
            $result = $this->WIdb->update($this->linksTable, $data, 'id = :id', [':id' => $linkId]);

            return $result !== false;
        }

        return false;
    }

    private function fetchLinkedMedia(array $context): array
    {
        $sql = "
            SELECT
                l.id AS link_id,
                l.media_id,
                l.system_code,
                l.entity_type,
                l.entity_id,
                l.link_type,
                l.org_business_id,
                l.org_site_id,
                l.org_department_id,
                l.created_at AS linked_at,
                m.id,
                m.original_name,
                m.stored_name,
                m.media_type,
                m.mime_type,
                m.file_size,
                m.visibility,
                m.access_scope,
                m.created_at
            FROM {$this->linksTable} l
            INNER JOIN {$this->mediaTable} m ON m.id = l.media_id
            WHERE l.system_code = :system_code
              AND l.entity_type = :entity_type
              AND l.entity_id = :entity_id
              AND l.link_type = :link_type
              AND (l.deleted_at IS NULL OR l.deleted_at = '0000-00-00 00:00:00')
            ORDER BY l.id DESC
        ";

        return $this->fetchAll($sql, [
            ':system_code' => $context['system_code'],
            ':entity_type' => $context['entity_type'],
            ':entity_id'   => $context['entity_id'],
            ':link_type'   => $context['link_type'],
        ]);
    }

    private function audit(int $mediaId, string $event, array $context): void
    {
        if (!method_exists($this->WIdb, 'insert')) {
            return;
        }

        if (!$this->tableExists($this->auditTable)) {
            return;
        }

        $this->WIdb->insert($this->auditTable, [
            'media_id'           => $mediaId,
            'event_type'         => $event,
            'system_code'        => $context['system_code'],
            'entity_type'        => $context['entity_type'],
            'entity_id'          => $context['entity_id'],
            'link_type'          => $context['link_type'],
            'org_business_id'    => $context['org_business_id'] ?: null,
            'org_site_id'        => $context['org_site_id'] ?: null,
            'org_department_id'  => $context['org_department_id'] ?: null,
            'created_by_user_id' => $context['created_by_user_id'] ?: null,
            'context_json'       => json_encode($context, JSON_UNESCAPED_SLASHES),
            'created_at'         => date('Y-m-d H:i:s'),
        ]);
    }

    private function tableExists(string $table): bool
    {
        $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table) ?: '';

        if ($table === '') {
            return false;
        }

        $sql = "SHOW TABLES LIKE :table_name";
        $row = $this->fetchOne($sql, [
            ':table_name' => $table,
        ]);

        return !empty($row);
    }

    private function fetchOne(string $sql, array $params = []): array
    {
        if (method_exists($this->WIdb, 'fetchOne')) {
            $row = $this->WIdb->fetchOne($sql, $params);

            return is_array($row) ? $row : [];
        }

        if (method_exists($this->WIdb, 'query')) {
            $result = $this->WIdb->query($sql, $params);

            if (is_array($result)) {
                return isset($result[0]) && is_array($result[0]) ? $result[0] : $result;
            }
        }

        return [];
    }

    private function fetchAll(string $sql, array $params = []): array
    {
        if (method_exists($this->WIdb, 'fetchAll')) {
            $rows = $this->WIdb->fetchAll($sql, $params);

            return is_array($rows) ? $rows : [];
        }

        if (method_exists($this->WIdb, 'query')) {
            $rows = $this->WIdb->query($sql, $params);

            return is_array($rows) ? $rows : [];
        }

        return [];
    }

    private function currentUserId(): int
    {
        if (isset($_SESSION['user_id'])) {
            return (int) $_SESSION['user_id'];
        }

        if (isset($_SESSION['member_id'])) {
            return (int) $_SESSION['member_id'];
        }

        return 0;
    }

    private function error(string $message): array
    {
        return [
            'success' => false,
            'status'  => 'error',
            'message' => $message,
            'data'    => [],
            'errors'  => [$message],
        ];
    }
}