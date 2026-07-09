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
| File: WIForms.php
| Location: /WICore/WIClass/WIForms.php
| Type: Class
| Layer: Forms / Document Acknowledgement Bridge
| Purpose Area: Worker/member forms, submissions and document acknowledgements
| Version: 1.1.1
| Created: 2026-05-24
| Last Updated: 2026-06-09
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Provides worker-facing forms and document acknowledgement payloads. Uploads
| remain future WIMedia work; this batch stores structured submissions and
| acknowledgement actions only.
*/

final class WIForms
{
    private WIdb $WIdb;
    private WIBusiness $business;
    private WIHr $hr;

    public function __construct(?WIdb $WIdb = null, ?WIBusiness $business = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
        $this->hr = new WIHr($this->WIdb);
        $this->business = $business ?? new WIBusiness($this->WIdb, $this->hr);
    }

    public function availableForUser(int $userId): array
    {
        if ($userId <= 0 || !$this->WIdb->tableExists('wi_member_forms')) {
            return [];
        }

        $context = $this->business->contextForUser($userId);
        $employeeContext = $this->hr->onboardingStatus($userId);
        $businessIds = array_map(static fn(array $row): int => (int) ($row['id'] ?? 0), $context['businesses'] ?? []);
        $siteIds = array_map(static fn(array $row): int => (int) ($row['org_site_id'] ?? $row['id'] ?? 0), $context['sites'] ?? []);
        $roleIds = array_map(static fn(array $row): int => (int) ($row['role_id'] ?? 0), $employeeContext['roles'] ?? []);
        $departmentIds = array_map(static fn(array $row): int => (int) ($row['org_department_id'] ?? 0), $employeeContext['departments'] ?? []);
        $assignmentDate = $this->assignmentDateFromContext($employeeContext);

        $rows = $this->WIdb->select(
            'SELECT f.*,
                    s.id AS submission_id,
                    s.status AS submission_status,
                    s.submitted_at,
                    s.reviewed_at
               FROM `wi_member_forms` f
          LEFT JOIN `wi_member_form_submissions` s ON s.form_id = f.id AND s.user_id = :user_id
              WHERE f.is_active = 1
           ORDER BY f.sort_order ASC, f.title ASC
              LIMIT 100',
            ['user_id' => $userId]
        );

        return array_values(array_filter(array_map(function (array $row) use ($businessIds, $siteIds, $roleIds, $departmentIds, $assignmentDate): ?array {
            $businessId = (int) ($row['org_business_id'] ?? 0);
            $siteId = (int) ($row['org_site_id'] ?? 0);

            if ($businessId > 0 && !in_array($businessId, $businessIds, true)) {
                return null;
            }

            if ($siteId > 0 && !in_array($siteId, $siteIds, true)) {
                return null;
            }

            $departmentId = (int) ($row['org_department_id'] ?? 0);
            if ($departmentId > 0 && !in_array($departmentId, $departmentIds, true)) {
                return null;
            }

            $roleId = (int) ($row['role_id'] ?? 0);
            if ($roleId > 0 && !in_array($roleId, $roleIds, true)) {
                return null;
            }

            return $this->normaliseFormRow($row, $assignmentDate);
        }, $rows)));
    }

    public function submissionsForUser(int $userId): array
    {
        if ($userId <= 0 || !$this->WIdb->tableExists('wi_member_form_submissions')) {
            return [];
        }

        return $this->WIdb->select(
            'SELECT s.*, f.title, f.form_type
               FROM `wi_member_form_submissions` s
          LEFT JOIN `wi_member_forms` f ON f.id = s.form_id
              WHERE s.user_id = :user_id
           ORDER BY s.submitted_at DESC
              LIMIT 50',
            ['user_id' => $userId]
        );
    }

