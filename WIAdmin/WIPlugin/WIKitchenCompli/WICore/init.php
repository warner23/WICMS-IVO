<?php

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/WIClass/WI.php';

spl_autoload_register(static function (string $class): void {
    $file = __DIR__ . '/WIClass/' . $class . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

$compliance = $compliance ?? new WICompliance();
$install = new WIKitchenCompliInstall();
