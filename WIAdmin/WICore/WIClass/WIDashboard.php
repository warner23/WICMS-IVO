<?php

declare(strict_types=1);

/**
* WIDashboard Class
* Created by Warner Infinity
* Author Jules Warner
*/

class WIDashboard
{
    protected $WIdb;
    protected mysqli $db;

    /**
     * Adjust these if your real table names differ.
     */
    private array $tables = [
        'users' => 'wi_users',
        'pages' => 'wi_pages',
        'media' => 'wi_media',
        'roles' => 'wi_roles',
        'modules' => 'wi_modules',
        'plugins' => 'wi_plugins',
        'notifications' => 'wi_notifications',
        'visitors' => 'wi_website_statistics',
    ];

    /**
     * Base paths for install-aware dashboard checks.
     * Update these if your real folder layout differs.
     */
    private array $paths = [
        'modules_dir' => __DIR__ . '/../../../../WIModules',
        'plugins_dir' => __DIR__ . '/../../../../WIPlugins',
        'root_dir'    => __DIR__ . '/../../../../',
        'admin_dir'   => __DIR__ . '/../../../',
    ];

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->db = $this->WIdb->mysqli;
    }

    /* =========================================================
     * CORE SAFE HELPERS
     * ========================================================= */

    private function tableExists(string $table): bool
    {
        if ($table === '') {
            return false;
        }

        $table = $this->db->real_escape_string($table);
        $sql = "SHOW TABLES LIKE '{$table}'";
        $result = $this->db->query($sql);

        if (!$result instanceof mysqli_result) {
            return false;
        }

        $exists = $result->num_rows > 0;
        $result->free();

        return $exists;
    }

    private function columnExists(string $table, string $column): bool
    {
        if ($table === '' || $column === '' || !$this->tableExists($table)) {
            return false;
        }

        $table = $this->db->real_escape_string($table);
        $column = $this->db->real_escape_string($column);

        $sql = "SHOW COLUMNS FROM `{$table}` LIKE '{$column}'";
        $result = $this->db->query($sql);

        if (!$result instanceof mysqli_result) {
            return false;
        }

        $exists = $result->num_rows > 0;
        $result->free();

        return $exists;
    }

    private function fetchValue(string $sql, string $types = '', array $params = []): mixed
    {
        $stmt = $this->db->prepare($sql);

        if (!$stmt instanceof mysqli_stmt) {
            return null;
        }

        if ($types !== '' && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            $stmt->close();
            return null;
        }

        $result = $stmt->get_result();

        if (!$result instanceof mysqli_result) {
            $stmt->close();
            return null;
        }

        $row = $result->fetch_row();
        $result->free();
        $stmt->close();

        return $row[0] ?? null;
    }

    private function fetchAssocAll(string $sql, string $types = '', array $params = []): array
    {
        $stmt = $this->db->prepare($sql);

        if (!$stmt instanceof mysqli_stmt) {
            return [];
        }

        if ($types !== '' && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        if (!$stmt->execute()) {
            $stmt->close();
            return [];
        }

        $result = $stmt->get_result();

        if (!$result instanceof mysqli_result) {
            $stmt->close();
            return [];
        }

        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }

        $result->free();
        $stmt->close();

        return $rows;
    }

    private function countRows(string $table, ?string $where = null): int
    {
        if (!$this->tableExists($table)) {
            return 0;
        }

        $sql = "SELECT COUNT(*) FROM `{$table}`";
        if ($where !== null && trim($where) !== '') {
            $sql .= " WHERE {$where}";
        }

        $value = $this->fetchValue($sql);

        return is_numeric($value) ? (int) $value : 0;
    }

    private function sumColumn(string $table, string $column, ?string $where = null): int|float
    {
        if (!$this->tableExists($table) || !$this->columnExists($table, $column)) {
            return 0;
        }

        $sql = "SELECT SUM(`{$column}`) FROM `{$table}`";
        if ($where !== null && trim($where) !== '') {
            $sql .= " WHERE {$where}";
        }

        $value = $this->fetchValue($sql);

        if ($value === null) {
            return 0;
        }

        return is_numeric($value) ? $value + 0 : 0;
    }

    private function pathExists(string $path): bool
    {
        return $path !== '' && file_exists($path);
    }

    private function dirExists(string $path): bool
    {
        return $path !== '' && is_dir($path);
    }

    private function fileExists(string $path): bool
    {
        return $path !== '' && is_file($path);
    }

    private function safeJsonFile(string $path): array
    {
        if (!$this->fileExists($path) || !is_readable($path)) {
            return [];
        }

        $json = file_get_contents($path);
        if ($json === false || trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function normaliseBoolean(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value === 1;
        }

        if (is_string($value)) {
            $value = strtolower(trim($value));
            return in_array($value, ['1', 'true', 'yes', 'on', 'enabled'], true);
        }

        return false;
    }

    private function countDirectories(string $basePath, array $ignore = []): int
    {
        if (!$this->dirExists($basePath)) {
            return 0;
        }

        $ignore = array_map('strtolower', $ignore);
        $items = scandir($basePath);

        if (!is_array($items)) {
            return 0;
        }

        $count = 0;

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (in_array(strtolower($item), $ignore, true)) {
                continue;
            }

            $fullPath = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . $item;

            if (is_dir($fullPath)) {
                $count++;
            }
        }

        return $count;
    }

    private function getDirectoryItems(string $basePath, array $ignore = []): array
    {
        if (!$this->dirExists($basePath)) {
            return [];
        }

        $ignore = array_map('strtolower', $ignore);
        $items = scandir($basePath);

        if (!is_array($items)) {
            return [];
        }

        $directories = [];

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (in_array(strtolower($item), $ignore, true)) {
                continue;
            }

            $fullPath = rtrim($basePath, '/\\') . DIRECTORY_SEPARATOR . $item;

            if (is_dir($fullPath)) {
                $directories[] = [
                    'name' => $item,
                    'path' => $fullPath,
                ];
            }
        }

        usort(
            $directories,
            static fn(array $a, array $b): int => strcasecmp($a['name'], $b['name'])
        );

        return $directories;
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        $value = $bytes / (1024 ** $power);

        return number_format($value, $power === 0 ? 0 : 2) . ' ' . $units[$power];
    }

    /* =========================================================
     * DATABASE STATS
     * ========================================================= */

    public function getTotalUsers(): int
    {
        return $this->countRows($this->tables['users']);
    }

    public function getTotalPages(): int
    {
        return $this->countRows($this->tables['pages']);
    }

    public function getTotalMedia(): int
    {
        return $this->countRows($this->tables['media']);
    }

    public function getTotalRoles(): int
    {
        return $this->countRows($this->tables['roles']);
    }

    public function getInstalledModulesCount(): int
    {
        if ($this->tableExists($this->tables['modules'])) {
            if ($this->columnExists($this->tables['modules'], 'installed')) {
                return $this->countRows($this->tables['modules'], "`installed` = 1");
            }

            return $this->countRows($this->tables['modules']);
        }

        return $this->countDirectories($this->paths['modules_dir'], ['.git', '.svn']);
    }

    public function getInstalledPluginsCount(): int
    {
        if ($this->tableExists($this->tables['plugins'])) {
            if ($this->columnExists($this->tables['plugins'], 'installed')) {
                return $this->countRows($this->tables['plugins'], "`installed` = 1");
            }

            return $this->countRows($this->tables['plugins']);
        }

        return $this->countDirectories($this->paths['plugins_dir'], ['.git', '.svn']);
    }

    public function getUnreadNotificationsCount(): int
    {
        $table = $this->tables['notifications'];

        if (!$this->tableExists($table)) {
            return 0;
        }

        if ($this->columnExists($table, 'viewed')) {
            return $this->countRows($table, "`viewed` = 0");
        }

        if ($this->columnExists($table, 'seen')) {
            return $this->countRows($table, "`seen` = 0");
        }

        if ($this->columnExists($table, 'read_status')) {
            return $this->countRows($table, "`read_status` = 0");
        }

        return 0;
    }

    /* =========================================================
     * INSTALL / FEATURE CHECKERS
     * ========================================================= */

    public function moduleExists(string $moduleName): bool
    {
        $moduleName = trim($moduleName);

        if ($moduleName === '') {
            return false;
        }

        $modulePath = rtrim($this->paths['modules_dir'], '/\\') . DIRECTORY_SEPARATOR . $moduleName;
        return $this->dirExists($modulePath);
    }

    public function pluginExists(string $pluginName): bool
    {
        $pluginName = trim($pluginName);

        if ($pluginName === '') {
            return false;
        }

        $pluginPath = rtrim($this->paths['plugins_dir'], '/\\') . DIRECTORY_SEPARATOR . $pluginName;
        return $this->dirExists($pluginPath);
    }

    public function getPluginCardData(string $name): array
    {
        $basePath = rtrim($this->paths['plugins_dir'], '/\\') . DIRECTORY_SEPARATOR . $name;
        $manifest = $this->safeJsonFile($basePath . DIRECTORY_SEPARATOR . 'plugin.json');
        $update = $this->safeJsonFile($basePath . DIRECTORY_SEPARATOR . 'update.json');

        return [
            'name' => $name,
            'type' => 'plugin',
            'installed' => $this->dirExists($basePath),
            'enabled' => $this->dirExists($basePath),
            'version' => $manifest['version'] ?? null,
            'latest_version' => $update['version'] ?? null,
            'title' => $manifest['name'] ?? $name,
            'description' => $manifest['description'] ?? '',
            'path' => $basePath,
            'manifest_exists' => !empty($manifest),
            'status' => $this->dirExists($basePath) ? 'installed' : 'not_installed',
        ];
    }

    public function getModuleCardData(string $name): array
    {
        $basePath = rtrim($this->paths['modules_dir'], '/\\') . DIRECTORY_SEPARATOR . $name;
        $manifest = $this->safeJsonFile($basePath . DIRECTORY_SEPARATOR . 'module.json');
        $update = $this->safeJsonFile($basePath . DIRECTORY_SEPARATOR . 'update.json');

        return [
            'name' => $name,
            'type' => 'module',
            'installed' => $this->dirExists($basePath),
            'enabled' => $this->dirExists($basePath),
            'version' => $manifest['version'] ?? null,
            'latest_version' => $update['version'] ?? null,
            'title' => $manifest['name'] ?? $name,
            'description' => $manifest['description'] ?? '',
            'path' => $basePath,
            'manifest_exists' => !empty($manifest),
            'status' => $this->dirExists($basePath) ? 'installed' : 'not_installed',
        ];
    }

    public function getInstalledPluginsSummary(): array
    {
        $items = [];

        if ($this->tableExists($this->tables['plugins'])) {
            $table = $this->tables['plugins'];
            $sql = "SELECT * FROM `{$table}` ORDER BY `id` DESC";
            $rows = $this->fetchAssocAll($sql);

            foreach ($rows as $row) {
                $name = $row['name'] ?? $row['plugin_name'] ?? $row['title'] ?? 'Plugin';
                $version = $row['version'] ?? null;
                $installed = $row['installed'] ?? 1;
                $enabled = $row['status'] ?? $row['enabled'] ?? 1;

                $items[] = [
                    'name' => (string) $name,
                    'title' => (string) ($row['title'] ?? $name),
                    'type' => 'plugin',
                    'installed' => $this->normaliseBoolean($installed),
                    'enabled' => $this->normaliseBoolean($enabled),
                    'version' => $version,
                    'description' => (string) ($row['description'] ?? ''),
                    'status' => $this->normaliseBoolean($installed) ? 'installed' : 'not_installed',
                ];
            }

            return $items;
        }

        foreach ($this->getDirectoryItems($this->paths['plugins_dir'], ['.git', '.svn']) as $plugin) {
            $items[] = $this->getPluginCardData($plugin['name']);
        }

        return $items;
    }

    public function getInstalledModulesSummary(): array
    {
        $items = [];

        if ($this->tableExists($this->tables['modules'])) {
            $table = $this->tables['modules'];
            $sql = "SELECT * FROM `{$table}` ORDER BY `id` DESC";
            $rows = $this->fetchAssocAll($sql);

            foreach ($rows as $row) {
                $name = $row['name'] ?? $row['module_name'] ?? $row['title'] ?? 'Module';
                $version = $row['version'] ?? null;
                $installed = $row['installed'] ?? 1;
                $enabled = $row['status'] ?? $row['enabled'] ?? 1;

                $items[] = [
                    'name' => (string) $name,
                    'title' => (string) ($row['title'] ?? $name),
                    'type' => 'module',
                    'installed' => $this->normaliseBoolean($installed),
                    'enabled' => $this->normaliseBoolean($enabled),
                    'version' => $version,
                    'description' => (string) ($row['description'] ?? ''),
                    'status' => $this->normaliseBoolean($installed) ? 'installed' : 'not_installed',
                ];
            }

            return $items;
        }

        foreach ($this->getDirectoryItems($this->paths['modules_dir'], ['.git', '.svn']) as $module) {
            $items[] = $this->getModuleCardData($module['name']);
        }

        return $items;
    }

    public function getAvailableExtensions(): array
    {
        $recommendedPlugins = [
            'WIBlog',
            'WIShop',
            'WIPOS',
            'WICompliance',
            'WIHospitality',
        ];

        $recommendedModules = [
            'Pages',
            'Media',
            'Users',
            'Roles',
            'Plugins',
            'Modules',
            'Settings',
        ];

        $available = [
            'plugins' => [],
            'modules' => [],
        ];

        foreach ($recommendedPlugins as $pluginName) {
            $available['plugins'][] = $this->getPluginCardData($pluginName);
        }

        foreach ($recommendedModules as $moduleName) {
            $available['modules'][] = $this->getModuleCardData($moduleName);
        }

        return $available;
    }

    /* =========================================================
     * SYSTEM HEALTH
     * ========================================================= */

    public function getSystemHealth(): array
    {
        $rootDir = realpath($this->paths['root_dir']) ?: $this->paths['root_dir'];
        $adminDir = realpath($this->paths['admin_dir']) ?: $this->paths['admin_dir'];

        $freeSpace = @disk_free_space($rootDir);
        $totalSpace = @disk_total_space($rootDir);

        $health = [
            'php_version' => PHP_VERSION,
            'php_ok' => version_compare(PHP_VERSION, '8.2.0', '>='),
            'database_connected' => $this->db->ping(),
            'modules_dir_exists' => $this->dirExists($this->paths['modules_dir']),
            'plugins_dir_exists' => $this->dirExists($this->paths['plugins_dir']),
            'root_writable' => is_writable($rootDir),
            'admin_writable' => is_writable($adminDir),
            'disk_free' => is_numeric($freeSpace) ? $this->formatBytes((int) $freeSpace) : 'Unknown',
            'disk_total' => is_numeric($totalSpace) ? $this->formatBytes((int) $totalSpace) : 'Unknown',
        ];

        return $health;
    }

    /* =========================================================
     * DASHBOARD PROVIDER
     * ========================================================= */

    public function getCoreStats(): array
    {
        return [
            'users' => $this->getTotalUsers(),
            'pages' => $this->getTotalPages(),
            'media' => $this->getTotalMedia(),
            'roles' => $this->getTotalRoles(),
            'modules' => $this->getInstalledModulesCount(),
            'plugins' => $this->getInstalledPluginsCount(),
            'notifications' => $this->getUnreadNotificationsCount(),
        ];
    }

    public function getDashboardData(): array
    {
        return [
            'stats' => $this->getCoreStats(),
            'modules' => $this->getInstalledModulesSummary(),
            'plugins' => $this->getInstalledPluginsSummary(),
            'available' => $this->getAvailableExtensions(),
            'health' => $this->getSystemHealth(),
        ];
    }

    /* =========================================================
     * COMPATIBILITY METHODS
     * These help keep the current dashboard alive until step 2.
     * ========================================================= */

    public function Info_Boxes(): array
    {
        $stats = $this->getCoreStats();

        return [
            [
                'title' => 'Users',
                'count' => $stats['users'],
                'icon' => 'fa fa-users',
                'class' => 'bg-primary',
            ],
            [
                'title' => 'Pages',
                'count' => $stats['pages'],
                'icon' => 'fa fa-file-text',
                'class' => 'bg-success',
            ],
            [
                'title' => 'Media',
                'count' => $stats['media'],
                'icon' => 'fa fa-image',
                'class' => 'bg-warning',
            ],
            [
                'title' => 'Modules',
                'count' => $stats['modules'],
                'icon' => 'fa fa-cubes',
                'class' => 'bg-info',
            ],
            [
                'title' => 'Plugins',
                'count' => $stats['plugins'],
                'icon' => 'fa fa-plug',
                'class' => 'bg-danger',
            ],
            [
                'title' => 'Notifications',
                'count' => $stats['notifications'],
                'icon' => 'fa fa-bell',
                'class' => 'bg-secondary',
            ],
        ];
    }

    public function toDoList(): array
    {
        return [
            [
                'title' => 'Rewrite dashboard view to use new provider methods',
                'completed' => false,
            ],
            [
                'title' => 'Remove legacy dashboard widgets not used in IVO',
                'completed' => false,
            ],
            [
                'title' => 'Add install-aware plugin and module cards',
                'completed' => false,
            ],
            [
                'title' => 'Verify media statistics against final media schema',
                'completed' => false,
            ],
        ];
    }

    public function Notifications(): array
    {
        $table = $this->tables['notifications'];

        if (!$this->tableExists($table)) {
            return [];
        }

        $orderColumn = $this->columnExists($table, 'id') ? 'id' : null;

        if ($orderColumn === null) {
            return [];
        }

        $sql = "SELECT * FROM `{$table}` ORDER BY `{$orderColumn}` DESC LIMIT 10";
        return $this->fetchAssocAll($sql);
    }

    public function WINotifications(): array
    {
        return $this->Notifications();
    }

    public function Visitors(): array
    {
        $table = $this->tables['visitors'];

        if (!$this->tableExists($table)) {
            return [
                'total' => 0,
                'today' => 0,
                'monthly' => 0,
            ];
        }

        $total = $this->countRows($table);
        $today = 0;
        $monthly = 0;

        if ($this->columnExists($table, 'date')) {
            $todaySql = "SELECT COUNT(*) FROM `{$table}` WHERE DATE(`date`) = CURDATE()";
            $monthSql = "SELECT COUNT(*) FROM `{$table}` WHERE YEAR(`date`) = YEAR(CURDATE()) AND MONTH(`date`) = MONTH(CURDATE())";

            $todayValue = $this->fetchValue($todaySql);
            $monthlyValue = $this->fetchValue($monthSql);

            $today = is_numeric($todayValue) ? (int) $todayValue : 0;
            $monthly = is_numeric($monthlyValue) ? (int) $monthlyValue : 0;
        }

        return [
            'total' => $total,
            'today' => $today,
            'monthly' => $monthly,
        ];
    }

    public function Map_visitors(): array
    {
        $table = $this->tables['visitors'];

        if (!$this->tableExists($table)) {
            return [];
        }

        if (!$this->columnExists($table, 'country')) {
            return [];
        }

        $sql = "SELECT `country`, COUNT(*) AS total
                FROM `{$table}`
                WHERE `country` IS NOT NULL AND `country` <> ''
                GROUP BY `country`
                ORDER BY total DESC
                LIMIT 10";

        return $this->fetchAssocAll($sql);
    }

    public function BounceRate(): float
    {
        /**
         * Keep this conservative for now.
         * Only calculate if the expected columns exist.
         */
        $table = $this->tables['visitors'];

        if (
            !$this->tableExists($table) ||
            !$this->columnExists($table, 'session_id') ||
            !$this->columnExists($table, 'page_url')
        ) {
            return 0.0;
        }

        $sql = "SELECT COUNT(*) FROM (
                    SELECT `session_id`
                    FROM `{$table}`
                    GROUP BY `session_id`
                    HAVING COUNT(DISTINCT `page_url`) = 1
                ) AS single_page_sessions";

        $singlePageSessions = $this->fetchValue($sql);
        $singlePageSessions = is_numeric($singlePageSessions) ? (int) $singlePageSessions : 0;

        $totalSessionsSql = "SELECT COUNT(DISTINCT `session_id`) FROM `{$table}`";
        $totalSessions = $this->fetchValue($totalSessionsSql);
        $totalSessions = is_numeric($totalSessions) ? (int) $totalSessions : 0;

        if ($totalSessions <= 0) {
            return 0.0;
        }

        return round(($singlePageSessions / $totalSessions) * 100, 2);
    }

    public function UniqueVisit(): int
    {
        $table = $this->tables['visitors'];

        if (!$this->tableExists($table)) {
            return 0;
        }

        if ($this->columnExists($table, 'session_id')) {
            $sql = "SELECT COUNT(DISTINCT `session_id`) FROM `{$table}`";
            $value = $this->fetchValue($sql);

            return is_numeric($value) ? (int) $value : 0;
        }

        if ($this->columnExists($table, 'ip')) {
            $sql = "SELECT COUNT(DISTINCT `ip`) FROM `{$table}`";
            $value = $this->fetchValue($sql);

            return is_numeric($value) ? (int) $value : 0;
        }

        return 0;
    }
}