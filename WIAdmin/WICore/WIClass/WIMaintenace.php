<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIMaintenace
| File: WIMaintenace.php
| Location: /WIAdmin/WICore/WIClass/WIMaintenace.php
| Type: Maintenance / Activity Log Service
| Layer: Shared Core
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Canonical maintenance, notification, and lightweight tracking class.
|
| Notes:
| - Class name intentionally kept as WIMaintenace for compatibility
| - Uses WIdb only
| - Safely adapts to whichever optional columns are present
|--------------------------------------------------------------------------
*/

final class WIMaintenace
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function LogFunction(string|int $user, string $operation): void
    {
        $this->insertLog('wi_logs', (string) $user, $operation);
    }

    public function Notifications(string|int $user, string $operation): void
    {
        $this->insertLog('wi_notifications', (string) $user, $operation);
    }

    public function UniqueVisitors(): array
    {
        if (!$this->WIdb->tableExists('wi_track')) {
            return [];
        }

        return $this->WIdb->select(
            "SELECT *
             FROM `wi_track`
             ORDER BY `date` DESC, `id` DESC",
            []
        );
    }

    public function WILogs(): void
    {
        $logs = $this->getLogs();

        if ($logs === []) {
            echo '<div class="divTable"><div class="divTableBody"><div class="divTableRow"><div class="divTableCell">No results to show.</div></div></div></div>';
            return;
        }

        echo '<div class="divTable">';
        echo '<div class="divTableBody">';

        foreach ($logs as $log) {
            echo '<div class="divTableRow">';
            echo '<div class="divTableCell">' . $this->e($log['date'] ?? '') . '</div>';
            echo '<div class="divTableCell">' . $this->e($log['user'] ?? '') . '</div>';
            echo '<div class="divTableCell">' . $this->e($log['opperation'] ?? '') . '</div>';
            echo '</div>';
        }

        echo '</div>';
        echo '</div>';
    }

    public function getLogs(int $limit = 100): array
    {
        if (!$this->WIdb->tableExists('wi_logs')) {
            return [];
        }

        $limit = max(1, min($limit, 500));

        return $this->WIdb->select(
            "SELECT `id`, `date`, `user`, `opperation`
             FROM `wi_logs`
             ORDER BY `date` DESC, `id` DESC
             LIMIT {$limit}",
            []
        );
    }

    public function getNotifications(int $limit = 100): array
    {
        if (!$this->WIdb->tableExists('wi_notifications')) {
            return [];
        }

        $limit = max(1, min($limit, 500));

        return $this->WIdb->select(
            "SELECT `id`, `date`, `user`, `opperation`
             FROM `wi_notifications`
             ORDER BY `date` DESC, `id` DESC
             LIMIT {$limit}",
            []
        );
    }

    public function trackVisitor(?int $userId = null, ?string $username = null): void
    {
        if (!$this->WIdb->tableExists('wi_track')) {
            return;
        }

        $payload = [
            'date'       => date('Y-m-d H:i:s'),
            'ip'         => $this->clientIp(),
            'user_agent' => $this->userAgent(),
            'page'       => $this->currentPage(),
        ];

        if ($userId !== null && $this->WIdb->columnExists('wi_track', 'user_id')) {
            $payload['user_id'] = $userId;
        }

        if ($username !== null && $this->WIdb->columnExists('wi_track', 'user')) {
            $payload['user'] = trim($username);
        }

        $allowedPayload = $this->filterPayloadByTableColumns('wi_track', $payload);

        if ($allowedPayload !== []) {
            $this->WIdb->insert('wi_track', $allowedPayload);
        }
    }

    public function logComplianceActivity(string $message, array $context = []): void
    {
        $userId = (string) (class_exists('WISession') ? WISession::get('user_id', '0') : '0');

        $this->Notifications($userId, $message);

        if (class_exists('WILogger')) {
            WILogger::info($message, $context, 'compliance');
        }
    }

    public function logSecurityEvent(string $message, array $context = []): void
    {
        $userId = (string) (class_exists('WISession') ? WISession::get('user_id', '0') : '0');

        $this->LogFunction($userId, $message);

        if (class_exists('WILogger')) {
            WILogger::security($message, $context, 'security');
        }
    }

    private function insertLog(string $table, string $user, string $operation): void
    {
        if (!$this->WIdb->tableExists($table)) {
            return;
        }

        $payload = [
            'date'       => date('Y-m-d H:i:s'),
            'user'       => trim($user),
            'opperation' => trim($operation),
        ];

        if ($this->WIdb->columnExists($table, 'ip_addr')) {
            $payload['ip_addr'] = $this->clientIp();
        }

        if ($this->WIdb->columnExists($table, 'user_agent')) {
            $payload['user_agent'] = $this->userAgent();
        }

        if ($this->WIdb->columnExists($table, 'page')) {
            $payload['page'] = $this->currentPage();
        }

        $allowedPayload = $this->filterPayloadByTableColumns($table, $payload);

        if ($allowedPayload !== []) {
            $this->WIdb->insert($table, $allowedPayload);
        }
    }

    private function filterPayloadByTableColumns(string $table, array $payload): array
    {
        $filtered = [];

        foreach ($payload as $column => $value) {
            if ($this->WIdb->columnExists($table, (string) $column)) {
                $filtered[$column] = $value;
            }
        }

        return $filtered;
    }

    private function clientIp(): string
    {
        return trim((string) ($_SERVER['REMOTE_ADDR'] ?? ''));
    }

    private function userAgent(): string
    {
        return trim((string) ($_SERVER['HTTP_USER_AGENT'] ?? ''));
    }

    private function currentPage(): string
    {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim((string) $_SERVER['REQUEST_URI']);
        }

        if (!empty($_GET['page'])) {
            return trim((string) $_GET['page']);
        }

        return '';
    }

    private function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}