<?php
declare(strict_types=1);

/**
 * File Information
 * ============================================================================
 * Written By:     Warner Infinity / WICMS
 * Company:        Warner Infinity
 * Product:        WICMS
 * Project:        WI Ecosystem
 * File:           WIPage.php
 * Location:       /WIAdmin/WICore/WIClass/WIPage.php
 * Type:           PHP Class
 * Layer:          Admin Core Service
 * Purpose Area:   Page records, route-file scaffolding and destination routing
 * Version:        3.1.0
 * Created:        Legacy
 * Last Updated:   2026-06-24
 * Status:         Production-ready cleanup
 * Summary:
 *   Core WICMS page manager service. Keeps page records and module assignment
 *   separate from module scaffolding, supports safe route creation in root,
 *   WICompliance or WIMembers, and exposes detected destination folders for the
 *   admin Pages manager. All database access stays through WIdb.
 * ============================================================================
 */
final class WIPage
{
    private WIdb $WIdb;

    /** @var array<string,array<string,bool>> */
    private array $tableColumnCache = [];

    /** @var array<string,string> */
    private array $reservedRootFolders = [
        'WIAdmin' => 'WIAdmin',
        'WICore' => 'WICore',
        'WIInc' => 'WIInc',
        'WIInstall' => 'WIInstall',
        'WITheme' => 'WITheme',
        'vendor' => 'vendor',
        'node_modules' => 'node_modules',
    ];

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function getPages(): array
    {
        return $this->WIdb->bindfree(
            "SELECT `id`, `name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`
             FROM `wi_page`
             ORDER BY `id` ASC"
        );
    }

    public function getPageById(int $id): ?array
    {
        if ($id <= 0) {
            return null;
        }

        $result = $this->WIdb->select(
            "SELECT `id`, `name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`
             FROM `wi_page`
             WHERE `id` = :id
             LIMIT 1",
            ['id' => $id]
        );

        return $result[0] ?? null;
    }

    public function getPageByName(string $name): ?array
    {
        $name = $this->normaliseSystemName($name);

        if ($name === '') {
            return null;
        }

        $result = $this->WIdb->select(
            "SELECT `id`, `name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`
             FROM `wi_page`
             WHERE `name` = :name
             LIMIT 1",
            ['name' => $name]
        );

        return $result[0] ?? null;
    }

    public function pageExists(string $name): bool
    {
        return $this->getPageByName($name) !== null;
    }

