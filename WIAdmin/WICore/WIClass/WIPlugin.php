<?php
declare(strict_types=1);

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

    private function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    private function cleanName($value): string
    {
        return preg_replace('/[^A-Za-z0-9_\-]/', '', (string)$value) ?? '';
    }

    private function registryRow(string $pluginSlug): ?array
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_plugin` WHERE `plugin_slug` = :slug LIMIT 1",
            ['slug' => $pluginSlug]
        );

        return $rows[0] ?? null;
    }

    private function previewHtml(string $pluginSlug, string $file, string $alt): string
    {
        if ($file === '') {
            return '<div style="height:120px;display:flex;align-items:center;justify-content:center;background:#f5f7fa;border-radius:8px;color:#94a3b8;">No Preview</div>';
        }

        return '<div style="height:120px;display:flex;align-items:center;justify-content:center;background:#f8fafc;border-radius:8px;overflow:hidden;">
                    <img src="WIPlugin/' . $this->e($pluginSlug) . '/' . $this->e($file) . '" alt="' . $this->e($alt) . '" style="max-width:100%;max-height:100%;object-fit:contain;">
                </div>';
    }

    private function badge(string $text, string $class = 'default'): string
    {
        return '<span class="label label-' . $this->e($class) . '">' . $this->e($text) . '</span>';
    }

    private function pluginMeta(string $pluginSlug): array
    {

        return $this->store->mergePluginData($pluginSlug);
    }

    private function renderPluginCard(array $meta, bool $installedMode = false): void
    {
        //var_dump($meta);
        $slug = (string)($meta['plugin_slug'] ?? '');
        if ($slug === '') {
            return;
        }

        $installed = ($this->registryRow($slug)['plugin_status'] ?? 'disabled') === 'enabled';
        $price = (float)($meta['plugin_price'] ?? 0);
        $currency = (string)($meta['plugin_currency'] ?? 'GBP');
        $subscription = !empty($meta['plugin_subscription']);

        echo '<div class="col-md-4 col-sm-6 col-xs-12">
                <div class="panel panel-info" style="border-radius:12px;overflow:hidden;min-height:390px;box-shadow:0 4px 14px rgba(0,0,0,.06);">
                    <div class="panel-heading" style="display:flex;justify-content:space-between;align-items:center;gap:10px;">
                        <strong>' . $this->e($meta['plugin_name'] ?? $slug) . '</strong>
                        <span>'
                            . $this->badge($installed ? 'Installed' : 'Available', $installed ? 'primary' : 'warning')
                            . ' ' .
                            ($subscription ? $this->badge('Subscription', 'success') : $this->badge('One-time', 'default'))
                        . '</span>
                    </div>

                    <div class="panel-body">
                        ' . $this->previewHtml($slug, (string)($meta['plugin_preview'] ?? ''), (string)($meta['plugin_name'] ?? $slug)) . '
                        <div style="margin-top:12px;">
                            <p><strong>Slug:</strong> ' . $this->e($slug) . '</p>
                            <p><strong>Version:</strong> ' . $this->e($meta['plugin_version'] ?? '1.0.0') . '</p>
                            <p><strong>Author:</strong> ' . $this->e($meta['plugin_author'] ?? 'Warner Infinity') . '</p>
                            <p><strong>Description:</strong> ' . $this->e($meta['plugin_description'] ?? '') . '</p>
                            <p><strong>Price:</strong> ' . $this->e($currency) . ' ' . $this->e((string)$price) . '</p>
                        </div>
                    </div>

                    <div class="panel-footer" style="display:flex;gap:8px;flex-wrap:wrap;">';

        if ($price <= 0) {
            if ($installedMode) {
                echo '<button type="button" class="btn btn-success" onclick="WIPlugin.enable(\'' . $this->e($slug) . '\')">Enable</button>
                      <button type="button" class="btn btn-danger" onclick="WIPlugin.disable(\'' . $this->e($slug) . '\')">Disable</button>';
            } else {
                echo '<button type="button" class="btn btn-success" onclick="WIPlugin.install(\'' . $this->e($slug) . '\')">Install</button>
                      <button type="button" class="btn btn-danger" onclick="WIPlugin.uninstall(\'' . $this->e($slug) . '\')">Uninstall</button>';
            }
        } else {
            echo '<button type="button" class="btn btn-warning" onclick="WIPlugin.buy(\'' . $this->e($slug) . '\')">Buy Now</button>';
        }

        echo '      </div>
                </div>
              </div>';
    }

    /*
    |--------------------------------------------------------------------------
    | UI Data Methods
    |--------------------------------------------------------------------------
    */

    public function marketplace(): void
    {
        $plugins = $this->store->listFilesystemPlugins();
        //var_dump($plugins);
        echo '<div class="row">';
        foreach ($plugins as $pluginSlug) {

            $this->renderPluginCard($this->pluginMeta((string)$pluginSlug), false);
        }
        echo '</div>';
    }

    public function installedPlugins(): void
    {
        $rows = $this->WIdb->select("SELECT * FROM `wi_plugin` ORDER BY `plugin_id` ASC");

        echo '<div class="row">';
        foreach ($rows as $row) {
            $slug = (string)($row['plugin_slug'] ?? $row['plugin_name'] ?? '');
            if ($slug === '') {
                continue;
            }
            $this->renderPluginCard($this->pluginMeta($slug), true);
        }
        echo '</div>';
    }

    public function subscriptions(): void
    {
        $rows = $this->WIdb->select("SELECT * FROM `wi_plugin_subscriptions` ORDER BY `subscription_id` DESC");

        echo '<div class="table-responsive"><table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Plugin</th>
                        <th>Price</th>
                        <th>Currency</th>
                        <th>Period</th>
                        <th>Status</th>
                        <th>Start</th>
                        <th>End</th>
                    </tr>
                </thead><tbody>';

        foreach ($rows as $row) {
            echo '<tr>
                    <td>' . $this->e($row['plugin_slug'] ?? '') . '</td>
                    <td>' . $this->e($row['subscription_price'] ?? '') . '</td>
                    <td>' . $this->e($row['subscription_currency'] ?? '') . '</td>
                    <td>' . $this->e($row['subscription_period'] ?? '') . '</td>
                    <td>' . $this->e($row['subscription_status'] ?? '') . '</td>
                    <td>' . $this->e($row['subscription_start'] ?? '') . '</td>
                    <td>' . $this->e($row['subscription_end'] ?? '') . '</td>
                  </tr>';
        }

        echo '</tbody></table></div>';
    }

    public function licenses(): void
    {
        $rows = $this->WIdb->select("SELECT * FROM `wi_plugin_licenses` ORDER BY `license_id` DESC");

        echo '<div class="table-responsive"><table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Plugin</th>
                        <th>License Key</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Expires</th>
                    </tr>
                </thead><tbody>';

        foreach ($rows as $row) {
            echo '<tr>
                    <td>' . $this->e($row['plugin_slug'] ?? '') . '</td>
                    <td>' . $this->e($row['license_key'] ?? '') . '</td>
                    <td>' . $this->e($row['license_type'] ?? '') . '</td>
                    <td>' . $this->e($row['license_status'] ?? '') . '</td>
                    <td>' . $this->e($row['license_created'] ?? '') . '</td>
                    <td>' . $this->e($row['license_expires'] ?? '') . '</td>
                  </tr>';
        }

        echo '</tbody></table></div>';
    }

    public function invoices(): void
    {
        $rows = $this->WIdb->select("SELECT * FROM `wi_plugin_invoices` ORDER BY `invoice_id` DESC");

        echo '<div class="table-responsive"><table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Order ID</th>
                        <th>Total</th>
                        <th>Currency</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead><tbody>';

        foreach ($rows as $row) {
            echo '<tr>
                    <td>' . $this->e($row['invoice_number'] ?? '') . '</td>
                    <td>' . $this->e($row['order_id'] ?? '') . '</td>
                    <td>' . $this->e($row['invoice_total'] ?? '') . '</td>
                    <td>' . $this->e($row['invoice_currency'] ?? '') . '</td>
                    <td>' . $this->e($row['invoice_status'] ?? '') . '</td>
                    <td>' . $this->e($row['invoice_date'] ?? '') . '</td>
                  </tr>';
        }

        echo '</tbody></table></div>';
    }

    /*
    |--------------------------------------------------------------------------
    | Install / Enable Methods
    |--------------------------------------------------------------------------
    */

    public function install(string $pluginSlug): bool
    {
        $pluginSlug = $this->cleanName($pluginSlug);
        if ($pluginSlug === '') {
            return false;
        }

        $meta = $this->pluginMeta($pluginSlug);
        $existing = $this->registryRow($pluginSlug);

        if ($existing) {
            return (bool)$this->WIdb->update(
                'wi_plugin',
                [
                    'plugin_status' => 'enabled',
                    'plugin_version' => $meta['plugin_version'] ?? '1.0.0',
                    'plugin_author' => $meta['plugin_author'] ?? 'Warner Infinity',
                    'plugin_description' => $meta['plugin_description'] ?? ''
                ],
                '`plugin_slug` = :slug',
                ['slug' => $pluginSlug]
            );
        }

        return (bool)$this->WIdb->insert('wi_plugin', [
            'plugin_name' => $meta['plugin_name'] ?? $pluginSlug,
            'plugin_slug' => $pluginSlug,
            'plugin_version' => $meta['plugin_version'] ?? '1.0.0',
            'plugin_author' => $meta['plugin_author'] ?? 'Warner Infinity',
            'plugin_description' => $meta['plugin_description'] ?? '',
            'plugin_status' => 'enabled'
        ]);
    }

    public function uninstall(string $pluginSlug): bool
    {
        $pluginSlug = $this->cleanName($pluginSlug);
        if ($pluginSlug === '') {
            return false;
        }

        return (bool)$this->WIdb->update(
            'wi_plugin',
            ['plugin_status' => 'disabled'],
            '`plugin_slug` = :slug',
            ['slug' => $pluginSlug]
        );
    }

    public function enable(string $pluginSlug): bool
    {
        return $this->install($pluginSlug);
    }

    public function disable(string $pluginSlug): bool
    {
        return $this->uninstall($pluginSlug);
    }

    /*
    |--------------------------------------------------------------------------
    | Commerce Wrappers
    |--------------------------------------------------------------------------
    */

    public function createOrder(
        int $userId,
        string $pluginSlug,
        float $price,
        string $currency = 'GBP',
        string $gateway = 'manual'
    ): int {
        return $this->commerce->createOrder($userId, $pluginSlug, $price, $currency, $gateway);
    }

    public function completePurchase(
        int $userId,
        string $pluginSlug,
        float $price,
        string $currency = 'GBP',
        string $gateway = 'manual',
        string $licenseType = 'lifetime',
        bool $subscription = false,
        string $subscriptionPeriod = 'monthly'
    ): array {
        return $this->commerce->completePurchase(
            $userId,
            $pluginSlug,
            $price,
            $currency,
            $gateway,
            $licenseType,
            $subscription,
            $subscriptionPeriod
        );
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
}
?>