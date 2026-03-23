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

    /**
     * Development-safe toggle.
     * Set to false later if you want completely silent frontend failures.
     */
    private bool $debugMode = true;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();

        $this->moduleRoot   = dirname(dirname(dirname(__FILE__))) . '/WIModule/';
        $this->elementsPath = $this->moduleRoot . 'elements/';
        $this->modulesPath  = $this->moduleRoot . 'modules/';
        $this->pagesPath    = $this->moduleRoot . 'pages/';
        $this->testmodsPath = $this->moduleRoot . 'testmods/';
    }

    /*
    |--------------------------------------------------------------------------
    | Core helpers
    |--------------------------------------------------------------------------
    */

    private function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    private function cleanName(mixed $value): string
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '', (string) $value) ?? '';
    }

    private function currentPageNumber(): int
    {
        $page = $_POST['page'] ?? 1;
        $page = filter_var($page, FILTER_VALIDATE_INT);

        return ($page && $page > 0) ? (int) $page : 1;
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
        if ($items === false) {
            return [];
        }

        $folders = [];

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') {
                continue;
            }

            $fullPath = rtrim($dir, '/') . '/' . $item;

            if (is_dir($fullPath)) {
                $folders[] = $item;
            }
        }

        natcasesort($folders);

        return array_values($folders);
    }

    private function fileExists(string $path): bool
    {
        return $path !== '' && is_file($path);
    }

    private function dirExists(string $path): bool
    {
        return $path !== '' && is_dir($path);
    }

    private function readJsonFile(string $path): array
    {
        if (!$this->fileExists($path)) {
            return [];
        }

        $raw = file_get_contents($path);
        if ($raw === false || trim($raw) === '') {
            return [];
        }

        $json = json_decode($raw, true);

        return is_array($json) ? $json : [];
    }

    private function readJsonMeta(string $folder, string $file): array
    {
        return $this->readJsonFile(rtrim($folder, '/') . '/' . $file);
    }

    private function findPreviewImage(string $folder): string
    {
        $preferred = [
            'preview.png',
            'preview.jpg',
            'preview.jpeg',
            'icon.png',
            'icon.jpg',
            'icon.jpeg',
            'thumb.png',
            'thumb.jpg',
            'thumb.jpeg',
        ];

        foreach ($preferred as $file) {
            $path = rtrim($folder, '/') . '/' . $file;
            if ($this->fileExists($path)) {
                return $file;
            }
        }

        $all = glob(rtrim($folder, '/') . '/*.{png,jpg,jpeg,gif,webp}', GLOB_BRACE);

        if (is_array($all) && isset($all[0])) {
            return basename($all[0]);
        }

        return '';
    }

    private function debugMessage(string $message, string $type = 'danger'): void
    {
        if (!$this->debugMode) {
            return;
        }

        echo '<div class="alert alert-' . $this->e($type) . '">' . $this->e($message) . '</div>';
    }

    private function normaliseStatus(?string $value, string $default = 'disabled'): string
    {
        $value = strtolower(trim((string) $value));

        return in_array($value, ['enabled', 'disabled'], true) ? $value : $default;
    }

    private function normalisePower(?string $value, string $default = 'power_off'): string
    {
        $value = strtolower(trim((string) $value));

        return in_array($value, ['power_on', 'power_off'], true) ? $value : $default;
    }

    private function getTypePath(string $type): string
    {
        return match ($type) {
            'element' => $this->elementsPath,
            'module'  => $this->modulesPath,
            'page'    => $this->pagesPath,
            default   => '',
        };
    }

    private function getJsonFilename(string $type): string
    {
        return match ($type) {
            'element' => 'element.json',
            'module'  => 'module.json',
            'page'    => 'page.json',
            default   => '',
        };
    }

    private function getRegistryTable(string $type): string
    {
        return match ($type) {
            'element' => 'wi_elements',
            'module'  => 'wi_mod',
            default   => '',
        };
    }

    private function getRegistryNameColumn(string $type): string
    {
        return match ($type) {
            'element' => 'element_name',
            'module'  => 'module_name',
            default   => '',
        };
    }

    private function getRegistryStatusColumn(string $type): string
    {
        return match ($type) {
            'element' => 'element_status',
            'module'  => 'mod_status',
            default   => '',
        };
    }

    private function getRegistryPowerColumn(string $type): string
    {
        return match ($type) {
            'element' => 'element_powered',
            'module'  => 'mod_powered',
            default   => '',
        };
    }

    private function getItemFolder(string $type, string $name): string
    {
        return rtrim($this->getTypePath($type), '/') . '/' . $name;
    }

    private function getMainFile(string $type, string $name): string
    {
        $folder = $this->getItemFolder($type, $name);

        return $folder . '/' . $name . '.php';
    }

    private function getUpdateManifest(string $type, string $name): array
    {
        $folder = $this->getItemFolder($type, $name);

        return $this->readJsonMeta($folder, 'update.json');
    }

    private function getItemManifest(string $type, string $name): array
    {
        $folder = $this->getItemFolder($type, $name);
        $jsonFile = $this->getJsonFilename($type);

        if ($jsonFile === '') {
            return [];
        }

        return $this->readJsonMeta($folder, $jsonFile);
    }

    private function getAttributeSchema(string $type, string $name): array
    {
        $manifest = $this->getItemManifest($type, $name);

        $attributes = $manifest['attributes'] ?? $manifest['options'] ?? $manifest['settings'] ?? [];

        return is_array($attributes) ? $attributes : [];
    }

    private function itemExistsOnDisk(string $type, string $name): bool
    {
        $name = $this->cleanName($name);
        if ($name === '') {
            return false;
        }

        $folder = $this->getItemFolder($type, $name);
        $file = $this->getMainFile($type, $name);

        return $this->dirExists($folder) && $this->fileExists($file);
    }

    private function validateConvention(string $type, string $name): array
    {
        $name = $this->cleanName($name);

        if ($name === '') {
            return [
                'valid'   => false,
                'message' => 'Invalid item name supplied.',
            ];
        }

        $folder = $this->getItemFolder($type, $name);
        $file = $this->getMainFile($type, $name);

        if (!$this->dirExists($folder)) {
            return [
                'valid'   => false,
                'message' => ucfirst($type) . ' folder not found: ' . $name,
            ];
        }

        if (!$this->fileExists($file)) {
            return [
                'valid'   => false,
                'message' => ucfirst($type) . ' file not found: ' . $name . '.php',
            ];
        }

        return [
            'valid'   => true,
            'message' => '',
            'folder'  => $folder,
            'file'    => $file,
            'class'   => $name,
        ];
    }

    private function includeClassFile(string $file): bool
    {
        if (!$this->fileExists($file)) {
            return false;
        }

        require_once $file;

        return true;
    }

    private function instantiateItem(string $className): ?object
    {
        if (!class_exists($className)) {
            return null;
        }

        try {
            return new $className();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function safeCall(object $object, string $method, array $args = []): bool
    {
        if (!method_exists($object, $method)) {
            return false;
        }

        try {
            call_user_func_array([$object, $method], $args);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function registryRow(string $type, string $name): ?array
    {
        $table = $this->getRegistryTable($type);
        $column = $this->getRegistryNameColumn($type);

        if ($table === '' || $column === '') {
            return null;
        }

        $rows = $this->WIdb->select(
            "SELECT * FROM `{$table}` WHERE `{$column}` = :name LIMIT 1",
            ['name' => $name]
        );

        return $rows[0] ?? null;
    }

    private function registryElement(string $name): ?array
    {
        return $this->registryRow('element', $name);
    }

    private function registryModule(string $name): ?array
    {
        return $this->registryRow('module', $name);
    }

    private function moduleContentRows(): array
    {
        return $this->WIdb->select("SELECT * FROM `wi_modules` ORDER BY `id` ASC");
    }

    private function updateRegistryState(string $type, string $name, string $status, string $power): bool
    {
        $table = $this->getRegistryTable($type);
        $nameColumn = $this->getRegistryNameColumn($type);
        $statusColumn = $this->getRegistryStatusColumn($type);
        $powerColumn = $this->getRegistryPowerColumn($type);

        if ($table === '' || $nameColumn === '' || $statusColumn === '' || $powerColumn === '') {
            return false;
        }

        $name = $this->cleanName($name);

        if ($name === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            $table,
            [
                $statusColumn => $this->normaliseStatus($status),
                $powerColumn  => $this->normalisePower($power),
            ],
            "`{$nameColumn}` = :name",
            ['name' => $name]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Metadata readers
    |--------------------------------------------------------------------------
    */

    private function elementMeta(string $elementName): array
    {
        $elementName = $this->cleanName($elementName);
        $folder = $this->getItemFolder('element', $elementName);
        $json = $this->getItemManifest('element', $elementName);
        $update = $this->getUpdateManifest('element', $elementName);
        $db = $this->registryElement($elementName);

        return [
            'name'             => $elementName,
            'title'            => $json['title'] ?? $elementName,
            'type'             => $json['type'] ?? ($db['element_type'] ?? 'Common Fields'),
            'group'            => $json['group'] ?? ($db['group'] ?? ''),
            'author'           => $json['author'] ?? ($db['element_author'] ?? 'Warner Infinity'),
            'description'      => $json['description'] ?? ($db['element_description'] ?? ''),
            'font'             => $json['icon_font'] ?? ($db['element_font'] ?? 'fa-cube'),
            'image'            => $json['image'] ?? $this->findPreviewImage($folder),
            'status'           => $this->normaliseStatus($db['element_status'] ?? 'disabled'),
            'powered'          => $this->normalisePower($db['element_powered'] ?? 'power_off'),
            'folder'           => $folder,
            'file'             => $this->getMainFile('element', $elementName),
            'class'            => $elementName,
            'source'           => 'file',
            'builder_visible'  => $json['builder_visible'] ?? true,
            'attributes'       => $this->getAttributeSchema('element', $elementName),
            'version'          => $json['version'] ?? ($update['version'] ?? ''),
            'update_available' => !empty($update),
        ];
    }

    private function moduleMeta(string $moduleName): array
    {
        $moduleName = $this->cleanName($moduleName);
        $folder = $this->getItemFolder('module', $moduleName);
        $json = $this->getItemManifest('module', $moduleName);
        $update = $this->getUpdateManifest('module', $moduleName);
        $db = $this->registryModule($moduleName);

        return [
            'name'             => $moduleName,
            'title'            => $json['title'] ?? $moduleName,
            'type'             => $json['category'] ?? $json['type'] ?? ($db['mod_type'] ?? 'Module'),
            'author'           => $json['author'] ?? ($db['mod_author'] ?? 'Warner Infinity'),
            'description'      => $json['description'] ?? ($db['mod_description'] ?? ''),
            'image'            => $json['image'] ?? $this->findPreviewImage($folder),
            'status'           => $this->normaliseStatus($db['mod_status'] ?? 'disabled'),
            'powered'          => $this->normalisePower($db['mod_powered'] ?? 'power_off'),
            'folder'           => $folder,
            'file'             => $this->getMainFile('module', $moduleName),
            'class'            => $moduleName,
            'source'           => 'file',
            'builder_visible'  => $json['builder_visible'] ?? true,
            'attributes'       => $this->getAttributeSchema('module', $moduleName),
            'version'          => $json['version'] ?? ($update['version'] ?? ''),
            'update_available' => !empty($update),
        ];
    }

    private function pageMeta(string $pageName): array
    {
        $pageName = $this->cleanName($pageName);
        $folder = $this->getItemFolder('page', $pageName);
        $json = $this->getItemManifest('page', $pageName);
        $update = $this->getUpdateManifest('page', $pageName);

        return [
            'name'             => $pageName,
            'title'            => $json['title'] ?? $pageName,
            'type'             => $json['type'] ?? 'Page',
            'author'           => $json['author'] ?? 'Warner Infinity',
            'description'      => $json['description'] ?? '',
            'image'            => $json['image'] ?? $this->findPreviewImage($folder),
            'folder'           => $folder,
            'file'             => $this->getMainFile('page', $pageName),
            'class'            => $pageName,
            'source'           => 'file',
            'builder_visible'  => $json['builder_visible'] ?? true,
            'attributes'       => $this->getAttributeSchema('page', $pageName),
            'version'          => $json['version'] ?? ($update['version'] ?? ''),
            'update_available' => !empty($update),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | UI helpers
    |--------------------------------------------------------------------------
    */

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
        $name = (string) ($meta['name'] ?? '');
        $installed = ((string) ($meta['status'] ?? 'disabled') === 'enabled');
        $powered = ((string) ($meta['powered'] ?? 'power_off') === 'power_on');

        $stateBadge = $availableMode
            ? $this->badge($installed ? 'Installed' : 'Ready to Install', $installed ? 'info' : 'warning')
            : $this->badge($installed ? 'Enabled' : 'Disabled', $installed ? 'success' : 'muted');

        $powerBadge = $this->badge($powered ? 'Powered' : 'Offline', $powered ? 'success' : 'muted');

        echo '<div class="col-md-4 col-sm-6 col-xs-12">
                <div class="wi-store-card">';

        $this->renderCardHeader(
            (string) ($meta['title'] ?? $name),
            $stateBadge,
            $powerBadge
        );

        echo '<div class="wi-store-card__body">
                ' . $this->previewHtml('WIAdmin/WIModule/elements/' . $name . '/', (string) ($meta['image'] ?? ''), (string) ($meta['title'] ?? $name)) . '

                <div class="wi-store-card__meta">';
        $this->renderMetaRow('Name', $name);
        $this->renderMetaRow('Type', (string) ($meta['type'] ?? 'Element'));
        $this->renderMetaRow('Group', (string) ($meta['group'] ?? ''));
        $this->renderMetaRow('Author', (string) ($meta['author'] ?? 'Warner Infinity'));
        echo '  </div>

                <div class="wi-store-card__description">
                    ' . $this->e($this->truncateText((string) ($meta['description'] ?? ''), 140)) . '
                </div>
              </div>

              <div class="wi-store-card__footer">';

        if ($availableMode) {
            if ($installed) {
                echo '<button type="button" class="btn btn-success" disabled>Installed</button>';
            } else {
                echo '<button type="button" class="btn btn-primary" onclick="WIMod.installElement(\'' . $this->e($name) . '\', \'' . $this->e((string) ($meta['author'] ?? '')) . '\')">Install</button>';
            }

            echo '<button type="button" class="btn btn-default" onclick="WIMod.uninstallElements(\'' . $this->e($name) . '\', \'' . $this->e((string) ($meta['author'] ?? '')) . '\')">Remove</button>';
        } else {
            echo '<button type="button" class="btn btn-success" onclick="WIMod.enableElement(\'' . $this->e($name) . '\')">Enable</button>
                  <button type="button" class="btn btn-default" onclick="WIMod.disableElement(\'' . $this->e($name) . '\')">Disable</button>
                  <button type="button" class="btn btn-danger" onclick="WIMod.uninstallElements(\'' . $this->e($name) . '\', \'' . $this->e((string) ($meta['author'] ?? '')) . '\')">Uninstall</button>';
        }

        echo '  </div>
              </div>
            </div>';
    }

    private function renderModuleCard(array $meta, bool $availableMode = false): void
    {
        $name = (string) ($meta['name'] ?? '');
        $installed = ((string) ($meta['status'] ?? 'disabled') === 'enabled');
        $powered = ((string) ($meta['powered'] ?? 'power_off') === 'power_on');

        $stateBadge = $availableMode
            ? $this->badge($installed ? 'Installed' : 'Ready to Install', $installed ? 'info' : 'warning')
            : $this->badge($installed ? 'Enabled' : 'Disabled', $installed ? 'success' : 'muted');

        $powerBadge = $this->badge($powered ? 'Powered' : 'Offline', $powered ? 'success' : 'muted');

        echo '<div class="col-md-4 col-sm-6 col-xs-12">
                <div class="wi-store-card">';

        $this->renderCardHeader(
            (string) ($meta['title'] ?? $name),
            $stateBadge,
            $powerBadge
        );

        echo '<div class="wi-store-card__body">
                ' . $this->previewHtml('WIAdmin/WIModule/modules/' . $name . '/', (string) ($meta['image'] ?? ''), (string) ($meta['title'] ?? $name)) . '

                <div class="wi-store-card__meta">';
        $this->renderMetaRow('Name', $name);
        $this->renderMetaRow('Type', (string) ($meta['type'] ?? 'Module'));
        $this->renderMetaRow('Author', (string) ($meta['author'] ?? 'Warner Infinity'));
        echo '  </div>

                <div class="wi-store-card__description">
                    ' . $this->e($this->truncateText((string) ($meta['description'] ?? ''), 140)) . '
                </div>
              </div>

              <div class="wi-store-card__footer">';

        if ($availableMode) {
            if ($installed) {
                echo '<button type="button" class="btn btn-success" disabled>Installed</button>';
            } else {
                echo '<button type="button" class="btn btn-primary" onclick="WIMod.install(\'' . $this->e($name) . '\', \'' . $this->e((string) ($meta['author'] ?? '')) . '\')">Install</button>';
            }

            echo '<button type="button" class="btn btn-default" onclick="WIMod.uninstall(\'' . $this->e($name) . '\', \'' . $this->e((string) ($meta['author'] ?? '')) . '\')">Remove</button>';
        } else {
            echo '<button type="button" class="btn btn-success" onclick="WIMod.enable(\'' . $this->e($name) . '\')">Enable</button>
                  <button type="button" class="btn btn-default" onclick="WIMod.disable(\'' . $this->e($name) . '\')">Disable</button>
                  <button type="button" class="btn btn-danger" onclick="WIMod.uninstall(\'' . $this->e($name) . '\', \'' . $this->e((string) ($meta['author'] ?? '')) . '\')">Uninstall</button>';
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

    public function install_mod(string $moduleName): bool
    {
        $moduleName = $this->cleanName($moduleName);

        if ($moduleName === '' || !$this->itemExistsOnDisk('module', $moduleName)) {
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
            'module_name'     => $moduleName,
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

    public function uninstall_mod(string $moduleName): bool
    {
        return $this->updateRegistryState('module', $moduleName, 'disabled', 'power_off');
    }

    public function active_available_mod(string $moduleName, string $enable = 'enabled'): bool
    {
        return $this->updateRegistryState('module', $moduleName, 'enabled', 'power_on');
    }

    public function deactive_available_mod(string $moduleName, string $disable = 'disabled'): bool
    {
        return $this->updateRegistryState('module', $moduleName, 'disabled', 'power_off');
    }

    public function installElement(string $elementName): bool
    {
        $elementName = $this->cleanName($elementName);

        if ($elementName === '' || !$this->itemExistsOnDisk('element', $elementName)) {
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
            'group'               => (string) ($meta['group'] ?? ''),
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

    public function unistall_Element(string $elementName): bool
    {
        return $this->updateRegistryState('element', $elementName, 'disabled', 'power_off');
    }

    public function activateAvailableElements(string $elementName, string $enable = 'enabled'): bool
    {
        return $this->updateRegistryState('element', $elementName, 'enabled', 'power_on');
    }

    public function deactivateAvailableElements(string $elementName, string $disable = 'disabled'): bool
    {
        return $this->updateRegistryState('element', $elementName, 'disabled', 'power_off');
    }

    /*
    |--------------------------------------------------------------------------
    | Cleaner aliases for future use
    |--------------------------------------------------------------------------
    */

    public function installModule(string $moduleName): bool
    {
        return $this->install_mod($moduleName);
    }

    public function uninstallModule(string $moduleName): bool
    {
        return $this->uninstall_mod($moduleName);
    }

    public function enableModule(string $moduleName): bool
    {
        return $this->active_available_mod($moduleName);
    }

    public function disableModule(string $moduleName): bool
    {
        return $this->deactive_available_mod($moduleName);
    }

    public function uninstallElement(string $elementName): bool
    {
        return $this->unistall_Element($elementName);
    }

    public function enableElement(string $elementName): bool
    {
        return $this->activateAvailableElements($elementName);
    }

    public function disableElement(string $elementName): bool
    {
        return $this->deactivateAvailableElements($elementName);
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
                $this->renderElementCard($this->elementMeta((string) $name), true);
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
                $this->renderModuleCard($this->moduleMeta((string) $name), true);
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

    public function getInstalledPages(): array
    {
        $folders = $this->scanFolders($this->pagesPath);
        $pages = [];

        foreach ($folders as $pageName) {
            $pages[] = $this->pageMeta((string) $pageName);
        }

        return $pages;
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
                'powered' => 'power_on',
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
                'powered' => 'power_on',
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

    public function dropElement(string $name): void
    {
        $name = $this->cleanName($name);

        if ($name === '') {
            $this->debugMessage('Invalid element name supplied.');
            return;
        }

        $validation = $this->validateConvention('element', $name);

        if (($validation['valid'] ?? false) !== true) {
            $this->debugMessage((string) ($validation['message'] ?? 'Element validation failed.'));
            return;
        }

        $file = (string) $validation['file'];
        $class = (string) $validation['class'];

        $this->includeClassFile($file);

        $obj = $this->instantiateItem($class);

        if (!$obj) {
            $this->debugMessage('Element class missing or failed to instantiate: ' . $name);
            return;
        }

        if ($this->safeCall($obj, 'defaults')) {
            return;
        }

        if ($this->safeCall($obj, 'render')) {
            return;
        }

        $this->debugMessage('Element loaded but has no defaults() or render() method.', 'warning');
    }

    public function dropColElement(string $name): void
    {
        $this->dropElement($name);
    }

    public function editDropElement(string $name, int $pageId = 1): void
    {
        unset($pageId);
        $this->dropElement($name);
    }

    public function createMod(string $contents, string $modName, string $layout = '', array $elements = [], string $columnPreset = ''): bool
    {
        $modName = trim($modName);

        if ($modName === '') {
            return false;
        }

        $payload = [
            'name'    => $modName,
            'content' => $contents,
        ];

        if ($layout !== '') {
            $payload['layout'] = $layout;
        }

        if (!empty($elements)) {
            $payload['elements'] = json_encode($elements);
        }

        if ($columnPreset !== '') {
            $payload['column_preset'] = $columnPreset;
        }

        return (bool) $this->WIdb->insert('wi_modules', $payload);
    }

    public function editContents(string $title, string $para, string $modName): bool
    {
        $modName = trim($modName);

        if ($modName === '') {
            return false;
        }

        return (bool) $this->WIdb->update(
            'wi_modules',
            [
                'title' => $title,
                'para'  => $para,
            ],
            '`name` = :name',
            ['name' => $modName]
        );
    }

    public function save_mod(string $modName, string $contents, string $content = ''): bool
    {
        $modName = trim($modName);

        if ($modName === '') {
            return false;
        }

        $payload = [
            'content' => $contents,
        ];

        if ($content !== '') {
            $payload['content_html'] = $content;
        }

        return (bool) $this->WIdb->update(
            'wi_modules',
            $payload,
            '`name` = :name',
            ['name' => $modName]
        );
    }

    private function renderElementGroupList(string $fallbackType, string $groupName = ''): void
    {
        $params = [
            'status'  => 'enabled',
            'powered' => 'power_on',
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
                'powered' => 'power_on',
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

    public function getMod(string $mod): void
    {
        $mod = $this->cleanName($mod);

        if ($mod === '') {
            $this->debugMessage('Invalid module name.');
            return;
        }

        $validation = $this->validateConvention('module', $mod);

        if (($validation['valid'] ?? false) !== true) {
            $this->debugMessage((string) ($validation['message'] ?? 'Module validation failed.'));
            return;
        }

        $file = (string) $validation['file'];
        $class = (string) $validation['class'];

        $this->includeClassFile($file);

        $obj = $this->instantiateItem($class);

        if (!$obj) {
            $this->debugMessage('Module class not found or failed to instantiate: ' . $mod);
            return;
        }

        if ($this->safeCall($obj, 'mod_name')) {
            return;
        }

        $this->debugMessage('Module method mod_name() not found: ' . $mod);
    }

    public function getModMain(string $mod, string $page, ?string $module = null): void
    {
        unset($module);

        $mod = $this->cleanName($mod);

        if ($mod === '') {
            $this->loadNotFound($page);
            return;
        }

        $validation = $this->validateConvention('page', $mod);

        if (($validation['valid'] ?? false) !== true) {
            $this->loadNotFound($page);
            return;
        }

        $file = (string) $validation['file'];
        $class = (string) $validation['class'];

        $this->includeClassFile($file);

        $obj = $this->instantiateItem($class);

        if (!$obj) {
            $this->loadNotFound($page);
            return;
        }

        if ($this->safeCall($obj, 'mod_name', [$page])) {
            return;
        }

        $this->loadNotFound($page);
    }

    private function loadNotFound(string $page): void
    {
        $validation = $this->validateConvention('page', 'notfound');

        if (($validation['valid'] ?? false) !== true) {
            $this->debugMessage('Notfound module missing.');
            return;
        }

        $file = (string) $validation['file'];
        $class = (string) $validation['class'];

        $this->includeClassFile($file);

        $obj = $this->instantiateItem($class);

        if (!$obj) {
            $this->debugMessage('Notfound class missing or failed to instantiate.');
            return;
        }

        if (!$this->safeCall($obj, 'mod_name', [$page])) {
            $this->debugMessage('Notfound method mod_name() missing.');
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Registry readers
    |--------------------------------------------------------------------------
    */

    public function InstallToggle(string $modName): string
    {
        $row = $this->registryModule($this->cleanName($modName));
        return $row['mod_status'] ?? 'disabled';
    }

    public function InstallElementToggle(string $elementName): string
    {
        $row = $this->registryElement($this->cleanName($elementName));
        return $row['element_powered'] ?? 'power_off';
    }

    public function moduleToggle(string $column, string $modName): mixed
    {
        $allowed = ['mod_status', 'mod_powered', 'module_name', 'mod_type', 'mod_author', 'mod_description'];

        if (!in_array($column, $allowed, true)) {
            return null;
        }

        $row = $this->registryModule($this->cleanName($modName));
        return $row[$column] ?? null;
    }

    public function elementToggle(string $column, string $elementName): mixed
    {
        $allowed = ['element_status', 'element_powered', 'element_name', 'element_type', 'element_author', 'element_description', 'element_font', 'group'];

        if (!in_array($column, $allowed, true)) {
            return null;
        }

        $row = $this->registryElement($this->cleanName($elementName));
        return $row[$column] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Attribute / manifest helpers for Phase 1 groundwork
    |--------------------------------------------------------------------------
    */

    public function getElementAttributes(string $elementName): array
    {
        return $this->getAttributeSchema('element', $this->cleanName($elementName));
    }

    public function getModuleAttributes(string $moduleName): array
    {
        return $this->getAttributeSchema('module', $this->cleanName($moduleName));
    }

    public function getPageAttributes(string $pageName): array
    {
        return $this->getAttributeSchema('page', $this->cleanName($pageName));
    }

    public function getElementManifest(string $elementName): array
    {
        return $this->getItemManifest('element', $this->cleanName($elementName));
    }

    public function getModuleManifest(string $moduleName): array
    {
        return $this->getItemManifest('module', $this->cleanName($moduleName));
    }

    public function getPageManifest(string $pageName): array
    {
        return $this->getItemManifest('page', $this->cleanName($pageName));
    }

    public function getElementUpdateManifest(string $elementName): array
    {
        return $this->getUpdateManifest('element', $this->cleanName($elementName));
    }

    public function getModuleUpdateManifest(string $moduleName): array
    {
        return $this->getUpdateManifest('module', $this->cleanName($moduleName));
    }

    public function getPageUpdateManifest(string $pageName): array
    {
        return $this->getUpdateManifest('page', $this->cleanName($pageName));
    }
}