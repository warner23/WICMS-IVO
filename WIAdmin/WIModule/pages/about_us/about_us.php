<?php
#[\AllowDynamicProperties]
/**
* 
*/
class about_us
{
    function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Web  = new WIWebsite();
        $this->site = new WISite();
        $this->mod  = new WIModules();
        $this->page = new WIPage();
        $this->Train = new WITraining();
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

       echo '<style>
    .text-center{
          text-align: center;  
    }

    .evade{
          font-size: 20px;
    padding: 10%;
    }

    .href{
    width: 7%;
    color: white;
    background-color: silver;
    border: 4px solid yellow;
    padding: 2%; 
    }

    .radiusImage{
           width: 108%;
    height: 199px;
    border-radius: 50%;
    border: 3px solid yellow; 
    }

    .met{
      background-color: black;  
    }

    </style>
    <div class="container-fluid text-center">    
  <div class="row content">

  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
  
    <div class="jumbotron text-center styleswitcher">
<div><h2 style="color: #b2b0b0;">All About</h2></div>
        <div><h1>MARTIAL ARTS</h1></div>
   </div>


  <div class="col-lg-12 col-md-12 col-sm-12 text-center">';

    $this->Train->martialArts();
    echo '<div class="col-lg-6 col-md-6 col-sm-6 text-center"> 
        <div ><p class="evade">
        Here at Evade we believe that training in Martial Arts has the power to change lives for the better.  As such we run an inclusive club under the banner “Martial Arts for ALL !”  No matter whether you are looking to achieve fitness related goals, increase self-confidence, learn self-defence, further your sporting ability or simply just have FUN, you will find a suitable class here!</p></div><div>
        <a href="register.php" class="href"> Sign up </a>
        </div>
        </div>
<div class="col-lg-6 col-md-6 col-sm-6 text-center"> 
        <img class="border" src="WIAdmin/WIMedia/Img/contents/aama.jpg">
    </div>
          
  <div class="col-lg-12 col-md-12 col-sm-12 met" data-effect="slide-top">                       
  <div class="title">                           
  <h2 style="color: white;">Meet our amazing team</h2> 
  </div>                                                             
  <div class="row">                 
  <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 meet_team" data-effect="slide-bottom">                        
  <ul class="team_slides">';
    $results = $this->WIdb->select('SELECT * FROM `wi_meet_team`');

    if(count($results) > 0){

        foreach ($results as $res) {
    echo '<li class="team_player">                                
              <div class="team list_team" data-effect="slide-bottom">                                   
              <div class="content_team" id="content_team">                                        
              <img alt="" src="WIAdmin/WIMedia/Img/team/'. $res['photo'].'" class="radiusImage">                                        
              <div class="photo_hover">                                         
              <ul style="width: 48%;height: 25px;margin-left: 36%;">';
              if($res['fb'] == ""){

              } else{
                echo '<li style="width: 18%;float: left;"><a class="facebook" href="'. $res['fb'].'" data-toggle="tooltip" title="Facebook" data-placement="right"><i class="fa fa-facebook"></i></a></li>';
              }  

              if($res['tw'] == ""){

              }else{
                echo ' <li style="width: 18%;float: left;"><a class="twitter" href="'. $res['tw'].'"  data-toggle="tooltip" title="Twitter" data-placement="right"><i class="fa fa-twitter"></i></a></li> ';
              }                                     
              
              if($res['linked'] == ""){  

              }else{
                echo '<li style="width: 18%;float: left;"><a class="linkedin" href="'. $res['linked'].'" data-toggle="tooltip" title="Linkedin" data-placement="right"><i class="fa fa-linkedin"></i></a></li>';
              }
                                              
              echo '</ul>                                 
              </div>                                    
              </div>                                    
              <div class="team_detail">                                     
              <h5 style="color: white;">'. $res['job_title'].'</h5>                                       
              <span style="color: white;" class="jobs">Resources Co-ordinator (volunteer)</span>                                      
              <p style="color: white;">'. $res['about_me'].'.
            </p>                                    
              </div>                                
              </div>                            
              </li> ';
        }
    }else{
        echo 'There are no team members to display';
    }
    echo '</div>

    </div>

    <!-- End Contact Page -->
    <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIContacts.js"></script>
    </div>
            </div></div></div></div></div>';
        

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