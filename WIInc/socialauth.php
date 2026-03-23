<?php

$provider = WISession::get("provider");
$token  = WISession::get("token");

if(! $token || $token !== WISession::get('WI_social_token')){
    WISession::destroy('WI_social_token');
    echo 'Wrong social auth token!';
}else{
    echo 'Token accepted.';
}

if (! $provider) {
    echo 'Wrong provider.' ;
}


switch ($provider) {
    case 'twitter':
        if (! TWITTER_ENABLED) {
            die('This provider is not enabled.');
        }
        break;
    case 'facebook':
        if (! FACEBOOK_ENABLED) {
            die('This provider is not enabled.');
        }
        break;
    case 'google':
        if (! GOOGLE_ENABLED) {
            die('This provider is not enabled.');
        }
        break;

    default:
        die('This provider is not supported!');
}


$config = array(
    "base_url" => SOCIAL_CALLBACK_URI,

    "providers" => array(
        "Google" => array(
            "enabled" => GOOGLE_ENABLED,
            "keys" => array("id" => GOOGLE_ID, "secret" => GOOGLE_SECRET),
            "scope" => "profile email"
        ),
        "Facebook" => array(
            "enabled" => FACEBOOK_ENABLED,
            "keys" => array("id" => FACEBOOK_ID, "secret" => FACEBOOK_SECRET),
            "scope"   => array('email', 'public_profile'),
            "trustForwarded" => true,
        ),
        "Twitter" => array(
            "enabled" => TWITTER_ENABLED,
            "keys" => array("key" => TWITTER_KEY, "secret" => TWITTER_SECRET),
            "includeEmail" => false,
        ),
    ),
);

//var_dump($config);

$reg     = new WIRegister();
$log     = new WILogin();         
$valid   = new WIValidator();
$WIdb    = WIdb::getInstance();


    require  dirname(dirname(__FILE__)) .'/WICore/WIVendor/Hybridauth/hybridauth/Hybrid/Hybridauth.php';
    require dirname(dirname(__FILE__)) .'/WICore/WIVendor/Hybridauth/autoload.php';
    use Hybridauth\Hybridauth;

try {


    $hybridauth = new Hybridauth($config);
    //var_dump($hybridauth);
    $adapter = $hybridauth->authenticate($provider);
    $userProfile = $adapter->getUserProfile();
    echo "2-0";
    // determine if this is first time that user logs in via this social network
    if ($reg->regedViaSocial($provider, $userProfile->identifier)) {
        // user already exist and his account is connected with this provider, log him in
        echo "checking user";
        $user = $reg->getBySocial($provider, $userProfile->identifier);
        $userInfo = $siteuser->getInfo($user['user_id']);

        if ($userInfo['banned'] == 'Y') {
            // this user is banned, we will just redirect him to login page
            redirect('index.php');
        } else {
            $log->byId($user['user_id']);
            redirect(get_redirect_page());
        }
    }

    // user is not reged via this social network, check if his email exist in db
    // and associate his account with this provider
    echo "3";
    if ($valid->emailExist($userProfile->email)) {
        // hey, this user is reged here, just associate social account with his email
        echo "valid";
        $user = $reg->getByEmail($userProfile->email);
        $reg->addSocialAccount($user['user_id'], $provider, $userProfile->identifier);
        $log->byId($user['user_id']);
        redirect(get_redirect_page());
    } else {
        // this is first time that user is registring on this webiste, create his account

        // Generate unique username
        // for example, if two users with same display name (that is usually first and last name)
        // are reged, they will have the same username, so we have to add some random number here
        echo "firstime";
        $username = str_replace(' ', '', $userProfile->displayName);
        $tmpUsername = $username;

        $i = 0;
        $max = 50;

        while ($valid->usernameExist($tmpUsername)) {
            // try maximum 50 times
            // Note: Chances for going over 2-3 times are really really low but just in case,
            // if somehow it always generate username that is already in use, prevent database from crashing
            // and generate some random unique username (it can be changed by administrator later)
            if ($i > $max) {
                break;
            }

            $tmpUsername = $username . rand(1, 10000);
            $i++;
        }

        // there are more than 50 trials, generate random username
        if ($i > $max) {
            $tmpUsername = uniqid('user', true);
        }

        $username = $tmpUsername;

        $info = array(
            'email' => $userProfile->email == null ? '' : $userProfile->email,
            'username' => $username,
            'password' => $reg->hashPassword(hash('sha512', $reg->randomPassword())),
            'confirmation_key' => '',
            'confirmed' => 'Y',
            'password_reset_key' => '',
            'password_reset_confirmed' => 'N',
            'reg_date' => date('Y-m-d H:i:s')
        );

        $details = array(
            'first_name' => $userProfile->firstName == null ? '' : $userProfile->firstName,
            'last_name' => $userProfile->lastName == null ? '' : $userProfile->lastName,
            'address' => $userProfile->address == null ? '' : $userProfile->address,
            'phone' => $userProfile->phone == null ? '' : $userProfile->phone
        );

        $WIdb->insert('wi_members', $info);

        $userId = $WIdb->lastInsertId();

        $details['user_id'] = $userId;

        $WIdb->insert('wi_user_details', $details);

        $reg->addSocialAccount($userId, $provider, $userProfile->identifier);
        $log->byId($userId);
        redirect(get_redirect_page());
    }
} catch (Exception $e) {
    // something happened (social auth cannot be completed), just redirect user to login page
    // Note: to debug check HybridAuth documentation for error codes:
    // http://hybridauth.sourceforge.net/userguide/Errors_and_Exceptions_Handling.html

    if (DEBUG) {
        echo "<p><strong>Social Authentication Error #{$e->getCode()}: </strong> {$e->getMessage()}</p>";
        echo "<pre><code>";
        var_dump($e);
        echo "</code></pre>";
        exit;
    }

    redirect('index.php');
}

?>