<?php
#[\AllowDynamicProperties]
/**
* WISecurity Class
* Created by Warner Infinity
* Author Jules Warner
*/

class WISecurity
{

	    function __construct() 
    {
         $this->WIdb = WIdb::getInstance();

    }

    
    public function Encryption($string)
    {
    	$results = $this->WIdb->select('SELECT * FROM `wi_site`');
    	if(count($results) > 0){
    		$ciphering = $results[0]['ciphering'];
    		$encryption_iv = $results[0]['encryption_iv'];
    		$encryption_key = $results[0]['encryption_key'];
    		$iv_length = openssl_cipher_iv_length($ciphering); 
		$options = 0; 

				// Use openssl_encrypt() function to encrypt the data 
		$encryption = openssl_encrypt($string, $ciphering, 
		            $encryption_key, $options, $encryption_iv); 
		  
		// Display the encrypted string 
		echo "Encrypted String: " . $encryption . "\n"; 

    	}
    	// Store the cipher method 
		//$ciphering = "AES-128-CTR"; 
		//$ciphering = "BF-CBC"; 
		// Use OpenSSl Encryption method 

		  

    }

    public function Decryption()
    {
    	    	$results = $this->WIdb->select('SELECT * FROM `wi_site`');
    	if(count($results) > 0){
    		$ciphering = $results[0]['ciphering'];
    		$decryption_iv = $results[0]['encryption_iv'];
    		$decryption_key = $results[0]['encryption_key'];

    		    	// Non-NULL Initialization Vector for decryption 

		// Use openssl_decrypt() function to decrypt the data 
		$decryption=openssl_decrypt ($encryption, $ciphering,  
		        $decryption_key, $options, $decryption_iv); 
		  
		// Display the decrypted string 
		echo "Decrypted String: " . $decryption; 
    	}

    }



}