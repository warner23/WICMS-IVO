<?php 
require 'vendor/PayPal-PHP-SDK/autoload.php';

$apiContext = new \PayPal\Rest\ApiContext(
  new \PayPal\Auth\OAuthTokenCredential(
    'CLIENT_ID',
    'CLIENT_SECRET'
  )
);

if (!empty($_GET['status'])) {
    if($_GET['status'] == "success") {
        $token = $_GET['token'];
        $agreement = new \PayPal\Api\Agreement();
        
        try {
            // Execute agreement
            $agreement->execute($token, $apiContext);
        } catch (PayPal\Exception\PayPalConnectionException $ex) {
            echo $ex->getCode();
            echo $ex->getData();
            die($ex);
        } catch (Exception $ex) {
            die($ex);
        }
    } else {
        echo "user canceled agreement";
    }
    exit;
}

if (! empty($_POST["subscribe"])) {
    require_once "./Service/createPHPTutorialSubscriptionPlan.php";
}

?>
<style>
body {
    font-family: Arial;
    color: #212121;
}

#subscription-plan {
    padding: 20px;
    border: #E0E0E0 2px solid;
    text-align: center;
    width: 200px;
    border-radius: 3px;
    margin: 40px auto;
}

.plan-info {
    font-size: 1em;
}

.plan-desc {
    margin: 10px 0px 20px 0px;
    color: #a3a3a3;
    font-size: 0.95em;
}

.price {
    font-size: 1.5em;
    padding: 30px 0px;
    border-top: #f3f1f1 1px solid;
}

.btn-subscribe {
    padding: 10px;
    background: #e2bf56;
    width: 100%;
    border-radius: 3px;
    border: #d4b759 1px solid;
    font-size: 0.95em;
}
</style>
<div id="subscription-plan">
    <div class="plan-info">PHP jQuery Tutorials</div>
    <div class="plan-desc">Read tutorials to learn PHP.</div>
    <div class="price">$49 / month</div>

    <div>
        <form method="post">

            <input type="hidden" name="plan_name"
                value="PHP jQuery Tutorials" /> <input type="hidden"
                name="plan_description"
                value="Tutorials access to learn PHP with simple examples." />
            <input type="submit" name="subscribe" value="Subscribe"
                class="btn-subscribe" />
        </form>
    </div>
</div>