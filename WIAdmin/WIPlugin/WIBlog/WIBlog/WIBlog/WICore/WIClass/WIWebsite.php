<?php

/**
* 
*/
class WIWebsite
{
    
    function __construct() 
    {
         $this->WIdb = WIdb::getInstance();
         $this->mobileDetect = new WIMobileDetect();
         $this->Login = new WILogin();
    }




        public function webSite_essentials($column)
    {
        $sql = "SELECT * FROM `wi_header`";
        $query = $this->WIdb->prepare($sql);
        $query->execute();

        $res = $query->fetch(PDO::PARAM_STR);
        //echo $res[$column];
        return $res[$column];
    }

    public function webSite_icons()
    {
     $result = $this->WIdb->select("SELECT * FROM `wi_site`");

        foreach ($result as $res ) {
          echo '<link rel="icon" type="image/png" href="../WIAdmin/WIMedia/Img/favicon/' . $res['favicon'] . '"/>';
        }
    }

    

    public function Meta($page)
    {
$mobile = $this->mobileDetect->isMobile();
       //echo $device;
        if($mobile == 1){ 
          echo '<meta name="viewport" content="width=device-width, 
    user-scalable=no, initial-scale=1, maximum-scale=1, user-scalable=0" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />';
        }else{
        $result = $this->WIdb->select("SELECT * FROM `wi_meta` WHERE `page`=:page",
          array(
            "page" => $page
          )
        );

         foreach($result as $res)
        {
            echo '<meta name="' . $res['name'] . '" content="' . $res['content'] . '" author="' . $res['author'] . '" >';
            
        }

        }

    }

    public function Theme()
    {
      $in_use = 1;

      $result = $this->WIdb->select("SELECT * FROM `wi_theme` WHERE `in_use`=:in_use",
          array(
            "in_use" => $in_use
          )
        );

      $theme = $result[0]['destination'];

      return $theme;

    }
    

    public function Styling($page)
    {
     $result = $this->WIdb->select("SELECT * FROM `wi_css` WHERE `page`=:page",
          array(
            "page" => $page
          )
        );

        foreach($result as $res)
        {
        echo '<link href="../' . self::theme() . $res['href'] . '" rel="' . $res['rel'] . '">';
        }
    }

    public function Scripts($page)
    {
            $result = $this->WIdb->select("SELECT * FROM `wi_scripts` WHERE `page`=:page",
          array(
            "page" => $page
          )
        );

        foreach($result as $res)
        {
        echo ' <script src="../' . self::theme() . $res['src'] . '" type="text/javascript"></script>';
        }
    }

    public function StartUp()
    {
        echo '<!DOCTYPE html>
                <html class="no-js" lang="en">
                <head>   
                  <title>' . WEBSITE_NAME. ' </title>
                  <meta charset="utf-8">';
    }

    public function Social()
    {

        $query = $this->WIdb->prepare('SELECT * FROM `wi_Social`');
        $query->execute();

        while($res = $query->fetch(PDO::FETCH_ASSOC))
        {
            echo ' <ul class="social_media"> 
                            <li><a href="' . $res['facebook'] .'" data-placement="bottom" data-toggle="tooltip" class="fa fa-facebook" title="Facebook">Facebook</a></li>
                            <li><a href="' . $res['google'] .'" data-placement="bottom" data-toggle="tooltip" class="fa fa-google-plus" title="Google+">Google+</a></li>
                            <li><a href="' . $res['twitter'] .'" data-placement="bottom" data-toggle="tooltip" class="fa fa-twitter" title="Twitter">Twitter</a></li>
                            <li><a href="' . $res['pinterest'] .'" data-placement="bottom" data-toggle="tooltip" class="fa fa-pinterest" title="Pinterest">Pinterest</a></li>
                            <li><a href="' . $res['linkedIn'] .'" data-placement="bottom" data-toggle="tooltip" class="fa fa-linkedin" title="Linkedin">Linkedin</a></li>
                            <li><a href="' . $res['rss'] .'" data-placement="bottom" data-toggle="tooltip" class="fa fa-rss" title="Feedburner">RSS</a></li>
                        </ul><!-- End Social --> ';
        }
    }

