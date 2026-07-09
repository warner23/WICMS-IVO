<?php
declare(strict_types=1);

/**
 * WICMS clean-base page module.
 * Plugins/themes can replace or extend this module later.
 */
class documents
{
    protected WIModules $mod;
    protected WIBootStrap $Boot;

    public function __construct()
    {
        $this->mod = new WIModules();
        $this->Boot = new WIBootStrap();
    }

    public function editPageContent($page_id): void
    {
        $page = 'documents';
        $heading = $this->escape($this->moduleText($page, 'text', 'Documents'));
        $body = $this->escape($this->moduleText($page, 'text1', 'Core document page placeholder.'));
        echo <<<HTML
<div class="wi-core-page-editor" style="max-width:980px;margin:24px auto;background:#fff;border:1px solid #e5e7eb;border-radius:20px;padding:24px;box-shadow:0 18px 40px rgba(15,23,42,.08);">
    <h2 style="margin:0 0 10px;color:#0f172a;">$heading</h2>
    <p style="margin:0 0 22px;color:#64748b;">$body</p>
    <label style="display:block;margin-bottom:14px;font-weight:700;color:#334155;">Title
        <input type="text" name="title" id="title" value="$heading" style="display:block;width:100%;margin-top:8px;border:1px solid #cbd5e1;border-radius:12px;padding:12px 14px;">
    </label>
    <label style="display:block;font-weight:700;color:#334155;">Intro text
        <textarea name="history" id="history" style="display:block;width:100%;min-height:140px;margin-top:8px;border:1px solid #cbd5e1;border-radius:12px;padding:12px 14px;">$body</textarea>
    </label>
</div>
HTML;
    }

    public function mod_name($page): void
    {
        $this->Boot->startMod($page, 'Documents', '<p>Core document page placeholder.</p>');
        $this->Boot->contentsHolder();
        echo '<section class="wi-core-public-page" style="max-width:1100px;margin:32px auto;padding:32px;background:#fff;border:1px solid #e5e7eb;border-radius:24px;box-shadow:0 20px 45px rgba(15,23,42,.08);">';
        echo '<h1 style="margin:0 0 12px;color:#0f172a;">' . $this->escape('Documents') . '</h1>';
        echo '<p style="margin:0;color:#475569;font-size:16px;line-height:1.7;">' . $this->escape('Document control can be extended by plugins, but the clean base keeps this page generic.') . '</p>';
        echo '</section>';
        $this->Boot->endContentsHolder();
        $this->Boot->endMod($page);
    }

    protected function moduleText(string $moduleName, string $field, string $fallback): string
    {
        if (method_exists($this->mod, 'module')) {
            ob_start();
            $this->mod->module($moduleName, $field);
            $value = trim((string) ob_get_clean());
            if ($value !== '') { return $value; }
        }
        return $fallback;
    }

    protected function escape(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }
}
