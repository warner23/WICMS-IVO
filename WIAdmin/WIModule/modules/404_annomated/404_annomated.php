<?php
declare(strict_types=1);

/** Clean WICMS placeholder for legacy WIAdmin/WIModule/modules/404_annomated/404_annomated.php. */
class WIModule404Annomated
{
    public function __construct() {}

    public function editMod(): void
    {
        echo '<div class="wi-admin-preview"><h2>404 module</h2><p>Generic WICMS 404 module.</p></div>';
    }

    public function editPageContent($page_id): void
    {
        echo '<div class="wi-admin-preview"><h2>404 module</h2><p>Generic WICMS 404 module.</p></div>';
    }

    public function mod_name($page): void
    {
        echo '<section class="wi-core-public-page"><h1>404 module</h1><p>Generic WICMS 404 module.</p></section>';
    }

    public function Install($name = null): void
    {
        $this->mod_name((string) ($name ?? '404'));
    }
}
