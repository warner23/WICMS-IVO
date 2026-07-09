<?php
declare(strict_types=1);

/** Clean WICMS placeholder for legacy WIAdmin/WIModule/pages/404/404.php. */
class WIPage404
{
    public function __construct() {}

    public function editMod(): void
    {
        echo '<div class="wi-admin-preview"><h2>Page not found</h2><p>The page you requested could not be found.</p></div>';
    }

    public function editPageContent($page_id): void
    {
        echo '<div class="wi-admin-preview"><h2>Page not found</h2><p>The page you requested could not be found.</p></div>';
    }

    public function mod_name($page): void
    {
        echo '<section class="wi-core-public-page"><h1>Page not found</h1><p>The page you requested could not be found.</p></section>';
    }

    public function Install($name = null): void
    {
        $this->mod_name((string) ($name ?? '404'));
    }
}
