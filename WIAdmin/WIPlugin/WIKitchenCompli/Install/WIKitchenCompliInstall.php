<?php
require_once dirname(__DIR__) . '/WICore/init.php';
$installer = new WIKitchenCompliInstall();
return $installer->install();
