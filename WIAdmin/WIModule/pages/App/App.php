<?php
#[\AllowDynamicProperties]
/**
* 
*/
class App 
{
	

	function __construct()
	{
		$this->WIdb = WIdb::getInstance();
		$this->Web  = new WIWebsite();
		$this->site = new WISite();
		$this->mod  = new WIModules();
		$this->page = new WIPage();
	}

   public function Install($element_name)
  {
    $author = "Jules Warner";
    $type = "module";
    $font = "wi_" . $element_name;
    $power = "power_on";
    $this->WIdb->insert('wi_modules', array(
            "module_name" => $element_name,
            "module_author" => $author,
            "module_type" => $type,
            "module_font" => $font,
            "module_powered" => $power
        )); 
  }

		public function editMod()
	{
		echo '<div id="remove">
      <a href="#">
      <button id="delete" onclick="WIMod.delete(event);">Delete</button>
      </a>
       <div id="dialog-confirm" title="Remove Module?" class="hide">
  <p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;">
  </span>Are you sure?</p>
  <p> This will remove the module and any unsaved data.</p>
  <span><button class="btn btn-danger" onclick="WIMod.remove(event);">Remove</button> <button class="btn" onclick="WIMod.close(event);">Close</button></span>
</div>
';

echo '<div class="container-fluid text-center">    
  <div class="row content">
    <div class="col-sm-2 col-lg-2 col-md-2 col-xs-2 sidenav">

    </div>
	<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8 front">

<a href="topics.php"><button class="btn btn-main"><h3>' . WILang::get('current_topics') . '</h3></button></a>
					</div>
					

					    <div class="col-sm-2 col-lg-2 col-md-2 col-xs-2 sidenav">

    </div>
    
				</div>
			</div>';


		echo '</div>';
	}

	public function editPageContent($page_id)
	{
		// include_once '../../WIInc/WI_StartUp.php';
		 echo '<style type="text/css">
	
		.content {
		    padding: 32px 0;
		    position: relative;
		    margin-top: 58px;
		}

		.text-left{
			text-align: center;
		}

		</style>

		<div class="container-fluid text-center" id="col"> ';  

		  $lsc = $this->page->GetColums($page_id, "left_sidebar");
		  $rsc = $this->page->GetColums($page_id, "right_sidebar");
		if ($lsc > 0) {

			  echo '<div class="col-sm-1 col-lg-2 col-md-2 col-xl-2 col-xs-2 sidenav" id="sidenavL">';
		 $this->mod->getMod("left_sidebar");  

		    echo '</div>
		    <div class=" col-lg-10 col-md-8 col-sm-8 block" id="block">
		    <div class="col-lg-10 col-md-8 col-sm-8" id="Mid">';
		}

		if ($lsc && $rsc > 0) {
			echo '<div class="col-lg-10 col-md-8 col-sm-8 block" id="block"><div class="col-lg-12 col-md-8 col-sm-8" id="Mid">';
		}else if($rsc > 0){
			echo '<div class="col-lg-10 col-md-8 col-sm-8 block" id="block"><div class="col-lg-12 col-md-8 col-sm-8" id="Mid">';

		 }else{
		echo '<div class="col-lg-12 col-md-12 col-sm-12 block" id="block"><div class="col-lg-12 col-md-12 col-sm-12" id="Mid">';
		}

			echo '<div class="col-lg-12 col-md-12 col-sm-12" >

						<div class="title_content">							
<h3>About Us</h3>						
</div>						
<div class="our_history">							
<p>  
This is the internet privacy policy for debatespot.net
This website is the property of debatespot. We take the privacy of all visitors to this Website very seriously and therefore set out in this privacy and cookies policy our position regarding certain privacy matters and the use of cookies on this Website.
This policy covers all data that is shared by a visitor with us whether directly via [add website url] or via email. This policy been created by the internet marketing experts at Surge Digital on our behalf, and is occasionally updated by us so we suggest you re-review from time to time.
This policy provides an explanation as to what happens to any personal data that you share with us, or that we collect from you either directly via this Website or via email.
Certain businesses are required under the data protection act to have a data controller. For the purpose of the Data Protection Act 1998 our data controller is [add individual’s name] and can be contacted via email at [add email address].

</p>	
					
  </div>	
			</div>';
							

		  
		if ($rsc > 0) {

			  echo '</div><div class="col-sm-1 col-lg-2 cool-md-2 col-xl-2 col-xs-2 sidenav" id="sidenavR">';
		  $this->mod->getMod("right_sidebar");  

		    echo '</div></div>';
		}

		echo '</div>
			</div>';
 

	}

