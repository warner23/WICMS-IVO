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
include_once 'WILogin.php';
include_once 'WIRegister.php';
include_once 'WIUser.php';
include_once 'WIHelperFunctions.php';
include_once 'WISite.php';
include_once 'WIMaintenace.php';
include_once 'WIUserInfo.php';
include_once 'WICalendar.php';
include_once 'WIEditor.php';
include_once 'WIImage.php';
include_once 'WIModal.php';
include_once 'WIContact.php';


$WIdb = WIdb::getInstance();


WISession::startSession();

WISession::set("name", WEBSITE_NAME);

if(WISession::get('session_id') == ""){
$randomNumber = rand(); 
$session_id = WISession::set("session_id", $randomNumber);
}else{

}

$Multilang  = new WILang();
$login    = new WILogin();
$register = new WIRegister();
$mailer   = new WIEmail();
$site   = new WISite();
$validator = new WIValidator();
$maint = new WIMaintenace();


if ( isset ( $_GET['lang'] ) )
	WILang::setLanguage($_GET['lang']);



?>