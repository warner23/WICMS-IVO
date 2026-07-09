<?php
declare(strict_types=1);

/**
 * FILE:
 * WICMS-IVO/WICore/WIClass/WIStartUp.php
 * 
 * Canonical front-side startup helper for WICMS.
 */
final class WIStartUp
{
    private WIWebsite $web;
    protected WIModules $mod;

    public function __construct()
    {
        WISession::startSession();
        WILang::loadFromSession();

        $this->web = new WIWebsite();
        $this->mod = new WIModules();
    }

    public function boot(string $page): void
    {
        $page = $this->sanitizePageName($page);

        $this->web->StartUp();
        $this->web->webSite_icons();
        $this->web->Meta($page);
        $this->web->Styling($page);
        $this->web->Scripts($page);

        echo '<script>var $_lang = ' . WILang::all() . ';</script>' . PHP_EOL;
        echo '</head><body>' . PHP_EOL;
    }

    public function header($page): void
    {

    $topPower = (int) $this->web->pageModPower($page, "top_head");

    if ($topPower > 0) {
        $this->mod->getMod("top_head", $page);
    }

    $headerPower = (int) $this->web->pageModPower($page, "header");

    if ($headerPower > 0 && method_exists($this->web, 'MainHeader')) {
        $this->web->MainHeader();
    }

    if (method_exists($this->web, 'MainMenu')) {
        $this->web->MainMenu();
    }
    }

    public function footer(): void
    {
        $this->web->footer();
        $this->web->backendJs();
        echo '</body></html>';
    }

    private function sanitizePageName(string $page): string
    {
        $value = preg_replace('/[^A-Za-z0-9_-]/', '', trim($page)) ?? '';
        return $value !== '' ? $value : 'home';
    }
}