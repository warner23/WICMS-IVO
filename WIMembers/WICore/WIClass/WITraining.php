<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WIMembers / WIProfile
| Project: WI Ecosystem
| File: WITraining.php
| Location: /WICore/WIClass/WITraining.php
| Type: Class
| Layer: Training Bridge
| Purpose Area: Worker/member training list, summary and simple completion actions
| Version: 1.1.0
| Created: 2026-05-24
| Last Updated: 2026-06-08
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Reads assigned compliance training for the logged-in user and exposes it to
| the WIProfile worker workspace. Admin-side training builders remain separate;
| this class is the worker-side assignment/record bridge.
*/

final class WITraining
{
    private WIdb $WIdb;

    public function __construct(?WIdb $WIdb = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
    }

    public function assignedToUser(int $userId, int $limit = 24): array
    {
        if ($userId <= 0 || !$this->hasTables(['wi_compliance_training_assignments', 'wi_compliance_training'])) {
            return [];
        }

        $limit = max(1, min(100, $limit));
        $rows = $this->WIdb->select(
            'SELECT a.id AS assignment_id,
                    a.training_id,
                    a.user_id,
                    a.site_id,
                    a.org_business_id,
                    a.org_site_id,
                    a.org_department_id,
                    a.role_id,
                    a.status AS assignment_status,
                    a.assigned_at,
                    a.due_date,
                    a.completed_at,
                    a.notes AS assignment_notes,
                    t.title,
                    t.training_code,
                    t.category,
                    t.description,
                    t.role_name,
                    t.is_required,
                    t.valid_months,
                    t.duration_minutes,
                    t.renewal_due,
                    t.status AS training_status,
                    s.site_name,
                    r.id AS record_id,
                    r.score,
                    r.result_status,
                    r.completed_on,
                    r.renewal_due AS record_renewal_due
               FROM `wi_compliance_training_assignments` a
         INNER JOIN `wi_compliance_training` t ON t.id = a.training_id
          LEFT JOIN `wi_org_sites` s ON s.id = COALESCE(a.org_site_id, a.site_id)
          LEFT JOIN `wi_compliance_training_records` r ON r.assignment_id = a.id
              WHERE a.user_id = :user_id
           ORDER BY CASE a.status
                        WHEN "overdue" THEN 1
                        WHEN "assigned" THEN 2
                        WHEN "in_progress" THEN 3
                        WHEN "completed" THEN 4
                        ELSE 5
                    END,
                    COALESCE(a.due_date, DATE(a.assigned_at), "2999-12-31") ASC
              LIMIT ' . $limit,
            ['user_id' => $userId]
        );

        return array_map(fn(array $row): array => $this->normaliseTrainingRow($row), $rows);
    }

    public function summary(int $userId): array
    {
        $items = $this->assignedToUser($userId, 100);
        $summary = [
            'total' => count($items),
            'completed' => 0,
            'due' => 0,
            'overdue' => 0,
            'in_progress' => 0,
            'required' => 0,
            'items' => array_slice($items, 0, 6),
        ];

        foreach ($items as $item) {
            if ((int) ($item['is_required'] ?? 0) === 1) {
                $summary['required']++;
            }

            match ((string) ($item['state'] ?? 'due')) {
                'completed' => $summary['completed']++,
                'overdue' => $summary['overdue']++,
                'in_progress' => $summary['in_progress']++,
                default => $summary['due']++,
            };
        }

        return $summary;
    }

    public function startAssignment(int $userId, int $assignmentId): array
    {
        if (!$this->canTouchAssignment($userId, $assignmentId)) {
            return ['success' => false, 'message' => 'Training assignment not found.'];
        }

        $this->WIdb->update(
            'wi_compliance_training_assignments',
            ['status' => 'in_progress'],
            '`id` = :assignment_id AND `user_id` = :user_id AND `status` IN ("assigned", "overdue")',
            ['assignment_id' => $assignmentId, 'user_id' => $userId]
        );

        return ['success' => true, 'message' => 'Training started.', 'training' => $this->assignedToUser($userId)];
    }

