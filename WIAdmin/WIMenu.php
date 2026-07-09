<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Admin - Menu Settings
|--------------------------------------------------------------------------
| Core WICMS route only. Loads the admin shell, then delegates the workspace
| UI to WIAdmin/WIInc/menu.php. Compliance must not be loaded from here.
*/

error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');

include_once 'WICore/init.php';

if (!isset($admin) || !is_object($admin) || !method_exists($admin, 'isAdmin') || !$admin->isAdmin()) {
    header('location:../index.php');
    exit;
}

$page = 'menus';

if (class_exists('WIStartUp')) {
    $startup = new WIStartUp();
    $startup->boot($page);
} else {
    include_once 'WIInc/WI_start_up.php';
}

include_once 'WIInc/WI_header.php';
include_once 'WIInc/sidebar.php';
include_once 'WIInc/menu.php';

echo '</body></html>';
