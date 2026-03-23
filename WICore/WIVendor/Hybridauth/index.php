<?php
/**
 * Build a simple HTML page with multiple providers, opening provider authentication in a pop-up.
 */

require_once dirname(dirname(dirname(__FILE__))) .'/WIClass/WI.php';

require 'autoload.php';
require 'config.php';

$provider = $_GET['p'];
$token    = $_GET['token'];

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


use Hybridauth\Hybridauth;

try {
    $user  = new WIUser(WISession::get("user_id"));

    WISession::set("provider", $provider);
    //Feed configuration array to Hybridauth
    $hybridauth = new Hybridauth($config);
    //Attempt to authenticate users with a provider by name
    $adapter = $hybridauth->authenticate($provider);
    //Returns a boolean of whether the user is connected with provider
    //$isConnected = $hybridauth->getConnectedAdapters();
    //Retrieve the user's profile
    $userProfile = $adapter->getUserProfile();
    //var_dump($userProfile);
    // determine if this is first time that user logs in via this social network
    if ($register->registeredViaSocial($provider, $userProfile->identifier)) {
        // user already exist and his account is connected with this provider, log him in
        $socialuser = $register->getBySocial($provider, $userProfile->identifier);
        $userInfo = $user->getInfo($socialuser['user_id']);

        if ($userInfo['banned'] == 'Y') {
            // this user is banned, we will just redirect him to login page
            redirect('index.php');
        } else {
            $login->byId($socialuser['user_id']);

            header('location: ../../../WIMembers/profile.php');  
        }
    }

    // user is not registered via this social network, check if his email exist in db
    // and associate his account with this provider
    if ($validator->emailExist($userProfile->email)) {
        // hey, this user is registered here, just associate social account with his email
        $user = $register->getByEmail($userProfile->email);
        $register->addSocialAccount($user['user_id'], $provider, $userProfile->identifier);
        $login->byId($user['user_id']);
        header('location: ../../../WIMembers/profile.php');  
    } else {
        // this is first time that user is registeristring on this webiste, create his account

        // Generate unique username
        // for example, if two users with same display name (that is usually first and last name)
        // are registered, they will have the same username, so we have to add some random number here
        $username = str_replace(' ', '', $userProfile->displayName);
        $tmpUsername = $username;

        $i = 0;
        $max = 50;

        while ($validator->usernameExist($tmpUsername)) {
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
            'password' => $register->hashPassword(hash('sha512', $register->randomPassword())),
            'confirmation_key' => '',
            'confirmed' => 'Y',
            'password_reset_key' => '',
            'password_reset_confirmed' => 'N',
            'register_date' => date('Y-m-d H:i:s')
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

        $register->addSocialAccount($userId, $provider, $userProfile->identifier);
        $login->byId($userId);

        header('location: ../../../WIMembers/profile.php');      
        /*redirect(get_redirect_page());
        echo "
            <script>
                window.close();
            </script>";*/
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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Example 07</title>

    <script>
        function auth_popup( provider ){
            // replace 'path/to/hybridauth' with the real path to this script
            var authWindow = window.open('https://wicms.uk/WICore/WIVendor/Hybtidauth/callback.php?provider='+provider, 'authWindow', 'width=600,height=400,scrollbars=yes');
            return false;
        }
    </script>
    
</head>
<body>
    <h1>Sign in</h1>

    <ul>

<?php foreach ($hybridauth->getProviders() as $name) : ?>
    <?php if (!isset($adapters[$name])) : ?>
        <li>
            <a href="#" onclick="javascript:auth_popup('<?php print $name ?>');">
                Sign in with <?php print $name ?>
            </a>
        </li>
    <?php endif; ?>
<?php endforeach; ?>

    </ul>

<?php if ($adapters) : ?>
    <h1>You are logged in:</h1>
    <ul>
        <?php foreach ($adapters as $name => $adapter) : ?>
            <li>
                <strong><?php print $adapter->getUserProfile()->displayName; ?></strong> from
                <i><?php print $name; ?></i>
                <span>(<a href="<?php print $config['callback'] . "?logout={$name}"; ?>">Log Out</a>)</span>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

</body>
</html>
