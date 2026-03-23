<?php
#[\AllowDynamicProperties]
/**
* install Class
* Created by Warner Infinity
* Author Jules Warner
*/

class WIInstall
{
   private $WIdb = null;

    function __construct() 
    {
      $this->WIdb = WIdb::getInstance();
      $this->System = new WISystem();
      //$this->Forum  = new WIForum();
     // $this->Blog  = new WIBlog();
    }

   
   public function pluginCheck($plug)
    {

      $result = $this->WIdb->select("SELECT * FROM `wi_plugin` WHERE `plugin`=:label", array("label" => $plug));
      if($result > 0){
        return "1";
      }else{
        return "0";
      }
    }

    public function sidebarCheck($configs, $plug)
    {
      $label = $configs['sidebar_name'];

      $result = $this->WIdb->select("SELECT * FROM `wi_sidebar` WHERE `label`=:label", array("label" => $label));

      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }

        public function menuCheck($configs, $plug)
        {    
          $label = $configs['sidebar_name'];

          $result = $this->WIdb->select("SELECT * FROM `wi_menu` WHERE `label`=:label", array("label" => $label));

      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }

        public function CssCheck($configs, $plug)
    {
      $result = $this->WIdb->select("SELECT * FROM `wi_css` WHERE `page`=:label", array("label" => $plug));
      //print_r($result);
      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }

    public function JsCheck($configs, $plug)
    {
      $result = $this->WIdb->select("SELECT * FROM `wi_scripts` WHERE `page`=:label", array("label" => $plug));
      //print_r($result);
      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }

    public function MetaCheck($configs, $plug)
    {
      $result = $this->WIdb->select("SELECT * FROM `wi_meta` WHERE `page`=:label", array("label" => $plug));
      //print_r($result);
      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }

        public function pageCheck($configs, $plug)
    {

      $result = $this->WIdb->select("SELECT * FROM `wi_page` WHERE `name`=:label", array("label" => $plug));

      //print_r($result);
      if( count($result) > 1){
        return "1";
      }else{
        return "0";
      }
    }



    public function AddPlugin($configs, $plug)
    {

      $pluginCheck = self::pluginCheck($plug);
      //echo "plug". $pluginCheck;
      if ($pluginCheck === "0") {
        
              $activated = "false";
              $Installed = "true";
        //echo "plugin" .$plug;
        // add plugin into db plugin
        $this->WIdb->insert('wi_plugin', array(
            "plugin"     => $plug,
            "activated"  => $activated,
            "Installed"  => $Installed
           
        ));

      }
 

    }

        public function AddtoSideBar($configs, $plug)
    {

      $label = $configs['sidebar_name'];
      $lang  = $configs['lang'];
      $sort  = $configs['sort_no'];
      $img   = $configs['img'];
  $parent_no = $configs['parent_no'];

      //check if sidebar links have been installed or not
      $sidebarCheck = self::sidebarCheck($configs, $plug);
      //echo "side". $sidebarCheck;
      if ($sidebarCheck === "0") {

         //place into sidebar db
        $this->WIdb->insert('wi_sidebar', array(
            "label" => $label,
            "lang"  => $lang,
            "sort"  => $sort,
            "img"   => $img,
            "parent" => $parent_no
           
        )); 
        $sidebarId = $this->WIdb->lastInsertId();
        
      $link  = $configs['link'];

        $this->WIdb->insert('wi_sidebar', array(
            "label"   => $label,
            "parent"  => $sidebarId,
            "link"    => $link,
            "sort"    => $parent_no,
            "lang"    => $lang
           
        )); 


        $link2  = $configs['link2'];
        $lang2  = $configs['lang2'];
        $this->WIdb->insert('wi_sidebar', array(
            "label"   => $label,
            "parent"  => $sidebarId,
            "link"    => $link2,
            "sort"    => "1",
            "lang"    => $lang2
           
        )); 
      }
       
    }

    public function AddToMenu($configs, $plug)
    {
       $menuCheck = self::menuCheck($configs,$plug);
      // echo "menu".$menuCheck;
      if ($menuCheck === "0") {
         //place into menu db
        $lang  = $configs['lang'];
      $label  = $configs['sidebar_name'];

        $this->WIdb->insert('wi_menu', array(
            "label"   => $label,
            "link"    => $plug .'/index.php',
            "lang"    => $lang
           
        )); 
      }
    }

