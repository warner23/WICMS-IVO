<?php
#[\AllowDynamicProperties]
/**
* 
*/
class delivery_checklist
{
    function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Web  = new WIWebsite();
        $this->site = new WISite();
        $this->mod  = new WIModules();
        $this->page = new WIPage();
        $this->Boot = new WIBootstrap();
        $this->Comp = new WICompliance();
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
       echo '<div class="intro_box">
<h1>' .WILang::get("delivery_checklist") . '</h1>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#accordion" ).accordion({
      heightStyle: "content"
    });
  } );
  </script>
     <div class="well">';
       $this->Comp->DeliveryChecks();
       echo '</div></div></div></div></div>
    </div><script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WICompliance.js"></script>
    <script type="text/javascript" src="WICore/WIJ/deliveryChecks.js"></script>';
       $this->Boot->endContentsHolder();  
       $this->Boot->endMod($page);
    }  
}