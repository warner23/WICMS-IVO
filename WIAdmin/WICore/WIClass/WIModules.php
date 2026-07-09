<?php
declare(strict_types=1);

/**
 * File Information
 * ============================================================================
 * Written By:     Warner Infinity / WICMS
 * Company:        Warner Infinity
 * Product:        WICMS
 * Project:        WI Ecosystem
 * File:           WIModules.php
 * Location:       /WIAdmin/WICore/WIClass/WIModules.php
 * Type:           PHP Class
 * Layer:          Shared Admin Core
 * Purpose Area:   Module and element discovery, registry actions and runtime rendering
 * Version:        2.1.0
 * Created:        Legacy
 * Last Updated:   2026-05-31
 * Status:         Production-ready compatibility refactor
 * Summary:
 *   Canonical WICMS module loader plus Modules & Elements Centre compatibility
 *   methods. Keeps UI files display-only by moving filesystem scanning, registry
 *   reads, install/enable/disable actions and builder compatibility hooks into
 *   this shared class. Uses WIdb only.
 * ============================================================================
 */
final class WIModules
{
    private WIdb $WIdb;
    private WIPage $page;
    private string $moduleRoot;
    private string $elementsPath;
    private string $modulesPath;
    private string $pagesPath;
    private string $testmodsPath;
    private bool $debugMode = true;

    /** @var array<string,array<string,bool>> */
    private array $columnCache = [];

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->page = new WIPage();

