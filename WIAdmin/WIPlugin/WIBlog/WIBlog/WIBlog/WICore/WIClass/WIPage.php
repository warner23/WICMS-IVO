<?php

/**
* Pages Class
* Created by Warner Infinity
* Author Jules Warner
*/

class WIPage
{

	function __construct() 
  {
       $this->WIdb = WIdb::getInstance();
       $this->Mods = new WIModules();
  }


    public function GetColums($page_id, $column)
    {
      $sql = "SELECT * FROM `wi_page` WHERE `name` =:name";
      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':name', $page_id, PDO::PARAM_STR);
      $query->execute();
      $res = $query->fetch(PDO::FETCH_ASSOC);
      return $res[$column];
    }

        public function newPage($pageName)
    {
      if( strpos($pageName, " ") !== false )
      {
        $pageName = preg_replace('/\s+/', '_', $pageName);
      }

        $dir = dirname(dirname(dirname(__FILE__)));

        echo $dir;

        $NewPage = fopen($dir. '/'  .$pageName .'.php', "w") or die("Unable to open file!");

$txt = '<?php
$page = "'. $pageName .'";

include_once "WIInc/WI_StartUp.php";

$ref = isset($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : "";

$agent = $_SERVER["HTTP_USER_AGENT"];
$ip = $_SERVER["REMOTE_ADDR"];


$tracking_page = $_SERVER["SCRIPT_NAME"];

$country = $maint->ip_info($ip, "country");
$location = $maint->ip_info($ip, "location");
$city = $location["city"];
if($country === null){
  $country = "localhost";
}

$maint->visitors_log($page, $ip, $country, $ref, $agent, $tracking_page, $city);

$panelPower = $web->pageModPower($page, "panel");

$Panel = $web->PageMod($page, "panel");
if ($panelPower > 0) {
  $mod->getMod($Panel);
}

$topPower = $web->pageModPower($page, "top_head");
$top_head = $web->PageMod($page, "top_head");
if ($topPower > 0) {
  $mod->getMod($top_head);
}

$headerPower = $web->pageModPower($page, "header");
if ($headerPower > 0) {
$web->MainHeader();
}

$web->MainMenu();


$contents = $web->pageModPower($page, "contents");
$mod->getModMain($contents, $page, $contents);

$footerPower = $web->pageModPower($page, "footer");

if ($footerPower > 0) {
$web->footer();
}
?>
  <script type="text/javascript" src="../WITheme/WICMS/blog/js/vendor/jquery.easing.1.3.js"></script>
<script type="text/javascript" src="../WITheme/WICMS/blog/js/jquery.cookie.js"></script> <!-- jQuery cookie --> 
<script type="text/javascript" src="../WITheme/WICMS/blog/js/styleswitch.js"></script> <!-- Style Colors Switcher -->

<script type="text/javascript" src="../WITheme/WICMS/blog/js/plugin/jquery.themepunch.revolution.min.js"></script>
<script type="text/javascript" src="../WITheme/WICMS/blog/js/plugin/jquery.plugin.js"></script>

<!-- Start Style Switcher -->
<div class="switcher"></div>
<!-- End Style Switcher -->
</body>
</html>
        ';
      fwrite($NewPage, $txt);
      fclose($NewPage);


     
       $cssCheck = self::CssCheck($pageName);
      $JsCheck = self::JsCheck($pageName);
      $MetaCheck = self::MetaCheck($pageName);
      $pageCheck = self::pageCheck($pageName);
      if ($cssCheck === "0") {
        
        $css = "INSERT INTO `wi_css` ( `href`, `rel`, `page`) VALUES
      ( 'site/css/frameworks/bootstrap4.css', 'stylesheet', '" . $pageName . "'),
      ( 'site/css/login_panel/css/slide.css', 'stylesheet', '" . $pageName . "'),
      ( 'site/css/frameworks/menus.css', 'stylesheet', '" . $pageName . "'),
      ( 'site/css/style.css', 'stylesheet', '" . $pageName . "'),
      ( 'site/css/font-awesome.css', 'stylesheet', '" . $pageName . "'),
      ( 'site/css/vendor/bootstrap.min.css', 'stylesheet', '" . $pageName . "'),
      ( 'blog/css/style.css', 'stylesheet', '" . $pageName . "'),
      ( 'blog/css/layout/wide.css', 'stylesheet', '" . $pageName . "'),
      ( 'blog/css/switcher.css', 'stylesheet', '" . $pageName . "'),
      ( 'blog/css/blog.css', 'stylesheet', '" . $pageName . "');";

      $query = $this->WIdb->prepare($css);
        $query->execute();
      }

      if ($JsCheck === "0") {
        $js = "INSERT INTO `wi_scripts` ( `src`, `page`) VALUES
      ( 'site/js/frameworks/JQuery.js', '" . $pageName . "'),
      ( 'site/js/frameworks/bootstrap.js', '" . $pageName . "'),
      ( 'site/js/login_panel/js/slide.js', '" . $pageName . "');
      ";

      $query = $this->WIdb->prepare($js);
        $query->execute();
      }

      if ($MetaCheck === "0") {
        $Meta = "INSERT INTO `wi_meta` ( `page`, `name`, `content`, `author`) VALUES
      ( '" . $pageName . "', 'viewport', 'width=device-width, initial-scale=1', 'Jules Warner'),
      ( '" . $pageName . "', 'description', 'Warner-Infinity Content Management System with simplified back end', 'Jules Warner'),
      ( '" . $pageName . "', 'keywords', 'WI, WICMS, System, UI', 'Jules Warner'),
      ( '" . $pageName . "', 'author', 'warner-infinity', 'Jules Warner');
      ";
      
      $query = $this->WIdb->prepare($Meta);
        $query->execute();
      }

      if ($pageCheck === "0") {
        
           $page = "INSERT INTO `wi_page` ( `name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`) VALUES
      ( '" . $pageName . "', '0', '1', '1', '0', '0', 'post', '1');
        ";
        $query = $this->WIdb->prepare($page);
        $query->execute();
        $page_id = $this->WIdb->lastInsertId();
        
      }
        
        

    $results = array(
    "completed" => "saved",
    "msg"    => "successfully createsd new page",
    "page_id" => $page_id,
    "page"  => $pageName
    );
    
    echo json_encode($results);
    }



     public function CssCheck($pageName)
    {
      $label = $pageName;


      $result = $this->WIdb->select("SELECT * FROM `wi_css` WHERE `page`=:label", 
            array(
            "page" => $label
            )
        );
      //print_r($result);
      if( count($result[0]) > 1){
        return "1";
      }else{
        return "0";
      }
    }

    public function JsCheck($pageName)
    {
      $label = $pageName;


       $result = $this->WIdb->select("SELECT * FROM `wi_scripts` WHERE `page`=:label", 
            array(
            "page" => $label
            )
        );
      //print_r($result);
      if( count($result[0]) > 1){
        return "1";
      }else{
        return "0";
      }
    }

    public function MetaCheck($pageName)
    {
      $label = $pageName;


      $result = $this->WIdb->select("SELECT * FROM `wi_meta` WHERE `page`=:label", 
            array(
            "page" => $label
            )
        );
      //print_r($result);
      if( count($result[0]) > 1){
        return "1";
      }else{
        return "0";
      }
    }

        public function pageCheck($pageName)
    {
      $label = $pageName;


      $result = $this->WIdb->select("SELECT * FROM `wi_page` WHERE `page`=:label", 
            array(
            "page" => $label
            )
        );
      //print_r($result);
      if( count($result[0]) > 1){
        return "1";
      }else{
        return "0";
      }
    }

   
}