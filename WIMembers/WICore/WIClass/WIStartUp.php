<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WIMembers
| Project: WI Ecosystem
| File: WIStartUp.php
| Location: /WIMembers/WICore/WIClass/WIStartUp.php
| Type: Class
| Layer: Startup / Page Shell
| Purpose Area: Member workspace shell, modular page rendering and WI page compatibility
| Version: 1.0.2
| Created: 2026-05-24
| Last Updated: 2026-06-09
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Boots WIMembers pages through the same WI-style page lifecycle used by the
| wider WICMS platform: boot, optional panel/top_head/header, page content and
| optional footer. The member workspace keeps its own sidebar/workspace chrome,
| while root/public navigation is always available through the sidebar's
| DB-driven root menu.
*/

final class WIStartUp
{
    private WIWebsite $web;
    private WIModules $modules;
    private WILogin $login;
    private string $page = 'profile';

    public function __construct(?WIWebsite $web = null, ?WIModules $modules = null, ?WILogin $login = null)
    {
        $this->web = $web ?? new WIWebsite();
        $this->modules = $modules ?? new WIModules();
        $this->login = $login ?? new WILogin();
    }

    public function boot(string $page): void
    {
        $this->page = $this->sanitizePage($page);

        $this->web->StartUp();
        $this->web->Meta($this->page);
        $this->web->Styling($this->page);
        $this->web->Scripts($this->page);
        $this->web->webSite_icons();

        echo '<script>var $_lang = ' . (class_exists('WILang') ? WILang::all() : '{}') . ';</script>';
        echo '</head><body class="wi-members-body" data-wi-page="' . wi_e($this->page) . '">';
    }

    /**
     * Backwards-compatible one-call renderer used by the current member pages.
     */
    public function render(string $page): void
    {
        $this->boot($page);
        $this->header($page);
        $this->content($page);
        $this->footer($page);
    }

    /**
     * Member pages render their own workspace chrome inside the content module.
     *
     * Do not render legacy/root panel, top_head, public header, public menu or
     * marketing actions here. The profile workspace sidebar/topbar is the source
     * of truth, and rendering root chrome here causes duplicate links such as
     * Public site / Admin portal / Logout plus old constant dependencies.
     */
    public function header(?string $page = null): void
    {
        $this->sanitizePage($page ?? $this->page);
        $this->assertLoggedIn();
    }

    public function content(?string $page = null): void
    {
        $page = $this->sanitizePage($page ?? $this->page);
        $this->assertLoggedIn();

        $module = $this->web->pageModule($page, 'contents');
        $this->modules->getModMain($module, $page, $module);
    }

    /**
     * Compatibility method for older calls that expected shell and content in
     * one call. New page files can call header() and content() separately.
     */
    public function renderChromeAndContent(?string $page = null): void
    {
        $page = $this->sanitizePage($page ?? $this->page);
        $this->header($page);
        $this->content($page);
    }

    public function footer(?string $page = null): void
    {
        // Member pages intentionally avoid the public marketing footer.
        // The workspace modules provide the visual shell; this simply closes the page.
        $this->web->backendJs();
        echo '</body></html>';
    }

    private function assertLoggedIn(): void
    {
        if (!$this->login->isLoggedIn()) {
            unauthorized_Redirect($this->web->rootUrl('login.php'));
        }
    }

    private function isPagePartEnabled(string $page, string $column): bool
    {
        return (string) $this->web->pageModPower($page, $column) === '1';
    }

    private function sanitizePage(string $page): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '', trim($page)) ?: 'profile';
    }
}