        $this->moduleRoot   = dirname(dirname(dirname(__DIR__))) . '/WIAdmin/WIModule/';
        $this->elementsPath = $this->moduleRoot . 'elements/';
        $this->modulesPath  = $this->moduleRoot . 'modules/';
        $this->pagesPath    = $this->moduleRoot . 'pages/';
        $this->testmodsPath = $this->moduleRoot . 'testmods/';
    }

    /*
    |--------------------------------------------------------------------------
    | Front/runtime module rendering
    |--------------------------------------------------------------------------
    */

    public function getMod(string $mod): void
    {
        $this->renderModule($mod);
    }

    public function getModMain(string $mod, string $page, ?string $module = null): void
    {
        $targetModule = $module !== null && trim($module) !== '' ? $module : $mod;
        $this->renderPageModule($targetModule, $page);
    }

    /**
     * Render a shared module/component by name. Components are resolved from
     * pages, elements, then modules so member/profile shells can reuse the same
     * central WIAdmin module tree instead of creating a second WIMembers tree.
     *
     * @param array<string,mixed> $payload
     */
    public function renderComponent(string $component, array $payload = []): void
    {
        $component = $this->sanitizeModuleName($component);

        if ($component === '') {
            return;
        }

        $page = isset($payload['page'])
            ? $this->sanitizeModuleName((string) $payload['page'])
            : $this->currentPageName();

        foreach ([
            $this->pagesPath . $component . '/' . $component . '.php',
            $this->elementsPath . $component . '/' . $component . '.php',
            $this->modulesPath . $component . '/' . $component . '.php',
        ] as $file) {
            if (!is_file($file)) {
                continue;
            }

            require_once $file;
            $class = $this->resolveClassName($component);

            if ($class === null) {
                $this->debugMessage('Component class missing: ' . $component);
                return;
            }

            $instance = new $class();

            if ($this->invokeRenderable($instance, $component, $page, $payload)) {
                return;
            }

            $this->debugMessage('Component render method missing: ' . $component);
            return;
        }
    }

    public function renderModule(string $module): void
    {
        $module = $this->sanitizeModuleName($module);

        if ($module === '') {
            $this->debugMessage('Module name missing.');
            return;
        }

        if (!$this->moduleExists($module, 'module')) {
            $this->debugMessage('Module file not found: ' . $module);
            return;
        }

        if (!$this->isModuleActive($module)) {
            return;
        }

        $file = $this->resolveModuleClassPath($module);

        if ($file === null || !is_file($file)) {
            $this->debugMessage('Module path could not be resolved: ' . $module);
            return;
        }

        require_once $file;
        $class = $this->resolveClassName($module);

        if ($class === null) {
            $this->debugMessage('Module class missing: ' . $module);
            return;
        }

        $instance = new $class();

        if ($this->invokeRenderable($instance, $module, $this->currentPageName(), [])) {
            return;
        }

        $this->debugMessage('mod_name() or render() not found on module: ' . $module);
    }

    public function renderPageModule(string $module, string $page): void
    {
        $module = $this->sanitizeModuleName($module);
        $page = $this->sanitizeModuleName($page);

        if ($module === '') {
            $this->debugMessage('Page module name missing.');
            return;
        }

        if ($page === '') {
            $page = $this->currentPageName();
        }

        $file = $this->resolvePageClassPath($module);

        if ($file === null || !is_file($file)) {
            $this->renderNotFound($page);
            return;
        }

        require_once $file;
        $class = $this->resolveClassName($module);

        if ($class === null) {
            $this->renderNotFound($page);
            return;
        }

        $instance = new $class();

        if ($this->invokeRenderable($instance, $module, $page, [])) {
            return;
        }

        $this->renderNotFound($page);
    }

    public function moduleExists(string $module, string $type = 'module'): bool
    {
        $module = $this->sanitizeModuleName($module);

        if ($module === '') {
            return false;
        }

        $path = $type === 'page'
            ? $this->resolvePageClassPath($module)
            : $this->resolveModuleClassPath($module);

        return $path !== null && is_file($path);
    }

    public function getModuleNameByPage(string $pageName): ?string
    {
        $value = $this->page->PageMod($pageName, 'contents');

        if ($value === null) {
            return null;
        }

        $value = $this->sanitizeModuleName((string) $value);

        return $value !== '' ? $value : null;
    }

    public function isModuleActive(string $module): bool
    {
        $module = $this->sanitizeModuleName($module);

        if ($module === '') {
            return false;
        }

        $result = $this->WIdb->select(
            "SELECT `module_name`, `mod_status`, `mod_powered`
             FROM `wi_mod`
             WHERE `module_name` = :module
             LIMIT 1",
            ['module' => $module]
        );

        if (!isset($result[0])) {
            return true;
        }

        $status = strtolower(trim((string) ($result[0]['mod_status'] ?? 'enabled')));
        $power  = strtolower(trim((string) ($result[0]['mod_powered'] ?? 'power_on')));

        if ($status === 'disabled') {
            return false;
        }

        return in_array($power, ['power_on', '1', 'on', 'enabled'], true);
    }

    /**
     * Returns raw installed module registry rows for services that need data.
     * UI rendering methods use displayInstalledModules() to avoid changing this
     * existing data-returning contract.
     *
     * @return array<int,array<string,mixed>>
     */
    public function getInstalledModules(): array
    {
        return $this->WIdb->select(
            "SELECT `id`, `module_name`, `mod_status`, `mod_powered`
             FROM `wi_mod`
             ORDER BY `module_name` ASC",
            []
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Modules & Elements Centre rendering
    |--------------------------------------------------------------------------
    */

    public function displayElements(): void
    {
        $folders = $this->scanFolders($this->elementsPath);
        $pager = $this->paginateArray($folders, 12);

        echo '<div class="row">';

        if (count($pager['items']) === 0) {
            $this->emptyState('No elements found', 'No element folders were found in the elements directory.');
        } else {
            foreach ($pager['items'] as $name) {
                $this->renderElementCard($this->elementMeta((string) $name), true);
            }
        }

        echo '</div>';

        $this->renderPager('NextElementsPage', '#elementsStoreList', (int) $pager['page'], (int) $pager['total_pages']);
    }

    public function displayModules(): void
    {
        $folders = $this->scanFolders($this->modulesPath);
        $pager = $this->paginateArray($folders, 12);

        echo '<div class="row">';

        if (count($pager['items']) === 0) {
            $this->emptyState('No modules found', 'No module folders were found in the modules directory.');
        } else {
            foreach ($pager['items'] as $name) {
                $this->renderModuleCard($this->moduleMeta((string) $name), true);
            }
        }

        echo '</div>';

        $this->renderPager('NextModPage', '#modulesStoreList', (int) $pager['page'], (int) $pager['total_pages']);
    }

    public function getElements(): void
    {
        $rows = $this->WIdb->select("SELECT * FROM `wi_elements` ORDER BY `element_id` ASC", []);

        if (!is_array($rows) || count($rows) === 0) {
            echo '<div class="row">';
            $this->emptyState('No installed elements', 'There are currently no installed elements to manage.');
            echo '</div>';
            return;
        }

        $names = [];
        foreach ($rows as $row) {
            $names[] = (string) ($row['element_name'] ?? '');
        }

        $pager = $this->paginateArray(array_filter($names), 12);

        echo '<div class="row">';
        foreach ($pager['items'] as $name) {
            $this->renderElementCard($this->elementMeta((string) $name), false);
        }
        echo '</div>';

        $this->renderPager('NextInstalledElementsPage', '#installedElementsList', (int) $pager['page'], (int) $pager['total_pages']);
    }

    public function getModules(): void
    {
        $rows = $this->WIdb->select("SELECT * FROM `wi_mod` ORDER BY `mod_id` ASC", []);

        if (!is_array($rows) || count($rows) === 0) {
            echo '<div class="row">';
            $this->emptyState('No installed modules', 'There are currently no installed modules to manage.');
            echo '</div>';
            return;
        }

        $names = [];
        foreach ($rows as $row) {
            $names[] = (string) ($row['module_name'] ?? '');
        }

        $pager = $this->paginateArray(array_filter($names), 12);

        echo '<div class="row">';
        foreach ($pager['items'] as $name) {
            $this->renderModuleCard($this->moduleMeta((string) $name), false);
        }
        echo '</div>';

        $this->renderPager('NextInstalledModulesPage', '#installedModulesList', (int) $pager['page'], (int) $pager['total_pages']);
    }

    public function displayInstalledModules(): void
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_mod`
             WHERE `mod_status` = :status
             ORDER BY `mod_id` ASC",
            ['status' => 'enabled']
        );

        if (!is_array($rows) || count($rows) === 0) {
            echo '<div class="row">';
            $this->emptyState('No active modules', 'There are currently no enabled modules.');
            echo '</div>';
            return;
        }

        echo '<div class="row">';
        foreach ($rows as $row) {
            $this->renderModuleCard($this->moduleMeta((string) ($row['module_name'] ?? '')), false);
        }
        echo '</div>';
    }

    public function getPageModules(): void
    {
        $rows = $this->moduleContentRows();
        $pager = $this->paginateArray($rows, 18);

        if (!is_array($pager['items']) || count($pager['items']) === 0) {
            echo '<div class="alert alert-warning">No builder modules found.</div>';
            return;
        }

        echo '<ul class="nav nav-list accordion-group">';
        foreach ($pager['items'] as $row) {
            $name = (string) ($row['name'] ?? $row['module_name'] ?? 'Unnamed Module');
            echo '<li class="wicreate ui-draggable" data-module="' . $this->e($name) . '">' . $this->e($name) . '</li>';
        }
        echo '</ul>';

        $this->renderPager('NextBuilderModulesPage', '#builderModulesList', (int) $pager['page'], (int) $pager['total_pages']);
    }

    /*
    |--------------------------------------------------------------------------
    | Registry install / uninstall / power actions
    |--------------------------------------------------------------------------
    */

    public function install_mod($moduleName): bool
    {
        $moduleName = $this->sanitizeModuleName((string) $moduleName);

        if ($moduleName === '') {
            return false;
        }

        $meta = $this->moduleMeta($moduleName);
        $payload = $this->filterPayloadForTable('wi_mod', [
            'mod_status'      => 'enabled',
            'mod_powered'     => 'power_on',
            'mod_type'        => 'custom',
            'mod_author'      => (string) ($meta['author'] ?? 'Warner Infinity'),
            'module_name'     => $moduleName,
            'Mod_description' => (string) ($meta['description'] ?? ''),
            'mod_description' => (string) ($meta['description'] ?? ''),
            'mod_font'        => 'fa-cube',
        ]);

        if ($payload === []) {
            return false;
        }

        if ($this->registryModule($moduleName) !== null) {
            return (bool) $this->WIdb->update(
                'wi_mod',
                $payload,
                '`module_name` = :name',
                ['name' => $moduleName]
            );
        }

        return (bool) $this->WIdb->insert('wi_mod', $payload);
    }

    public function uninstall_mod($moduleName): bool
    {
        $moduleName = $this->sanitizeModuleName((string) $moduleName);

        if ($moduleName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_mod',
            $this->filterPayloadForTable('wi_mod', [
                'mod_status'  => 'disabled',
                'mod_powered' => 'power_off',
            ]),
            '`module_name` = :name',
            ['name' => $moduleName]
        );
    }

    public function active_available_mod($moduleName, $enable = 'enabled'): bool
    {
        $moduleName = $this->sanitizeModuleName((string) $moduleName);

        if ($moduleName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_mod',
            $this->filterPayloadForTable('wi_mod', [
                'mod_status'  => 'enabled',
                'mod_powered' => 'power_on',
            ]),
            '`module_name` = :name',
            ['name' => $moduleName]
        );
    }

    public function deactive_available_mod($moduleName, $disable = 'disabled'): bool
    {
        $moduleName = $this->sanitizeModuleName((string) $moduleName);

        if ($moduleName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_mod',
            $this->filterPayloadForTable('wi_mod', [
                'mod_status'  => 'disabled',
                'mod_powered' => 'power_off',
            ]),
            '`module_name` = :name',
            ['name' => $moduleName]
        );
    }

    public function installElement($elementName): bool
    {
        $elementName = $this->sanitizeModuleName((string) $elementName);

        if ($elementName === '') {
            return false;
        }

        $meta = $this->elementMeta($elementName);
        $type = $this->normaliseElementType((string) ($meta['type'] ?? 'Common Fields'));

        $payload = $this->filterPayloadForTable('wi_elements', [
            'element_status'      => 'enabled',
            'element_powered'     => 'power_on',
            'element_type'        => $type,
            'element_author'      => (string) ($meta['author'] ?? 'Warner Infinity'),
            'element_name'        => $elementName,
            'element_description' => (string) ($meta['description'] ?? ''),
            'element_font'        => (string) ($meta['font'] ?? 'fa-cube'),
            'group'               => (string) ($meta['group'] ?? ''),
        ]);

        if ($payload === []) {
            return false;
        }

        if ($this->registryElement($elementName) !== null) {
            return (bool) $this->WIdb->update(
                'wi_elements',
                $payload,
                '`element_name` = :name',
                ['name' => $elementName]
            );
        }

        return (bool) $this->WIdb->insert('wi_elements', $payload);
    }

    /** Legacy misspelling kept because WIAjax still calls this method. */
    public function unistall_Element($elementName): bool
    {
        $elementName = $this->sanitizeModuleName((string) $elementName);

        if ($elementName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_elements',
            $this->filterPayloadForTable('wi_elements', [
                'element_status'  => 'disabled',
                'element_powered' => 'power_off',
            ]),
            '`element_name` = :name',
            ['name' => $elementName]
        );
    }

    public function activateAvailableElements($elementName, $enable = 'enabled'): bool
    {
        $elementName = $this->sanitizeModuleName((string) $elementName);

        if ($elementName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_elements',
            $this->filterPayloadForTable('wi_elements', [
                'element_status'  => 'enabled',
                'element_powered' => 'power_on',
            ]),
            '`element_name` = :name',
            ['name' => $elementName]
        );
    }

    public function deactivateAvailableElements($elementName, $disable = 'disabled'): bool
    {
        $elementName = $this->sanitizeModuleName((string) $elementName);

        if ($elementName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_elements',
            $this->filterPayloadForTable('wi_elements', [
                'element_status'  => 'disabled',
                'element_powered' => 'power_off',
            ]),
            '`element_name` = :name',
            ['name' => $elementName]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Builder compatibility methods
    |--------------------------------------------------------------------------
    */

    public function modules(): void
    {
        $this->getPageModules();
    }

    public function installed_modules(): void
    {
        $this->displayInstalledModules();
    }

    public function available_modules(): void
    {
        $this->displayModules();
    }

    public function elements(): void
    {
        $this->getElements();
    }

    public function available_elements(): void
    {
        $this->displayElements();
    }

    public function ActiveModulesGrid(): void
    {
        $this->displayInstalledModules();
    }

    public function ActiveElementsGrid(): void
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_elements`
             WHERE `element_status` = :status
             AND `element_powered` = :powered
             ORDER BY `element_type` ASC, `element_name` ASC",
            [
                'status'  => 'enabled',
                'powered' => 'power_on',
            ]
        );

        if (!is_array($rows) || count($rows) === 0) {
            echo '<div class="alert alert-warning">No active elements found.</div>';
            return;
        }

        echo '<div class="row">';
        foreach ($rows as $row) {
            $this->renderElementCard($this->elementMeta((string) ($row['element_name'] ?? '')), false);
        }
        echo '</div>';
    }

    public function ActiveElementsBase(): void
    {
        $rows = $this->activeElementRows('', '');

        if ($rows === []) {
            echo '<div class="alert alert-warning">No active elements found.</div>';
            return;
        }

        echo '<ul class="nav nav-list accordion-group">';
        foreach ($rows as $row) {
            $this->renderBuilderElementListItem($row);
        }
        echo '</ul>';
    }

    public function ActiveElementsComponents(): void
    {
        $this->renderElementGroupList('Common Fields', 'Components');
    }

    public function ActiveElementsForms(): void
    {
        $this->renderElementGroupList('HTML Elements', 'Forms');
    }

    public function ActiveElementsJavascript(): void
    {
        $this->renderElementGroupList('Layout', 'Javascript');
    }

    public function ActiveElementsModules(): void
    {
        $this->renderInstalledModuleList();
    }

    public function ActiveElements(): void
    {
        $this->ActiveElementsBase();
    }

    public function ActiveMods(): void
    {
        $this->renderInstalledModuleList();
    }

    public function columns(): bool
    {
        return is_dir($this->modulesPath . 'columns/');
    }

    public function dropElement($name): void
    {
        $name = $this->sanitizeModuleName((string) $name);

        if ($name === '') {
            return;
        }

        $file = $this->elementsPath . $name . '/' . $name . '.php';

        if (!is_file($file)) {
            echo '<div class="alert alert-danger">Element file missing: ' . $this->e($name) . '</div>';
            return;
        }

        require_once $file;

        if (!class_exists($name)) {
            echo '<div class="alert alert-danger">Element class missing: ' . $this->e($name) . '</div>';
            return;
        }

        $obj = new $name();

        if (method_exists($obj, 'defaults')) {
            $obj->defaults();
            return;
        }

        if (method_exists($obj, 'render')) {
            $obj->render();
            return;
        }

        echo '<div class="alert alert-warning">Element loaded but has no defaults() or render() method.</div>';
    }

    public function dropColElement($name): void
    {
        $this->dropElement($name);
    }

    public function editDropElement($name, $pageId = 1): void
    {
        $this->dropElement($name);
    }

    public function createMod($contents, $modName, $layout = '', $elements = [], $columnPreset = ''): bool
    {
        $modName = trim((string) $modName);

        if ($modName === '') {
            return false;
        }

        $payload = $this->filterPayloadForTable('wi_modules', [
            'name' => $modName,
            'text' => (string) $contents,
            'content' => (string) $contents,
        ]);

        if ($payload === []) {
            return false;
        }

        return (bool) $this->WIdb->insert('wi_modules', $payload);
    }

    public function editContents($title, $para, $modName): bool
    {
        $modName = trim((string) $modName);

        if ($modName === '') {
            return false;
        }

        $payload = $this->filterPayloadForTable('wi_modules', [
            'title' => (string) $title,
            'para'  => (string) $para,
            'text'  => (string) $para,
        ]);

        if ($payload === []) {
            return false;
        }

        return (bool) $this->WIdb->update('wi_modules', $payload, '`name` = :name', ['name' => $modName]);
    }

    public function save_mod($modName, $contents, $content = ''): bool
    {
        $modName = trim((string) $modName);

        if ($modName === '') {
            return false;
        }

        $payload = $this->filterPayloadForTable('wi_modules', [
            'text'    => (string) $contents,
            'content' => (string) $contents,
        ]);

        if ($payload === []) {
            return false;
        }

        return (bool) $this->WIdb->update('wi_modules', $payload, '`name` = :name', ['name' => $modName]);
    }

    /*
    |--------------------------------------------------------------------------
    | Private helpers
    |--------------------------------------------------------------------------
    */

    private function resolveModuleClassPath(string $module): ?string
    {
        $module = $this->sanitizeModuleName($module);

        if ($module === '') {
            return null;
        }

        return $this->modulesPath . $module . '/' . $module . '.php';
    }

    private function resolvePageClassPath(string $module): ?string
    {
        $module = $this->sanitizeModuleName($module);

        if ($module === '') {
            return null;
        }

        return $this->pagesPath . $module . '/' . $module . '.php';
    }

    private function renderNotFound(string $page): void
    {
        $notFoundFile = $this->pagesPath . 'notfound/notfound.php';

        if (!is_file($notFoundFile)) {
            $this->debugMessage('Notfound page module missing.');
            return;
        }

        require_once $notFoundFile;
        $class = $this->resolveClassName('notfound') ?? (class_exists('notfound', false) ? 'notfound' : null);

        if ($class === null) {
            $this->debugMessage('Notfound class missing.');
            return;
        }

        $instance = new $class();

        if ($this->invokeRenderable($instance, 'notfound', $page, [])) {
            return;
        }

        $this->debugMessage('mod_name() missing on notfound.');
    }

    private function resolveClassName(string $module): ?string
    {
        $module = $this->sanitizeModuleName($module);

        if ($module === '') {
            return null;
        }

        $studly = $this->studly($module);
        $candidates = [
            $module,
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
    private function invokeRenderable(object $instance, string $module, string $page, array $payload): bool
    {
        if (method_exists($instance, 'mod_name')) {
            $method = new ReflectionMethod($instance, 'mod_name');
            $count = $method->getNumberOfParameters();

            if ($count >= 3) {
                $instance->mod_name($module, $page, $payload);
                return true;
            }

            if ($count === 2) {
                $instance->mod_name($module, $page);
                return true;
            }

            if ($count === 1) {
                $instance->mod_name($page);
                return true;
            }

            $instance->mod_name();
            return true;
        }

        if (method_exists($instance, 'render')) {
            $instance->render($payload, $page, $module);
            return true;
        }

        return false;
    }

    private function studly(string $value): string
    {
        $parts = preg_split('/[_\-]+/', $value) ?: [$value];

        return implode('', array_map(
            static fn(string $part): string => ucfirst(strtolower($part)),
            $parts
        ));
    }

    private function currentPageName(): string
    {
        if (!empty($_GET['page'])) {
            return $this->sanitizeModuleName((string) $_GET['page']);
        }

        return 'home';
    }

    private function currentPageNumber(): int
    {
        $page = $_POST['page'] ?? $_GET['page_num'] ?? 1;
        $page = filter_var($page, FILTER_VALIDATE_INT);

        return ($page && $page > 0) ? (int) $page : 1;
    }

    /**
     * @param array<int|string,mixed> $items
     * @return array{items:array<int|string,mixed>,page:int,per_page:int,total:int,total_pages:int}
     */
    private function paginateArray(array $items, int $perPage = 12): array
    {
        $page = $this->currentPageNumber();
        $total = count($items);
        $offset = ($page - 1) * $perPage;

        return [
            'items'       => array_slice($items, $offset, $perPage),
            'page'        => $page,
            'per_page'    => $perPage,
            'total'       => $total,
            'total_pages' => (int) ceil($total / max(1, $perPage)),
        ];
    }

    private function renderPager(string $action, string $targetSelector, int $page, int $totalPages): void
    {
        if ($totalPages <= 1) {
            return;
        }

        echo '<div class="wi-module-pager">';

        for ($i = 1; $i <= $totalPages; $i++) {
            $active = ($i === $page) ? 'btn btn-primary' : 'btn btn-default';

            echo '<button type="button"
                    class="' . $this->e($active) . '"
                    data-action="' . $this->e($action) . '"
                    data-page="' . $i . '"
                    data-target="' . $this->e($targetSelector) . '">' . $i . '</button>';
        }

        echo '</div>';
    }

    /** @return array<int,string> */
    private function scanFolders(string $dir): array
    {
        if (!is_dir($dir)) {
            return [];
        }

        $items = scandir($dir);
        $folders = [];

        foreach ($items ?: [] as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            if (is_dir($dir . $item)) {
                $folders[] = $item;
            }
        }

        natcasesort($folders);

        return array_values($folders);
    }

    /** @return array<string,mixed> */
    private function readJsonMeta(string $folder, string $file): array
    {
        $path = rtrim($folder, '/') . '/' . $file;

        if (!is_file($path)) {
            return [];
        }

        $raw = file_get_contents($path);
        $json = json_decode((string) $raw, true);

        return is_array($json) ? $json : [];
    }

    private function findPreviewImage(string $folder): string
    {
        $preferred = ['preview.png', 'preview.jpg', 'preview.jpeg', 'icon.png', 'icon.jpg', 'icon.jpeg', 'index.png', 'welcome.png'];

        foreach ($preferred as $file) {
            if (is_file($folder . '/' . $file)) {
                return $file;
            }
        }

        $all = glob($folder . '/*.{png,jpg,jpeg,gif,webp,PNG,JPG,JPEG}', GLOB_BRACE);

        return ($all && isset($all[0])) ? basename($all[0]) : '';
    }

    /** @return array<string,mixed>|null */
    private function registryElement(string $name): ?array
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_elements` WHERE `element_name` = :name LIMIT 1",
            ['name' => $name]
        );

        return $rows[0] ?? null;
    }

    /** @return array<string,mixed>|null */
    private function registryModule(string $name): ?array
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_mod` WHERE `module_name` = :name LIMIT 1",
            ['name' => $name]
        );

        return $rows[0] ?? null;
    }

    /** @return array<int,array<string,mixed>> */
    private function moduleContentRows(): array
    {
        return $this->WIdb->select("SELECT * FROM `wi_modules` ORDER BY `id` ASC", []);
    }

    /** @return array<string,mixed> */
    private function elementMeta(string $elementName): array
    {
        $elementName = $this->sanitizeModuleName($elementName);
        $folder = $this->elementsPath . $elementName;
        $json = $this->readJsonMeta($folder, 'element.json');
        $db = $this->registryElement($elementName) ?? [];

        return [
            'name'        => $elementName,
            'title'       => $json['title'] ?? $elementName,
            'type'        => $this->normaliseElementType((string) ($json['type'] ?? ($db['element_type'] ?? 'Common Fields'))),
            'group'       => $json['group'] ?? ($db['group'] ?? ''),
            'author'      => $json['author'] ?? ($db['element_author'] ?? 'Warner Infinity'),
            'description' => $json['description'] ?? ($db['element_description'] ?? ''),
            'font'        => $json['icon_font'] ?? ($db['element_font'] ?? 'fa-cube'),
            'image'       => $json['image'] ?? $this->findPreviewImage($folder),
            'status'      => $db['element_status'] ?? 'disabled',
            'powered'     => $db['element_powered'] ?? 'power_off',
            'folder'      => $folder,
        ];
    }

    /** @return array<string,mixed> */
    private function moduleMeta(string $moduleName): array
    {
        $moduleName = $this->sanitizeModuleName($moduleName);
        $folder = $this->modulesPath . $moduleName;
        $json = $this->readJsonMeta($folder, 'module.json');
        $db = $this->registryModule($moduleName) ?? [];

        return [
            'name'        => $moduleName,
            'title'       => $json['title'] ?? $moduleName,
            'type'        => $json['category'] ?? ($db['mod_type'] ?? 'custom'),
            'author'      => $json['author'] ?? ($db['mod_author'] ?? 'Warner Infinity'),
            'description' => $json['description'] ?? ($db['Mod_description'] ?? ($db['mod_description'] ?? '')),
            'image'       => $json['image'] ?? $this->findPreviewImage($folder),
            'status'      => $db['mod_status'] ?? 'disabled',
            'powered'     => $db['mod_powered'] ?? 'power_off',
            'folder'      => $folder,
        ];
    }

    private function normaliseElementType(string $type): string
    {
        $type = trim($type);
        $allowed = ['Common Fields', 'HTML Elements', 'Layout'];

        return in_array($type, $allowed, true) ? $type : 'Common Fields';
    }

    private function renderElementCard(array $meta, bool $availableMode = false): void
    {
        $name = (string) $meta['name'];
        $installed = ((string) $meta['status'] === 'enabled');
        $powered = ((string) $meta['powered'] === 'power_on');

        $stateBadge = $availableMode
            ? $this->badge($installed ? 'Installed' : 'Ready to Install', $installed ? 'info' : 'warning')
            : $this->badge($installed ? 'Enabled' : 'Disabled', $installed ? 'success' : 'muted');

        $powerBadge = $this->badge($powered ? 'Powered' : 'Offline', $powered ? 'success' : 'muted');

        echo '<div class="col-md-4 col-sm-6 col-xs-12"><div class="wi-store-card">';
        $this->renderCardHeader((string) $meta['title'], $stateBadge, $powerBadge);

        echo '<div class="wi-store-card__body">'
            . $this->previewHtml('WIAdmin/WIModule/elements/' . $name . '/', (string) $meta['image'], (string) $meta['title'])
            . '<div class="wi-store-card__meta">';

        $this->renderMetaRow('Name', $name);
        $this->renderMetaRow('Type', (string) $meta['type']);
        $this->renderMetaRow('Group', (string) $meta['group']);
        $this->renderMetaRow('Author', (string) $meta['author']);

        echo '</div><div class="wi-store-card__description">'
            . $this->e($this->truncateText((string) $meta['description'], 140))
            . '</div></div><div class="wi-store-card__footer">';

        if ($availableMode) {
            if ($installed) {
                echo '<button type="button" class="btn btn-success" disabled>Installed</button>';
            } else {
                echo '<button type="button" class="btn btn-primary" onclick="WIMod.installElement(\'' . $this->e($name) . '\', \'' . $this->e((string) $meta['author']) . '\')">Install</button>';
            }

            echo '<button type="button" class="btn btn-default" onclick="WIMod.uninstallElements(\'' . $this->e($name) . '\', \'' . $this->e((string) $meta['author']) . '\')">Remove</button>';
        } else {
            echo '<button type="button" class="btn btn-success" onclick="WIMod.enableElement(\'' . $this->e($name) . '\')">Enable</button>
                  <button type="button" class="btn btn-default" onclick="WIMod.disableElement(\'' . $this->e($name) . '\')">Disable</button>
                  <button type="button" class="btn btn-danger" onclick="WIMod.uninstallElements(\'' . $this->e($name) . '\', \'' . $this->e((string) $meta['author']) . '\')">Uninstall</button>';
        }

        echo '</div></div></div>';
    }

    private function renderModuleCard(array $meta, bool $availableMode = false): void
    {
        $name = (string) $meta['name'];
        $installed = ((string) $meta['status'] === 'enabled');
        $powered = ((string) $meta['powered'] === 'power_on');

        $stateBadge = $availableMode
            ? $this->badge($installed ? 'Installed' : 'Ready to Install', $installed ? 'info' : 'warning')
            : $this->badge($installed ? 'Enabled' : 'Disabled', $installed ? 'success' : 'muted');

        $powerBadge = $this->badge($powered ? 'Powered' : 'Offline', $powered ? 'success' : 'muted');

        echo '<div class="col-md-4 col-sm-6 col-xs-12"><div class="wi-store-card">';
        $this->renderCardHeader((string) $meta['title'], $stateBadge, $powerBadge);

        echo '<div class="wi-store-card__body">'
            . $this->previewHtml('WIAdmin/WIModule/modules/' . $name . '/', (string) $meta['image'], (string) $meta['title'])
            . '<div class="wi-store-card__meta">';

        $this->renderMetaRow('Name', $name);
        $this->renderMetaRow('Type', (string) $meta['type']);
        $this->renderMetaRow('Author', (string) $meta['author']);

        echo '</div><div class="wi-store-card__description">'
            . $this->e($this->truncateText((string) $meta['description'], 140))
            . '</div></div><div class="wi-store-card__footer">';

        if ($availableMode) {
            if ($installed) {
                echo '<button type="button" class="btn btn-success" disabled>Installed</button>';
            } else {
                echo '<button type="button" class="btn btn-primary" onclick="WIMod.install(\'' . $this->e($name) . '\', \'' . $this->e((string) $meta['author']) . '\')">Install</button>';
            }

            echo '<button type="button" class="btn btn-default" onclick="WIMod.uninstall(\'' . $this->e($name) . '\', \'' . $this->e((string) $meta['author']) . '\')">Remove</button>';
        } else {
            echo '<button type="button" class="btn btn-success" onclick="WIMod.enable(\'' . $this->e($name) . '\')">Enable</button>
                  <button type="button" class="btn btn-default" onclick="WIMod.disable(\'' . $this->e($name) . '\')">Disable</button>
                  <button type="button" class="btn btn-danger" onclick="WIMod.uninstall(\'' . $this->e($name) . '\', \'' . $this->e((string) $meta['author']) . '\')">Uninstall</button>';
        }

        echo '</div></div></div>';
    }

    private function renderCardHeader(string $title, string $badgeLeft = '', string $badgeRight = ''): void
    {
        echo '<div class="wi-store-card__header">
                <div class="wi-store-card__title-wrap"><h4 class="wi-store-card__title">' . $this->e($title) . '</h4></div>
                <div class="wi-store-card__badges">' . $badgeLeft . $badgeRight . '</div>
              </div>';
    }

    private function badge(string $text, string $variant = 'default'): string
    {
        return '<span class="wi-store-badge wi-store-badge--' . $this->e($variant) . '">' . $this->e($text) . '</span>';
    }

    private function truncateText(string $text, int $limit = 120): string
    {
        $text = trim($text);

        if ($text === '') {
            return 'No description available yet.';
        }

        if (function_exists('mb_strlen') && mb_strlen($text) > $limit) {
            return mb_substr($text, 0, $limit - 3) . '...';
        }

        if (!function_exists('mb_strlen') && strlen($text) > $limit) {
            return substr($text, 0, $limit - 3) . '...';
        }

        return $text;
    }

    private function previewHtml(string $baseWebPath, string $file, string $alt): string
    {
        if ($file === '') {
            return '<div class="wi-store-card__preview wi-store-card__preview--empty"><span>No Preview</span></div>';
        }

        return '<div class="wi-store-card__preview"><img src="' . $this->e($baseWebPath . $file) . '" alt="' . $this->e($alt) . '" class="wi-store-card__image"></div>';
    }

    private function renderMetaRow(string $label, string $value): void
    {
        echo '<div class="wi-store-card__meta-row"><span class="wi-store-card__meta-label">' . $this->e($label) . '</span><span class="wi-store-card__meta-value">' . $this->e($value) . '</span></div>';
    }

    private function emptyState(string $title, string $text): void
    {
        echo '<div class="col-md-12"><div class="wi-store-empty"><h4>' . $this->e($title) . '</h4><p>' . $this->e($text) . '</p></div></div>';
    }

    /** @return array<int,array<string,mixed>> */
    private function activeElementRows(string $fallbackType = '', string $groupName = ''): array
    {
        $params = ['status' => 'enabled', 'powered' => 'power_on'];
        $sql = "SELECT * FROM `wi_elements` WHERE `element_status` = :status AND `element_powered` = :powered";

        if ($groupName !== '') {
            $sql .= " AND (`group` = :grp OR `element_type` = :etype)";
            $params['grp'] = $groupName;
            $params['etype'] = $this->normaliseElementType($fallbackType);
        } elseif ($fallbackType !== '') {
            $sql .= " AND `element_type` = :etype";
            $params['etype'] = $this->normaliseElementType($fallbackType);
        }

        $sql .= " ORDER BY `element_name` ASC";

        $rows = $this->WIdb->select($sql, $params);
        return is_array($rows) ? $rows : [];
    }

    private function renderElementGroupList(string $fallbackType, string $groupName = ''): void
    {
        $rows = $this->activeElementRows($fallbackType, $groupName);

        if ($rows === []) {
            echo '<div class="alert alert-warning">No active elements found in this group.</div>';
            return;
        }

        echo '<ul class="nav nav-list accordion-group">';
        foreach ($rows as $row) {
            $this->renderBuilderElementListItem($row);
        }
        echo '</ul>';
    }

    /** @param array<string,mixed> $row */
    private function renderBuilderElementListItem(array $row): void
    {
        $name = $this->e($row['element_name'] ?? '');
        $type = $this->e($row['element_type'] ?? 'Element');
        $font = $this->e($row['element_font'] ?? 'fa-cube');

        echo '<li class="wicreate draggable-element" data-element="' . $name . '" data-type="' . $type . '"><i class="fa ' . $font . '"></i> ' . $name . '</li>';
    }

    private function renderInstalledModuleList(): void
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_mod`
             WHERE `mod_status` = :status
             AND `mod_powered` = :powered
             ORDER BY `module_name` ASC",
            ['status' => 'enabled', 'powered' => 'power_on']
        );

        if (!is_array($rows) || count($rows) === 0) {
            echo '<div class="alert alert-warning">No active modules found.</div>';
            return;
        }

        echo '<ul class="nav nav-list accordion-group">';
        foreach ($rows as $row) {
            $name = $this->e($row['module_name'] ?? '');
            echo '<li class="wicreate ui-draggable" data-module="' . $name . '"><i class="fa fa-cube"></i> ' . $name . '</li>';
        }
        echo '</ul>';
    }

    /** @return array<string,bool> */
    private function tableColumns(string $table): array
    {
        $table = preg_replace('/[^A-Za-z0-9_]/', '', $table) ?? '';

        if ($table === '') {
            return [];
        }

        if (isset($this->columnCache[$table])) {
            return $this->columnCache[$table];
        }

        $columns = [];

        try {
            $rows = $this->WIdb->select('SHOW COLUMNS FROM `' . $table . '`', []);
            foreach ($rows as $row) {
                $field = (string) ($row['Field'] ?? '');
                if ($field !== '') {
                    $columns[$field] = true;
                }
            }
        } catch (Throwable $e) {
            $columns = [];
        }

        $this->columnCache[$table] = $columns;
        return $columns;
    }

    /**
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    private function filterPayloadForTable(string $table, array $payload): array
    {
        $columns = $this->tableColumns($table);

        if ($columns === []) {
            return $payload;
        }

        return array_intersect_key($payload, $columns);
    }

    private function sanitizeModuleName(string $value): string
    {
        return preg_replace('/[^A-Za-z0-9_-]/', '', trim($value)) ?? '';
    }

    private function debugMessage(string $message, string $type = 'danger'): void
    {
        if (!$this->debugMode) {
            return;
        }

        echo '<div class="alert alert-' . $this->e($type) . '">' . $this->e($message) . '</div>';
    }

    private function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
