<?php
#[\AllowDynamicProperties]
/**
* 
*/
class upgrade 
{
	

	function __construct()
	{
		$this->WIdb = WIdb::getInstance();
		$this->Web  = new WIWebsite();
		$this->site = new WISite();
		$this->mod  = new WIModules();
		$this->page = new WIPage();
		$this->upgrade  = new WIUpgrade();
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

		echo '<div class="container-fluid text-center bg-index">    
    <div class="row">

	<div class="col-lg-12 col-md-12 col-sm-12" >
       <div class="col-lg-12 col-md-12 col-sm-12 text-left">
       <button onlick="WIProfile.back();">Back</button>
       <style>
    .box{
    background-color: white;
    padding: 2%;
    }

    .box-title{
    text-align: center;
    width: 50%;
    margin-left: 26%;
    }

        .box-price{
    text-align: center;
    width: 50%;
    margin-left: 26%;
    }

    .box-text{
    width: 60%;
    margin-left: 20%;
    }

    #upgrade_desc{
width: 70%;
    margin-left: 20%;
    font-size: 20px;
}
	.paypal{
	background-color:white;
	    min-height: 554px;
	}
    </style>
    <div class="col-lg-12 col-md-12 col-sm-12">
    <div class="col-lg-8 col-md-8 col-sm-8">
    <div class="box" id="upgrade_acc">

           </div>
    </div>
    <div class="col-lg-4 col-md-4 col-sm-4">
    <div class="paypal">
    <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />

  <script
      src="https://www.paypal.com/sdk/js?client-id=' .PAYPAL_CLIENT_ID . '&vault=true&intent=subscription">
  </script>

   <div id="paypal-button-container"></div>

  <script>

    if( sessionStorage.getItem("membership_plan") == "VIP"){
      var plan = "P-21411404HK733952FMBSG2YQ";
    }else if( sessionStorage.getItem("membership_plan") == "Platium"){
      var plan = "P-3EA78446E91240712MBSHCJA";
    }else if( sessionStorage.getItem("membership_plan") == "Student"){
      var plan = "P-3EA78446E91240712MBSHCJA";
    }

    price = sessionStorage.getItem("membership_price");
    m_id  = sessionStorage.getItem("membership_id");
    role = sessionStorage.getItem("membership_plan");
    paypal.Buttons({

  createSubscription: function(data, actions) {
    console.log(plan);
    return actions.subscription.create({

      "plan_id": plan

    });

  },


  onApprove: function(data, actions) {

    console.log("You have successfully created subscription " + data.subscriptionID);
    WIUpgrade.accepted(data.subscriptionID, plan,price, role);

  }


}).render("#paypal-button-container");
  </script>
    </div>
  
</div>
   </div>
       </div>';
					


					
       echo '<script type="text/javascript" src="WICore/WIJ/WIUpgrade.js"></script>';
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
			</div></div>';
	}


}