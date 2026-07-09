<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Core Roles & Permissions AJAX Endpoint
|--------------------------------------------------------------------------
| Narrow endpoint used by the modern Roles/Permissions manager. This avoids
| overwriting the main WIAjax dispatcher while the WICMS cleanup is ongoing.
|--------------------------------------------------------------------------
*/

require_once dirname(__DIR__) . '/WIClass/WI.php';
require_once dirname(__DIR__) . '/WIClass/WIRolesPermissionsService.php';

WISession::startSession();

function wi_roles_permissions_json(array $payload, int $status = 200): never
{
    if (!headers_sent()) {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
    }

    if (class_exists('WIToken')) {
        try {
            $payload['csrf_token'] = WIToken::getToken('wicms_roles_permissions_manager');
        } catch (Throwable $ignored) {
        }
    }

    echo json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    exit;
}

function wi_roles_permissions_error(string $message, int $status = 400, array $data = []): never
{
    wi_roles_permissions_json([
        'success' => false,
        'status' => 'error',
        'message' => $message,
        'msg' => $message,
        'data' => $data,
    ], $status);
}

function wi_roles_permissions_required_permissions(string $action): array
{
    $map = [
        'wicms_roles_permissions_dashboard' => ['roles.view', 'permissions.view'],
        'wicms_role_get' => ['roles.view'],
        'wicms_role_save' => ['roles.edit'],
        'wicms_role_delete' => ['roles.delete'],
        'wicms_permission_get' => ['permissions.view'],
        'wicms_permission_save' => ['permissions.edit'],
        'wicms_permission_delete' => ['permissions.delete'],
        'wicms_role_permissions_save' => ['permissions.edit'],
    ];

    return $map[$action] ?? ['roles.view', 'permissions.view'];
}

function wi_roles_permissions_require_permission(WIAdmin $admin, string $action): void
{
    $required = wi_roles_permissions_required_permissions($action);

    if (!$admin->hasAnyPermission($required)) {
        wi_roles_permissions_error('You do not have permission to perform this roles/permissions action.', 403, [
            'required_permissions' => $required,
            'action' => $action,
        ]);
    }
}

try {
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        wi_roles_permissions_error('Invalid request method.', 405);
    }

    if (class_exists('WIRequest') && method_exists('WIRequest', 'isAjax') && !WIRequest::isAjax()) {
        wi_roles_permissions_error('Invalid request type.', 403);
    }

    if (class_exists('WIRequest') && method_exists('WIRequest', 'sameOrigin') && !WIRequest::sameOrigin()) {
        wi_roles_permissions_error('Invalid origin.', 403);
    }

    $submittedToken = $_POST['csrf_token'] ?? null;
    if (is_array($submittedToken)) {
        wi_roles_permissions_error('Invalid security token.', 403);
    }

    if (class_exists('WIToken') && method_exists('WIToken', 'validate')) {
        if (!WIToken::validate('wicms_roles_permissions_manager', $submittedToken !== null ? (string)$submittedToken : null)) {
            wi_roles_permissions_error('Invalid security token.', 403);
        }
    }

    $login = new WILogin();
    if (!$login->isLoggedIn()) {
        wi_roles_permissions_error('You must be logged in.', 401);
    }

    $currentAdminId = (int)WISession::get('user_id', 0);
    $admin = new WIAdmin($currentAdminId);
    if (!$admin->isAdmin()) {
        wi_roles_permissions_error('Admin access required.', 403);
    }

    $action = trim((string)($_POST['action'] ?? ''));
    wi_roles_permissions_require_permission($admin, $action);

    $service = new WIRolesPermissionsService();

    switch ($action) {
        case 'wicms_roles_permissions_dashboard':
            wi_roles_permissions_json([
                'success' => true,
                'status' => 'success',
                'message' => 'Roles and permissions loaded.',
                'data' => $service->dashboard(),
            ]);

        case 'wicms_role_get':
            $role = $service->getRole((int)($_POST['role_id'] ?? 0));
            if ($role === null) {
                wi_roles_permissions_error('Role not found.', 404);
            }

            wi_roles_permissions_json([
                'success' => true,
                'status' => 'success',
                'message' => 'Role loaded.',
                'data' => ['role' => $role],
            ]);

        case 'wicms_role_save':
            $result = $service->saveRole($_POST);
            wi_roles_permissions_json($result, (($result['status'] ?? 'error') === 'success') ? 200 : 422);

        case 'wicms_role_delete':
            $result = $service->deleteRole((int)($_POST['role_id'] ?? 0));
            wi_roles_permissions_json($result, (($result['status'] ?? 'error') === 'success') ? 200 : 422);

        case 'wicms_permission_get':
            $permission = $service->getPermission((int)($_POST['id'] ?? 0));
            if ($permission === null) {
                wi_roles_permissions_error('Permission not found.', 404);
            }

            wi_roles_permissions_json([
                'success' => true,
                'status' => 'success',
                'message' => 'Permission loaded.',
                'data' => ['permission' => $permission],
            ]);

        case 'wicms_permission_save':
            $result = $service->savePermission($_POST);
            wi_roles_permissions_json($result, (($result['status'] ?? 'error') === 'success') ? 200 : 422);

        case 'wicms_permission_delete':
            $result = $service->deletePermission((int)($_POST['id'] ?? 0));
            wi_roles_permissions_json($result, (($result['status'] ?? 'error') === 'success') ? 200 : 422);

        case 'wicms_role_permissions_save':
            $permissionIds = $_POST['permission_ids'] ?? [];
            if (!is_array($permissionIds)) {
                $permissionIds = [];
            }

            $result = $service->updateRolePermissions((int)($_POST['role_id'] ?? 0), $permissionIds);
            wi_roles_permissions_json($result, (($result['status'] ?? 'error') === 'success') ? 200 : 422);
    }

    wi_roles_permissions_error('Unknown roles/permissions action.', 400);
} catch (Throwable $e) {
    if (class_exists('WILogger')) {
        try {
            WILogger::error('WICMS roles/permissions manager error', [
                'message' => $e->getMessage(),
                'file' => basename($e->getFile()),
                'line' => $e->getLine(),
            ], 'roles_permissions');
        } catch (Throwable $ignored) {
        }
    }

    if (defined('APP_DEBUG') && APP_DEBUG) {
        wi_roles_permissions_error($e->getMessage(), 500);
    }

    wi_roles_permissions_error('Roles and permissions manager system error.', 500);
}
