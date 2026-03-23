<?php
#[\AllowDynamicProperties]
class WIMembership
{

  private $userId;

  private $WIdb;

  function __construct()
  {
    $this->WIdb = WIdb::getInstance();
    $this->Reg  = new WIRegister();
    $this->site = new WISite();
    $this->User  = new WIUser(WISession::get('user_id'));
  }


  public function account()
  {
    $plans = $this->WIdb->select('SELECT * FROM `wi_paypment_plan`');
    $options = $this->WIdb->select('SELECT * FROM `wi_payment_options`');
    if(count($plans) > 0){

      $userAcc = $this->User->getRole();
        echo '<div class="col-lg-12 col-md-12 col-sm-12 text-left">
    <div class="intro_box" style="background-color:white;">
     <h1>You Currently have a <span>' .$userAcc. ' account</span></h1>
      <p>Belwo are some options if you would like to upgrade your account/</p>
      </div>
       <button onlick="WIProfile.back();">Back</button>

       </div>
       <div class="col-lg-8 col-md-8 col-sm-8" style="margin-left: 13%; background-color:white">
       <link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
          <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/js/bootstrap.min.js"></script>
         <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
         <div class="membership-pricing-table">
         <style>
         .plans{
    width: 19%;
    float: left;
         }

         li .action-header{
           float: left;
         }
         </style>
         <ul class="member-header" style="width:100%;height: 148px;">
         <li style="width: 230px;height: 148px;float: left;"></li>';

         foreach ($plans as $plan) {
           $acc = $plan['user_acc'];
          if($userAcc == $acc){
           echo '<li class="plan-header-'. $plan['class'] .' plans">
                <div class="pricing-plan-name-'. $plan['id'] .'">'. $plan['user_acc'] .'</div>
                <div class="pricing-plan-price-'. $plan['id'] .'">
                    <sup>'. CURRENCY_SYMBOL .' </sup>'. $plan['price'] .'<span>.00</span>
                </div>
                <div class="pricing-plan-period">month</div>
                </li>';
          }else{
            if ($plan['class'] == "standard") {

               echo '<li class="plan-header-'. $plan['class'] .'  plans">
                  <div class="header-plan-inner">
                  <span class="recommended-plan-ribbon">RECOMMENDED</span>
                <div class="pricing-plan-name-'. $plan['id'] .'">'. $plan['user_acc'] .'</div>
                <div class="pricing-plan-price-'. $plan['id'] .'">
                    <sup>'. CURRENCY_SYMBOL .' </sup>'. $plan['price'] .'<span>.00</span>
                </div>
                <div class="pricing-plan-period">month</div>
                </div>
                </li>';
            }else{
              echo '<li class="plan-header-'. $plan['class'] .'  plans">
                <div class="pricing-plan-name-'. $plan['id'] .'">'. $plan['user_acc'] .'</div>
                <div class="pricing-plan-price-'. $plan['id'] .'">
                    <sup>'. CURRENCY_SYMBOL .' </sup>'. $plan['price'] .'<span>.00</span>
                </div>
                <div class="pricing-plan-period">month</div>
                </li>';
            }

          }

        }

        echo '</ul>';

        echo '<ul style="width:100%;height: 67px;">
         <li style="width: 230px;height: 67px;float: left;"></li>';

          $counter = 1;
           foreach ($plans as $plan) {
            $acc = $plan['user_acc'];

          if($userAcc == $acc){
            if($counter == 1){
            echo '<li class="action-header">
                <a class="btn btn-info" href="javascript:void(0);" onclick="WIMembership.downgrade(`'. $plan['id'] .'`,`'. $plan['user_acc'] .'`,`'. $plan['price'] .'`);">
                            Downgrade
                        </a>
                </li>';
          }else{
            echo '<li class="action-header">
                <div class="current-plan">
                            <div class="with-date">Current Plan</div>
                            <div><em class="smaller block">renews Feb 19, 2025</em></div>
                        </div> 
                </li>';
          }

           
          }else{
             if($counter == 1){
            echo '<li class="action-header">
                <a class="btn btn-info" href="javascript:void(0);" onclick="WIMembership.downgrade(`'. $plan['id'] .'`,`'. $plan['user_acc'] .'`,`'. $plan['price'] .'`);">
                            Downgrade
                        </a>
                </li>';
          }else{

             echo '<li class="action-header">
                <a class="btn btn-info"href="javascript:void(0);" onclick="WIMembership.upgrade(`'. $plan['id'] .'`,`'. $plan['user_acc'] .'`,`'. $plan['price'] .'`);">
                            Upgrade
                        </a>
                </li>';
           }
          }
          $counter++;
          
        }

        echo '</ul>';

        foreach ($options as $option) {
          echo '<ul style="width: 100%;height: 50px;">
           <li>'. $option['option'] .'</li>
           ';
          foreach ($plans as $plan) {
            $opt = $plan['option_'.$option['id'].''];
            if($opt == "true"){
            echo '<li><span class="icon-yes glyphicon glyphicon-ok-circle"></span></li>';
            }else{
              echo '<li><span class="icon-no glyphicon glyphicon-remove-circle"></span></li>';
            }
            
            }
          echo '</ul>';
          
        }
         
         echo '</div>';

    }else{
      echo "Nothibng to show here.";
    }
  }

}

?>