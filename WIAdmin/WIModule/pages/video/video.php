<?php
#[\AllowDynamicProperties]
/**
* 
*/
class video
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
        if(isset($page)){
        $left_sidePower = $this->Web->pageModPower($page, "left_sidebar");
        $leftSideBar = $this->Web->PageMod($page, "left_sidebar");
        if ($left_sidePower >0) {

            $this->mod->getMod($leftSideBar, $page);
        }
        }

        echo '<style>
         .animated {
        -webkit-transition: height 0.2s;
        -moz-transition: height 0.2s;
        transition: height 0.2s;
        }

        .stars
        {
            margin: 20px 0;
            font-size: 24px;
            color: #d17581;
        }

        
        </style>
        <div class="container-fluid text-center bg-index">    
  <div class="row">

    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" id="course_item">
       
    
    </div>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
 <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

  <script>
  $( function() {
    $( "#tabs" ).tabs();
  } );
  </script>
<div class="col-xs-12 col-lg-12 col-sm-12 col-md-12">
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">Rating</a></li>
    <li><a href="#tabs-2">Reviews</a></li>
  </ul>
  <div id="tabs-1">
    <div id="description"></div>  
    </div>
  <div id="tabs-2">
  <div id="reviews"></div> 
  </div>

</div>
</div>
            
          </div>
        </div>

              <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
             <script type="text/javascript" src="WICore/WIJ/WIVideo.js"></script>
              <script src="WICore/WIJ/WICart.js"></script>
              <script src="WICore/WIJ/WIReview.js"></script>
              <script src="WICore/WIJ/WIComment.js"></script>';

      if(isset($page)){         
        $right_sidePower = $this->Web->pageModPower($page, "right_sidebar");
        $rightSideBar = $this->Web->PageMod($page, "right_sidebar");
        //echo $Panel;
        if ($right_sidePower > 0) {

            $this->mod->getMod($rightSideBar, $page);
        }

        }           
                    

    echo "</div>
            </div></div>";
    }  
}