<?php

/**
* 
*/
class profile 
{
	

	function __construct()
	{
		$this->WIdb = WIdb::getInstance();
		$this->Web  = new WIWebsite();
		$this->site = new WISite();
		$this->mod  = new WIModules();
		$this->page = new WIPage();
		$this->login = new WILogin();
		$this->Profile = new WIProfile();
		$this->modal = new WIModal();
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

echo '<style type="text/css">
	
.content {
    padding: 32px 0;
    position: relative;
    margin-top: 58px;
}

.text-left{
	text-align: center;
}

.show{
  display: block;
}

.hide{
  display: none;
}

</style>



<div class="container-fluid text-center">    
  <div class="row content">
    <div class="col-md-2 col sm-2 col-xs-2 col-lg-4 sidenav">

    </div>
	<div class="col-lg-4 col-md-8 col-sm-8">
	
<div class="panel-heading">Topics</div>
<?php 

if(!$login->isLoggedIn()){
?>
  <div class="col-lg-4 col-md-8 col-sm-8">


<button>Click the tab above to register/login</button>

  </div>

<?php
}else{
  $topic->topic(); 
}


?>

</div>

					</div>
					

					    <div class="col-md-2 col sm-2 col-xs-2 col-lg-4 sidenav">

    </div>
    
				</div>
			</div>

      <!-- add info modal -->
  <div class="modal hide" id="votingModal">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" onclick="WITopic.close();" data-dismiss="modal" aria-hidden="true">&times;</button>
                </div>
                <div class="modal-body" id="details-body">
                    <form class="form-horizontal" id="add-user-form" enctype="multipart/form-data" method="POST"> 
                     <div class="col-md-12 body-info">
                     <div class="panel-body">


                     </div>
                     </div>
                     
                  </form>
                </div>

                <div class="modal-footer">
                </div>
              </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
          </div><!-- /.modal -->

<script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
<script type="text/javascript" src="WICore/WIJ/WITopic.js"></script>
';
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

			$thisRandNum = rand(9999999999999,999999999999999999);
	$userId = WISession::get("user_id");
	$friendId = WISession::get("friendId");
	//echo "friend" . $friendId;
	if($friendId > 0){
	  //include_once 'WIInc/friend_profile.php';
	self::friendProfile();
	}else{
	//include_once 'WIInc/my_profile.php';
	self::profilePage($userId);
	}



		
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


	public function friendProfile()
	{
		echo '<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.4.95/css/materialdesignicons.css">

<style>

.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid #e7eaed;
    border-radius: 0;
}
.card .card-description {
    margin-bottom: .875rem;
    font-weight: 400;
    color: #76838f;
}






.profile-header {
	height: 150px;
	width: 100%;
	position: relative;
}

.cover-image {
	height: 150px;
	width: 100%;
	overflow: hidden;
}



.user-image {
	position: absolute;
	height: 80px;
	width: 80px;
	border-radius: 50%;
	bottom: -50%;
	left: 50%;
	/* top: 50%; */
	transform: translate(-50%, -50%);
	z-index: 5;
}

.user-image img {
	height: 80px;
	width: 80px;
	border-radius: 50%;
	top: -40px;
	border: 5px solid #777;
}

.profile-card .profile-content {
	padding: 50px 20px 30px 20px;
}



.profile-card .profile-name {
	font-size: 1.2rem;
	color: #3249b9;
	font-weight: 600;
	text-align: center;
}

.profile-card .profile-designation {
	font-size: 13px;
	color: #777;
	text-align: center;
}

.profile-card .profile-description {
	padding: 10px;
	font-size: 13px;
	color: #777;
	margin: 5px 0px;
	background-color: #F1F2F3;
	border-radius: 5px;
}

.profile-card ul.profile-info-list {
	padding: 0;
	margin: 10px 0;
	list-style: none;
	font-size: 1rem;
	font-weight: 500;
	color: #777;
}




.profile-card ul.profile-info-list a {
	text-decoration: none;
	color:inherit;
}



.profile-card a.profile-info-list-item {
	margin: 10px 0;
	padding:15px;
	background-color: #F1F2F3;
	display: block;
	 -webkit-transition: all .5s ease-in-out;
    -o-transition: all .5s ease-in-out;
    transition: all .5s ease-in-out;

}

.profile-card a.profile-info-list-item:hover {
    background-color: #9E9E9E;
    color: white;
	 -webkit-transition: all .5s ease-in-out;
    -o-transition: all .5s ease-in-out;
    transition: all .5s ease-in-out;
	
}


.profile-card a.profile-info-list-item  i {
	padding: 10px;
	
}

ul.about {
    list-style: none;
    color: black;
	padding: 0;
}
li.about-items {
    margin: 10px;
    font-size: 0.9rem;
    /* font-family: sans-serif; */
    /* font-weight: 400; */
}



li.about-items i {
color:#607d8b;
}

span.about-item-name {
    width: 100px;
    display: inline-flex;
	    margin-left: 10px;
}


span.about-item-detail {
    display: inline-flex;
    width: calc(100% - 160px);
}
span.about-item-detail > button{
  margin-right: 10px;
}

.btn.btn-icon {
    width: 40px;
    height: 40px;
    padding: 0;
}
.btn.btn-rounded {
    border-radius: 50px;
}

a.about-item-edit {
    float: right;
}
</style>
		<!-- partial -->
		<div class="main-panel">
			<div class="container">


				<div class="row">
					<div class="col-md-4 grid-margin stretch-card">
						<div class="card">
							<div class="profile-card">

								<div class="profile-header">

									<div class="cover-image">
										<img src="https://cdn.pixabay.com/photo/2019/10/19/14/16/away-4561518_960_720.jpg" class="img img-fluid">
									</div>
									<div class="user-image">
										<img src="https://scontent.fslv1-1.fna.fbcdn.net/v/t1.0-1/p240x240/71764978_543119796448430_5969446522109034496_n.jpg?_nc_cat=105&_nc_oc=AQnoaTQx8fxeyzbIbtQyvZXghFdEQl-ds6NQr_13xAXuGmWnigS1zJHTuH8sKv5e9-TSN3KRAuptCtwM-LlwxidP&_nc_ht=scontent.fslv1-1.fna&oh=52752a53e6b91980d3f9bf227064a95d&oe=5E5A25B9" class="img ">
									</div>
								</div>

								<div class="profile-content">
									<div class="profile-name">Santosh Ghimire</div>
									<div class="profile-designation">Webdeveloper</div>
									<p class="profile-description">Lorem ipsum dolor sit amet, consetetur sadipscing elitr, sed diam nonumy eirmod tempor.</p>
									<ul class="profile-info-list">
										<a href="" class="profile-info-list-item"><i class="mdi mdi-eye"></i>Timeline</a>
										<a href="" class="profile-info-list-item"><i class="mdi mdi-bookmark-check"></i>Saved</a>
										<a href="" class="profile-info-list-item"><i class="mdi mdi-movie"></i>Medias</a>
										<a href="" class="profile-info-list-item"><i class="mdi mdi-account"></i>About</a>

									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="col-md-8 grid-margin stretch-card">
						<div class="card">
							<div class="card-body">
								<p class="card-title font-weight-bold">About</p>
								<hr>
								<p class="card-description">User Information</p>
								<ul class="about">
									<li class="about-items"><i class="mdi mdi-account icon-sm "></i><span class="about-item-name">Name:</span><span class="about-item-detail">Santosh Ghimire</span><a href="" class="about-item-edit">Edit</a></li>
									<li class="about-items"><i class="mdi mdi-mail-ru icon-sm "></i><span class="about-item-name">username:</span><span class="about-item-detail">santoshghimire</span> <a href="" class="about-item-edit">Edit</a></li>
									<li class="about-items"><i class="mdi mdi-lock-outline icon-sm "></i><span class="about-item-name">Password:</span><span class="about-item-detail">**********</span> <a href="" class="about-item-edit">Edit</a></li>
									<li class="about-items"><i class="mdi mdi-format-align-left icon-sm "></i><span class="about-item-name">Bio:</span><span class="about-item-detail">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Architecto totam, nemo quidem delectus dolores vero porro inventore perferendis minus perspiciatis.</span> <a href="" class="about-item-edit">Edit</a></li>
									
                       <li class="about-items"><i class="mdi mdi-trophy-variant-outline icon-sm "></i><span class="about-item-name">Badges:</span><span class="about-item-detail">
                       <button type="button" class="btn btn-success btn-rounded btn-icon">
                        <i class="mdi mdi-star text-white"></i>
                      </button>  
                        <button type="button" class="btn btn-info btn-rounded btn-icon">
                        <i class="mdi mdi-check text-white"></i>
                      </button>
                       <button type="button" class="btn btn-danger btn-rounded btn-icon">
                        <i class="mdi mdi-check text-white"></i>
                      </button>
                      </span> <a href="" class="about-item-edit">View</a></li>
                      
								</ul>
								<p class="card-description">Contact Information</p>
								<ul class="about">
									<li class="about-items"><i class="mdi mdi-phone icon-sm "></i><span class="about-item-name">Phone:</span><span class="about-item-detail">+9779861106179</span><a href="" class="about-item-edit">Edit</a></li>
									<li class="about-items"><i class="mdi mdi-map-marker icon-sm "></i><span class="about-item-name">Address:</span><span class="about-item-detail">254 National Highway , Hisar India</span> <a href="" class="about-item-edit">Edit</a></li>
									<li class="about-items"><i class="mdi mdi-email-outline icon-sm "></i><span class="about-item-name">Email:</span><span class="about-item-detail"><a href="">reasonghimire706@gmail.com</a></span> <a href="" class="about-item-edit">Edit</a></li>
									<li class="about-items"><i class="mdi mdi-web icon-sm "></i><span class="about-item-name">Site:</span><span class="about-item-detail"><a href="google.com">www.google.com</a></span> <a href="" class="about-item-edit">Edit</a></li>
								</ul>
								<p class="card-description">Basic Information</p>
								<ul class="about">
									<li class="about-items"><i class="mdi mdi-cake icon-sm "></i><span class="about-item-name">Birthday:</span><span class="about-item-detail">Aug 3 , 1998</span><a href="" class="about-item-edit">Edit</a></li>
									<li class="about-items"><i class="mdi mdi-account icon-sm "></i><span class="about-item-name">Gender:</span><span class="about-item-detail">Male</span> <a href="" class="about-item-edit">Edit</a></li>
									<li class="about-items"><i class="mdi mdi-clipboard-account icon-sm "></i><span class="about-item-name">Profession:</span><span class="about-item-detail">Student</span> <a href="" class="about-item-edit">Edit</a></li>
									<li class="about-items"><i class="mdi mdi-water icon-sm "></i><span class="about-item-name">Blood Group:</span><span class="about-item-detail">AB+</span> <a href="" class="about-item-edit">Edit</a></li>
									<li class="about-items"><i class="mdi mdi-human-male-female icon-sm "></i><span class="about-item-name">Relationship Status:</span><span class="about-item-detail">Single</span> <a href="" class="about-item-edit">Edit</a></li>
								</ul>


							</div>
						</div>
					</div>

				</div>




			</div>';
	}

