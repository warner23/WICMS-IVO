<?php


/**
* 
*/
class WICheckout 
{

	function __construct()
	{
		$this->WIdb = WIdb::getInstance();
	    $this->login = new WILogin();
        $this->site = new WISite();
        $this->user   = new WIUser(WISession::get('user_id'));
        $this->Shop  = new WIShop();
        //$this->Paypal = new WIPaypalExpress();
	}

	public function checkout()
	{

		$user_id = $this->user->id();

		$result = $this->WIdb->select(
                    "SELECT * FROM `wi_cart` WHERE userId =:u",
                     array(
                       "u" => $user_id
                     )
                  );

		if($this->login->isLoggedIn()){
			self::UserisLoggedIn($result);	
        }else{
       echo "please log in or register";
        }

	}


	public function UserisLoggedIn($cart)
	{
		echo '<div class="wizard col-lg-12 col-md-12 col-sm-12">
            <div class="steps">
                <ul>';

        $result = $this->WIdb->select("SELECT * FROM `wi_checkout_steps`");

		foreach($result as $res){
			echo '<li>
                   <a :class="active">
                   <div class="stepNumber '. $res['classification'].'" id="'. $res['step_number'].'"><i class="'. $res['class'].'"></i></div>
                   <span class="stepDesc text-small">'; echo WILang::get($res['name']); 
                    echo '
                      </span>
                        </a>
                    </li>';
		}

		echo '</ul>
            </div>';

            foreach($result as $res){
			echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 step-content '.$res['show'].'" id="'. $res['identifier'].'">';
			self::steps($res['step_number'], $cart);

			echo '</div>';
		}
            echo '</div>';
	}

	public function steps($step, $cart)
	{
		$result = $this->WIdb->select("SELECT * FROM `wi_checkout_steps` WHERE `step_number`=:step", 
			array(
			"step" => $step
			)
			);

		$position = $result[0]['name'];

		if($position == "shipping"){
			self::shipping($cart);
		}elseif ($position == "payment") {
		    self::payment($cart);
		}elseif ($position == "confirmation") {
			self::confirmation($cart);
		}


	}