    public function Tables($configs, $plug)
    {
        // install tables
        require_once dirname(dirname(dirname(__FILE__))) . '/WIPlugin/' . $plug . '/Install/WICore/WIClass/WITable.php';      
        $table = new WITable();
        $table->tables();
    }




    public function StartUpDb($configs, $plug)
    {
      // add meta
      // add css
      //add scripts
      //add page
      $cssCheck = self::CssCheck($configs, $plug);
      $JsCheck = self::JsCheck($configs, $plug);
      $MetaCheck = self::MetaCheck($configs, $plug);
      $pageCheck = self::pageCheck($configs, $plug);
      if ($cssCheck === "0") {
        
        $css = "INSERT INTO `wi_css` ( `href`, `rel`, `page`) VALUES
      ( 'site/css/frameworks/bootstrap.css', 'stylesheet', 'forum'),
      ( 'site/css/login_panel/css/slide.css', 'stylesheet', 'forum'),
      ( 'forum/css/frameworks/menus.css', 'stylesheet', 'forum'),
      ( 'forum/css/style.css', 'stylesheet', 'forum'),
      ( 'site/css/font-awesome.css', 'stylesheet', 'forum'),
      ( 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'forum'),
      ( 'forum/css/forum.css', 'stylesheet', 'forum'),
      ( 'forum/css/layout/wide.css', 'stylesheet', 'forum'),
      ( 'forum/css/switcher.css', 'stylesheet', 'forum'),

      ( 'site/css/frameworks/bootstrap.css', 'stylesheet', 'section'),
      ( 'site/css/login_panel/css/slide.css', 'stylesheet', 'section'),
      ( 'forum/css/frameworks/menus.css', 'stylesheet', 'section'),
      ( 'forum/css/style.css', 'stylesheet', 'section'),
      ( 'site/css/font-awesome.css', 'stylesheet', 'section'),
      ( 'site/css/vendor/bootstrap.min.css', 'stylesheet', 'section'),
      ( 'forum/css/forum.css', 'stylesheet', 'section'),
      ( 'forum/css/layout/wide.css', 'stylesheet', 'section'),
      ( 'forum/css/switcher.css', 'stylesheet', 'section')";


      $query = $this->WIdb->prepare($css);
        $query->execute();

      }

      if ($JsCheck === "0") {
        $js = "INSERT INTO `wi_scripts` ( `src`, `page`) VALUES
        ( 'site/js/frameworks/JQuery.js', 'forum'),
      ( 'site/js/frameworks/bootstrap.js', 'forum'),
      ( 'site/js/login_panel/js/slide.js', 'forum'),
      ( 'site/js/frameworks/JQuery.js', 'section'),
      ( 'site/js/frameworks/bootstrap.js', 'section'),
      ( 'site/js/login_panel/js/slide.js', 'section')

      ";

      $query = $this->WIdb->prepare($js);
        $query->execute();
      }

      if ($MetaCheck === "0") {
        $Meta = "INSERT INTO `wi_meta` ( `page`, `name`, `content`, `author`) VALUES
      ( 'forum', 'viewport', 'width=device-width, initial-scale=1', 'Jules Warner'),
      ( 'forum', 'description', 'Warner-Infinity Content Management System with simplified back end', 'Jules Warner'),
      ( 'forum', 'keywords', 'WI, WICMS, System, UI', 'Jules Warner'),
      ( 'forum', 'author', 'warner-infinity', 'Jules Warner'),

      ( 'section', 'viewport', 'width=device-width, initial-scale=1', 'Jules Warner'),
      ( 'section', 'description', 'Warner-Infinity Content Management System with simplified back end', 'Jules Warner'),
       ( 'section', 'keywords', 'WI, WICMS, System, UI', 'Jules Warner'),
      ( 'section', 'author', 'warner-infinity', 'Jules Warner')
      
      ";
      
      $query = $this->WIdb->prepare($Meta);
        $query->execute();
      }

      if ($pageCheck === "0") {
        $page = "INSERT INTO `wi_page` ( `name`, `panel`, `top_head`, `header`, `left_sidebar`, `right_sidebar`, `contents`, `footer`) VALUES
      ( 'forum', '1', '1', '0', '0', '0', 'forum', '1'),
      ( 'section', '1', '1', '0', '0', '0', 'section', '1');
      ";
        $query = $this->WIdb->prepare($page);
        $query->execute();
      }
      
    }

   

