<?php
#[\AllowDynamicProperties]
/**
* 
*/
class 404_annomated 
{
	

	function __construct()
	{
		$this->WIdb = WIdb::getInstance();
		$this->Web  = new WIWebsite();
		$this->site = new WISite();
		$this->mod  = new WIModules();
		$this->page = new WIPage();
	}

		public function Install($mod_name)
  {
    $author = "Jules Warner";
    $type = "module";
    $font = "wi_" . $mod_name;
    $power = "power_on";

    $module = '<div class="preview">404</div>
                    <div class="view">
        <div class="col-lg-8 col-md-8 col-sm-8 about">						
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<style>
	.four-zero-four h1{
		font-size: 500px;
		color: #FF6666;
		text-align: center;
	}
	.four-zero-four h2{
		font-size: 500px;
		color: #FFD845;text-align: center;
	}
	.four-zero-four h3{
		font-size: 500px;
		color: #6F77F4;text-align: center;
	}
	.four-zero-four {
		padding-bottom: 120px;
	}
 @media only screen and (max-width: 1200px) {
 	.four-zero-four h1{
		font-size: 120px;
		color: #FF6666;
		text-align: center;
	}
	.four-zero-four h2{
		font-size: 120px;
		color: #FFD845;text-align: center;
	}
	.four-zero-four h3{
		font-size: 120px;
		color: #6F77F4;text-align: center;
	}}

.four-z-four {
    background: #fbfbfd;
}
.four-z-top {
    padding: 120px 0px 270px;
    position: relative;
    overflow-x: hidden;
}	
.four-z-top .four_z .four_z_three {
    background: url("https://studyshoot.com/wp-content/uploads/2020/04/volks.gif") no-repeat center center;
    width: 330px;
    height: 105px;
    background-size:100%;
    position: absolute;
    bottom: 0;
    left: 30%;
    -webkit-animation: myfirst 30s linear infinite;
    animation: myfirst 12s linear infinite;
}	
	
@-moz-keyframes myfirst {
  0% {
    left: -25%;
  }
  100% {
    left: 100%;
  }
}

@-webkit-keyframes myfirst {
  0% {
    left: -25%;
  }
  100% {
    left: 100%;
  }
}

@keyframes myfirst {
  0% {
    left: -25%;
  }
  100% {
    left: 100%;
  }
}
</style>
<div class="four-z-four bg_color">
    <div class="four-z-top">
        <div class="four_z">
         <div class="four_z_three">
         </div>
        </div>         
    </div>
    <div class="four-zero-four" style="text-align: center;">	
            <div class="container" >
                <div class="row"> 
                    <div class="col-4">  
                    <h1>4</h1>
                    </div>
                    <div class="col-4">
                    <h2> 0 </h2>
                    </div>
                    <div class="col-4">
                    <h3> 4 </h3>
                    </div>
                </div>
            </div>
    </div>
</div>						
						
  </div>
    </div>';
    $this->WIdb->insert('wi_modules', array(
            "module_name" => $mod_name,
            "module_author" => $author,
            "module_type" => $type,
            "module_font" => $font,
            "module_powered" => $power,
            "module"           => $module
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
		if(isset($page)){
		$left_sidePower = $this->Web->pageModPower($page, "left_sidebar");
		$leftSideBar = $this->Web->PageMod($page, "left_sidebar");
		//echo $Panel;
		if ($left_sidePower === 0) {
			
		}else{

			$this->mod->getMod($leftSideBar);
		}
		}

		echo '<div class="container-fluid text-center">    
  <div class="row content">
   

<div class="col-lg-8 col-md-8 col-sm-8 about">						
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<style>
	.four-zero-four h1{
		font-size: 500px;
		color: #FF6666;
		text-align: center;
	}
	.four-zero-four h2{
		font-size: 500px;
		color: #FFD845;text-align: center;
	}
	.four-zero-four h3{
		font-size: 500px;
		color: #6F77F4;text-align: center;
	}
	.four-zero-four {
		padding-bottom: 120px;
	}
 @media only screen and (max-width: 1200px) {
 	.four-zero-four h1{
		font-size: 120px;
		color: #FF6666;
		text-align: center;
	}
	.four-zero-four h2{
		font-size: 120px;
		color: #FFD845;text-align: center;
	}
	.four-zero-four h3{
		font-size: 120px;
		color: #6F77F4;text-align: center;
	}}

.four-z-four {
    background: #fbfbfd;
}
.four-z-top {
    padding: 120px 0px 270px;
    position: relative;
    overflow-x: hidden;
}	
.four-z-top .four_z .four_z_three {
    background: url("https://studyshoot.com/wp-content/uploads/2020/04/volks.gif") no-repeat center center;
    width: 330px;
    height: 105px;
    background-size:100%;
    position: absolute;
    bottom: 0;
    left: 30%;
    -webkit-animation: myfirst 30s linear infinite;
    animation: myfirst 12s linear infinite;
}	
	
@-moz-keyframes myfirst {
  0% {
    left: -25%;
  }
  100% {
    left: 100%;
  }
}

@-webkit-keyframes myfirst {
  0% {
    left: -25%;
  }
  100% {
    left: 100%;
  }
}

@keyframes myfirst {
  0% {
    left: -25%;
  }
  100% {
    left: 100%;
  }
}
</style>
<div class="four-z-four bg_color">
    <div class="four-z-top">
        <div class="four_z">
         <div class="four_z_three">
         </div>
        </div>         
    </div>
    <div class="four-zero-four" style="text-align: center;">	
            <div class="container" >
                <div class="row"> 
                    <div class="col-4">  
                    <h1>4</h1>
                    </div>
                    <div class="col-4">
                    <h2> 0 </h2>
                    </div>
                    <div class="col-4">
                    <h3> 4 </h3>
                    </div>
                </div>
            </div>
    </div>
</div>						
						
  </div>
    
				</div>
			</div>';

		if(isset($page)){			
		$right_sidePower = $this->Web->pageModPower($page, "right_sidebar");
		$rightSideBar = $this->Web->PageMod($page, "right_sidebar");
		//echo $Panel;
		if ($right_sidePower === 0) {
			
		}else{

			$this->mod->getMod($rightSideBar);
		}

		}			
					

	echo '</div>
			</div>';
	}


}