    public function completeAssignment(int $userId, int $assignmentId, array $input = []): array
    {
        $assignment = $this->assignmentForUser($userId, $assignmentId);
        if ($assignment === []) {
            return ['success' => false, 'message' => 'Training assignment not found.'];
        }

        $now = date('Y-m-d H:i:s');
        $today = date('Y-m-d');
        $score = isset($input['score']) && $input['score'] !== '' ? (float) $input['score'] : null;
        $notes = trim((string) ($input['notes'] ?? ''));

        $this->WIdb->update(
            'wi_compliance_training_assignments',
            [
                'status' => 'completed',
                'completed_at' => $now,
                'notes' => $notes !== '' ? $notes : (string) ($assignment['notes'] ?? ''),
            ],
            '`id` = :assignment_id AND `user_id` = :user_id',
            ['assignment_id' => $assignmentId, 'user_id' => $userId]
        );

        if ($this->WIdb->tableExists('wi_compliance_training_records')) {
            $existing = $this->WIdb->row(
                'SELECT `id` FROM `wi_compliance_training_records` WHERE `assignment_id` = :assignment_id LIMIT 1',
                ['assignment_id' => $assignmentId]
            );

            $record = [
                'assignment_id' => $assignmentId,
                'training_id' => (int) $assignment['training_id'],
                'user_id' => $userId,
                'site_id' => (int) ($assignment['site_id'] ?? 0) ?: null,
                'score' => $score,
                'result_status' => 'completed',
                'completed_on' => $today,
                'renewal_due' => $this->renewalDueFromAssignment($assignment),
                'trainer_name' => 'Worker self-complete',
                'notes' => $notes,
            ];

            if ($existing !== []) {
                $this->WIdb->update('wi_compliance_training_records', $record, '`id` = :id', ['id' => (int) $existing['id']]);
            } else {
                $this->WIdb->insert('wi_compliance_training_records', $record);
            }
        }

        return ['success' => true, 'message' => 'Training marked as complete.', 'training' => $this->assignedToUser($userId)];
    }

    private function normaliseTrainingRow(array $row): array
    {
        $status = (string) ($row['assignment_status'] ?? 'assigned');
        $dueDate = (string) ($row['due_date'] ?? '');
        $completed = $status === 'completed' || !empty($row['completed_at']) || !empty($row['completed_on']);
        $overdue = !$completed && $dueDate !== '' && $dueDate < date('Y-m-d');
        $state = $completed ? 'completed' : ($overdue ? 'overdue' : ($status === 'in_progress' ? 'in_progress' : 'due'));

        return [
            'assignment_id' => (int) ($row['assignment_id'] ?? 0),
            'training_id' => (int) ($row['training_id'] ?? 0),
            'title' => (string) ($row['title'] ?? 'Training'),
            'training_code' => (string) ($row['training_code'] ?? ''),
            'category' => (string) ($row['category'] ?? 'General'),
            'description' => (string) ($row['description'] ?? ''),
            'role_name' => (string) ($row['role_name'] ?? ''),
            'site_name' => (string) ($row['site_name'] ?? ''),
            'due_date' => $dueDate,
            'assigned_at' => (string) ($row['assigned_at'] ?? ''),
            'completed_at' => (string) ($row['completed_at'] ?? ''),
            'renewal_due' => (string) ($row['record_renewal_due'] ?? $row['renewal_due'] ?? ''),
            'duration_minutes' => (int) ($row['duration_minutes'] ?? 0),
            'is_required' => (int) ($row['is_required'] ?? 0),
            'score' => $row['score'] ?? null,
            'state' => $state,
            'status_label' => ucwords(str_replace('_', ' ', $state)),
            'overdue' => $overdue,
            'can_start' => in_array($state, ['due', 'overdue'], true),
            'can_complete' => $state !== 'completed',
        ];
    }

    private function assignmentForUser(int $userId, int $assignmentId): array
    {
        if ($userId <= 0 || $assignmentId <= 0 || !$this->WIdb->tableExists('wi_compliance_training_assignments')) {
            return [];
        }

        return $this->WIdb->row(
            'SELECT * FROM `wi_compliance_training_assignments` WHERE `id` = :id AND `user_id` = :user_id LIMIT 1',
            ['id' => $assignmentId, 'user_id' => $userId]
        );
    }

    private function canTouchAssignment(int $userId, int $assignmentId): bool
    {
        return $this->assignmentForUser($userId, $assignmentId) !== [];
    }

    private function renewalDueFromAssignment(array $assignment): ?string
    {
        if (!$this->WIdb->tableExists('wi_compliance_training')) {
            return null;
        }

        $training = $this->WIdb->row('SELECT `valid_months`, `renewal_due` FROM `wi_compliance_training` WHERE `id` = :id LIMIT 1', ['id' => (int) $assignment['training_id']]);
        $validMonths = (int) ($training['valid_months'] ?? 0);

        if ($validMonths > 0) {
            return date('Y-m-d', strtotime('+' . $validMonths . ' months'));
        }

        return !empty($training['renewal_due']) ? (string) $training['renewal_due'] : null;
    }

    private function hasTables(array $tables): bool
    {
        foreach ($tables as $table) {
            if (!$this->WIdb->tableExists((string) $table)) {
                return false;
            }
        }

        return true;
    }
}
