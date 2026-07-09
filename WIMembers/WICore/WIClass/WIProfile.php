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
| File: WIProfile.php
| Location: /WIMembers/WICore/WIClass/WIProfile.php
| Type: Class
| Layer: Profile Workspace Orchestrator
| Purpose Area: Member/worker dashboard payload
| Version: 1.2.0
| Created: 2026-05-24
| Last Updated: 2026-06-08
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Builds the worker/member profile payload by reading user, WIHR, WIOrg,
| training, forms, document acknowledgements and task bridge data. This keeps
| WIProfile as a workspace facade instead of duplicating compliance/checklist
| business logic.
*/

final class WIProfile
{
    private WIdb $WIdb;
    private WIUser $user;
    private WIBusiness $business;
    private WITraining $training;
    private WIHr $hr;
    private WIForms $forms;

    public function __construct(?int $userId = null)
    {
        $this->WIdb = WIdb::getInstance();
        $this->user = new WIUser($userId);
        $this->hr = new WIHr($this->WIdb);
        $this->business = new WIBusiness($this->WIdb, $this->hr);
        $this->training = new WITraining($this->WIdb);
        $this->forms = new WIForms($this->WIdb, $this->business);
    }

    public function dashboardPayload(): array
    {
        $userId = $this->resolveUserId();
        if ($userId <= 0) {
            return [
                'success' => false,
                'auth_required' => true,
                'csrf_token' => WICsrf::getToken(),
                'message' => 'Please log in to open your profile workspace.',
                'user' => ['id' => 0, 'name' => 'Member', 'email' => '', 'username' => '', 'role_id' => 0, 'role_name' => '', 'avatar' => '', 'member_since' => '', 'last_login' => '', 'profile_completion' => 0],
                'details' => [],
                'employee' => [],
                'business' => ['businesses' => [], 'sites' => [], 'primary_site' => [], 'flags' => []],
                'training' => [],
                'forms' => [],
                'checklists' => [],
                'actions' => ['total' => 0, 'items' => []],
                'tasks' => [],
            ];
        }

        $info = $this->user->getInfo();
        $details = $this->user->getDetails();
        $businessContext = $this->business->contextForUser($userId);
        $hr = $this->hr->onboardingStatus($userId);
        $training = $this->training->summary($userId);
        $forms = $this->forms->summary($userId);
        $tasks = $this->taskSummary($userId);
        $openActions = $this->openOptionalActions($userId);
        $checklists = $this->checklistSummary($userId, $businessContext);

        return [
            'success' => true,
            'csrf_token' => WICsrf::getToken(),
            'user' => [
                'id' => $userId,
                'name' => $this->user->fullName(),
                'email' => (string) ($info['email'] ?? ''),
                'username' => (string) ($info['username'] ?? ''),
                'role_id' => $this->user->getRoleId(),
                'role_name' => $this->user->getRoleName(),
                'avatar' => $this->user->avatarUrl(),
                'member_since' => (string) ($info['register_date'] ?? ''),
                'last_login' => (string) ($info['last_login'] ?? ''),
                'profile_completion' => $this->user->profileCompletion(),
            ],
            'details' => $details,
            'employee' => $hr,
            'business' => $businessContext,
            'training' => $training,
            'forms' => $forms,
            'checklists' => $checklists,
            'actions' => $openActions,
            'tasks' => $tasks,
        ];
    }

    public function updateProfile(array $data): array
    {
        $allowed = [
            'first_name',
            'last_name',
            'phone',
            'address',
            'country',
            'region',
            'city',
            'bio_body',
            'website',
            'youtube',
            'facebook',
            'twitter',
        ];

        $details = [];
        foreach ($allowed as $field) {
            if (array_key_exists($field, $data)) {
                $details[$field] = trim((string) $data[$field]);
            }
        }

        $this->user->updateDetails($details);

        return ['success' => true, 'message' => 'Profile saved.', 'profile' => $this->dashboardPayload()];
    }

