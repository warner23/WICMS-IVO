<?php

/**
* 
*/
class west
{
    function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->Web  = new WIWebsite();
        $this->site = new WISite();
        $this->mod  = new WIModules();
        $this->page = new WIPage();
        $this->Boot = new WIBootStrap();
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
        
     echo 
            <div class="container-fluid">
            <div class="row-fluid">

              
              
              
            
              
              <div class="col-lg-12 col-md-12 col-sm-12">
            <div class="col-lg-12 col-md-12 col-sm-12 column">
              <div class="box ui-draggable col-lg-12 col-md-12 col-sm-12" style="display: block; ">
              
              
            

                    
                      <div class="col-lg-12 col-md-12 col-sm-12">
              <div class="intro_box hero-unit" contenteditable="true">
              <h1><span>Hello, world!</span></h1>
              <p>This is a template for a simple marketing or informational website.
                          It includes a large callout called the hero unit and three supporting pieces of content.
                          Use it as a starting point to create something more unique.</p>
              </div>
            </div>
          </div>
          <div class="box ui-draggable col-lg-12 col-md-12 col-sm-12" style="position: relative; left: 0px; top: 0px; width: 100%; display: block; height: auto; opacity: 1;">
                     
                    
                    
                    <div class="col-lg-12 col-md-12 col-sm-12">
                      <button class="btn" type="button" contenteditable="true">Button</button>
                    </div>
                  </div></div>
        </div>
        
      





              
            </div>
          </div>
          

     $this->Boot->endContentsHolder();           
    
    $this->Boot->endMod($page);
    }  
}