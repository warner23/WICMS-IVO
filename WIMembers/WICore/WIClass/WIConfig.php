<?php

include_once dirname(dirname(dirname(dirname(__FILE__)))) . '/WIAdmin/WICore/WILib.php';

include_once dirname(dirname(dirname(dirname(__FILE__)))) . '/WIAdmin/WICore/WIMemberLib.php';

date_default_timezone_set('UTC');

//Bootstrap

define('BOOTSTRAP_VERSION', $bootstrap_version);


//WEBSITE

define('WEBSITE_NAME', $webName);

define('WEBSITE_DOMAIN', $domain);


//it can be the same as domain (if script is placed on website's root folder) 
//or it can cotain path that include subfolders, if script is located in some subfolder and not in root folder
define('SCRIPT_URL', $script);

//DATABASE CONFIGURATION
define('DB_HOST', $dbhost); 

define('DB_TYPE', $dbtype); 

define('DB_USER', $dbusername); 

define('DB_PASS', $dbpass); 

define('DB_NAME', $dbname); 


//SESSION CONFIGURATION

define('SESSION_SECURE', $session);   

define('SESSION_HTTP_ONLY', $http);

define('SESSION_REGENERATE_ID', $regenerate);   

define('SESSION_USE_ONLY_COOKIES', $cookie);


//LOGIN CONFIGURATION

define('LOGIN_MAX_LOGIN_ATTEMPTS', $loginAttemp); 

define('LOGIN_FINGERPRINT', $loginFinger); 

define('SUCCESS_LOGIN_REDIRECT', serialize(array( 'default' => "WIMembers/profile.php" ))); 


//PASSWORD CONFIGURATION

define('PASSWORD_ENCRYPTION', $encryption); //available values: "sha512", "bcrypt"

define('PASSWORD_BCRYPT_COST', $bcrypt); 

define('PASSWORD_SHA512_ITERATIONS', $sha512); 

define('PASSWORD_SALT', $salt); //22 characters to be appended on first 7 characters that will be generated using PASSWORD_ info above

define('PASSWORD_RESET_KEY_LIFE', $keylife); 


//REGISTRATION CONFIGURATION

define('MAIL_CONFIRMATION_REQUIRED', $mailConfirm); 

define('REGISTER_CONFIRM', $regConfirm); 

define('REGISTER_PASSWORD_RESET', $passReset); 


//EMAIL SENDING CONFIGURATION

define('MAILER', $mail); // available options are 'mail' for php mail() and 'smtp' for using SMTP server for sending emails

define('SMTP_HOST', $smpt_host); 

define('SMTP_PORT', $smpt_port); 

define('SMTP_USERNAME', $smpt_username); 

define('SMTP_PASSWORD', $smpt_password); 

define('SMTP_ENCRYPTION', $smpt_encryption); 


//SOCIAL LOGIN CONFIGURATION

define('SOCIAL_CALLBACK_URI', $social); 


// GOOGLE

define('GOOGLE_ENABLED', $google); 

define('GOOGLE_ID', $google_id); 

define('GOOGLE_SECRET', $google_secret); 


// FACEBOOK

define('FACEBOOK_ENABLED', $fb); 

define('FACEBOOK_ID', $fb_id); 

define('FACEBOOK_SECRET', $fb_secret); 


// TWITTER

// NOTE: Twitter api for authentication doesn't provide users email address!
// So, if you email address is strictly required for all users, consider disabling twitter login option.

define('TWITTER_ENABLED', $tw_enabled); 

define('TWITTER_KEY', $tw_key); 

define('TWITTER_SECRET', $tw_secret); 


// TRANSLATION

define('DEFAULT_LANGUAGE', $default_lang); 

// VERSION 
define('WICMS_VERSION', $version);


//membership paypal

//SHOP
define('SHOP_NAME',$shop_name);

define('BUSINESS_EMAIL', $business_email );

define('PAYPAL_CALLBACK', $paypal_callback);

define('PAYPAL_CANCEL_URL', $cancel_url);

define('PAYPAL_NOTIFY_URL', $notify_url);

define("BASE_URL", $base_url);

define('PAYPAL_PRO',$paypal_pro );

define('CURRENCY',$currency);

define('CURRENCY_SYMBOL',$currency_symbol);

define('CURRENT_URL', $current_url);

define('PAYPAL_ENVIRONMENT', $paypal_environment);

if(PAYPAL_PRO){
	define("PAYPAL_CLIENT_ID", $paypal_id) ;
	define("PAYPAL_SECRET", $paypal_secret);
	define("PAYPAL_BASE_URL", $paypal_pro_base_url);
}
else{
	define("PAYPAL_CLIENT_ID", $paypal_id);
	define("PAYPAL_SECRET", $paypal_secret);
	define("PAYPAL_BASE_URL", $paypal_base_url);
}


define("PAYPAL_ACCESS_TOKEN", "A21AAGGnlDrmEJhYh3z4DxtsQm2BV_DdyYrZJp7y6EbRlujb7RcC-0_2q2pcmsvqjWFmKYCDQNN07jDj2fWqi2B5x6keNO1bw");

define("API_VERSION", "V2");

define("URL", array(

    "current" => CURRENT_URL,

    "services" => array(
        "orderCreate" => BASE_URL.'WICore/WIVendor/paypal/' . API_VERSION.'/api/createOrder.php',
        "orderGet" => BASE_URL.'WICore/WIVendor/paypal/' . API_VERSION.'/api/getOrderDetails.php',
		"orderPatch" => BASE_URL.'WICore/WIVendor/paypal/' . API_VERSION.'/api/patchOrder.php',
		"orderCapture" => BASE_URL.'WICore/WIVendor/paypal/' . API_VERSION.'/api/captureOrder.php'
    ),

	"redirectUrls" => array(
        "returnUrl" => BASE_URL.'success.php',
		"cancelUrl" => BASE_URL.'cancel.php',
    )
));

define("PAYPAL_ENDPOINTS", array(
	"sandbox" => "https://api.sandbox.paypal.com",
	"production" => "https://api.paypal.com"
));

// PayPal REST API version
define("PAYPAL_REST_VERSION", "v2");

// ButtonSource Tracker Code
define("SBN_CODE", "sb-ubere2307035@personal.example.com");


define("PAYPAL_CREDENTIALS", array(
	"sandbox" => [
		"client_id" => $paypal_id,
		"client_secret" => $paypal_secret
	],
	"production" => [
		"client_id" => "",
		"client_secret" => ""
	]
));


define("VAT", $VAT);

