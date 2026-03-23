
<?php
#[\AllowDynamicProperties]
/**
* 
*/
class appointment
{
    function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Web  = new WIWebsite();
        $this->site = new WISite();
        $this->mod  = new WIModules();
        $this->page = new WIPage();
        $this->calendar = new WICalendar();
        $this->Modal    = new WIModal();
        $this->Book     = new WIBooker();
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
    
    echo '<div class="title_content">             
 <h3 style="color:black;">Class Booker</h3>           
  </div>   
<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">    

         <!-- Calendar -->
    <div class="box box-warning">
        <div class="box-header">Class Type :';
                $this->Book->classType();
                echo '<div>

            </div>       
    </div><!-- /.box-header -->
            <div class="box-body no-padding">
                <!--The calendar -->
                <div id="calendar">';
                $this->calendar->getCalendar();
                echo '</div>
            </div><!-- /.box-body -->
        </div><!-- /.box -->
    </div>
    ';
       // $this->Book->bookerForm();
        echo '
    <!-- End appointment Page -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIAppointment.js"></script>
    </div>';

     $this->Modal->moduleModal('training', 'Training', 'WIAppointment', 'training','next','train');

     $this->Modal->moduleModal('booker', 'Training Booker', 'WIAppointment', 'booker','continue','bookslots');
     $this->Modal->moduleModal('person', 'Training Details', 'WIAppointment', 'details','continue','payperson');
     $this->Modal->moduleModal('paypal', 'paypal Details', 'WIAppointment', 'payment','','');


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