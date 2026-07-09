<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WI Shared Core
| Project: WI Ecosystem
| Class: WIBugReporter
| File: WIBugReporter.php
| Location: /WIAdmin/WICore/WIClass/
| Type: Shared Core Class
| Layer: Admin / Shared Module Service
| Purpose Area: Local bug reporting and admin issue tracking
| Version: 1.1.0
| Created: 2026-04-16
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Shared local bug reporter for WICMS, WIMembers, Admin, Compliance and future
| marketplace modules. This first version saves reports locally and supports
| admin list/thread/status/reply actions. WILabs forwarding is intentionally
| left for the next phase after local reporting is proven.
*/

final class WIBugReporter
{
    private WIdb $WIdb;
    private WIBugReportSanitizer $sanitizer;

    private string $reportTable = 'wi_system_bug_reports';
    private string $messageTable = 'wi_system_bug_report_messages';

    /** @var array<string,array<int,string>> */
    private array $columnCache = [];

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->sanitizer = new WIBugReportSanitizer();
    }

    /**
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public function createReport(array $payload): array
    {
        if (!$this->isReady()) {
            return $this->error('Bug reporting tables are not available.');
        }

        $context = $this->getContextSnapshot();
        $report = $this->normaliseReportPayload($payload, $context);
        $validation = $this->validateReportPayload($report);

        if ($validation !== null) {
            return $validation;
        }

        $insert = $this->filterDataForTable($this->reportTable, [
            'business_id'         => $report['business_id'],
            'site_id'             => $report['site_id'],
            'user_id'             => $report['user_id'],
            'user_role_id'        => $report['user_role_id'],
            'area_type'           => $report['area_type'],
            'module_name'         => $report['module_name'],
            'page_key'            => $report['page_key'],
            'page_url'            => $report['page_url'],
            'action_name'         => $report['action_name'],
            'severity'            => $report['severity'],
            'title'               => $report['title'],
            'description'         => $report['description'],
            'status'              => 'open',
            'assigned_to_user_id' => null,
            'browser'             => $report['browser'],
            'user_agent'          => $report['user_agent'],
            'ip_address'          => $report['ip_address'],
        ]);

        if ($insert === [] || !$this->WIdb->insert($this->reportTable, $insert)) {
            return $this->error('Unable to save bug report.');
        }

        $reportId = (int) $this->WIdb->lastInsertId();

        $this->addMessage(
            $reportId,
            'report',
            (string) $report['description'],
            $report['user_id'] > 0 ? (int) $report['user_id'] : null
        );

        $forwarding = $this->forwardReportIfEnabled($reportId);

        return $this->success('Bug report submitted successfully.', [
            'report_id' => $reportId,
            'forwarding' => $forwarding,
        ]);
    }

    /**
     * @param array<string,mixed> $filters
     * @return array<string,mixed>
     */
    public function getReports(array $filters = []): array
    {
        if (!$this->tableExists($this->reportTable)) {
            return $this->success('No bug reports table found.', []);
        }

        $sql = "SELECT * FROM `{$this->reportTable}` WHERE 1 = 1";
        $params = [];

        foreach (['status', 'severity', 'area_type', 'module_name'] as $field) {
            if (!empty($filters[$field])) {
                $sql .= " AND `{$field}` = :{$field}";
                $params[$field] = $this->sanitizer->sanitizeScalar($filters[$field], 120);
            }
        }

        foreach (['site_id', 'business_id', 'user_id'] as $field) {
            $value = $this->toNullableInt($filters[$field] ?? null);

            if ($value !== null) {
                $sql .= " AND `{$field}` = :{$field}";
                $params[$field] = $value;
            }
        }

        $sql .= " ORDER BY `id` DESC LIMIT 200";

        return $this->success('Bug reports loaded.', $this->WIdb->select($sql, $params));
    }

    public function getReportThread(int $reportId): array
    {
        $report = $this->getReportById($reportId);

        if (($report['status'] ?? '') !== 'success') {
            return $report;
        }

        $messages = $this->getReportMessages($reportId);

        $data = is_array($report['data'] ?? null) ? $report['data'] : [];
        $data['messages'] = is_array($messages['data'] ?? null) ? $messages['data'] : [];

        return $this->success('Bug report loaded.', $data);
    }

    public function getReportById(int $reportId): array
    {
        if ($reportId <= 0 || !$this->tableExists($this->reportTable)) {
            return $this->error('Invalid report id supplied.');
        }

        $rows = $this->WIdb->select(
            "SELECT * FROM `{$this->reportTable}` WHERE `id` = :id LIMIT 1",
            ['id' => $reportId]
        );

        if ($rows === []) {
            return $this->error('Report not found.');
        }

        return $this->success('Bug report loaded.', $rows[0]);
    }

    public function getReportMessages(int $reportId): array
    {
        if ($reportId <= 0 || !$this->tableExists($this->messageTable)) {
            return $this->success('No messages found.', []);
        }

        $messages = $this->WIdb->select(
            "SELECT * FROM `{$this->messageTable}` WHERE `report_id` = :report_id ORDER BY `id` ASC",
            ['report_id' => $reportId]
        );

        return $this->success('Bug report messages loaded.', $messages);
    }

    public function replyToReport(int $reportId, string $message, ?int $userId = null): array
    {
        if ($reportId <= 0) {
            return $this->error('Invalid report id supplied.');
        }

        $message = $this->sanitizer->sanitizeDescription($message);

        if ($message === '') {
            return $this->error('Reply message is required.');
        }

        if (!$this->addMessage($reportId, 'comment', $message, $userId)) {
            return $this->error('Unable to save reply.');
        }

        return $this->success('Reply saved.', [
            'report_id' => $reportId,
        ]);
    }

    public function updateReportStatus(int $reportId, string $status, ?int $userId = null): array
    {
        if ($reportId <= 0) {
            return $this->error('Invalid report id supplied.');
        }

        $status = strtolower(trim($status));
        $allowed = ['open', 'in_progress', 'resolved', 'closed'];

        if (!in_array($status, $allowed, true)) {
            return $this->error('Invalid bug report status.');
        }

        if (!$this->tableExists($this->reportTable)) {
            return $this->error('Bug report table is missing.');
        }

        $ok = $this->WIdb->update(
            $this->reportTable,
            ['status' => $status],
            '`id` = :id',
            ['id' => $reportId]
        );

        if (!$ok) {
            return $this->error('Unable to update report status.');
        }

        $this->addMessage(
            $reportId,
            'status_update',
            'Status changed to ' . str_replace('_', ' ', $status) . '.',
            $userId
        );

        return $this->success('Status updated.', [
            'report_id' => $reportId,
            'status' => $status,
        ]);
    }

    public function addMessage(int $reportId, string $messageType, string $message, ?int $userId = null): bool
    {
        if ($reportId <= 0 || trim($message) === '' || !$this->tableExists($this->messageTable)) {
            return false;
        }

        $allowedTypes = ['report', 'comment', 'status_update', 'internal_note'];

        if (!in_array($messageType, $allowedTypes, true)) {
            $messageType = 'comment';
        }

        $insert = $this->filterDataForTable($this->messageTable, [
            'report_id'    => $reportId,
            'user_id'      => $userId,
            'message_type' => $messageType,
            'message'      => $this->sanitizer->sanitizeDescription($message),
        ]);

        return $insert !== [] && $this->WIdb->insert($this->messageTable, $insert);
    }

    /**
     * Forward a report if WILabs forwarding is enabled in module settings.
     * Local report storage always happens first, regardless of forwarding.
     *
     * @return array{success:bool,status:string,message:string,forwarded?:bool}
     */
    public function forwardReportIfEnabled(int $reportId): array
    {
        if (!$this->loadForwarderClass()) {
            return [
                'success' => true,
                'status' => 'disabled',
                'message' => 'WILabs forwarding service is not available.',
                'forwarded' => false,
            ];
        }

        $report = $this->getReportById($reportId);

        if (($report['status'] ?? '') !== 'success') {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Report could not be loaded for forwarding.',
                'forwarded' => false,
            ];
        }

        $messages = $this->getReportMessages($reportId);

        try {
            $forwarder = new WIBugReportForwarder();
            return $forwarder->forwardIfEnabled(
                $reportId,
                is_array($report['data'] ?? null) ? $report['data'] : [],
                is_array($messages['data'] ?? null) ? $messages['data'] : []
            );
        } catch (Throwable $e) {
            error_log('WIBugReporter forwarding failed: ' . $e->getMessage());

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Unable to forward report.',
                'forwarded' => false,
            ];
        }
    }

    /**
     * Manually retry forwarding from the admin report screen.
     *
     * @return array{success:bool,status:string,message:string,forwarded?:bool}
     */
    public function forwardReportNow(int $reportId): array
    {
        if (!$this->loadForwarderClass()) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'WILabs forwarding service is not available.',
                'forwarded' => false,
            ];
        }

        $report = $this->getReportById($reportId);

        if (($report['status'] ?? '') !== 'success') {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Report could not be loaded for forwarding.',
                'forwarded' => false,
            ];
        }

        $messages = $this->getReportMessages($reportId);

        try {
            $forwarder = new WIBugReportForwarder();
            return $forwarder->forwardNow(
                $reportId,
                is_array($report['data'] ?? null) ? $report['data'] : [],
                is_array($messages['data'] ?? null) ? $messages['data'] : []
            );
        } catch (Throwable $e) {
            error_log('WIBugReporter manual forwarding failed: ' . $e->getMessage());

            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Unable to forward report.',
                'forwarded' => false,
            ];
        }
    }

    private function loadForwarderClass(): bool
    {
        if (class_exists('WIBugReportForwarder')) {
            return true;
        }

        $file = __DIR__ . '/WIBugReportForwarder.php';

        if (is_file($file)) {
            require_once $file;
        }

        return class_exists('WIBugReportForwarder');
    }

    /**
     * @param array<string,mixed> $options
     */
    public function renderWidget(array $options = []): string
    {
        $context = $this->getContextSnapshot();

        $config = [
            'ajax_url'     => (string) ($options['ajax_url'] ?? $this->defaultAjaxUrl()),
            'area_type'    => (string) ($options['area_type'] ?? $context['area_type']),
            'module_name'  => (string) ($options['module_name'] ?? $context['module_name']),
            'page_key'     => (string) ($options['page_key'] ?? $context['page_key']),
            'page_url'     => (string) ($options['page_url'] ?? $context['page_url']),
            'action_name'  => (string) ($options['action_name'] ?? ''),
            'business_id'  => (string) ($options['business_id'] ?? $context['business_id']),
            'site_id'      => (string) ($options['site_id'] ?? $context['site_id']),
            'user_id'      => (string) ($context['user_id'] ?? ''),
            'user_role_id' => (string) ($context['user_role_id'] ?? ''),
            'browser'      => (string) ($context['browser'] ?? ''),
            'user_agent'   => (string) ($context['user_agent'] ?? ''),
        ];

        $e = static fn(mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');

        return '
<div
    id="wiBugReporterRoot"
    class="wi-bug-reporter-root"
    data-ajax-url="' . $e($config['ajax_url']) . '"
    data-area-type="' . $e($config['area_type']) . '"
    data-module-name="' . $e($config['module_name']) . '"
    data-page-key="' . $e($config['page_key']) . '"
    data-action-name="' . $e($config['action_name']) . '"
    data-business-id="' . $e($config['business_id']) . '"
    data-site-id="' . $e($config['site_id']) . '"
    data-user-id="' . $e($config['user_id']) . '"
    data-user-role-id="' . $e($config['user_role_id']) . '"
    data-browser="' . $e($config['browser']) . '"
    data-user-agent="' . $e($config['user_agent']) . '"
>
    <button type="button" class="wi-bug-reporter-toggle" aria-label="Report a bug" title="Report a bug">
        <span class="wi-bug-reporter-toggle-icon">🐞</span>
    </button>

    <aside class="wi-bug-reporter-panel" aria-hidden="true">
        <div class="wi-bug-reporter-header">
            <h3>Report a problem</h3>
            <button type="button" class="wi-bug-reporter-close" aria-label="Close bug reporter">×</button>
        </div>

        <form class="wi-bug-reporter-form" novalidate>
            <input type="hidden" name="action" value="create_bug_report">
            <input type="hidden" name="area_type" value="' . $e($config['area_type']) . '">
            <input type="hidden" name="module_name" value="' . $e($config['module_name']) . '">
            <input type="hidden" name="page_key" value="' . $e($config['page_key']) . '">
            <input type="hidden" name="page_url" value="' . $e($config['page_url']) . '">
            <input type="hidden" name="action_name" value="' . $e($config['action_name']) . '">
            <input type="hidden" name="business_id" value="' . $e($config['business_id']) . '">
            <input type="hidden" name="site_id" value="' . $e($config['site_id']) . '">
            <input type="hidden" name="user_id" value="' . $e($config['user_id']) . '">
            <input type="hidden" name="user_role_id" value="' . $e($config['user_role_id']) . '">
            <input type="hidden" name="browser" value="' . $e($config['browser']) . '">
            <input type="hidden" name="user_agent" value="' . $e($config['user_agent']) . '">

            <div class="wi-bug-reporter-field">
                <label for="wiBugReporterTitle">Short title</label>
                <input type="text" id="wiBugReporterTitle" name="title" maxlength="255" required>
            </div>

            <div class="wi-bug-reporter-field">
                <label for="wiBugReporterSeverity">Severity</label>
                <select id="wiBugReporterSeverity" name="severity" required>
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                    <option value="critical">Critical</option>
                </select>
            </div>

            <div class="wi-bug-reporter-field">
                <label for="wiBugReporterDescription">What is not working?</label>
                <textarea id="wiBugReporterDescription" name="description" rows="6" required></textarea>
                <small>Do not include passwords, payment details, private documents or personal customer information.</small>
            </div>

            <div class="wi-bug-reporter-actions">
                <button type="submit" class="wi-bug-reporter-submit">Submit report</button>
            </div>

            <div class="wi-bug-reporter-feedback" aria-live="polite"></div>
        </form>
    </aside>
</div>';
    }

    /**
     * @return array<string,string|int|null>
     */
    public function getContextSnapshot(): array
    {
        $userAgent = (string) ($_SERVER['HTTP_USER_AGENT'] ?? '');

        return [
            'business_id'  => $this->getSessionInt(['business_id', 'org_business_id', 'current_business_id']),
            'site_id'      => $this->getSessionInt(['site_id', 'staff_site_id', 'current_site_id', 'compliance_site_id']),
            'user_id'      => $this->getSessionInt(['user_id', 'id']),
            'user_role_id' => $this->getSessionInt(['user_role_id', 'role_id', 'staff_role_id']),
            'area_type'    => $this->guessAreaType(),
            'module_name'  => $this->guessModuleName(),
            'page_key'     => $this->guessPageKey(),
            'page_url'     => $this->buildCurrentUrl(),
            'action_name'  => '',
            'browser'      => $this->detectBrowser($userAgent),
            'user_agent'   => $userAgent,
            'ip_address'   => $this->getClientIp(),
        ];
    }

    private function isReady(): bool
    {
        return $this->tableExists($this->reportTable) && $this->tableExists($this->messageTable);
    }

    /**
     * @param array<string,mixed> $payload
     * @param array<string,mixed> $context
     * @return array<string,mixed>
     */
    private function normaliseReportPayload(array $payload, array $context): array
    {
        $severity = strtolower(trim((string) ($payload['severity'] ?? 'medium')));

        if (!in_array($severity, ['low', 'medium', 'high', 'critical'], true)) {
            $severity = 'medium';
        }

        return [
            'business_id'  => $this->toNullableInt($payload['business_id'] ?? $context['business_id'] ?? null),
            'site_id'      => $this->toNullableInt($payload['site_id'] ?? $context['site_id'] ?? null),
            'user_id'      => $this->toNullableInt($context['user_id'] ?? $payload['user_id'] ?? null),
            'user_role_id' => $this->toNullableInt($context['user_role_id'] ?? $payload['user_role_id'] ?? null),
            'area_type'    => $this->sanitizer->sanitizeScalar($payload['area_type'] ?? $context['area_type'] ?? 'admin', 80),
            'module_name'  => $this->sanitizer->sanitizeScalar($payload['module_name'] ?? $context['module_name'] ?? '', 150),
            'page_key'     => $this->sanitizer->sanitizeScalar($payload['page_key'] ?? $context['page_key'] ?? '', 150),
            'page_url'     => $this->sanitizer->sanitizeUrl($payload['page_url'] ?? $context['page_url'] ?? '', 500),
            'action_name'  => $this->sanitizer->sanitizeScalar($payload['action_name'] ?? $context['action_name'] ?? '', 150),
            'severity'     => $severity,
            'title'        => $this->sanitizer->sanitizeTitle($payload['title'] ?? ''),
            'description'  => $this->sanitizer->sanitizeDescription($payload['description'] ?? ''),
            'browser'      => $this->sanitizer->sanitizeScalar($context['browser'] ?? '', 255),
            'user_agent'   => $this->sanitizer->sanitizeScalar($context['user_agent'] ?? '', 1000),
            'ip_address'   => $this->sanitizer->sanitizeScalar($context['ip_address'] ?? '', 64),
        ];
    }

    /**
     * @param array<string,mixed> $payload
     * @return array<string,mixed>|null
     */
    private function validateReportPayload(array $payload): ?array
    {
        $errors = [];

        if ((string) ($payload['title'] ?? '') === '') {
            $errors[] = ['id' => 'title', 'msg' => 'Title is required.'];
        }

        if ((string) ($payload['description'] ?? '') === '') {
            $errors[] = ['id' => 'description', 'msg' => 'Description is required.'];
        }

        if ($errors !== []) {
            return [
                'success' => false,
                'status'  => 'error',
                'message' => 'Please complete the required fields.',
                'errors'  => $errors,
            ];
        }

        return null;
    }

    /**
     * @param string $table
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function filterDataForTable(string $table, array $data): array
    {
        $columns = $this->getColumns($table);
        $filtered = [];

        foreach ($data as $column => $value) {
            if (in_array((string) $column, $columns, true)) {
                $filtered[(string) $column] = $value;
            }
        }

        return $filtered;
    }

    /**
     * @return array<int,string>
     */
    private function getColumns(string $table): array
    {
        if (isset($this->columnCache[$table])) {
            return $this->columnCache[$table];
        }

        if (!$this->isSafeIdentifier($table)) {
            return [];
        }

        $rows = $this->WIdb->select(
            "SELECT COLUMN_NAME
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name",
            ['table_name' => $table]
        );

        $columns = [];

        foreach ($rows as $row) {
            $column = (string) ($row['COLUMN_NAME'] ?? '');

            if ($column !== '') {
                $columns[] = $column;
            }
        }

        $this->columnCache[$table] = $columns;

        return $columns;
    }

    private function tableExists(string $table): bool
    {
        if (!$this->isSafeIdentifier($table)) {
            return false;
        }

        $rows = $this->WIdb->select(
            "SELECT COUNT(*) AS count_value
             FROM INFORMATION_SCHEMA.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name",
            ['table_name' => $table]
        );

        return (int) ($rows[0]['count_value'] ?? 0) > 0;
    }

    private function isSafeIdentifier(string $value): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_]+$/', $value);
    }

    /**
     * @param array<int,string> $keys
     */
    private function getSessionInt(array $keys): ?int
    {
        foreach ($keys as $key) {
            try {
                $value = class_exists('WISession') ? WISession::get($key, null) : ($_SESSION[$key] ?? null);
            } catch (Throwable $e) {
                $value = $_SESSION[$key] ?? null;
            }

            $int = $this->toNullableInt($value);

            if ($int !== null && $int > 0) {
                return $int;
            }
        }

        return null;
    }

    private function toNullableInt(mixed $value): ?int
    {
        if ($value === null || $value === '' || is_array($value) || is_object($value)) {
            return null;
        }

        $int = (int) $value;

        return $int > 0 ? $int : null;
    }

    private function buildCurrentUrl(): string
    {
        $scheme = (!empty($_SERVER['HTTPS']) && strtolower((string) $_SERVER['HTTPS']) !== 'off') ? 'https' : 'http';
        $host = (string) ($_SERVER['HTTP_HOST'] ?? '');
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');

        return $host !== '' ? $scheme . '://' . $host . $uri : $uri;
    }

    private function defaultAjaxUrl(): string
    {
        return '/WIAdmin/WICore/WIClass/WIAjax.php';
    }

    private function guessAreaType(): string
    {
        $uri = strtolower((string) ($_SERVER['REQUEST_URI'] ?? ''));

        if (str_contains($uri, '/wicompliance/wiadmin')) {
            return 'compliance_admin';
        }

        if (str_contains($uri, '/wicompliance/')) {
            return 'compliance_worker';
        }

        if (str_contains($uri, '/wimembers/')) {
            return 'member';
        }

        if (str_contains($uri, '/wiadmin/')) {
            return 'admin';
        }

        return 'public';
    }

    private function guessModuleName(): string
    {
        $uri = strtolower((string) ($_SERVER['REQUEST_URI'] ?? ''));

        return match (true) {
            str_contains($uri, 'wicompliance') => 'WICompliance',
            str_contains($uri, 'wimembers') => 'WIMembers',
            str_contains($uri, 'wiorg') => 'WIOrg',
            str_contains($uri, 'wihr') => 'WIHR',
            str_contains($uri, 'wilabs') => 'WILabs',
            str_contains($uri, 'wiadmin') => 'WIAdmin',
            default => 'WICMS',
        };
    }

    private function guessPageKey(): string
    {
        $uri = trim((string) ($_SERVER['REQUEST_URI'] ?? ''), '/');

        if ($uri === '') {
            return 'home';
        }

        $parts = explode('/', $uri);
        $last = end($parts);

        if ($last === false || $last === '') {
            return 'dashboard';
        }

        return preg_replace('/[^A-Za-z0-9_\-]/', '', pathinfo($last, PATHINFO_FILENAME)) ?: 'dashboard';
    }

    private function detectBrowser(string $userAgent): string
    {
        $ua = strtolower($userAgent);

        return match (true) {
            str_contains($ua, 'edg') => 'Edge',
            str_contains($ua, 'chrome') => 'Chrome',
            str_contains($ua, 'firefox') => 'Firefox',
            str_contains($ua, 'safari') && !str_contains($ua, 'chrome') => 'Safari',
            str_contains($ua, 'opera') || str_contains($ua, 'opr/') => 'Opera',
            default => 'Unknown',
        };
    }

    private function getClientIp(): string
    {
        $keys = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_CLIENT_IP',
            'REMOTE_ADDR',
        ];

        foreach ($keys as $key) {
            $value = trim((string) ($_SERVER[$key] ?? ''));

            if ($value === '') {
                continue;
            }

            if (str_contains($value, ',')) {
                $value = trim((string) explode(',', $value)[0]);
            }

            return $value;
        }

        return '';
    }

    /**
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    private function success(string $message, array $data = []): array
    {
        return [
            'success' => true,
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ];
    }

    private function error(string $message): array
    {
        return [
            'success' => false,
            'status'  => 'error',
            'message' => $message,
            'errors'  => [],
        ];
    }
}
