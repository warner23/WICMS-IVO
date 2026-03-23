<?php
/**
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2015, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html
*/

// ------------------------------------------------------------------------
//  HybridAuth End Point
// ------------------------------------------------------------------------
require_once dirname(dirname(dirname(__FILE__))) .'/WIClass/WI.php';
require_once 'hybridauth/config.php';
include_once 'hybridauth/Hybridauth.php';
include_once 'hybridauth/Endpoint.php';

$Hybrid_Endpoint = new Hybrid_Endpoint();

$Hybrid_Endpoint->process();
