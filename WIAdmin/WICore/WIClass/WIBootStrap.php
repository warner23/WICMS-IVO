<?php
declare(strict_types=1);

/**
 * FILE:
 * WICMS-IVO/WIAdmin/WICore/WIClass/WIBootStrap.php
 *
 * Canonical front layout/bootstrap wrapper for WICMS modules.
 */
final class WIBootStrap
{
    private WIdb $WIdb;
    private WIMaintenace $maint;
    private WIWebsite $Web;
    private WIModules $mod;
    private WIPage $page;

    private int $leftSidebarPower = 0;
    private int $rightSidebarPower = 0;
    private ?string $leftSidebarModule = null;
    private ?string $rightSidebarModule = null;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->maint = new WIMaintenace();
        $this->Web = new WIWebsite();
        $this->mod = new WIModules();
        $this->page = new WIPage();
    }

    public function startMod($page): void
    {
        $pageName = $this->normalisePageName($page);
        $this->hydratePageLayout($pageName);

        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-index">';

        if ($this->leftSidebarPower > 0 && $this->leftSidebarModule !== null) {
            echo '<aside class="col-lg-2 col-md-2 col-sm-2 col-xs-12 sidenav" id="sidenavL">';
            $this->mod->getMod($this->leftSidebarModule);
            echo '</aside>';
        }

        echo '<section class="' . $this->contentColumnClass() . '" id="wi-main-content">';
    }

    public function startContentsHolder(): void
    {
        echo '<div class="container-fluid text-center">';
        echo '<div class="row content">';
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
    }

    public function endContentsHolder(): void
    {
        echo '</div>';
        echo '</div>';
        echo '</div>';

        echo '<script src="WICore/WIJ/WIIndex.js"></script>';
    }

    public function endMod($page): void
    {
        $pageName = $this->normalisePageName($page);

        if ($this->rightSidebarPower === 0 && $this->rightSidebarModule === null) {
            $this->hydratePageLayout($pageName);
        }

        echo '</section>';

        if ($this->rightSidebarPower > 0 && $this->rightSidebarModule !== null) {
            echo '<aside class="col-lg-2 col-md-2 col-sm-2 col-xs-12 sidenav" id="sidenavR">';
            $this->mod->getMod($this->rightSidebarModule);
            echo '</aside>';
        }

        echo '</div>';
    }

    private function hydratePageLayout(string $pageName): void
    {
        $this->leftSidebarPower  = $this->page->PageModPower($pageName, 'left_sidebar');
        $this->rightSidebarPower = $this->page->PageModPower($pageName, 'right_sidebar');

        $leftSidebar = $this->page->PageMod($pageName, 'left_sidebar');
        $rightSidebar = $this->page->PageMod($pageName, 'right_sidebar');

        $this->leftSidebarModule = $this->normaliseModuleName($leftSidebar);
        $this->rightSidebarModule = $this->normaliseModuleName($rightSidebar);

        if ($this->leftSidebarPower === 0) {
            $this->leftSidebarModule = null;
        }

        if ($this->rightSidebarPower === 0) {
            $this->rightSidebarModule = null;
        }
    }

    private function contentColumnClass(): string
    {
        if ($this->leftSidebarPower > 0 && $this->rightSidebarPower > 0) {
            return 'col-lg-8 col-md-8 col-sm-8 col-xs-12';
        }

        if ($this->leftSidebarPower > 0 || $this->rightSidebarPower > 0) {
            return 'col-lg-10 col-md-10 col-sm-10 col-xs-12';
        }

        return 'col-lg-12 col-md-12 col-sm-12 col-xs-12';
    }

    private function normalisePageName(mixed $page): string
    {
        $value = preg_replace('/[^A-Za-z0-9_-]/', '', (string) $page) ?? '';
        return $value !== '' ? $value : 'home';
    }

    private function normaliseModuleName(mixed $module): ?string
    {
        if ($module === null) {
            return null;
        }

        $value = preg_replace('/[^A-Za-z0-9_-]/', '', trim((string) $module)) ?? '';

        return $value !== '' ? $value : null;
    }
}