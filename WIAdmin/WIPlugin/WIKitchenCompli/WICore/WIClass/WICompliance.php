<?php

declare(strict_types=1);

class WICompliance
{
    protected mixed $db;
    protected string $tablePrefix = 'wi_compliance_';

    public function __construct(mixed $db = null)
    {
        $this->db = $db ?? $this->resolveDb();
    }

    protected function resolveDb(): mixed
    {
        if (class_exists('WIdb')) {
            if (method_exists('WIdb', 'getInstance')) {
                try {
                    return WIdb::getInstance();
                } catch (Throwable $e) {
                    return null;
                }
            }
        }

        return null;
    }

    public function dashboardSummary(): array
    {
        return [
            'legal_items' => $this->count('legal_register'),
            'active_checklists' => $this->count('checklists', ['is_active' => 1]),
            'open_actions' => $this->count('records', ['status' => 'open']),
            'overdue_reminders' => $this->countOverdueReminders(),
            'documents' => $this->count('documents'),
            'equipment_due' => $this->countEquipmentDue(),
            'audits' => $this->count('audits'),
            'training_due' => $this->countTrainingDue(),
        ];
    }

    public function getMenu(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'setup_wizard' => 'Setup Wizard',
            'legal_register' => 'Legal Register',
            'checklists' => 'Checklists',
            'logs_records' => 'Logs & Records',
            'audits' => 'Audits',
            'documents' => 'Documents',
            'training' => 'Training',
            'gdpr' => 'GDPR',
            'equipment' => 'Equipment',
            'reminders' => 'Reminders',
            'archive' => 'Archive',
            'help' => 'Help',
            'addons' => 'Add-ons',
            'settings' => 'Settings',
        ];
    }

    public function getSetupTemplate(): array
    {
        return [
            'business_name' => '',
            'business_type' => 'restaurant',
            'risk_profile' => 'medium',
            'site_count' => 1,
            'uses_delivery' => 0,
            'uses_alcohol_service' => 0,
            'stores_staff_records' => 1,
            'stores_customer_data' => 1,
            'retention_profile' => 'standard',
        ];
    }

    public function getDefaultModules(): array
    {
        return [
            ['code' => 'opening_checks', 'title' => 'Opening Checks', 'frequency' => 'daily'],
            ['code' => 'closing_checks', 'title' => 'Closing Checks', 'frequency' => 'daily'],
            ['code' => 'delivery_checks', 'title' => 'Delivery Checks', 'frequency' => 'daily'],
            ['code' => 'cleaning_checks', 'title' => 'Daily Cleaning', 'frequency' => 'daily'],
            ['code' => 'deep_cleaning', 'title' => 'Deep Cleaning', 'frequency' => 'weekly'],
            ['code' => 'safety_spot_checks', 'title' => 'Safety Spot Checks', 'frequency' => 'weekly'],
            ['code' => 'gdpr_register', 'title' => 'GDPR Register', 'frequency' => 'monthly'],
            ['code' => 'equipment_planner', 'title' => 'Equipment Planner', 'frequency' => 'monthly'],
        ];
    }

    public function saveSetup(array $data): array
    {
        $payload = [
            'business_name' => $this->string($data['business_name'] ?? ''),
            'business_type' => $this->string($data['business_type'] ?? 'restaurant'),
            'risk_profile' => $this->string($data['risk_profile'] ?? 'medium'),
            'site_count' => (int) ($data['site_count'] ?? 1),
            'uses_delivery' => !empty($data['uses_delivery']) ? 1 : 0,
            'uses_alcohol_service' => !empty($data['uses_alcohol_service']) ? 1 : 0,
            'stores_staff_records' => !empty($data['stores_staff_records']) ? 1 : 0,
            'stores_customer_data' => !empty($data['stores_customer_data']) ? 1 : 0,
            'retention_profile' => $this->string($data['retention_profile'] ?? 'standard'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($payload['business_name'] === '') {
            return ['status' => 'error', 'message' => 'Business name is required.'];
        }

        $this->insert('settings', $payload);
        return ['status' => 'success', 'message' => 'Setup saved successfully.', 'data' => $payload];
    }

    public function addChecklist(array $data): array
    {
        $payload = [
            'title' => $this->string($data['title'] ?? ''),
            'category' => $this->string($data['category'] ?? 'general'),
            'frequency' => $this->string($data['frequency'] ?? 'daily'),
            'assigned_role' => $this->string($data['assigned_role'] ?? 'manager'),
            'evidence_required' => !empty($data['evidence_required']) ? 1 : 0,
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($payload['title'] === '') {
            return ['status' => 'error', 'message' => 'Checklist title is required.'];
        }

        $this->insert('checklists', $payload);
        return ['status' => 'success', 'message' => 'Checklist created.', 'data' => $payload];
    }

    public function addLegalItem(array $data): array
    {
        $payload = [
            'title' => $this->string($data['title'] ?? ''),
            'jurisdiction' => $this->string($data['jurisdiction'] ?? 'UK'),
            'source_type' => $this->string($data['source_type'] ?? 'law'),
            'reference_code' => $this->string($data['reference_code'] ?? ''),
            'applies_to' => $this->string($data['applies_to'] ?? 'all_sites'),
            'summary' => $this->string($data['summary'] ?? ''),
            'review_cycle' => $this->string($data['review_cycle'] ?? 'annual'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($payload['title'] === '') {
            return ['status' => 'error', 'message' => 'Legal item title is required.'];
        }

        $this->insert('legal_register', $payload);
        return ['status' => 'success', 'message' => 'Legal register item added.', 'data' => $payload];
    }

    public function addReminder(array $data): array
    {
        $payload = [
            'title' => $this->string($data['title'] ?? ''),
            'priority' => $this->string($data['priority'] ?? 'medium'),
            'due_date' => $this->string($data['due_date'] ?? date('Y-m-d')),
            'owner_role' => $this->string($data['owner_role'] ?? 'manager'),
            'status' => $this->string($data['status'] ?? 'open'),
            'created_at' => date('Y-m-d H:i:s'),
        ];

        if ($payload['title'] === '') {
            return ['status' => 'error', 'message' => 'Reminder title is required.'];
        }

        $this->insert('reminders', $payload);
        return ['status' => 'success', 'message' => 'Reminder created.', 'data' => $payload];
    }

    public function recentActivity(): array
    {
        return [
            ['type' => 'checklist', 'title' => 'Opening checks completed', 'status' => 'complete'],
            ['type' => 'document', 'title' => 'Allergen policy updated', 'status' => 'review'],
            ['type' => 'reminder', 'title' => 'Fridge calibration due', 'status' => 'overdue'],
            ['type' => 'audit', 'title' => 'Kitchen hygiene audit scheduled', 'status' => 'planned'],
        ];
    }

    public function renderAdminDashboard(): string
    {
        $summary = $this->dashboardSummary();
        $activity = $this->recentActivity();
        $cards = '';

        foreach ($summary as $key => $value) {
            $cards .= '<div class="wic-card"><span class="wic-label">' . $this->escape(ucwords(str_replace('_', ' ', $key))) . '</span><strong>' . (int) $value . '</strong></div>';
        }

        $rows = '';
        foreach ($activity as $item) {
            $rows .= '<tr><td>' . $this->escape(ucfirst($item['type'])) . '</td><td>' . $this->escape($item['title']) . '</td><td>' . $this->escape(ucfirst($item['status'])) . '</td></tr>';
        }

        return '<div class="wic-wrap">'
            . '<div class="wic-hero"><h2>Kitchen Compliance Dashboard</h2><p>Audit-ready, evidence-driven compliance management for hospitality and catering businesses.</p></div>'
            . '<div class="wic-grid">' . $cards . '</div>'
            . '<div class="wic-panel"><h3>Recent Activity</h3><table class="wic-table"><thead><tr><th>Type</th><th>Title</th><th>Status</th></tr></thead><tbody>' . $rows . '</tbody></table></div>'
            . $this->renderQuickActions()
            . '</div>';
    }

    public function renderQuickActions(): string
    {
        return '<div class="wic-panel">'
            . '<h3>Quick Actions</h3>'
            . '<div class="wic-actions">'
            . '<button class="wic-btn" data-wic-action="open-checklist-form">New Checklist</button>'
            . '<button class="wic-btn" data-wic-action="open-legal-form">Add Legal Item</button>'
            . '<button class="wic-btn" data-wic-action="open-reminder-form">Create Reminder</button>'
            . '</div>'
            . '</div>';
    }

    public function renderSettingsScreen(): string
    {
        $menuItems = '';
        foreach ($this->getMenu() as $key => $label) {
            $menuItems .= '<li><span>' . $this->escape($label) . '</span><code>' . $this->escape($key) . '</code></li>';
        }

        return '<div class="wic-wrap">'
            . '<div class="wic-panel"><h2>Kitchen Compliance Settings</h2><p>Use this screen to align module names, permissions, scoring, reminders, retention and add-on behaviour with WICMS.</p></div>'
            . '<div class="wic-grid wic-grid-2">'
            . '<div class="wic-panel"><h3>Enabled Areas</h3><ul class="wic-list">' . $menuItems . '</ul></div>'
            . '<div class="wic-panel"><h3>Design Defaults</h3><ul class="wic-list">'
            . '<li>Evidence-driven checklist completion</li>'
            . '<li>GDPR by design</li>'
            . '<li>Multi-site ready structure</li>'
            . '<li>Role-based access paths</li>'
            . '<li>Retention and archive planning</li>'
            . '</ul></div>'
            . '</div>'
            . '</div>';
    }

    protected function count(string $table, array $conditions = []): int
    {
        if (!$this->db) {
            return 0;
        }

        try {
            $sql = 'SELECT COUNT(*) AS total FROM ' . $this->tableName($table);
            $params = [];
            if ($conditions !== []) {
                $parts = [];
                foreach ($conditions as $field => $value) {
                    $parts[] = $field . ' = :' . $field;
                    $params[$field] = $value;
                }
                $sql .= ' WHERE ' . implode(' AND ', $parts);
            }

            $stmt = $this->prepare($sql);
            foreach ($params as $field => $value) {
                $this->bindValue($stmt, ':' . $field, $value);
            }
            $this->execute($stmt);
            $row = $this->fetch($stmt);
            return (int) ($row['total'] ?? 0);
        } catch (Throwable $e) {
            return 0;
        }
    }

    protected function countOverdueReminders(): int
    {
        if (!$this->db) {
            return 0;
        }

        try {
            $sql = 'SELECT COUNT(*) AS total FROM ' . $this->tableName('reminders') . ' WHERE status = :status AND due_date < :today';
            $stmt = $this->prepare($sql);
            $this->bindValue($stmt, ':status', 'open');
            $this->bindValue($stmt, ':today', date('Y-m-d'));
            $this->execute($stmt);
            $row = $this->fetch($stmt);
            return (int) ($row['total'] ?? 0);
        } catch (Throwable $e) {
            return 0;
        }
    }

    protected function countEquipmentDue(): int
    {
        return $this->count('equipment', ['status' => 'due']);
    }

    protected function countTrainingDue(): int
    {
        return $this->count('training', ['status' => 'due']);
    }

    protected function insert(string $table, array $data): bool
    {
        if (!$this->db) {
            return false;
        }

        try {
            if (method_exists($this->db, 'insert')) {
                $this->db->insert($this->tableName($table), $data);
                return true;
            }

            $columns = array_keys($data);
            $placeholders = array_map(static fn (string $column): string => ':' . $column, $columns);
            $sql = 'INSERT INTO ' . $this->tableName($table)
                . ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';
            $stmt = $this->prepare($sql);
            foreach ($data as $column => $value) {
                $this->bindValue($stmt, ':' . $column, $value);
            }
            return $this->execute($stmt);
        } catch (Throwable $e) {
            return false;
        }
    }

    protected function tableName(string $table): string
    {
        return $this->tablePrefix . $table;
    }

    protected function prepare(string $sql): mixed
    {
        if (method_exists($this->db, 'prepare')) {
            return $this->db->prepare($sql);
        }

        if (property_exists($this->db, 'pdo') && $this->db->pdo instanceof PDO) {
            return $this->db->pdo->prepare($sql);
        }

        throw new RuntimeException('Database prepare method not available.');
    }

    protected function bindValue(mixed $stmt, string $param, mixed $value): void
    {
        if (method_exists($stmt, 'bindValue')) {
            $stmt->bindValue($param, $value);
        }
    }

    protected function execute(mixed $stmt): bool
    {
        if (method_exists($stmt, 'execute')) {
            return (bool) $stmt->execute();
        }

        return false;
    }

    protected function fetch(mixed $stmt): array
    {
        if (method_exists($stmt, 'fetch')) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return is_array($row) ? $row : [];
        }

        return [];
    }

    protected function string(mixed $value): string
    {
        return trim((string) $value);
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