	public function shipping($cart)
	{
		$user_id = $this->user->id();
		$result = $this->WIdb->select("SELECT * FROM `wi_cust_address` WHERE `user_id`=:u", 
			array(
			"u" => $user_id
			)
			);

		echo '
		<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" >
                <h3>'; WILang::get('shipping'); echo '</h3>
                <hr>
                <br>';

                echo '<form  class="form-horizontal address">
                    <fieldset>
                      <div id="legend">
                        <legend class="center">Address</legend>
                      </div>';

				$addresses = count($result);
				if($addresses > 0){
					
				}else{
					echo '<!--SHIPPING METHOD-->
					<div id="main_address_shipping">
                    <div class="panel panel-info">
                        <div class="panel-heading">Main Address</div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="col-md-12">
                                    <h4>Shipping Address</h4>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12"><strong>Country:</strong></div>
                                <div class="col-md-12">';
                                  $this->site->countries(); 
                                  echo '
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6 col-xs-12">
                                    <strong>First Name:</strong>
                                    <input type="text" id="fname" name="first_name" class="form-control" value="" />
                                </div>
                                <div class="span1"></div>
                                <div class="col-md-6 col-xs-12">
                                    <strong>Last Name:</strong>
                                    <input type="text" id="lname" name="last_name" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12"><strong>Address:</strong></div>
                                <div class="col-md-12">
                                    <input type="text" id="address" name="address" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12"><strong>City:</strong></div>
                                <div class="col-md-12">
                                    <input type="text" id="city" name="city" class="form-control" value="" />
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12"><strong>Zip / Postal Code:</strong></div>
                                <div class="col-md-12">
                                    <input type="text" id="postcode" name="post_code" class="form-control" value="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-12"><strong>Phone Number:</strong></div>
                                <div class="col-md-12">
                                <input type="text" id="phone" name="phone_number" class="form-control" value="" /></div>
                            </div>

                            <div class="form-group">
                                <div class="col-md-12"><strong>Address Reference:</strong></div>
                                <div class="col-md-12">
                                <input type="text" id="addy_ref" name="addy_ref" class="form-control" value="" />
                                </div>
                            </div>


                            <div class="form-group">
                                <div class="col-md-12"><strong></strong></div>
                                <div class="col-md-12">
                                <label>Main Address</label>
                    <div class="btn-group" data-toggle="buttons-radio">
                        <input type="hidden" name="main_address" class="btn-group-value" id="mn-address" value="true" />
                        <label class="switch">
                        <input type="checkbox" id="main_address" checked>
                        <span class="slider round" id="main_addy">ON</span>
                        
                      </label>
                    </div>
                                </div>
                            </div>
                        </div>

                        <script type="text/javascript">
                          var main_address = $("#mn-address").attr("value");
                       if (main_address === "false"){
                        $("#main_address").prop("checked", false);
                        $("#main_addy").text("OFF");
                        $("#main_addy").css("padding-left", "50%");
                       }else if (main_address === "true"){
                        $("#main_address").prop("checked", true);
                        $("#main_addy").text("ON");
                       }

                         </script>
                         </div>

                    <!--SHIPPING METHOD END-->

                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-md-4 col-lg-8 col-xs-4">
                           <button id="addy" class="btn btn-success">Save</button> 
                        </div>
                      </div>

                      <div class="results" id="aresults"></div>
                    </fieldset>
                  </form> ';
				}

				echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12"><ul>';
                $count = 0;
        $total = 0;
        $len = count($cart);
				foreach($cart as $c){
                     $count++;
                    $subtotal = $c['price'] * $c['quantity'];
                $total = $total+ $subtotal;
              	echo '<li>
              	<div class="row">
				<div class="col-sm-2 hidden-xs">
				<img src="../../../WIAdmin/WIMedia/Img/shop/products/' . $c['photo'] . '" alt="..." class="img-responsive"/></div>
				<div class="col-sm-10">
				<h4 class="nomargin title">' . $c['title'] . '</h4>
				</div>
                <div id="new_address"></div>
				<div class="addressing">';

				if($addresses > 0){
					echo '<label for="delivery_address">Choose a delivery address:</label>
						<select name="delivery_address" id="delivery_address">';
						foreach ($result as $res) {
							echo '<option value="'. $res['address_ref'] .'">'. $res['address_ref'] .'</option>';
						}
							 
							  
							echo '</select>
							<a href="javascript:void(0)" onclick="WICheckout.addAddress()">add new address</a>';
				}else{
					echo '<button class="btn">Add address</button>';
				}
				echo '</div>
                <script type="text/javascript">
                       $(document).ready(function(){

                        $("select#shipping").on("change", function() {
                           // console.log( this.value );

                            WICheckout.changeShipping(this.value);

                          })
                       });
                     </script>

                <div class="pricing" id="pricing">';
                    echo 'Item Price : £
                    <span class="individual_price">
                    <input type="text" class="item_price" value="' .$c['price'] . '" readonly>
                    </span>
                    Qty
                <span class="qty">
                    <input type="text" class="item_qty" value="' .$c['quantity'] . '" readonly>
                    </span>
                Total Price : £
                <span class="total_price">
                <input type="text" class="tot" id="total" value="' .$c['total_amount'] . '" readonly>
                </span>';
                
                echo '</div>
				</div>

              	</li>';

                if($count == $len){
                    $total;
                }
                       
              }

              echo '</ul>
              
               <div class="ship" id="shipping_method">';
                $this->Shop->shippingMethod();
                echo '</div>


              <strong>Total: £</strong>
            <span id="total">' .$total .'</span>
                </div>';
		

                echo '<a href="javascript:;" onclick="WICheckout.stepOne();" class="btn btn-as pull-right" type="button">
                    '; echo WILang::get('next'); echo '

                    <i class="fa fa-arrow-right"></i>
                </a>
                <div class="clearfix"></div>
            </div>';
	}

