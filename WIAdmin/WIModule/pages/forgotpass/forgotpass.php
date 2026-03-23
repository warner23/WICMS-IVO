<?php
#[\AllowDynamicProperties]
/**
* 
*/
class forgotpass
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

    public function mod_name($page)
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
    <div class="col-lg-12 col-md-12 col-sm-12" >
     <div class="tab-pane in" id="forgot">
                        <form class="form-horizontal" id="forgot-pass-form">
                        <fieldset>
                          <div id="legend">
                            <legend style="color:white !important;">' . WILang::get("forgot_password") . '</legend>
                          </div>    
                          <div class="control-group form-group">
                            <!-- Username -->
                            <label style="color:white !important;" class="control-label col-lg-4"  for="forgot-password-email">' . WILang::get("your_email") . '</label>
                            <div class="controls col-lg-8">
                              <input type="email" id="forgot-password-email" class="input-xlarge form-control">
                            </div>
                          </div>

                          <div class="control-group form-group">
                            <!-- Button -->
                            <div class="controls col-lg-offset-4 col-lg-8">
                              <button id="btn-forgot-password" class="btn btn-success">' . WILang::get("reset_password") . '</button>
                            </div>
                          </div>
                        </fieldset>
                      </form>
                        
                  </div>
                  <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
                  <script type="text/javascript" src="WICore/WIJ/WIPasswordReset.js"></script>
                  ';
        

      if(isset($page)){         
        $right_sidePower = $this->Web->pageModPower($page, "right_sidebar");
        $rightSideBar = $this->Web->PageMod($page, "right_sidebar");
        //echo $Panel;
        if ($right_sidePower>0) {

            $this->mod->getMod($rightSideBar);
        }

        }           
                    

    echo "</div>
            </div></div></div></div></div></div>";
    }  
}