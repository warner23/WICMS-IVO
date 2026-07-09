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
| File: WIWebsite.php
| Location: /WIMembers/WICore/WIClass/WIWebsite.php
| Type: Class
| Layer: Website / Page Support
| Purpose Area: Member workspace page metadata, assets, header, root menu and footer
| Version: 1.0.2
| Created: 2026-05-24
| Last Updated: 2026-06-09
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Provides WIMembers-compatible page support while preserving WI's root/public
| database-driven menu. The member workspace may style the menu differently,
| but menu data must come from wi_menu rather than hardcoded public links.
*/

#[\AllowDynamicProperties]
final class WIWebsite
{
    private WIdb $WIdb;
    private WILogin $login;
    private WIUser $user;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->login = new WILogin();
        $this->user = new WIUser();
    }

    public function StartUp(): void
    {
        echo '<!DOCTYPE html><html class="no-js" lang="en"><head><meta charset="utf-8"><title>' . wi_e(defined('WEBSITE_NAME') ? WEBSITE_NAME : 'WICMS') . ' Members</title>';
        echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    }

    public function Meta(string $page): void
    {
        if (!$this->WIdb->tableExists('wi_meta')) {
            return;
        }

        foreach ($this->WIdb->select('SELECT * FROM `wi_meta` WHERE `page` = :page', ['page' => $page]) as $res) {
            echo '<meta name="' . wi_e($res['name'] ?? '') . '" content="' . wi_e($res['content'] ?? '') . '">';
        }
    }

    public function Theme(): string
    {
        if (!$this->WIdb->tableExists('wi_theme')) {
            return 'WITheme/WICMS/';
        }

        $row = $this->WIdb->row('SELECT * FROM `wi_theme` WHERE `in_use` = 1 LIMIT 1');
        $destination = str_replace('\\', '/', trim((string) ($row['destination'] ?? '')));

        if ($destination === '') {
            return 'WITheme/WICMS/';
        }

        return rtrim($destination, '/') . '/';
    }

    public function Styling(string $page): void
    {
        $themeBase = $this->Theme();
        // Root/public chrome styles so WIProfile pages match /index.php.
        $this->emitStylesheet($themeBase . 'site/css/WIMarketing.css');

        // Workspace styles so the existing WIProfile sidebar/content design remains intact.
        $this->emitStylesheet($themeBase . 'user/css/WIMembers.css');

        if (!$this->WIdb->tableExists('wi_css')) {
            return;
        }

        foreach ($this->WIdb->select('SELECT * FROM `wi_css` WHERE `page` = :page', ['page' => $page]) as $res) {
            $href = trim((string) ($res['href'] ?? ''));
            $rel = trim((string) ($res['rel'] ?? 'stylesheet')) ?: 'stylesheet';

            if ($href !== '') {
                $this->emitStylesheet($themeBase . $href, $rel);
            }
        }
    }

    public function Scripts(string $page): void
    {
        echo '<script>window.WIMemberPage = ' . json_encode($page, JSON_THROW_ON_ERROR) . '; window.WICSRF_TOKEN = ' . json_encode(WICsrf::getToken(), JSON_THROW_ON_ERROR) . '; window.WIMEMBERS_AJAX_URL = ' . json_encode($this->memberUrl('WICore/WIAjax/WIMembersAjax.php'), JSON_THROW_ON_ERROR) . ';</script>';

        if ($this->WIdb->tableExists('wi_scripts')) {
            foreach ($this->WIdb->select('SELECT * FROM `wi_scripts` WHERE `page` = :page', ['page' => $page]) as $res) {
                $src = trim((string) ($res['src'] ?? ''));

                if ($src !== '') {
                    echo '<script src="' . wi_e($this->rootUrl($this->Theme() . $src)) . '" type="text/javascript"></script>';
                }
            }
        }

        echo '<script src="' . wi_e($this->memberUrl('WICore/WIJ/WIMembers.js?v=20260613')) . '" defer></script>';
    }


    /**
     * Root compatibility helper used by legacy panel/header modules.
     * Keep output aligned with the original root WIWebsite::google_lang().
     */
    public function google_lang(): void
    {
        echo '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                         <div class="flags-wrapper">
                         <div id="google_translate_element"></div><script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: "en", layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, "google_translate_element");
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
                         </div>
                    </div>';
    }

    /**
     * Root compatibility helper used by legacy social/footer modules.
     * Social links remain DB-driven from wi_social.
     */
    public function Social(): void
    {
        if (!$this->WIdb->tableExists('wi_social')) {
            return;
        }

        $result = $this->WIdb->select('SELECT * FROM `wi_social`');

        echo '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">';
        echo '<ul class="social_media">';

        foreach ($result as $res) {
            $href = $this->e($res['href'] ?? '#');
            $name = $this->e($res['name'] ?? '');

            if ($name === '') {
                continue;
            }

            echo '<li>';
            echo '<a href="' . $href . '" target="_blank" rel="noopener" data-placement="bottom" data-toggle="tooltip" class="fa fa-' . $name . ' fa-5x" title="' . $name . '">';
            echo $name;
            echo '</a>';
            echo '</li>';
        }

        echo '</ul>';
        echo '</div>';
    }

    private function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public function webSite_icons(): void
    {
        if (!$this->WIdb->tableExists('wi_site')) {
            return;
        }

        $row = $this->WIdb->row('SELECT * FROM `wi_site` LIMIT 1');

        if (!empty($row['favicon'])) {
            echo '<link rel="icon" type="image/png" href="' . wi_e($this->rootUrl('WIAdmin/WIMedia/Img/favicon/' . rawurlencode((string) $row['favicon']))) . '">';
        }
    }

    public function pageModPower(string $page, string $column)
    {
        return $this->pageColumn($page, $column);
    }

    public function PageMod(string $page, string $column): string
    {
        $value = $this->pageColumn($page, $column);

        if ($column === 'contents') {
            return is_string($value) && trim($value) !== '' ? trim($value) : $page;
        }

        if ((string) $value === '1') {
            return $column;
        }

        if (is_string($value) && trim($value) !== '' && trim($value) !== '0') {
            return trim($value);
        }

        return $column;
    }

    public function pageModule(string $page, string $column = 'contents'): string
    {
        return $this->PageMod($page, $column);
    }

    private function pageColumn(string $page, string $column)
    {
        if (!$this->WIdb->tableExists('wi_page') || !$this->WIdb->columnExists('wi_page', $column)) {
            return $column === 'contents' ? $page : 0;
        }

        return $this->WIdb->selectColumn(
            'SELECT * FROM `wi_page` WHERE `name` = :page LIMIT 1',
            ['page' => $page],
            $column
        ) ?? ($column === 'contents' ? $page : 0);
    }

    public function MainHeader(): void
    {
        $siteName = $this->siteName();
        $logoPath = $this->headerLogoPath();

        echo '<header class="wi-marketing-topbar" role="banner">';
        echo '<div class="wi-marketing-container wi-topbar-inner">';
        echo '<a class="wi-brand" href="' . wi_e($this->rootUrl('index.php')) . '" aria-label="' . wi_e($siteName) . ' home">';

        if ($logoPath !== '') {
            echo '<span class="wi-brand-logo"><img src="' . wi_e($this->rootUrl($logoPath)) . '" alt="' . wi_e($siteName) . '"></span>';
        } else {
            echo '<span class="wi-brand-mark">WI</span>';
        }

        echo '<span class="wi-brand-copy">';
        echo '<strong>' . wi_e($siteName) . '</strong>';
        echo '<small>Powered by WICMS</small>';
        echo '</span>';
        echo '</a>';

        echo '<div class="wi-topbar-copy">';
        echo '<span>Flexible WICMS platform for websites, members, plugins and themes</span>';
        echo '</div>';

        echo '<div class="wi-topbar-actions">';
        echo '<a class="wi-topbar-link" href="' . wi_e($this->rootUrl('login.php')) . '">Member login</a>';
        echo '<a class="wi-topbar-button" href="' . wi_e($this->rootUrl('alogin.php')) . '">Admin portal</a>';
        echo '</div>';

        echo '</div>';
        echo '</header>';
    }

    /**
     * Renders the same DB-driven public navigation used by root/index.php.
     * Sidebar mode is retained for the WIProfile workspace menu footer.
     */
    public function MainMenu(string $placement = 'primary'): void
    {
        $placement = preg_replace('/[^a-zA-Z0-9_\-]/', '', $placement) ?: 'primary';

        if ($placement === 'sidebar') {
            // Member sidebar no longer injects the public/root DB menu.
            return;
        }

        $items = $this->rootMenuTree();

        echo '<nav class="wi-marketing-nav" role="navigation" aria-label="Primary navigation">';
        echo '<div class="wi-marketing-container wi-nav-inner">';
        echo '<button class="wi-nav-toggle" type="button" aria-label="Open navigation" onclick="document.body.classList.toggle(&quot;wi-nav-open&quot;)">';
        echo '<span></span><span></span><span></span>';
        echo '</button>';

        echo '<div class="wi-nav-links">';
        foreach ($items as $item) {
            $this->renderPrimaryMenuItem($item);
        }
        echo '</div>';

        echo '<div class="wi-nav-actions">';
        if ($this->login->isLoggedIn()) {
            echo '<a class="wi-nav-link" href="' . wi_e($this->rootUrl('profile.php')) . '">' . wi_e(class_exists('WILang') ? WILang::get('profile') : 'Profile') . '</a>';
            echo '<a class="wi-nav-link" href="' . wi_e($this->rootUrl('logout.php')) . '">' . wi_e(class_exists('WILang') ? WILang::get('logout') : 'Logout') . '</a>';
        } else {
            echo '<a class="wi-nav-link" href="' . wi_e($this->rootUrl('register.php')) . '">' . wi_e(class_exists('WILang') ? WILang::get('register') : 'Register') . '</a>';
            echo '<a class="wi-nav-link" href="' . wi_e($this->rootUrl('login.php')) . '">' . wi_e(class_exists('WILang') ? WILang::get('login') : 'Login') . '</a>';
            echo '<a class="wi-nav-cta" href="' . wi_e($this->rootUrl('alogin.php')) . '">Admin portal</a>';
        }
        echo '</div>';
        echo '</div>';
        echo '</nav>';
    }


    private function renderSidebarRootMenu(): void
    {
        $items = $this->rootMenuTree();

        echo '<nav class="wi-member-root-menu wi-member-root-menu--sidebar" aria-label="Main site navigation">';
        echo '<span class="wi-member-root-menu__label">Main site</span>';
        echo '<div class="wi-member-root-menu__links">';

        if ($items === []) {
            echo '<a href="' . wi_e($this->rootUrl('index.php')) . '">Home</a>';
        } else {
            foreach ($items as $item) {
                $this->renderRootMenuItem($item);
            }
        }

        echo '</div>';
        echo '</nav>';
    }

    public function footer(): void
    {
        $siteName = $this->footerWebsiteName();
        $year = date('Y');

        echo '<footer class="wi-marketing-footer" role="contentinfo">';
        echo '<div class="wi-marketing-container wi-footer-grid">';

        echo '<div class="wi-footer-brand">';
        echo '<strong>' . wi_e($siteName) . '</strong>';
        echo '<p>WICMS core for websites, members, themes, plugins and admin workflows.</p>';
        echo '</div>';

        echo '<div class="wi-footer-column">';
        echo '<h4>Core</h4>';
        echo '<a href="' . wi_e($this->rootUrl('index.php')) . '">Home</a>';
        echo '<a href="' . wi_e($this->rootUrl('about_us.php')) . '">About</a>';
        echo '<a href="' . wi_e($this->rootUrl('contact_us.php')) . '">Contact</a>';
        echo '</div>';

        echo '<div class="wi-footer-column">';
        echo '<h4>Access</h4>';
        echo '<a href="' . wi_e($this->rootUrl('login.php')) . '">Member login</a>';
        echo '<a href="' . wi_e($this->rootUrl('register.php')) . '">Register</a>';
        echo '<a href="' . wi_e($this->rootUrl('alogin.php')) . '">Admin portal</a>';
        echo '</div>';

        echo '<div class="wi-footer-column">';
        echo '<h4>System</h4>';
        echo '<span>Role-based access</span>';
        echo '<span>Plugin-ready core</span>';
        echo '<span>Privacy-first controls</span>';
        echo '</div>';

        echo '</div>';
        echo '<div class="wi-marketing-container wi-footer-bottom">';
        echo '<p>Copyright &copy; ' . wi_e($year) . ' ' . wi_e($siteName) . '. All rights reserved.</p>';
        echo '<p>Powered by WICMS.</p>';
        echo '</div>';
        echo '</footer>';
    }


    public function backendJs(): void
    {
        echo '';
    }

    private function emitStylesheet(string $href, string $rel = 'stylesheet'): void
    {
        $href = trim($href);
        $rel = trim($rel) ?: 'stylesheet';

        if ($href === '') {
            return;
        }

        echo '<link href="' . wi_e($this->rootUrl($href)) . '" rel="' . wi_e($rel) . '">';
    }

    private function siteName(): string
    {
        if ($this->WIdb->tableExists('wi_site')) {
            $row = $this->WIdb->row('SELECT * FROM `wi_site` LIMIT 1');
            foreach (['site_name', 'website_name', 'name'] as $column) {
                $value = trim((string) ($row[$column] ?? ''));
                if ($value !== '') {
                    return $value;
                }
            }
        }

        return defined('WEBSITE_NAME') ? (string) WEBSITE_NAME : 'WICMS';
    }

    private function footerWebsiteName(): string
    {
        if ($this->WIdb->tableExists('wi_footer')) {
            $row = $this->WIdb->row('SELECT * FROM `wi_footer` WHERE `footer_id` = :id LIMIT 1', ['id' => 1]);
            $value = trim((string) ($row['website_name'] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return $this->siteName();
    }

    private function headerLogoPath(): string
    {
        if (!$this->WIdb->tableExists('wi_header')) {
            return '';
        }

        $row = $this->WIdb->row('SELECT * FROM `wi_header` LIMIT 1');
        $logo = trim((string) ($row['logo'] ?? ''));

        return $logo !== '' ? 'WIAdmin/WIMedia/Img/header/' . $logo : '';
    }

    /**
     * @param array<string,mixed> $item
     */
    private function renderPrimaryMenuItem(array $item): void
    {
        $label = $this->rootMenuLabel($item);
        $href = $this->rootLinkForMemberArea((string) ($item['link'] ?? '#'));
        echo '<a class="wi-nav-link" href="' . wi_e($href) . '">' . wi_e($label) . '</a>';
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function rootMenuRows(): array
    {
        if (!$this->WIdb->tableExists('wi_menu')) {
            return [];
        }

        return $this->WIdb->select(
            'SELECT * FROM `wi_menu` ORDER BY `parent` ASC, `sort` ASC, `id` ASC',
            []
        );
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    private function rootMenuTree(): array
    {
        $rows = $this->rootMenuRows();
        $children = [];
        $byId = [];

        foreach ($rows as $row) {
            $id = (int) ($row['id'] ?? 0);
            $parent = (int) ($row['parent'] ?? 0);

            if ($id <= 0) {
                continue;
            }

            $row['children'] = [];
            $byId[$id] = $row;
            $children[$parent][] = $id;
        }

        $build = function (int $parentId) use (&$build, &$children, &$byId): array {
            $branch = [];

            foreach ($children[$parentId] ?? [] as $id) {
                $item = $byId[$id];
                $item['children'] = $build($id);
                $branch[] = $item;
            }

            return $branch;
        };

        return $build(0);
    }

    /**
     * @param array<string,mixed> $item
     */
    private function renderRootMenuItem(array $item): void
    {
        $label = $this->rootMenuLabel($item);
        $href = $this->rootLinkForMemberArea((string) ($item['link'] ?? '#'));
        $children = is_array($item['children'] ?? null) ? $item['children'] : [];

        echo '<span class="wi-member-root-menu__item">';
        echo '<a href="' . wi_e($href) . '">' . wi_e($label) . '</a>';

        if ($children !== []) {
            echo '<span class="wi-member-root-menu__children">';
            foreach ($children as $child) {
                if (is_array($child)) {
                    $this->renderRootMenuItem($child);
                }
            }
            echo '</span>';
        }

        echo '</span>';
    }

    /**
     * @param array<string,mixed> $item
     */
    private function rootMenuLabel(array $item): string
    {
        $lang = trim((string) ($item['lang'] ?? ''));
        $label = trim((string) ($item['label'] ?? ''));

        if ($lang !== '' && class_exists('WILang')) {
            $translated = trim((string) WILang::get($lang));
            if ($translated !== '') {
                return $translated;
            }
        }

        return $label !== '' ? $label : 'Menu item';
    }

    private function rootLinkForMemberArea(string $link): string
    {
        $link = trim($link);

        if ($link === '') {
            return '#';
        }

        if (
            str_starts_with($link, '#') ||
            str_starts_with($link, '/') ||
            str_starts_with($link, '../') ||
            preg_match('/^[a-z][a-z0-9+.-]*:/i', $link) === 1
        ) {
            return $link;
        }

        return $this->rootUrl($link);
    }

    public function rootUrl(string $path = ''): string
    {
        $path = ltrim(trim($path), '/');
        if ($path === '') {
            return $this->isMemberAreaRequest() ? '../' : '';
        }

        // Inside WIMembers, member-owned pages must stay local. Root is not the
        // dumping ground for profile/member navigation.
        if ($this->isMemberAreaRequest() && $this->isMemberOwnedPath($path)) {
            return $path;
        }

        return $this->isMemberAreaRequest() ? '../' . $path : $path;
    }

    public function memberUrl(string $path = ''): string
    {
        $path = ltrim(trim($path), '/');
        return $this->isMemberAreaRequest() ? $path : 'WIMembers/' . $path;
    }

    private function isMemberOwnedPath(string $path): bool
    {
        $clean = preg_split('/[?#]/', $path, 2)[0] ?? $path;
        return in_array($clean, [
            'profile.php', 'workspace.php', 'forms.php', 'account.php',
            'settings.php', 'support.php', 'membership.php', 'userpayments.php',
            'payments.php', 'transactions.php', 'security.php', 'usersecurity.php',
            'delete_profile.php', 'upgrade.php', 'downgrade.php', 'logout.php',
        ], true);
    }

    private function isMemberAreaRequest(): bool
    {
        $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        return str_contains($script, '/WIMembers/');
    }
}

