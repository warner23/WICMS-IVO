<?php


// redirect user to installation page if script is not installed
if ( ! file_exists( dirname(__FILE__) . '/WIConfig.php' ) && ! isset($installation) )
    header("Location: WIInstall/index.php");

include_once 'WIConfig.php';
include_once 'WISession.php';
include_once 'WIValidator.php';
include_once 'WILang.php';
include_once 'WIRole.php';
include_once 'WIdb.php';
include_once 'WIEmail.php';
include_once 'WIPermissions.php';
include_once 'WILogin.php';
include_once 'WIRegister.php';
include_once 'WIUser.php';
include_once 'WIHelperFunctions.php';
include_once 'WISite.php';
include_once 'WIMaintenace.php';
include_once 'WIComment.php';
include_once 'WIMobileDetect.php';
include_once 'WIEncryption.php';
include_once 'WIWebsite.php';
include_once 'WICompliance.php';

$WIdb = WIdb::getInstance();

WISession::startSession();

WISession::set("name", 'WICMS');

if(WISession::get("user_id") == ""){

	if(WISession::get("guest_user") == ""){
		$user = rand(150,10000);
      WISession::set("guest_user", $user);
	}
	
}

$Multilang  = new WILang();
$login    = new WILogin();
$register = new WIRegister();
$mailer   = new WIEmail();
$perm     = new WIPermissions();
$site   = new WISite();
$validator = new WIValidator();
$maint = new WIMaintenace();
$comment = new WIComment();
$mobileDetect = new WIMobileDetect();

if ( isset ( $_GET['lang'] ) )
	WILang::setLanguage($_GET['lang']);
?>