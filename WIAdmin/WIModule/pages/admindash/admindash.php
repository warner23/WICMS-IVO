<?php
declare(strict_types=1);

class admincompdash
{
    protected WIPage $page;
    protected WIModules $mod;
    protected WIBootStrap $Boot;
    protected WIDashboard $dash;

    public function __construct()
    {
        $this->page = new WIPage();
        $this->mod = new WIModules();
        $this->Boot = new WIBootStrap();
        $this->dash = new WIDashboard();
    }

    public function Install(): void
    {
        if (!method_exists($this->page, 'pageExists') || !method_exists($this->page, 'newPage')) {
            return;
        }

        if ($this->page->pageExists('admincompdash')) {
            return;
        }

        $this->page->newPage('admincompdash');
    }

    private function renderDashboardPage(string $page): void
    {
        $this->Boot->startMod($page);
        $this->Boot->startContentsHolder();

        $this->dash->dashboard();

        $this->Boot->endContentsHolder();
        $this->Boot->endMod($page);
    }

    public function editPageContent($page): void
    {
        $safePage = is_scalar($page) ? (string) $page : 'admincompdash';
        $this->renderDashboardPage($safePage);
    }

    public function mod_name($page): void
    {
        $safePage = is_scalar($page) ? (string) $page : 'admincompdash';
        $this->renderDashboardPage($safePage);
    }
}