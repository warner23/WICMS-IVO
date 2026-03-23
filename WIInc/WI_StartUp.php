<?php
declare(strict_types=1);

define('INCLUDE_CHECK', true);

require_once __DIR__ . '/../WICore/init.php';

/*
|--------------------------------------------------------------------------
| Frontend startup
|--------------------------------------------------------------------------
|
| This file should stay focused on:
| - bootstrapping the frontend runtime
| - outputting page head assets
| - opening the body tag
|
| It should NOT duplicate library loading or include multiple versions of
| jQuery / jQuery UI.
|
*/

$web->StartUp();
$web->Meta($page ?? '');
$web->Styling($page ?? '');
$web->Scripts($page ?? '');
$web->webSite_icons();
?>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.3/themes/base/jquery-ui.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/timepicker/1.3.5/jquery.timepicker.min.css">

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.3/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>

<script>
window.$_lang = <?php echo WILang::all(); ?>;
</script>
</head>
<body class="wi">