<?php
#[\AllowDynamicProperties]
/**
* 
*/
class food
{
    function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Web  = new WIWebsite();
        $this->site = new WISite();
        $this->mod  = new WIModules();
        $this->page = new WIPage();
        $this->Boot = new WIBootStrap();
        $this->Food = new WIFood();
    }

    public function editMod()
    {
        
    $this->Web->EditModTemp();
     $result = $this->WIdb->select("SELECT `edit_page_mod` FROM `wi_pages` WHERE `page_name` =:page", array("page" => $page,));
        if(count($result) < 1)
        {
            echo "No Page Found.";
        }
        else{
           echo $result[0]["edit_page_mod"];
        }
 
    }

    public function editPageContent($page)
    {
      $result = $this->WIdb->select("SELECT `edit_page_mod` FROM `wi_pages` WHERE `page_name` =:page", array("page" => $page,));
        if(count($result) < 1)
        {
            echo "No Page Found.";
        }
        else{
           echo $result[0]["edit_page_mod"];
        }

    }

    public function mod_name($page)
    {
       $this->Boot->startMod($page);
       $this->Boot->startContentsHolder();

       echo '<!-- FOOD: JUMBOTRON -->
      <div class="jumbotron food-jumbotron dark-overlay text-white">
        <img
          
          src="../WIAdmin/WIMedia/Img/menu/cover-food-menu.jpg"
          alt=""
          class="food-jumbotron-bg">
        <div class="food-jumbotron-caption container">
          <h1 id="title-1" class="special-title-2">Food Menu</h1>';
       $this->Food->FoodSectiona();
       echo '</div></div>';

       $this->Food->allergiens();

      echo '<!-- FOOD: DESCRIPTIONS -->
      <div class="food-descriptions container">';
        
       $this->Food->food();

      echo '</div>
      <script type="text/javascript" src="WICore/WIJ/WIShop.js"></script>
              <script type="text/javascript" src="WICore/WIJ/WIProducts.js"></script>
              <script src="WICore/WIJ/WICart.js"></script>
              <script src="WICore/WIJ/WIReview.js"></script>
      ';

       $this->Boot->endContentsHolder();  
       $this->Boot->endMod($page);
    }  
}