	public function mod_name()
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

		echo '<div class="container-fluid text-center">    
  <div class="row content">
   
    </div>
<div class="col-lg-12 col-md-12 col-sm-12">						
					
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" rel="stylesheet">
<style>
body {
    font-family: "Poppins";font-size: 14px;
}
img {height: auto;max-width: 100%;}
.text-voilet {color :#6a2de3 ;}
.home-area {
    height: 800px;
    z-index: 1;
}
.bg-voilet {
    background: #6a2de3;
}
.home-thumb {
    max-width: 300px;
    padding-top: 70px;
}
.bg-overlay, .overlay-dark {
    position: relative;
    z-index: 0;
}

* {
    margin: 0;
    padding: 0;
}
.fw-3 {
    font-weight: 300;
}
.home-area .shape-bottom {
    z-index: -1;
}
.shape-bottom {
    position: absolute;
    top: auto;
    bottom: -7px;
    left: 0;
    right: 0;
}
.button-group {
    margin-top: 30px;
}
.store-buttons img {
    max-width: 190px;
}

.store-buttons a {
    text-align: left;
}
.button-group a {
    margin-right: 10px;
}

</style>


<section id="home" class="section bg-voilet bg-overlay overflow-hidden d-flex align-items-center">
            <div class="container">
                <div class="row align-items-center">
                    <!-- home Intro Start -->
                    <div class="col-12 col-md-7 col-lg-6">
                        <div class="home-intro">
                            <h1 class="text-white">Broca The Speech Therapy App</h1>
                            <p class="text-white my-4">Broca is modern easy to use, fun and highly interactive, designed to tackle a range of types of speech therapy, available on the go, at home, work, anywhere.

                            </p>
                            <!-- Store Buttons -->
                            <div class="button-group store-buttons d-flex">
                                <a href="javascript:void(0);">
                                    <img src="WIAdmin/WIMedia/Img/contents/app/google_play.png" alt="">
                                </a>
                                <a href="#">
                                    <img src="WIAdmin/WIMedia/Img/contents/app/app_store.png" alt="">
                                </a>
                            </div>
                            <span class="d-inline-block text-white fw-3 font-italic mt-3">* Available on iPhone, iPad and all Android devices</span>
                        </div>
                    </div>
                    <div class="col-12 col-md-5 col-lg-6">
                        <!-- home Thumb -->
                        <div class="home-thumb mx-auto" >
                            <img src="WIAdmin/WIMedia/Img/contents/app/blank_app.png" alt="">
                        </div>
                    </div>
                </div>
            </div>
            <!-- Shape Bottom -->
            <div class="shape-bottom">
                <svg viewBox="0 0 1920 310" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" class="svg replaced-svg">
                    <title>Shape</title>
                    <desc>Created with WICMS</desc>
                    <defs></defs>
                    <g id="Landing-Page" stroke="none" stroke-width="12" fill="none" fill-rule="evenodd">
                        <g id="sApp-v1.0" transform="translate(0.000000, -554.000000)" fill="#ffffff">
                            <path d="M-3,551 C186.257589,757.321118 319.044414,856.322454 395.360475,848.004007 C509.834566,835.526337 561.525143,796.329212 637.731734,765.961549 C713.938325,735.593886 816.980646,681.910577 1035.72208,733.065469 C1254.46351,784.220361 1511.54925,678.92359 1539.40808,662.398665 C1567.2669,645.87374 1660.9143,591.478574 1773.19378,597.641868 C1848.04677,601.75073 1901.75645,588.357675 1934.32284,557.462704 L1934.32284,863.183395 L-3,863.183395" id="v1.0"></path>
                        </g>
                    </g>
                </svg>
            </div>
        </section>
<section id="stats" class="py-3">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-5 col-sm-3 text-center">
                        <h1 class="text-voilet"><b>1M</b></h1>
                        <div class="mb-3"></div>
                        <h5>Users</h5>
                    </div>
                    <div class="col-5 col-sm-3 text-center">
                        <h1 class="text-voilet"><b>3M</b></h1>
                        <div class="mb-3"></div>
                        <h5>Users</h5>
                    </div>
                    <div class="col-5 col-sm-3 text-center">
                        <h1 class="text-voilet"><b>3M</b></h1>
                        <div class="mb-3"></div>
                        <h5>Users</h5>
                    </div>
                    <div class="col-5 col-sm-3 text-center">
                        <h1 class="text-voilet"><b>3M</b></h1>
                        <div class="mb-3"></div>
                        <h5>Users</h5>
                    </div>
                    
                </div>
            </div>
        </section>
<section id="features" class="py-5 ">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-12 col-md-10 col-lg-7 section-heading text-center">
                <h5 class="text-voilet mb-4">Premium Features</h5>
                <h2>What Makes Broca Different?</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati dignissimos quae quo ad iste ipsum officiis deleniti asperiores sit.</p>
            </div>
            
        </div>
        
        <div class="row justify-content-center text-center">
            <div class="col-12 col-md-6 col-lg-4">
                <i class="fa fa-bicycle fa-4x text-voilet"></i>
                <h3 class="mb-2">Fully functional</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veritatis culpa expedita dignissimos.</p>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <i class="fa fa-phone fa-4x text-success"></i>
                <h3 class="mb-2">Fully functional</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veritatis culpa expedita dignissimos.</p>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <i class="fa fa-coffee fa-4x text-voilet"></i>
                <h3 class="mb-2">Fully functional</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veritatis culpa expedita dignissimos.</p>
            </div>
        </div>
    </div>
</section>
<section id="services" class=" ">
    <div class="container">
        <div class="row justify-content-between align-items-center">
            <div class="col-12 col-lg-6 ">
                 <h2 class="mb-4">Make your Device Manage Everything For you</h2>
                 <ul class="list-unstyled">
                     <li><p>Fully layered dolor sit amet, consectetur adipisicing elit. Facere, nobis, id expedita dolores officiis laboriosam.</p></li>
                     <li><p>Fully layered dolor sit amet, consectetur adipisicing elit. Facere, nobis, id expedita dolores officiis laboriosam.</p></li>
                     <li><p>Fully layered dolor sit amet, consectetur adipisicing elit. Facere, nobis, id expedita dolores officiis laboriosam.</p></li>
                     <li><p>Fully layered dolor sit amet, consectetur adipisicing elit. Facere, nobis, id expedita dolores officiis laboriosam.</p></li>
                     
                 </ul>
            </div>
            <div class="col-12 col-lg-4 order-1 order-lg-2 d-none d-lg-block">
                        <div class="home-thumb mx-auto">
                            <img src="http://theme-land.com/sApp/demo/assets/img/features/thumb-1.png" alt="">
                        </div>
            </div>
        </div>
    </div>
</section>
<section id="info" class=" py-5 ">
    <div class="container">
        <div class="row justify-content-between align-items-center">
            <div class="col-12 col-lg-4 ">
                        <div class="mx-auto">
                            <img src="http://theme-land.com/sApp/demo/assets/img/discover/iphone_x.png" alt="">
                        </div>
            </div>
            <div class="col-12 col-lg-6 ">
                 <h2 class="mb-4">Easily communicate with clients using sApp.</h2>
                 <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Similique dolor ut iusto vitae autem neque eum ipsam, impedit asperiores vel cumque laborum dicta repellendus inventore voluptatibus et explicabo nobis unde.
</p>
                 <ul>
                     <li><p>Fully layered dolor sit amet, consectetur adipisicing elit. Facere, nobis, id expedita dolores officiis laboriosam.</p></li>
                     <li><p>Fully layered dolor sit amet, consectetur adipisicing elit. Facere, nobis, id expedita dolores officiis laboriosam.</p></li>
                     <li><p>Fully layered dolor sit amet, consectetur adipisicing elit. Facere, nobis, id expedita dolores officiis laboriosam.</p></li>
                     <li><p>Fully layered dolor sit amet, consectetur adipisicing elit. Facere, nobis, id expedita dolores officiis laboriosam.</p></li>
                 </ul>
            </div>
            
        </div>
    </div>
</section>
<section id="features" class="py-5 bg-voilet text-white">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-12 col-md-10 col-lg-7 section-heading text-center">
                <h5 class="text-voilet mb-4">Premium Features</h5>
                <h2>What Makes sApp Different?</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati dignissimos quae quo ad iste ipsum officiis deleniti asperiores sit.</p>
            </div>
            
        </div>
        
        <div class="row justify-content-center text-center">
            <div class="col-12 col-md-6 col-lg-4">
                <i class="fa fa-bicycle fa-4x text-white"></i>
                <h3 class="mb-2">Fully functional</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veritatis culpa expedita dignissimos.</p>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <i class="fa fa-phone fa-4x text-white"></i>
                <h3 class="mb-2">Fully functional</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veritatis culpa expedita dignissimos.</p>
            </div>
            <div class="col-12 col-md-6 col-lg-4">
                <i class="fa fa-coffee fa-4x text-white"></i>
                <h3 class="mb-2">Fully functional</h3>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Veritatis culpa expedita dignissimos.</p>
            </div>
        </div>
    </div>
</section>
<section id="features" class="py-5">
    <div class="container">
        <div class="row justify-content-center mb-5">
            <div class="col-12 col-md-10 col-lg-7 section-heading text-center">
                <h5 class="text-voilet mb-4">Premium Features</h5>
                <h2>Frequently Asked Questions</h2>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum obcaecati dignissimos quae quo ad iste ipsum officiis deleniti asperiores sit.</p>
            </div>
            
        </div>
        <div class="row mb-5">
            <div class="col-12 col-md-6">
               <div class="faq-items">
                    <h4> How to install sApp?</h4>
                <p>The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using "Content here, content here", making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text.</p>
               </div>
               <div class="faq-items">
                    <h4> How to install sApp?</h4>
                <p>The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using "Content here, content here", making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text.</p>
               </div>
               <div class="faq-items">
                    <h4> How to install sApp?</h4>
                <p>The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using "Content here, content here", making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text.</p>
               </div>
            </div>
            <div class="col-12 col-md-6 justify-content-center">
               <div class="faq-items">
                    <h4> How to install sApp?</h4>
                <p>The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using "Content here, content here", making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text.</p>
               </div>
               <div class="faq-items">
                    <h4> How to install sApp?</h4>
                <p>The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using "Content here, content here", making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text.</p>
               </div>
               <div class="faq-items">
                    <h4> How to install sApp?</h4>
                <p>The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using "Content here, content here", making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text.</p>
               </div>
            </div>
            
        </div>
        
       
    </div>
</section>


  </div>
    
		</div>
			</div></div>';

		if(isset($page)){			
		$right_sidePower = $this->Web->pageModPower($page, "right_sidebar");
		$rightSideBar = $this->Web->PageMod($page, "right_sidebar");
		//echo $Panel;
		if ($right_sidePower === 0) {
			
		}else{

			$this->mod->getMod($rightSideBar);
		}

		}			
					

	echo '</div>';
	}


}