    public function pageNameExists(string $name, int $excludeId = 0): bool
    {
        $name = $this->normaliseSystemName($name);

        if ($name === '') {
            return false;
        }

        $sql = "SELECT `id`
                FROM `wi_page`
                WHERE LOWER(`name`) = :name";

        $params = ['name' => strtolower($name)];

        if ($excludeId > 0) {
            $sql .= " AND `id` != :id";
            $params['id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $result = $this->WIdb->select($sql, $params);

        return count($result) > 0;
    }

    /**
     * Save the page record and optionally create the physical route/assets.
     *
     * Destination only controls the physical PHP route location. The database
     * record remains in wi_page so the normal WICMS module mapping can still be
     * used by root-style pages. Compliance/member routes may still need their
     * own area-specific startup logic later if they are not root-style pages.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function savePage(array $data): array
    {
        $id             = isset($data['id']) ? (int) $data['id'] : 0;
        $rawName        = (string) ($data['name'] ?? '');
        $name           = $this->normaliseSystemName($rawName);
        $panel          = isset($data['panel']) ? (string) $data['panel'] : '0';
        $topHead        = isset($data['top_head']) ? (string) $data['top_head'] : '0';
        $header         = isset($data['header']) ? (string) $data['header'] : '0';
        $leftSidebar    = isset($data['left_sidebar']) ? (string) $data['left_sidebar'] : '0';
        $rightSidebar   = isset($data['right_sidebar']) ? (string) $data['right_sidebar'] : '0';
        $footer         = isset($data['footer']) ? (string) $data['footer'] : '0';
        $contents       = $this->normaliseSystemName((string) ($data['contents'] ?? ''));
        $destination    = $this->normaliseDestination((string) ($data['destination'] ?? 'root'));
        $createRoute    = $this->truthy($data['create_route'] ?? ($id === 0 ? '1' : '0'));
        $createDefaults = $this->truthy($data['create_defaults'] ?? ($id === 0 ? '1' : '0'));
        $overwriteRoute = $this->truthy($data['overwrite_route'] ?? '0');

        if ($contents === '') {
            $contents = 'notfound';
        }

        $errors = $this->validatePagePayload($name, $contents, $id, [
            'panel' => $panel,
            'top_head' => $topHead,
            'header' => $header,
            'left_sidebar' => $leftSidebar,
            'right_sidebar' => $rightSidebar,
            'footer' => $footer,
        ], $destination);

        if ($errors !== []) {
            return [
                'status' => 'error',
                'errors' => $errors,
            ];
        }

        $payload = [
            'name'          => $name,
            'panel'         => $panel,
            'top_head'      => $topHead,
            'header'        => $header,
            'left_sidebar'  => $leftSidebar,
            'right_sidebar' => $rightSidebar,
            'contents'      => $contents,
            'footer'        => $footer,
        ];

        $created = [];
        $warnings = [];

        try {
            if ($id > 0) {
                $this->WIdb->update(
                    'wi_page',
                    $payload,
                    '`id` = :id',
                    ['id' => $id]
                );
            } else {
                $this->WIdb->insert('wi_page', $payload);
                $id = (int) $this->WIdb->lastInsertId();
                $created[] = 'wi_page';
            }

            if ($createRoute) {
                $routeResult = $this->createRouteFile($name, $destination, $overwriteRoute);
                $created[] = $routeResult['created'] === true ? 'route_file' : 'route_file_existing';

                if (($routeResult['warning'] ?? '') !== '') {
                    $warnings[] = (string) $routeResult['warning'];
                }
            }

            if ($createDefaults) {
                $created = array_merge($created, $this->ensureDefaultCssRows($name));
                $created = array_merge($created, $this->ensureDefaultJsRows($name));
                $created = array_merge($created, $this->ensureDefaultMetaRows($name));
            }
        } catch (Throwable $e) {
            return [
                'status' => 'error',
                'msg'    => 'Page save failed: ' . $e->getMessage(),
            ];
        }

        return [
            'status'      => 'success',
            'msg'         => 'Page saved successfully.',
            'page_id'     => $id,
            'page'        => $name,
            'contents'    => $contents,
            'destination' => $destination,
            'created'     => array_values(array_unique($created)),
            'warnings'    => $warnings,
        ];
    }

    /**
     * Assign a page to an existing or planned module by updating wi_page.contents.
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function assignPageModule(array $data): array
    {
        $id = isset($data['id']) ? (int) $data['id'] : 0;
        $pageName = $this->normaliseSystemName((string) ($data['page'] ?? ''));
        $contents = $this->normaliseSystemName((string) ($data['contents'] ?? ''));

        if ($contents === '') {
            return [
                'status' => 'error',
                'errors' => [
                    ['id' => 'wicms-page-contents', 'msg' => 'Contents module is required.'],
                ],
            ];
        }

        $page = $id > 0 ? $this->getPageById($id) : $this->getPageByName($pageName);

        if ($page === null) {
            return [
                'status' => 'error',
                'msg'    => 'Page not found.',
            ];
        }

        $this->WIdb->update(
            'wi_page',
            ['contents' => $contents],
            '`id` = :id',
            ['id' => (int) $page['id']]
        );

        return [
            'status'   => 'success',
            'msg'      => 'Module assigned successfully.',
            'page_id'  => (int) $page['id'],
            'page'     => (string) $page['name'],
            'contents' => $contents,
        ];
    }

    public function deletePage(int $id): array
    {
        if ($id <= 0) {
            return [
                'status' => 'error',
                'msg'    => 'Invalid page id.',
            ];
        }

        $this->WIdb->delete(
            'wi_page',
            '`id` = :id',
            ['id' => $id]
        );

        return [
            'status' => 'success',
            'msg'    => 'Page deleted successfully. Physical route files are not deleted automatically.',
        ];
    }

    public function PageMod(string $page, string $column): mixed
    {
        if (!$this->isAllowedColumn($column)) {
            return null;
        }

        $row = $this->getPageByName($page);

        return $row[$column] ?? null;
    }

    public function PageModPower(string $page, string $column): int
    {
        $value = $this->PageMod($page, $column);

        if ($value === null || $value === '') {
            return 0;
        }

        if (is_numeric($value)) {
            return ((int) $value) > 0 ? 1 : 0;
        }

        return trim((string) $value) !== '' ? 1 : 0;
    }

    /**
     * Backward-compatible alias for older admin JS/actions.
     *
     * @return array<string,mixed>
     */
    public function newPage(string $pageName): array
    {
        return $this->savePage([
            'name' => $pageName,
            'contents' => 'notfound',
            'panel' => '1',
            'top_head' => '0',
            'header' => '0',
            'left_sidebar' => '0',
            'right_sidebar' => defined('RIGHT_SIDEBAR') && (int) RIGHT_SIDEBAR > 0 ? '1' : '0',
            'footer' => '1',
            'destination' => 'root',
            'create_route' => '1',
            'create_defaults' => '1',
            'overwrite_route' => '0',
        ]);
    }

    /**
     * @return array<int,string>
     */
    public function getAvailableContentModules(): array
    {
        $modules = ['notfound'];
        $pagesPath = $this->adminModulePagesPath();

        if (is_dir($pagesPath)) {
            $dirs = glob($pagesPath . '*', GLOB_ONLYDIR) ?: [];

            foreach ($dirs as $dir) {
                $module = basename($dir);
                $moduleFile = $dir . DIRECTORY_SEPARATOR . $module . '.php';

                if (is_file($moduleFile)) {
                    $modules[] = $module;
                }
            }
        }

        if ($this->tableExists('wi_mod') && $this->columnExists('wi_mod', 'module_name')) {
            $rows = $this->WIdb->select(
                "SELECT `module_name`
                 FROM `wi_mod`
                 WHERE `module_name` IS NOT NULL AND `module_name` != ''
                 ORDER BY `module_name` ASC"
            );

            foreach ($rows as $row) {
                $module = $this->normaliseSystemName((string) ($row['module_name'] ?? ''));

                if ($module !== '') {
                    $modules[] = $module;
                }
            }
        }

        $modules = array_values(array_unique(array_filter($modules)));
        natcasesort($modules);

        return array_values($modules);
    }

    /**
     * Return safe root-level destination folders the page manager may create a
     * route file inside. This intentionally excludes core/admin/theme folders.
     *
     * @return array<int,array<string,string|bool>>
     */
    public function getRouteDestinations(): array
    {
        $root = $this->publicRootPath();
        $destinations = [[
            'key' => 'root',
            'label' => 'Root / public site',
            'folder' => '/',
            'path' => $root,
            'exists' => is_dir($root),
            'writable' => is_writable($root),
            'recommended' => true,
        ]];

        $preferred = ['WICompliance', 'WIMembers'];
        foreach ($preferred as $folder) {
            $path = $root . DIRECTORY_SEPARATOR . $folder;
            if (!is_dir($path)) {
                continue;
            }

            $destinations[] = [
                'key' => $folder,
                'label' => $folder,
                'folder' => '/' . $folder,
                'path' => $path,
                'exists' => true,
                'writable' => is_writable($path),
                'recommended' => false,
            ];
        }

        $dirs = glob($root . DIRECTORY_SEPARATOR . 'WI*', GLOB_ONLYDIR) ?: [];
        foreach ($dirs as $dir) {
            $folder = basename($dir);
            if (isset($this->reservedRootFolders[$folder]) || in_array($folder, $preferred, true)) {
                continue;
            }

            if (!preg_match('/^WI[A-Za-z0-9_\-]+$/', $folder)) {
                continue;
            }

            $destinations[] = [
                'key' => $folder,
                'label' => $folder,
                'folder' => '/' . $folder,
                'path' => $dir,
                'exists' => true,
                'writable' => is_writable($dir),
                'recommended' => false,
            ];
        }

        return $destinations;
    }

    public function selectPage(): void
    {
        foreach ($this->getPages() as $value) {
            $name = htmlspecialchars((string) $value['name'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            echo '<option value="' . $name . '">' . $name . '</option>';
        }
    }

    private function createRouteFile(string $pageName, string $destination, bool $overwrite = false): array
    {
        $destination = $this->normaliseDestination($destination);
        $targetDir = $this->resolveDestinationPath($destination);
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $pageName . '.php';

        if (is_file($filePath) && !$overwrite) {
            return [
                'created' => false,
                'path' => $filePath,
                'warning' => 'Route file already existed and was not overwritten: ' . $this->displayRoutePath($destination, $pageName),
            ];
        }

        if (!is_dir($targetDir) || !is_writable($targetDir)) {
            throw new RuntimeException('Destination folder is not writable: ' . $targetDir);
        }

        $bytes = file_put_contents($filePath, $this->routeTemplate($pageName, $destination), LOCK_EX);

        if ($bytes === false) {
            throw new RuntimeException('Unable to write route file: ' . $filePath);
        }

        @chmod($filePath, 0644);

        return [
            'created' => true,
            'path' => $filePath,
            'warning' => '',
        ];
    }

    private function routeTemplate(string $pageName, string $destination): string
    {
        if ($destination === 'WICompliance') {
            return $this->areaRouteTemplate($pageName, 'WICompliance');
        }

        if ($destination === 'WIMembers') {
            return $this->areaRouteTemplate($pageName, 'WIMembers');
        }

        return $this->rootRouteTemplate($pageName);
    }

    private function rootRouteTemplate(string $pageName): string
    {
        $safePage = var_export($pageName, true);

        return <<<PHP
<?php
declare(strict_types=1);

require_once __DIR__ . '/WICore/WIClass/WI.php';

\$page = {$safePage};
\$startup = new WIStartUp();

\$startup->boot(\$page);
\$startup->header(\$page);

\$modules = new WIModules();
\$moduleName = \$modules->getModuleNameByPage(\$page) ?? 'notfound';
\$modules->getModMain(\$moduleName, \$page);

\$startup->footer();
PHP;
    }

    private function areaRouteTemplate(string $pageName, string $area): string
    {
        $safePage = var_export($pageName, true);
        $safeArea = var_export($area, true);

        return <<<PHP
<?php
declare(strict_types=1);

/*
 * WICMS generated {$area} route.
 * This file is intentionally thin. Area-specific business logic belongs in the
 * relevant module/engine, not in this route file.
 */

\$page = {$safePage};
\$area = {$safeArea};

\$localBootstrap = __DIR__ . '/WICore/WIClass/WI.php';
\$rootBootstrap = dirname(__DIR__) . '/WICore/WIClass/WI.php';

if (is_file(\$localBootstrap)) {
    require_once \$localBootstrap;
} elseif (is_file(\$rootBootstrap)) {
    require_once \$rootBootstrap;
} else {
    http_response_code(500);
    echo 'WICMS startup file not found.';
    exit;
}

\$startup = new WIStartUp();
\$startup->boot(\$page);
\$startup->header(\$page);

\$modules = new WIModules();
\$moduleName = \$modules->getModuleNameByPage(\$page) ?? 'notfound';
\$modules->getModMain(\$moduleName, \$page);

\$startup->footer();
PHP;
    }

    /**
     * @return array<int,string>
     */
    private function ensureDefaultCssRows(string $pageName): array
    {
        if (!$this->tableExists('wi_css')) {
            return [];
        }

        $created = [];
        $rows = [
            ['href' => 'site/css/frameworks/bootstrap4.css', 'rel' => 'stylesheet', 'page' => $pageName],
            ['href' => 'site/css/login_panel/css/slide.css', 'rel' => 'stylesheet', 'page' => $pageName],
            ['href' => 'site/css/frameworks/menus.css', 'rel' => 'stylesheet', 'page' => $pageName],
            ['href' => 'site/css/style.css', 'rel' => 'stylesheet', 'page' => $pageName],
            ['href' => 'site/css/font-awesome.css', 'rel' => 'stylesheet', 'page' => $pageName],
            ['href' => 'site/css/vendor/bootstrap.min.css', 'rel' => 'stylesheet', 'page' => $pageName],
        ];

        foreach ($rows as $row) {
            if ($this->cssRowExists((string) $row['href'], $pageName)) {
                continue;
            }

            $this->WIdb->insert('wi_css', $row);
            $created[] = 'wi_css:' . $row['href'];
        }

        return $created;
    }

    /**
     * @return array<int,string>
     */
    private function ensureDefaultJsRows(string $pageName): array
    {
        if (!$this->tableExists('wi_scripts')) {
            return [];
        }

        $created = [];
        $rows = [
            ['src' => 'site/js/frameworks/JQuery.js', 'page' => $pageName],
            ['src' => 'site/js/frameworks/bootstrap.js', 'page' => $pageName],
            ['src' => 'site/js/login_panel/js/slide.js', 'page' => $pageName],
        ];

        foreach ($rows as $row) {
            if ($this->jsRowExists((string) $row['src'], $pageName)) {
                continue;
            }

            $this->WIdb->insert('wi_scripts', $row);
            $created[] = 'wi_scripts:' . $row['src'];
        }

        return $created;
    }

    /**
     * @return array<int,string>
     */
    private function ensureDefaultMetaRows(string $pageName): array
    {
        if (!$this->tableExists('wi_meta')) {
            return [];
        }

        $created = [];
        $rows = [
            ['page' => $pageName, 'name' => 'viewport', 'content' => 'width=device-width, initial-scale=1', 'author' => 'WICMS'],
            ['page' => $pageName, 'name' => 'description', 'content' => 'WICMS page', 'author' => 'WICMS'],
            ['page' => $pageName, 'name' => 'keywords', 'content' => 'WICMS', 'author' => 'WICMS'],
            ['page' => $pageName, 'name' => 'author', 'content' => 'WICMS', 'author' => 'WICMS'],
        ];

        foreach ($rows as $row) {
            if ($this->metaRowExists((string) $row['name'], $pageName)) {
                continue;
            }

            $this->WIdb->insert('wi_meta', $row);
            $created[] = 'wi_meta:' . $row['name'];
        }

        return $created;
    }

    /**
     * @param array<string,string> $sectionFlags
     * @return array<int,array<string,string>>
     */
    private function validatePagePayload(string $name, string $contents, int $id, array $sectionFlags, string $destination): array
    {
        $errors = [];

        if ($name === '') {
            $errors[] = ['id' => 'wicms-page-name', 'msg' => 'Page name is required.'];
        }

        if ($name !== '' && !preg_match('/^[a-zA-Z0-9_\-]+$/', $name)) {
            $errors[] = ['id' => 'wicms-page-name', 'msg' => 'Use letters, numbers, underscores or hyphens only.'];
        }

        if ($contents === '') {
            $errors[] = ['id' => 'wicms-page-contents', 'msg' => 'Contents module is required.'];
        }

        if ($contents !== '' && !preg_match('/^[a-zA-Z0-9_\-]+$/', $contents)) {
            $errors[] = ['id' => 'wicms-page-contents', 'msg' => 'Use a valid module name only.'];
        }

        if ($this->pageNameExists($name, $id)) {
            $errors[] = ['id' => 'wicms-page-name', 'msg' => 'Page name already exists.'];
        }

        if (!$this->isAllowedDestination($destination)) {
            $errors[] = ['id' => 'wicms-page-destination', 'msg' => 'Invalid or unsafe destination folder.'];
        }

        foreach ($sectionFlags as $field => $value) {
            if (!in_array($value, ['0', '1'], true)) {
                $errors[] = [
                    'id' => 'wicms-page-' . str_replace('_', '-', $field),
                    'msg' => 'Invalid value supplied.',
                ];
            }
        }

        return $errors;
    }

    private function cssRowExists(string $href, string $pageName): bool
    {
        $result = $this->WIdb->select(
            "SELECT `id` FROM `wi_css` WHERE `href` = :href AND `page` = :page LIMIT 1",
            ['href' => $href, 'page' => $pageName]
        );

        return $result !== [];
    }

    private function jsRowExists(string $src, string $pageName): bool
    {
        $result = $this->WIdb->select(
            "SELECT `id` FROM `wi_scripts` WHERE `src` = :src AND `page` = :page LIMIT 1",
            ['src' => $src, 'page' => $pageName]
        );

        return $result !== [];
    }

    private function metaRowExists(string $name, string $pageName): bool
    {
        $result = $this->WIdb->select(
            "SELECT `meta_id` FROM `wi_meta` WHERE `name` = :name AND `page` = :page LIMIT 1",
            ['name' => $name, 'page' => $pageName]
        );

        return $result !== [];
    }

    private function isAllowedColumn(string $column): bool
    {
        return in_array(
            $column,
            ['id', 'name', 'panel', 'top_head', 'header', 'left_sidebar', 'right_sidebar', 'contents', 'footer'],
            true
        );
    }

    private function normaliseSystemName(string $name): string
    {
        $name = trim($name);
        $name = preg_replace('/\s+/', '_', $name) ?? $name;
        $name = preg_replace('/[^a-zA-Z0-9_\-]/', '', $name) ?? $name;

        return trim($name, '_-');
    }

    private function normaliseDestination(string $destination): string
    {
        $destination = trim($destination);
        $destination = trim($destination, " \/\\\t\n\r\0\x0B");

        if ($destination === '' || strtolower($destination) === 'root') {
            return 'root';
        }

        $destination = preg_replace('/[^A-Za-z0-9_\-]/', '', $destination) ?? '';

        return $destination !== '' ? $destination : 'root';
    }

    private function isAllowedDestination(string $destination): bool
    {
        if ($destination === 'root') {
            return true;
        }

        if (isset($this->reservedRootFolders[$destination])) {
            return false;
        }

        if (!preg_match('/^WI[A-Za-z0-9_\-]+$/', $destination)) {
            return false;
        }

        return is_dir($this->publicRootPath() . DIRECTORY_SEPARATOR . $destination);
    }

    private function resolveDestinationPath(string $destination): string
    {
        $destination = $this->normaliseDestination($destination);

        if (!$this->isAllowedDestination($destination)) {
            throw new RuntimeException('Invalid destination folder.');
        }

        if ($destination === 'root') {
            return $this->publicRootPath();
        }

        return $this->publicRootPath() . DIRECTORY_SEPARATOR . $destination;
    }

    private function displayRoutePath(string $destination, string $pageName): string
    {
        if ($destination === 'root') {
            return '/' . $pageName . '.php';
        }

        return '/' . $destination . '/' . $pageName . '.php';
    }

    private function truthy(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        $value = strtolower(trim((string) $value));

        return in_array($value, ['1', 'true', 'yes', 'on'], true);
    }

    private function publicRootPath(): string
    {
        return dirname(__DIR__, 3);
    }

    private function adminModulePagesPath(): string
    {
        return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'WIModule' . DIRECTORY_SEPARATOR . 'pages' . DIRECTORY_SEPARATOR;
    }

    private function tableExists(string $table): bool
    {
        $result = $this->WIdb->select(
            "SELECT COUNT(*) AS `total`
             FROM information_schema.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table",
            ['table' => $table]
        );

        return (int) ($result[0]['total'] ?? 0) > 0;
    }

    private function columnExists(string $table, string $column): bool
    {
        if (!isset($this->tableColumnCache[$table])) {
            $rows = $this->WIdb->select(
                "SELECT `COLUMN_NAME`
                 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = :table",
                ['table' => $table]
            );

            $this->tableColumnCache[$table] = [];

            foreach ($rows as $row) {
                $this->tableColumnCache[$table][(string) $row['COLUMN_NAME']] = true;
            }
        }

        return isset($this->tableColumnCache[$table][$column]);
    }
}
