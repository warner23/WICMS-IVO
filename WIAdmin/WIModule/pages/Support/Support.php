<?php
#[\AllowDynamicProperties]
/**
* 
*/
class support
{
    function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Web  = new WIWebsite();
        $this->site = new WISite();
        $this->mod  = new WIModules();
        $this->page = new WIPage();
        $this->support = new WISupport();
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

    public function mod_name($module, $page)
    {
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-index">';
        if(isset($page)){
        $left_sidePower = $this->Web->pageModPower($page, "left_sidebar");
        $leftSideBar = $this->Web->PageMod($page, "left_sidebar");
         if ($left_sidePower > 0) {
      $this->mod->getMod($leftSideBar);
      echo '<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">';
    }else{
      echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">';
    }

    }

        echo '<div class="container-fluid text-center bg-index">    
    <div class="row content">

    <div class="col-lg-12 col-md-12 col-sm-12" >';
    $this->support->support();
     echo '</div>
     <script type="text/javascript" src="WICore/WIJ/WISupport.js"></script>';
        

      if(isset($page)){         
        $right_sidePower = $this->Web->pageModPower($page, "right_sidebar");
        $rightSideBar = $this->Web->PageMod($page, "right_sidebar");
        //echo $Panel;
        if ($right_sidePower>0) {

            $this->mod->getMod($rightSideBar);
        }

        }           
                    

    echo "</div>
            </div></div>";
    }  
}