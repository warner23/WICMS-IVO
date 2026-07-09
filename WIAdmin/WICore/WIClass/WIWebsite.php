<?php
declare(strict_types=1);

class WIWebsite
{
    private WIdb $WIdb;
    private WIPagination $Page;
    private WISystem $System;
    private WIMaintenace $maint;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Page = new WIPagination();
        $this->System = new WISystem();
        $this->maint = new WIMaintenace();
    }

    /*
    |--------------------------------------------------------------------------
    | Shared helpers
    |--------------------------------------------------------------------------
    */


    private function resolveAdminMediaAsset(string $value, string $legacyType = 'header'): string
    {
        $value = trim($value);

        if ($value === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $value) === 1 || str_starts_with($value, '/')) {
            return $value;
        }

        if (str_starts_with($value, 'WIAdmin/')) {
            return '../' . $value;
        }

        if (str_starts_with($value, 'WIMedia/')) {
            return $value;
        }

        $legacyFolder = $legacyType === 'favicon' ? 'favicon' : 'header';
        return 'WIMedia/Img/' . $legacyFolder . '/' . rawurlencode($value);
    }

    private function notify(string $message): void
    {
        $this->maint->Notifications((string) WISession::get('user_id', '0'), $message);
    }

    private function success(string $message, array $data = []): array
    {
        return [
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ];
    }

    private function error(string $message): array
    {
        return [
            'status' => 'error',
            'message' => $message,
        ];
    }

    private function rootPath(): string
    {
        return dirname(dirname(dirname(dirname(__FILE__))));
    }

    private function activeThemePath(): string
    {
        return $this->rootPath() . '/' . $this->gettheme();
    }

    /*
    |--------------------------------------------------------------------------
    | Essentials / legacy helpers
    |--------------------------------------------------------------------------
    */

    public function webSite_essentials(string|int $column): mixed
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_header`");
        return $result[$column] ?? null;
    }

    public function webSite_icons(): void
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_site`");

        foreach ($result as $res) {
            echo '<link rel="icon" type="image/png" href="WIAdmin/WIMedia/Img/favicon/' .
                htmlspecialchars((string) $res['favicon'], ENT_QUOTES, 'UTF-8') .
                '"/>';
        }
    }

    public function StartUp(): void
    {
        echo '<!DOCTYPE html>
                <html class="no-js" lang="en">
                <head>
                  <title>' . htmlspecialchars((string) (defined('WEBSITE_NAME') ? WEBSITE_NAME : 'WICMS'), ENT_QUOTES, 'UTF-8') . '</title>
                  <meta charset="utf-8">';
    }

    public function google_lang(): void
    {
        echo '<div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                <div class="flags-wrapper">
                    <div id="google_translate_element"></div>
                    <script type="text/javascript">
                        function googleTranslateElementInit() {
                            new google.translate.TranslateElement(
                                {pageLanguage: "en", layout: google.translate.TranslateElement.InlineLayout.SIMPLE},
                                "google_translate_element"
                            );
                        }
                    </script>
                    <script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
                </div>
              </div>';
    }

    public function href(string $href): void
    {
        include $this->rootPath() . '/' . $this->gettheme() . $href;
    }

    /*public function Href(string $href): void
    {
        $this->href($href);
    }*/

    /*
    |--------------------------------------------------------------------------
    | Theme
    |--------------------------------------------------------------------------
    */

    public function Theme(): string
    {
        $result = $this->WIdb->select(
            'SELECT * FROM `wi_theme` WHERE `in_use` = :in_use LIMIT 1',
            ['in_use' => 1]
        );

        return $result[0]['destination'] ?? 'WITheme/WICMS/';
    }

    public function gettheme(): string
    {
        return $this->Theme();
    }

    public function ViewThemes(): void
    {
        echo '<ul class="theme">';

        $result = $this->WIdb->bindfree("SELECT * FROM `wi_theme`");

        foreach ($result as $theme) {
            $id = (int) $theme['id'];
            $themeName = htmlspecialchars((string) $theme['theme'], ENT_QUOTES, 'UTF-8');
            $inUse = (string) $theme['in_use'];

            echo '<li class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                    <div class="col-sm-3 col-md-3 col-lg-3 col-xs-3">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">' . $themeName . '</div>
                    </div>

                    <div class="btn-group" id="WITheme_settings-' . $id . '" data-toggle="buttons-radio">
                        <input type="hidden" name="theme" id="WITheme-' . $id . '" class="btn-group-value" value="' . htmlspecialchars($inUse, ENT_QUOTES, 'UTF-8') . '"/>
                        <button type="button" id="theme_true-' . $id . '" name="theme" value="true" onclick="WITheme.activate(' . $id . ')" class="btn">In Use</button>
                        <button type="button" id="theme_false-' . $id . '" name="theme" value="false" class="btn btn-danger activewhens">Not In Use</button>
                    </div>

                    <div class="col-sm-1 col-md-1 col-lg-1 col-xs-1">
                        <a href="javascript:void(0);" class="fa fa-trash" onclick="WITheme.DeleteThemeModal(' . $id . ')"></a>
                    </div>
                  </li>';
        }

        echo '</ul>';
    }

    public function viewCmsThemes(): void
    {
        $this->ViewThemes();
    }

    public function setTheme(int $id, mixed $val): array
    {
        $ok = $this->WIdb->update(
            'wi_theme',
            ['val' => $val],
            '`id` = :current_id',
            ['current_id' => $id]
        );

        if (!$ok) {
            return $this->error('Unable to update theme state.');
        }

        $this->notify('Updated theme state');

        return $this->success('Theme state updated.');
    }

    public function activateThemes(int $id): array
    {
        $result = $this->WIdb->select(
            'SELECT * FROM `wi_theme` WHERE `in_use` = :in_use LIMIT 1',
            ['in_use' => 1]
        );

        $currentActiveThemeId = (int) ($result[0]['id'] ?? 0);

        $this->WIdb->update(
            'wi_theme',
            ['in_use' => '1'],
            '`id` = :id',
            ['id' => $id]
        );

        if ($currentActiveThemeId > 0 && $currentActiveThemeId !== $id) {
            $this->WIdb->update(
                'wi_theme',
                ['in_use' => '0'],
                '`id` = :current_id',
                ['current_id' => $currentActiveThemeId]
            );
        }

        $this->notify('Activated admin theme');

        return $this->success('Theme activated.');
    }

    public function deactivateThemes(int $id): array
    {
        $ok = $this->WIdb->update(
            'wi_theme',
            ['in_use' => '0'],
            '`id` = :id',
            ['id' => $id]
        );

        if (!$ok) {
            return $this->error('Unable to deactivate theme.');
        }

        $this->notify('Deactivated theme');

        return $this->success('Theme deactivated.');
    }

    public function NewTheme(string $name): array
    {
        $name = trim(strip_tags($name));

        if ($name === '') {
            return $this->error('Theme name is required.');
        }

        $source = $this->rootPath() . '/WITheme/WICMS/';
        $dest = $this->rootPath() . '/WITheme/' . $name;
        $dbDestination = 'WITheme/' . $name . '/';

        if (!file_exists($dest)) {
            $this->System->full_copy($source, $dest);
        }

        $existing = $this->WIdb->select(
            'SELECT * FROM `wi_theme` WHERE `theme` = :name LIMIT 1',
            ['name' => $name]
        );

        if (count($existing) === 0) {
            $this->WIdb->insert('wi_theme', [
                'theme' => $name,
                'destination' => $dbDestination,
                'in_use' => 0,
            ]);
        }

        $this->notify('Created new theme');

        return $this->success('Theme created.');
    }

    public function deletetheme(int $id): array
    {
        $result = $this->WIdb->select(
            'SELECT * FROM `wi_theme` WHERE `id` = :id LIMIT 1',
            ['id' => $id]
        );

        if (count($result) === 0) {
            return $this->error('Theme not found.');
        }

        $theme = (string) $result[0]['theme'];
        $folder = $this->rootPath() . '/WITheme/' . $theme;

        $this->WIdb->delete('wi_theme', 'id = :id', ['id' => $id]);

        if (file_exists($folder)) {
            $this->System->rrmdir($folder);
        }

        $this->notify('Deleted theme');

        return $this->success('Theme deleted.');
    }

    /*
    |--------------------------------------------------------------------------
    | Meta
    |--------------------------------------------------------------------------
    */

    public function Meta(string $page): void
    {
        $result = $this->WIdb->select(
            "SELECT * FROM `wi_meta` WHERE `page` = :page",
            ['page' => $page]
        );

        foreach ($result as $res) {
            echo '<meta name="' . htmlspecialchars((string) $res['name'], ENT_QUOTES, 'UTF-8') .
                '" content="' . htmlspecialchars((string) $res['content'], ENT_QUOTES, 'UTF-8') .
                '" author="' . htmlspecialchars((string) ($res['author'] ?? ''), ENT_QUOTES, 'UTF-8') . '">';
        }
    }

    public function ViewMeta(string $page): void
    {
        $result = $this->WIdb->select(
            "SELECT * FROM `wi_meta` WHERE `page` = :page",
            ['page' => $page]
        );

        echo '<ul class="meta">';

        foreach ($result as $meta) {
            echo '<li class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                    <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">' . htmlspecialchars((string) $meta['name'], ENT_QUOTES, 'UTF-8') . '</div>
                    </div>

                    <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">' . htmlspecialchars((string) $meta['content'], ENT_QUOTES, 'UTF-8') . '</div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3 col-xs-3">
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">' . htmlspecialchars((string) ($meta['author'] ?? ''), ENT_QUOTES, 'UTF-8') . '</div>
                    </div>

                    <div class="col-sm-1 col-md-1 col-lg-1 col-xs-1">
                        <a href="javascript:void(0);" onclick="WIMeta.showMetaModal(`' . (int) $meta['meta_id'] . '`)" class="metalink">
                            <i class="fa fa-edit"></i>
                        </a>
                    </div>
                  </li>';
        }

        echo '</ul>';
    }

    public function ViewEditMeta(int $id): array
    {
        $res = $this->WIdb->select(
            'SELECT * FROM `wi_meta` WHERE `meta_id` = :id LIMIT 1',
            ['id' => $id]
        );

        if (count($res) === 0) {
            return $this->error('Meta record not found.');
        }

        return [
            'status' => 'completed',
            'name' => $res[0]['name'],
            'content' => $res[0]['content'],
            'page' => $res[0]['page'],
            'id' => $res[0]['meta_id'],
        ];
    }

    public function EditMeta(array $meta): array
    {
        $data = $meta['MetaData'] ?? [];

        $name = trim((string) ($data['name'] ?? ''));
        $content = trim((string) ($data['content'] ?? ''));
        $page = trim((string) ($data['page'] ?? ''));
        $id = (int) ($data['meta_id'] ?? 0);

        if ($name === '' || $page === '' || $id <= 0) {
            return $this->error('Invalid meta data.');
        }

        $this->WIdb->update(
            'wi_meta',
            [
                'name' => $name,
                'content' => $content,
                'page' => $page,
            ],
            '`meta_id` = :id',
            ['id' => $id]
        );

        $this->notify('Updated meta settings');

        return $this->success('Meta updated successfully.');
    }

    public function DeleteMeta(int $id): array
    {
        $this->WIdb->delete('wi_meta', 'meta_id = :id', ['id' => $id]);

        $this->notify('Deleted meta entry');

        return $this->success('Meta deleted successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | CSS
    |--------------------------------------------------------------------------
    */

    public function Styling(string $page): void
    {
        $result = $this->WIdb->select(
            "SELECT * FROM `wi_css` WHERE `page` = :page",
            ['page' => $page]
        );

        foreach ($result as $res) {
            echo '<link href="' . htmlspecialchars($this->Theme() . (string) $res['href'], ENT_QUOTES, 'UTF-8') .
                '" rel="' . htmlspecialchars((string) ($res['rel'] ?? 'stylesheet'), ENT_QUOTES, 'UTF-8') . '">';
        }
    }

    public function ViewCSS(string $page): void
    {
        $result = $this->WIdb->select(
            "SELECT * FROM `wi_css` WHERE `page` = :page",
            ['page' => $page]
        );

        echo '<ul class="css">';

        foreach ($result as $css) {
            echo '<li class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                            <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                                <a href="javascript:void(0);" onclick="WICSS.editCode(`' . htmlspecialchars((string) $css['href'], ENT_QUOTES, 'UTF-8') . '`)">' . htmlspecialchars((string) $css['href'], ENT_QUOTES, 'UTF-8') . '</a>
                            </div>
                        </div>

                        <div class="col-sm-2 col-md-2 col-lg-2">
                            <div class="col-sm-2 col-md-2 col-lg-2">' . htmlspecialchars((string) ($css['rel'] ?? ''), ENT_QUOTES, 'UTF-8') . '</div>
                        </div>

                        <div class="col-sm-1 col-md-1 col-lg-1">
                            <a href="javascript:void(0);" onclick="WICSS.editcssCode(`' . (int) $css['id'] . '`)">
                                <i class="fa fa-edit"></i>
                            </a>
                        </div>

                        <div class="col-sm-1 col-md-1 col-lg-1">
                            <a href="javascript:void(0);" onclick="WICSS.CssDelete(' . (int) $css['id'] . ')">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </div>
                  </li>';
        }

        echo '</ul>';
    }

    public function ViewEditCSS(int $id): array
    {
        $res = $this->WIdb->select(
            'SELECT * FROM `wi_css` WHERE `id` = :id LIMIT 1',
            ['id' => $id]
        );

        if (count($res) === 0) {
            return $this->error('CSS entry not found.');
        }

        return [
            'status' => 'completed',
            'href' => $res[0]['href'],
            'page' => $res[0]['page'],
            'id' => $res[0]['id'],
        ];
    }

    public function ViewCmsEditCss(int $id): array
    {
        return $this->ViewEditCSS($id);
    }

    public function EditCss(array $css): array
    {
        $style = $css['CssData'] ?? [];

        $href = trim((string) ($style['CSS'] ?? ''));
        $page = trim((string) ($style['page'] ?? ''));
        $id = (int) ($style['id'] ?? 0);

        if ($href === '' || $page === '' || $id <= 0) {
            return $this->error('Invalid CSS data.');
        }

        $this->WIdb->update(
            'wi_css',
            [
                'href' => $href,
                'page' => $page,
            ],
            '`id` = :id',
            ['id' => $id]
        );

        $this->notify('Updated CSS entry');

        return $this->success('CSS updated successfully.');
    }

    public function DeleteCss(int|string $idOrPage): array
    {
        if (is_numeric($idOrPage)) {
            $this->WIdb->delete('wi_css', 'id = :id', ['id' => (int) $idOrPage]);
        } else {
            $this->WIdb->delete('wi_css', 'page = :name', ['name' => (string) $idOrPage]);
        }

        $this->notify('Deleted CSS entry');

        return $this->success('CSS deleted successfully.');
    }

    public function newCss(array $styling, string $href): array
    {
        $cssCode = (string) ($styling['UserData']['css'] ?? '');
        $href = trim($href);

        if ($href === '') {
            return $this->error('CSS file path is required.');
        }

        $filePath = $this->activeThemePath() . $href;
        $folder = dirname($filePath);

        if (!is_dir($folder)) {
            mkdir($folder, 0775, true);
        }

        file_put_contents($filePath, $cssCode);

        $this->notify('Created CSS file');

        return $this->success('CSS file created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | JS
    |--------------------------------------------------------------------------
    */

    public function Scripts(string $page): void
    {
        $result = $this->WIdb->select(
            "SELECT * FROM `wi_scripts` WHERE `page` = :page",
            ['page' => $page]
        );

        foreach ($result as $res) {
            echo '<script src="' . htmlspecialchars($this->Theme() . (string) $res['src'], ENT_QUOTES, 'UTF-8') . '" type="text/javascript"></script>';
        }
    }

    public function ViewJS(string $page): void
    {
        $result = $this->WIdb->select(
            "SELECT * FROM `wi_scripts` WHERE `page` = :page",
            ['page' => $page]
        );

        echo '<ul class="js">';

        foreach ($result as $script) {
            echo '<li class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                    <div class="col-sm-12 col-md-12 col-lg-12">
                        <label class="col-sm-1 col-md-1 col-lg-1" for="Src">Src:<span class="required">*</span></label>
                        <div class="col-xs-8 col-sm-8 col-md-8 col-lg-8">
                            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                                <a href="javascript:void(0);" onclick="WIJS.editCode(`' . htmlspecialchars((string) $script['src'], ENT_QUOTES, 'UTF-8') . '`)">' . htmlspecialchars((string) $script['src'], ENT_QUOTES, 'UTF-8') . '</a>
                            </div>
                        </div>

                        <div class="col-sm-1 col-md-1 col-lg-1">
                            <a href="javascript:void(0);" onclick="WIJS.showEditCodeModal(`' . (int) $script['id'] . '`)">
                                <i class="fa fa-edit"></i>
                            </a>
                        </div>

                        <div class="col-sm-1 col-md-1 col-lg-1">
                            <a href="#" onclick="WIJS.showJsDelete(' . (int) $script['id'] . ')">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>
                    </div>
                  </li>';
        }

        echo '</ul>';
    }

    public function ViewEditJs(int $id): array
    {
        $res = $this->WIdb->select(
            'SELECT * FROM `wi_scripts` WHERE `id` = :id LIMIT 1',
            ['id' => $id]
        );

        if (count($res) === 0) {
            return $this->error('Script entry not found.');
        }

        return [
            'status' => 'completed',
            'src' => $res[0]['src'],
            'page' => $res[0]['page'],
            'id' => $res[0]['id'],
        ];
    }

    public function EditJs(array $script): array
    {
        $style = $script['JsData'] ?? [];

        $js = trim((string) ($style['js'] ?? ''));
        $page = trim((string) ($style['page'] ?? ''));
        $id = (int) ($style['id'] ?? 0);

        if ($js === '' || $page === '' || $id <= 0) {
            return $this->error('Invalid JS data.');
        }

        $this->WIdb->update(
            'wi_scripts',
            [
                'src' => $js,
                'page' => $page,
            ],
            '`id` = :id',
            ['id' => $id]
        );

        $this->notify('Updated JS entry');

        return $this->success('JS updated successfully.');
    }

    public function EditaddJs(int $id, mixed $js): array
    {
        $filePath = trim((string) $js);

        if ($id <= 0 || $filePath === '') {
            return $this->error('Invalid JS entry.');
        }

        $this->WIdb->update(
            'wi_scripts',
            ['src' => $filePath],
            '`id` = :id',
            ['id' => $id]
        );

        $this->notify('Updated JS entry');

        return $this->success('JS entry updated successfully.');
    }

    public function deletejs(int|string $idOrPage): array
    {
        if (is_numeric($idOrPage)) {
            $this->WIdb->delete('wi_scripts', 'id = :id', ['id' => (int) $idOrPage]);
        } else {
            $this->WIdb->delete('wi_scripts', 'page = :name', ['name' => (string) $idOrPage]);
        }

        $this->notify('Deleted JS entry');

        return $this->success('JS deleted successfully.');
    }

    public function editScript(array $script): array
    {
        $newScript = $script['ScriptData'] ?? [];

        $js = (string) ($newScript['js'] ?? '');
        $href = trim((string) ($newScript['href'] ?? ''));

        if ($href === '') {
            return $this->error('Script file path is required.');
        }

        $filePath = $this->activeThemePath() . $href;
        $folder = dirname($filePath);

        if (!is_dir($folder)) {
            mkdir($folder, 0775, true);
        }

        file_put_contents($filePath, $js);

        $this->notify('Edited JS file');

        return $this->success('JS file updated successfully.');
    }

    public function newJs(array $script): array
    {
        $js = $script['JsData'] ?? [];

        $src = trim((string) ($js['js'] ?? ''));
        $page = trim((string) ($js['page'] ?? ''));

        if ($src === '' || $page === '') {
            return $this->error('Invalid JS entry.');
        }

        $this->WIdb->insert('wi_scripts', [
            'src' => $src,
            'page' => $page,
        ]);

        $this->notify('Added JS entry');

        return $this->success('JS entry created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Header / Footer / Favicon
    |--------------------------------------------------------------------------
    */

    public function MainHeader(string $context = ''): void
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_header`");

        foreach ($result as $res) {
            echo '<header class="header">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-2">
                                <div class="navbar_brand" id="HeaderImg">
                                    <img class="img-responsive cp" id="headerPic" src="' . htmlspecialchars($this->resolveAdminMediaAsset((string) $res['logo'], 'header'), ENT_QUOTES, 'UTF-8') . '" style="width:120px; height:120px;">
                                </div>
                            </div>
                        </div>
                    </div>
                  </header>';
        }
    }

    public function headerSettings(array $settings): array
    {
        $data = $settings['UserData'] ?? $settings;

        if (!is_array($data) || $data === []) {
            return $this->error('Invalid header settings.');
        }

        $this->WIdb->update(
            'wi_header',
            $data,
            '`header_id` = :id',
            ['id' => 1]
        );

        $this->notify('Updated header settings');

        return $this->success('Header settings updated.');
    }

    public function footer(): void
    {
        $id = 1;
        $date = date("Y");

        $result = $this->WIdb->select(
            "SELECT * FROM `wi_footer` WHERE footer_id = :id",
            ['id' => $id]
        );

        foreach ($result as $res) {
            echo '<footer class="footer">
                    <section class="footer_bottom container-fluid text-center">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-4 col-md-ol col-sm-4 col-lg-4 col-xs-4"></div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <p class="copyright">&copy; ' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . ' ' . htmlspecialchars((string) $res['website_name'], ENT_QUOTES, 'UTF-8') . ' - All rights reserved.</p>
                                </div>
                                <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4"></div>
                            </div>
                        </div>
                    </section>
                  </footer>';
        }
    }

    public function edit_footer(): void
    {
        $date = date("Y");
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_footer`");

        foreach ($result as $res) {
            echo '<footer class="footer">
                    <section class="footer_bottom container-fluid text-center">
                        <div class="container">
                            <div class="row">
                                <div class="col-md-6 col-sm-6 col-lg-6 col-xs-6">
                                    <p class="copyright">&copy; <input type="text" value="' . htmlspecialchars($date, ENT_QUOTES, 'UTF-8') . '"> <input type="text" value="' . htmlspecialchars((string) $res['website_name'], ENT_QUOTES, 'UTF-8') . '"> - All rights reserved.</p>
                                </div>
                            </div>
                        </div>
                    </section>
                  </footer>';
        }
    }

    public function showFavicon(): string
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_site`");
        return (string) ($result[0]['favicon'] ?? '');
    }

    public function Favicon(): void
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_site`");

        foreach ($result as $res) {
            echo '<div class="container">
                    <div class="row">
                        <div id="favimg">
                            <img class="img-responsive cp" id="faviconPic" src="' . htmlspecialchars($this->resolveAdminMediaAsset((string) $res['favicon'], 'favicon'), ENT_QUOTES, 'UTF-8') . '" style="width:120px; height:120px;">
                        </div>
                    </div>

                    <div class="col-lg-9 col-md-9 col-sm-8">
                        <div id="message"></div>
                        <div class="col-lg-offset-4 col-lg-8">
                            <button id="favicon_settings" onclick="WIMedia.savefaviconPic()" class="btn btn-success">Save</button>
                        </div>

                        <div class="results" id="results"></div>
                    </div>
                  </div>';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Social / contact
    |--------------------------------------------------------------------------
    */

    public function Social(): void
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_Social`");

        foreach ($result as $res) {
            echo '<ul class="social_media">
                    <li><a href="' . htmlspecialchars((string) $res['facebook'], ENT_QUOTES, 'UTF-8') . '" data-placement="bottom" data-toggle="tooltip" class="fa fa-facebook" title="Facebook">Facebook</a></li>
                    <li><a href="' . htmlspecialchars((string) $res['google'], ENT_QUOTES, 'UTF-8') . '" data-placement="bottom" data-toggle="tooltip" class="fa fa-google-plus" title="Google+">Google+</a></li>
                    <li><a href="' . htmlspecialchars((string) $res['twitter'], ENT_QUOTES, 'UTF-8') . '" data-placement="bottom" data-toggle="tooltip" class="fa fa-twitter" title="Twitter">Twitter</a></li>
                    <li><a href="' . htmlspecialchars((string) ($res['pinterest'] ?? ''), ENT_QUOTES, 'UTF-8') . '" data-placement="bottom" data-toggle="tooltip" class="fa fa-pinterest" title="Pinterest">Pinterest</a></li>
                    <li><a href="' . htmlspecialchars((string) ($res['linkedIn'] ?? ''), ENT_QUOTES, 'UTF-8') . '" data-placement="bottom" data-toggle="tooltip" class="fa fa-linkedin" title="Linkedin">Linkedin</a></li>
                    <li><a href="' . htmlspecialchars((string) ($res['rss'] ?? ''), ENT_QUOTES, 'UTF-8') . '" data-placement="bottom" data-toggle="tooltip" class="fa fa-rss" title="Feedburner">RSS</a></li>
                  </ul>';
        }
    }

    public function contact(): void
    {
        $result = $this->WIdb->bindfree('SELECT * FROM `wi_site`');

        echo '<div class="col-lg-6 col-md-3 col-sm-3 col-xs-12">
                <div class="phone">
                    <ul class="phone__no">';

        foreach ($result as $res) {
            echo '<li class="align">
                    <i class="fa fa-phone" aria-hidden="true"></i>
                    <a href="' . htmlspecialchars((string) $res['contact_no'], ENT_QUOTES, 'UTF-8') . '" class="white" data-placement="bottom" data-toggle="tooltip" title="' . htmlspecialchars((string) $res['contact_no'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars((string) $res['contact_no'], ENT_QUOTES, 'UTF-8') . '</a>
                  </li>';

            echo '<li class="align">
                    <i class="fa fa-envelope-o" aria-hidden="true"></i>
                    <a href="mailto:' . htmlspecialchars((string) $res['contact_email'], ENT_QUOTES, 'UTF-8') . '" class="white" data-placement="bottom" data-toggle="tooltip" title="' . htmlspecialchars((string) $res['contact_email'], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars((string) $res['contact_email'], ENT_QUOTES, 'UTF-8') . '</a>
                  </li>';
        }

        echo '      </ul>
                </div>
              </div>';
    }

    /*
    |--------------------------------------------------------------------------
    | Sidebar / admin menu
    |--------------------------------------------------------------------------
    */

    public function getSidebarSections(): array
    {
        return $this->WIdb->select(
            "SELECT * FROM `wi_sidebar` WHERE `parent` = 0 ORDER BY `sort` ASC, `id` ASC"
        );
    }

    public function getSidebarChildren(int $sectionId): array
    {
        return $this->WIdb->select(
            "SELECT * FROM `wi_sidebar` WHERE `parent` = :parent ORDER BY `sort` ASC, `id` ASC",
            ['parent' => $sectionId]
        );
    }

    public function AddChildren(int $sectionId): void
    {
        $children = $this->getSidebarChildren($sectionId);

        foreach ($children as $res) {
            $link = htmlspecialchars((string) ($res['link'] ?? '#'), ENT_QUOTES, 'UTF-8');
            $img = htmlspecialchars((string) ($res['img'] ?? ''), ENT_QUOTES, 'UTF-8');
            $langKey = (string) ($res['lang'] ?? '');
            $fallbackLabel = (string) ($res['label'] ?? '');

            $translated = $langKey !== '' ? WILang::get($langKey) : '';
            $label = ($translated !== '' && $translated !== $langKey)
                ? $translated
                : ($fallbackLabel !== '' ? $fallbackLabel : $langKey);

            echo '<li class="sidebar-sub-item">';
            echo '<a href="' . $link . '">';
            echo '<i class="fa fa-angle-double-right"></i>';

            if ($img !== '') {
                echo '<img class="img-responsive mobileShow" src="WIMedia/Img/icons/admin_sidebar/' . $img . '.png" alt="">';
            }

            echo '<span class="mobileHide">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
            echo '</a>';
            echo '</li>';
        }
    }

    public function EditAddChildren(int $sectionId): void
    {
        $children = $this->getSidebarChildren($sectionId);

        foreach ($children as $res) {
            $itemId = (int) ($res['id'] ?? 0);

            echo '<div class="sidebar-editor-item">';

            echo '<div class="form-group">';
            echo '<label>Link</label>';
            echo '<input type="text" name="sidebar_items[' . $itemId . '][link]" class="form-control" value="' . htmlspecialchars((string) ($res['link'] ?? ''), ENT_QUOTES, 'UTF-8') . '">';
            echo '</div>';

            echo '<div class="form-group">';
            echo '<label>Label / Language Key</label>';
            echo '<input type="text" name="sidebar_items[' . $itemId . '][lang]" class="form-control" value="' . htmlspecialchars((string) ($res['lang'] ?? ''), ENT_QUOTES, 'UTF-8') . '">';
            echo '</div>';

            echo '<input type="hidden" name="sidebar_items[' . $itemId . '][id]" value="' . $itemId . '">';
            echo '<input type="hidden" name="sidebar_items[' . $itemId . '][parent]" value="' . (int) ($res['parent'] ?? 0) . '">';

            echo '</div>';
        }
    }

    public function EditAdminSideBar(): void
    {
        $sections = $this->getSidebarSections();

        echo '<div id="editaccordion" class="sidebar-editor-accordion">';

        foreach ($sections as $res) {
            $langKey = (string) ($res['lang'] ?? '');
            $fallbackLabel = (string) ($res['label'] ?? 'Menu Section');

            $translated = $langKey !== '' ? WILang::get($langKey) : '';
            $label = ($translated !== '' && $translated !== $langKey)
                ? $translated
                : $fallbackLabel;

            echo '<h3>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</h3>';
            echo '<div>';
            $this->EditAddChildren((int) $res['id']);
            echo '</div>';
        }

        echo '</div>';
    }

    public function AdminSideBar(): void
    {
        $sections = $this->getSidebarSections();

        echo '<ul class="sidebar-menu">';
        echo '<li class="active">';
        echo '<a href="dashboard.php">';
        echo '<i class="fa fa-dashboard"></i> <span>Dashboard</span>';
        echo '</a>';
        echo '</li>';
        echo '</ul>';

        echo '<div id="accordion" class="admin-sidebar-groups">';

        foreach ($sections as $res) {
            $langKey = (string) ($res['lang'] ?? '');
            $fallbackLabel = (string) ($res['label'] ?? 'Menu Section');

            $translated = $langKey !== '' ? WILang::get($langKey) : '';
            $label = ($translated !== '' && $translated !== $langKey)
                ? $translated
                : $fallbackLabel;

            echo '<h3>' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</h3>';
            echo '<div>';
            echo '<ul class="sidebar-menu sidebar-sub-menu">';
            $this->AddChildren((int) $res['id']);
            echo '</ul>';
            echo '</div>';
        }

        echo '</div>';
    }

    public function CreateSidebarLink(string $name, string $link, int $parent = 0): array
    {
        $name = trim($name);
        $link = trim($link);

        if ($name === '' || $link === '') {
            return $this->error('Sidebar link name and URL are required.');
        }

        $this->WIdb->insert('wi_sidebar', [
            'label' => $name,
            'lang' => $name,
            'link' => $link,
            'parent' => $parent,
        ]);

        $this->notify('Created sidebar link');

        return $this->success('Sidebar link created.');
    }

    public function saveSidebarItems(array $items): array
    {
        if ($items === []) {
            return $this->error('No sidebar items were submitted.');
        }

        foreach ($items as $item) {
            $id = (int) ($item['id'] ?? 0);

            if ($id <= 0) {
                continue;
            }

            $data = [
                'link'   => trim((string) ($item['link'] ?? '')),
                'lang'   => trim((string) ($item['lang'] ?? '')),
                'parent' => (int) ($item['parent'] ?? 0),
            ];

            if ($data['lang'] !== '') {
                $data['label'] = $data['lang'];
            }

            $this->WIdb->update(
                'wi_sidebar',
                $data,
                '`id` = :id',
                ['id' => $id]
            );
        }

        $this->notify('Updated sidebar menu');

        return $this->success('Sidebar menu updated.');
    }

     
    public function AdminMenu(): void
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_admin_menu`");

        echo '<ul class="nav navbar-nav">';

        foreach ($result as $res) {
            echo '<li class="top_admin_li"><a href="' . htmlspecialchars((string) $res['link'], ENT_QUOTES, 'UTF-8') . '">' . WILang::get((string) $res['lang']) . '</a></li>';
        }

        echo '</ul>';
    }

    public function MainMenu(): void
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_menu` ORDER BY `sort` ASC");

        echo '<div class="menu">
                <div class="col-lg-12 col-md-12 col-sm-12 menusT">
                    <div id="nav">
                        <ul id="sortable" class="mainMenu default">';

        foreach ($result as $res) {
            echo '<li class="ings" id="' . (int) $res['id'] . '" sort="' . htmlspecialchars((string) $res['sort'], ENT_QUOTES, 'UTF-8') . '">
                    ' . WILang::get((string) $res['lang']) . '
                  </li>';
        }

        echo '          </ul>
                    </div>
                </div>
              </div>';
    }

    public function editMenu(int $id): array
    {
        $res = $this->WIdb->select("SELECT * FROM `wi_menu` WHERE `id` = :id LIMIT 1", ['id' => $id]);

        if (count($res) === 0) {
            return $this->error('Menu item not found.');
        }

        return [
            'status' => 'completed',
            'menu' => $res[0],
        ];
    }

    public function menuEdit(array $menu): array
    {
        $data = $menu['MenuData'] ?? $menu;
        $id = (int) ($data['id'] ?? 0);

        if ($id <= 0) {
            return $this->error('Invalid menu item.');
        }

        unset($data['id']);

        $this->WIdb->update('wi_menu', $data, '`id` = :id', ['id' => $id]);
        $this->notify('Updated menu item');

        return $this->success('Menu item updated.');
    }

    public function newmenuitem(array $menu): array
    {
        $data = $menu['MenuData'] ?? $menu;

        if (!is_array($data) || $data === []) {
            return $this->error('Invalid menu data.');
        }

        $this->WIdb->insert('wi_menu', $data);
        $this->notify('Created menu item');

        return $this->success('Menu item created.');
    }

    public function DeleteMenu(int $id): array
    {
        $this->WIdb->delete('wi_menu', 'id = :id', ['id' => $id]);
        $this->notify('Deleted menu item');

        return $this->success('Menu item deleted.');
    }


    public function editAdminMenu(int $id): array
    {
        $res = $this->WIdb->select(
            "SELECT * FROM `wi_admin_menu` WHERE `id` = :id LIMIT 1",
            ['id' => $id]
        );

        if (count($res) === 0) {
            return $this->error('Admin menu item not found.');
        }

        return [
            'status' => 'completed',
            'menu'   => $res[0],
        ];
    }

    public function adminMenuEdit(array $menu): array
    {
        $data = $menu['MenuData'] ?? $menu;
        $id   = (int) ($data['id'] ?? 0);

        if ($id <= 0) {
            return $this->error('Invalid admin menu item.');
        }

        $update = [
            'label' => trim((string) ($data['name'] ?? $data['label'] ?? '')),
            'lang'  => trim((string) ($data['lang'] ?? $data['name'] ?? $data['label'] ?? '')),
            'link'  => trim((string) ($data['link'] ?? '')),
        ];

        if ($update['label'] === '' || $update['link'] === '') {
            return $this->error('Admin menu name and link are required.');
        }

        $this->WIdb->update(
            'wi_admin_menu',
            $update,
            '`id` = :id',
            ['id' => $id]
        );

        $this->notify('Updated admin menu item');

        return $this->success('Admin menu item updated.');
    }

    public function newAdminMenuItem(array $menu): array
    {
        $data = $menu['MenuData'] ?? $menu;

        $insert = [
            'label'  => trim((string) ($data['name'] ?? $data['label'] ?? '')),
            'lang'   => trim((string) ($data['lang'] ?? $data['name'] ?? $data['label'] ?? '')),
            'link'   => trim((string) ($data['link'] ?? '')),
            'parent' => (int) ($data['parent'] ?? 0),
            'sort'   => isset($data['sort']) && $data['sort'] !== '' ? (int) $data['sort'] : 0,
        ];

        if ($insert['label'] === '' || $insert['link'] === '') {
            return $this->error('Admin menu name and link are required.');
        }

        $this->WIdb->insert('wi_admin_menu', $insert);
        $this->notify('Created admin menu item');

        return $this->success('Admin menu item created.');
    }

    public function deleteAdminMenu(int $id): array
    {
        if ($id <= 0) {
            return $this->error('Invalid admin menu item.');
        }

        $this->WIdb->delete('wi_admin_menu', 'id = :id', ['id' => $id]);
        $this->notify('Deleted admin menu item');

        return $this->success('Admin menu item deleted.');
    }

    public function renderAdminMenuManager(): void
    {
        $items = $this->WIdb->bindfree("SELECT * FROM `wi_admin_menu` ORDER BY `sort` ASC, `id` ASC");

        echo '<div class="wi-admin-menu-list">';

        if (!$items || count($items) === 0) {
            echo '<div class="alert alert-info">No admin menu items found.</div>';
            echo '</div>';
            return;
        }

        foreach ($items as $item) {
            $id    = (int) ($item['id'] ?? 0);
            $label = (string) ($item['label'] ?? '');
            $lang  = (string) ($item['lang'] ?? '');
            $link  = (string) ($item['link'] ?? '#');
            $sort  = isset($item['sort']) ? (int) $item['sort'] : 0;

            echo '<div class="wi-admin-menu-item">';
                echo '<div class="wi-admin-menu-item-main">';
                    echo '<div class="wi-admin-menu-item-title">'
                        . htmlspecialchars($label !== '' ? $label : $lang, ENT_QUOTES, 'UTF-8')
                        . '</div>';
                    echo '<div class="wi-admin-menu-item-meta">';
                        echo '<span><strong>Lang:</strong> ' . htmlspecialchars($lang, ENT_QUOTES, 'UTF-8') . '</span>';
                        echo '<span><strong>Link:</strong> ' . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . '</span>';
                        echo '<span><strong>Sort:</strong> ' . $sort . '</span>';
                    echo '</div>';
                echo '</div>';

                echo '<div class="wi-admin-menu-item-actions">';
                    echo '<button type="button" class="btn btn-sm btn-primary" onclick="WIMenu.editAdminMenu(' . $id . ');">
                            <i class="fa fa-pencil"></i> Edit
                          </button>';
                    echo '<button type="button" class="btn btn-sm btn-danger" onclick="WIMenu.deleteAdminMenu(' . $id . ');">
                            <i class="fa fa-trash"></i> Delete
                          </button>';
                echo '</div>';
            echo '</div>';
        }

        echo '</div>';
    }


    /*
    |--------------------------------------------------------------------------
    | Legacy placeholders for methods still referenced elsewhere
    |--------------------------------------------------------------------------
    */

    public function viewTrans(?string $page = null): void
    {
        echo '<div class="alert alert-info">Translation view cleanup pending.</div>';
    }

    public function viewLang(): void
    {
        echo '<div class="alert alert-info">Language view cleanup pending.</div>';
    }

    public function saveLang(string $name, string $code, string $flag): array
    {
        return $this->success('Language saved (placeholder).');
    }

    public function saveeditLang(string $name, string $code, string $flag, int|string $id): array
    {
        return $this->success('Language updated (placeholder).');
    }

    public function DeleteCountryLang(int|string $id): array
    {
        return $this->success('Language deleted (placeholder).');
    }

    public function transitemdelete(int|string $id): array
    {
        return $this->success('Translation item deleted (placeholder).');
    }

    public function getLangInfo(string $lang): array
    {
        return [
            'status' => 'success',
            'message' => 'Language info placeholder.',
            'data' => ['lang' => $lang],
        ];
    }
}