<?php
/**
 * File Information
 *
 * Written By: Warner Infinity
 * Company: Warner Infinity
 * Product: WIMaintenance
 * Project: WI Ecosystem
 * File: WIMaintenanceRepository.php
 * Location: /WIAdmin/WIPlugin/WIMaintenance/WIAdmin/WICore/WIClass/WIMaintenanceRepository.php
 * Type: Plugin class
 * Layer: Repository / WIdb-only data access
 * Purpose Area: WIMaintenance records and events
 * Version: 0.1.0-dev
 * Created: 2026-06-01
 * Last Updated: 2026-06-01
 * Status: Functional developer foundation
 * Summary: Owns WIMaintenance reads/writes. UI and controllers must not access DB directly.
 */

declare(strict_types=1);

final class WIMaintenanceRepository
{
    private WIdb $WIdb;
    private string $recordsTable = 'wi_maintenance_records';
    private string $eventsTable = 'wi_maintenance_events';

    public function __construct(WIdb $WIdb)
    {
        $this->WIdb = $WIdb;
    }

    public function tableReady(): bool
    {
        return $this->tableExists($this->recordsTable) && $this->tableExists($this->eventsTable);
    }

    public function tableExists(string $table): bool
    {
        try {
            $rows = $this->WIdb->select(
                'SELECT COUNT(*) AS found FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :table_name',
                ['table_name' => $table]
            );
            return (int)($rows[0]['found'] ?? 0) > 0;
        } catch (Throwable $e) {
            return false;
        }
    }

    public function businesses(): array
    {
        if (!$this->tableExists('wi_org_businesses')) {
            return [];
        }

        try {
            return $this->WIdb->select(
                "SELECT id, id AS org_business_id, id AS business_id,
                        COALESCE(NULLIF(display_name, ''), NULLIF(business_name, ''), CONCAT('Business #', id)) AS display_name,
                        business_ref, status
                 FROM wi_org_businesses
                 WHERE status IN ('active','draft')
                 ORDER BY display_name ASC, id ASC"
            );
        } catch (Throwable $e) {
            return [];
        }
    }

    public function sites(int $businessId = 0): array
    {
        if (!$this->tableExists('wi_org_sites')) {
            return [];
        }

        $where = ["status IN ('active','draft')"];
        $params = [];

        if ($businessId > 0) {
            $where[] = 'org_business_id = :org_business_id';
            $params['org_business_id'] = $businessId;
        }

        try {
            return $this->WIdb->select(
                'SELECT id, id AS org_site_id, org_business_id, site_name, site_type, city_name, status, is_primary
                 FROM wi_org_sites
                 WHERE ' . implode(' AND ', $where) . '
                 ORDER BY org_business_id ASC, is_primary DESC, site_name ASC, id ASC',
                $params
            );
        } catch (Throwable $e) {
            return [];
        }
    }

    public function summary(array $filters = []): array
    {
        if (!$this->tableReady()) {
            return $this->emptySummary();
        }

        $scope = $this->scopeWhere('r', $filters);
        $rows = $this->safeSelect(
            "SELECT
                COUNT(*) AS total_records,
                SUM(CASE WHEN r.status = 'open' THEN 1 ELSE 0 END) AS open_records,
                SUM(CASE WHEN r.status = 'in_progress' THEN 1 ELSE 0 END) AS in_progress_records,
                SUM(CASE WHEN r.status = 'complete' THEN 1 ELSE 0 END) AS complete_records,
                SUM(CASE WHEN r.state = 'red' THEN 1 ELSE 0 END) AS red_records,
                SUM(CASE WHEN r.state = 'amber' THEN 1 ELSE 0 END) AS amber_records,
                SUM(CASE WHEN DATE(r.due_at) = CURRENT_DATE() THEN 1 ELSE 0 END) AS due_today,
                SUM(CASE WHEN r.due_at IS NOT NULL AND r.due_at < NOW() AND r.status <> 'complete' THEN 1 ELSE 0 END) AS overdue_records
             FROM {$this->recordsTable} r
             WHERE {$scope['where']}",
            $scope['params']
        );

        return $rows[0] ?? $this->emptySummary();
    }

    public function records(array $filters = []): array
    {
        if (!$this->tableReady()) {
            return [];
        }

        $scope = $this->scopeWhere('r', $filters);
        $where = [$scope['where']];
        $params = $scope['params'];

        $status = trim((string)($filters['status'] ?? ''));
        if ($status !== '' && $status !== 'all') {
            $where[] = 'r.status = :status';
            $params['status'] = $status;
        }

        $search = trim((string)($filters['search'] ?? ''));
        if ($search !== '') {
            $where[] = '(r.title LIKE :search_title OR r.record_type LIKE :search_type OR r.notes LIKE :search_notes)';
            $params['search_title'] = '%' . $search . '%';
            $params['search_type'] = '%' . $search . '%';
            $params['search_notes'] = '%' . $search . '%';
        }

        return $this->safeSelect(
            'SELECT r.*
             FROM ' . $this->recordsTable . ' r
             WHERE ' . implode(' AND ', $where) . '
             ORDER BY CASE r.state WHEN "red" THEN 1 WHEN "amber" THEN 2 WHEN "green" THEN 3 ELSE 4 END,
                      COALESCE(r.due_at, r.created_at) ASC,
                      r.id DESC
             LIMIT 300',
            $params
        );
    }

    public function events(array $filters = []): array
    {
        if (!$this->tableReady()) {
            return [];
        }

        $scope = $this->scopeWhere('e', $filters);
        return $this->safeSelect(
            'SELECT e.* FROM ' . $this->eventsTable . ' e WHERE ' . $scope['where'] . ' ORDER BY e.created_at DESC, e.id DESC LIMIT 100',
            $scope['params']
        );
    }

