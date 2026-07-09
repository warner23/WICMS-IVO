<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Core Users Manager AJAX Endpoint
|--------------------------------------------------------------------------
| This endpoint is intentionally narrow and self-contained so it does not
| overwrite the main WIAjax dispatcher while the core admin cleanup is ongoing.
|--------------------------------------------------------------------------
*/

require_once dirname(__DIR__) . '/WIClass/WI.php';
require_once dirname(__DIR__) . '/WIClass/WIUser.php';

WISession::startSession();

function wi_users_json(array $payload, int $status = 200): never
{
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
    }

    if (class_exists('WIToken')) {
        try {
            $payload['csrf_token'] = WIToken::getToken('wicms_users_manager');
        } catch (Throwable $e) {
        }
    }

    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function wi_users_error(string $message, int $status = 400, array $data = []): never
{
    wi_users_json([
        'success' => false,
        'status' => 'error',
        'message' => $message,
        'data' => $data,
    ], $status);
}

try {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        wi_users_error('Invalid request method.', 405);
    }

    if (class_exists('WIRequest') && method_exists('WIRequest', 'isAjax') && !WIRequest::isAjax()) {
        wi_users_error('Invalid request type.', 403);
    }

    if (class_exists('WIRequest') && method_exists('WIRequest', 'sameOrigin') && !WIRequest::sameOrigin()) {
        wi_users_error('Invalid origin.', 403);
    }

    $submittedToken = $_POST['csrf_token'] ?? null;
    if (is_array($submittedToken)) {
        wi_users_error('Invalid security token.', 403);
    }

    if (class_exists('WIToken') && method_exists('WIToken', 'validate')) {
        if (!WIToken::validate('wicms_users_manager', $submittedToken !== null ? (string)$submittedToken : null)) {
            wi_users_error('Invalid security token.', 403);
        }
    }

    $login = new WILogin();
    if (!$login->isLoggedIn()) {
        wi_users_error('You must be logged in.', 401);
    }

    $currentAdminId = (int)WISession::get('user_id', 0);
    $admin = new WIAdmin($currentAdminId);
    if (!$admin->isAdmin()) {
        wi_users_error('Admin access required.', 403);
    }

    $action = trim((string)($_POST['action'] ?? ''));
    $service = new WIUser();

    switch ($action) {
        case 'wicms_users_list':
            wi_users_json([
                'success' => true,
                'status' => 'success',
                'message' => 'Users loaded.',
                'data' => $service->searchUsers([
                    'search' => (string)($_POST['search'] ?? ''),
                    'role_id' => (int)($_POST['role_id'] ?? 0),
                    'status' => (string)($_POST['status'] ?? 'all'),
                    'page' => (int)($_POST['page'] ?? 1),
                    'per_page' => (int)($_POST['per_page'] ?? 10),
                ]),
            ]);

        case 'wicms_user_get':
            $user = $service->getUserById((int)($_POST['user_id'] ?? 0));
            if ($user === null) {
                wi_users_error('User not found.', 404);
            }

            wi_users_json([
                'success' => true,
                'status' => 'success',
                'message' => 'User loaded.',
                'data' => ['user' => $user],
            ]);

        case 'wicms_user_save':
            $result = $service->saveUser($_POST, $currentAdminId);
            wi_users_json($result, (($result['status'] ?? 'error') === 'success') ? 200 : 422);

        case 'wicms_user_role':
            $result = $service->changeRole(
                (int)($_POST['user_id'] ?? 0),
                (int)($_POST['role_id'] ?? 0),
                $currentAdminId
            );
            wi_users_json($result, (($result['status'] ?? 'error') === 'success') ? 200 : 422);

        case 'wicms_user_status':
            $result = $service->setStatus(
                (int)($_POST['user_id'] ?? 0),
                (string)($_POST['field'] ?? ''),
                (string)($_POST['value'] ?? ''),
                $currentAdminId
            );
            wi_users_json($result, (($result['status'] ?? 'error') === 'success') ? 200 : 422);

        case 'wicms_user_delete':
            $result = $service->deleteUserById((int)($_POST['user_id'] ?? 0), $currentAdminId);
            wi_users_json($result, (($result['status'] ?? 'error') === 'success') ? 200 : 422);
    }

    wi_users_error('Unknown user action.', 400);
} catch (Throwable $e) {
    if (class_exists('WILogger')) {
        try {
            WILogger::error('WICMS users manager error', [
                'message' => $e->getMessage(),
                'file' => basename($e->getFile()),
                'line' => $e->getLine(),
            ], 'users');
        } catch (Throwable $ignored) {
        }
    }

    if (defined('APP_DEBUG') && APP_DEBUG) {
        wi_users_error($e->getMessage(), 500);
    }

    wi_users_error('Users manager system error.', 500);
}
