<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Meta Route Compatibility
|--------------------------------------------------------------------------
| Meta is now managed inside the core Styling manager with Theme/CSS/JS.
| Keep WIMeta.php route alive so old sidebar links do not break.
*/

include_once 'WICore/init.php';

if ($admin !== null && $admin->isAdmin()) {
    include_once 'WIInc/WI_start_up.php';
    include_once 'WIInc/WI_header.php';
    include_once 'WIInc/sidebar.php';
    include_once 'WIInc/styling.php';
} else {
    header('Location:../index.php');
    exit;
}
?>
</body>
</html>
