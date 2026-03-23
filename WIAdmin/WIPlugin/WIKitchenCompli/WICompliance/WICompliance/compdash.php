<?php
$page = 'compdash';
require_once dirname(__DIR__, 2) . '/WICore/init.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>WI Kitchen Compliance - <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $page)), ENT_QUOTES, 'UTF-8'); ?></title>
<style>body{font-family:Arial,sans-serif;background:#f8fafc;color:#17212f;padding:32px}.panel{max-width:980px;margin:0 auto;background:#fff;border:1px solid #d8e0ea;border-radius:16px;padding:24px;box-shadow:0 8px 28px rgba(12,35,64,.06)}</style>
</head>
<body>
<div class="panel">
<h1><?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $page)), ENT_QUOTES, 'UTF-8'); ?></h1>
<p>This page is part of the WIKitchenCompli plugin shell and is ready to be connected to WICMS page/module rendering.</p>
<div><?php echo $compliance->renderQuickActions(); ?></div>
</div>
<script src="../../WICore/WIJ/WICompliance.js"></script>
</body>
</html>
