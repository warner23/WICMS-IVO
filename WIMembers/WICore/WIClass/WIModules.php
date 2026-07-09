<?php
declare(strict_types=1);

/**
 * WIMembers central module loader.
 *
 * WIMembers must not own render modules. It resolves DB contents keys to the
 * central WIAdmin/WIModule tree and supports the new module lifecycle shape:
 * Install(), editMod(), editPageContent(), mod_name().
 */
#[\AllowDynamicProperties]
class WIModules
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function getMod(string $mod): void
    {
        $this->renderModule($mod, 'element', $mod, $mod);
    }

    public function getModMain(string $mod, string $page, string $module = ''): void
    {
        $this->renderModule($mod, 'page', $page, $module !== '' ? $module : $mod);
    }

    public function renderComponent(string $component, array $payload = []): void
    {
        $page = (string)($payload['page'] ?? $component);
        $this->renderModule($component, 'component', $page, $component, $payload);
    }

    public function getModuleNameByPage(string $page): string
    {
        $page = $this->sanitizeModuleName($page);
        if ($this->WIdb->tableExists('wi_page')) {
            $contents = (string)($this->WIdb->selectColumn(
                'SELECT * FROM `wi_page` WHERE `name` = :page LIMIT 1',
                ['page' => $page],
                'contents'
            ) ?? '');
            if (trim($contents) !== '') {
                return $this->sanitizeModuleName($contents);
            }
        }
        return $page;
    }

    private function renderModule(string $module, string $type, string $page, string $moduleKey, array $payload = []): void
    {
        $module = $this->sanitizeModuleName($module);
        $page = $this->sanitizeModuleName($page);
        $moduleKey = $this->sanitizeModuleName($moduleKey !== '' ? $moduleKey : $module);

        $resolved = $this->resolveModuleFile($module, $type);
        if ($resolved === null && $module !== 'notfound') {
            $resolved = $this->resolveModuleFile('notfound', 'page');
            $module = 'notfound';
            $moduleKey = 'notfound';
        }

        if ($resolved === null) {
            echo '<section class="wi-member-card wi-member-missing-module"><h2>Missing module</h2><p>Module <strong>' . wi_e($module) . '</strong> could not be found.</p></section>';
            return;
        }

        require_once $resolved;

        $class = $this->resolveClassName($module);
        if ($class === '') {
            echo '<section class="wi-member-card wi-member-missing-module"><h2>Module class missing</h2><p>Module <strong>' . wi_e($module) . '</strong> loaded, but no compatible class was found.</p></section>';
            return;
        }

        $object = new $class();
        if (method_exists($object, 'mod_name')) {
            $this->callModuleMethod($object, 'mod_name', [$moduleKey, $page, $payload]);
            return;
        }

        if (method_exists($object, 'render')) {
            $this->callModuleMethod($object, 'render', [$payload, $page, $moduleKey]);
            return;
        }

        echo '<section class="wi-member-card wi-member-missing-module"><h2>Module render missing</h2><p>Module <strong>' . wi_e($module) . '</strong> has no mod_name() or render() method.</p></section>';
    }

    private function resolveModuleFile(string $module, string $type): ?string
    {
        foreach ($this->centralPaths($module, $type) as $path) {
            if (is_file($path)) {
                return $path;
            }
        }
        return null;
    }

    /** @return array<int,string> */
    private function centralPaths(string $module, string $type): array
    {
        $root = WI_PUBLIC_ROOT_DIR . '/WIAdmin/WIModule';
        $paths = [];

        if ($type === 'page') {
            $paths[] = $root . '/pages/' . $module . '/' . $module . '.php';
            $paths[] = $root . '/modules/' . $module . '/' . $module . '.php';
            $paths[] = $root . '/elements/' . $module . '/' . $module . '.php';
            $paths[] = $root . '/components/' . $module . '/' . $module . '.php';
            return $paths;
        }

        $paths[] = $root . '/elements/' . $module . '/' . $module . '.php';
        $paths[] = $root . '/components/' . $module . '/' . $module . '.php';
        $paths[] = $root . '/modules/' . $module . '/' . $module . '.php';
        $paths[] = $root . '/pages/' . $module . '/' . $module . '.php';
        return $paths;
    }

    private function resolveClassName(string $module): string
    {
        $studly = $this->studly($module);
        $candidates = [
            'WI' . $studly . 'Module',
            $studly . 'Module',
            'WI' . $studly,
            $studly,
            $module,
            strtolower($module),
        ];

        foreach ($candidates as $candidate) {
            if ($candidate !== '' && class_exists($candidate, false)) {
                return $candidate;
            }
        }
        return '';
    }

    private function callModuleMethod(object $object, string $method, array $args): void
    {
        try {
            $ref = new ReflectionMethod($object, $method);
            $count = $ref->getNumberOfParameters();
            $ref->invokeArgs($object, array_slice($args, 0, $count));
        } catch (Throwable $e) {
            echo '<section class="wi-member-card wi-member-missing-module"><h2>Module error</h2><p>' . wi_e($e->getMessage()) . '</p></section>';
        }
    }

    private function sanitizeModuleName(string $module): string
    {
        $module = trim($module);
        $module = preg_replace('/[^a-zA-Z0-9_\-]/', '', $module) ?: 'notfound';
        return str_replace('-', '_', $module);
    }

    private function studly(string $module): string
    {
        $parts = preg_split('/[_\-]+/', $module) ?: [$module];
        return implode('', array_map(static fn($part): string => ucfirst(strtolower((string)$part)), $parts));
    }
}
