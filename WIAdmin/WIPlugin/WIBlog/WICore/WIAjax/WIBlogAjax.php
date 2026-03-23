<?php
declare(strict_types=1);

/**
 * WIBlog AJAX Handler
 * Location: WIPlugin/WIBlog/WICore/WIAjax/WIBlogAjax.php
 */

header('Content-Type: application/json; charset=utf-8');

require_once dirname(__DIR__) . '/../init.php';

if (!class_exists('WIBlog_Options')) {
    echo json_encode([
        'status' => 'error',
        'message' => 'WIBlog_Options class could not be loaded.'
    ]);
    exit;
}

/**
 * JSON response helper
 */
function wiblog_json_response(string $status, string $message, array $extra = []): void
{
    echo json_encode(array_merge([
        'status' => $status,
        'message' => $message
    ], $extra));
    exit;
}

/**
 * Safe POST string
 */
function wiblog_post(string $key, string $default = ''): string
{
    return isset($_POST[$key]) ? trim((string)$_POST[$key]) : $default;
}

/**
 * Optional auth gate
 * Adjust this if your admin auth/session wrapper uses a different class/function.
 */
function wiblog_is_admin_request(): bool
{
    if (class_exists('WISession')) {
        try {
            $session = new WISession();

            if (method_exists($session, 'isLoggedIn') && !$session->isLoggedIn()) {
                return false;
            }
        } catch (\Throwable $e) {
            return true;
        }
    }

    return true;
}

if (!wiblog_is_admin_request()) {
    wiblog_json_response('error', 'Unauthorized request.');
}

$action = wiblog_post('action');

if ($action === '') {
    wiblog_json_response('error', 'No action supplied.');
}

$options = new WIBlog_Options();

switch ($action) {

    case 'wiblog_save_options':

        $defaults = $options->defaults();
        $incoming = [];

        foreach ($defaults as $key => $defaultValue) {
            $incoming[$key] = array_key_exists($key, $_POST)
                ? $_POST[$key]
                : $defaultValue;
        }

        $saved = $options->saveMany($incoming);

        if (!$saved) {
            wiblog_json_response('error', 'Some blog options could not be saved.');
        }

        wiblog_json_response('success', 'Blog options saved successfully.');
        break;

    case 'wiblog_reset_options':

        $reset = $options->resetToDefaults();

        if (!$reset) {
            wiblog_json_response('error', 'Blog options could not be reset.');
        }

        wiblog_json_response('success', 'Blog options were reset to defaults.');
        break;

    case 'wiblog_get_options':

        wiblog_json_response('success', 'Blog options loaded.', [
            'data' => $options->all()
        ]);
        break;

    default:
        wiblog_json_response('error', 'Invalid WIBlog action.');
        break;
}