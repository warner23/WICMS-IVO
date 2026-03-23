<?php

/**
* 
*/
class checkout 
{
	

	function __construct()
	{
		$this->WIdb = WIdb::getInstance();
		$this->Web  = new WIWebsite();
		$this->site = new WISite();
		$this->mod  = new WIModules();
		$this->page = new WIPage();
		$this->login = new WILogin();
        $this->Info = new WIUserInfo();
        $this->user   = new WIUser(WISession::get('user_id'));
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


		echo '<div class="container-fluid text-center">    
  <div class="row content">

	<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >

						 <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left"> Check Out';
                         if($this->login->isLoggedIn()){
							
						$this->regUser(); 
                        }else{
                        $this->guestUser();
                        }


						echo '</div>
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



    public function regUser()
    {
        echo '<div class="wizard col-md-12">
            <div class="steps">
                <ul>
                    <li>
                        <a :class="active">
                            <div class="stepNumber active" id="stepOne"><i class="fa fa-user"></i></div>
                            <span class="stepDesc text-small">Billing
                               </span>
                        </a>
                    </li>
                    <li>
                        <a :class="active">
                            <div class="stepNumber inactive" id="stepTwo"><i class="fa fa-list"></i></div>
                            <span class="stepDesc text-small">Shipping</span>
                        </a>
                    </li>

                     <li>
                        <a :class="active">
                            <div class="stepNumber inactive" id="stepThree"><i class="fa fa-gears"></i></div>
                            <span class="stepDesc text-small">Payment</span>
                        </a>
                    </li>
                    <li>
                        <a :class="active">
                            <div class="stepNumber inactive" id="stepFour"><i class="fa fa-database"></i></div>
                            <span class="stepDesc text-small">Complete</span>
                        </a>
                    </li>
                   

                  
                </ul>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 step-content show" id="step_one">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
                <h3>'; WILang::get('welcome'); echo '</h3>
                <hr>
                <p>'; echo WILang::get('steps'); echo '</p>
                <p>'; echo WILang::get('guide') ; echo '</p>
                <br>
                

 <div class="col-md-6 col-sm-6 col-xs-12">
                                        <h3 class="text-center">Billing Address</h3>
                         
                                        <hr>

                                              <div class="col-xs-6 col-sm-6 col-md-6">
                                                 <div class="form-group">
                                                     <input type="text" name="first_name" id="first_name" class="form-control input-sm" placeholder="First Name">
                                                </div>
                                 </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="last_name" id="last_name" class="form-control input-sm" placeholder="Last Name">
                                    </div>
                                </div>

                                          <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                            <input type="text" name="first_name" id="first_name" class="form-control input-sm" placeholder="Email id">
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="last_name" id="last_name" class="form-control input-sm" placeholder="Mobile no">
                                    </div>
                                </div>

                                          <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                            <textarea class="form-control" placeholder="Delivery Info">                                                
                                            </textarea>
                                    </div>
                                </div>

                                          <div class="col-xs-12 col-sm-4 col-md-4">
                                    <div class="form-group">
                            <input type="text" name="first_name" id="first_name" class="form-control input-sm" placeholder="country">
                                    </div>
                                </div>
                                   <div class="col-xs-12 col-sm-4 col-md-4">
                                    <div class="form-group">
                            <input type="text" name="first_name" id="first_name" class="form-control input-sm" placeholder="city">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-4 col-md-4">
                                    <div class="form-group">
                                        <input type="text" name="last_name" id="last_name" class="form-control input-sm" placeholder="pincode">
                                    </div>
                                </div>
                                               <input class="coupon_question" type="checkbox" name="coupon_question" value="1">
                                        <span class="item-text">Check Shipping address</span>
                                  </div>


                                  <div class="col-md-6 shipping col-sm-6 col-xs-12">
                                        <h3 class="text-center">shipping Address</h3>

                                        <hr>
                                              <div class="col-xs-6 col-sm-6 col-md-6">
                                                 <div class="form-group">
                                                     <input type="text" name="first_name" id="first_name" class="form-control input-sm" placeholder="First Name">
                                                </div>
                                 </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="last_name" id="last_name" class="form-control input-sm" placeholder="Last Name">
                                    </div>
                                </div>

                                          <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                            <input type="text" name="first_name" id="first_name" class="form-control input-sm" placeholder="Email id">
                                    </div>
                                </div>
                                <div class="col-xs-6 col-sm-6 col-md-6">
                                    <div class="form-group">
                                        <input type="text" name="last_name" id="last_name" class="form-control input-sm" placeholder="Mobile no">
                                    </div>
                                </div>

                                          <div class="col-xs-12 col-sm-12 col-md-12">
                                    <div class="form-group">
                                            <textarea class="form-control">                                                
                                            </textarea>
                                    </div>
                                </div>

                                          <div class="col-xs-12 col-sm-4 col-md-4">
                                    <div class="form-group">
                            <input type="text" name="first_name" id="first_name" class="form-control input-sm" placeholder="country">
                                    </div>
                                </div>
                                   <div class="col-xs-12 col-sm-4 col-md-4">
                                    <div class="form-group">
                            <input type="text" name="first_name" id="first_name" class="form-control input-sm" placeholder="city">
                                    </div>
                                </div>
                                <div class="col-xs-12 col-sm-4 col-md-4">
                                    <div class="form-group">
                                        <input type="text" name="last_name" id="last_name" class="form-control input-sm" placeholder="pincode">
                                    </div>
                                </div>
                                  </div> 


                               
                                 
                               

                <a href="javascript:;" onclick="checkout.stepOne();" class="btn btn-as pull-right" type="button">
                    '; echo WILang::get('next'); echo '

                    <i class="fa fa-arrow-right"></i>
                </a>
                <div class="clearfix"></div>
            </div>
            </div>


            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 step-content hide" id="step_two">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >               
                     <fieldset>
                    <legend>Select Postage Option </legend>
                    <label for="google-trans">First Class</label>
                    <input type="radio" name="trans" id="google">
                    <label for="wi-trans">Second Class</label>
                    <input type="radio" name="trans" id="wilang">
                  </fieldset>

                    </div>


                               
                  

                    <a href="javascript:;" class="btn btn-as pull-right" onclick="checkout.stepTwo()" type="button" id="required">
                        '; echo WILang::get('next') ; echo '
                        <i class="fa fa-arrow-right"></i>
                    </a>
                    <div class="clearfix"></div>
                </div>
            </div>
            </div>
             <div class="step-content hide" id="step_three">
                <h3>'; echo WILang::get('benefits'); echo '</h3>
                <hr>
                <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
                 <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

			                  <script>
			  $( function() {
			    $( "#payment_type" ).accordion({
			      heightStyle: "content"
			    });
			  } );
			  </script>

			<div id="payment_type">
			  <h3>Paypal</h3>
			  <div>
			    <p>Mauris mauris ante, blandit et, ultrices a, susceros. Nam mi. Proin viverra leo ut odio. Curabitur malesuada. Vestibulum a velit eu ante scelerisque vulputate.</p>
			  </div>
			  <h3>Card</h3>
			  <div>
			    <form class="form-horizontal">
        <fieldset>
          <div id="legend">
            <legend class="">Payment</legend>
          </div>
     
          <!-- Name -->
          <div class="control-group">
            <label class="control-label"  for="username">Card Holder\'s Name</label>
            <div class="controls">
              <input type="text" id="username" name="username" placeholder="" class="input-xlarge">
            </div>
          </div>
     
          <!-- Card Number -->
          <div class="control-group">
            <label class="control-label" for="email">Card Number</label>
            <div class="controls">
              <input type="text" id="email" name="email" placeholder="" class="input-xlarge">
            </div>
          </div>
     
          <!-- Expiry-->
          <div class="control-group">
            <label class="control-label" for="password">Card Expiry Date</label>
            <div class="controls">
              <select class="span3" name="expiry_month" id="expiry_month">
                <option></option>
                <option value="01">Jan (01)</option>
                <option value="02">Feb (02)</option>
                <option value="03">Mar (03)</option>
                <option value="04">Apr (04)</option>
                <option value="05">May (05)</option>
                <option value="06">June (06)</option>
                <option value="07">July (07)</option>
                <option value="08">Aug (08)</option>
                <option value="09">Sep (09)</option>
                <option value="10">Oct (10)</option>1
                <option value="11">Nov (11)</option>
                <option value="12">Dec (12)</option>
              </select>
              <select class="span2" name="expiry_year">
                <option value="13">2013</option>
                <option value="14">2014</option>
                <option value="15">2015</option>
                <option value="16">2016</option>
                <option value="17">2017</option>
                <option value="18">2018</option>
                <option value="19">2019</option>
                <option value="20">2020</option>
                <option value="21">2021</option>
                <option value="22">2022</option>
                <option value="23">2023</option>
              </select>
            </div>
          </div>
     
          <!-- CVV -->
          <div class="control-group">
            <label class="control-label"  for="password_confirm">Card CVV</label>
            <div class="controls">
              <input type="password" id="password_confirm" name="password_confirm" placeholder="" class="span2">
            </div>
          </div>
     
          <!-- Save card -->
          <div class="control-group">
            <div class="controls">
              <label class="checkbox" for="save_card">
                <input type="checkbox" id="save_card" value="option1">
                Save card on file?
              </label>
            </div>
          </div>
     
          <!-- Submit -->
          <div class="control-group">
            <div class="controls">
              <button class="btn btn-success">Pay Now</button>
            </div>
          </div>
     
        </fieldset>
      </form>
			  </div>

			  </div>
			</div>

                <button class="btn btn-as pull-right" onclick="checkout.stepThree();" type="button">
                    <span class="show" id="next">
                        '; echo WILang::get('next'); echo '
                        
                        <i class="fa fa-arrow-right" ></i>
                    </span>
                    <span class="hide" id="spin">
                        <i class="fa fa-circle-o-notch fa-spin"></i>
                       '; echo WILang::get('connecting'); echo '
                    </span>
                </button>
                <div class="clearfix"></div>
            </div>


            <div class="step-content hide" id="step_four">
                <h3>'; echo WILang::get('support'); echo '</h3>
                <hr>





               

                <button class="btn btn-as pull-right" onclick="checkout.stepFour();" type="button">
                    <span class="show" id="next">
                        '; echo WILang::get('next'); echo '
                        <i class="fa fa-arrow-right" ></i>
                    </span>
                    <span class="hide" id="spin">
                        <i class="fa fa-circle-o-notch fa-spin"></i>
                        '; echo WILang::get('connecting'); echo '
                    </span>
                   
                </button>
                 <span class="show" id="mess">
                        <i class="fa"></i>
                    </span>
                <div class="clearfix"></div>
            </div>

           



        </div>
    </div>
</div>
<script src="WICore/WIJ/WICheckout.js"></script>';
    }

    public function guestUser()
    {
        echo '<div class="wizard col-md-6 col-md-offset-3">
            <div class="steps">
                <ul>
                    <li>
                        <a :class="active">
                            <div class="stepNumber active" id="stepOne"><i class="fa fa-user"></i></div>
                            <span class="stepDesc text-small">Sign In
                               </span>
                        </a>
                    </li>
                    <li>
                        <a :class="active">
                            <div class="stepNumber inactive" id="stepTwo"><i class="fa fa-list"></i></div>
                            <span class="stepDesc text-small">Billing</span>
                        </a>
                    </li>

                     <li>
                        <a :class="active">
                            <div class="stepNumber inactive" id="stepThree"><i class="fa fa-gears"></i></div>
                            <span class="stepDesc text-small">Postage</span>
                        </a>
                    </li>
                    <li>
                        <a :class="active">
                            <div class="stepNumber inactive" id="stepFour"><i class="fa fa-database"></i></div>
                            <span class="stepDesc text-small">Payment</span>
                        </a>
                    </li>
                    <li>
                        <a :class="active">
                            <div class="stepNumber inactive" id="stepFive"><i class="fa fa-terminal"></i></div>
                            <span class="stepDesc text-small">Complete</span>
                        </a>
                    </li>

                  
                </ul>
            </div>

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 step-content show" id="step_one">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
                <form class="form-horizontal register-form" id="tab">
                      <fieldset>
                        <div id="legend">
                          <legend class="">' . WILang::get("create_account") . '</legend>
                        </div>

                        <div class="control-group  form-group">
                            <label class="control-label col-lg-4" for="reg-email" >' . WILang::get("email") .' <span class="required">*</span></label>
                            <div class="controls col-lg-8">
                                <input type="text" id="reg-email" class="input-xlarge form-control">
                            </div>
                        </div>

                        <div class="control-group  form-group">
                            <label class="control-label col-lg-4" for="reg-username">' .WILang::get("username") . '<span class="required">*</span></label>
                            <div class="controls col-lg-8">
                                <input type="text" id="reg-username" class="input-xlarge form-control">
                            </div>
                        </div>


                        <div class="control-group  form-group">
                            <label class="control-label col-lg-4" for="reg-password">' . WILang::get("password") . ' <span class="required">*</span></label>
                            <div class="controls col-lg-8">
                                <input type="password" id="reg-password" class="input-xlarge form-control">
                            </div>
                        </div>

                        <div class="control-group  form-group">
                            <label class="control-label col-lg-4" for="reg-repeat-password">' . WILang::get("repeat_password") . ' <span class="required">*</span></label>
                            <div class="controls col-lg-8">
                                <input type="password" id="reg-repeat-password" class="input-xlarge form-control">
                            </div>
                        </div>

                        

                          
                        <div class="control-group  form-group">
                            <label class="control-label col-lg-4" for="reg-bot-sum">
            ' . WISession::get("bot_first_number") . '+ 
                                ' . WISession::get("bot_second_number") . '
                                <span class="required">*</span>
                            </label>
                            <div class="controls col-lg-8">
                                <input type="text" id="reg-bot-sum" class="input-xlarge form-control">
                            </div>
                        </div>

                        <div class="control-group  form-group">
                            <div class="controls col-lg-offset-4 col-lg-8">
                                <button id="btn-register" class="btn btn-success">' . WILang::get("create_account") . '</button>
                            </div>
                        </div>
                       </fieldset>
                  </form>
                <br>
                

                <a href="javascript:;" onclick="WIClient.stepOne();" class="btn btn-as pull-right" type="button">
                    '; echo WILang::get('next'); echo '

                    <i class="fa fa-arrow-right"></i>
                </a>
                <div class="clearfix"></div>
            </div>
            </div>


            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 step-content hide" id="step_two">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >                
            <div class="alert alert-danger hide" id="snap" >
                    <strong>'; echo WILang::get('next'); echo '</strong> 
                </div>

                
                    <h3>'; echo WILang::get('personal'); echo '</h3>
                <hr>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-3">
                    <div class="form-group">
                        <label for="fname">'; echo WILang::get('fname'); echo '</label>
                        <input type="text" class="form-control" id="fname" value="John">
                        <small>'; echo WILang::get('webdom'); echo '
                            <strong>'; echo WILang::get('donot'); echo '</strong> 
                            '; echo WILang::get('write_path_info'); echo '
                        </small>
                    </div>
                    </div>

                <div class="col-sm-3 col-md-3 col-lg-3 col-xs-3">
                   <div class="form-group">
                        <label for="fam_name">'; echo WILang::get('fam_name'); echo '</label>
                        <input type="text" class="form-control" id="fam_name"
                               v-model="website.fam_name" value="Doe">
                    </div>
                    </div>

                    

                    <div class="col-sm-2 col-md-2 col-lg-2 col-xs-2">
                     <div class="form-group">
                     <label for="dependants">'; echo WILang::get('dependants'); echo '</label><br>
                        <select id="dependants">
                            <option id="0" value="0">0</option>
                            <option id="1" value="1">1</option>
                            <option id="2" value="2">2</option>
                            <option id="3" value="3">3</option>
                            <option id="4" value="4">4</option>
                            <option id="5" value="5">5</option>
                            <option id="6" value="6">6</option>
                            <option id="7" value="7">7</option>
                        </select>
                        <small>'; echo WILang::get('webdom'); echo '
                            <strong>'; echo WILang::get('donot'); echo '</strong> 
                            '; echo WILang::get('write_path_info'); echo '
                        </small>
                    </div>
                    </div>

                    <div class="col-sm-3 col-md-3 col-lg-3 col-xs-3">
                     <div class="form-group">
                        <label for="marital">'; echo WILang::get('marital'); echo '</label>
                        <select id="marital_status">
                            <option id="Single" value="Single">Single</option>
                            <option id="Married" value="Married">Married</option>
                            <option id="Seperated" value="Seperated">Seperated</option>
                            <option id="Widowed" value="Widowed">Widowed</option>
                            <option id="dv" value="dv">In Abusive relationship</option>

                        </select>
                        <small>'; echo WILang::get('webdom'); echo '
                            <strong>'; echo WILang::get('donot'); echo '</strong> 
                            '; echo WILang::get('write_path_info'); echo '
                        </small>
                    </div>
                    </div>
                    <br>
                    </div>

                    
                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="col-sm-3 col-md-3 col-lg-3 col-xs-3">
                    <div class="form-group">
                        <label for="contact_no">'; echo WILang::get('contact_no'); echo '</label>
                        <input type="text" class="form-control" id="contact_no" value="07944556677"
                                value="">
                        <small>'; echo WILang::get('webdom'); echo '
                            <strong>'; echo WILang::get('donot'); echo '</strong> 
                            '; echo WILang::get('write_path_info'); echo '
                        </small>
                    </div>
                    </div>
                    <br>
                    <div class="col-sm-8 col-md-8 col-lg-8 col-xs-8">
                    <label for="dob">'; echo WILang::get('DOB'); echo '</label>
                     <div class="form-group">
                        

                        <select id="Month" class="col-sm-4 col-md-4 col-lg-4 col-xs-4">
                            <option id="January" value="January">January</option>
                            <option id="Febuary" value="Febuary">Febuary</option>
                            <option id="March" value="March">March</option>
                            <option id="April" value="April">April</option>
                            <option id="May" value="May">May</option>
                            <option id="June" value="June">June</option>
                            <option id="July" value="July">July</option>
                            <option id="August" value="August">August</option>
                            <option id="September" value="September">September</option>
                            <option id="October" value="October">October</option>
                            <option id="November" value="November">November</option>
                            <option id="December" value="December">December</option>
                        </select>

                        

                        <input type="text" class="col-sm-4 col-md-4 col-lg-4 col-xs-4" id="date" value="15" placeholder="Date">

                        <input type="text" class="col-sm-4 col-md-4 col-lg-4 col-xs-4" id="year" value="1980" placeholder="Year">
                    </div>
                    </div>
                    </div>

                    <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                    <div class="form-group">
                     <div id="legend">

                        <label>'; echo WILang::get('sex'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input type="hidden" name="gender" id="gender" class="btn-group-value" value="male"/>
                        <button type="button" id="sex_true" name="male" value="male"  class="btn">'; echo WILang::get('male'); echo '</button>
                        <button type="button" id="sex_false" name="female" value="female" class="btn activewhens" >'; echo WILang::get('female'); echo '</button>
                    </div>

                    <span class="help-block">'; echo WILang::get('select'); echo WILang::get('http_info'); echo ' </span>


                        <label>'; echo WILang::get('asylum'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input type="hidden" name="asylum" id="asylum_seeker" class="btn-group-value" value="no"/>
                        <button type="button" id="asylum_true" name="asylum" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button type="button" id="asylum_false" name="asylum" value="no" class="btn activewhens" >'; echo WILang::get('no'); echo '</button>
                        
                    </div>
                    <span class="help-block">'; echo WILang::get('select'); echo WILang::get('http_info'); echo ' </span>
                    
                    
                    <label>'; echo WILang::get('reguse'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input type="hidden" id="refugee" name="refugee" class="btn-group-value" value="no" />
                        <button id="refugee_true" type="button" name="refugee" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button id="refugee_false" type="button" name="refugee" value="no" class="btn btn-no" >'; echo WILang::get('no'); echo '</button>
                    </div>
                    <span class="help-block">'; echo WILang::get('prevent'); echo ' <strong'; echo WILang::get('yes'); echo '</strong></span>
                    
                     <label>'; echo WILang::get('localauth'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input id="localauth" type="hidden" name="localauth" class="btn-group-value" value="no" />
                        <button id="localauth_true" type="button" name="localauth" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button id="localauth_false" type="button" name="localauth" value="no" class="btn btn-no" >'; echo WILang::get('no'); echo '</button>
                    </div>
                    <span class="help-block">'; echo WILang::get('localAuth'); echo '<strong>'; echo WILang::get('cookies'); echo '</strong></span>
                   
                     <label>'; echo WILang::get('fundsav'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input  id="fundsav" type="hidden" name="fundsav" class="btn-group-value" value="no" />
                        <button id="fundsav_true" type="button" name="fundsav" value="yes"  class="btn">'; echo WILang::get('yes') ; echo '</button>
                        <button id="fundsav_false" type="button" name="fundsav" value="no" class="btn btn-no">'; echo WILang::get('no') ; echo '</button>
                    </div>
                    <span class="help-block">'; echo WILang::get('funds'); echo '</strong></span>
                </div>
            </div>
            </div>

                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                    

                    <div class="col-sm-7 col-md-7 col-lg-7 col-xs-7">
                    <label for="address">';echo WILang::get('address'); echo '</label>
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                        <label for="first_line">'; echo WILang::get('first_line');  echo '</label>
                        <input id="first_line" type="text" value="10 globe lane" placeholder="first line" />
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                        <label for="second_line">'; echo WILang::get('second_line');  echo '</label>
                        <input id="second_line" type="text" value="dukinfield" placeholder="second line" />
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                        <label for="town">'; echo WILang::get('town');  echo '</label>
                        <input id="town" type="text" value="stalybridge" placeholder="town" />
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                        <label for="county">'; echo WILang::get('county'); echo '</label>
                        <input id="county" type="text" value="cheshire" placeholder="county" />
                    </div>
                    <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                        <label for="postcode">'; echo WILang::get('post');  echo '</label>
                        <input id="postcode" type="text" value="sk153hb" placeholder="postcode" />

                        <small>
                            ';echo WILang::get('script_info'); echo '
                            
                        </small>
                        </div>
                        <div class="col-sm-12 col-md-12 col-lg-12 col-xs-12">
                    <div class="form-group">
                        <label for="country">'; echo WILang::get('countryO'); echo '</label>';
                        $this->site->countries(); echo '
                        <small>'; echo WILang::get('webdom'); echo '
                            <strong>'; echo WILang::get('donot'); echo '</strong> 
                            '; echo WILang::get('write_path_info'); echo '
                        </small>
                    </div>
                    </div>
                    </div>
                    </div>


                                      <script type="text/javascript">

                       var sex = $("#gender").attr("value");
                       if (sex === "male"){
                        $("#sex_true").removeClass("btn-yes active")
                        $("#sex_false").addClass("btn-no");
                       }else if (sex === "female"){
                        $("#sex_false").removeClass("btn-no active")
                        $("#sex_true").addClass("btn-yes");
                       }


                       var asylum = $("#asylum_seeker").attr("value");
                       if (asylum === "yes"){
                        $("#asylum_true").removeClass("btn-yes active")
                        $("#asylum_false").addClass("btn-no");
                       }else if (asylum === "no"){
                        $("#asylum_false").removeClass("btn-no active")
                        $("#asylum_true").addClass("btn-yes");
                       }

                       var refugee = $("#refugee").attr("value");

                       if (refugee === "no"){
                        $("#refugee_true").removeClass("btn-yes active")
                        $("#refugee_false").addClass("btn-no");
                       }else if (refugee === "yes"){
                        $("#refugee_false").removeClass("btn-no active")
                        $("#refugee_true").addClass("btn-yes");
                       }

                       var localauth = $("#localauth").attr("value");
                       if (localauth === "no"){
                        $("#localauth_true").removeClass("btn-yes active")
                        $("#localauth_false").addClass("btn-no");
                       }else 
                       if (localauth === "yes"){
                        $("#localauth_false").removeClass("btn-no active")
                        $("#localauth_true").addClass("btn-yes");
                       }

                       var fundsav = $("#fundsav").attr("value");
                       if (fundsav === "no"){
                        $("#fundsav_true").removeClass("btn-yes active")
                        $("#fundsav_false").addClass("btn-no");
                       }else if (fundsav === "yes"){
                        $("#fundsav_false").removeClass("btn-no active")
                        $("#fundsav_true").addClass("btn-yes");
                       }
                   
                      </script>
                  

                    <a href="javascript:;" class="btn btn-as pull-right" onclick="WIClient.stepTwo()" type="button" id="required">
                        '; echo WILang::get('next') ; echo '
                        <i class="fa fa-arrow-right"></i>
                    </a>
                    <div class="clearfix"></div>
                </div>
            </div>
            </div>
             <div class="step-content hide" id="step_three">
                <h3>'; echo WILang::get('benefits'); echo '</h3>
                <hr>
                
                  <div class="form-group">
                     <label>'; echo WILang::get('jsa'); echo '</label>
                    <div class="btn-group"  data-toggle="buttons-radio">
                        <input type="hidden" name="jsa" id="jsa" class="btn-group-value" value="no"/>
                        <button type="button" id="jsa_true" name="jsa" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button type="button" id="jsa_false" name="jsa" value="no" class="btn btn-no activewhens" >'; echo WILang::get('no'); echo '</button>
                    </div>

                    <span class="help-block">'; echo WILang::get('select'); echo WILang::get('http_info'); echo ' </span>
                    </div>

                    <div class="form-group">
                     <div id="legend">

                        <label>'; echo WILang::get('income_sup'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input type="hidden" name="income_sup" id="income_sup" class="btn-group-value" value="no"/>
                        <button type="button" id="income_sup_true" name="income_sup" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button type="button" id="income_sup_false" name="income_sup" value="no" class="btn btn-no activewhens" >'; echo WILang::get('no'); echo '</button>
                    </div>

                    <span class="help-block">'; echo WILang::get('select'); echo WILang::get('http_info'); echo ' </span>
                    
                    <label>'; echo WILang::get('pension'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input type="hidden" id="pension" name="pension" class="btn-group-value" value="no" />
                        <button id="pension_true" type="button" name="pension" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button id="pension_false" type="button" name="pension" value="no" class="btn btn-no" >'; echo WILang::get('no'); echo '</button>
                    </div>
                    <span class="help-block">'; echo WILang::get('prevent'); echo ' <strong'; echo WILang::get('yes'); echo '</strong></span>
                    
                     <label>'; echo WILang::get('dla'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input id="dla" type="hidden" name="dla" class="btn-group-value" value="no" />
                        <button id="dla_true" type="button" name="dla" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button id="dla_false" type="button" name="dla" value="no" class="btn btn-no" >'; echo WILang::get('no'); echo '</button>
                    </div>
                    <span class="help-block"></span>
                   
                     <label>'; echo WILang::get('nass'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input  id="nass" type="hidden" name="nass" class="btn-group-value" value="no" />
                        <button id="nass_true" type="button" name="nass" value="yes"  class="btn">'; echo WILang::get('yes') ; echo '</button>
                        <button id="nass_false" type="button" name="nass" value="no" class="btn btn-no">'; echo WILang::get('no') ; echo '</button>
                    </div>
                    <span class="help-block">'; echo WILang::get('funds'); echo '</strong></span>

                    <label>'; echo WILang::get('incap'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input  id="incap" type="hidden" name="incap" class="btn-group-value" value="no" />
                        <button id="incap_true" type="button" name="incap" value="yes"  class="btn">'; echo WILang::get('yes') ; echo '</button>
                        <button id="incap_false" type="button" name="incap" value="no" class="btn btn-no">'; echo WILang::get('no') ; echo '</button>
                    </div>
                    <span class="help-block">'; echo WILang::get('funds'); echo '</strong></span>

                    <label>'; echo WILang::get('none'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input  id="none" type="hidden" name="none" class="btn-group-value" value="no" />
                        <button id="none_true" type="button" name="none" value="yes"  class="btn">'; echo WILang::get('yes') ; echo '</button>
                        <button id="none_false" type="button" name="none" value="no" class="btn btn-no">'; echo WILang::get('no') ; echo '</button>
                    </div>
                    <span class="help-block">'; echo WILang::get('funds'); echo '</strong></span>
                </div>
            </div>

                    <script type="text/javascript">
                       var jsa = $("#jsa").attr("value");
                       if (jsa === "no"){
                        $("#jsa_true").removeClass("btn-yes active")
                        $("#jsa_false").addClass("btn-no");
                       }else if (jsa === "yes"){
                        $("#jsa_false").removeClass("btn-no")
                        $("#jsa_true").addClass("btn-yes active");
                       }

                       var income_sup = $("#income_sup").attr("value");

                       if (income_sup === "no"){
                        $("#income_sup_true").removeClass("btn-yes active")
                        $("#income_sup_false").addClass("btn-no");
                       }else if (income_sup === "yes"){
                        $("#income_sup_false").removeClass("btn-no")
                        $("#income_sup_true").addClass("btn-yes active");
                       }

                       var pension = $("#pension").attr("value");
                       if (pension === "false"){
                        $("#pension_true").removeClass("btn-yes active")
                        $("#pension_false").addClass("btn-no");
                       }else 
                       if (pension === "true"){
                        $("#pension_false").removeClass("btn-no")
                        $("#pension_true").addClass("btn-yes active");
                       }

                       var dla = $("#dla").attr("value");
                       if (dla === "yes"){
                        $("#dla_true").removeClass("btn-yes active")
                        $("#dla_false").addClass("btn-no");
                       }else if (dla === "no"){
                        $("#dla_false").removeClass("btn-no")
                        $("#dla_true").addClass("btn-yes active");
                       }

                       var nass = $("#nass").attr("value");
                       if (nass === "no"){
                        $("#nass_true").removeClass("btn-yes active")
                        $("#nass_false").addClass("btn-no");
                       }else 
                       if (nass === "yes"){
                        $("#nass_false").removeClass("btn-no")
                        $("#nass_true").addClass("btn-yes active");
                       }

                       var none = $("#none").attr("value");
                       if (none === "no"){
                        $("#none_true").removeClass("btn-yes active")
                        $("#none_false").addClass("btn-no");
                       }else if (none === "yes"){
                        $("#none_false").removeClass("btn-no")
                        $("#none_true").addClass("btn-yes active");
                       }
                   
                      </script>

               

                <button class="btn btn-as pull-right" onclick="WIClient.stepThree();" type="button">
                    <span class="show" id="next">
                        '; echo WILang::get('next'); echo '
                        
                        <i class="fa fa-arrow-right" ></i>
                    </span>
                    <span class="hide" id="spin">
                        <i class="fa fa-circle-o-notch fa-spin"></i>
                       '; echo WILang::get('connecting'); echo '
                    </span>
                </button>
                <div class="clearfix"></div>
            </div>


            <div class="step-content hide" id="step_four">
                <h3>'; echo WILang::get('support'); echo '</h3>
                <hr>

               <div class="form-group">
                     <label>'; echo WILang::get('furn'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input type="hidden" name="furn" id="furn" class="btn-group-value" value="no"/>
                        <button type="button" id="furn_true" name="furn" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button type="button" id="furn_false" name="furn" value="no" class="btn btn-no activewhens" >'; echo WILang::get('no'); echo '</button>
                    </div>

                    <span class="help-block">'; echo WILang::get('select'); echo WILang::get('http_info'); echo ' </span>
                    </div>

                    <div class="form-group">
                     <div id="legend">

                        <label>'; echo WILang::get('bedding'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input type="hidden" name="bedding" id="bedding" class="btn-group-value" value="no"/>
                        <button type="button" id="bedding_true" name="bedding" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button type="button" id="bedding_false" name="bedding" value="no" class="btn btn-no activewhens" >'; echo WILang::get('no'); echo '</button>
                    </div>

                    <span class="help-block">'; echo WILang::get('select'); echo WILang::get('http_info'); echo ' </span>
                    
                    <label>'; echo WILang::get('cloths'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input type="hidden" id="cloths" name="cloths" class="btn-group-value" value=" no" />
                        <button id="cloths_true" type="button" name="cloths" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button id="cloths_false" type="button" name="cloths" value="no" class="btn btn-no" >'; echo WILang::get('no'); echo '</button>
                    </div>
                    <span class="help-block">'; echo WILang::get('prevent'); echo ' <strong'; echo WILang::get('yes'); echo '</strong></span>
                    
                     <label>'; echo WILang::get('training'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input id="training" type="hidden" name="training" class="btn-group-value" value="no" />
                        <button id="training_true" type="button" name="training" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button id="training_false" type="button" name="training" value="no" class="btn btn-no" >'; echo WILang::get('no'); echo '</button>
                    </div>
                    <span class="help-block">'; echo WILang::get('localAuth'); echo '<strong>'; echo WILang::get('cookies'); echo '</strong></span>
                   
                     <label>'; echo WILang::get('food'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input  id="food" type="hidden" name="food" class="btn-group-value" value="no" />
                        <button id="food_true" type="button" name="food" value="yes"  class="btn">'; echo WILang::get('yes') ; echo '</button>
                        <button id="food_false" type="button" name="food" value="no" class="btn btn-no">'; echo WILang::get('no') ; echo '</button>
                    </div>
                    <span class="help-block">'; echo WILang::get('funds'); echo '</strong></span>

                    <label>'; echo WILang::get('room'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input  id="room" type="hidden" name="room" class="btn-group-value" value="no" />
                        <button id="room_true" type="button" name="room" value="yes"  class="btn">'; echo WILang::get('yes') ; echo '</button>
                        <button id="room_false" type="button" name="room" value="no" class="btn btn-no">'; echo WILang::get('no') ; echo '</button>
                    </div>
                    <span class="help-block">'; echo WILang::get('funds'); echo '</strong></span>

                    <label>'; echo WILang::get('house'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input  id="house" type="hidden" name="house" class="btn-group-value" value="no" />
                        <button id="house_true" type="button" name="house" value="yes"  class="btn">'; echo WILang::get('yes') ; echo '</button>
                        <button id="house_false" type="button" name="house" value="no" class="btn btn-no">'; echo WILang::get('no') ; echo '</button>
                    </div>
                    <span class="help-block">'; echo WILang::get('funds'); echo '</strong></span>
                </div>
            </div>


                                      <script type="text/javascript">
                       var furn = $("#furn").attr("value");
                       if (furn === "no"){
                        $("#furn_true").removeClass("btn-yes active")
                        $("#furn_false").addClass("btn-no");
                       }else if (furn === "yes"){
                        $("#furn_false").removeClass("btn-no")
                        $("#furn_true").addClass("btn-yes active");
                       }

                       var bedding = $("#bedding").attr("value");

                       if (bedding === "no"){
                        $("#bedding_true").removeClass("btn-yes active")
                        $("#bedding_false").addClass("btn-no");
                       }else if (bedding === "yes"){
                        $("#bedding_false").removeClass("btn-no")
                        $("#bedding_true").addClass("btn-yes active");
                       }

                       var cloths = $("#cloths").attr("value");
                       if (cloths === "no"){
                        $("#cloths_true").removeClass("btn-yes active")
                        $("#cloths_false").addClass("btn-no");
                       }else 
                       if (cloths === "yes"){
                        $("#cloths_false").removeClass("btn-no")
                        $("#cloths_true").addClass("btn-yes active");
                       }

                       var food = $("#food").attr("value");
                       if (food === "yes"){
                        $("#food_true").removeClass("btn-yes active")
                        $("#food_false").addClass("btn-no");
                       }else if (food === "no"){
                        $("#food_false").removeClass("btn-no")
                        $("#food_true").addClass("btn-yes active");
                       }

                       var room = $("#room").attr("value");
                       if (room === "yes"){
                        $("#room_true").removeClass("btn-yes active")
                        $("#room_false").addClass("btn-no");
                       }else if (room === "no"){
                        $("#room_false").removeClass("btn-no")
                        $("#room_true").addClass("btn-yes active");
                       }

                       var house = $("#house").attr("value");
                       if (house === "yes"){
                        $("#house_true").removeClass("btn-yes active")
                        $("#house_false").addClass("btn-no");
                       }else if (house === "no"){
                        $("#house_false").removeClass("btn-no")
                        $("#house_true").addClass("btn-yes active");
                       }
                   
                      </script>
               

                <button class="btn btn-as pull-right" onclick="WIClient.stepFour();" type="button">
                    <span class="show" id="next">
                        '; echo WILang::get('next'); echo '
                        <i class="fa fa-arrow-right" ></i>
                    </span>
                    <span class="hide" id="spin">
                        <i class="fa fa-circle-o-notch fa-spin"></i>
                        '; echo WILang::get('connecting'); echo '
                    </span>
                   
                </button>
                 <span class="show" id="mess">
                        <i class="fa"></i>
                    </span>
                <div class="clearfix"></div>
            </div>

            <div class="step-content hide" id="step_five">
                <h3>'; echo WILang::get('ref'); echo '</h3>
                <hr>


                     <div class="form-group">
                        <label for="name">'; echo WILang::get('name'); echo '</label>
                        <input type="text" class="form-control" id="name"
                               v-model="website.name" value="Doe">
                    </div>

                     <div class="form-group">
                        <label for="agency">'; echo WILang::get('agency'); echo '</label>
                        <input type="text" class="form-control" id="agency"
                               v-model="agency.name" value="Doe">
                    </div>

                     <div class="form-group">
                        <label for="contact_num">'; echo WILang::get('contact_num'); echo '</label>
                        <input type="text" class="form-control" id="contact_num"
                               v-model="website.contact_num" value="079566854">
                    </div>
                    

                    <button class="btn btn-as pull-right" onclick="WIClient.stepFive();">
                        '; echo WILang::get('next') ; echo '
                        <i class="fa fa-arrow-right"></i>
                    </button>
                <div class="clearfix"></div>
            </div>


            <div class="step-content hide" id="step_six">
                <h3>'; echo WILang::get('admin'); echo '</h3>
                <hr>

                <br>
                <div class="form-group">
                        <label for="adname">'; echo WILang::get('adname'); echo '</label>
                        <input type="text" class="form-control" id="adname"
                               v-model="website.adname" value="Doe">
                    </div>

                     <div class="form-group">
                        <label for="idprov">'; echo WILang::get('idprov'); echo '</label>
                        <input type="text" class="form-control" id="idprov"
                               v-model="website.idprov" value="Doe">
                    </div>

                     <div class="form-group">
                        <label for="clibud">'; echo WILang::get('clibud'); echo '</label>
                        <input type="text" class="form-control" id="clibud"
                               v-model="website.clibud" value="">
                    </div>

                     <div class="form-group">
                     <label>'; echo WILang::get('furnPak'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input type="hidden" name="furnPak" id="furnPak" class="btn-group-value" value="no"/>
                        <button type="button" id="furnPak_true" name="furnPak" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button type="button" id="furnPak_false" name="furnPak" value="no" class="btn btn-no activewhens" >'; echo WILang::get('no'); echo '</button>
                    </div>

                    <span class="help-block">'; echo WILang::get('select'); echo WILang::get('http_info'); echo ' </span>
                    </div>

                    <div class="form-group">
                     <label>'; echo WILang::get('gifting'); echo '</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input type="hidden" name="gifting" id="gifting" class="btn-group-value" value="no"/>
                        <button type="button" id="gifting_true" name="gifting" value="yes"  class="btn">'; echo WILang::get('yes'); echo '</button>
                        <button type="button" id="gifting_false" name="gifting" value="no" class="btn btn-no activewhens" >'; echo WILang::get('no'); echo '</button>
                    </div>

                    <span class="help-block">'; echo WILang::get('select'); echo WILang::get('http_info'); echo ' </span>
                    </div>

                    <div class="form-group">
                        <label for="databy">'; echo WILang::get('databy'); echo '</label>
                        <input type="text" class="form-control" id="databy"
                               v-model="website.databy" value="Doe">
                    </div>
                                                          <script type="text/javascript">


                       var furnPak = $("#furnPak").attr("value");
                       if (furnPak === "yes"){
                        $("#furnPak_true").removeClass("btn-yes active")
                        $("#furnPak_false").addClass("btn-no");
                       }else if (furnPak === "no"){
                        $("#furnPak_false").removeClass("btn-no")
                        $("#furnPak_true").addClass("btn-yes active");
                       }

                       var gifting = $("#gifting").attr("value");
                       if (gifting === "yes"){
                        $("#gifting_true").removeClass("btn-yes active")
                        $("#gifting_false").addClass("btn-no");
                       }else if (gifting === "no"){
                        $("#gifting_false").removeClass("btn-no")
                        $("#gifting_true").addClass("btn-yes active");
                       }
                   
                      </script>


                <br>

                 <button class="btn btn-as pull-right" onclick="WIClient.stepSix();">
                         '; echo WILang::get('next') ; echo '
                        <i class="fa fa-arrow-right"></i>
                    </button>
                <div class="clearfix"></div>
            </div>


            <div class="step-content hide" id="step_seven">
                <h3>'; echo WILang::get('Next'); echo '</h3>
                <hr>
                <p id="date" class="hide"></p>
<div id="clock" class="hide">
  <p class="unit" id="time"></p>

</div>
                
                <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                   <div class="form-group">
                        <label for="action_taken">'; echo WILang::get('action_taken'); echo '</label>
                        <textarea type="text" class="form-control" id="action_taken"
                               v-model="website.action_taken" value="Doe">
                    </textarea>
                    </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                   <div class="form-group">
                        <label for="outcome">'; echo WILang::get('outcome'); echo '</label>
                        <textarea type="text" class="form-control" id="outcome"
                               v-model="website.outcome" value="Doe"></textarea>
                    </div>
                    </div>

                    <div class="col-sm-6 col-md-6 col-lg-6 col-xs-6">
                   <div class="form-group">
                        <label for="full_name">'; echo WILang::get('full_name'); echo '</label>
                        <input type="text" class="form-control" id="full_name"
                               v-model="website.full_name" value="Doe">
                    </div>
                    </div>

                <br>

               <button class="btn btn-as pull-right" onclick="WIClient.install();">
                        <span class="show" id="install">
                               <i class="fa fa-play"></i>
                            '; echo WILang::get('insta'); echo '
                        </span>
                            <span class="hide" id="installing">
                            <i class="fa fa-circle-o-notch fa-spin"></i>
                            '; echo WILang::get('I'); echo '
                        </span>
                    </button>
                <div class="clearfix"></div>
            </div>

             <div class="step-content hide" id="step_eight">
                <h3>'; echo WILang::get('completed'); echo '</h3>
                <hr>
                <p><strong>'; echo WILang::get('welldone'); echo '</strong></p>
                    
                </p>


                <br>

                <button class="btn btn-as pull-right" onclick="WIClient.stepreturn();">
                        <span class="show" id="next">
                        '; echo WILang::get('next'); echo '
                        
                        <i class="fa fa-arrow-right" ></i>
                    </span>
                    </button>
                <div class="clearfix"></div>
            </div>



        </div>
    </div>
</div>
<script src="WICore/WIJ/WICheckout.js"></script>';
    }


}