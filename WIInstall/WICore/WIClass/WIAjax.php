<?php
include_once 'WI.php';

header('Content-Type: application/json; charset=utf-8');

// csrf / ajax protection
if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
    echo json_encode(array('status' => 'error', 'message' => 'Invalid installer request.'));
    exit;
}

$url = parse_url(isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');
if (!isset($url['host']) || ($url['host'] != $_SERVER['SERVER_NAME'])) {
    echo json_encode(array('status' => 'error', 'message' => 'Installer request origin was not accepted.'));
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';

try {
    switch ($action) {
        case 'requirements':
            $require = new WIRequirements();
            $require->Required();
            break;

        case 'db_settings':
            $DB = new WIDBSeetings();
            $DB->TestDB($_POST['host'], $_POST['db'], $_POST['user'], $_POST['pass']);
            break;

        case 'install_settings':
            $install = new Install();
            $install->Installer(
                $_POST['name'],
                $_POST['dom'],
                $_POST['script'],
                $_POST['session_secure'],
                $_POST['http'],
                $_POST['session_regenerate'],
                $_POST['cookieonly'],
                $_POST['login_fingerprint'],
                $_POST['max_login_attempts'],
                $_POST['redirect_after_login'],
                $_POST['encryption'],
                $_POST['cost'],
                $_POST['mailer'],
                $_POST['db_host'],
                $_POST['db_name'],
                $_POST['db_password'],
                $_POST['db_username'],
                $_POST['bootstrap_version'],
                $_POST['salt'],
                $_POST['admin_password'],
                $_POST['admin_username'],
                $_POST['email_address']
            );
            break;

        default:
            echo json_encode(array('status' => 'error', 'message' => 'Unknown installer action.'));
            break;
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(array(
        'status' => 'error',
        'message' => 'Installer error: ' . $e->getMessage()
    ));
}
