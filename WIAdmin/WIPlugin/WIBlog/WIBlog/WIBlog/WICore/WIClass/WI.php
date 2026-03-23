<?php


// redirect user to installation page if script is not installed
if ( ! file_exists( dirname(__FILE__) . '/WIConfig.php' ) && ! isset($installation) )
    header("Location: install/install.php");

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
include_once 'WIModules.php';
include_once 'WIPage.php';
include_once 'WIBlog.php';
include_once 'WIPost.php';
include_once 'WIComment.php';
include_once 'WIResponse.php';
include_once 'WIABS.php';


$WIdb = WIdb::getInstance();


WISession::startSession();

$login    = new WILogin();
$register = new WIRegister();
$mailer   = new WIEmail();
$site   = new WISite();
$validator = new WIValidator();
$maint = new WIMaintenace();
$blog    = new WIBlog();
$post    = new WIPost();
$comments  = new WIComment();

if ( isset ( $_GET['lang'] ) )
	WILang::setLanguage($_GET['lang']);
?>