    public function documentAcknowledgementsForUser(int $userId): array
    {
        if ($userId <= 0 || !$this->hasTables(['wi_compliance_documents', 'wi_compliance_document_acknowledgements'])) {
            return [];
        }

        $context = $this->business->contextForUser($userId);
        $employeeContext = $this->hr->onboardingStatus($userId);
        $businessIds = array_map(static fn(array $row): int => (int) ($row['id'] ?? 0), $context['businesses'] ?? []);
        $siteIds = array_map(static fn(array $row): int => (int) ($row['org_site_id'] ?? $row['id'] ?? 0), $context['sites'] ?? []);
        $departmentIds = array_map(static fn(array $row): int => (int) ($row['org_department_id'] ?? 0), $employeeContext['departments'] ?? []);

        $rows = $this->WIdb->select(
            'SELECT d.id AS document_id,
                    d.title,
                    d.document_type,
                    d.category,
                    d.description,
                    d.org_business_id,
                    d.org_site_id,
                    d.org_department_id,
                    d.is_required,
                    d.review_due,
                    d.expiry_date,
                    a.id AS acknowledgement_id,
                    a.acknowledged_at,
                    a.notes AS acknowledgement_notes
               FROM `wi_compliance_documents` d
          LEFT JOIN `wi_compliance_document_acknowledgements` a ON a.document_id = d.id AND a.user_id = :user_id
              WHERE d.is_active = 1
                AND d.status IN ("approved", "pending_signoff", "needs_update")
           ORDER BY d.is_required DESC, COALESCE(d.review_due, d.expiry_date, "2999-12-31") ASC, d.title ASC
              LIMIT 100',
            ['user_id' => $userId]
        );

        return array_values(array_filter(array_map(function (array $row) use ($businessIds, $siteIds, $departmentIds): ?array {
            $businessId = (int) ($row['org_business_id'] ?? 0);
            $siteId = (int) ($row['org_site_id'] ?? 0);

            if ($businessId > 0 && !in_array($businessId, $businessIds, true)) {
                return null;
            }

            if ($siteId > 0 && !in_array($siteId, $siteIds, true)) {
                return null;
            }

            $departmentId = (int) ($row['org_department_id'] ?? 0);
            if ($departmentId > 0 && !in_array($departmentId, $departmentIds, true)) {
                return null;
            }

            $acknowledged = (int) ($row['acknowledgement_id'] ?? 0) > 0;

            return [
                'document_id' => (int) ($row['document_id'] ?? 0),
                'title' => (string) ($row['title'] ?? 'Document'),
                'document_type' => (string) ($row['document_type'] ?? ''),
                'category' => (string) ($row['category'] ?? ''),
                'description' => (string) ($row['description'] ?? ''),
                'is_required' => (int) ($row['is_required'] ?? 0),
                'review_due' => (string) ($row['review_due'] ?? ''),
                'expiry_date' => (string) ($row['expiry_date'] ?? ''),
                'acknowledged' => $acknowledged,
                'acknowledged_at' => (string) ($row['acknowledged_at'] ?? ''),
                'state' => $acknowledged ? 'completed' : 'due',
            ];
        }, $rows)));
    }

    public function summary(int $userId): array
    {
        $forms = $this->availableForUser($userId);
        $documents = $this->documentAcknowledgementsForUser($userId);

        return [
            'forms_total' => count($forms),
            'forms_submitted' => count(array_filter($forms, static fn(array $row): bool => (string) ($row['state'] ?? '') === 'completed')),
            'forms_due' => count(array_filter($forms, static fn(array $row): bool => (string) ($row['state'] ?? '') !== 'completed')),
            'documents_total' => count($documents),
            'documents_acknowledged' => count(array_filter($documents, static fn(array $row): bool => (bool) ($row['acknowledged'] ?? false))),
            'documents_due' => count(array_filter($documents, static fn(array $row): bool => !(bool) ($row['acknowledged'] ?? false))),
            'forms' => array_slice($forms, 0, 6),
            'documents' => array_slice($documents, 0, 6),
        ];
    }

