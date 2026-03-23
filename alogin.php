<?php
$page = "alogin";

include_once 'WIInc/WI_StartUp.php';

$ref = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

//$ref = $_SERVER['HTTP_REFERER'];
//echo $ref;
$agent = $_SERVER['HTTP_USER_AGENT'];
$ip = $_SERVER['REMOTE_ADDR'];


$tracking_page = $_SERVER['SCRIPT_NAME'];
//$ip = getenv('REMOTE_ADDR');
//$ip2 = $maint->get_ip();
//echo "ip". $ip;
//echo "ip2". $ip2;
$country = $maint->ip_info($ip, "country");
$location = $maint->ip_info($ip, "location");
$city = $location['city'];
//echo "country"  .$country;
if($country === null){
  $country = "localhost";
}else{
  $city = $location['city'];
  $maint->visitors_log($page, $ip, $country, $ref, $agent,$tracking_page, $city);
}


$panelPower = $web->pageModPower($page, "panel");

$Panel = $web->PageMod($page, "panel");
//echo $Panel;
if ($panelPower > 0) {
$mod->getMod($Panel);
}

$topPower = $web->pageModPower($page, "top_head");
$top_head = $web->PageMod($page, "top_head");
//echo $Panel;
if ($topPower > 0) {
$mod->getMod($top_head);
}

$headerPower = $web->pageModPower($page, "header");

if ($headerPower > 0) {
	$web->MainHeader();
}


$web->MainMenu();	


$contents = $web->pageModPower($page, "contents");
//echo $contents;
$mod->getModMain($contents, $page, $contents);

  	
//include_once 'WIInc/welcome_box.php';

$web->footer();
?>



</body>
</html>