    public function MainHeader()
    {

        $sql = "SELECT * FROM `wi_header`";
        $query = $this->WIdb->prepare($sql);
        $query->execute();

        while($res = $query->fetch(PDO::PARAM_STR))
        {
         echo ' <header class="header">

                        <div class="col-lg-3 col-md-3 col-sm-2">
                            <div class="navbar_brand">
                                <a href="index.php">
                                <img alt=""  class="logo" src="../WIAdmin/WIMedia/Img/header/' . $res['logo'] .'"></a>
                                
                            </div>
                        </div>
                        <!-- start of header-->
                        <div class="col-lg-9 col-md-9 col-sm-9">
                        <div class="col-ms bg-header" style="background-image: url(../WIAdmin/WIMedia/Img/header/' . $res['bk_header_image'] .');"> 
                        <div class="zapfino">' . $res['header_content'] . '
                        <span class="slogan">' . $res['header_slogan'] . '</span>
                        </div><!-- end col-ms-->
                        </div>

        </header>';
    }

    }


        public function MainMenu()
    {

 $result0 = $this->WIdb->select("SELECT * FROM `wi_menu` ORDER BY `sort`");
        echo '<nav class="navbar navbar-expand-lg navbar-light bg-light">
          <a class="navbar-brand" href="index.php">'; echo WEBSITE_NAME ; echo '</a>

  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
    aria-controls="basicExampleNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>

  <div class="collapse navbar-collapse" id="basicExampleNav">

    <!-- Links -->
    <ul class="navbar-nav mr-auto">';
               $count = "0";
               $loop = count($result0);
        foreach($result0 as $res)
        {

          if($count > 0){
            echo '<li class="nav-item active">
            <a class="nav-link" href="../' . $res['link'] . '">' . WILang::get('' .$res['lang'] .'') . '</a></li>';
         if($res['parent'] > 0)
         {
            echo '<li class="nav-item">
            <a class="nav-link" href="../' . $res['link'] . '">' . WILang::get('' .$res['lang'] .'') . '</a></li>';
         }
          }else{
            echo '<li class="nav-item">
            <a class="nav-link" href="../' . $res['link'] . '">' . WILang::get('' .$res['lang'] .'') . '</a></li>';
         if($res['parent'] > 0)
         {
            echo '<li class="nav-item">
            <a class="nav-link" href="../' . $res['link'] . '">' . WILang::get('' .$res['lang'] .'') . '</a></li>';
         }
          }   
         
        }
        echo '</ul>
             <form class="form-inline">
      <div class="md-form my-0">';
        if($this->Login->isloggedIn() ){
          echo '<ul>
        <li class="nav-item">
            <a class="nav-link" href="../WIMembers/profile.php">' . WILang::get('profile') . '</a></li>
      <li class="nav-item">
            <a class="nav-link" href="../logout.php">' . WILang::get('logout') . '</a></li>
        </ul>';
        }else{
          echo '<ul>
        <li class="nav-item">
            <a class="nav-link" href="../register.php">' . WILang::get('register') . '</a></li>
      <li class="nav-item">
            <a class="nav-link" href="../login.php">' . WILang::get('login') . '</a></li>
        </ul>';
        }
        echo '</div>
    </form>
  </div>
  <!-- Collapsible content -->

</nav>';
    }