    private function resolveUserId(): int
    {
        $id = $this->user->id();
        if ((int) $id > 0) {
            return (int) $id;
        }

        if (class_exists('WISession')) {
            $sessionId = (int) WISession::get('user_id', 0);
            if ($sessionId > 0) {
                return $sessionId;
            }
        }

        return (int) ($_SESSION['user_id'] ?? $_SESSION['id'] ?? 0);
    }

    private function correctiveActionDueExpression(): string
    {
        if ($this->WIdb->columnExists('wi_compliance_corrective_actions', 'due_at')) {
            return 'COALESCE(`due_at`, `created_at`)';
        }

        if ($this->WIdb->columnExists('wi_compliance_corrective_actions', 'due_date')) {
            return 'COALESCE(`due_date`, `created_at`)';
        }

        return '`created_at`';
    }

    private function checklistSummary(int $userId, array $businessContext): array
    {
        if ($userId <= 0 || !$this->WIdb->tableExists('wi_compliance_checklist_instances')) {
            return [
                'total' => 0,
                'available' => 0,
                'completed' => 0,
                'missed' => 0,
                'next_url' => '',
            ];
        }

        $siteIds = array_values(array_filter(array_map(static fn(array $site): int => (int) ($site['org_site_id'] ?? $site['id'] ?? 0), $businessContext['sites'] ?? [])));
        if ($siteIds === []) {
            return [
                'total' => 0,
                'available' => 0,
                'completed' => 0,
                'missed' => 0,
                'next_url' => '',
            ];
        }

        $placeholders = [];
        $bind = [];
        foreach ($siteIds as $index => $siteId) {
            $key = 'site_' . $index;
            $placeholders[] = ':' . $key;
            $bind[$key] = $siteId;
        }

        $rows = $this->WIdb->select(
            'SELECT `status`, COUNT(*) AS total
               FROM `wi_compliance_checklist_instances`
              WHERE `org_site_id` IN (' . implode(',', $placeholders) . ')
                AND DATE(`instance_date`) = CURDATE()
           GROUP BY `status`',
            $bind
        );

        $summary = [
            'total' => 0,
            'available' => 0,
            'completed' => 0,
            'missed' => 0,
            'next_url' => '',
        ];

        foreach ($rows as $row) {
            $status = (string) ($row['status'] ?? '');
            $count = (int) ($row['total'] ?? 0);
            $summary['total'] += $count;

            if (isset($summary[$status])) {
                $summary[$status] += $count;
            }
        }

        return $summary;
    }

    private function openOptionalActions(int $userId): array
    {
        if ($userId <= 0 || !$this->WIdb->tableExists('wi_compliance_corrective_actions')) {
            return ['total' => 0, 'items' => []];
        }

        $assignedColumn = $this->WIdb->columnExists('wi_compliance_corrective_actions', 'assigned_to_user_id')
            ? 'assigned_to_user_id'
            : ($this->WIdb->columnExists('wi_compliance_corrective_actions', 'assigned_user_id') ? 'assigned_user_id' : 'created_by_user_id');

        $rows = $this->WIdb->select(
            'SELECT *
               FROM `wi_compliance_corrective_actions`
              WHERE `' . $assignedColumn . '` = :user_id
                AND COALESCE(`status`, "open") NOT IN ("closed", "completed", "cancelled", "archived")
           ORDER BY ' . $this->correctiveActionDueExpression() . ' ASC
              LIMIT 6',
            ['user_id' => $userId]
        );

        return [
            'total' => count($rows),
            'items' => $rows,
        ];
    }

    private function taskSummary(int $userId): array
    {
        $path = __DIR__ . '/WIProfileTaskEngineBridge.php';
        if (is_file($path) && !class_exists('WIProfileTaskEngineBridge', false)) {
            require_once $path;
        }

        if (!class_exists('WIProfileTaskEngineBridge', false)) {
            return [
                'success' => false,
                'message' => 'WITaskEngine bridge not installed.',
                'summary' => [
                    'total' => 0,
                    'open' => 0,
                    'due_today' => 0,
                    'overdue' => 0,
                    'high_priority' => 0,
                    'awaiting_proof' => 0,
                    'completed' => 0,
                ],
                'tasks' => [],
            ];
        }

        return (new WIProfileTaskEngineBridge($userId))->dashboard(['status' => 'all']);
    }
}
