<?php

ini_set('display_errors',1);
error_reporting(E_ALL);
//require 'WILib.php';
require 'WIClass/WI.php';


$token = $register->socialToken();
WISession::set('WI_social_token', $token);
$register->botProtection();
date_default_timezone_set('Europe/London');

spl_autoload_register(function($class)
{
	require_once 'WIClass/' . $class . '.php';
});

$user         = new WIUser(WISession::get("user_id"));
$userInfo     = $user->getInfo();
$userDetails  = $user->getDetails();
$admin        = new WIAdmin(WISession::get("user_id"));
$Info         = new WIUserInfo();
$web          = new WIWebsite();
$mod          = new WIModules();
$maint        = new WIMaintenace();
$perm         = new WIPermissions();
$boot         = new WIBootStrap();




?>