    public function footer()
    {
        $id = 1;

        $date = date("Y");
        $http = str_replace("www.", "", $_SERVER['HTTP_HOST']);
        $query = $this->WIdb->prepare('SELECT * FROM `wi_footer` WHERE footer_id=:id');
        $query->bindParam(':id', $id, PDO::PARAM_INT);
        $query->execute();

        while($res = $query->fetch(PDO::PARAM_STR))
        {
            echo '<footer class="footer">
            <section class="footer_bottom container-fluid text-center">
            <div class="container">
                <div class="row">
                <div class="col-md-4 col-md-ol col-sm-4 col-lg-4 col-xs-4">
               
                </div>

                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                        <p class="copyright"><?php echo WILang::get("copyright");?> &copy; ' . $date . ' ' . $res['website_name'] . '-  All rights reserved.</p>
                    </div>
                    <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
                    </div>
                </div>
            </div>
        </section>
        </footer>
        <!--End Footer-->';
        }
    }

    public function langClassSelector($lang)
    {
      //echo $lang;

      if( WILang::getLanguage() === $lang){
        return WILang::getLanguage();
      }else{
        return "fade";
      }

    }

      public function viewLang()
    {
    

         
        $sql = "SELECT * FROM `wi_lang`";
        $query = $this->WIdb->prepare($sql);
        $query->execute();
         echo '<div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                         <div class="flags-wrapper">';
        while($res = $query->fetchAll(PDO::FETCH_ASSOC) ){

          
        foreach ($res as $lang ) {


        
            echo '<a href="../' . $lang['href'] . '">
                 <img src="../WIAdmin/WIMedia/Img/lang/' . $lang['lang_flag'] . '" alt="' . $lang['name'] .'" title="' . $lang['name'] .'"
                      class="'. WIWebsite::langClassSelector($lang['lang']) .'" /></a>';
            }
        }

         echo '</div>
                    </div><!-- end col-lg-6 col-md-6 col-sm-6-->';
    }



   

    public function PageMod($page, $column)
    {
        //echo "col" . $column;

                $result = $this->WIdb->selectColumn(
                    "SELECT * FROM `wi_page` WHERE `name`=:page",
                     array(
                       "page" => $page
                     ), $column
                  );
               // print_r($result[$column]);
         if($result < 1){
            return $column;
         }else{
            return $column;
         }


    }

       public function pageModPower($page, $column)
        {
        //echo "col" . $column;

                $result[$column] = $this->WIdb->selectColumn(
                    "SELECT * FROM `wi_page` WHERE `name`=:page",
                     array(
                       "page" => $page
                     ), $column
                  );
               // print_r($result[$column]);
         if($result < 1){
            return $result[$column];
         }else{
            return $result[$column];
         }


    }

        public function showFavicon()
    {
        $sql = "SELECT * FROM `wi_site`";

        $query = $this->WIdb->prepare($sql);
        $query->execute();

        $res = $query->fetch();

        $favicon = $res['favicon'];
        return $favicon;

    }


     public function google_lang()
    {
      echo '<div class="col-lg-3 col-md-3 col-sm-3 col-xs-6">
                         <div class="flags-wrapper">
                         <div id="google_translate_element"></div><script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: `en`, layout: google.translate.TranslateElement.InlineLayout.SIMPLE}, `google_translate_element`);
}
</script><script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
                         </div>
                    </div><!-- end col-lg-6 col-md-6 col-sm-6-->';

    }


    public function backendJs()
  {

    echo '<script type="text/javascript" src="../' . self::theme() . 'blog/js/vendor/jquery.easing.1.3.js"></script>
  <script type="text/javascript" src="../' . self::theme() . 'blog/js/jquery.cookie.js"></script> <!-- jQuery cookie --> 
  <script type="text/javascript" src="../' . self::theme() . 'blog/js/styleswitch.js"></script> <!-- Style Colors Switcher -->
   
  <script type="text/javascript" src="../' . self::theme() . 'blog/js/plugin/jquery.themepunch.revolution.min.js"></script>
  <script type="text/javascript" src="../' . self::theme() . 'blog/js/plugin/jquery.plugin.js"></script>';
  }
}



?>