	public function payment($cart)
	{

		echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" > 

            <div class="alert alert-danger hide" id="snap" >
                    <strong>'; echo WILang::get('next'); echo '</strong> 
                </div>

                
                    <h3>'; echo WILang::get('payment'); echo '</h3>
                <hr>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                
               <!-- // payment api code for PayPal
                // DO NOT  ALTER !!!-->


                 <meta name="viewport" content="width=device-width, initial-scale=1">
                 <!-- Ensures optimal rendering on mobile devices. -->
  <meta http-equiv="X-UA-Compatible" content="IE=edge" /> 
  <!-- Optimal Internet Explorer compatibility -->

  <script
    src="https://www.paypal.com/sdk/js?client-id=' .PAYPAL_CLIENT_ID . '&currency=' . CURRENCY . '">
  </script>

              <div id="paypalCheckoutContainer"></div>
              <div id="paypalpay" class="hide"></div>
              
            <!-- PayPal In-Context Checkout script -->
            <script type="text/javascript">';

              echo "paypal.Buttons({

        // Set your environment
        env: '". PAYPAL_ENVIRONMENT ."',


        // Set style of buttons
        style: {
            layout: 'horizontal',   // horizontal | vertical
            size:   'responsive',   // medium | large | responsive
            shape:  'pill',         // pill | rect
            color:  'gold',         // gold | blue | silver | black,
            fundingicons: false,    // true | false,
            tagline: false          // true | false,
        },

        // Wait for the PayPal button to be clicked
        createOrder: function() {

            let currency = '".  CURRENCY ."'
            let formData = new FormData();

            $( '.total_price' ).each(function() {
              formData.append('item_amt', $('.item_price').attr('value'))
            });

            $( '.total_price' ).each(function() {
              formData.append('item_title', $('.title').text())
            });

            $( '.qty' ).each(function() {
              formData.append('item_qty', $('.item_qty').attr('value'))
            });

            formData.append('total_amt', $('#total').value);
            formData.append('return_url',  '". PAYPAL_CALLBACK ."?commit=false');
            formData.append('cancel_url', '". PAYPAL_CANCEL_URL."');

            return fetch(
                '".URL['services']['orderCreate']."',
                {
                    method: 'POST',
                    headers: {
                    'content-type': 'application/json',
                    'access_token': '" . PAYPAL_ACCESS_TOKEN."'
                },
                    body: formData
                }
            ).then(function(response) {
                console.log(response);
                return response.json();
            }).then(function(resJson) {
                console.log(resJson);
                
            console.log('Order ID: '+ resJson.data.id);
            return resJson.data.id;
            });
        },

        // Wait for the payment to be authorized by the customer
        onApprove: function(data, actions) {
            return fetch(
                '". URL['services']['orderGet'] ."',
                {
                    method: 'GET'
                }
            ).then(function(res) {
                return res.json();
            }).then(function(res) {
                WICheckout.pushpayment(res);

            });
        }

    }).render('#paypalCheckoutContainer');

</script>";

            echo '</div>
