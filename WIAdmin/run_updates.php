<?php
declare(strict_types=1);

require_once __DIR__ . '/WICore/init.php';

onlyAdminUpdate();

$updater = new WIUpdater();
$result = $updater->updateTo(WICMS_VERSION);

echo '<pre>';
print_r($result);
echo '</pre>';

function onlyAdminUpdate(): void
{
    $login = new WILogin();

    if (!$login->isLoggedIn()) {
        exit('Unauthorized');
    }

    $admin = new WIAdmin(WISession::get('user_id'));

    if (!$admin->isAdmin()) {
        exit('Forbidden');
    }
}