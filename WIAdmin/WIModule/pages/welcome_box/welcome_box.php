<?php
#[\AllowDynamicProperties]
/**
* 
*/
class welcome_box 
{
	

	function __construct()
	{
		$this->WIdb = WIdb::getInstance();
		$this->Web  = new WIWebsite();
		$this->site = new WISite();
		$this->mod  = new WIModules();
		$this->page = new WIPage();
		$this->Slide = new WISlideshow();
		$this->Boot = new WIBootStrap();
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
		$this->Boot->startMod($page);

    $this->Boot->startContentsHolder();
		
		$this->Slide->SlideShow('index');

		echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

							<div class="col-md-4 homepage-food">
            <h3 class="special-title-2">Food</h3>
            <div id="food">
              <img src="WIAdmin/WIMedia/Img/index/homepage-food.jpg"
                alt="Food Menu"
                class="rounded-circle lazyload">
              <p class="homepage-menu-description">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam 
                dolor neque, condimentum quis ante ac, imperdiet varius sapien. 
                Maecenas commodo ante et odio varius, at placerat mi tristique. 
              </p>
              <a class="btn btn-outline-dark" href="WIPos/food.php" role="button">VIEW MENU</a>
            </div>
          </div>
          <div class="col-md-4 homepage-desserts">
            <h3 class="special-title-2">Desserts</h3>
            <div id="desserts">
              <img src="WIAdmin/WIMedia/Img/index/homepage-desserts.jpg"
                alt="Desserts Menu"
                class="rounded-circle lazyload">
              <p class="homepage-menu-description">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam 
                dolor neque, condimentum quis ante ac, imperdiet varius sapien. 
                Maecenas commodo ante et odio varius, at placerat mi tristique. 
              </p>
              <a class="btn btn-outline-dark" href="WIPos/food.php" role="button">VIEW MENU</a>
            </div>
          </div>
          <div class="col-md-4 homepage-drinks">
            <h3 class="special-title-2">Drinks</h3>
            <div id="drinks">
              <img src="WIAdmin/WIMedia/Img/index/homepage-drinks.jpg"
                alt="Drinks Menu"
                class="rounded-circle lazyload">
              <p class="homepage-menu-description">
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam 
                dolor neque, condimentum quis ante ac, imperdiet varius sapien. 
                Maecenas commodo ante et odio varius, at placerat mi tristique. 
              </p>
              <a class="btn btn-outline-dark" href="WIPos/drinks.php" role="button">VIEW MENU</a>
            </div>
          </div>
        </div>
      </div>

					</div>
					
          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" >

					</div>';

		$this->Boot->endContentsHolder();	
		echo '<script src="WICore/WIJ/WIIndex.js"></script>';	
    
    $this->Boot->endMod($page);
	}


}