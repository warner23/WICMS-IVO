<?php
declare(strict_types=1);

/**
* WEbsite Class
* Created by Warner Infinity
* Author Jules Warner
*/

#[\AllowDynamicProperties]
class WIWebsite
{
    public function __construct()
    {
        $this->WIdb         = WIdb::getInstance();
        $this->mobileDetect = new WIMobileDetect();
        $this->Login        = new WILogin();
        $this->User         = new WIUser(WISession::get('user_id'));
    }

    private function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }

    private function getSingleRow(string $table, string $where = '', array $params = []): array
    {
        $sql = "SELECT * FROM `{$table}`";

        if ($where !== '') {
            $sql .= " WHERE {$where}";
        }

        $sql .= " LIMIT 1";

        $result = $this->WIdb->select($sql, $params);

        return $result[0] ?? [];
    }

    public function webSite_essentials($column)
    {
        $row = $this->getSingleRow('wi_header');
        return $row[$column] ?? null;
    }

    public function webSite_icons()
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_site`");
        //var_dump($result);
        foreach ($result as $res) {
            $favicon = $this->e($res['favicon'] ?? '');
            echo '<link rel="icon" type="image/png" href="WIAdmin/WIMedia/Img/favicon/' . $favicon . '"/>';
        }
    }

    public function Meta($page)
    {
        $mobile = $this->mobileDetect->isMobile();

        if ((int)$mobile === 1) {
            echo '<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1, maximum-scale=1, user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />';
            return;
        }

        $result = $this->WIdb->select(
            "SELECT * FROM `wi_meta` WHERE `page` = :page",
            [
                "page" => $page
            ]
        );
        //var_dump($result);
        foreach ($result as $res) {
            echo '<meta name="' . $this->e($res['name'] ?? '') . '" content="' . $this->e($res['content'] ?? '') . '" author="' . $this->e($res['author'] ?? '') . '" >';
        }
    }

    public function Theme()
    {
        $result = $this->WIdb->select(
            "SELECT * FROM `wi_theme` WHERE `in_use` = :in_use LIMIT 1",
            [
                "in_use" => 1
            ]
        );
        //var_dump($result);
        return $result[0]['destination'] ?? '';
    }

    public function Styling($page)
    {
        $result = $this->WIdb->select(
            "SELECT * FROM `wi_css` WHERE `page` = :page",
            [
                "page" => $page
            ]
        );
        //var_dump($result);
        foreach ($result as $res) {
            echo '<link href="' . $this->e($this->Theme() . ($res['href'] ?? '')) . '" rel="' . $this->e($res['rel'] ?? 'stylesheet') . '">';
        }
    }

    public function Scripts($page)
    {
        $result = $this->WIdb->select(
            "SELECT * FROM `wi_scripts` WHERE `page` = :page",
            [
                "page" => $page
            ]
        );
        //var_dump($result);

        foreach ($result as $res) {
            echo '<script src="' . $this->e($this->Theme() . ($res['src'] ?? '')) . '" type="text/javascript"></script>';
        }
    }

    public function StartUp()
    {

        echo "<!DOCTYPE html>
<html class='no-js' lang='en'>
<head>

<!-- Google Tag Manager -->
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
})(window,document,'script','dataLayer','GTM-KCW4NMQ');</script>
<!-- End Google Tag Manager -->

<title>" . $this->e(WEBSITE_NAME) . "</title>
<meta charset='utf-8'>";
    }

    public function Social()
    {
        $result = $this->WIdb->bindfree('SELECT * FROM `wi_social`');

        echo '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
        <ul class="social_media">';

        foreach ($result as $res) {
            $href = $this->e($res['href'] ?? '#');
            $name = $this->e($res['name'] ?? '');

            echo '<li>
            <a href="' . $href . '" target="_blank" data-placement="bottom" data-toggle="tooltip" class="fa fa-' . $name . ' fa-5x" title="' . $name . '">
            ' . $name . '
            </a></li>';
        }

        echo '</ul></div>';
    }

    public function contact()
    {
        $result = $this->WIdb->bindfree('SELECT * FROM `wi_site`');

        echo '<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
        <div class="phone">
        <style>
        .phone{

        }

        .phone__no{
        width: 100%;
        }

        .white{
            color: rgb(12 12 12)!important;
        }

        .align{
            width:fit-content;
            float:left;
        }
        </style>
        <ul class="phone__no">';

        foreach ($result as $res) {
            $contactNo = $this->e($res['contact_no'] ?? '');
            $contactEmail = $this->e($res['contact_email'] ?? '');

            echo '<li class="align">
            <i class="fa fa-phone" aria-hidden="true"></i>
            <a href="tel:' . $contactNo . '" class="white" data-placement="bottom" data-toggle="tooltip" title="' . $contactNo . '">' . $contactNo . '
            </a></li>';

            echo '<li class="align">
            <i class="fa fa-envelope-o" aria-hidden="true"></i>
            <a href="mailto:' . $contactEmail . '" class="white" data-placement="bottom" data-toggle="tooltip" title="' . $contactEmail . '">' . $contactEmail . '
            </a></li>';
        }

        echo '</ul></div></div>';
    }

    public function MainHeader()
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_header`");

        foreach ($result as $res) {
            $logo = $this->e($res['logo'] ?? '');
            $bkHeader = $this->e($res['bk_header_image'] ?? '');
            $headerContent = $res['header_content'] ?? '';
            $headerSlogan = $res['header_slogan'] ?? '';

            echo '<header class="header">

                        <div class="col-lg-3 col-md-3 col-sm-2">
                            <div class="navbar_brand">
                                <a href="index.php">
                                <img alt="" class="logo" src="WIAdmin/WIMedia/Img/header/' . $logo . '"></a>

                            </div>
                        </div>
                        <div class="col-lg-9 col-md-9 col-sm-9">
                        <div class="col-ms bg-header" style="background-image: url(WIAdmin/WIMedia/Img/header/' . $bkHeader . ');">
                        <div class="zapfino">' . $headerContent . '
                        <span class="slogan">' . $headerSlogan . '</span>
                        </div>
                        </div>

        </header>';
        }
    }

    public function MainMenu()
    {
        $result0 = $this->WIdb->bindfree("SELECT * FROM `wi_menu` ORDER BY `sort` ASC, `id` ASC");

        echo '<nav class="navbar navbar-expand-lg navbar-light bg-light">
          <a class="navbar-brand" href="index.php">' . $this->e(WEBSITE_NAME) . '</a>

  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#expanderNav"
    aria-controls="basicExampleNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="expanderNav">

    <ul class="navbar-nav mr-auto">';

        foreach ($result0 as $res) {
            $link = $this->e($res['link'] ?? '#');
            $langKey = (string)($res['lang'] ?? '');
            $label = $this->e(WILang::get($langKey));

            echo '<li class="nav-item">
            <a class="nav-link" href="' . $link . '">' . $label . '</a></li>';

            if ((int)($res['parent'] ?? 0) > 0) {
                echo '<li class="nav-item">
                <a class="nav-link" href="' . $link . '">' . $label . '</a></li>';
            }
        }

        if ($this->User->isStaff()) {
            echo '<li class="nav-item">
            <a class="nav-link" href="WIPOS/WIAdmin/cashposlogin.php">' . $this->e(WILang::get('Staff_log_in')) . '</a></li>
            <li class="nav-item">
            <a class="nav-link" href="WICompliance/WIAdmin/admin_comp_login.php">' . $this->e(WILang::get('compliance')) . '</a></li>
            <li class="dropdown" style="padding-top: 13px;">
            <a href="javascript:void(0)" id="specs" class="dropdown-toggle" aria-expanded="false" dropdown="false" data-toggle="dropdown">' . $this->e(WILang::get('specs')) . '</a>
<div class="dropdown-menu" >

            <a class="nav-link" href="WIPOS/WICashier/kitchen.php">' . $this->e(WILang::get('kitchen')) . '</a>
            <a class="nav-link" href="WIPOS/WICashier/bar.php">' . $this->e(WILang::get('bar')) . '</a>
</div>

</li>';
        }

        echo '</ul>
             <form class="form-inline">
      <div class="md-form my-0">';

        if ($this->Login->isLoggedIn()) {
            echo '<ul>
        <li class="nav-item">
            <a class="nav-link" href="WIMembers/profile.php">' . $this->e(WILang::get('profile')) . '</a></li>
      <li class="nav-item">
            <a class="nav-link" href="logout.php">' . $this->e(WILang::get('logout')) . '</a></li>
        </ul>';
        } else {
            echo '<ul>
        <li class="nav-item">
            <a class="nav-link" href="register.php">' . $this->e(WILang::get('register')) . '</a></li>
      <li class="nav-item">
            <a class="nav-link" href="login.php">' . $this->e(WILang::get('login')) . '</a></li>
        </ul>';
        }

        echo '</div>
    </form>
  </div>

</nav>';
    }

    public function footer()
    {
        $date = date("Y");
        $res = $this->getSingleRow('wi_footer', 'footer_id = :id', ['id' => 1]);

        if (!empty($res)) {
            echo '<footer class="footer">
            <section class="footer_bottom container-fluid text-center">
            <div class="container">
                <div class="row">

                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p class="copyright">' . $this->e(WILang::get("copyright")) . ' &copy; ' . $this->e($date) . ' ' . $this->e($res['website_name'] ?? WEBSITE_NAME) . ' - All rights reserved Powered by WICMS.</p>
                    </div>

                </div>
            </div>
        </section>
        </footer>
        <!--End Footer-->';
        }
    }

    public static function langClassSelector($lang)
    {
        if (WILang::getLanguage() === $lang) {
            return WILang::getLanguage();
        }

        return "fade";
    }

    public function viewLang()
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_lang`");

        echo '<div class="col-lg-5 col-md-5 col-sm-5 col-xs-5">
                         <div class="flags-wrapper">';

        foreach ($result as $lang) {
            echo '<a href="' . $this->e($lang['href'] ?? '#') . '">
                 <img src="WIAdmin/WIMedia/Img/lang/' . $this->e($lang['lang_flag'] ?? '') . '" alt="' . $this->e($lang['name'] ?? '') . '" title="' . $this->e($lang['name'] ?? '') . '"
                      class="' . $this->e(self::langClassSelector($lang['lang'] ?? '')) . '" /></a>';
        }

        echo '</div>
                    </div>';
    }

    public function PageMod($page, $column)
    {
        
        return $this->WIdb->selectColumn(
            "SELECT * FROM `wi_page` WHERE `name` = :page LIMIT 1",
            [
                "page" => $page
            ],
            $column
        );
    }

    public function pageModPower($page, $column)
    {
        $result = $this->WIdb->selectColumn(
            "SELECT * FROM `wi_page` WHERE `name` = :page LIMIT 1",
            [
                "page" => $page
            ],
            $column
        );

        if ($result === null || $result === '') {
            return 0;
        }

        if (is_numeric($result)) {
            return (int)$result;
        }

        return strlen((string)$result) > 0 ? 1 : 0;
    }

    public function showFavicon()
    {
        $site = $this->getSingleRow('wi_site');
        return $site['favicon'] ?? '';
    }

    public function google_lang()
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

    public function backendJs()
    {
        echo '<script type="text/javascript" src="' . $this->e($this->Theme() . 'site/js/vendor/jquery.easing.1.3.js') . '"></script>
  <script type="text/javascript" src="' . $this->e($this->Theme() . 'site/js/jquery.cookie.js') . '"></script>
  <script type="text/javascript" src="' . $this->e($this->Theme() . 'site/js/styleswitch.js') . '"></script>
  <script type="text/javascript" src="' . $this->e($this->Theme() . 'site/js/plugin/jquery.themepunch.revolution.min.js') . '"></script>
  <script type="text/javascript" src="' . $this->e($this->Theme() . 'site/js/plugin/jquery.plugin.js') . '"></script>';
    }


    public function getPage(): string
    {
        if (!empty($_GET['page'])) {
            return preg_replace('/[^a-zA-Z0-9_-]/', '', $_GET['page']);
        }

        return 'home';
    }




    public function getPageModule(string $page): ?string
    {
        return $this->WIdb->selectColumn(
            "SELECT module_name FROM wi_page WHERE name = :page LIMIT 1",
            ["page" => $page],
            "module_name"
        );
    }

    public function modulePowered(string $moduleName): bool
    {
        $power = $this->WIdb->selectColumn(
            "SELECT mod_powered FROM wi_mod WHERE module_name = :name LIMIT 1",
            ["name" => $moduleName],
            "mod_powered"
        );

        return $power === "power_on";
    }

    public function loadModule(string $moduleName): void
    {
        $file = "WIAdmin/WIModule/modules/{$moduleName}/{$moduleName}.php";

        if (!file_exists($file)) {
            echo "Module file missing: " . htmlspecialchars($moduleName);
            return;
        }

        require_once $file;

        if (!class_exists($moduleName)) {
            echo "Module class missing: " . htmlspecialchars($moduleName);
            return;
        }

        $module = new $moduleName();

        if (method_exists($module, "mod_name")) {
            $module->mod_name();
        }
    }
}
?>