<?php
#[\AllowDynamicProperties]
/**
* 
*/
class login 
{
	

	function __construct()
	{
		$this->WIdb = WIdb::getInstance();
		$this->Web  = new WIWebsite();
		$this->site = new WISite();
		$this->mod  = new WIModules();
		$this->page = new WIPage();
		 $this->login = new WILogin();
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

	<div class="col-lg-12 col-md-12 col-sm-12" >
						<div class="col-lg-12 col-md-12 col-sm-12" >

						 <div class="col-lg-12 col-md-12 col-sm-12 text-left"> 
							<div class="intro_box">
<h1>' .WILang::get("welcome_") . '<span>'. $this->site->Website_Info('site_name') . '</span></h1>
							<p>' . WILang::get("main_title") . '</p>
							</div>
						</div>
					</div>
				
					<div class="col-lg-4 col-md-4 col-sm-4" >
						<div class="services">
							<div class="icon">
								<i class="fa fa-laptop"></i>
							</div>
							<div class="serv_detail">
								<h3>' . WILang::get("community") . '</h3>
								<p>' . WILang::get("learn") . '
</p>
							</div>
						</div>
					</div>
					
					<div class="col-lg-4 col-md-4 col-sm-4">
						<div class="services">
							<div class="icon">
								<i class="fa fa-trophy"></i>
							</div>
							<div class="serv_detail">
								<h3>' . WILang::get("software") . '</h3>
								<p>' .WILang::get("software") . '
</p>
							</div>
						</div>
					</div>
					
					<div class="col-lg-4 col-md-4 col-sm-4" >
						<div class="services">
							<div class="icon">
								<i class="fa fa-cogs"></i>
							</div>
							<div class="serv_detail">
								<h3>' . WILang::get("it") . '</h3>
								<p>' . WILang::get("it_title")  . '
</p>
							</div>
						</div>
					</div>
					</div>
					


    
				</div>
			</div>';


		echo '</div>';
	}

