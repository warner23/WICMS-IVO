<?php
#[\AllowDynamicProperties]
class WIUpgrade
{

  private $userId;

  private $WIdb;

  function __construct()
  {
    $this->WIdb = WIdb::getInstance();
    $this->User = new WIUser(WISession::get('user_id'));
  }



  public function upgrade($id)
  {
    $results = $this->WIdb->select('SELECT * FROM `wi_paypment_plan` WHERE `id`=:id', array(
      "id"  => $id
    ));

    $options = $this->WIdb->select('SELECT * FROM `wi_payment_options`');
    if(count($results) > 0){
   
        echo '<div class="box-title">
              <h3 id="upgrade_title">' . $results[0]['user_acc'] .'</h3>
            </div>
            <div class="box-price">
              <h3 id="upgrade_title">' .CURRENCY_SYMBOL . ' ' . $results[0]['price'] .'</h3>
            </div>
            <div class="box-text" id="upgrade_desc">
                ' . $results[0]['description'] .'
            </div><div class="box-text" id="you_get">';
          echo'<ul style="width: 50%;margin-left: 36%;padding: 3%;font-size: 19px;">';
             foreach ($options as $option) {
         
          foreach ($results as $plan) {
            $opt = $plan['option_'.$option['id'].''];
            if($opt == "true"){
             echo'<li>'. $option['option'] .'</li>';
            }
            
            }
           
          
        }
          echo '</ul>';

            echo '</div>
            <div class="box-text" id="privileges">
                
            </div>';
      


      
    }else{
      echo 'nothing to show';
    }

  }

  public function accepted($id, $plan, $price, $role, $createdAt)
  {
    $user_id = $this->User->id();
    // generate receipt

     $receipt = '<div class="row-fluid">
    <!-- Middle Section -->
    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div id="loadingAlert"
             class="card"
             style="display: none;">
            <div class="card-body">
                <div class="alert alert-info block"
                     role="alert">
                    Loading....
                </div>
            </div>
        </div>
        <form id="orderConfirm"
              class="form-horizontal"
              style="display: none;">
            <h3>Your payment is authorized.</h3>
            <h4>Confirm the order to execute</h4>
            <hr>
            <div class="form-group">
                <label class="col-sm-5 control-label">Shipping Information</label>
                <div class="col-sm-7">
                    <p id="confirmRecipient"></p>
                    <p id="confirmAddressLine1"></p>
                    <p id="confirmAddressLine2"></p>
                    <p>
                        <span id="confirmCity"></span>,
                        <span id="confirmState"></span> - <span id="confirmZip"></span>
                    </p>
                    <p id="confirmCountry"></p>
                </div>
            </div>
            <div class="form-group">
                <label for="shippingMethod" class="col-sm-5 control-label">Shipping Type</label>
                <div class="col-sm-7">
                    <select class="form-control" name="shippingMethod" id="shippingMethod">
                        <optgroup label="United Parcel Service" style="font-style:normal;">
                            <option value="8.00">
                                Worldwide Expedited - $8.00</option>
                            <option value="4.00">
                                Worldwide Express Saver - $4.00</option>
                        </optgroup>
                        <optgroup label="Flat Rate" style="font-style:normal;">
                            <option value="2.00" selected>
                                Fixed - $2.00</option>
                        </optgroup>
                    </select>
                </div>
            </div>
            <hr>
            <div class="form-group">
                <div class="col-sm-offset-5 col-sm-7">
                    <label class="btn btn-primary" id="confirmButton">Complete Payment</label>
                </div>
            </div>
        </form>
        <div id="receipt">
        <form id="orderView"
              class="form-horizontal"
              style="display: block;">

            <div class="completed"><h3>Your payment is complete</h3>


            <div id="emailoption">
                <div id="emailInput">
                <h4> <span id="emailaddy">Click here to email email a copy of the receipt to yourself</span></h4><a href="javascript:void(0);" class="btn email" onclick="WICheckout.addEmailing();">Email Receipt</a>
                </div>
                <div id="Einput" class="hide">
                   <div class="col-xs-6 col-sm-6 col-lg-6">
                <input name="email" id="receiptEmail">
                   </div>
                   <div class="col-xs-3 col-sm-3 col-lg-3">
                <a href="javascript:void(0);"  id="sendEmail" class="btn email" onclick="WICheckout.emailReceipt();">Send</a>
                   </div>
                </div>
            </div>
            <hr>';
         
            $receipt .= '
            <div class="form-group" style="margin-left: 34%;">'; 


            



        $receipt .= '<div class="form-group">
                    <label class="col-sm-5 control-label">Transaction Details</label>
                    <div class="col-sm-7">';
                    

                        $receipt .='<p>Transaction ID: <span id="viewTransactionID">' . $id . '</span></p>
                        <p>Payment Total Amount: <span id="viewFinalAmount"> 
                        ' . $price . '
                        </span> </p>
                        <p>Currency Code: <span id="viewCurrency">
                        ' . $view_currentcy . '
                        </span></p>
                        <p>Payment Status: <span id="viewPaymentState">
                        result.status
                        PAID
                        </span></p>

                    </div>
                </div>
            </div>
            <hr>
            </div>
            </form>

        </div>
            <div style="width: 50%;margin-left: 34%;" class="href"><h3> Click <a href="Courses.php">here </a> to return to Home Courses Page</h3></div>
        
    </div>
</div>
';

   // add to db
   $this->WIdb->insert("wi_transactions", array(
    "userId" => $user_id,
    "orderId"  => $id,
    "reference"  => $plan,
    "type"   => "Monthly Subscription",
    "mode"  => "payapl",
    "status"  => "paid",
    "createdAt"  => $createdAt,
    "receipt"   => $receipt,
    "receipt_id"  => $id
   )); 

   // change role
   $roleId = self::findRoleId();
   $id = $this->WIdb->update('wi_members', array("user_role" => $roleId), 'user_id=:id', array("id" => $user_id));
  }


  public function findRoleId($role)
  {
    $role = $this->WIdb->select('SELECT `role_id` FROM `wi_user_roles` WHERE `role`=:role', array('role' => $role));

    return $role;
  }

}

?>