    public function submit(int $userId, array $data): array
    {
        if ($userId <= 0) {
            return ['success' => false, 'message' => 'Not logged in.'];
        }

        if (!$this->WIdb->tableExists('wi_member_form_submissions')) {
            return ['success' => false, 'message' => 'Missing wi_member_form_submissions table. Run the included SQL patch.'];
        }

        $formId = (int) ($data['form_id'] ?? 0);
        if ($formId <= 0) {
            return ['success' => false, 'message' => 'Missing form id.'];
        }

        $available = array_filter($this->availableForUser($userId), static fn(array $row): bool => (int) $row['id'] === $formId);
        if ($available === []) {
            return ['success' => false, 'message' => 'Form is not available for this account.'];
        }

        $answers = $data['answers'] ?? [];
        if (!is_array($answers)) {
            $answers = ['answer' => (string) $answers];
        }

        $payload = [
            'form_id' => $formId,
            'user_id' => $userId,
            'status' => 'submitted',
            'answers_json' => json_encode($answers, JSON_THROW_ON_ERROR),
            'submitted_at' => date('Y-m-d H:i:s'),
        ];

        $existing = $this->WIdb->row(
            'SELECT `id` FROM `wi_member_form_submissions` WHERE `form_id` = :form_id AND `user_id` = :user_id LIMIT 1',
            ['form_id' => $formId, 'user_id' => $userId]
        );

        if ($existing !== []) {
            $this->WIdb->update('wi_member_form_submissions', $payload, '`id` = :id', ['id' => (int) $existing['id']]);
        } else {
            $this->WIdb->insert('wi_member_form_submissions', $payload);
        }

        return ['success' => true, 'message' => 'Form submitted.', 'forms' => $this->availableForUser($userId)];
    }

    public function acknowledgeDocument(int $userId, int $documentId, string $notes = ''): array
    {
        if ($userId <= 0 || $documentId <= 0) {
            return ['success' => false, 'message' => 'Missing document acknowledgement details.'];
        }

        if (!$this->WIdb->tableExists('wi_compliance_document_acknowledgements')) {
            return ['success' => false, 'message' => 'Document acknowledgement table is missing.'];
        }

        $existing = $this->WIdb->row(
            'SELECT `id` FROM `wi_compliance_document_acknowledgements` WHERE `document_id` = :document_id AND `user_id` = :user_id LIMIT 1',
            ['document_id' => $documentId, 'user_id' => $userId]
        );

        $payload = [
            'document_id' => $documentId,
            'user_id' => $userId,
            'site_id' => 0,
            'acknowledged_at' => date('Y-m-d H:i:s'),
            'notes' => $notes !== '' ? mb_substr($notes, 0, 255) : 'Acknowledged from WIProfile workspace.',
        ];

        if ($existing !== []) {
            $this->WIdb->update('wi_compliance_document_acknowledgements', $payload, '`id` = :id', ['id' => (int) $existing['id']]);
        } else {
            $this->WIdb->insert('wi_compliance_document_acknowledgements', $payload);
        }

        return ['success' => true, 'message' => 'Document acknowledged.', 'documents' => $this->documentAcknowledgementsForUser($userId)];
    }

    private function normaliseFormRow(array $row, string $assignmentDate = ''): array
    {
        $submitted = (int) ($row['submission_id'] ?? 0) > 0;
        $schema = json_decode((string) ($row['schema_json'] ?? '[]'), true);

        $dueDays = isset($row['due_days_after_assignment']) ? (int) $row['due_days_after_assignment'] : 0;
        $dueDate = $dueDays > 0 && $assignmentDate !== ''
            ? date('Y-m-d', strtotime($assignmentDate . ' +' . $dueDays . ' days'))
            : '';
        $overdue = !$submitted && $dueDate !== '' && $dueDate < date('Y-m-d');

        return [
            'id' => (int) ($row['id'] ?? 0),
            'form_code' => (string) ($row['form_code'] ?? ''),
            'title' => (string) ($row['title'] ?? 'Form'),
            'form_type' => (string) ($row['form_type'] ?? 'general'),
            'description' => (string) ($row['description'] ?? ''),
            'is_required' => (int) ($row['is_required'] ?? 0),
            'due_days_after_assignment' => $dueDays,
            'due_date' => $dueDate,
            'schema' => is_array($schema) ? $schema : [],
            'submission_id' => (int) ($row['submission_id'] ?? 0),
            'submission_status' => (string) ($row['submission_status'] ?? ''),
            'submitted_at' => (string) ($row['submitted_at'] ?? ''),
            'state' => $submitted ? 'completed' : ($overdue ? 'overdue' : 'due'),
            'status_label' => $submitted ? 'Submitted' : ($overdue ? 'Overdue' : 'Due'),
        ];
    }

    private function assignmentDateFromContext(array $employeeContext): string
    {
        $dates = [];

        foreach (['sites', 'roles', 'departments'] as $group) {
            foreach ($employeeContext[$group] ?? [] as $assignment) {
                $date = trim((string) ($assignment['effective_from'] ?? ''));
                if ($date !== '') {
                    $dates[] = $date;
                }
            }
        }

        sort($dates);
        return $dates[0] ?? '';
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
