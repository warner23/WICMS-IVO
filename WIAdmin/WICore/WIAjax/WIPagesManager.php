<?php
declare(strict_types=1);

/**
 * WICMS Core Pages Manager AJAX Endpoint
 * Location: /WIAdmin/WICore/WIAjax/WIPagesManager.php
 */

require_once dirname(__DIR__) . '/WIClass/WI.php';
require_once dirname(__DIR__) . '/WIClass/WIPage.php';

if (class_exists('WISession')) {
    WISession::startSession();
}

function wi_pages_json(array $payload, int $status = 200): never
{
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('X-Content-Type-Options: nosniff');
    }

    if (class_exists('WIToken')) {
        try {
            $payload['csrf_token'] = WIToken::getToken('wicms_pages_manager');
        } catch (Throwable $ignored) {
        }
    }

    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function wi_pages_error(string $message, int $status = 400, array $data = []): never
{
    wi_pages_json([
        'success' => false,
        'status' => 'error',
        'message' => $message,
        'msg' => $message,
        'data' => $data,
    ], $status);
}

function wi_pages_normalise_result(array $result): array
{
    $status = (string) ($result['status'] ?? 'error');
    $message = (string) ($result['message'] ?? ($result['msg'] ?? ($status === 'success' ? 'Action completed.' : 'Action failed.')));

    $result['success'] = $status === 'success';
    $result['status'] = $status;
    $result['message'] = $message;
    $result['msg'] = $message;

    return $result;
}

try {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        wi_pages_error('Invalid request method.', 405);
    }

    if (class_exists('WIRequest') && method_exists('WIRequest', 'isAjax') && !WIRequest::isAjax()) {
        wi_pages_error('Invalid request type.', 403);
    }

    if (class_exists('WIRequest') && method_exists('WIRequest', 'sameOrigin') && !WIRequest::sameOrigin()) {
        wi_pages_error('Invalid origin.', 403);
    }

    $submittedToken = $_POST['csrf_token'] ?? null;
    if (is_array($submittedToken)) {
        wi_pages_error('Invalid security token.', 403);
    }

    if (class_exists('WIToken') && method_exists('WIToken', 'validate')) {
        if (!WIToken::validate('wicms_pages_manager', $submittedToken !== null ? (string) $submittedToken : null)) {
            wi_pages_error('Invalid security token.', 403);
        }
    }

    if (class_exists('WILogin')) {
        $login = new WILogin();
        if (method_exists($login, 'isLoggedIn') && !$login->isLoggedIn()) {
            wi_pages_error('You must be logged in.', 401);
        }
    }

    if (class_exists('WIAdmin')) {
        $currentAdminId = class_exists('WISession') ? (int) WISession::get('user_id', 0) : 0;
        $admin = new WIAdmin($currentAdminId);
        if (method_exists($admin, 'isAdmin') && !$admin->isAdmin()) {
            wi_pages_error('Admin access required.', 403);
        }
    }

    $action = trim((string) ($_POST['action'] ?? ''));
    $service = new WIPage();

    switch ($action) {
        case 'wicms_pages_list':
            wi_pages_json([
                'success' => true,
                'status' => 'success',
                'message' => 'Pages loaded.',
                'data' => [
                    'pages' => $service->getPages(),
                    'modules' => $service->getAvailableContentModules(),
                    'destinations' => $service->getRouteDestinations(),
                ],
            ]);

        case 'wicms_page_save':
        case 'savePage':
            $result = $service->savePage($_POST);
            wi_pages_json(wi_pages_normalise_result($result), (($result['status'] ?? 'error') === 'success') ? 200 : 422);

        case 'wicms_page_assign_module':
        case 'assignPageModule':
            $result = $service->assignPageModule($_POST);
            wi_pages_json(wi_pages_normalise_result($result), (($result['status'] ?? 'error') === 'success') ? 200 : 422);

        case 'wicms_page_delete':
        case 'deletePage':
            $result = $service->deletePage((int) ($_POST['id'] ?? 0));
            wi_pages_json(wi_pages_normalise_result($result), (($result['status'] ?? 'error') === 'success') ? 200 : 422);
    }

    wi_pages_error('Unknown pages action.', 400);
} catch (Throwable $e) {
    if (class_exists('WILogger')) {
        try {
            WILogger::error('WICMS pages manager error', [
                'message' => $e->getMessage(),
                'file' => basename($e->getFile()),
                'line' => $e->getLine(),
            ], 'pages');
        } catch (Throwable $ignored) {
        }
    }

    if (defined('APP_DEBUG') && APP_DEBUG) {
        wi_pages_error($e->getMessage(), 500);
    }

    wi_pages_error('Pages manager system error.', 500);
}
