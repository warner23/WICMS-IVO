<?php
declare(strict_types=1);

/**
 * FILE:
 * WICMS-IVO/WICore/WIClass/WIAjax.php
 *
 * Canonical root AJAX dispatcher for WICMS.
 */

require_once __DIR__ . '/WI.php';


final class WIAjax
{
    private WILogin $login;
    private WIRegister $register;

    public function __construct()
    {
        WISession::startSession();
        WIToken::cleanupExpiredCsrfTokens();

        $this->login = new WILogin();
        $this->register = new WIRegister();
    }

    public function handle(): never
    {
        $this->guardRequest();

        $action = WIRequest::postString('action', '');

        if ($action === '') {
            WIResponse::error('No action supplied.', 400);
        }

        $this->requireCsrfForAction($action);

        try {
            switch ($action) {
                /*
                |--------------------------------------------------------------------------
                | Auth
                |--------------------------------------------------------------------------
                */

                case 'checkLogin':
                    $this->handleLogin();

                case 'registerUser':
                    $this->handleRegister();

                case 'forgotPassword':
                    $this->handleForgotPassword();

                case 'resetPassword':
                    $this->handleResetPassword();

                /*
                |--------------------------------------------------------------------------
                | Account / profile
                |--------------------------------------------------------------------------
                */

                case 'updatePassword':
                    $this->handleUpdatePassword();

                case 'updateDetails':
                    $this->handleUpdateDetails();

                /*
                |--------------------------------------------------------------------------
                | Comments
                |--------------------------------------------------------------------------
                */

                case 'postComment':
                    $this->handlePostComment();

                case 'public_consent_save':
                    $this->handlePublicConsentSave();

                /*
                |--------------------------------------------------------------------------
                | Admin users / roles
                |--------------------------------------------------------------------------
                */

                case 'getUserDetails':
                    $this->handleGetUserDetails();

                case 'getUser':
                    $this->handleGetUser();

                case 'deleteUser':
                    $this->handleDeleteUser();

                case 'changeRole':
                    $this->handleChangeRole();

                case 'addRole':
                    $this->handleAddRole();

                case 'deleteRole':
                    $this->handleDeleteRole();

                case 'addUser':
                    $this->handleAddUser();

                case 'updateUser':
                    $this->handleUpdateUser();

                case 'banUser':
                    $this->handleBanUser();

                case 'unbanUser':
                    $this->handleUnbanUser();

                /*
                |--------------------------------------------------------------------------
                | Legacy passthroughs kept for compatibility
                |--------------------------------------------------------------------------
                */

                case 'nextSlider':
                    $this->legacyNextSlider();

                default:
                    WIResponse::error('Unknown action.', 404);
            }
        } catch (Throwable $e) {
            error_log('WIAjax error: ' . $e->getMessage());

            if (defined('APP_DEBUG') && APP_DEBUG) {
                WIResponse::error($e->getMessage(), 500);
            }

            WIResponse::error('System error.', 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Request guards
    |--------------------------------------------------------------------------
    */

    private function guardRequest(): void
    {
        if (!WIRequest::isPost()) {
            WIResponse::error('Method not allowed.', 405);
        }

        if (!WIRequest::isAjax()) {
            WIResponse::error('Invalid request type.', 403);
        }

        if (!WIRequest::sameOrigin()) {
            WIResponse::error('Invalid origin.', 403);
        }
    }

    private function requireCsrfForAction(string $action): void
    {
        $form = $this->csrfFormForAction($action);

        if ($form === null) {
            return;
        }

        if (!WIToken::validatePostToken($form)) {
            WIResponse::error('Invalid security token.', 403);
        }
    }

    private function csrfFormForAction(string $action): ?string
    {
        $map = [
            'checkLogin'      => 'login',
            'registerUser'    => 'register',
            'forgotPassword'  => 'forgot_password',
            'resetPassword'   => 'reset_password',
            'postComment'     => 'comment',
            'public_consent_save' => 'public_consent',
            'updatePassword'  => 'update_password',
            'updateDetails'   => 'update_details',
            'changeRole'      => 'change_role',
            'deleteUser'      => 'delete_user',
            'addRole'         => 'add_role',
            'deleteRole'      => 'delete_role',
            'addUser'         => 'add_user',
            'updateUser'      => 'update_user',
            'banUser'         => 'ban_user',
            'unbanUser'       => 'unban_user',
            'nextSlider'      => 'next_slider',
        ];

        return $map[$action] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Common guards
    |--------------------------------------------------------------------------
    */

    private function requireAuth(): WIUser
    {
        if (!$this->login->isLoggedIn()) {
            WIResponse::error('Unauthorized.', 401);
        }

        $userId = (int) WISession::get('user_id', 0);

        if ($userId <= 0) {
            WIResponse::error('Unauthorized.', 401);
        }

        return new WIUser($userId);
    }

    private function requireAdmin(): WIUser
    {
        $user = $this->requireAuth();

        if (!method_exists($user, 'isAdmin') || !$user->isAdmin()) {
            WIResponse::error('Forbidden.', 403);
        }

        return $user;
    }

    /*
    |--------------------------------------------------------------------------
    | Auth handlers
    |--------------------------------------------------------------------------
    */

    private function handleLogin(): never
    {
        $logged = $this->login->userLogin(
            WIRequest::postString('username'),
            WIRequest::postString('password')
        );

        if ($logged !== true) {
            WIResponse::json($this->login->getLastResult(), 422);
        }

        $redirectPage = 'WIMembers/profile.php';
        $userId = (int) WISession::get('user_id', 0);

        if ($userId > 0) {
            $user = new WIUser($userId);

            if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
                $redirectPage = 'WIAdmin/dashboard.php';
            } elseif (function_exists('get_redirect_page')) {
                $redirectPage = (string) get_redirect_page();
            }
        }

        WIResponse::success([
            'message' => 'Login successful',
            'page' => $redirectPage,
            'redirect' => $redirectPage,
        ]);
    }

    private function handleRegister(): never
    {
        $result = $this->register->register(WIRequest::postArray('User'));

        if (($result['status'] ?? 'error') !== 'success') {
            WIResponse::json($result, 422);
        }

        WIResponse::success([
            'message' => (string) ($result['message'] ?? $result['msg'] ?? 'Registration successful'),
            'user_id' => (int) ($result['user_id'] ?? 0),
        ]);
    }

    private function handleForgotPassword(): never
    {
        $result = $this->register->forgotPassword(
            WIRequest::postString('email')
        );

        if ($result !== true) {
            WIResponse::error((string) $result, 422);
        }

        WIResponse::success([
            'message' => 'Password reset request sent.',
        ]);
    }

    private function handleResetPassword(): never
    {
        $this->register->resetPassword(
            WIRequest::postString('newPass'),
            WIRequest::postString('key')
        );

        WIResponse::success([
            'message' => 'Password reset complete.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Account / profile handlers
    |--------------------------------------------------------------------------
    */

    private function handleUpdatePassword(): never
    {
        $user = $this->requireAuth();

        $user->updatePassword(
            WIRequest::postString('oldpass'),
            WIRequest::postString('newpass')
        );

        WIResponse::success([
            'message' => 'Password updated.',
        ]);
    }

    private function handleUpdateDetails(): never
    {
        $user = $this->requireAuth();
        $details = WIRequest::postArray('details');

        $user->updateDetails($details);

        WIResponse::success([
            'message' => 'Details updated.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Comments
    |--------------------------------------------------------------------------
    */

    private function handlePostComment(): never
    {
        $this->requireAuth();

        $comment = new WIComment();
        $userId = (int) WISession::get('user_id', 0);

        $html = $comment->insertComment(
            $userId,
            WIRequest::postString('comment')
        );

        WIResponse::success([
            'html' => $html,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Admin users / roles
    |--------------------------------------------------------------------------
    */

    private function handleGetUserDetails(): never
    {
        $this->requireAdmin();

        $user = new WIUser(WIRequest::postInt('userId'));
        WIResponse::json($user->getAll());
    }

    private function handleGetUser(): never
    {
        $this->requireAdmin();

        $user = new WIUser(WIRequest::postInt('userId'));
        WIResponse::json($user->getAll());
    }

    private function handleDeleteUser(): never
    {
        $this->requireAdmin();

        $user = new WIUser(WIRequest::postInt('userId'));
        $user->deleteUser();

        WIResponse::success([
            'message' => 'User deleted.',
        ]);
    }


    private function handlePublicConsentSave(): never
    {
        if (!class_exists('WIConsentManager')) {
            $managerClass = __DIR__ . '/WIConsentManager.php';
            if (is_file($managerClass)) {
                require_once $managerClass;
            }
        }

        if (!class_exists('WIConsentManager')) {
            WIResponse::error('Consent manager could not be loaded.', 500);
        }

        $manager = new WIConsentManager();
        $result = $manager->recordPublicConsent([
            'consent_id' => WIRequest::postString('consent_id', ''),
            'categories' => is_array($_POST['categories'] ?? null) ? $_POST['categories'] : [],
        ]);

        WIResponse::json($result);
    }

    private function handleChangeRole(): never
    {
        $this->requireAdmin();

        $user = new WIUser(WIRequest::postInt('userId'));
        $role = ucfirst((string) $user->changeRole());

        WIResponse::success([
            'message' => 'Role updated.',
            'role' => $role,
        ]);
    }

    private function handleAddRole(): never
    {
        $this->requireAdmin();

        $roleName = WIRequest::postString('role');

        if ($roleName === '') {
            WIResponse::error('Role name is required.', 422);
        }

        $role = new WIRole();
        $result = $role->add($roleName);

        if (is_array($result)) {
            WIResponse::json($result);
        }

        WIResponse::success([
            'message' => 'Role added.',
            'roleName' => $roleName,
        ]);
    }

    private function handleDeleteRole(): never
    {
        $this->requireAdmin();

        $roleId = WIRequest::postInt('roleId');

        if ($roleId <= 0) {
            WIResponse::error('Invalid role id.', 422);
        }

        $role = new WIRole();
        $role->delete($roleId);

        WIResponse::success([
            'message' => 'Role deleted.',
        ]);
    }

    private function handleAddUser(): never
    {
        $this->requireAdmin();

        $user = new WIUser(null);
        $result = $user->add($_POST);

        if (is_array($result)) {
            WIResponse::json($result);
        }

        WIResponse::success([
            'message' => 'User added.',
        ]);
    }

    private function handleUpdateUser(): never
    {
        $this->requireAdmin();

        $userId = WIRequest::postInt('userId');

        if ($userId <= 0) {
            WIResponse::error('Invalid user id.', 422);
        }

        $user = new WIUser($userId);
        $user->updateUser($_POST);

        WIResponse::success([
            'message' => 'User updated.',
        ]);
    }

    private function handleBanUser(): never
    {
        $this->requireAdmin();

        $userId = WIRequest::postInt('userId');

        if ($userId <= 0) {
            WIResponse::error('Invalid user id.', 422);
        }

        $user = new WIUser($userId);
        $user->updateInfo(['banned' => 'Y']);

        WIResponse::success([
            'message' => 'User banned.',
        ]);
    }

    private function handleUnbanUser(): never
    {
        $this->requireAdmin();

        $userId = WIRequest::postInt('userId');

        if ($userId <= 0) {
            WIResponse::error('Invalid user id.', 422);
        }

        $user = new WIUser($userId);
        $user->updateInfo(['banned' => 'N']);

        WIResponse::success([
            'message' => 'User unbanned.',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Legacy passthrough kept for active front-end usage
    |--------------------------------------------------------------------------
    */

    private function legacyNextSlider(): never
    {
        $pagin = new WIPagination();

        $result = $pagin->SlideNextPagination(
            WIRequest::post('ele'),
            WIRequest::post('pagin'),
            WIRequest::post('clas'),
            WIRequest::post('item_per_page'),
            WIRequest::post('current_page'),
            WIRequest::post('total_records'),
            WIRequest::post('total_pages')
        );

        /*
         * Some legacy methods echo directly, some may return content.
         * If returned, wrap it in JSON for the new JS contract.
         */
        if ($result !== null) {
            WIResponse::success([
                'html' => (string) $result,
            ]);
        }

        exit;
    }
}

$ajax = new WIAjax();
$ajax->handle();