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
| File: WIMediaRepository.php
| Location: /root/WIAdmin/WICore/WIClass/Media/WIMediaRepository.php
| Type: PHP Repository Class
| Layer: Shared Media Data Access
| Purpose Area: wi_media Repository
| Version: 1.2.0
| Created: 2026-05-05
| Last Updated: 2026-05-05
| Status: Production Refactor Batch 7
|--------------------------------------------------------------------------
| Summary:
| Repository for wi_media database reads and writes.
| - Uses WIdb only
| - Guards table/column availability before queries
| - Supports create, find, list, update, delete and restore
| - Contains no compliance dependency
|--------------------------------------------------------------------------
*/

final class WIMediaRepository
{
    private WIdb $WIdb;

    private WIMediaTableGuard $Guard;

    private string $table = 'wi_media';

    /**
     * Creates the repository.
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
     * Creates a media record.
     *
     * @param array<string, mixed> $data Insert payload.
     *
     * @return int
     */
    public function create(array $data): int
    {
        if (!$this->Guard->tableExists($this->table)) {
            return 0;
        }

        $data = $this->Guard->filter($this->table, $data);

        if ($data === []) {
            return 0;
        }

        $ok = $this->WIdb->insert($this->table, $data);

        if (!$ok) {
            return 0;
        }

        return $this->lastInsertId($data);
    }

    /**
     * Finds one media record by ID.
     *
     * @param int $id Media ID.
     *
     * @return array<string, mixed>|false
     */
    public function find(int $id): array|false
    {
        if ($id <= 0 || !$this->Guard->tableExists($this->table)) {
            return false;
        }

        $rows = $this->WIdb->select(
            "SELECT *
             FROM `wi_media`
             WHERE `id` = :id
             LIMIT 1",
            [
                'id' => $id,
            ]
        );

        return $rows[0] ?? false;
    }

    /**
     * Lists media records using safe filters.
     *
     * @param array<string, mixed> $filters List filters.
     *
     * @return array<int, array<string, mixed>>
     */
    public function list(array $filters = []): array
    {
        if (!$this->Guard->tableExists($this->table)) {
            return [];
        }

        $sql = "SELECT * FROM `wi_media` WHERE 1=1";
        $params = [];

        foreach (['media_type', 'visibility', 'access_scope', 'status', 'org_business_id', 'org_site_id', 'org_department_id'] as $field) {
            if (!isset($filters[$field]) || $filters[$field] === '' || !$this->Guard->columnExists($this->table, $field)) {
                continue;
            }

            $sql .= " AND `{$field}` = :{$field}";
            $params[$field] = $filters[$field];
        }

        if (!empty($filters['search'])) {
            $search = '%' . trim((string)$filters['search']) . '%';
            $parts = [];

            foreach (['title', 'original_name', 'caption', 'description'] as $column) {
                if (!$this->Guard->columnExists($this->table, $column)) {
                    continue;
                }

                $key = 'search_' . $column;
                $parts[] = "`{$column}` LIKE :{$key}";
                $params[$key] = $search;
            }

            if ($parts !== []) {
                $sql .= ' AND (' . implode(' OR ', $parts) . ')';
            }
        }

        if ($this->Guard->columnExists($this->table, 'deleted_at')) {
            $sql .= " AND `deleted_at` IS NULL";
        }

        $sql .= " ORDER BY `id` DESC";

        $limit = isset($filters['limit']) ? max(1, min(250, (int)$filters['limit'])) : 100;
        $offset = isset($filters['offset']) ? max(0, (int)$filters['offset']) : 0;

        $sql .= " LIMIT {$offset}, {$limit}";

        return $this->WIdb->select($sql, $params);
    }

    public function update(int $id, array $data): bool
    {
        if ($id <= 0 || !$this->Guard->tableExists($this->table)) {
            return false;
        }

        $data = $this->Guard->filter($this->table, $data, ['id']);

        if ($data === []) {
            return false;
        }

        return (bool)$this->WIdb->update(
            $this->table,
            $data,
            '`id` = :id',
            [
                'id' => $id,
            ]
        );
    }

    public function softDelete(int $id, ?int $userId = null): bool
    {
        if ($this->Guard->columnExists($this->table, 'deleted_at')) {
            return $this->update($id, [
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by_user_id' => $userId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return (bool)$this->WIdb->delete(
            $this->table,
            '`id` = :id',
            [
                'id' => $id,
            ]
        );
    }

    public function restore(int $id): bool
    {
        return $this->update($id, [
            'deleted_at' => null,
            'deleted_by_user_id' => null,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }


    /**
     * Resolves the last inserted media ID.
     *
     * @param array<string, mixed> $data Inserted data.
     *
     * @return int
     */
    private function lastInsertId(array $data): int
    {
        if (method_exists($this->WIdb, 'lastInsertId')) {
            return (int)$this->WIdb->lastInsertId();
        }

        if (!empty($data['uuid'])) {
            $rows = $this->WIdb->select(
                "SELECT `id`
                 FROM `wi_media`
                 WHERE `uuid` = :uuid
                 ORDER BY `id` DESC
                 LIMIT 1",
                [
                    'uuid' => $data['uuid'],
                ]
            );

            return (int)($rows[0]['id'] ?? 0);
        }

        if (!empty($data['stored_name'])) {
            $rows = $this->WIdb->select(
                "SELECT `id`
                 FROM `wi_media`
                 WHERE `stored_name` = :stored_name
                 ORDER BY `id` DESC
                 LIMIT 1",
                [
                    'stored_name' => $data['stored_name'],
                ]
            );

            return (int)($rows[0]['id'] ?? 0);
        }

        return 0;
    }

    /**
     * Normalises media rows for admin UI callers.
     *
     * @param array<string, mixed> $row Raw media row.
     *
     * @return array<string, mixed>
     */
    private function normaliseMediaRow(array $row): array
    {
        $row['id'] = (int)($row['id'] ?? $row['media_id'] ?? 0);
        $row['media_id'] = (int)($row['media_id'] ?? $row['id']);
        $row['title'] = (string)($row['title'] ?? $row['original_name'] ?? $row['stored_name'] ?? 'Media #' . $row['id']);
        $row['media_type'] = (string)($row['media_type'] ?? $row['file_type'] ?? 'file');
        $row['file_url'] = (string)($row['file_url'] ?? $row['url'] ?? $row['file_path'] ?? '');

        return $row;
    }
}
