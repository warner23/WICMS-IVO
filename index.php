<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

$page = 'index';

include_once 'WIInc/WI_StartUp.php';

/**
 * Visitor tracking
 */
$ref = $_SERVER['HTTP_REFERER'] ?? '';
$agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$ip = $_SERVER['REMOTE_ADDR'] ?? '';
$tracking_page = $_SERVER['SCRIPT_NAME'] ?? '';

$country = $maint->ip_info($ip, 'country');
$location = $maint->ip_info($ip, 'location');

if ($country === null) {
    $country = 'localhost';
} else {
    $city = is_array($location) ? ($location['city'] ?? '') : '';
    $maint->visitors_log($page, $ip, $country, $ref, $agent, $tracking_page, $city);
}

/**
 * Top panel module
 */
$panelPower = (int)$web->pageModPower($page, 'panel');
$panelModule = $web->PageMod($page, 'panel');

if ($panelPower > 0 && !empty($panelModule)) {
    $mod->getMod("panel");
}

/**
 * Top header strip module
 */
$topPower = (int)$web->pageModPower($page, 'top_head');
$topHeadModule = $web->PageMod($page, 'top_head');

if ($topPower > 0 && !empty($topHeadModule)) {
    $mod->getMod("top_head");
}

/**
 * Main header block
 */
$headerPower = (int)$web->pageModPower($page, 'header');

if ($headerPower > 0) {
    $web->MainHeader();
}

/**
 * Main navigation
 */
$web->MainMenu();

/**
 * Main page content module
 */
$contentsPower = (int)$web->pageModPower($page, 'contents');
$contentModule = $web->PageMod($page, 'contents');

if ($contentsPower > 0 && !empty($contentModule)) {
    $mod->getModMain($contentModule, $page, $contentModule);
}

/**
 * Footer + scripts
 */
$web->footer();
$web->backendJs();
?>
<script type="text/javascript" src="WITheme/Galaxy/site/js/carousal.js"></script>

<!-- Start Style Switcher -->
<div class="switcher"></div>
<!-- End Style Switcher -->

</body>
</html>