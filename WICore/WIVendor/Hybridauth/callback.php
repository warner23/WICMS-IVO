<?php
/**
 * A simple example that shows how to use multiple providers, opening provider authentication in a pop-up.
 */

require_once dirname(dirname(dirname(__FILE__))) .'/WIClass/WI.php';
require 'autoload.php';
require 'config.php';

use Hybridauth\Exception\Exception;
use Hybridauth\Hybridauth;
use Hybridauth\HttpClient;
use Hybridauth\Storage\Session;

try {

    $hybridauth = new Hybridauth($config);
    $storage = new Session();
    $error = false;
    
    $provider = WISession::get('provider');
    $storage->set('provider',$provider);

    echo $provider;
    // Event 1: User clicked SIGN-IN link
    //


    if (isset($_GET['provider'])) {
        // Validate provider exists in the $config
        if (in_array($_GET['provider'], $hybridauth->getProviders())) {
            // Store the provider for the callback event
            $storage->set('provider', $_GET['provider']);
        } else {
            $error = $_GET['provider'];
        }
    }

    //
    // Event 2: User clicked LOGOUT link
    //
    if (isset($_GET['logout'])) {
        if (in_array($_GET['logout'], $hybridauth->getProviders())) {
            // Disconnect the adapter
            $adapter = $hybridauth->getAdapter($_GET['logout']);
            $adapter->disconnect();
        } else {
            $error = $_GET['logout'];
        }
    }

    //
    // Handle invalid provider errors
    //
    if ($error) {
        error_log('HybridAuth Error: Provider '. json_encode($error) .' not found or not enabled in $config');
        // Close the pop-up window
        echo "test";
        redirect('index.php');
        echo " 
            <script>
                window.opener.location.reload();
                window.close();
            </script>";
        exit;
    }

    //
    // Event 3: Provider returns via CALLBACK
    //
    if ($provider = $storage->get('provider')) {

        $hybridauth->authenticate($provider);
        $storage->set('provider', null);

        // Retrieve the provider record
        $adapter = $hybridauth->getAdapter($provider);
        $userProfile = $adapter->getUserProfile();
        $accessToken = $adapter->getAccessToken();
        //var_dump($userProfile);
        // add your custom AUTH functions (if any) here
        // ...
        $data = [
            'token'         => $accessToken,
            'identifier'    => $userProfile->identifier,
            'email'         => $userProfile->email,
            'first_name'    => $userProfile->firstName,
            'last_name'     => $userProfile->lastName,
            'photoURL'      => strtok($userProfile->photoURL,'?'),
            ];
            //var_dump($data);
        // ...

        //redirect(get_redirect_page() );

        // Close pop-up window

            

        echo "
            <script>
           var location = 'https://wicms.uk/testing_IVO/index.php';
                window.opener.location.reload();
                window.close();
            </script>";

    }

} catch (Exception $e) {
    error_log( $e->getMessage());
    echo $e->getMessage();
            // Close pop-up window
    redirect('index.php');
    echo "catch";
        echo "
            <script>
                window.opener.location.reload();
                window.close();
                cartch
            </script>";
}