	public function editPageContent($page_id)
	{
		// include_once '../../WIInc/WI_StartUp.php';
		 echo '		 <style type="text/css">
	
		.content {
		    padding: 32px 0;
		    position: relative;
		    margin-top: 58px;
		}

		.text-left{
			text-align: center;
		}
		.edit{
			width:21%;
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

						 <div class="col-lg-12 col-md-12 col-sm-12 text-left"> 
							<input type="text" class="edit" value="' .WILang::get("welcome_") . '"><span>'. $this->site->Website_Info('site_name') . '</span></h1>
							<p><input class="edit" type="text" value="' . WILang::get("main_title") . '>"</p>
							</div>
						</div>
					</div>
				
					<div class="col-lg-4 col-md-4 col-sm-4" >
						<div class="services">
							<div class="icon">
								<i class="fa fa-laptop"></i>
							</div>
							<div class="serv_detail">
								<h3><input type="text" class="edit" value="' . WILang::get("community") . '"></h3>
								<p><input type="text" class="edit" value="' . WILang::get("learn") . '">"
</p>
							</div>
						</div>
					</div>
					
					<div class="col-lg-4 col-md-4 col-sm-4">
						<div class="services">
							<div class="icon">
								<i class="fa fa-trophy"></i>
							</div>
							<div class="serv_detail">
								<h3><input type="text" class="edit" value="' . WILang::get("software") . '"></h3>
								<p><input type="text" class="edit" value="' .WILang::get("software") . '">"
</p>
							</div>
						</div>
					</div>
					
					<div class="col-lg-4 col-md-4 col-sm-4" >
						<div class="services">
							<div class="icon">
								<i class="fa fa-cogs"></i>
							</div>
							<div class="serv_detail">
								<h3><input type="text" class="edit" value="' . WILang::get("it") . '"></h3>
								<p><input type="text" class="edit" value="' . WILang::get("it_title")  . '">"
</p>
							</div>
						</div>
					</div>
					</div>
					


    
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


	public function mod_name($page)
	{
		echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-index">';
		if(isset($page)){
		$left_sidePower = $this->Web->pageModPower($page, "left_sidebar");
		$leftSideBar = $this->Web->PageMod($page, "left_sidebar");
		//echo "side". $leftSideBar;
		if ($left_sidePower > 0) {
			$this->mod->getMod($leftSideBar,$page);
			echo '<div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">';
		}
		}

		echo '<style>

		.divider-text {
    position: relative;
    text-align: center;
    margin-top: 15px;
    margin-bottom: 15px;
}
.divider-text span {
    padding: 7px;
    font-size: 12px;
    position: relative;   
    z-index: 2;
}
.divider-text:after {
    content: "";
    position: absolute;
    width: 100%;
    border-bottom: 1px solid #ddd;
    top: 55%;
    left: 0;
    z-index: 1;
}

.btn-facebook {
    background-color: #405D9D;
    color: #fff;
}
.btn-twitter {
    background-color: #42AEEC;
    color: #fff;
}
</style><div class="container-fluid text-center">    
  <div class="row content"><div class="col-sm-2"></div><div class="col-sm-8">'; 

   if($this->login->isLoggedIn() ) {
   	header('Location: index.php');
   }else{
   	echo '<div class="card bg-light">
<article class="card-body mx-auto" style="max-width: 400px;">
	<h4 class="card-title mt-3 text-center">Login</h4>';

			 if(TWITTER_ENABLED === "true"){
        echo '<a href="WICore/WIVendor/Hybridauth/index.php?p=twitter&token=' .WISession::get("WI_social_token") .'" class="btn btn-block btn-twitter"> <i class="fa fa-twitter"></i>   Login via Twitter</a>';
       }else{
        echo "";
       }


      if(GOOGLE_ENABLED === "true"){
           echo  '<a href="WICore/WIVendor/Hybridauth/index.php?p=google&token=' .WISession::get("WI_social_token") .'" class="btn btn-block btn-googleplus"> <i class="fa fa-googleplus"></i>   Login via Google</a>';
       }else{

       }
	if(FACEBOOK_ENABLED === "true"){
          echo '<a href="WICore/WIVendor/Hybridauth/index.php?p=facebook&token=' .WISession::get("WI_social_token") .'" class="btn btn-block btn-facebook"> <i class="fa fa-facebook-f"></i>   Login via facebook</a>';
       }else{
        echo '';
       }
		echo '</p>
	<p class="divider-text">
        <span class="bg-light">OR</span>
    </p>
	<form class="form-horizontal login-form">
		<fieldset>
           <div class="control-group form-group">
                        <!-- Username -->
                        <label class="control-label col-xs-2 col-md-2 col-lg-2 col-sm-2" title="Username" for="login-username"><span class="input-group-text"> 
                            <i class="fa fa-user"></i> </span></label>
                        <div class="col-xs-8 col-md-8 col-lg-8 col-sm-8">
                          <input type="text" id="login-username" name="username" placeholder="" class="input-xlarge form-control regular"> <br />
                        </div>
                      </div>

                      <div class="control-group form-group">
                        <!-- Password-->
                        <label class="control-label col-xs-2 col-md-2 col-lg-2 col-sm-2" title="Password" for="login-password"><span class="input-group-text"> 
                            <i class="fa fa-lock"></i> </span></label>
                        <div class="col-xs-8 col-md-8 col-lg-8 col-sm-8">
                          <input type="password" id="login-password" name="password" placeholder="" class="input-xlarge form-control regular">
                        </div>
                      </div>
             <div class="control-group  form-group">
                            <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                                <button id="btn-login" class="btn btn-primary btn-block">' . WILang::get("login") . '</button>

    </div>  
    <div class="regDiv">   
    <p id"regmess" class="text-center">Dont have an account
    <a href="register.php">Register</a> 
    </p>
    </div>  

    <div class="log">    
    <p id"regmess" class="text-center">Forgotten password<a href="forgotpass.php">Click Here</a> </p>  
    </div>
    </fieldset>                                                               
</form>
</article>
</div> <!-- card.// -->';
   }
  echo '</div>
<div class="col-sm-2"></div>
  </div>
		</div>
		 <script type="text/javascript" src="WICore/WIJ/sha512.js"></script>
              <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
              <script type="text/javascript" src="WICore/WIJ/WILogin.js"></script>
     <script src="WICore/WIJ/WIUsers.js" type="text/javascript" charset="utf-8"></script>';

		if(isset($page)){			
		$right_sidePower = $this->Web->pageModPower($page, "right_sidebar");
		$rightSideBar = $this->Web->PageMod($page, "right_sidebar");
		//echo "righ". $right_sidePower;
		if ($right_sidePower > 0) {

	    $this->mod->getMod($rightSideBar,$page);
		}

		}			
					

	echo '</div>
			</div>';
	}


}