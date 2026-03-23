<?php
#[\AllowDynamicProperties]
/**
* 
*/
class about_me
{
    function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Web  = new WIWebsite();
        $this->site = new WISite();
        $this->mod  = new WIModules();
        $this->page = new WIPage();
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
       echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-index" style="height: 670px;">';
        if(isset($page)){
        $left_sidePower = $this->Web->pageModPower($page, "left_sidebar");
        $right_sidePower = $this->Web->pageModPower($page, "right_sidebar");
        $leftSideBar = $this->Web->PageMod($page, "left_sidebar");
        if ($left_sidePower > 0 && $right_sidePower > 0 ) {
        $this->mod->getMod($leftSideBar);
        echo '<div class="col-lg-8 col-md-8 col-sm-8">';
        }else if ($left_sidePower > 0){
            $this->mod->getMod($leftSideBar);
            echo '<div class="col-lg-10 col-md-10 col-sm-10">';
        }else if ($right_sidePower > 0){
            echo '<div class="col-lg-10 col-md-10 col-sm-10">';
        }else{
            echo '<div class="col-lg-12 col-md-12 col-sm-12">';
        }
        }
    
    echo '<style>
    .text-center{
          text-align: center;  
    }

    .footer{
     margin-top: 48%;
    }

    .social_media {
    float: none;
    list-style-type: none;
    margin: 28% 0% 4% 43%;
    /* padding: 0; */
    /* margin-right: 0; */
}
    </style>
    <div class="jumbotron text-center">
  <h1>About Me</h1>
   </div>


  <div class="col-lg-12 col-md-12 col-sm-12 text-center bg-index"> 
    I began training in martial arts in 1993 and teaching in 2000.
    I\'m a vegan martial arts coach with a strong background in striking martial arts, and self defence.
    </div>';
    $this->Web->Social();
    echo '</div>
    <!-- End Contact Page -->
    <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIContacts.js"></script>
    </div>';


        
        


      if(isset($page)){     
    $right_sidePower = $this->Web->pageModPower($page, "right_sidebar");
    $rightSideBar = $this->Web->PageMod($page, "right_sidebar");
    //echo $Panel;
    if ($right_sidePower > 0) {
      $this->mod->getMod($rightSideBar);
    } 
    }        
                    

    echo "</div>";
    }  
}