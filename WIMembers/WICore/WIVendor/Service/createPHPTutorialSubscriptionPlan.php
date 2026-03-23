<?php
use PayPal\Api\ChargeModel;
use PayPal\Api\Currency;
use PayPal\Api\MerchantPreferences;
use PayPal\Api\PaymentDefinition;
use PayPal\Api\Plan;
use PayPal\Api\Patch;
use PayPal\Api\PatchRequest;
use PayPal\Common\PayPalModel;

// Create a new billing plan
if (! empty($_POST["plan_name"]) && ! empty($_POST["plan_description"])) {

$plan = new Plan();
$plan->setName($_POST["plan_name"])
    ->setDescription($_POST["plan_description"])
    ->setType('FIXED');

// Set billing plan definitions
$paymentDefinition = new PaymentDefinition();
$paymentDefinition->setName('Regular Payments')
    ->setType('REGULAR')
    ->setFrequency('DAY')
    ->setFrequencyInterval('1')
    ->setCycles('3')
    ->setAmount(new Currency(array(
    'value' => 3,
    'currency' => 'USD'
)));

// Set charge models
$chargeModel = new ChargeModel();
$chargeModel->setType('SHIPPING')->setAmount(new Currency(array(
    'value' => 1,
    'currency' => 'USD'
)));
$paymentDefinition->setChargeModels(array(
    $chargeModel
));

// Set merchant preferences
$merchantPreferences = new MerchantPreferences();
$merchantPreferences->setReturnUrl('http://<host>/how-to-manage-recurring-payments-using-paypal-subscriptions-in-php/index.php?status=success')
    ->setCancelUrl('http://<host>/how-to-manage-recurring-payments-using-paypal-subscriptions-in-php/index.php?status=cancel')
    ->setAutoBillAmount('yes')
    ->setInitialFailAmountAction('CONTINUE')
    ->setMaxFailAttempts('0')
    ->setSetupFee(new Currency(array(
    'value' => 1,
    'currency' => 'USD'
)));

$plan->setPaymentDefinitions(array(
    $paymentDefinition
));
$plan->setMerchantPreferences($merchantPreferences);

try {
    $createdPlan = $plan->create($apiContext);
    
    try {
        $patch = new Patch();
        $value = new PayPalModel('{"state":"ACTIVE"}');
        $patch->setOp('replace')
            ->setPath('/')
            ->setValue($value);
        $patchRequest = new PatchRequest();
        $patchRequest->addPatch($patch);
        $createdPlan->update($patchRequest, $apiContext);
        $patchedPlan = Plan::get($createdPlan->getId(), $apiContext);
        
        require_once "createPHPTutorialSubscriptionAgreement.php";
    } catch (PayPal\Exception\PayPalConnectionException $ex) {
        echo $ex->getCode();
        echo $ex->getData();
        die($ex);
    } catch (Exception $ex) {
        die($ex);
    }
} catch (PayPal\Exception\PayPalConnectionException $ex) {
    echo $ex->getCode();
    echo $ex->getData();
    die($ex);
} catch (Exception $ex) {
    die($ex);
}
}
?>