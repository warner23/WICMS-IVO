<?php
#[\AllowDynamicProperties]
class WIBootStrap 
{

	public function __construct()
	{
		$this->WIdb = WIdb::getInstance();
		$this->maint = new WIMaintenace();
		$this->Web  = new WIWebsite();
		$this->mod  = new WIModules();
	}	


	public function startMod($page)
	{
	echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bg-index">';
		if(isset($page)){
		$left_sidePower = $this->Web->pageModPower($page, "left_sidebar");
		$right_sidePower = $this->Web->pageModPower($page, "right_sidebar");
		$leftSideBar = $this->Web->PageMod($page, "left_sidebar");
		if ($left_sidePower > 0 && $right_sidePower > 0 ) {
        $this->mod->getMod($leftSideBar, $page);
        echo '<div class="col-lg-8 col-md-8 col-sm-8">';
		}else if ($left_sidePower > 0){
			$this->mod->getMod($leftSideBar, $page);
			echo '<div class="col-lg-10 col-md-10 col-sm-10">';
		}else if ($right_sidePower > 0){
			echo '<div class="col-lg-10 col-md-10 col-sm-10">';
		}else{
			echo '<div class="col-lg-12 col-md-12 col-sm-12">';
		}
		}
	}


	public function startContentsHolder()
	{
		echo '<div class="container-fluid text-center">    
    <div class="row content">
	<div class="col-lg-12 col-md-12 col-sm-12" >';
	}

	public function endContentsHolder()
	{
	echo '</div>
			</div></div></div>';
	}

	public function endMod($page)
	{
		if(isset($page)){			
		$right_sidePower = $this->Web->pageModPower($page, "right_sidebar");
		$rightSideBar = $this->Web->PageMod($page, "right_sidebar");
		//echo $Panel;
		if ($right_sidePower > 0) {
			$this->mod->getMod($rightSideBar, $page);
		}

		}	
	echo '</div>';
	}



}

?>