    public function saveRecord(array $input): int
    {
        $id = (int)($input['id'] ?? 0);
        $title = trim((string)($input['title'] ?? ''));
        if ($title === '') {
            $title = 'WIMaintenance record';
        }

        $data = [
            'org_business_id' => $this->nullableInt($input['org_business_id'] ?? $input['business_id'] ?? null),
            'org_site_id' => $this->nullableInt($input['org_site_id'] ?? $input['site_id'] ?? null),
            'record_type' => trim((string)($input['record_type'] ?? 'asset')) ?: 'asset',
            'title' => $title,
            'status' => $this->allowed((string)($input['status'] ?? 'open'), ['open','in_progress','complete','cancelled','archived'], 'open'),
            'state' => $this->allowed((string)($input['state'] ?? 'grey'), ['green','amber','red','grey'], 'grey'),
            'priority' => $this->allowed((string)($input['priority'] ?? 'normal'), ['low','normal','high','critical'], 'normal'),
            'due_at' => $this->normaliseDateTime((string)($input['due_at'] ?? '')),
            'assigned_user_id' => $this->nullableInt($input['assigned_user_id'] ?? null),
            'source_plugin' => trim((string)($input['source_plugin'] ?? 'wimaintenance')) ?: 'wimaintenance',
            'source_ref' => trim((string)($input['source_ref'] ?? '')) ?: null,
            'notes' => trim((string)($input['notes'] ?? '')) ?: null,
            'metadata_json' => $this->normaliseJson($input['metadata_json'] ?? '{}'),
            'updated_by_user_id' => $this->nullableInt($input['updated_by_user_id'] ?? $input['user_id'] ?? null),
        ];

        if ($id > 0) {
            $this->WIdb->update($this->recordsTable, $data, 'id = :id', ['id' => $id]);
            return $id;
        }

        $data['created_by_user_id'] = $this->nullableInt($input['created_by_user_id'] ?? $input['user_id'] ?? null);
        $this->WIdb->insert($this->recordsTable, $data);
        return (int)$this->WIdb->lastInsertId();
    }

    public function updateRecordState(int $id, string $state, string $status = ''): bool
    {
        if ($id <= 0) {
            return false;
        }

        $data = ['state' => $this->allowed($state, ['green','amber','red','grey'], 'grey')];
        if ($status !== '') {
            $data['status'] = $this->allowed($status, ['open','in_progress','complete','cancelled','archived'], 'open');
        }

        return $this->WIdb->update($this->recordsTable, $data, 'id = :id', ['id' => $id]);
    }

    public function logEvent(array $event): void
    {
        if (!$this->tableReady()) {
            return;
        }

        $this->WIdb->insert($this->eventsTable, [
            'org_business_id' => $this->nullableInt($event['org_business_id'] ?? null),
            'org_site_id' => $this->nullableInt($event['org_site_id'] ?? null),
            'event_type' => trim((string)($event['event_type'] ?? 'activity')) ?: 'activity',
            'related_type' => trim((string)($event['related_type'] ?? 'record')) ?: 'record',
            'related_id' => $this->nullableInt($event['related_id'] ?? null),
            'state' => $this->allowed((string)($event['state'] ?? 'grey'), ['green','amber','red','grey'], 'grey'),
            'message' => trim((string)($event['message'] ?? '')) ?: null,
            'created_by_user_id' => $this->nullableInt($event['created_by_user_id'] ?? $event['user_id'] ?? null),
        ]);
    }

    private function scopeWhere(string $alias, array $filters): array
    {
        $where = ['1=1'];
        $params = [];
        $businessId = (int)($filters['org_business_id'] ?? $filters['business_id'] ?? 0);
        $siteId = (int)($filters['org_site_id'] ?? $filters['site_id'] ?? 0);

        if ($businessId > 0) {
            $where[] = $alias . '.org_business_id = :' . $alias . '_org_business_id';
            $params[$alias . '_org_business_id'] = $businessId;
        }

        if ($siteId > 0) {
            $where[] = $alias . '.org_site_id = :' . $alias . '_org_site_id';
            $params[$alias . '_org_site_id'] = $siteId;
        }

        return ['where' => implode(' AND ', $where), 'params' => $params];
    }

    private function safeSelect(string $sql, array $params = []): array
    {
        try {
            return $this->WIdb->select($sql, $params);
        } catch (Throwable $e) {
            return [];
        }
    }

    private function emptySummary(): array
    {
        return [
            'total_records' => 0,
            'open_records' => 0,
            'in_progress_records' => 0,
            'complete_records' => 0,
            'red_records' => 0,
            'amber_records' => 0,
            'due_today' => 0,
            'overdue_records' => 0,
        ];
    }

    private function nullableInt(mixed $value): ?int
    {
        $int = (int)$value;
        return $int > 0 ? $int : null;
    }

    private function allowed(string $value, array $allowed, string $fallback): string
    {
        return in_array($value, $allowed, true) ? $value : $fallback;
    }

    private function normaliseDateTime(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        $timestamp = strtotime($value);
        return $timestamp ? date('Y-m-d H:i:s', $timestamp) : null;
    }

    private function normaliseJson(mixed $value): string
    {
        if (is_array($value)) {
            return json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}';
        }

        $string = trim((string)$value);
        if ($string === '') {
            return '{}';
        }

        json_decode($string, true);
        return json_last_error() === JSON_ERROR_NONE ? $string : '{}';
    }
}
