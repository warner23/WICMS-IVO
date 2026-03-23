<?php
declare(strict_types=1);

#[\AllowDynamicProperties]
class WIModules
{
    private WIdb $WIdb;
    private string $moduleRoot;
    private string $elementsPath;
    private string $modulesPath;
    private string $pagesPath;
    private string $testmodsPath;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();

        $this->moduleRoot   = dirname(dirname(dirname(__FILE__))) . '/WIModule/';
        $this->elementsPath = $this->moduleRoot . 'elements/';
        $this->modulesPath  = $this->moduleRoot . 'modules/';
        $this->pagesPath    = $this->moduleRoot . 'pages/';
        $this->testmodsPath = $this->moduleRoot . 'testmods/';
    }

    private function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    private function cleanName($value): string
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '', (string)$value) ?? '';
    }

    private function currentPageNumber(): int
    {
        $page = $_POST['page'] ?? 1;
        $page = filter_var($page, FILTER_VALIDATE_INT);

        return ($page && $page > 0) ? $page : 1;
    }

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
            'total_pages' => (int) ceil($total / max(1, $perPage))
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

            echo '<button
                    type="button"
                    class="' . $active . '"
                    data-action="' . $this->e($action) . '"
                    data-page="' . $i . '"
                    data-target="' . $this->e($targetSelector) . '"
                  >' . $i . '</button>';
        }

        echo '</div>';
    }

    private function scanFolders(string $dir): array
    {
        if (!is_dir($dir)) {
            return [];
        }

        $items = scandir($dir);
        $folders = [];

        foreach ($items as $item) {
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

    private function readJsonMeta(string $folder, string $file): array
    {
        $path = rtrim($folder, '/') . '/' . $file;

        if (!file_exists($path)) {
            return [];
        }

        $raw = file_get_contents($path);
        $json = json_decode((string) $raw, true);

        return is_array($json) ? $json : [];
    }

    private function findPreviewImage(string $folder): string
    {
        $preferred = ['preview.png', 'preview.jpg', 'preview.jpeg', 'icon.png', 'icon.jpg', 'icon.jpeg'];

        foreach ($preferred as $file) {
            if (file_exists($folder . '/' . $file)) {
                return $file;
            }
        }

        $all = glob($folder . '/*.{png,jpg,jpeg,gif,webp}', GLOB_BRACE);

        if ($all && isset($all[0])) {
            return basename($all[0]);
        }

        return '';
    }

    private function registryElement(string $name): ?array
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_elements` WHERE `element_name` = :name LIMIT 1",
            ['name' => $name]
        );

        return $rows[0] ?? null;
    }

    private function registryModule(string $name): ?array
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_mod` WHERE `module_name` = :name LIMIT 1",
            ['name' => $name]
        );

        return $rows[0] ?? null;
    }

    private function moduleContentRows(): array
    {
        return $this->WIdb->select("SELECT * FROM `wi_modules` ORDER BY `id` ASC");
    }

    private function elementMeta(string $elementName): array
    {
        $folder = $this->elementsPath . $elementName;
        $json = $this->readJsonMeta($folder, 'element.json');
        $db = $this->registryElement($elementName);

        return [
            'name'        => $elementName,
            'title'       => $json['title'] ?? $elementName,
            'type'        => $json['type'] ?? ($db['element_type'] ?? 'Common Fields'),
            'group'       => $json['group'] ?? ($db['group'] ?? ''),
            'author'      => $json['author'] ?? ($db['element_author'] ?? 'Warner Infinity'),
            'description' => $json['description'] ?? ($db['element_description'] ?? ''),
            'font'        => $json['icon_font'] ?? ($db['element_font'] ?? 'fa-cube'),
            'image'       => $json['image'] ?? $this->findPreviewImage($folder),
            'status'      => $db['element_status'] ?? 'disabled',
            'powered'     => $db['element_powered'] ?? 'power_off',
            'folder'      => $folder
        ];
    }

    private function moduleMeta(string $moduleName): array
    {
        $folder = $this->modulesPath . $moduleName;
        $json = $this->readJsonMeta($folder, 'module.json');
        $db = $this->registryModule($moduleName);

        return [
            'name'        => $moduleName,
            'title'       => $json['title'] ?? $moduleName,
            'type'        => $json['category'] ?? ($db['mod_type'] ?? 'Module'),
            'author'      => $json['author'] ?? ($db['mod_author'] ?? 'Warner Infinity'),
            'description' => $json['description'] ?? ($db['mod_description'] ?? ''),
            'image'       => $json['image'] ?? $this->findPreviewImage($folder),
            'status'      => $db['mod_status'] ?? 'disabled',
            'powered'     => $db['mod_powered'] ?? 'power_off',
            'folder'      => $folder
        ];
    }

    private function renderCardHeader(string $title, string $badgeLeft = '', string $badgeRight = ''): void
    {
        echo '<div class="wi-store-card__header">
                <div class="wi-store-card__title-wrap">
                    <h4 class="wi-store-card__title">' . $this->e($title) . '</h4>
                </div>
                <div class="wi-store-card__badges">
                    ' . $badgeLeft . '
                    ' . $badgeRight . '
                </div>
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

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        return mb_substr($text, 0, $limit - 3) . '...';
    }

    private function previewHtml(string $baseWebPath, string $file, string $alt): string
    {
        if ($file === '') {
            return '<div class="wi-store-card__preview wi-store-card__preview--empty">
                        <span>No Preview</span>
                    </div>';
        }

        return '<div class="wi-store-card__preview">
                    <img src="' . $this->e($baseWebPath . $file) . '" alt="' . $this->e($alt) . '" class="wi-store-card__image">
                </div>';
    }

    private function renderMetaRow(string $label, string $value): void
    {
        echo '<div class="wi-store-card__meta-row">
                <span class="wi-store-card__meta-label">' . $this->e($label) . '</span>
                <span class="wi-store-card__meta-value">' . $this->e($value) . '</span>
              </div>';
    }

    private function emptyState(string $title, string $text): void
    {
        echo '<div class="col-md-12">
                <div class="wi-store-empty">
                    <h4>' . $this->e($title) . '</h4>
                    <p>' . $this->e($text) . '</p>
                </div>
              </div>';
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

        echo '<div class="col-md-4 col-sm-6 col-xs-12">
                <div class="wi-store-card">';

        $this->renderCardHeader(
            (string) $meta['title'],
            $stateBadge,
            $powerBadge
        );

        echo '<div class="wi-store-card__body">
                ' . $this->previewHtml('WIAdmin/WIModule/elements/' . $name . '/', (string) $meta['image'], (string) $meta['title']) . '

                <div class="wi-store-card__meta">';
        $this->renderMetaRow('Name', $name);
        $this->renderMetaRow('Type', (string) $meta['type']);
        $this->renderMetaRow('Group', (string) $meta['group']);
        $this->renderMetaRow('Author', (string) $meta['author']);
        echo '  </div>

                <div class="wi-store-card__description">
                    ' . $this->e($this->truncateText((string) $meta['description'], 140)) . '
                </div>
              </div>

              <div class="wi-store-card__footer">';

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

        echo '  </div>
              </div>
            </div>';
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

        echo '<div class="col-md-4 col-sm-6 col-xs-12">
                <div class="wi-store-card">';

        $this->renderCardHeader(
            (string) $meta['title'],
            $stateBadge,
            $powerBadge
        );

        echo '<div class="wi-store-card__body">
                ' . $this->previewHtml('WIAdmin/WIModule/modules/' . $name . '/', (string) $meta['image'], (string) $meta['title']) . '

                <div class="wi-store-card__meta">';
        $this->renderMetaRow('Name', $name);
        $this->renderMetaRow('Type', (string) $meta['type']);
        $this->renderMetaRow('Author', (string) $meta['author']);
        echo '  </div>

                <div class="wi-store-card__description">
                    ' . $this->e($this->truncateText((string) $meta['description'], 140)) . '
                </div>
              </div>

              <div class="wi-store-card__footer">';

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

        echo '  </div>
              </div>
            </div>';
    }

    /*
    |--------------------------------------------------------------------------
    | Registry install / uninstall / power actions
    |--------------------------------------------------------------------------
    */

    public function install_mod($moduleName): bool
    {
        $moduleName = $this->cleanName($moduleName);

        if ($moduleName === '') {
            return false;
        }

        $meta = $this->moduleMeta($moduleName);
        $existing = $this->registryModule($moduleName);

        $payload = [
            'mod_status'      => 'enabled',
            'mod_powered'     => 'power_on',
            'mod_type'        => (string) ($meta['type'] ?? 'Module'),
            'mod_author'      => (string) ($meta['author'] ?? 'Warner Infinity'),
            'mod_description' => (string) ($meta['description'] ?? ''),
            'module_name'     => $moduleName
        ];

        if ($existing) {
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
        $moduleName = $this->cleanName($moduleName);

        if ($moduleName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_mod',
            [
                'mod_status'  => 'disabled',
                'mod_powered' => 'power_off'
            ],
            '`module_name` = :name',
            ['name' => $moduleName]
        );
    }

    public function active_available_mod($moduleName, $enable = 'enabled'): bool
    {
        $moduleName = $this->cleanName($moduleName);

        if ($moduleName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_mod',
            [
                'mod_status'  => 'enabled',
                'mod_powered' => 'power_on'
            ],
            '`module_name` = :name',
            ['name' => $moduleName]
        );
    }

    public function deactive_available_mod($moduleName, $disable = 'disabled'): bool
    {
        $moduleName = $this->cleanName($moduleName);

        if ($moduleName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_mod',
            [
                'mod_status'  => 'disabled',
                'mod_powered' => 'power_off'
            ],
            '`module_name` = :name',
            ['name' => $moduleName]
        );
    }

    public function installElement($elementName): bool
    {
        $elementName = $this->cleanName($elementName);

        if ($elementName === '') {
            return false;
        }

        $meta = $this->elementMeta($elementName);
        $existing = $this->registryElement($elementName);

        $payload = [
            'element_status'      => 'enabled',
            'element_powered'     => 'power_on',
            'element_type'        => (string) ($meta['type'] ?? 'Common Fields'),
            'element_author'      => (string) ($meta['author'] ?? 'Warner Infinity'),
            'element_name'        => $elementName,
            'element_description' => (string) ($meta['description'] ?? ''),
            'element_font'        => (string) ($meta['font'] ?? 'fa-cube'),
            'group'               => (string) ($meta['group'] ?? '')
        ];

        if ($existing) {
            return (bool) $this->WIdb->update(
                'wi_elements',
                $payload,
                '`element_name` = :name',
                ['name' => $elementName]
            );
        }

        return (bool) $this->WIdb->insert('wi_elements', $payload);
    }

    public function unistall_Element($elementName): bool
    {
        $elementName = $this->cleanName($elementName);

        if ($elementName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_elements',
            [
                'element_status'  => 'disabled',
                'element_powered' => 'power_off'
            ],
            '`element_name` = :name',
            ['name' => $elementName]
        );
    }

    public function activateAvailableElements($elementName, $enable = 'enabled'): bool
    {
        $elementName = $this->cleanName($elementName);

        if ($elementName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_elements',
            [
                'element_status'  => 'enabled',
                'element_powered' => 'power_on'
            ],
            '`element_name` = :name',
            ['name' => $elementName]
        );
    }

    public function deactivateAvailableElements($elementName, $disable = 'disabled'): bool
    {
        $elementName = $this->cleanName($elementName);

        if ($elementName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_elements',
            [
                'element_status'  => 'disabled',
                'element_powered' => 'power_off'
            ],
            '`element_name` = :name',
            ['name' => $elementName]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Lists for tabs
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
                $this->renderElementCard($this->elementMeta($name), true);
            }
        }

        echo '</div>';

        $this->renderPager('NextElementsPage', '#elementsStoreList', $pager['page'], $pager['total_pages']);
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
                $this->renderModuleCard($this->moduleMeta($name), true);
            }
        }

        echo '</div>';

        $this->renderPager('NextModPage', '#modulesStoreList', $pager['page'], $pager['total_pages']);
    }

    public function getElements(): void
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_elements` ORDER BY `element_id` ASC"
        );

        if (!is_array($rows) || count($rows) === 0) {
            echo '<div class="row">';
            $this->emptyState('No installed elements', 'There are currently no installed elements to manage.');
            echo '</div>';
            return;
        }

        $names = [];
        foreach ($rows as $row) {
            $names[] = $row['element_name'];
        }

        $pager = $this->paginateArray($names, 12);

        echo '<div class="row">';
        foreach ($pager['items'] as $name) {
            $this->renderElementCard($this->elementMeta((string) $name), false);
        }
        echo '</div>';

        $this->renderPager('NextInstalledElementsPage', '#installedElementsList', $pager['page'], $pager['total_pages']);
    }

    public function getModules(): void
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_mod` ORDER BY `mod_id` ASC"
        );

        if (!is_array($rows) || count($rows) === 0) {
            echo '<div class="row">';
            $this->emptyState('No installed modules', 'There are currently no installed modules to manage.');
            echo '</div>';
            return;
        }

        $names = [];
        foreach ($rows as $row) {
            $names[] = $row['module_name'];
        }

        $pager = $this->paginateArray($names, 12);

        echo '<div class="row">';
        foreach ($pager['items'] as $name) {
            $this->renderModuleCard($this->moduleMeta((string) $name), false);
        }
        echo '</div>';

        $this->renderPager('NextInstalledModulesPage', '#installedModulesList', $pager['page'], $pager['total_pages']);
    }

    public function getInstalledModules(): void
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_mod` WHERE `mod_status` = :status ORDER BY `mod_id` ASC",
            ['status' => 'enabled']
        );

        if (!is_array($rows) || count($rows) === 0) {
            echo '<div class="row">';
            $this->emptyState('No active modules', 'There are currently no enabled modules.');
            echo '</div>';
            return;
        }

        $names = [];
        foreach ($rows as $row) {
            $names[] = $row['module_name'];
        }

        $pager = $this->paginateArray($names, 12);

        echo '<div class="row">';
        foreach ($pager['items'] as $name) {
            $this->renderModuleCard($this->moduleMeta((string) $name), false);
        }
        echo '</div>';

        $this->renderPager('NextInstalledModulesPage', '#installedModulesList', $pager['page'], $pager['total_pages']);
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
            $name = $row['name'] ?? $row['module_name'] ?? 'Unnamed Module';
            echo '<li class="wicreate ui-draggable" data-module="' . $this->e($name) . '">' . $this->e($name) . '</li>';
        }
        echo '</ul>';

        $this->renderPager('NextBuilderModulesPage', '#builderModulesList', $pager['page'], $pager['total_pages']);
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
        $this->getInstalledModules();
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
        $this->getInstalledModules();
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
                'powered' => 'power_on'
            ]
        );

        if (!is_array($rows) || count($rows) === 0) {
            echo '<div class="alert alert-warning">No active elements found.</div>';
            return;
        }

        echo '<div class="row">';
        foreach ($rows as $row) {
            $this->renderElementCard($this->elementMeta((string) $row['element_name']), false);
        }
        echo '</div>';
    }

    public function ActiveElementsBase(): void
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_elements`
             WHERE `element_status` = :status
             AND `element_powered` = :powered
             ORDER BY `element_type` ASC, `element_name` ASC",
            [
                'status'  => 'enabled',
                'powered' => 'power_on'
            ]
        );

        if (!is_array($rows) || count($rows) === 0) {
            echo '<div class="alert alert-warning">No active base elements found.</div>';
            return;
        }

        echo '<ul class="nav nav-list accordion-group">';
        foreach ($rows as $row) {
            $name = $this->e($row['element_name'] ?? '');
            $type = $this->e($row['element_type'] ?? 'Element');
            $font = $this->e($row['element_font'] ?? 'fa-cube');

            echo '<li class="wicreate draggable-element" data-element="' . $name . '" data-type="' . $type . '">
                    <i class="fa ' . $font . '"></i> ' . $name . '
                  </li>';
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

    /*
    |--------------------------------------------------------------------------
    | Builder drop/create/edit hooks
    |--------------------------------------------------------------------------
    */

    public function dropElement($name): void
    {
        $name = $this->cleanName($name);

        if ($name === '') {
            return;
        }

        $file = $this->elementsPath . $name . '/' . $name . '.php';

        if (!file_exists($file)) {
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

        return (bool) $this->WIdb->insert('wi_modules', [
            'name'    => $modName,
            'content' => (string) $contents
        ]);
    }

    public function editContents($title, $para, $modName): bool
    {
        $modName = trim((string) $modName);

        if ($modName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_modules',
            [
                'title' => (string) $title,
                'para'  => (string) $para
            ],
            '`name` = :name',
            ['name' => $modName]
        );
    }

    public function save_mod($modName, $contents, $content = ''): bool
    {
        $modName = trim((string) $modName);

        if ($modName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_modules',
            [
                'content' => (string) $contents
            ],
            '`name` = :name',
            ['name' => $modName]
        );
    }

    private function renderElementGroupList(string $fallbackType, string $groupName = ''): void
    {
        $params = [
            'status'  => 'enabled',
            'powered' => 'power_on'
        ];

        $sql = "SELECT * FROM `wi_elements`
                WHERE `element_status` = :status
                AND `element_powered` = :powered";

        if ($groupName !== '') {
            $sql .= " AND (`group` = :grp OR `element_type` = :etype)";
            $params['grp'] = $groupName;
            $params['etype'] = $fallbackType;
        } else {
            $sql .= " AND `element_type` = :etype";
            $params['etype'] = $fallbackType;
        }

        $sql .= " ORDER BY `element_name` ASC";

        $rows = $this->WIdb->select($sql, $params);

        if (!is_array($rows) || count($rows) === 0) {
            echo '<div class="alert alert-warning">No active elements found in this group.</div>';
            return;
        }

        echo '<ul class="nav nav-list accordion-group">';

        foreach ($rows as $row) {
            $name = $this->e($row['element_name'] ?? '');
            $type = $this->e($row['element_type'] ?? 'Element');
            $font = $this->e($row['element_font'] ?? 'fa-cube');

            echo '<li class="wicreate draggable-element"
                     data-element="' . $name . '"
                     data-type="' . $type . '">
                    <i class="fa ' . $font . '"></i> ' . $name . '
                  </li>';
        }

        echo '</ul>';
    }

    private function renderInstalledModuleList(): void
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_mod`
             WHERE `mod_status` = :status
             AND `mod_powered` = :powered
             ORDER BY `module_name` ASC",
            [
                'status'  => 'enabled',
                'powered' => 'power_on'
            ]
        );

        if (!is_array($rows) || count($rows) === 0) {
            echo '<div class="alert alert-warning">No active modules found.</div>';
            return;
        }

        echo '<ul class="nav nav-list accordion-group">';

        foreach ($rows as $row) {
            $name = $this->e($row['module_name'] ?? '');
            $type = $this->e($row['mod_type'] ?? 'Module');

            echo '<li class="wicreate draggable-module"
                     data-module="' . $name . '"
                     data-type="' . $type . '">
                    <i class="fa fa-cubes"></i> ' . $name . '
                  </li>';
        }

        echo '</ul>';
    }

    /*
    |--------------------------------------------------------------------------
    | Frontend loader compatibility
    |--------------------------------------------------------------------------
    */

    public function getMod($mod): void
    {
        $mod = $this->cleanName($mod);

        if ($mod === '') {
            echo '<div class="alert alert-danger">Invalid module name.</div>';
            return;
        }

        $file = $this->modulesPath . $mod . '/' . $mod . '.php';

        if (!file_exists($file)) {
            echo '<div class="alert alert-danger">Module file not found: ' . $this->e($mod) . '</div>';
            return;
        }

        require_once $file;

        if (!class_exists($mod)) {
            echo '<div class="alert alert-danger">Module class not found: ' . $this->e($mod) . '</div>';
            return;
        }

        $obj = new $mod();

        if (method_exists($obj, 'mod_name')) {
            $obj->mod_name();
            return;
        }

        echo '<div class="alert alert-danger">Module method mod_name() not found: ' . $this->e($mod) . '</div>';
    }

    public function getModMain($mod, $page, $module = null): void
    {
        $mod = $this->cleanName($mod);

        if ($mod === '') {
            $this->loadNotFound($page);
            return;
        }

        $file = $this->pagesPath . $mod . '/' . $mod . '.php';

        if (!file_exists($file)) {
            $this->loadNotFound($page);
            return;
        }

        require_once $file;

        if (!class_exists($mod)) {
            $this->loadNotFound($page);
            return;
        }

        $obj = new $mod();

        if (method_exists($obj, 'mod_name')) {
            $obj->mod_name($page);
            return;
        }

        $this->loadNotFound($page);
    }

    private function loadNotFound($page): void
    {
        $file = $this->pagesPath . 'notfound/notfound.php';

        if (!file_exists($file)) {
            echo '<div class="alert alert-danger">Notfound module missing.</div>';
            return;
        }

        require_once $file;

        if (!class_exists('notfound')) {
            echo '<div class="alert alert-danger">Notfound class missing.</div>';
            return;
        }

        $obj = new notfound();

        if (method_exists($obj, 'mod_name')) {
            $obj->mod_name($page);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Registry readers
    |--------------------------------------------------------------------------
    */

    public function InstallToggle(string $modName): string
    {
        $row = $this->registryModule($modName);
        return $row['mod_status'] ?? 'disabled';
    }

    public function InstallElementToggle(string $elementName): string
    {
        $row = $this->registryElement($elementName);
        return $row['element_powered'] ?? 'power_off';
    }

    public function moduleToggle(string $column, string $modName)
    {
        $allowed = ['mod_status', 'mod_powered', 'module_name', 'mod_type', 'mod_author', 'mod_description'];

        if (!in_array($column, $allowed, true)) {
            return null;
        }

        $row = $this->registryModule($modName);
        return $row[$column] ?? null;
    }

    public function elementToggle(string $column, string $elementName)
    {
        $allowed = ['element_status', 'element_powered', 'element_name', 'element_type', 'element_author', 'element_description', 'element_font', 'group'];

        if (!in_array($column, $allowed, true)) {
            return null;
        }

        $row = $this->registryElement($elementName);
        return $row[$column] ?? null;
    }
}
?>