<a href="javascript:void(0);" class="btn btn-as pull-right" onclick="WICheckout.stepTwo()" type="button" id="required">'; 
                        echo WILang::get('next') ; echo '
                        <i class="fa fa-arrow-right"></i>
                    </a>
                    <div class="clearfix"></div>

                 </div>
                    
                ';
	}

	public function confirmation()
	{
		echo '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12" > 

            <div class="alert alert-danger hide" id="snap" >
                    <strong>'; echo WILang::get('next'); echo '</strong> 
                </div>

                
                    <h3>'; echo WILang::get('confirmation'); echo '</h3>
                <hr>
                
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div id="confirmation_t">
                </div>
                </div>

       
                <div class="clearfix"></div>
            ';
	}

	public function add_Address($Main_address)
	{
		$address = $Main_address['UserData'];

		$user_id = $this->user->id();

		$this->WIdb->insert('wi_cust_address', array(
			"user_id" => $user_id,
            "fname"     => strip_tags($address['first_name']),
            "lname"  => strip_tags($address['last_name']),
            "address"  => strip_tags($address['addy']),
            "city" => strip_tags($address['city']),
            "postcode" => strip_tags($address['post_code']),
            "country" => strip_tags($address['country']),
            "address_ref" => strip_tags($address['address_ref']),
            "main_addy" => $address['main_addy'],
            "phone" => strip_tags($address['phone'])
        ));

            $result = array(
                "status" => "successful"
            );
            
            //output result
            echo json_encode ($result);   
	}


    public function cart()
    {
        $user_id = WISession::get('user_id');
        
        $result = $this->WIdb->select(
                    "SELECT * FROM `wi_cart`
                     WHERE `user_id` = :u",
                     array(
                       "u" => $user_id
                     )
                  );

        echo "item_list: {
                items: [
                    {";
        $count=0;
        $len = count($result);                         
        foreach ($result as $res) {
            $count++;

            if($count == $len){
                        echo "name: '".$res['product_title'] ."',
              image: '".$res['product_image'] ."',
              quantity: '".$res['quantity'] ."',
              price: '".$res['price'] ."',
              tax: '0.01',
              currency: 'GBP'
            }";
            }else{
                        echo "name: '".$res['product_title'] ."',
              image: '".$res['product_image'] ."',
              quantity: '".$res['quantity'] ."',
              price: '".$res['price'] ."',
              tax: '0.01',
              currency: 'GBP'
            },";
            }

        }

        echo "}]

         },
         amount: { total: '40', 
                               currency: 'GBP'
                               },";

    }

    public function getAccessToken($clientId, $secret)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.sandbox.paypal.com/v1/oauth2/token");
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSLVERSION , 6); //NEW ADDITION
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($ch, CURLOPT_USERPWD, $clientId.":".$secret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");

        $result = curl_exec($ch);

        if(empty($result))die("Error: No response.");
        else
        {
            $json = json_decode($result);
            print_r($json->access_token);
        }

        curl_close($ch); //THIS CODE IS NOW WORKING!
    }


    public function showPaymentExecute($res)
    {

        $intent = $res['intent'];
        $status = $res['status'];
        $user_id = $this->user->id();
        $receipt_id = $res['id'];
        $payerInfo = $res['payer'];
        $ref_id    = $res['purchase_units'][0]['reference_id'];
        $shipping = $res['purchase_units'][0]['shipping'];

        $cost = $res['purchase_units'][0]['amount']['value'];
        $shipping_cost = $res['purchase_units'][0]['amount']['breakdown']['shipping']['value'];
        $tax = $res['purchase_units'][0]['amount']['breakdown']['tax_total']['value'];
        $total = $res['purchase_units'][0]['amount']['value'];

        $shipping_address = $shipping['address'];
        $item_cost = $res['purchase_units'][0]['amount']['breakdown']['item_total']['value']; 
        $tax_cost = $res['purchase_units'][0]['amount']['breakdown']['tax_total']['value']; 
        $handling_cost = $res['purchase_units'][0]['amount']['breakdown']['handling']['value'];
        $insurance_cost = $res['purchase_units'][0]['amount']['breakdown']['insurance']['value']; 
        $ship_discount = $res['purchase_units'][0]['amount']['breakdown']['shipping_discount']['value']; 

        $cc = $res['purchase_units'][0]['amount']['currency_code'];


        $this->WIdb->insert('wi_order',
        array(
        "userId" => $user_id,
        "sessionId" => $ref_id,
        "status" => $status,
        "subTotal" => $cost,
        "tax" => $tax,
        "shipping" => $shipping_cost,
        "total" => $total,
        "grandTotal" => $total,
        "email" => $payerInfo['email'],
        "firstName" => $payerInfo['name']['given_name'],
        "lastName" => $payerInfo['name']['sirname'],
        "line1" => $shipping['address']['address_line_1'],
        "line2" => $shipping['address']['viewAddressLine2'],
        "city" => $shipping['address']['admin_area_2'],
        "province" => $shipping['address']['admin_area_1']
        )
        );

        $this->WIdb->insert('wi_transaction',
        array(
        "userId" => $user_id,
        "orderId" => $receipt_id,
        "code"    => $ref_id,
        "status"  => $status,
        "mode"    => $intent
        )
        );

        $this->WIdb->delete("wi_cart", "userId = :id", array( "id" => $user_id ));

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
        <form id="orderView"
              class="form-horizontal"
              style="display: block;">
            <h3>Your payment is complete</h3>
            <h4>
                <span id="viewFirstName">' .$payerInfo['name']['given_name'] . '</span>
                <span id="viewLastName">' .$payerInfo['name']['surname'] . '</span>,
                Thank you for your Order
            </h4>
            <hr>
            <div class="form-group">
                <div class="form-group">
                    <label class="col-sm-5 control-label">Shipping Details</label>
                    <div class="col-sm-7">
                        <p id="viewRecipientName">' .$shipping['name']['full_name'] . '</p>
                        <p id="viewAddressLine1">' .$shipping['address']['address_line_1'] . '</p>
                        <p id="viewAddressLine2">' .$shipping['address']['viewAddressLine2'] . '</p>
                        <p>
                            <span id="viewCity">' .$shipping['address']['admin_area_2'] . '</span>,
                            <span id="viewState">' .$shipping['address']['admin_area_1'] . '</span> - <span id="viewPostalCode">' .$shipping['address']['postal_code'] . '</span>
                        </p>
                        <p id="confirmCountry"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 control-label">Transaction Details</label>
                    <div class="col-sm-7">';
                    
        if($res['purchase_units'][0]['payments'] && $res['purchase_units'][0]['payments']['captures']) {

            $final_amount = $res['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $view_currentcy = $res['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];
        }else {
            $final_amount = $res['purchase_units'][0]['amount']['value'];
            $view_currentcy = $res['purchase_units'][0]['amount']['currency_code'];
        }
                        $receipt .='<p>Transaction ID: <span id="viewTransactionID">' . $receipt_id . '</span></p>
                        <p>Payment Total Amount: <span id="viewFinalAmount"> 
                        ' . $final_amount . '
                        </span> </p>
                        <p>Currency Code: <span id="viewCurrency">
                        ' . $view_currentcy . '
                        </span></p>
                        <p>Payment Status: <span id="viewPaymentState">
                        result.status
                        ' . $res['status'] . '
                        </span></p>
                        <p id="transactionType">Payment Type: <span id="viewTransactionType"></span> </p>
                    </div>
                </div>
            </div>
            <hr>
            <h3> Click <a href="index.php">here </a> to return to Home Shopping Page</h3>
        </form>
    </div>
</div>
';
    
    $result = array(
    "status"   => "COMPLETED",
    "receipt"  => $receipt
    );
    echo json_encode($result); 
       
    }


    public function showPaymentGet($res)
    {

        $user_id = WISession::get('user_id');
        $receipt_id = $res['id'];
        $shipping = $res['purchase_units'][0]['shipping'];
        $shipping_address = $shipping['address'];
        $cost = $res['purchase_units'][0]['amount']['value'];
        $shipping_cost = $res['purchase_units'][0]['amount']['breakdown']['shipping']['value'];
        $tax = $res['purchase_units'][0]['amount']['breakdown']['tax_total']['value'];
        $total = $res['purchase_units'][0]['amount']['value'];
        $item_cost = $res['purchase_units'][0]['amount']['breakdown']['item_total']['value']; 
        $tax_cost = $res['purchase_units'][0]['amount']['breakdown']['tax_total']['value']; 
        $handling_cost = $res['purchase_units'][0]['amount']['breakdown']['handling']['value'];
        $insurance_cost = $res['purchase_units'][0]['amount']['breakdown']['insurance']['value']; 
        $ship_discount = $res['purchase_units'][0]['amount']['breakdown']['shipping_discount']['value']; 

        $cc = $res['purchase_units'][0]['amount']['currency_code'];

        $status = $res['status'];
        $this->WIdb->insert('wi_order',
        array(
        "userId" => $user_id,
        "sessionId" => $ref_id,
        "status" => $status,
        "subTotal" => $cost,
        "tax" => $tax,
        "shipping" => $shipping_cost,
        "total" => $total,
        "grandTotal" => $total,
        "email" => $payerInfo['email'],
        "firstName" => $$payerInfo['name']['given_name'],
        "lastName" => $$payerInfo['name']['sirname'],
        "line1" => $shipping['address']['address_line_1'],
        "line2" => $shipping['address']['viewAddressLine2'],
        "city" => $shipping['address']['admin_area_2'],
        "province" => $shipping['address']['admin_area_1']
        )
        );

        $this->WIdb->insert('wi_transaction',
        array(
        "userId" => $user_id,
        "orderId" => $receipt_id,
        "code"    => $ref_id,
        "status"  => $status,
        "mode"    => $intent
        )
        );
        $receipt = '<div class="row-fluid">
    <!-- Middle Section -->
    <div class="col-sm-offset-3 col-md-4">
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
              style="display: block;">
            <h3>Your payment is authorized.</h3>
            <h4>Confirm the order to execute</h4>
            <hr>
            <div class="form-group">
                <label class="col-sm-5 control-label">Shipping Information</label>
                <div class="col-sm-7">
                    <p id="confirmRecipient">' . $shipping['name']['full_name'] . '</p>
                    <p id="confirmAddressLine1">' . $shipping['address']['address_line_1'] . '</p>
                    <p id="confirmAddressLine2">' . $shipping['address']['address_line_2'] . '</p>
                    <p>
                        <span id="confirmCity">'.$shipping['address']['admin_area_2'] .'</span>,
                        <span id="confirmState"></span> - <span id="confirmZip">'.$shipping['address']['postal_code'] .'</span>
                    </p>
                    <p id="confirmCountry">'.$shipping['address']['country_code'] .'</p>
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
        <form id="orderView"
              class="form-horizontal"
              style="display: none;">
            <h3>Your payment is complete</h3>
            <h4>
                <span id="viewFirstName"></span>
                <span id="viewLastName"></span>,
                Thank you for your Order
            </h4>
            <hr>
            <div class="form-group">
                <div class="form-group">
                    <label class="col-sm-5 control-label">Shipping Details</label>
                    <div class="col-sm-7">
                        <p id="viewRecipientName"></p>
                        <p id="viewAddressLine1"></p>
                        <p id="viewAddressLine2"></p>
                        <p>
                            <span id="viewCity"></span>,
                            <span id="viewState"></span> - <span id="viewPostalCode"></span>
                        </p>
                        <p id="confirmCountry"></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-5 control-label">Transaction Details</label>
                    <div class="col-sm-7">
                        <p>Transaction ID: <span id="viewTransactionID"></span></p>
                        <p>Payment Total Amount: <span id="viewFinalAmount"></span> </p>
                        <p>Currency Code: <span id="viewCurrency"></span></p>
                        <p>Payment Status: <span id="viewPaymentState"></span></p>
                        <p id="transactionType">Payment Type: <span id="viewTransactionType"></span> </p>
                    </div>
                </div>
            </div>
            <hr>
            <h3> Click <a href="index.php">here </a> to return to Home Shopping Page</h3>
        </form>
    </div>
</div>
';
    $msg = "You have successfully Paid for your items, thank you for your Payment.";
    $result = array(
        "status"             => $status,
        "msg"                => $msg,
        "receipt"            => $receipt,
        "shipping"           => $shipping,
        "shipping_address"   => $shipping_address,
        "shipping_cost"      => $shipping_cost,
        "item_cost"          => $item_cost,
        "tax_cost"           => $tax_cost,
        "insurance_cost"     => $insurance_cost,
        "handling_cost"      => $handling_cost,
        "shipping_discount"  => $shipping_discount,
        "cost"               => $cost,
        "cc"                 => $cc,
        "id"                 => $receipt_id

    );

    echo json_encode($result);
    }

   
}

?>