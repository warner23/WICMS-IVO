<?php

declare(strict_types=1);

define('INCLUDE_CHECK', true);
require_once dirname(__DIR__) . '/WICore/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WI Kitchen Compliance Settings</title>
    <style>body{font-family:Arial,sans-serif;background:#f5f7fb;color:#17212f;margin:0;padding:24px}.wrap{max-width:1100px;margin:0 auto}.panel{background:#fff;border:1px solid #d8e0ea;border-radius:16px;padding:20px;box-shadow:0 8px 28px rgba(12,35,64,.06)}</style>
</head>
<body>
<div class="wrap">
    <div class="panel"><?= $compliance->renderSettingsScreen(); ?></div>
</div>
</body>
</html>
