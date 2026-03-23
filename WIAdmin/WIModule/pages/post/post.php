<?php
#[\AllowDynamicProperties]
/**
* 
*/
class post
{
    function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Web  = new WIWebsite();
        $this->site = new WISite();
        $this->mod  = new WIModules();
        $this->page = new WIPage();
        $this->WIForum = new WIForum();
        $this->modal   = new WIModal();
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
        echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-index" style="height: 670px;">';
		if(isset($page)){
		$left_sidePower = $this->Web->pageModPower($page, "left_sidebar");
		$right_sidePower = $this->Web->pageModPower($page, "right_sidebar");
		$leftSideBar = $this->Web->PageMod($page, "left_sidebar");
		if ($left_sidePower > 0) {
        $this->mod->getMod($leftSideBar);
        echo '<div class="col-lg-10 col-md-10 col-sm-10">';
		}else if ($right_sidePower > 0){
			echo '<div class="col-lg-10 col-md-10 col-sm-10">';
		}else{
			echo '<div class="col-lg-12 col-md-12 col-sm-12">';
		}
		}

        echo '<div class="container-fluid text-center">    
    <div class="row content">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
				<div class="col-lg-12 col-xs-12 col-xl-12 col-md-12">';
						          echo $this->WIForum->ForumMenu();
						echo '</div>
						<div class="col-forum-main">
						
						<div class="modal-body">
                          <div class="well" id="post">';
						  echo $this->WIForum->forum();
						echo '</div>
					</div>
					</div>
					</div>
					</div>
					<script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
					<script type="text/javascript" src="WICore/WIJ/WIWYSIWYG.js"></script>
					<script type="text/javascript" src="WICore/WIJ/WIPost.js"></script>
                    <script type="text/javascript" src="WICore/WIJ/WIForum.js"></script>';
        

      if(isset($page)){         
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