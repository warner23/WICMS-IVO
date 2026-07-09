<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS
| File: /WIAdmin/WICore/WIClass/WIPlugin.php
| Type: Plugin Management Service
| Layer: Shared Core Service
| Version: 2.4.5
| Status: Production Ready - Registry Self-Heal + Diagnostic Install Patch
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Installs/enables/disables WICMS plugins using the shared WIdb layer only.
| The marketplace card flow now separates commercial status from local package
| availability. Paid/subscription plugins already present in WIAdmin/WIPlugin
| show Install, not Download from WILabs. The package registry now
| self-heals older wi_plugin schemas with isolated metadata queries before installing or activating packs, and returns exact registry write failures instead of a generic install error.
|--------------------------------------------------------------------------
*/

#[\AllowDynamicProperties]
class WIPlugin
{
    private WIdb $WIdb;
    private WIPluginStore $store;
    private WIPluginCommerce $commerce;
    private WIPluginLicense $licenseService;
    private WIPluginInvoice $invoiceService;
    private WIPluginSubscription $subscriptionService;
    private string $pluginRoot;
    private string $lastError = '';
    private array $lastResult = [];

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->store = new WIPluginStore();
        $this->commerce = new WIPluginCommerce();
        $this->licenseService = new WIPluginLicense();
        $this->invoiceService = new WIPluginInvoice();
        $this->subscriptionService = new WIPluginSubscription();
        $this->pluginRoot = dirname(dirname(dirname(__FILE__))) . '/WIPlugin/';
    }

    public function getLastError(): string
    {
        return $this->lastError;
    }

    public function getLastResult(): array
    {
        return $this->lastResult;
    }

    private function setError(string $message): bool
    {
        $this->lastError = $message;
        $this->logPluginError($message);
        return false;
    }

    private function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    private function cleanName($value): string
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '', (string) $value) ?? '';
    }

    private function normalisePluginIdentity(string $value): string
    {
        $value = strtolower(trim($value));
        return preg_replace('/[^a-z0-9]+/', '', $value) ?? '';
    }

    /**
     * Resolve the canonical plugin registry row from wi_plugin only.
     *
     * Important: the filesystem folder or marketplace/store manifest can prove that a
     * local package exists, but only wi_plugin can prove Installed/Active state.
     * This method deliberately normalises names because older package SQL and
     * manifests have used both WIInspectorPro and wiinspectorpro style slugs.
     */
    private function registryRow(string $pluginSlug): ?array
    {
        if (!$this->tableExists('wi_plugin')) {
            return null;
        }

        $pluginSlug = $this->cleanName($pluginSlug);
        if ($pluginSlug === '') {
            return null;
        }

        $target = $this->normalisePluginIdentity($pluginSlug);
        $hasSlugColumn = $this->columnExists('wi_plugin', 'plugin_slug');

        // Older Showcase databases may not have plugin_slug yet. Do not let a
        // lookup fail before install() has had a chance to self-heal the schema.
        if ($hasSlugColumn) {
            $rows = $this->WIdb->select(
                "SELECT * FROM `wi_plugin` WHERE `plugin_slug` = :slug OR `plugin_name` = :name LIMIT 2",
                ['slug' => $pluginSlug, 'name' => $pluginSlug]
            );
        } else {
            $rows = $this->WIdb->select(
                "SELECT * FROM `wi_plugin` WHERE `plugin_name` = :name LIMIT 2",
                ['name' => $pluginSlug]
            );
        }

        foreach ($rows as $row) {
            $rowSlug = $hasSlugColumn ? $this->normalisePluginIdentity((string) ($row['plugin_slug'] ?? '')) : '';
            $rowName = $this->normalisePluginIdentity((string) ($row['plugin_name'] ?? ''));

            if ($rowSlug === $target || $rowName === $target) {
                return $row;
            }
        }

        // Fallback scan is intentional: some legacy rows are lowercase while folder
        // names are camel-case. This still only trusts wi_plugin rows, never manifests.
        $allRows = $this->WIdb->select(
            "SELECT * FROM `wi_plugin` ORDER BY `plugin_id` ASC"
        );

        foreach ($allRows as $row) {
            $rowSlug = $hasSlugColumn ? $this->normalisePluginIdentity((string) ($row['plugin_slug'] ?? '')) : '';
            $rowName = $this->normalisePluginIdentity((string) ($row['plugin_name'] ?? ''));

            if ($rowSlug === $target || $rowName === $target) {
                return $row;
            }
        }

        return null;
    }

    private function previewHtml(string $pluginSlug, string $file, string $alt): string
    {
        $pluginSlug = $this->cleanName($pluginSlug);
        $pluginDir = $this->pluginRoot . $pluginSlug;
        $candidateFiles = [];

        if (trim($file) !== '') {
            $candidateFiles[] = trim($file);
        }

        $candidateFiles = array_merge($candidateFiles, [
            'preview.png', 'preview.jpg', 'preview.jpeg', 'preview.webp',
            'assets/preview.svg', 'assets/preview.png', 'assets/preview.jpg',
            'assets/banner.svg', 'assets/banner.png', 'icon.png', 'assets/icon.svg', 'assets/icon.png',
        ]);

        foreach (array_unique($candidateFiles) as $candidateFile) {
            $candidateFile = ltrim(str_replace('\\', '/', (string) $candidateFile), '/');
            if ($candidateFile === '') {
                continue;
            }

            $path = $this->normalisePackagePath($pluginDir, $candidateFile);
            if ($path === null || !is_file($path)) {
                continue;
            }

            return '<div class="wi-plugin-card__media wi-plugin-card__media--image"><img src="WIPlugin/' . $this->e($pluginSlug) . '/' . $this->e($candidateFile) . '" alt="' . $this->e($alt) . '"></div>';
        }

        $initials = $this->pluginInitials($alt !== '' ? $alt : $pluginSlug);

        return '<div class="wi-plugin-card__media wi-plugin-card__media--fallback" aria-label="' . $this->e($alt) . ' preview"><span class="wi-plugin-card__fallback-icon"><i class="fa fa-puzzle-piece" aria-hidden="true"></i></span><strong>' . $this->e($initials) . '</strong><small>' . $this->e($alt !== '' ? $alt : $pluginSlug) . '</small></div>';
    }

    private function pluginInitials(string $name): string
    {
        $name = trim(preg_replace('/[^A-Za-z0-9 ]+/', ' ', $name) ?? '');
        if ($name === '') {
            return 'WI';
        }

        $parts = preg_split('/\s+/', $name) ?: [];
        $initials = '';

        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $initials .= strtoupper(substr($part, 0, 1));
            if (strlen($initials) >= 3) {
                break;
            }
        }

        return $initials !== '' ? $initials : strtoupper(substr($name, 0, 2));
    }

    private function badge(string $text, string $class = 'default'): string
    {
        return '<span class="label label-' . $this->e($class) . ' wi-plugin-badge">' . $this->e($text) . '</span>';
    }

    private function pluginMeta(string $pluginSlug): array
    {
        return $this->store->mergePluginData($pluginSlug);
    }

    private function localPackageAvailable(string $pluginSlug): bool
    {
        $pluginSlug = $this->cleanName($pluginSlug);

        if ($pluginSlug === '') {
            return false;
        }

        $pluginDir = rtrim($this->pluginRoot, '/\\') . '/' . $pluginSlug;

        return is_dir($pluginDir) && (
            is_file($pluginDir . '/plugin.json')
            || is_file($pluginDir . '/pack.json')
            || is_file($pluginDir . '/config/pack.json')
        );
    }

    private function pluginRegistryState(string $pluginSlug): array
    {
        $row = $this->registryRow($pluginSlug);

        if ($row === null) {
            return [
                'row' => [],
                'state' => 'available',
                'label' => 'Available',
                'badge' => 'warning',
                'registered' => false,
                'active' => false,
                'db_status' => 'not_registered',
            ];
        }

        $status = strtolower(trim((string) ($row['plugin_status'] ?? 'disabled')));

        if (in_array($status, ['enabled', 'active'], true)) {
            return [
                'row' => $row,
                'state' => 'active',
                'label' => 'Active',
                'badge' => 'success',
                'registered' => true,
                'active' => true,
                'db_status' => $status,
            ];
        }

        return [
            'row' => $row,
            'state' => 'installed',
            'label' => 'Installed',
            'badge' => 'primary',
            'registered' => true,
            'active' => false,
            'db_status' => $status !== '' ? $status : 'disabled',
        ];
    }

    private function normaliseTypeLabel(string $type): string
    {
        $type = strtolower(trim(str_replace(['-', '_'], ' ', $type)));
        $map = ['industry pack' => 'Industry Pack', 'add on' => 'Add-on', 'addon' => 'Add-on', 'core' => 'Core', 'foundation' => 'Foundation', 'operations' => 'Operations', 'utility' => 'Utility', 'plugin' => 'Plugin'];
        return $map[$type] ?? ($type !== '' ? ucwords($type) : 'Plugin');
    }

    private function normaliseFilterValue(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', '-', $value) ?? 'general';
        return trim($value, '-') ?: 'general';
    }

    private function commercialProfile(array $meta, string $pluginSlug): array
    {
        $price = (float) ($meta['plugin_price'] ?? 0);
        $subscription = !empty($meta['plugin_subscription']);
        $explicit = strtolower(trim((string) ($meta['commercial_status'] ?? $meta['commercial'] ?? $meta['license_model'] ?? '')));
        $foundation = [
            'WICompliance', 'WIOrg', 'WIHR', 'WIScheduler', 'WITaskEngine', 'WIAlerts',
            'WINotifications', 'WIUpload', 'WICalender', 'WIBlog', 'WIForum'
        ];

        if ($subscription || in_array($explicit, ['subscription', 'subscribed', 'recurring'], true)) {
            return [
                'label' => 'Subscription',
                'filter' => 'subscription',
                'badge' => 'success',
                'licence' => 'Required',
                'source' => 'Download from WILabs',
                'action' => 'wilabs',
            ];
        }

        if ($price > 0 || in_array($explicit, ['paid', 'one_time', 'one-time', 'licence', 'license'], true)) {
            return [
                'label' => 'Paid',
                'filter' => 'paid',
                'badge' => 'warning',
                'licence' => 'Required',
                'source' => 'Download from WILabs',
                'action' => 'wilabs',
            ];
        }

        if (in_array($pluginSlug, $foundation, true) || in_array($explicit, ['included', 'core', 'foundation'], true)) {
            return [
                'label' => 'Included',
                'filter' => 'included',
                'badge' => 'default',
                'licence' => 'Not required',
                'source' => 'Included with WICMS',
                'action' => 'install',
            ];
        }

        return [
            'label' => 'Free',
            'filter' => 'free',
            'badge' => 'info',
            'licence' => 'Not required',
            'source' => 'Local package',
            'action' => 'install',
        ];
    }

    private function wilabsUrl(array $meta, string $pluginSlug): string
    {
        foreach (['wilabs_url', 'wilabsUrl', 'download_url', 'downloadUrl', 'purchase_url', 'purchaseUrl', 'product_url', 'productUrl'] as $key) {
            if (!empty($meta[$key]) && is_string($meta[$key])) {
                return (string) $meta[$key];
            }
        }

        return '';
    }

    private function licenceDisplay(array $commercial, array $state, bool $localPackageFound = false): string
    {
        if ((string) $commercial['licence'] === 'Not required') {
            return 'Not required';
        }

        if (!empty($state['active'])) {
            return 'Required · local plugin active';
        }

        if (!empty($state['registered'])) {
            return 'Required · installed locally';
        }

        if ($localPackageFound) {
            return 'Required · validate during install/activation';
        }

        return 'Required · validate after WILabs download';
    }

    private function renderPluginToolbar(string $context, array $plugins = []): void
    {
        $summary = ['total' => count($plugins), 'available' => 0, 'installed' => 0, 'active' => 0];

        foreach ($plugins as $meta) {
            $slug = (string) ($meta['plugin_slug'] ?? '');
            if ($slug !== '') {
                $state = $this->pluginRegistryState($slug)['state'];
                if (isset($summary[$state])) {
                    $summary[$state]++;
                }
            }
        }

        echo '<div class="wi-plugin-readiness wi-comp-health-strip" data-plugin-context="' . $this->e($context) . '">
                <div class="wi-comp-health-tile"><span>Plugins found</span><strong>' . $this->e((string) $summary['total']) . '</strong></div>
                <div class="wi-comp-health-tile"><span>Available</span><strong>' . $this->e((string) $summary['available']) . '</strong></div>
                <div class="wi-comp-health-tile"><span>Installed</span><strong>' . $this->e((string) $summary['installed']) . '</strong></div>
                <div class="wi-comp-health-tile"><span>Active</span><strong>' . $this->e((string) $summary['active']) . '</strong></div>
              </div>
              <div class="wi-plugin-filters wi-comp-widget" data-plugin-filter-context="' . $this->e($context) . '">
                <div class="wi-plugin-filter wi-plugin-filter--search"><label>Search plugins</label><input type="search" class="form-control wi-plugin-filter-input" data-plugin-filter="search" placeholder="Search name, slug, author or description"></div>
                <div class="wi-plugin-filter"><label>Status</label><select class="form-control wi-plugin-filter-input" data-plugin-filter="status"><option value="all">All statuses</option><option value="available">Available</option><option value="installed">Installed</option><option value="active">Active</option></select></div>
                <div class="wi-plugin-filter"><label>Type</label><select class="form-control wi-plugin-filter-input" data-plugin-filter="type"><option value="all">All types</option><option value="industry-pack">Industry Packs</option><option value="add-on">Add-ons</option><option value="operations">Operations</option><option value="core">Core</option><option value="foundation">Foundation</option></select></div>
                <div class="wi-plugin-filter"><label>Commercial</label><select class="form-control wi-plugin-filter-input" data-plugin-filter="pricing"><option value="all">All commercial states</option><option value="included">Included</option><option value="free">Free</option><option value="paid">Paid</option><option value="subscription">Subscription</option></select></div>
                <div class="wi-plugin-filter wi-plugin-filter--reset"><button type="button" class="btn btn-default wi-plugin-filter-reset">Reset</button></div>
              </div>';
    }

    private function renderPluginCard(array $meta, bool $installedMode = false): void
    {
        $slug = (string) ($meta['plugin_slug'] ?? '');
        if ($slug === '') {
            return;
        }

        $state = $this->pluginRegistryState($slug);
        $commercial = $this->commercialProfile($meta, $slug);
        $localPackageFound = $this->localPackageAvailable($slug);
        $sourceLabel = $localPackageFound ? 'Local package found' : (string) ($commercial['source'] ?? 'Download from WILabs');
        $wilabsUrl = $this->wilabsUrl($meta, $slug);
        $typeLabel = $this->normaliseTypeLabel((string) ($meta['plugin_type'] ?? 'plugin'));
        $typeFilter = $this->normaliseFilterValue($typeLabel);
        $category = (string) ($meta['plugin_category'] ?? 'general');
        $name = (string) ($meta['plugin_name'] ?? $slug);
        $description = (string) ($meta['plugin_description'] ?? '');
        $adminUrl = $this->resolveAdminUrl($slug, $meta);
        $licenceDisplay = $this->licenceDisplay($commercial, $state, $localPackageFound);
        $search = strtolower(trim($name . ' ' . $slug . ' ' . ($meta['plugin_author'] ?? '') . ' ' . $description . ' ' . $category . ' ' . $typeLabel . ' ' . $commercial['label'] . ' ' . $sourceLabel));

        echo '<div class="col-md-4 col-sm-6 col-xs-12 wi-plugin-card-wrap"
                    data-plugin-card="1"
                    data-plugin-search="' . $this->e($search) . '"
                    data-plugin-status="' . $this->e((string) $state['state']) . '"
                    data-plugin-type="' . $this->e($typeFilter) . '"
                    data-plugin-pricing="' . $this->e((string) $commercial['filter']) . '">
                <article class="wi-plugin-card wi-comp-dashboard-widget wi-plugin-card--' . $this->e((string) $state['state']) . '" data-plugin-slug="' . $this->e($slug) . '">
                    <header class="wi-plugin-card__head">
                        <div>
                            <span class="wi-comp-kicker">' . $this->e($typeLabel) . '</span>
                            <h3>' . $this->e($name) . '</h3>
                            <p>' . $this->e((string) $state['label']) . ' · ' . $this->e((string) $commercial['label']) . ' · ' . $this->e($sourceLabel) . '</p>
                        </div>
                        <div class="wi-plugin-card__badges">' .
                            $this->badge((string) $state['label'], (string) $state['badge']) .
                            $this->badge((string) $commercial['label'], (string) $commercial['badge']) .
                        '</div>
                    </header>
                    ' . $this->previewHtml($slug, (string) ($meta['plugin_preview'] ?? ''), $name) . '
                    <section class="wi-plugin-card__body wi-comp-widget__body">
                        <p class="wi-plugin-card__description">' . $this->e($description !== '' ? $description : 'No description has been provided for this plugin package.') . '</p>
                        <dl class="wi-plugin-card__meta">
                            <div><dt>Slug</dt><dd>' . $this->e($slug) . '</dd></div>
                            <div><dt>Version</dt><dd>' . $this->e($meta['plugin_version'] ?? '1.0.0') . '</dd></div>
                            <div><dt>Author</dt><dd>' . $this->e($meta['plugin_author'] ?? 'Warner Infinity') . '</dd></div>
                            <div><dt>Commercial</dt><dd>' . $this->e((string) $commercial['label']) . '</dd></div>
                            <div><dt>Licence</dt><dd>' . $this->e($licenceDisplay) . '</dd></div>
                            <div><dt>Source</dt><dd>' . $this->e($sourceLabel) . '</dd></div>
                        </dl>
                    </section>
                    <section class="wi-plugin-card__progress" hidden><div class="wi-plugin-card__progress-top"><strong data-plugin-progress-title>Preparing...</strong><span data-plugin-progress-percent>0%</span></div><div class="progress wi-plugin-progressbar"><div class="progress-bar progress-bar-success" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width:0%"></div></div><p data-plugin-progress-text>Waiting for action.</p></section>
                    <section class="wi-plugin-card__error" hidden><strong><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Plugin action failed</strong><p data-plugin-error-message></p><details><summary>Technical detail</summary><pre data-plugin-error-detail></pre></details></section>
                    <footer class="wi-plugin-card__foot">';

        if ($state['state'] === 'available' && $localPackageFound) {
            echo '<button type="button" class="wi-comp-btn wi-comp-btn--primary wi-plugin-action" data-plugin-action="install" data-plugin-slug="' . $this->e($slug) . '">Install</button>';
        } elseif ($state['state'] === 'available' && in_array((string) $commercial['filter'], ['paid', 'subscription'], true)) {
            echo '<button type="button" class="wi-comp-btn wi-comp-btn--primary wi-plugin-action" data-plugin-action="wilabs" data-plugin-slug="' . $this->e($slug) . '" data-wilabs-url="' . $this->e($wilabsUrl) . '">Download from WILabs</button>';
        } elseif ($state['state'] === 'available') {
            echo '<button type="button" class="wi-comp-btn wi-comp-btn--primary wi-plugin-action" data-plugin-action="install" data-plugin-slug="' . $this->e($slug) . '">Install</button>';
        } elseif ($state['state'] === 'installed') {
            echo '<button type="button" class="wi-comp-btn wi-comp-btn--primary wi-plugin-action" data-plugin-action="enable" data-plugin-slug="' . $this->e($slug) . '">Activate</button>';
        } else {
            if ($adminUrl !== '') {
                echo '<a class="wi-comp-btn wi-comp-btn--primary" href="' . $this->e($adminUrl) . '">Manage</a>';
            }
            echo '<button type="button" class="wi-comp-btn wi-comp-btn--secondary wi-plugin-action" data-plugin-action="disable" data-plugin-slug="' . $this->e($slug) . '">Disable</button>';
        }

        echo '<button type="button" class="wi-comp-btn wi-comp-btn--ghost wi-plugin-details-toggle">Details</button></footer>
                    <section class="wi-plugin-card__details" hidden>
                        <p><strong>Category:</strong> ' . $this->e($category) . '</p>
                        <p><strong>Commercial:</strong> ' . $this->e((string) $commercial['label']) . '</p>
                        <p><strong>Licence:</strong> ' . $this->e($licenceDisplay) . '</p>
                        <p><strong>Source:</strong> ' . $this->e($sourceLabel) . '</p>
                        <p><strong>Current state:</strong> ' . $this->e((string) $state['label']) . '</p>
                        <p><strong>Registry:</strong> ' . $this->e(!empty($state['registered']) ? 'Found in wi_plugin' : 'Not registered in wi_plugin') . '</p>
                        <p><strong>DB status:</strong> ' . $this->e((string) ($state['db_status'] ?? 'unknown')) . '</p>
                    </section>
                </article>
              </div>';
    }

    public function marketplace(): void
    {
        $plugins = [];
        foreach ($this->store->listFilesystemPlugins() as $pluginSlug) {
            $plugins[] = $this->pluginMeta((string) $pluginSlug);
        }

        $this->renderPluginToolbar('marketplace', $plugins);
        echo '<div class="row wi-plugin-grid" data-plugin-grid="marketplace">';
        foreach ($plugins as $meta) {
            $this->renderPluginCard($meta, false);
        }
        echo '</div>';
    }

    public function installedPlugins(): void
    {
        $rows = $this->tableExists('wi_plugin') ? $this->WIdb->select("SELECT * FROM `wi_plugin` ORDER BY `plugin_id` ASC") : [];
        $plugins = [];
        foreach ($rows as $row) {
            $slug = (string) ($row['plugin_slug'] ?? $row['plugin_name'] ?? '');
            if ($slug !== '') {
                $plugins[] = $this->pluginMeta($slug);
            }
        }

        $this->renderPluginToolbar('installed', $plugins);
        echo '<div class="row wi-plugin-grid" data-plugin-grid="installed">';
        foreach ($plugins as $meta) {
            $this->renderPluginCard($meta, true);
        }
        echo '</div>';
    }

    public function subscriptions(): void
    {
        $rows = $this->tableExists('wi_plugin_subscriptions')
            ? $this->WIdb->select("SELECT * FROM `wi_plugin_subscriptions` ORDER BY `subscription_id` DESC")
            : [];

        echo '<div class="table-responsive wi-plugin-status-table"><table class="table table-bordered table-striped">
                <thead><tr><th>Plugin</th><th>Period</th><th>Status</th><th>Start</th><th>End</th><th>Source</th></tr></thead><tbody>';

        foreach ($rows as $row) {
            echo '<tr><td>' . $this->e($row['plugin_slug'] ?? '') . '</td><td>' . $this->e($row['subscription_period'] ?? '') . '</td><td>' . $this->e($row['subscription_status'] ?? '') . '</td><td>' . $this->e($row['subscription_start'] ?? '') . '</td><td>' . $this->e($row['subscription_end'] ?? '') . '</td><td>WILabs / local cache</td></tr>';
        }

        echo '</tbody></table></div>';
    }

    public function licenses(): void
    {
        $rows = $this->tableExists('wi_plugin_licenses')
            ? $this->WIdb->select("SELECT * FROM `wi_plugin_licenses` ORDER BY `license_id` DESC")
            : [];

        echo '<div class="table-responsive"><table class="table table-bordered table-striped">
                <thead><tr><th>Plugin</th><th>License Key</th><th>Type</th><th>Status</th><th>Created</th><th>Expires</th></tr></thead><tbody>';

        foreach ($rows as $row) {
            echo '<tr><td>' . $this->e($row['plugin_slug'] ?? '') . '</td><td>' . $this->e($row['license_key'] ?? '') . '</td><td>' . $this->e($row['license_type'] ?? '') . '</td><td>' . $this->e($row['license_status'] ?? '') . '</td><td>' . $this->e($row['license_created'] ?? '') . '</td><td>' . $this->e($row['license_expires'] ?? '') . '</td></tr>';
        }

        echo '</tbody></table></div>';
    }

    public function invoices(): void
    {
        $rows = $this->tableExists('wi_plugin_invoices')
            ? $this->WIdb->select("SELECT * FROM `wi_plugin_invoices` ORDER BY `invoice_id` DESC")
            : [];

        echo '<div class="table-responsive wi-plugin-status-table"><table class="table table-bordered table-striped">
                <thead><tr><th>Invoice</th><th>Order ID</th><th>Status</th><th>Date</th><th>Source</th></tr></thead><tbody>';

        foreach ($rows as $row) {
            echo '<tr><td>' . $this->e($row['invoice_number'] ?? '') . '</td><td>' . $this->e($row['order_id'] ?? '') . '</td><td>' . $this->e($row['invoice_status'] ?? '') . '</td><td>' . $this->e($row['invoice_date'] ?? '') . '</td><td>WILabs / local cache</td></tr>';
        }

        echo '</tbody></table></div>';
    }

    public function install(string $pluginSlug): bool
    {
        $this->lastError = '';
        $this->lastResult = [];
        $pluginSlug = $this->cleanName($pluginSlug);

        if ($pluginSlug === '') {
            return $this->setError('No plugin slug was supplied.');
        }

        $this->prepareBufferedRegistryQueries();

        if (!$this->ensurePluginRegistrySchema()) {
            return false;
        }

        if (!$this->tableExists('wi_plugin')) {
            return $this->setError('The wi_plugin table is still missing after registry repair.');
        }

        $pluginDir = $this->pluginRoot . $pluginSlug;
        if (!is_dir($pluginDir)) {
            return $this->setError('Plugin package not found: WIAdmin/WIPlugin/' . $pluginSlug . '. Check the folder name and plugin.json.');
        }

        $meta = $this->pluginMeta($pluginSlug);

        // Compatibility bridge: several newer WI packages use installer_class in
        // plugin.json, while the older WIPluginStore only exposed installer.
        // Keep this fallback here so older store files do not silently skip a package installer.
        if ((string) ($meta['installer'] ?? '') === '' && method_exists($this->store, 'readPluginMeta')) {
            $rawMeta = $this->store->readPluginMeta($pluginSlug);
            if (!empty($rawMeta['installer_class']) && is_string($rawMeta['installer_class'])) {
                $meta['installer'] = $rawMeta['installer_class'];
            }
            if ((string) ($meta['installer_file'] ?? '') === '' && !empty($rawMeta['installer_file']) && is_string($rawMeta['installer_file'])) {
                $meta['installer_file'] = $rawMeta['installer_file'];
            }
        }

        $installerOk = $this->runOptionalInstaller($pluginSlug, $meta);
        if (!$installerOk) {
            return false;
        }

        if (!$this->ensurePluginRegistrySchema()) {
            return false;
        }

        $existing = $this->registryRow($pluginSlug);
        $data = $this->filterExistingColumns('wi_plugin', [
            'plugin_name' => $meta['plugin_name'] ?? $pluginSlug,
            'plugin_slug' => $pluginSlug,
            'plugin_version' => $meta['plugin_version'] ?? '1.0.0',
            'plugin_author' => $meta['plugin_author'] ?? 'Warner Infinity',
            'plugin_description' => $meta['plugin_description'] ?? '',
            'plugin_status' => $existing ? (string) ($existing['plugin_status'] ?? 'disabled') : 'disabled',
            'plugin_updated' => date('Y-m-d H:i:s'),
            'plugin_installed' => date('Y-m-d H:i:s'),
        ]);

        if ($data === []) {
            return $this->setError('Plugin registry write failed before insert/update: none of the expected wi_plugin columns were detected.');
        }

        try {
            if ($existing) {
                unset($data['plugin_slug'], $data['plugin_installed']);

                if ($data === []) {
                    $this->lastResult = $this->buildInstallResult($pluginSlug, $meta, true);
                    return true;
                }

                $where = '';
                $whereParams = [];
                $existingId = (int) ($existing['plugin_id'] ?? 0);

                if ($existingId > 0 && $this->columnExists('wi_plugin', 'plugin_id')) {
                    $where = '`plugin_id` = :where_plugin_id';
                    $whereParams = ['where_plugin_id' => $existingId];
                } elseif ($this->columnExists('wi_plugin', 'plugin_slug')) {
                    $where = '`plugin_slug` = :where_plugin_slug';
                    $whereParams = ['where_plugin_slug' => (string) ($existing['plugin_slug'] ?? $pluginSlug)];
                } elseif ($this->columnExists('wi_plugin', 'plugin_name')) {
                    $where = '`plugin_name` = :where_plugin_name';
                    $whereParams = ['where_plugin_name' => (string) ($existing['plugin_name'] ?? $pluginSlug)];
                } else {
                    return $this->setError('Plugin registry update failed: no usable identity column exists on wi_plugin.');
                }

                $updated = (bool) $this->WIdb->update('wi_plugin', $data, $where, $whereParams);

                if (!$updated) {
                    return $this->setError('Plugin registry update returned false for ' . $pluginSlug . '. Columns attempted: ' . implode(', ', array_keys($data)) . '.');
                }

                $this->lastResult = $this->buildInstallResult($pluginSlug, $meta, true);
                return true;
            }

            $inserted = (bool) $this->WIdb->insert('wi_plugin', $data);

            if (!$inserted) {
                return $this->setError('Plugin registry insert returned false for ' . $pluginSlug . '. Columns attempted: ' . implode(', ', array_keys($data)) . '.');
            }

            $this->lastResult = $this->buildInstallResult($pluginSlug, $meta, false);
            return true;
        } catch (Throwable $e) {
            return $this->setError('Plugin registry update failed: ' . $e->getMessage());
        }
    }

    private function buildInstallResult(string $pluginSlug, array $meta, bool $updatedExisting): array
    {
        $row = $this->registryRow($pluginSlug) ?? [];
        $adminUrl = $this->resolveAdminUrl($pluginSlug, $meta);

        return [
            'plugin_slug' => $pluginSlug,
            'plugin_name' => (string) ($meta['plugin_name'] ?? $pluginSlug),
            'plugin_version' => (string) ($meta['plugin_version'] ?? '1.0.0'),
            'plugin_status' => (string) ($row['plugin_status'] ?? 'disabled'),
            'registry_row_found' => $row !== [],
            'updated_existing' => $updatedExisting,
            'admin_url' => $adminUrl,
            'next_step' => $adminUrl !== ''
                ? 'Plugin installed. Activate it to make it available in the admin navigation.'
                : 'Plugin installed. Next step: activate it from the Installed tab.',
        ];
    }

    private function resolveAdminUrl(string $pluginSlug, array $meta): string
    {
        foreach (['admin_url', 'adminUrl', 'admin_route', 'adminRoute', 'menu_url', 'menuUrl', 'settings_url', 'settingsUrl'] as $key) {
            if (!empty($meta[$key]) && is_string($meta[$key])) {
                return (string) $meta[$key];
            }
        }

        if (!empty($meta['admin']) && is_array($meta['admin'])) {
            foreach (['url', 'route', 'path', 'page'] as $key) {
                if (!empty($meta['admin'][$key]) && is_string($meta['admin'][$key])) {
                    return (string) $meta['admin'][$key];
                }
            }
        }

        if (!empty($meta['navigation']) && is_array($meta['navigation'])) {
            foreach ($meta['navigation'] as $item) {
                if (!is_array($item)) {
                    continue;
                }

                foreach (['url', 'route', 'path', 'page'] as $key) {
                    if (!empty($item[$key]) && is_string($item[$key])) {
                        return (string) $item[$key];
                    }
                }
            }
        }

        $candidateFiles = [
            'WIAdmin/' . $pluginSlug . '.php',
            'WIAdmin/WIInc/site/' . strtolower($pluginSlug) . '/' . strtolower($pluginSlug) . '.php',
            'WIAdmin/WIInc/site/' . $pluginSlug . '/' . $pluginSlug . '.php',
        ];

        foreach ($candidateFiles as $relative) {
            if (is_file($this->pluginRoot . $pluginSlug . '/' . $relative)) {
                return $relative;
            }
        }

        return '';
    }

    public function uninstall(string $pluginSlug): bool
    {
        $this->lastError = '';
        $this->lastResult = [];
        $pluginSlug = $this->cleanName($pluginSlug);
        if ($pluginSlug === '') {
            return $this->setError('No plugin slug was supplied.');
        }

        if (!$this->ensurePluginRegistrySchema()) {
            return false;
        }

        try {
            return (bool) $this->WIdb->update(
                'wi_plugin',
                $this->filterExistingColumns('wi_plugin', ['plugin_status' => 'disabled', 'plugin_updated' => date('Y-m-d H:i:s')]),
                '`plugin_slug` = :where_plugin_slug',
                ['where_plugin_slug' => $pluginSlug]
            );
        } catch (Throwable $e) {
            return $this->setError('Plugin uninstall failed: ' . $e->getMessage());
        }
    }

    public function enable(string $pluginSlug): bool
    {
        $this->lastError = '';
        $this->lastResult = [];
        $pluginSlug = $this->cleanName($pluginSlug);

        if ($pluginSlug === '') {
            return $this->setError('No plugin slug was supplied.');
        }

        if (!$this->ensurePluginRegistrySchema()) {
            return false;
        }

        if ($this->registryRow($pluginSlug) === null) {
            if (!$this->install($pluginSlug)) {
                return false;
            }
        }

        $meta = $this->pluginMeta($pluginSlug);

        try {
            $updated = (bool) $this->WIdb->update(
                'wi_plugin',
                $this->filterExistingColumns('wi_plugin', [
                    'plugin_status' => 'enabled',
                    'plugin_updated' => date('Y-m-d H:i:s'),
                ]),
                '`plugin_slug` = :where_plugin_slug',
                ['where_plugin_slug' => $pluginSlug]
            );

            if ($updated) {
                $this->lastResult = $this->buildActivationResult($pluginSlug, $meta);
            }

            return $updated;
        } catch (Throwable $e) {
            return $this->setError('Plugin activation failed: ' . $e->getMessage());
        }
    }

    private function buildActivationResult(string $pluginSlug, array $meta): array
    {
        $row = $this->registryRow($pluginSlug) ?? [];
        $adminUrl = $this->resolveAdminUrl($pluginSlug, $meta);

        return [
            'plugin_slug' => $pluginSlug,
            'plugin_name' => (string) ($meta['plugin_name'] ?? $pluginSlug),
            'plugin_version' => (string) ($meta['plugin_version'] ?? '1.0.0'),
            'plugin_status' => (string) ($row['plugin_status'] ?? 'enabled'),
            'registry_row_found' => $row !== [],
            'admin_url' => $adminUrl,
            'next_step' => $adminUrl !== ''
                ? 'Plugin is active. Open it from the admin navigation or the supplied admin_url.'
                : 'Plugin is active. If it does not appear in the sidebar, run plugin/module resync or check the plugin manifest navigation block.',
        ];
    }

    public function disable(string $pluginSlug): bool
    {
        return $this->uninstall($pluginSlug);
    }

    public function createOrder(int $userId, string $pluginSlug, float $price, string $currency = 'GBP', string $gateway = 'manual'): int
    {
        return $this->commerce->createOrder($userId, $pluginSlug, $price, $currency, $gateway);
    }

    public function completePurchase(int $userId, string $pluginSlug, float $price, string $currency = 'GBP', string $gateway = 'manual', string $licenseType = 'lifetime', bool $subscription = false, string $subscriptionPeriod = 'monthly'): array
    {
        return $this->commerce->completePurchase($userId, $pluginSlug, $price, $currency, $gateway, $licenseType, $subscription, $subscriptionPeriod);
    }

    public function validateLicense(string $licenseKey, string $pluginSlug): bool
    {
        return $this->licenseService->validateKey($licenseKey, $pluginSlug);
    }

    public function getLicenseByUserAndPlugin(int $userId, string $pluginSlug): ?array
    {
        return $this->licenseService->getByPluginAndUser($userId, $pluginSlug);
    }

    public function getInvoiceByOrderId(int $orderId): ?array
    {
        return $this->invoiceService->getByOrderId($orderId);
    }

    public function getActiveSubscriptionsByUser(int $userId): array
    {
        return $this->subscriptionService->getActiveByUser($userId);
    }

    /**
     * Ensure the shared plugin registry schema is ready before a package tries
     * to register itself.
     *
     * Important: this repair deliberately uses a short-lived, isolated WIdb/PDO
     * connection instead of the main shared installer connection. Showcase can
     * leave an unbuffered result set open before plugin actions reach this
     * method; using the same PDO connection for ALTER/metadata checks can then
     * trigger MySQL error 2014. A dedicated repair connection avoids touching
     * the active installer cursor, closes every statement, then is discarded.
     */
    private function ensurePluginRegistrySchema(): bool
    {
        $repairDb = null;

        try {
            $repairDb = new WIdb(
                (string) DB_TYPE,
                (string) DB_HOST,
                (string) DB_NAME,
                (string) DB_USER,
                (string) DB_PASS
            );

            if (defined('PDO::MYSQL_ATTR_USE_BUFFERED_QUERY')) {
                try {
                    $repairDb->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                } catch (Throwable $ignored) {
                    // Non-MySQL PDO drivers may not support this attribute.
                }
            }

            $safeIdentifier = static function (string $identifier): bool {
                return (bool) preg_match('/^[A-Za-z0-9_]+$/', $identifier);
            };

            $fetchAll = static function (PDO $db, string $sql, array $params = []): array {
                $stmt = $db->prepare($sql);

                foreach ($params as $key => $value) {
                    $placeholder = ':' . ltrim((string) $key, ':');
                    if (strpos($sql, $placeholder) === false) {
                        continue;
                    }

                    $type = PDO::PARAM_STR;
                    if (is_int($value)) {
                        $type = PDO::PARAM_INT;
                    } elseif (is_bool($value)) {
                        $type = PDO::PARAM_BOOL;
                    } elseif ($value === null) {
                        $type = PDO::PARAM_NULL;
                    } elseif (is_float($value)) {
                        $value = (string) $value;
                    }

                    $stmt->bindValue($placeholder, $value, $type);
                }

                $stmt->execute();
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt->closeCursor();

                return is_array($rows) ? $rows : [];
            };

            $fetchValue = static function (PDO $db, string $sql, array $params = [], string $column = 'count_value') use ($fetchAll): mixed {
                $rows = $fetchAll($db, $sql, $params);
                if ($rows === []) {
                    return null;
                }

                return $rows[0][$column] ?? null;
            };

            $execute = static function (PDO $db, string $sql, array $params = []) use ($fetchAll): bool {
                if ($params === []) {
                    $db->exec($sql);
                    return true;
                }

                $stmt = $db->prepare($sql);

                foreach ($params as $key => $value) {
                    $placeholder = ':' . ltrim((string) $key, ':');
                    if (strpos($sql, $placeholder) === false) {
                        continue;
                    }

                    $type = PDO::PARAM_STR;
                    if (is_int($value)) {
                        $type = PDO::PARAM_INT;
                    } elseif (is_bool($value)) {
                        $type = PDO::PARAM_BOOL;
                    } elseif ($value === null) {
                        $type = PDO::PARAM_NULL;
                    } elseif (is_float($value)) {
                        $value = (string) $value;
                    }

                    $stmt->bindValue($placeholder, $value, $type);
                }

                $ok = (bool) $stmt->execute();
                $stmt->closeCursor();

                return $ok;
            };

            $tableExists = static function (PDO $db, string $table) use ($safeIdentifier, $fetchValue): bool {
                if (!$safeIdentifier($table)) {
                    return false;
                }

                return (int) $fetchValue(
                    $db,
                    'SELECT COUNT(*) AS count_value
                     FROM INFORMATION_SCHEMA.TABLES
                     WHERE TABLE_SCHEMA = DATABASE()
                       AND TABLE_NAME = :table_name',
                    ['table_name' => $table],
                    'count_value'
                ) > 0;
            };

            $columnExists = static function (PDO $db, string $table, string $column) use ($safeIdentifier, $fetchValue): bool {
                if (!$safeIdentifier($table) || !$safeIdentifier($column)) {
                    return false;
                }

                return (int) $fetchValue(
                    $db,
                    'SELECT COUNT(*) AS count_value
                     FROM INFORMATION_SCHEMA.COLUMNS
                     WHERE TABLE_SCHEMA = DATABASE()
                       AND TABLE_NAME = :table_name
                       AND COLUMN_NAME = :column_name',
                    [
                        'table_name' => $table,
                        'column_name' => $column,
                    ],
                    'count_value'
                ) > 0;
            };

            if (!$tableExists($repairDb, 'wi_plugin')) {
                $repairDb->exec(
                    "CREATE TABLE IF NOT EXISTS `wi_plugin` (\n" .
                    "  `plugin_id` INT NOT NULL AUTO_INCREMENT,\n" .
                    "  `plugin_name` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,\n" .
                    "  `plugin_slug` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,\n" .
                    "  `plugin_version` VARCHAR(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,\n" .
                    "  `plugin_author` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,\n" .
                    "  `plugin_description` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,\n" .
                    "  `plugin_status` ENUM('enabled','disabled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT 'disabled',\n" .
                    "  `plugin_installed` DATETIME DEFAULT CURRENT_TIMESTAMP,\n" .
                    "  `plugin_updated` DATETIME DEFAULT NULL,\n" .
                    "  `plugin_license_key` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,\n" .
                    "  PRIMARY KEY (`plugin_id`)\n" .
                    ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
                );
            }

            if (!$columnExists($repairDb, 'wi_plugin', 'plugin_slug')) {
                $repairDb->exec(
                    "ALTER TABLE `wi_plugin` " .
                    "ADD COLUMN `plugin_slug` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL AFTER `plugin_name`"
                );
            }

            if (!$columnExists($repairDb, 'wi_plugin', 'plugin_installed')) {
                $repairDb->exec("ALTER TABLE `wi_plugin` ADD COLUMN `plugin_installed` DATETIME DEFAULT CURRENT_TIMESTAMP");
            }

            if (!$columnExists($repairDb, 'wi_plugin', 'plugin_updated')) {
                $repairDb->exec("ALTER TABLE `wi_plugin` ADD COLUMN `plugin_updated` DATETIME DEFAULT NULL");
            }

            $repairDb->exec(
                "UPDATE `wi_plugin` " .
                "SET `plugin_slug` = LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(TRIM(COALESCE(`plugin_name`, CONCAT('plugin-', `plugin_id`))), ' ', ''), '_', ''), '-', ''), '.', ''), '/', '')) " .
                "WHERE `plugin_slug` IS NULL OR `plugin_slug` = ''"
            );

            $repairDb->exec(
                "UPDATE `wi_plugin` SET `plugin_slug` = CONCAT('plugin-', `plugin_id`) " .
                "WHERE `plugin_slug` IS NULL OR `plugin_slug` = ''"
            );

            $duplicates = $fetchAll(
                $repairDb,
                "SELECT `plugin_slug` FROM `wi_plugin` GROUP BY `plugin_slug` HAVING COUNT(*) > 1"
            );

            foreach ($duplicates as $duplicate) {
                $slug = (string) ($duplicate['plugin_slug'] ?? '');
                if ($slug === '') {
                    continue;
                }

                $rows = $fetchAll(
                    $repairDb,
                    "SELECT `plugin_id` FROM `wi_plugin` WHERE `plugin_slug` = :slug ORDER BY `plugin_id` ASC",
                    ['slug' => $slug]
                );

                $keepFirst = true;
                foreach ($rows as $row) {
                    if ($keepFirst) {
                        $keepFirst = false;
                        continue;
                    }

                    $id = (int) ($row['plugin_id'] ?? 0);
                    if ($id <= 0) {
                        continue;
                    }

                    $execute(
                        $repairDb,
                        "UPDATE `wi_plugin` SET `plugin_slug` = :plugin_slug WHERE `plugin_id` = :plugin_id",
                        [
                            'plugin_slug' => $slug . '-' . $id,
                            'plugin_id' => $id,
                        ]
                    );
                }
            }

            $repairDb->exec(
                "ALTER TABLE `wi_plugin` MODIFY COLUMN `plugin_slug` VARCHAR(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL"
            );

            $indexCount = (int) $fetchValue(
                $repairDb,
                "SELECT COUNT(*) AS count_value FROM INFORMATION_SCHEMA.STATISTICS " .
                "WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'wi_plugin' AND INDEX_NAME = 'plugin_slug'",
                [],
                'count_value'
            );

            if ($indexCount === 0) {
                $repairDb->exec("ALTER TABLE `wi_plugin` ADD UNIQUE KEY `plugin_slug` (`plugin_slug`)");
            }

            $ok = $columnExists($repairDb, 'wi_plugin', 'plugin_slug');
            $repairDb = null;

            return $ok;
        } catch (Throwable $e) {
            $repairDb = null;
            return $this->setError('Plugin registry schema repair failed: ' . $e->getMessage());
        }
    }

    private function prepareBufferedRegistryQueries(): void
    {
        if (defined('PDO::MYSQL_ATTR_USE_BUFFERED_QUERY')) {
            try {
                $this->WIdb->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            } catch (Throwable $e) {
                // Non-MySQL PDO drivers may not support this attribute.
            }
        }
    }

    private function registrySafeIdentifier(string $identifier): bool
    {
        return (bool) preg_match('/^[A-Za-z0-9_]+$/', $identifier);
    }

    private function registryTableExists(string $table): bool
    {
        if (!$this->registrySafeIdentifier($table)) {
            return false;
        }

        return (int) $this->registryFetchValue(
            'SELECT COUNT(*) AS count_value
             FROM INFORMATION_SCHEMA.TABLES
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name',
            ['table_name' => $table],
            'count_value'
        ) > 0;
    }

    private function registryColumnExists(string $table, string $column): bool
    {
        if (!$this->registrySafeIdentifier($table) || !$this->registrySafeIdentifier($column)) {
            return false;
        }

        return (int) $this->registryFetchValue(
            'SELECT COUNT(*) AS count_value
             FROM INFORMATION_SCHEMA.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = :table_name
               AND COLUMN_NAME = :column_name',
            [
                'table_name' => $table,
                'column_name' => $column,
            ],
            'count_value'
        ) > 0;
    }

    private function registryFetchValue(string $sql, array $params = [], string $column = 'count_value'): mixed
    {
        $rows = $this->registryFetchAll($sql, $params);
        if ($rows === []) {
            return null;
        }

        return $rows[0][$column] ?? null;
    }

    private function registryFetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->WIdb->prepare($sql);
        $this->bindRegistryParams($stmt, $sql, $params);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return is_array($rows) ? $rows : [];
    }

    private function registryExecute(string $sql, array $params = []): bool
    {
        $stmt = $this->WIdb->prepare($sql);
        $this->bindRegistryParams($stmt, $sql, $params);
        $ok = (bool) $stmt->execute();
        $stmt->closeCursor();

        return $ok;
    }

    private function registryExec(string $sql): void
    {
        $this->WIdb->exec($sql);
    }

    private function bindRegistryParams(PDOStatement $stmt, string $sql, array $params): void
    {
        foreach ($params as $key => $value) {
            $placeholder = ':' . ltrim((string) $key, ':');
            if (strpos($sql, $placeholder) === false) {
                continue;
            }

            $type = PDO::PARAM_STR;
            if (is_int($value)) {
                $type = PDO::PARAM_INT;
            } elseif (is_bool($value)) {
                $type = PDO::PARAM_BOOL;
            } elseif ($value === null) {
                $type = PDO::PARAM_NULL;
            } elseif (is_float($value)) {
                $value = (string) $value;
            }

            $stmt->bindValue($placeholder, $value, $type);
        }
    }

    private function runOptionalInstaller(string $pluginSlug, array $meta): bool
    {
        $pluginDir = $this->pluginRoot . $pluginSlug;
        $installerClass = (string) ($meta['installer'] ?? '');
        $installerFile = (string) ($meta['installer_file'] ?? '');

        if ($installerFile === '' && $installerClass !== '') {
            $installerFile = 'Install/' . $installerClass . '.php';
        }

        if ($installerFile === '') {
            return true;
        }

        $path = $this->normalisePackagePath($pluginDir, $installerFile);
        if ($path === null || !is_file($path)) {
            return $this->setError('Plugin installer file is missing: ' . $installerFile . '.');
        }

        require_once $path;

        if ($installerClass === '') {
            $base = basename($path, '.php');
            $installerClass = preg_replace('/[^A-Za-z0-9_\\\\]/', '', $base) ?? '';
        }

        if ($installerClass === '' || !class_exists($installerClass)) {
            return $this->setError('Plugin installer class was not found after loading ' . $installerFile . '.');
        }

        try {
            $installer = $this->instantiateInstaller($installerClass);

            foreach (['install', 'run', 'execute'] as $method) {
                if (!method_exists($installer, $method)) {
                    continue;
                }

                $result = $this->callInstallerMethod($installer, $method, $pluginSlug, $meta);

                if ($result === false) {
                    return $this->setError('Plugin installer returned false.');
                }

                if (is_array($result) && isset($result['success']) && !$result['success']) {
                    return $this->setError((string) ($result['message'] ?? 'Plugin installer failed.'));
                }

                return true;
            }

            return true;
        } catch (Throwable $e) {
            return $this->setError('Plugin installer failed: ' . $e->getMessage());
        }
    }

    private function instantiateInstaller(string $class): object
    {
        try {
            return new $class($this->WIdb);
        } catch (Throwable $e) {
            return new $class();
        }
    }

    private function callInstallerMethod(object $installer, string $method, string $pluginSlug, array $meta): mixed
    {
        $reflection = new ReflectionMethod($installer, $method);
        $parameters = $reflection->getParameters();

        if ($parameters === []) {
            return $installer->{$method}();
        }

        $context = [
            'plugin_slug' => $pluginSlug,
            'plugin_root' => $this->pluginRoot,
            'plugin_path' => $this->pluginRoot . $pluginSlug,
            'meta' => $meta,
            'db' => $this->WIdb,
            'WIdb' => $this->WIdb,
        ];

        $arguments = [];

        foreach ($parameters as $parameter) {
            $arguments[] = $this->resolveInstallerArgument($parameter, $pluginSlug, $context, $meta);
        }

        return $reflection->invokeArgs($installer, $arguments);
    }

    /**
     * Resolve one installer method argument without weakening installer security.
     *
     * Older WI plugins commonly expose install(WIdb $WIdb). Newer package-aware
     * installers may expose install(array $context) or install(WIdb $WIdb, array $context).
     * This adapter keeps both styles working while still passing the real WIdb instance,
     * never a plain array, when the installer asks for the database layer.
     */
    private function resolveInstallerArgument(ReflectionParameter $parameter, string $pluginSlug, array $context, array $meta): mixed
    {
        $name = strtolower($parameter->getName());
        $type = $parameter->getType();
        $typeNames = [];

        if ($type instanceof ReflectionNamedType) {
            $typeNames[] = ltrim($type->getName(), '\\');
        } elseif ($type instanceof ReflectionUnionType) {
            foreach ($type->getTypes() as $unionType) {
                if ($unionType instanceof ReflectionNamedType) {
                    $typeNames[] = ltrim($unionType->getName(), '\\');
                }
            }
        }

        foreach ($typeNames as $typeName) {
            $lowerType = strtolower($typeName);

            if ($lowerType === 'widb' || str_ends_with($lowerType, '\\widb')) {
                return $this->WIdb;
            }

            if ($lowerType === 'array') {
                if ($name === 'meta' || $name === 'metadata') {
                    return $meta;
                }

                return $context;
            }

            if ($lowerType === 'string') {
                if (in_array($name, ['pluginslug', 'plugin_slug', 'slug', 'plugin'], true)) {
                    return $pluginSlug;
                }

                if (in_array($name, ['pluginpath', 'plugin_path', 'path'], true)) {
                    return (string) $context['plugin_path'];
                }

                if (in_array($name, ['pluginroot', 'plugin_root', 'root'], true)) {
                    return (string) $context['plugin_root'];
                }
            }
        }

        if (in_array($name, ['widb', 'db', 'database'], true)) {
            return $this->WIdb;
        }

        if (in_array($name, ['context', 'package', 'plugincontext', 'plugin_context'], true)) {
            return $context;
        }

        if (in_array($name, ['meta', 'metadata'], true)) {
            return $meta;
        }

        if (in_array($name, ['pluginslug', 'plugin_slug', 'slug', 'plugin'], true)) {
            return $pluginSlug;
        }

        if ($parameter->isDefaultValueAvailable()) {
            return $parameter->getDefaultValue();
        }

        if ($parameter->allowsNull()) {
            return null;
        }

        return $context;
    }

    private function normalisePackagePath(string $baseDir, string $relativePath): ?string
    {
        $candidate = $baseDir . '/' . ltrim(str_replace('\\', '/', $relativePath), '/');
        $realBase = realpath($baseDir);
        $realCandidate = realpath($candidate);

        if ($realBase === false || $realCandidate === false) {
            return null;
        }

        if (strpos($realCandidate, $realBase) !== 0) {
            return null;
        }

        return $realCandidate;
    }

    private function filterExistingColumns(string $table, array $data): array
    {
        $filtered = [];

        foreach ($data as $column => $value) {
            if ($this->columnExists($table, (string) $column)) {
                $filtered[$column] = $value;
            }
        }

        return $filtered;
    }

    private function tableExists(string $table): bool
    {
        try {
            return $this->registryTableExists($table);
        } catch (Throwable $e) {
            return false;
        }
    }

    private function columnExists(string $table, string $column): bool
    {
        try {
            return $this->registryColumnExists($table, $column);
        } catch (Throwable $e) {
            return false;
        }
    }

    private function logPluginError(string $message): void
    {
        @file_put_contents(
            __DIR__ . '/WIPlugin_install_error.log',
            '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL,
            FILE_APPEND
        );
    }
}
?>
