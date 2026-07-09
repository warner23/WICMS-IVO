<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('log_errors', '1');


require_once 'WICore/WIClass/WI.php';

$page = 'contact_us';
$startup = new WIStartUp();

$startup->boot($page);
$startup->header($page);
//die('WI loaded');
$modules = new WIModules();
$moduleName = $modules->getModuleNameByPage($page) ?? 'notfound';
$modules->getModMain($moduleName, $page);

$startup->footer();