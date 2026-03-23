<?php
declare(strict_types=1);

/**
 * WIBlog Main Bootstrap
 * Location: WIPlugin/WIBlog/blog/WIBlog.php
 */

if (!defined('WIBLOG_LOADED')) {
    define('WIBLOG_LOADED', true);
}

if (!defined('WIBLOG_ROOT')) {
    define('WIBLOG_ROOT', dirname(__DIR__) . DIRECTORY_SEPARATOR);
}

if (!defined('WIBLOG_BLOG_PATH')) {
    define('WIBLOG_BLOG_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

if (!defined('WIBLOG_INSTALL_PATH')) {
    define('WIBLOG_INSTALL_PATH', WIBLOG_ROOT . 'Install' . DIRECTORY_SEPARATOR);
}

if (!defined('WIBLOG_ASSET_PATH')) {
    define('WIBLOG_ASSET_PATH', WIBLOG_ROOT . 'assets' . DIRECTORY_SEPARATOR);
}

if (!defined('WIBLOG_PLUGIN_JSON')) {
    define('WIBLOG_PLUGIN_JSON', WIBLOG_ROOT . 'plugin.json');
}

if (!defined('WIBLOG_UPDATE_JSON')) {
    define('WIBLOG_UPDATE_JSON', WIBLOG_ROOT . 'update.json');
}

#[\AllowDynamicProperties]
class WIBlog
{
    private WIdb $WIdb;
    private array $manifest = [];
    private array $updateManifest = [];
    private string $version = '1.0.0';

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();

        $this->manifest = $this->loadJsonFile(WIBLOG_PLUGIN_JSON);
        $this->updateManifest = $this->loadJsonFile(WIBLOG_UPDATE_JSON);
        $this->version = (string)($this->manifest['version'] ?? '1.0.0');

        $this->loadSupportFiles();
    }

    /**
     * Load optional supporting classes if present.
     */
    private function loadSupportFiles(): void
    {
        $files = [
            WIBLOG_BLOG_PATH . 'WIBlog_Options.php',
            WIBLOG_BLOG_PATH . 'WIBlog_Admin.php',
            WIBLOG_BLOG_PATH . 'WIBlog_Render.php',
            WIBLOG_BLOG_PATH . 'WIBlog_SEO.php',
            WIBLOG_BLOG_PATH . 'WIBlog_Permissions.php',
        ];

        foreach ($files as $file) {
            if (file_exists($file)) {
                require_once $file;
            }
        }
    }

    /**
     * Read JSON safely.
     */
    private function loadJsonFile(string $path): array
    {
        if (!file_exists($path)) {
            return [];
        }

        $json = file_get_contents($path);

        if ($json === false || trim($json) === '') {
            return [];
        }

        $decoded = json_decode($json, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * Escape output.
     */
    private function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Basic plugin info from plugin.json
     */
    public function getManifest(): array
    {
        return $this->manifest;
    }

    public function getUpdateManifest(): array
    {
        return $this->updateManifest;
    }

    public function getName(): string
    {
        return (string)($this->manifest['name'] ?? 'WIBlog');
    }

    public function getSlug(): string
    {
        return (string)($this->manifest['slug'] ?? 'WIBlog');
    }

    public function getTitle(): string
    {
        return (string)($this->manifest['title'] ?? 'Blog');
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getAuthor(): string
    {
        return (string)($this->manifest['author'] ?? 'Warner Infinity');
    }

    public function getDescription(): string
    {
        return (string)($this->manifest['description'] ?? '');
    }

    /**
     * Requirements
     */
    public function getRequiredPhp(): string
    {
        return (string)($this->manifest['min_php'] ?? '8.2');
    }

    public function getRequiredWICMS(): string
    {
        return (string)($this->manifest['min_wicms'] ?? '1.0.0');
    }

    public function isCompatiblePhp(): bool
    {
        return version_compare(PHP_VERSION, $this->getRequiredPhp(), '>=');
    }

    /**
     * Admin / frontend readiness checks
     */
    public function isInstalled(): bool
    {
        try {
            $result = $this->WIdb->select(
                "SHOW TABLES LIKE 'wi_blog_posts'"
            );

            return !empty($result);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function isReady(): bool
    {
        return $this->isCompatiblePhp() && $this->isInstalled();
    }

    /**
     * Main plugin init hook
     */
    public function init(): void
    {
        if (!$this->isCompatiblePhp()) {
            return;
        }
    }

    /**
     * Frontend entry point
     */
    public function renderFront(string $view = 'index', array $data = []): void
    {
        if (class_exists('WIBlog_Render')) {
            $renderer = new WIBlog_Render($this->WIdb, $this->manifest);
            $renderer->render($view, $data);
            return;
        }

        echo '<div class="alert alert-warning">WIBlog_Render is missing.</div>';
    }

    /**
     * Admin entry point
     */
    public function renderAdmin(string $view = 'dashboard', array $data = []): void
    {
        if (class_exists('WIBlog_Admin')) {
            $admin = new WIBlog_Admin($this->WIdb, $this->manifest);
            $admin->render($view, $data);
            return;
        }

        echo '<div class="alert alert-warning">WIBlog_Admin is missing.</div>';
    }

    /**
     * Settings/options entry point
     */
    public function renderOptions(string $view = 'general'): void
    {
        if (class_exists('WIBlog_Options')) {
            $options = new WIBlog_Options($this->WIdb, $this->manifest);
            $options->render($view);
            return;
        }

        echo '<div class="alert alert-warning">WIBlog_Options is missing.</div>';
    }

    /**
     * Useful dashboard card / plugin info block
     */
    public function pluginCard(): void
    {
        echo '<div class="panel panel-info" style="border-radius:12px;overflow:hidden;">
                <div class="panel-heading">
                    <strong>' . $this->e($this->getTitle()) . '</strong>
                </div>
                <div class="panel-body">
                    <p><strong>Slug:</strong> ' . $this->e($this->getSlug()) . '</p>
                    <p><strong>Version:</strong> ' . $this->e($this->getVersion()) . '</p>
                    <p><strong>Author:</strong> ' . $this->e($this->getAuthor()) . '</p>
                    <p><strong>Description:</strong> ' . $this->e($this->getDescription()) . '</p>
                    <p><strong>PHP Required:</strong> ' . $this->e($this->getRequiredPhp()) . '</p>
                    <p><strong>WICMS Required:</strong> ' . $this->e($this->getRequiredWICMS()) . '</p>
                    <p><strong>Installed:</strong> ' . ($this->isInstalled() ? 'Yes' : 'No') . '</p>
                </div>
              </div>';
    }

    /**
     * Future update support
     */
    public function updateAvailable(): bool
    {
        $remoteVersion = (string)($this->updateManifest['version'] ?? '');

        if ($remoteVersion === '') {
            return false;
        }

        return version_compare($remoteVersion, $this->getVersion(), '>');
    }

    public function getUpdateVersion(): string
    {
        return (string)($this->updateManifest['version'] ?? '');
    }

    public function getUpdateDownloadUrl(): string
    {
        return (string)($this->updateManifest['download_url'] ?? $this->updateManifest['download'] ?? '');
    }
}
?>