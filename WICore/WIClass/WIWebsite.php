<?php
declare(strict_types=1);

/**
 * FILE:
 * WICMS-IVO/WICore/WIClass/WIWebsite.php
 *
 * Canonical website chrome/helper class for WICMS front-end.
 */
final class WIWebsite
{
    private WIdb $WIdb;
    private WISite $site;
    private ?WIMobileDetect $mobileDetect;
    private WILogin $Login;
    private ?WIUser $User;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->site = new WISite();
        $this->mobileDetect = class_exists('WIMobileDetect') ? new WIMobileDetect() : null;
        $this->Login = new WILogin();

        $userId = (int) WISession::get('user_id', 0);
        $this->User = ($userId > 0 && class_exists('WIUser')) ? new WIUser($userId) : null;
    }

    public function webSite_essentials(string $column): mixed
    {
        $row = $this->getSingleRow('wi_header');
        return $row[$column] ?? null;
    }

    public function webSite_icons(): void
    {
        $favicon = $this->showFavicon();

        if ($favicon === '') {
            return;
        }

        echo '<link rel="icon" type="image/png" href="' . $this->e($this->resolvePublicMediaAsset($favicon, 'favicon')) . '"/>';
    }

    public function Meta($page): void
    {
        $pageName = $this->sanitizeAssetPage((string) $page);
        $seen = [];

        if ($this->isMobileDevice()) {
            echo '<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">' . PHP_EOL;
            echo '<meta name="apple-mobile-web-app-capable" content="yes">' . PHP_EOL;
            echo '<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">' . PHP_EOL;
            $seen['viewport'] = true;
            $seen['apple-mobile-web-app-capable'] = true;
            $seen['apple-mobile-web-app-status-bar-style'] = true;
        }

        $rows = $this->WIdb->select(
            'SELECT *
             FROM `wi_meta`
             WHERE `page` IN (:global_page, :all_page, :star_page, :page)
             ORDER BY CASE
                WHEN `page` IN (\'global\', \'all\', \'*\') THEN 0
                ELSE 1
             END, `meta_id` ASC',
            [
                'global_page' => 'global',
                'all_page' => 'all',
                'star_page' => '*',
                'page' => $pageName,
            ]
        );

        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            $content = trim((string) ($row['content'] ?? ''));

            if ($name === '' || $content === '') {
                continue;
            }

            $key = strtolower($name);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;

            echo '<meta name="' . $this->e($name) . '" content="' . $this->e($content) . '">' . PHP_EOL;
        }
    }

    public function Theme(): string
    {
        $theme = $this->site->getActiveThemeRow();

        return trim((string) ($theme['destination'] ?? ''));
    }

    public function Styling($page): void
    {
        $pageName = $this->sanitizeAssetPage((string) $page);

        $rows = $this->WIdb->select(
            'SELECT *
             FROM `wi_css`
             WHERE `page` IN (:global_page, :all_page, :star_page, :page)
             ORDER BY CASE
                WHEN `page` IN (\'global\', \'all\', \'*\') THEN 0
                ELSE 1
             END, `id` ASC',
            [
                'global_page' => 'global',
                'all_page' => 'all',
                'star_page' => '*',
                'page' => $pageName,
            ]
        );

        $themeBase = $this->Theme();
        $seen = [];

        foreach ($rows as $row) {
            $href = trim((string) ($row['href'] ?? ''));
            if ($href === '') {
                continue;
            }

            $key = strtolower($href);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $rel = trim((string) ($row['rel'] ?? 'stylesheet'));
            echo '<link href="' . $this->e($themeBase . $href) . '" rel="' . $this->e($rel) . '">' . PHP_EOL;
        }

        $consentManager = $this->consentManager();
        if ($consentManager !== null) {
            echo $consentManager->renderPublicCssLink();
        }
    }

    public function Scripts($page): void
    {
        $consentManager = $this->consentManager();
        if ($consentManager !== null) {
            echo $consentManager->renderGoogleConsentDefaultScript();
        }

        $pageName = $this->sanitizeAssetPage((string) $page);

        $rows = $this->WIdb->select(
            'SELECT *
             FROM `wi_scripts`
             WHERE `page` IN (:global_page, :all_page, :star_page, :page)
             ORDER BY CASE
                WHEN `page` IN (\'global\', \'all\', \'*\') THEN 0
                ELSE 1
             END, `id` ASC',
            [
                'global_page' => 'global',
                'all_page' => 'all',
                'star_page' => '*',
                'page' => $pageName,
            ]
        );

        $themeBase = $this->Theme();
        $seen = [];

        foreach ($rows as $row) {
            $src = trim((string) ($row['src'] ?? ''));
            if ($src === '') {
                continue;
            }

            $key = strtolower($src);
            if (isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            echo '<script src="' . $this->e($themeBase . $src) . '" type="text/javascript"></script>' . PHP_EOL;
        }
    }

    public function StartUp(): void
    {
        $siteName = $this->site->siteName('WICMS');

        echo '<!DOCTYPE html>' . PHP_EOL;
        echo '<html class="no-js" lang="' . $this->e(WILang::getLanguage()) . '">' . PHP_EOL;
        echo '<head>' . PHP_EOL;
        echo '<meta charset="utf-8">' . PHP_EOL;
        echo '<meta http-equiv="X-UA-Compatible" content="IE=edge">' . PHP_EOL;
        echo '<title>' . $this->e($siteName) . '</title>' . PHP_EOL;
    }

    public function Social(): void
    {
        $rows = $this->WIdb->select('SELECT * FROM `wi_social` ORDER BY `id` ASC', []);

        echo '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">';
        echo '<ul class="social_media">';

        foreach ($rows as $row) {
            $href = trim((string) ($row['href'] ?? '#'));
            $name = trim((string) ($row['name'] ?? ''));

            if ($name === '') {
                continue;
            }

            echo '<li>';
            echo '<a href="' . $this->e($href) . '" target="_blank" rel="noopener noreferrer" class="fa fa-' . $this->e($name) . ' fa-5x" title="' . $this->e($name) . '">';
            echo $this->e($name);
            echo '</a>';
            echo '</li>';
        }

        echo '</ul></div>';
    }

    public function contact(): void
    {
        $contactNo = $this->site->contactNumber();
        $contactEmail = $this->site->contactEmail();

        echo '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">';
        echo '<div class="phone">';
        echo '<ul class="phone__no">';

        if ($contactNo !== '') {
            echo '<li class="align">';
            echo '<i class="fa fa-phone" aria-hidden="true"></i> ';
            echo '<a href="tel:' . $this->e($contactNo) . '" class="white">' . $this->e($contactNo) . '</a>';
            echo '</li>';
        }

        if ($contactEmail !== '') {
            echo '<li class="align">';
            echo '<i class="fa fa-envelope-o" aria-hidden="true"></i> ';
            echo '<a href="mailto:' . $this->e($contactEmail) . '" class="white">' . $this->e($contactEmail) . '</a>';
            echo '</li>';
        }

        echo '</ul></div></div>';
    }

    public function MainHeader(): void
    {
        $row = $this->getSingleRow('wi_header');
        $siteName = $this->site->siteName('WICMS');
        $logo = trim((string) ($row['logo'] ?? ''));
        $logoPath = $logo !== '' ? $this->resolvePublicMediaAsset($logo, 'header') : '';

        echo '<header class="wi-marketing-topbar" role="banner">';
        echo '<div class="wi-marketing-container wi-topbar-inner">';
        echo '<a class="wi-brand" href="index.php" aria-label="' . $this->e($siteName) . ' home">';

        if ($logoPath !== '') {
            echo '<span class="wi-brand-logo"><img src="' . $this->e($logoPath) . '" alt="' . $this->e($siteName) . '"></span>';
        } else {
            echo '<span class="wi-brand-mark">WI</span>';
        }

        echo '<span class="wi-brand-copy">';
        echo '<strong>' . $this->e($siteName) . '</strong>';
        echo '<small>Powered by WICMS</small>';
        echo '</span>';
        echo '</a>';

        echo '<div class="wi-topbar-copy">';
        echo '<span>Flexible WICMS platform for websites, members, plugins and themes</span>';
        echo '</div>';

        echo '<div class="wi-topbar-actions">';
        echo '<a class="wi-topbar-link" href="login.php">Member login</a>';
        echo '<a class="wi-topbar-button" href="alogin.php">Admin portal</a>';
        echo '</div>';

        echo '</div>';
        echo '</header>';
    }

    public function MainMenu(): void
    {
        $rows = $this->WIdb->select(
            'SELECT * FROM `wi_menu` ORDER BY `sort` ASC, `id` ASC',
            []
        );

        echo '<nav class="wi-marketing-nav" role="navigation" aria-label="Primary navigation">';
        echo '<div class="wi-marketing-container wi-nav-inner">';
        echo '<button class="wi-nav-toggle" type="button" aria-label="Open navigation" onclick="document.body.classList.toggle(&quot;wi-nav-open&quot;)">';
        echo '<span></span><span></span><span></span>';
        echo '</button>';

        echo '<div class="wi-nav-links">';

        foreach ($this->buildMenuTree($rows) as $item) {
            $this->renderMenuItem($item);
        }

        echo '</div>';
        echo '<div class="wi-nav-actions">';

        if ($this->Login->isLoggedIn()) {
            echo '<a class="wi-nav-link" href="WIMembers/profile.php">' . $this->e(class_exists('WILang') ? WILang::get('profile') : 'Profile') . '</a>';
            echo '<a class="wi-nav-link" href="logout.php">' . $this->e(class_exists('WILang') ? WILang::get('logout') : 'Logout') . '</a>';
        } else {
            echo '<a class="wi-nav-link" href="register.php">' . $this->e(class_exists('WILang') ? WILang::get('register') : 'Register') . '</a>';
            echo '<a class="wi-nav-link" href="login.php">' . $this->e(class_exists('WILang') ? WILang::get('login') : 'Login') . '</a>';
            echo '<a class="wi-nav-cta" href="alogin.php">Admin portal</a>';
        }

        echo '</div>';
        echo '</div>';
        echo '</nav>';
    }


    public function footer(): void
    {
        $row = $this->getSingleRow('wi_footer', 'footer_id = :id', ['id' => 1]);
        $year = date('Y');
        $websiteName = trim((string) ($row['website_name'] ?? ''));

        if ($websiteName === '') {
            $websiteName = $this->site->siteName('WICMS');
        }

        echo '<footer class="wi-marketing-footer" role="contentinfo">';
        echo '<div class="wi-marketing-container wi-footer-grid">';

        echo '<div class="wi-footer-brand">';
        echo '<strong>' . $this->e($websiteName) . '</strong>';
        echo '<p>WICMS core for websites, members, themes, plugins and admin workflows.</p>';
        echo '</div>';

        echo '<div class="wi-footer-column">';
        echo '<h4>Core</h4>';
        echo '<a href="index.php">Home</a>';
        echo '<a href="about_us.php">About</a>';
        echo '<a href="contact_us.php">Contact</a>';
        echo '</div>';

        echo '<div class="wi-footer-column">';
        echo '<h4>Access</h4>';
        echo '<a href="login.php">Member login</a>';
        echo '<a href="register.php">Register</a>';
        echo '<a href="alogin.php">Admin portal</a>';
        echo '</div>';

        echo '<div class="wi-footer-column">';
        echo '<h4>System</h4>';
        echo '<span>Role-based access</span>';
        echo '<span>Plugin-ready core</span>';
        echo '<span>Privacy-first controls</span>';

        $consentManager = $this->consentManager();
        if ($consentManager !== null) {
            echo $consentManager->renderFooterCookieLink();
        }

        echo '</div>';

        echo '</div>';
        echo '<div class="wi-marketing-container wi-footer-bottom">';
        echo '<p>Copyright &copy; ' . $this->e($year) . ' ' . $this->e($websiteName) . '. All rights reserved.</p>';
        echo '<p>Powered by WICMS.</p>';
        echo '</div>';
        echo '</footer>';
    }


    public static function langClassSelector($lang): string
    {
        return WILang::getLanguage() === (string) $lang ? WILang::getLanguage() : 'fade';
    }

    public function viewLang(): void
    {
        $rows = $this->WIdb->select('SELECT * FROM `wi_lang` ORDER BY `id` ASC', []);

        echo '<div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">';
        echo '<div class="flags-wrapper">';

        foreach ($rows as $lang) {
            $href = trim((string) ($lang['href'] ?? '#'));
            $flag = trim((string) ($lang['lang_flag'] ?? ''));
            $name = trim((string) ($lang['name'] ?? ''));
            $code = trim((string) ($lang['lang'] ?? ''));

            echo '<a href="' . $this->e($href) . '">';
            echo '<img src="' . $this->e('WIAdmin/WIMedia/Img/lang/' . $flag) . '" alt="' . $this->e($name) . '" title="' . $this->e($name) . '" class="' . $this->e(self::langClassSelector($code)) . '" />';
            echo '</a>';
        }

        echo '</div>';
        echo '</div>';
    }

    public function PageMod($page, $column): mixed
    {
        $result = $this->WIdb->select(
            'SELECT * FROM `wi_page` WHERE `name` = :page LIMIT 1',
            ['page' => (string) $page]
        );

        return $result[0][$column] ?? null;
    }

    public function pageModPower($page, $column): int
    {
        $result = $this->PageMod((string) $page, (string) $column);

        if ($result === null || $result === '') {
            return 0;
        }

        if (is_numeric($result)) {
            return (int) $result;
        }

        return strlen((string) $result) > 0 ? 1 : 0;
    }

    public function showFavicon(): string
    {
        return trim((string) $this->site->favicon(''));
    }

    public function google_lang(): void
    {
        echo '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">';
        echo '<div class="flags-wrapper">';
        echo '<div id="google_translate_element"></div>';
        echo '<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: "en", layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, "google_translate_element");
}
</script>';
        echo '<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>';
        echo '</div>';
        echo '</div>';
    }

    public function backendJs(): void
    {
        $theme = $this->Theme();

        echo '<script type="text/javascript" src="' . $this->e($theme . 'site/js/vendor/jquery.easing.1.3.js') . '"></script>' . PHP_EOL;
        echo '<script type="text/javascript" src="' . $this->e($theme . 'site/js/jquery.cookie.js') . '"></script>' . PHP_EOL;
        echo '<script type="text/javascript" src="' . $this->e($theme . 'site/js/styleswitch.js') . '"></script>' . PHP_EOL;
        echo '<script type="text/javascript" src="' . $this->e($theme . 'site/js/plugin/jquery.themepunch.revolution.min.js') . '"></script>' . PHP_EOL;
        echo '<script type="text/javascript" src="' . $this->e($theme . 'site/js/plugin/jquery.plugin.js') . '"></script>' . PHP_EOL;

        $consentManager = $this->consentManager();
        if ($consentManager !== null) {
            echo $consentManager->renderPublicJsLink();
        }
    }

    private function sanitizeAssetPage(string $page): string
    {
        $value = preg_replace('/[^A-Za-z0-9_-]/', '', trim($page)) ?? '';
        return $value !== '' ? $value : 'index';
    }

    public function getPage(): string
    {
        if (!empty($_GET['page'])) {
            return preg_replace('/[^a-zA-Z0-9_-]/', '', (string) $_GET['page']);
        }

        return 'home';
    }

    public function getPageModule(string $page): ?string
    {
        $result = $this->WIdb->select(
            'SELECT `module_name` FROM `wi_page` WHERE `name` = :page LIMIT 1',
            ['page' => $page]
        );

        if (!isset($result[0]['module_name'])) {
            return null;
        }

        $value = trim((string) $result[0]['module_name']);

        return $value !== '' ? $value : null;
    }

    public function modulePowered(string $moduleName): bool
    {
        $result = $this->WIdb->select(
            'SELECT `mod_powered` FROM `wi_mod` WHERE `module_name` = :name LIMIT 1',
            ['name' => $moduleName]
        );

        $power = (string) ($result[0]['mod_powered'] ?? '');

        return $power === 'power_on' || $power === '1' || strtolower($power) === 'on';
    }

    public function loadModule(string $moduleName): void
    {
        $moduleName = preg_replace('/[^A-Za-z0-9_-]/', '', $moduleName);

        if ($moduleName === '') {
            echo 'Module name missing.';
            return;
        }

        $file = dirname(dirname(dirname(__DIR__))) . '/WIAdmin/WIModule/modules/' . $moduleName . '/' . $moduleName . '.php';

        if (!is_file($file)) {
            echo 'Module file missing: ' . $this->e($moduleName);
            return;
        }

        require_once $file;

        if (!class_exists($moduleName)) {
            echo 'Module class missing: ' . $this->e($moduleName);
            return;
        }

        $module = new $moduleName();

        if (method_exists($module, 'mod_name')) {
            $module->mod_name($this->getPage());
        }
    }

    private function resolvePublicMediaAsset(string $asset, string $area = 'header'): string
    {
        $asset = trim(str_replace('\\', '/', $asset));

        if ($asset === '') {
            return '';
        }

        if (preg_match('/^(https?:)?\/\//i', $asset) === 1) {
            return $asset;
        }

        $asset = ltrim($asset, '/');

        if (str_contains($asset, '../') || str_contains($asset, '..\\')) {
            return '';
        }

        $root = dirname(dirname(__DIR__));
        $allowedPrefixes = [
            'WIAdmin/WIMedia/Images/',
            'WIAdmin/WIMedia/Img/',
            'WIMedia/Images/',
            'WIMedia/Img/',
        ];

        foreach ($allowedPrefixes as $prefix) {
            if (str_starts_with($asset, $prefix)) {
                return $asset;
            }
        }

        if (str_contains($asset, '/')) {
            $candidate = preg_replace('#/+#', '/', $asset) ?? '';
            if ($candidate !== '' && is_file($root . '/' . $candidate)) {
                return $candidate;
            }
        }

        $filename = basename($asset);
        $area = strtolower(preg_replace('/[^a-z0-9_-]/i', '', $area) ?? 'header');

        if ($filename === '' || $filename === '.' || $filename === '..') {
            return '';
        }

        $candidates = [];

        if ($area === 'favicon') {
            $candidates[] = 'WIAdmin/WIMedia/Images/wicms/favicon/' . $filename;
            $candidates[] = 'WIAdmin/WIMedia/Img/favicon/' . $filename;
            $candidates[] = 'WIAdmin/WIMedia/Img/icons/' . $filename;
        } else {
            $candidates[] = 'WIAdmin/WIMedia/Images/wicms/header/' . $filename;
            $candidates[] = 'WIAdmin/WIMedia/Img/header/' . $filename;
        }

        $candidates[] = 'WIAdmin/WIMedia/Images/wicms/' . $filename;
        $candidates[] = 'WIAdmin/WIMedia/Images/' . $filename;
        $candidates[] = 'WIAdmin/WIMedia/Img/' . $filename;

        foreach ($candidates as $candidate) {
            if (is_file($root . '/' . $candidate)) {
                return $candidate;
            }
        }

        return $candidates[0] ?? '';
    }

    private function consentManager(): ?WIConsentManager
    {
        if (!class_exists('WIConsentManager')) {
            $consentClass = __DIR__ . '/WIConsentManager.php';
            if (is_file($consentClass)) {
                require_once $consentClass;
            }
        }

        if (!class_exists('WIConsentManager')) {
            return null;
        }

        try {
            return new WIConsentManager();
        } catch (Throwable $e) {
            return null;
        }
    }

    private function e($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    private function getSingleRow(string $table, string $where = '', array $params = []): array
    {
        if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
            return [];
        }

        $sql = "SELECT * FROM `{$table}`";

        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }

        $sql .= ' LIMIT 1';

        $result = $this->WIdb->select($sql, $params);

        return $result[0] ?? [];
    }

    private function isMobileDevice(): bool
    {
        if ($this->mobileDetect === null) {
            return false;
        }

        try {
            return (bool) $this->mobileDetect->isMobile();
        } catch (Throwable $e) {
            return false;
        }
    }

    private function buildMenuTree(array $rows): array
    {
        $items = [];
        $children = [];

        foreach ($rows as $row) {
            $id = (int) ($row['id'] ?? 0);
            $parent = (int) ($row['parent'] ?? 0);

            $item = [
                'id' => $id,
                'parent' => $parent,
                'link' => (string) ($row['link'] ?? '#'),
                'lang' => (string) ($row['lang'] ?? ''),
                'children' => [],
            ];

            if ($parent > 0) {
                $children[$parent][] = $item;
            } else {
                $items[$id] = $item;
            }
        }

        foreach ($children as $parentId => $childItems) {
            if (isset($items[$parentId])) {
                $items[$parentId]['children'] = $childItems;
            } else {
                foreach ($childItems as $child) {
                    $items[$child['id']] = $child;
                }
            }
        }

        return array_values($items);
    }

    private function renderMenuItem(array $item): void
    {
        $link = $this->e($item['link'] ?? '#');
        $langKey = (string) ($item['lang'] ?? '');
        $label = $langKey !== '' ? $this->e(WILang::get($langKey)) : $link;
        $children = $item['children'] ?? [];

        if (!is_array($children) || $children === []) {
            echo '<a class="wi-nav-link" href="' . $link . '">' . $label . '</a>';
            return;
        }

        echo '<span class="wi-nav-dropdown">';
        echo '<a class="wi-nav-link" href="' . $link . '">' . $label . '</a>';
        echo '<span class="wi-nav-dropdown-menu">';

        foreach ($children as $child) {
            $childLink = $this->e($child['link'] ?? '#');
            $childLangKey = (string) ($child['lang'] ?? '');
            $childLabel = $childLangKey !== '' ? $this->e(WILang::get($childLangKey)) : $childLink;

            echo '<a href="' . $childLink . '">' . $childLabel . '</a>';
        }

        echo '</span>';
        echo '</span>';
    }
}