<?php
/**
 * Build a simple HTML page with multiple providers, opening provider authentication in a pop-up.
 */

require_once dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) .'/WIClass/WI.php';
require_once 'autoload.php';
/*        require  'Common/PayPalModel.php';
        require  'Api/Payer.php';
        require  'Api/Item.php';
        require  'Api/ItemList.php';
        require 'Api/Details.php';
        require 'Api/Amount.php';
        require 'Api/CartBase.php';
        require 'Api/TransactionBase.php';
        require 'Api/Transaction.php';
        require 'Api/RedirectUrls.php';
        require 'Rest/IResource.php';
        require 'Common/PayPalResourceModel.php';
        require 'Api/Payment.php';
        require 'Rest/ApiContext.php';*/


        $paypal = new ApiContext(new OAuthTokenCredential(paypal_id,paypal_secret)
    );

        $user_id = WISession::get('user_id');

        $data = $WIdb->select("SELECT * FROM `wi_cart` WHERE `user_id`=:user_id", array(
            "user_id" => $user_id
            )
        );
        

        foreach ($data as $d ) {
            $product = $d['product_title'];
            $price   = $d['price'];
            $quantity   = $d['quantity'];
            $shipping = 2.00;

        $total = $price + $shipping;

        $payer = new Payer();
        $payer->SetPaymentMethod('paypal');

        $item = new Item();
        $item->SetName($product)
             ->SetCurrency('GBP')
             ->SetQuantity($quantity)
             ->SetPrice($price);

        $itemList = new ItemList();
        $itemList->SetItems($item);

        $details = new Details();
        $details->SetShipping($shipping)
                ->SetSubtotal($price);

        $amount = new Amount();
        $amount->SetCurrency('GBP')
               ->SetTotal($total)
               ->SetDetails($details);

        $transaction = new Transaction();
        $transaction->SetAmount($amount)
                    ->SetItemList($itemList)
                    ->SetDescription('Pay for your items')
                    ->SetInvoiceNumber(uniqid() );

        $redirectUrls = new RedirectUrls();
        $redirectUrls->SetReturnUrl(paypal_callback . 'pay.php?success=true')
                    ->SetCancelUrl(paypal_callback . 'pay.php?success=true');

        $payment = new Payment();
        $payment->SetIntent('sale')
                ->SetPayer($payer)
                ->SetRedirectUrl($redirectUrls)
                ->SetTransactions($transaction);

        try{
            $payment->create($paypal);
        }catch(Exception $e){
            die($e);
        }

        }

        

        $approveUrl->payment->getApprovalLink();

        hedader("Location:{$redirectUrls}");

?>