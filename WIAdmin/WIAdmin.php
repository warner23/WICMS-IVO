<?php
declare(strict_types=1);
error_reporting(E_ALL);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
ini_set('log_errors', '1');

include_once 'WICore/init.php';
 if($admin->isAdmin()){
$page = 'admin';
$startup = new WIStartUp();
$startup->boot($page);
include_once 'WIInc/WI_header.php';
include_once 'WIInc/sidebar.php';
include_once 'WIInc/admin.php';
}else{
header("location:../index.php");
    //echo "kicked out";
}
