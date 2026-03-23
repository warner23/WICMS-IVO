<?php
#[\AllowDynamicProperties]
/**
* 
*/
class WIWebsite
{
    
    function __construct() 
    {
         $this->WIdb = WIdb::getInstance();
         $this->mobileDetect = new WIMobileDetect();
         $this->Login        = new WILogin();
         $this->User         = new WIUser(WISession::get('user_id'));

    }




        public function webSite_essentials($column)
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_header`");
        return $result[$column];
    }

    public function webSite_icons()
    {

      $result = $this->WIdb->bindfree("SELECT * FROM `wi_site`");

        foreach ($result as $res ) {
          echo '<link rel="icon" type="image/png" href="../../WIAdmin/WIMedia/Img/favicon/' . $res['favicon'] . '"/>';
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
        echo '<link href="../../' . self::theme() . $res['href'] . '" rel="' . $res['rel'] . '">';
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
        echo ' <script src="../../' . self::theme() . $res['src'] . '" type="text/javascript"></script>';
        }
    }

    public function StartUp()
    {

      //If the HTTPS is not found to be "on"

        echo '<!DOCTYPE html>
                <html class="no-js" lang="en">
                <head>   
                  <title>' . WEBSITE_NAME. ' </title>
                  <meta charset="utf-8">';
    }

    public function Social()
    {

      $result = $this->WIdb->bindfree("SELECT * FROM `wi_Social`");

        foreach($result as $res)
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
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_header`");
        foreach($result as $res)
        {
         echo ' <header class="header">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="navbar_brand">
                                <a href="index.php">
                                <img alt=""  class="logo img-responsiv3" src="WIAdmin/WIMedia/Img/header/' . $res['logo'] .'"></a>
                                
                            </div>
                        </div>
                    </div>
                </div> 
        </header>';
    }

    }


     public function MainMenu()
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_menu` ORDER BY `sort`");

 echo '<nav class="navbar navbar-expand-lg navbar-light bg-light">
          <a class="navbar-brand" href="../../index.php">'; echo WEBSITE_NAME ; echo '</a>

  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#menuNav"
    aria-controls="basicExampleNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"><img src="../../WIAdmin/WIMedia/Img/dots.PNG" style="width: 25px;"></span>
  </button>

  <div class="collapse navbar-collapse" id="menuNav">

    <!-- Links -->
    <ul class="navbar-nav mr-auto">';
               $count = "0";
               $loop = count($result);
        foreach($result as $res)
        {

          if($count > 0){
            echo '<li class="nav-item active">
            <a class="nav-link" href="../../' . $res['link'] . '">' . WILang::get('' .$res['lang'] .'') . '</a></li>';
         if($res['parent'] > 0)
         {
            echo '<li class="nav-item">
            <a class="nav-link" href="../../' . $res['link'] . '">' . WILang::get('' .$res['lang'] .'') . '</a></li>';
         }
          }else{
            echo '<li class="nav-item">
            <a class="nav-link" href="../../' . $res['link'] . '">' . WILang::get('' .$res['lang'] .'') . '</a></li>';
         if($res['parent'] > 0)
         {
            echo '<li class="nav-item">
            <a class="nav-link" href="../../' . $res['link'] . '">' . WILang::get('' .$res['lang'] .'') . '</a></li>';
         }
          }   
         
        }

        if($this->User->isStaff()){
               echo '<li class="nav-item">
            <a class="nav-link" href="../../WIPOS/indexu.php">' . WILang::get('Staff_log_in') . '</a></li>
            <li class="nav-item">
            <a class="nav-link" href="index.php">' . WILang::get('compliance') . '</a></li>
            <li class="dropdown" style="padding-top: 13px;">
            <a href="javascript:void(0)" id="specs" class="dropdown-toggle-menu" aria-expanded="false" dropdown="false" data-toggle="dropdown">' . WILang::get('specs') . '</a>
<div class="dropdown-menu-specs" style="display:none;" >
 
            <a class="nav-link" href="WIPOS/WICashier/kitchen.php">' . WILang::get('kitchen') . '</a>
            <a class="nav-link" href="WIPOS/WICashier/bar.php">' . WILang::get('bar') . '</a>
</div>

</li>'; 
            }
        echo '</ul>
             <form class="form-inline">
      <div class="md-form my-0">';
        if($this->Login->isloggedIn() ){
          echo '<ul>
        <li class="nav-item">
            <a class="nav-link" href="../../WIMembers/profile.php">' . WILang::get('profile') . '</a></li>
      <li class="nav-item">
            <a class="nav-link" href="../../logout.php">' . WILang::get('logout') . '</a></li>
        </ul>';
        }else{
          echo '<ul>
        <li class="nav-item">
            <a class="nav-link" href="../../register.php">' . WILang::get('register') . '</a></li>
      <li class="nav-item">
            <a class="nav-link" href="../../login.php">' . WILang::get('login') . '</a></li>
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

        $result = $this->WIdb->select("SELECT * FROM `wi_footer` WHERE footer_id=:id",
          array(
            "id" => $id
          )
        );


        foreach($result as $res)
        {
            echo '<footer class="footer">
            <section class="footer_bottom text-center">
            <div class="container">
                <div class="row">


                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <p class="copyright"><?php echo WILang::get("copyright");?> &copy; ' . $date . ' ' . $res['website_name'] . '-  All rights reserved Powered by WICMS.</p>
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
    

        $result = $this->WIdb->bindfree("SELECT * FROM `wi_lang`");

        echo '<div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
                         <div class="flags-wrapper">';
        foreach ($result as $lang ) {

       echo '<a href="' . $lang['href'] . '">
             <img src="WIAdmin/WIMedia/Img/lang/' . $lang['lang_flag'] . '" alt="' . $lang['name'] .'" title="' . $lang['name'] .'"
                      class="'. WIWebsite::langClassSelector($lang['lang']) .'" /></a>';
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
               //print_r($result[$column]);
         if($result[$column] < 1){
            return $result[$column];
         }else{
            return $result[$column];
         }


    }

        public function showFavicon()
    {
      $result = $this->WIdb->select("SELECT * FROM `wi_site`");

/*        $sql = "SELECT * FROM `wi_site`";

        $query = $this->WIdb->prepare($sql);
        $query->execute();

        $res = $query->fetch();*/

        $favicon = $res[0]['favicon'];
        return $favicon;

    }

    public function google_lang()
    {
      echo '<div class="col-lg-5 col-md-5 col-sm-5 col-xs-10">
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

    echo '<script type="text/javascript" src="' . self::theme() . 'site/js/jquery.cookie.js"></script> <!-- jQuery cookie --> 
       <script type="text/javascript" src="' . self::theme() . 'site/js/styleswitch.js"></script> <!-- Style Colors Switcher -->
       <script type="text/javascript" src="' . self::theme() . 'site/js/jquery.themepunch.revolution.min.js"></script>
   ';
  }

  public function adminSidebar()
    {
       echo '<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#accordion" ).accordion({
      heightStyle: "content"
    });
  } );
  </script>

<div id="accordion">
  <h3>Dashboard</h3>
  <div>
  <li>
  <a href="dashboard.php">
  <i class="ni ni-tv-2 text-primary"></i> Dashboard
 <img class="img-responsive mobileShow" src="WIMedia/Img/icons/admin_sidebar/Dashboard.png">
  <span class="mobileHide">Dashboard</span></a>
  </li>
  </div>
  <h3>HRM</h3>
  <div>
    <li>
  <a href="hrm.php">
  <i class="fas fa-user-tie text-primary"></i> HRM
 <img class="img-responsive mobileShow" src="WIMedia/Img/icons/admin_sidebar/HRM.png">
  <span class="mobileHide">HRM</span></a>
  </li>
  </div>
  <h3>Customers</h3>
  <div>
   <li>
  <a href="customes.php">
     <i class="fas fa-users text-primary"></i> Customers
 <img class="img-responsive mobileShow" src="WIMedia/Img/icons/admin_sidebar/Customers.png">
  <span class="mobileHide">Customers</span></a>
  </li>
  </div>
  <h3>Products</h3>
  <div>
   <li>
  <a href="products.php">
  <i class="ni ni-bullet-list-67 text-primary"></i>Products
 <img class="img-responsive mobileShow" src="WIMedia/Img/icons/admin_sidebar/Products.png">
  <span class="mobileHide">Products</span></a>
  </li>
  </div>
  <h3>Orders</h3>
  <div>
   <li>
  <a href="orders.php">
              <i class="ni ni-cart text-primary"></i> Orders
 <img class="img-responsive mobileShow" src="WIMedia/Img/icons/admin_sidebar/Orders.png">
  <span class="mobileHide">Orders</span></a>
  </li>
  </div>
  <h3>Payments</h3>
  <div>
   <li>
  <a href="payments.php">
   <i class="ni ni-credit-card text-primary"></i> Payments
 <img class="img-responsive mobileShow" src="WIMedia/Img/icons/admin_sidebar/Payments.png">
  <span class="mobileHide">Payments</span></a>
  </li>
  </div>
  <h3>Receipts</h3>
  <div>
   <li>
  <a href="receipts.php">
   <i class="fas fa-file-invoice-dollar text-primary"></i> Receipts
 <img class="img-responsive mobileShow" src="WIMedia/Img/icons/admin_sidebar/Receipts.png">
  <span class="mobileHide">Receipts</span></a>
  </li>
  </div>
</div>';

    }

    public function toggleButton($id,$cid, $value, $class, $data)
    {
        echo '<div class="swt_tgl">
        <input type="hidden" id="' . $id . '" value="' . $value . '" />
          <input type="checkbox" class="swt_inp ' . $class . '" id="' . $cid . '" data-id="' . $data . '" />
          <span class="swt_crl"></span>
        </div><script>
        $(".swt_inp").click(function() {
  var mainParent = $(this).parent(".swt_tgl");
  if($(mainParent).find("input.swt_inp").is(":checked")) {
    $(mainParent).addClass("swt_act");
    
  } else {
    $(mainParent).removeClass("swt_act");
  }

})
</script>';
    }

    public function toggleButtonLabel($title,$id,$cid, $value)
    {
        echo '<label class="labelling">' . $title . '</label>
        <div class="swt_tgl">
        <input type="hidden" id="' . $id . '" value="' . $value . '" />
          <input type="checkbox" class="swt_inp" id="' . $cid . '" />
          <span class="swt_crl"></span>
        </div><script>
        $(".swt_inp").click(function() {
  var mainParent = $(this).parent(".swt_tgl");
  if($(mainParent).find("input.swt_inp").is(":checked")) {
    $(mainParent).addClass("swt_act");
    
  } else {
    $(mainParent).removeClass("swt_act");
  }

})
</script>';
    }

        public function advancedToggleButton()
    {
        echo '<label class="switch switch-left-right">
        <input class="switch-input" type="checkbox">
        <span class="switch-label" data-on="Yes" data-off="No"></span> 
        <span class="switch-handle"></span> </label>';
    }


}



?>
