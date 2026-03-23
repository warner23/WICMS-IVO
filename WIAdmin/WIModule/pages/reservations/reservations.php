<?php
#[\AllowDynamicProperties]
/**
* 
*/
class reservations
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

     echo ' <!-- RESERVATIONS DESCRIPTION -->
      <div class="reservations-description container">
        <h1 id="title-1" class="special-title-1">RESERVATIONS</h1>
        <h2 id="title-2" class="special-title-2">Book a Table</h2>
        <p id="description" class="mb-0">
          Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean et 
          consequat augue. Morbi condimentum interdum magna sit amet pulvinar. 
          Vestibulum in dolor egestas, vestibulum quam sit amet, feugiat neque. 
        </p>
      </div>

      <!-- RESERVATIONS FORM -->
      <div class="container-fluid reservations-form dark-overlay">
        <img src="WIAdmin/WIMedia/Img/contents/reservations.jpg"
          sizes="(max-width: 2560px) 100vw, 2560px"
          
          alt=""
          class="reservations-form-bg">
        <form>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputName">NAME:</label>
              <input type="text" class="form-control" id="inputName" placeholder="John Smith">
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail">EMAIL:</label>
              <input type="email" class="form-control" id="inputEmail" placeholder="name@example.com">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-5">
              <label for="inputDate">DATE:</label>
              <input type="date" class="form-control" id="inputDate">
            </div>
            <div class="form-group col-md-2">
              <label for="inputNumber">PARTY OF:</label>
              <input type="number" min="1" class="form-control" id="inputNumber" placeholder="1">
            </div>
            <div class="form-group col-md-5">
              <label for="inputPhone">PHONE:</label>
              <input type="tel" class="form-control" id="inputPhone" placeholder="0151 223 6789">
            </div>
          </div>
          <div class="form-group">
            <label for="inputTextarea">MESSAGE:</label>
            <textarea class="form-control" id="inputTextarea" rows="7" placeholder="Additional details"></textarea>
          </div>
          <button type="submit" class="btn btn-light">SUBMIT</button>
        </form>
      </div>

      <!-- GOOGLE MAP -->
      <div id="googleMapContainer" class="homepage-google-map">
        <iframe title="Our location on Google My Maps" class="google-map" src="https://www.google.com/maps/d/embed?mid=1YihUagJV98aTSoPRpalSyqRjTVhaFv5E&hl=en"></iframe>
      </div>';

     $this->Boot->endContentsHolder();  
     $this->Boot->endMod($page);
    }  
}