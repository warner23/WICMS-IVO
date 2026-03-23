<?php
#[\AllowDynamicProperties]
class WIAccount
{

  private $userId;

  private $WIdb;

  function __construct()
  {
    $this->WIdb = WIdb::getInstance();
    $this->Reg  = new WIRegister();
    $this->site = new WISite();
  }

  public function myAccount()
  {
    echo '<div class="col-lg-8 orders">

        <div class="col-lg-4 col-sm-4 col-xs-4 col-md-4 box">
        <a href="transactions.php">
        <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12 bor">
        <div class="col-lg-4 col-sm-4 col-xs-4 col-md-4">
        <div class="img">
        <img src="../WIAdmin/WIMedia/Img/profile/orders.png">
        </div>
        </div>
        <div class="col-8 col-sm-8 col-xs-8 col-md-8">
        <div class="top"><h3>Your Orders</h3></div>
        <div class="">Transactions, orders placed</div>
        </div>
      </div></a>
      </div>

        <div class="col-lg-4 col-sm-4 col-xs-4 col-md-4 box">
        <a href="usersecurity.php">
        <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12 bor">
        <div class="col-lg-4 col-sm-4 col-xs-4 col-md-4">
        <div class="img">
        <img src="../WIAdmin/WIMedia/Img/profile/login.jpg">
        </div>
        </div>
        <div class="col-8 col-sm-8 col-xs-8 col-md-8">
        <div class="top"><h3>Login and Security</h3></div>
        <div class="">User Security</div>
        </div>
        </div></a>
        </div>
        
        <div class="col-lg-4 col-sm-4 col-xs-4 col-md-4 box">
        <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12 bor">
        <a href="membership.php">
        <div class="col-lg-4 col-sm-4 col-xs-4 col-md-4">
        <div class="img">
        <img src="../WIAdmin/WIMedia/Img/profile/membership.jpg">
        </div>
        </div>
        <div class="col-8 col-sm-8 col-xs-8 col-md-8">
        <div class="top"><h3>Membership</h3></div>
        <div class="">
         Transactions, orders placed
        </div>
        </div>
        </div></a>
          </div>

      <div class="col-lg-4 col-sm-4 col-xs-4 col-md-4 box">
          <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12 bor">
          <a href="userpayments.php">
        <div class="col-lg-4 col-sm-4 col-xs-4 col-md-4">
        <div class="img">
        <img src="../WIAdmin/WIMedia/Img/profile/payments.png">
        </div>
        </div>
        <div class="col-8 col-sm-8 col-xs-8 col-md-8">
        <div class="top"><h3>Your Payments</h3></div>
        <div class="">
         Transactions, orders placed
        </div>
        </div>
        </div></a>
          </div>




          <div class="col-lg-4 col-sm-4 col-xs-4 col-md-4 box">
          <div class="col-lg-12 col-sm-12 col-xs-12 col-md-12 bor">
          <a href="support.php">
        <div class="col-lg-4 col-sm-4 col-xs-4 col-md-4">
        <div class="img">
        <img src="../WIAdmin/WIMedia/Img/profile/help.png">
        </div>
        </div>
        <div class="col-8 col-sm-8 col-xs-8 col-md-8">
        <div class="top"><h3>Help and Support</h3></div>
        <div class="">Transactions, orders placed</div>
        </div>
          </div></a>
          </div>

          </div>';
  }

}

?>