	public function profilePage($userId)
	{
		echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/4.4.95/css/materialdesignicons.css">

<style>

.card {
    position: relative;
    display: flex;
    flex-direction: column;
    min-width: 0;
    word-wrap: break-word;
    background-color: #fff;
    background-clip: border-box;
    border: 1px solid #e7eaed;
    border-radius: 0;
}
.card .card-description {
    margin-bottom: .875rem;
    font-weight: 400;
    color: #76838f;
}






.profile-header {
	height: 150px;
	width: 100%;
	position: relative;
}

.cover-image {
	height: 150px;
	width: 100%;
	overflow: hidden;
}



.user-image {
	position: absolute;
	height: 80px;
	width: 80px;
	border-radius: 50%;
	bottom: -50%;
	left: 50%;
	/* top: 50%; */
	transform: translate(-50%, -50%);
	z-index: 5;
}

.user-image img {
	height: 80px;
	width: 80px;
	border-radius: 50%;
	top: -40px;
	border: 5px solid #777;
}

.profile-card .profile-content {
	padding: 50px 20px 30px 20px;
	margin: 31px 0px 0px 0px;
}



.profile-card .profile-name {
	font-size: 1.2rem;
	color: #3249b9;
	font-weight: 600;
	text-align: center;
}

.profile-card .profile-designation {
	font-size: 13px;
	color: #777;
	text-align: center;
}

.profile-card .profile-description {
	padding: 10px;
	font-size: 13px;
	color: #777;
	margin: 5px 0px;
	background-color: #F1F2F3;
	border-radius: 5px;
}

.profile-card ul.profile-info-list {
	padding: 0;
	margin: 10px 0;
	list-style: none;
	font-size: 1rem;
	font-weight: 500;
	color: #777;
}




.profile-card ul.profile-info-list a {
	text-decoration: none;
	color:inherit;
}



.profile-card a.profile-info-list-item {
	margin: 10px 0;
	padding:15px;
	background-color: #F1F2F3;
	display: block;
	 -webkit-transition: all .5s ease-in-out;
    -o-transition: all .5s ease-in-out;
    transition: all .5s ease-in-out;

}

.profile-card a.profile-info-list-item:hover {
    background-color: #9E9E9E;
    color: white;
	 -webkit-transition: all .5s ease-in-out;
    -o-transition: all .5s ease-in-out;
    transition: all .5s ease-in-out;
	
}


.profile-card a.profile-info-list-item  i {
	padding: 10px;
	
}

ul.about {
    list-style: none;
    color: black;
	padding: 0;
}
li.about-items {
    margin: 10px;
    font-size: 0.9rem;
    /* font-family: sans-serif; */
    /* font-weight: 400; */
}



li.about-items i {
color:#607d8b;
}

span.about-item-name {
    width: 100px;
    display: inline-flex;
	    margin-left: 10px;
}


span.about-item-detail {
    display: inline-flex;
    width: calc(100% - 160px);
}
span.about-item-detail > button{
  margin-right: 10px;
}

.btn.btn-icon {
    width: 40px;
    height: 40px;
    padding: 0;
}
.btn.btn-rounded {
    border-radius: 50px;
}

a.about-item-edit {
    float: right;
}
</style>
		<!-- partial -->
		<div class="main-panel">
			<div class="container">


				<div class="row">
					<div class="col-md-4 grid-margin stretch-card">
						<div class="card">
							<div class="profile-card">

								<div class="profile-header">

									<div class="cover-image">
										<img src="../WIAdmin/WIMedia/Img/contents/profile/waterfall.jpg" class="img img-fluid">
									</div>
									<div class="user-image">';
									$user_pic  = $this->Profile->User_pic($userId);
									echo '</div>
								</div>
								<a href="javascript:void(0);" onclick="WIProfile.photo()">' . WILang::get("change_pic") . '</a>

								<a href="javascript:void(0);" onclick="WIProfile.edit()" style="margin-left: 164px;">' . WILang::get("edit") . '</a>';

								$this->Profile->userInfo();

								$this->Profile->EditUserInFo();

								echo '
							</div>
						</div>
					</div><div id="userTabs">';
					$this->mod->getMod("profile_tabs");
					echo '</div>
				</div>




			</div>
			<script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
			<script type="text/javascript" src="WICore/WIJ/WIMedia.js"></script>
			<script type="text/javascript" src="WICore/WIJ/WIMediaCenter.js"></script>
			<script type="text/javascript" src="WICore/WIJ/WIProfile.js"></script>';
$this->modal->moduleModal('change-avatar', 'Add avatar', 'WIProfile', 'changeAvatar','Save'); 
$this->modal->moduleModal('change-media', 'Change avatar', 'WIProfile', 'avatarPics','Save'); 
$this->modal->moduleModal('media-upload', 'Upload avatar', 'WIMedia', 'UploadAvatarPics','Save'); 



	}


}