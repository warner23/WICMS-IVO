<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WIProfile
| Project: WI Ecosystem
| File: WIModules.php
| Location: /WICore/WIClass/WIModules.php
| Type: PHP Class
| Layer: Front-Side Module Loader
| Purpose Area: Root page/module rendering and shared component rendering
| Version: 2.0.0
| Created: Legacy
| Last Updated: 2026-06-11
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Canonical front-side module loader for root pages. Supports the original
| WICMS class-name pattern and the newer WI*Module class pattern used by
| profile/member modules. Components are loaded from the central WIAdmin
| module/element tree, not from a separate WIMembers module tree.
*/

#[\AllowDynamicProperties]
class WIModules
{
    private WIdb $WIdb;
    private string $moduleRoot;
    private string $pagesPath;
    private string $modulesPath;
    private string $elementsPath;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->moduleRoot = ROOT_PATH . '/WIAdmin/WIModule';
        $this->pagesPath = $this->moduleRoot . '/pages';
        $this->modulesPath = $this->moduleRoot . '/modules';
        $this->elementsPath = $this->moduleRoot . '/elements';
    }

    public function getMod(string $mod, ?string $page = null): void
    {
        $this->renderModule($mod, $page ?? $this->currentPageName());
    }

    public function getModMain(string $mod, string $page, ?string $module = null): void
    {
        $targetModule = $module !== null && trim($module) !== '' ? $module : $mod;
        $this->renderPageModule($targetModule, $page);
    }

    public function renderComponent(string $component, array $payload = []): void
    {
        $component = $this->sanitizeModuleName($component);

        if ($component === '') {
            return;
        }

        $componentPage = isset($payload['page'])
            ? $this->sanitizeModuleName((string) $payload['page'])
            : $this->currentPageName();

        $this->renderFromPaths(
            $component,
            $componentPage !== '' ? $componentPage : $component,
            $payload,
            [
                $this->elementsPath . '/' . $component . '/' . $component . '.php',
                $this->pagesPath . '/' . $component . '/' . $component . '.php',
                $this->modulesPath . '/' . $component . '/' . $component . '.php',
            ]
        );
    }

    public function getModuleNameByPage(string $page): ?string
    {
        $page = trim($page);

        if ($page === '') {
            return null;
        }

        $result = $this->WIdb->select(
            'SELECT `contents` FROM `wi_page` WHERE `name` = :page LIMIT 1',
            ['page' => $page]
        );

        if (!is_array($result) || empty($result[0]['contents'])) {
            return null;
        }

        $module = $this->sanitizeModuleName((string) $result[0]['contents']);

        return $module !== '' ? $module : null;
    }

    public function getInstalled(): array
    {
        if ($this->tableExists('wi_modules')) {
            return $this->WIdb->select('SELECT * FROM `wi_modules` ORDER BY `id` ASC', []);
        }

        return [];
    }

    public function isEnabled(string $module): bool
    {
        $module = $this->sanitizeModuleName($module);

        if ($module === '') {
            return false;
        }

        if ($this->tableExists('wi_mod')) {
            $rows = $this->WIdb->select(
                'SELECT `mod_status`, `mod_powered`
                   FROM `wi_mod`
                  WHERE `module_name` = :module
                  LIMIT 1',
                ['module' => $module]
            );

            if (isset($rows[0])) {
                return strtolower((string) ($rows[0]['mod_status'] ?? 'enabled')) !== 'disabled'
                    && strtolower((string) ($rows[0]['mod_powered'] ?? 'power_on')) !== 'power_off';
            }
        }

        if ($this->tableExists('wi_modules')) {
            $enabled = $this->WIdb->selectColumn(
                'SELECT * FROM `wi_modules` WHERE `name` = :name LIMIT 1',
                ['name' => $module],
                'enabled'
            );

            if ($enabled !== null) {
                return (int) $enabled === 1;
            }
        }

        return true;
    }

    public function render(string $module): void
    {
        $this->renderModule($module, $this->currentPageName());
    }

    public function getModule(string $module): ?array
    {
        $module = $this->sanitizeModuleName($module);

        if ($module === '' || !$this->tableExists('wi_modules')) {
            return null;
        }

        $result = $this->WIdb->select(
            'SELECT * FROM `wi_modules` WHERE `name` = :name LIMIT 1',
            ['name' => $module]
        );

        return $result[0] ?? null;
    }

    public function scanModules(): array
    {
        if (!is_dir($this->modulesPath)) {
            return [];
        }

        $folders = scandir($this->modulesPath) ?: [];
        $modules = [];

        foreach ($folders as $folder) {
            if ($folder === '.' || $folder === '..') {
                continue;
            }

            if (is_dir($this->modulesPath . '/' . $folder)) {
                $modules[] = $folder;
            }
        }

        return $modules;
    }

    public function install(string $module): bool
    {
        $module = $this->sanitizeModuleName($module);

        if ($module === '' || !$this->tableExists('wi_modules') || $this->getModule($module) !== null) {
            return false;
        }

        return (bool) $this->WIdb->insert('wi_modules', ['name' => $module, 'enabled' => 1]);
    }

    public function enable(string $module): bool
    {
        return $this->setModuleEnabled($module, 1);
    }

    public function disable(string $module): bool
    {
        return $this->setModuleEnabled($module, 0);
    }

    public function uninstall(string $module): bool
    {
        $module = $this->sanitizeModuleName($module);

        if ($module === '' || !$this->tableExists('wi_modules')) {
            return false;
        }

        return (bool) $this->WIdb->delete('wi_modules', '`name` = :name', ['name' => $module]);
    }

    public function renderPageModules(string $page): void
    {
        if (!$this->tableExists('wi_page_modules')) {
            return;
        }

        $modules = $this->WIdb->select(
            'SELECT * FROM `wi_page_modules`
              WHERE `page` = :page
              ORDER BY `position` ASC',
            ['page' => $page]
        );

        foreach ($modules as $module) {
            $name = (string) ($module['module'] ?? '');

            if ($name !== '') {
                $this->render($name);
            }
        }
    }

    private function renderModule(string $module, string $page): void
    {
        $module = $this->sanitizeModuleName($module);

        if ($module === '' || !$this->isEnabled($module)) {
            return;
        }

        $this->renderFromPaths(
            $module,
            $page,
            [],
            [
                $this->modulesPath . '/' . $module . '/' . $module . '.php',
                $this->elementsPath . '/' . $module . '/' . $module . '.php',
            ]
        );
    }

    private function renderPageModule(string $module, string $page): void
    {
        $module = $this->sanitizeModuleName($module);
        $page = $this->sanitizeModuleName($page) ?: 'index';

        if ($module === '') {
            $this->renderNotFound($page);
            return;
        }

        $rendered = $this->renderFromPaths(
            $module,
            $page,
            [],
            [
                $this->pagesPath . '/' . $module . '/' . $module . '.php',
                $this->modulesPath . '/' . $module . '/' . $module . '.php',
            ],
            true
        );

        if (!$rendered) {
            $this->renderNotFound($page);
        }
    }

    /**
     * @param array<int,string> $paths
     * @param array<string,mixed> $payload
     */
    private function renderFromPaths(string $module, string $page, array $payload, array $paths, bool $returnStatus = false): bool
    {
        foreach ($paths as $path) {
            if (!is_file($path)) {
                continue;
            }

            require_once $path;
            $class = $this->resolveClassName($module);

            if ($class === null) {
                return false;
            }

            $object = new $class();

            if (method_exists($object, 'mod_name')) {
                $this->invokeModName($object, $module, $page, $payload);
                return true;
            }

            if (method_exists($object, 'render')) {
                $object->render($payload, $page, $module);
                return true;
            }

            return false;
        }

        return false;
    }

    private function renderNotFound(string $page): void
    {
        $path = $this->pagesPath . '/notfound/notfound.php';

        if (!is_file($path)) {
            echo '<section class="wi-member-panel"><h2>Page not found</h2><p>The requested module could not be found.</p></section>';
            return;
        }

        require_once $path;

        if (!class_exists('notfound')) {
            return;
        }

        $object = new notfound();

        if (method_exists($object, 'mod_name')) {
            $this->invokeModName($object, 'notfound', $page, []);
        }
    }

    private function resolveClassName(string $module): ?string
    {
        $legacy = $module;
        $studly = $this->studly($module);

        $candidates = [
            $legacy,
            'WI' . $studly . 'Module',
            $studly . 'Module',
            'WI' . $studly,
        ];

        foreach ($candidates as $class) {
            if (class_exists($class, false)) {
                return $class;
            }
        }

        return null;
    }

    /** @param array<string,mixed> $payload */
    private function invokeModName(object $object, string $module, string $page, array $payload): void
    {
        $method = new ReflectionMethod($object, 'mod_name');
        $count = $method->getNumberOfParameters();

        if ($count >= 3) {
            $object->mod_name($module, $page, $payload);
            return;
        }

        if ($count === 2) {
            $object->mod_name($module, $page);
            return;
        }

        if ($count === 1) {
            $object->mod_name($page);
            return;
        }

        $object->mod_name();
    }

    private function setModuleEnabled(string $module, int $enabled): bool
    {
        $module = $this->sanitizeModuleName($module);

        if ($module === '' || !$this->tableExists('wi_modules')) {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_modules',
            ['enabled' => $enabled],
            '`name` = :name',
            ['name' => $module]
        );
    }


    private function tableExists(string $table): bool
    {
        $table = preg_replace('/[^A-Za-z0-9_]/', '', $table) ?: '';

        if ($table === '') {
            return false;
        }

        try {
            $stmt = $this->WIdb->prepare('SHOW TABLES LIKE :table_name');
            $stmt->bindValue(':table_name', $table, PDO::PARAM_STR);
            $stmt->execute();
            $exists = (bool) $stmt->fetchColumn();
            $stmt->closeCursor();

            return $exists;
        } catch (Throwable $e) {
            return false;
        }
    }

    private function currentPageName(): string
    {
        $script = basename((string) ($_SERVER['SCRIPT_NAME'] ?? ''), '.php');

        return $this->sanitizeModuleName($script) ?: 'index';
    }

    private function sanitizeModuleName(string $value): string
    {
        $value = trim($value);
        $value = preg_replace('/[^A-Za-z0-9_\-]/', '', $value) ?: '';

        return str_replace('-', '_', $value);
    }

    private function studly(string $value): string
    {
        $parts = preg_split('/[_\-]+/', $value) ?: [$value];

        return implode('', array_map(
            static fn(string $part): string => ucfirst(strtolower($part)),
            $parts
        ));
    }
}