    public function TransferFiles($configs, $plug)
    {
            // forum dir
            echo 'dest ' . SCRIPT_URL . '/';
            $source = SCRIPT_URL .'/WIAdmin/WIPlugin/'. $plug .'/' . $plug.'/' . $plug;
            $dest = SCRIPT_URL . '/' . $plug;
            $check = SCRIPT_URL . '/'. $plug .'/';

            if(!file_exists($check)){
                $this->System->full_copy($source , $dest);
                $fil = glob($dest . "/*");
               print_r($fil);
            }

            //modules dir
            $source1 = SCRIPT_URL .'/WIAdmin/WIPlugin/'. $plug .'/Install/Module/';
            $dest1 = SCRIPT_URL . '/WIAdmin/WIModule/';
            $check1 = SCRIPT_URL . '/WIAdmin/WIModule/'. $configs['lang'];

            if(!file_exists($check1)){
              $this->System->full_copy($source1 , $dest1);
             // $fil = glob($dest1 . "/*");
             //print_r($fil);
            }
            
           // $fil = glob($dest . "/*");
             //  print_r($fil);

            //forum back end forum page WIforum

            $source2 = SCRIPT_URL .'/WIAdmin/WIPlugin/'. $plug .'/forum/' . $plug . '.php';
            $dest2 = SCRIPT_URL . '/' . $plug . '.php';
            $check2 = SCRIPT_URL . '/' . $plug . '.php';

            if(!file_exists($check2)){
              $this->System->file_copy($source2 , $dest2);
            }

            //forum back end forum options page

            $source3 = SCRIPT_URL .'/WIAdmin/WIPlugin/'. $plug .'/forum/' . $plug . '_Options.php';
            $dest3 = SCRIPT_URL . '/WIAdmin/' . $plug . '_Options.php';
            $check3 = SCRIPT_URL . '/WIAdmin/' . $plug . '_Options.php';

            if(!file_exists($check3)){
              $this->System->file_copy($source3 , $dest3);
            }


            //forum back end forum include files 
            
            $source4 = SCRIPT_URL .'/WIAdmin/WIPlugin/'. $plug .'/forum/WIInc/' . $plug;
            $dest4 = SCRIPT_URL . '/WIAdmin/WIInc/site/' . $plug;
            $check4 = SCRIPT_URL . '/WIAdmin/WIInc/site/' . $plug;

            if(!file_exists($check4)){
              $this->System->full_copy($source4 , $dest4);
                //$fil = glob($dest4 . "/*");
               //print_r($fil);
            }

            //forum back end forum inc page  forum

            $source5 = SCRIPT_URL .'/WIAdmin/WIPlugin/'. $plug .'/forum/WIInc/' .$configs['lang'] . '.php';
            $dest5 = SCRIPT_URL . '/WIAdmin/WIInc/';
            $check5 = SCRIPT_URL . '/WIAdmin/WIInc/' .$configs['lang'] . '.php';

            if(!file_exists($check5)){
              $this->System->full_copy($source5 , $dest5);
            }

            //forum back end forum inc page forum_Options
            $source6 = SCRIPT_URL .'/WIAdmin/WIPlugin/'. $plug .'/forum/WIInc/' .$configs['page'].'.php';
            $dest6 = SCRIPT_URL . '/WIAdmin/WIInc/';
            $check6 = SCRIPT_URL . '/WIAdmin/WIInc/' .$configs['page'].'.php';

            if(!file_exists($check6)){
              $this->System->full_copy($source6 , $dest6);

            }
                    

    }

    public function styling($configs, $plug)
    {
            $currentTheme = self::WITheme();

            $source = SCRIPT_URL .'/WIAdmin/WIPlugin/'. $plug . '/Install/Theme/'. $configs['lang'];
            $dest = SCRIPT_URL . '/WITheme/' . $currentTheme .'/'. $configs['lang'];

          $check = SCRIPT_URL . '/WITheme/' . $currentTheme .'/'. $configs['lang'];

            if(!file_exists($check)){
              $this->System->full_copy($source , $dest);
               //$fil = glob($dest . "/*");
               //print_r($fil);
            }
               
            
            
          
    }

    public function WITheme()
    {
      $in_use = 1;
      $sql = "SELECT * FROM  `wi_theme` WHERE `in_use`=:in_use";
      $query = $this->WIdb->prepare($sql);
      $query->bindParam(':in_use', $in_use, PDO::PARAM_INT);
      $query->execute();

      $res = $query->fetch();
      $currentTheme = $res['theme'];

      return $currentTheme;
    }
        

}
