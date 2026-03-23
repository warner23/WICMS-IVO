<?php
#[\AllowDynamicProperties]
/**
* 
*/
class section
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
        echo '<div class="col-lg-8 col-md-8 col-sm-8">';
        }else if ($right_sidePower > 0){
            echo '<div class="col-lg-8 col-md-8 col-sm-8">';
        }else{
            echo '<div class="col-lg-12 col-md-12 col-sm-12">';
        }
        }


        echo '<div class="container-fluid text-center">    
    <div class="row content">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
              <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
              <link rel="stylesheet" href="/resources/demos/style.css">
              <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
              <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
                        
                <div class="col-lg-12 col-xs-12 col-xl-12 col-md-12">';
                                  echo $this->WIForum->ForumMenu();
                        echo '</div>
                        <div class="col-forum-main">
                        
                        <div class="modal-body">
                          <div class="well" id="section">';
                          echo $this->WIForum->forum();
                        echo '</div>
                    </div>
                    </div>
                    </div>
                    </div>
                    <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
                    <script type="text/javascript" src="WICore/WIJ/WIWYSIWYG.js"></script>
                    <script type="text/javascript" src="WICore/WIJ/WISection.js"></script>';
    $this->modal->moduleModal('new-cat', 'Add new categor', 'WIForum', 'ForumCategory','create category'); 

    $this->modal->moduleModal('new-section', 'Add new section', 'WIForum', 'ForumSection','create section'); 

    $this->modal->moduleModal('edit-cat', 'Edit category', 'WIForum', 'ForumEditCategory','edit category'); 

    $this->modal->moduleModal('edit-section', 'Edit Section', 'WIForum', 'ForumEditSection','edit section'); 

    //$this->modal->moduleModal('delete-cat', 'Delete Section', 'WIForum', 'DeleteCategory','delete category'); 

        echo '</div>
            </div>';
      if(isset($page)){         
       // echo "page".$page;
        $rightSideBar = $this->Web->PageMod($page, "right_sidebar");
       // echo "rightsidebar". $rightSideBar;
        if ($right_sidePower > 0) {
            $this->mod->getMod($rightSideBar);
        }

        }  
                    

    echo '</div>';
    }  
}