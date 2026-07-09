<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| File: /WIAdmin/WICore/WIClass/WIAjax.php
| Type: Admin AJAX Dispatcher
| Layer: Controller (Router Only)
| Version: 4.4.0
| Status: Legal Register System Error Hotfix
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Canonical admin AJAX dispatcher for admin-side actions across WICMS,
| WICOS, WIKitchenCompli, builder tooling, bug reporting, plugins, and
| marketplace flows.
|
| Rules:
| - Dispatcher only
| - No business logic here
| - No direct database access here
| - Compliance actions route into WICompliance
| - Shared/admin/platform actions route into WICMS/core handlers
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/WI.php';
require_once __DIR__ . '/WICMSAdminActionGuard.php';

final class WIAdminAjax
{
    /**
     * Login service.
     *
     * @var WILogin
     */
    private WILogin $login;

    /**
     * Compliance bridge.
     *
     * @var WICompliance|null
     */
    private ?WICompliance $compliance = null;

    /**
     * Bug reporter bridge.
     *
     * @var WIBugReporter|null
     */
    private ?WIBugReporter $bugReporter = null;

    /**
     * HR/Org route handler.
     *
     * @var WIAjaxOrgHr|null
     */
    private ?WIAjaxOrgHr $orgHrRouter = null;

    /**
     * Construct the dispatcher.
     *
     * @return void
     */
    public function __construct()
    {
        WISession::startSession();

        if (class_exists('WIToken') && method_exists('WIToken', 'cleanupExpiredCsrfTokens')) {
            WIToken::cleanupExpiredCsrfTokens();
        }

        $this->login = new WILogin();
    }

    /**
     * Handle the incoming AJAX request.
     *
     * @return never
     */
    public function handle(): never
    {
        $this->guardRequest();

        $action = WIRequest::postString('action', '');
        $method = 'POST';

        if ($action === '') {
            $action = WIRequest::string('action', '', 'get');
            $method = 'GET';
        }

        if ($action === '') {
            WIResponse::error('No action supplied.', [], 400);
        }

        $this->requireCsrfForAction($action, $method);
        $this->requirePermissionForAction($action, $method);

        try {
            if ($method === 'POST') {
                $this->handlePost($action);
            }

            $this->handleGet($action);
        } catch (Throwable $e) {
            if (class_exists('WILogger')) {
                try {
                    WILogger::error('Admin WIAjax error', [
                        'action'  => $action,
                        'message' => $e->getMessage(),
                        'trace'   => defined('APP_DEBUG') && APP_DEBUG ? $e->getTraceAsString() : '',
                    ], 'admin_ajax');
                } catch (Throwable $logError) {
                }
            }

            @file_put_contents(
                __DIR__ . '/WIAjax_runtime_error.log',
                '[' . date('Y-m-d H:i:s') . '] action=' . $action
                    . ' message=' . $e->getMessage()
                    . ' file=' . $e->getFile()
                    . ' line=' . $e->getLine()
                    . PHP_EOL,
                FILE_APPEND
            );

            if (in_array($action, ['checkLogin', 'checkAdminLogin'], true)) {
                $isLocal = in_array((string) ($_SERVER['REMOTE_ADDR'] ?? ''), ['127.0.0.1', '::1', ''], true)
                    || str_contains(strtolower((string) ($_SERVER['HTTP_HOST'] ?? '')), 'localhost');

                WIResponse::json([
                    'status'  => 'error',
                    'message' => $isLocal
                        ? 'Admin AJAX error: ' . $e->getMessage() . ' in ' . basename($e->getFile()) . ':' . $e->getLine()
                        : 'System error.',
                    'errors'  => [],
                    'data'    => $isLocal ? [
                        'action' => $action,
                        'file'   => basename($e->getFile()),
                        'line'   => $e->getLine(),
                    ] : [],
                ], 500);
            }

            $pluginActions = [
                'install_plugin',
                'uninstall_plugin',
                'enable_plugin',
                'disable_plugin',
                'plugin_validate_license',
                'plugin_create_order',
                'plugin_complete_purchase',
            ];

            if (in_array($action, $pluginActions, true)) {
                $safeMessage = trim($e->getMessage());
                if ($safeMessage === '') {
                    $safeMessage = 'Unknown plugin handler exception.';
                }

                WIResponse::error(
                    'Plugin action failed before install handler: ' . $safeMessage,
                    [
                        'action' => $action,
                        'file'   => basename($e->getFile()),
                        'line'   => $e->getLine(),
                    ],
                    500
                );
            }

            if (defined('APP_DEBUG') && APP_DEBUG) {
                WIResponse::error($e->getMessage(), [], 500);
            }

            WIResponse::error('System error.', [], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Request Guards
    |--------------------------------------------------------------------------
    */

    /**
     * Guard the request type and origin.
     *
     * @return void
     */
    private function guardRequest(): void
    {
        if (!WIRequest::isAjax()) {
            WIResponse::error('Invalid request type.', [], 403);
        }

        if (method_exists('WIRequest', 'sameOrigin') && !WIRequest::sameOrigin()) {
            WIResponse::error('Invalid origin.', [], 403);
        }
    }

    /**
     * Require CSRF protection for mapped POST actions.
     *
     * @param string $action Action name.
     * @param string $method HTTP method.
     *
     * @return void
     */
    private function requireCsrfForAction(string $action, string $method): void
    {
        if ($method !== 'POST') {
            return;
        }

        $form = $this->csrfFormForAction($action);

        if ($form === null) {
            return;
        }

        if (!class_exists('WIToken')) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | CSRF compatibility for staged plugin marketplace actions
        |--------------------------------------------------------------------------
        |
        | Normal admin forms post csrf_token. The plugin marketplace also keeps
        | one token per action because install/activate/disable cards can run from
        | the same page without a dedicated form. Accept either value, but always
        | validate it against the mapped form key.
        |
        */
        $submittedToken = $_POST['csrf_token'] ?? null;
        if (is_array($submittedToken)) {
            WIResponse::error('Invalid security token.', [], 403);
        }

        if (method_exists('WIToken', 'validate') && WIToken::validate($form, $submittedToken !== null ? (string) $submittedToken : null)) {
            return;
        }

        $pluginSpecificField = 'wi_plugin_csrf_' . $form;
        $pluginSpecificToken = $_POST[$pluginSpecificField] ?? null;
        if (is_array($pluginSpecificToken)) {
            WIResponse::error('Invalid security token.', [], 403);
        }

        if (method_exists('WIToken', 'validate') && WIToken::validate($form, $pluginSpecificToken !== null ? (string) $pluginSpecificToken : null)) {
            return;
        }

        $this->csrfFailure($action, $form);
    }

    /**
     * Return a fresh token for auth forms instead of leaving the user stuck.
     *
     * This keeps CSRF protection enabled, but makes fresh installs and expired
     * login pages recover cleanly: the browser receives a new token and the
     * login JS retries once.
     *
     * @param string $action Action name.
     * @param string $form CSRF form key.
     *
     * @return never
     */
    private function csrfFailure(string $action, string $form): never
    {
        $freshToken = '';

        if (class_exists('WIToken') && method_exists('WIToken', 'getToken')) {
            try {
                $freshToken = (string) WIToken::getToken($form);
            } catch (Throwable $e) {
                $freshToken = '';
            }
        }

        $recoverableAuthActions = [
            'checkLogin',
            'checkAdminLogin',
            'registerUser',
            'forgotPassword',
            'resetPassword',
        ];

        if (in_array($action, $recoverableAuthActions, true)) {
            WIResponse::json([
                'status'     => 'error',
                'message'    => 'Security token refreshed. Please try again.',
                'errors'     => [],
                'csrf_token' => $freshToken,
                'data'       => [
                    'csrf_token' => $freshToken,
                ],
            ], 403);
        }

        WIResponse::error('Invalid security token.', [], 403);
    }

    /**
     * Map actions to CSRF form keys.
     *
     * @param string $action Action name.
     *
     * @return string|null
     */
    private function csrfFormForAction(string $action): ?string
    {
        $map = [
            'checkLogin'                           => 'login',
            'checkAdminLogin'                      => 'login',
            'registerUser'                         => 'register',
            'forgotPassword'                       => 'forgot_password',
            'resetPassword'                        => 'reset_password',
            'updatePassword'                       => 'update_password',
            'updateDetails'                        => 'update_details',

            'saveRole'                             => 'save_role',
            'deleteRole'                           => 'delete_role',
            'updateRolePermissions'                => 'update_role_permissions',
            'savePermission'                       => 'save_permission',
            'deletePermission'                     => 'delete_permission',
            'toggleRolePermission'                 => 'toggle_role_permission',

            'site_settings'                        => 'site_settings',
            'database_settings'                    => 'database_settings',
            'email_settings'                       => 'email_settings',
            'mailer_settings'                      => 'mailer_settings',
            'session_settings'                     => 'session_settings',
            'verification_settings'                => 'verification_settings',
            'bug_reporter_settings'             => 'wi_ajax',
            'encryption'                           => 'encryption_settings',
            'login_settings'                       => 'login_settings',
            'social_settings'                      => 'social_settings',
            'twitter'                              => 'twitter_settings',
            'header_settings'                      => 'header_settings',
            'footer_settings'                      => 'footer_settings',
            'wicms_header_footer_save'             => 'wicms_header_footer_save',
            'wicms_header_footer_upload'           => 'wicms_header_footer_upload',
            'lang_settings'                        => 'lang_settings',
            'multilanguage'                        => 'multilanguage',
            'wicms_language_settings_save'         => 'wicms_language_settings',
            'wicms_language_save'                  => 'wicms_language_save',
            'wicms_language_delete'                => 'wicms_language_delete',
            'wicms_translation_save'               => 'wicms_translation_save',
            'wicms_translation_delete'             => 'wicms_translation_delete',
            'version_control'                      => 'version_control',

            'editMenu'                             => 'edit_menu',
            'menuEdit'                             => 'menu_edit',
            'newmenuitem'                          => 'new_menu_item',
            'DeleteMenu'                           => 'delete_menu',
            'menuLink'                             => 'menu_link',
            'saveSidebarMenu'                      => 'save_sidebar_menu',
            'editAdminMenu'                        => 'edit_admin_menu',
            'adminMenuEdit'                        => 'admin_menu_edit',
            'newAdminMenuItem'                     => 'new_admin_menu_item',
            'deleteAdminMenu'                      => 'delete_admin_menu',

            'savePage'                             => 'save_page',
            'deletePage'                           => 'delete_page',

            'themeActivate'                        => 'theme_activate',
            'theme'                                => 'theme_create',
            'setTheme'                             => 'set_theme',
            'deletetheme'                          => 'delete_theme',
            'editMetaDetails'                      => 'edit_meta_details',
            'DeleteMeta'                           => 'delete_meta',
            'editCssDetails'                       => 'edit_css_details',
            'DeleteCss'                            => 'delete_css',
            'editJsDetails'                        => 'edit_js_details',
            'deletejs'                             => 'delete_js',
            'editScript'                           => 'edit_script',

            'install_module'                       => 'install_module',
            'mod_install'                          => 'install_module',
            'uninstall_module'                     => 'uninstall_module',
            'mod_uninstall'                        => 'uninstall_module',
            'enable_module'                        => 'enable_module',
            'mod_enable'                           => 'enable_module',
            'disable_module'                       => 'disable_module',
            'mod_disable'                          => 'disable_module',

            'install_element'                      => 'install_element',
            'element_install'                      => 'install_element',
            'uninstall_element'                    => 'uninstall_element',
            'ele_uninstall'                        => 'uninstall_element',
            'enable_element'                       => 'enable_element',
            'Element_enable'                       => 'enable_element',
            'disable_element'                      => 'disable_element',
            'Element_disable'                      => 'disable_element',

            'install_plugin'                       => 'install_plugin',
            'uninstall_plugin'                     => 'uninstall_plugin',
            'enable_plugin'                        => 'enable_plugin',
            'disable_plugin'                       => 'disable_plugin',
            'plugin_create_order'                  => 'plugin_create_order',
            'plugin_complete_purchase'             => 'plugin_complete_purchase',
            'plugin_validate_license'              => 'plugin_validate_license',

            'deleteUser'                           => 'delete_user',
            'changeRole'                           => 'change_role',
            'addUser'                              => 'add_user',
            'updateUser'                           => 'update_user',
            'banUser'                              => 'ban_user',
            'unbanUser'                            => 'unban_user',
            'addAttr'                              => 'add_attr',

            'addSite'                              => 'wi_compliance_site',
            'updateSite'                           => 'wi_compliance_site',
            'deleteSite'                           => 'wi_compliance_site',
            'addChecklist'                         => 'wi_compliance_checklist',
            'updateChecklist'                      => 'wi_compliance_checklist',
            'deleteChecklist'                      => 'wi_compliance_checklist',
            'addQuestion'                          => 'wi_compliance_question',
            'updateQuestion'                       => 'wi_compliance_question',
            'deleteQuestion'                       => 'wi_compliance_question',
            'addEquipment'                         => 'wi_compliance_equipment',
            'updateEquipment'                      => 'wi_compliance_equipment',
            'deleteEquipment'                      => 'wi_compliance_equipment',
            'addIncident'                          => 'wi_compliance_incident',
            'updateIncident'                       => 'wi_compliance_incident',
            'deleteIncident'                       => 'wi_compliance_incident',
            'addDocument'                          => 'wi_compliance_document',
            'updateDocument'                       => 'wi_compliance_document',
            'deleteDocument'                       => 'wi_compliance_document',
            'addTrainingItem'                      => 'wi_compliance_training',
            'updateTrainingItem'                   => 'wi_compliance_training',
            'deleteTrainingItem'                   => 'wi_compliance_training',
            'addNotificationRule'                  => 'wi_compliance_notification',
            'updateNotificationRule'               => 'wi_compliance_notification',
            'deleteNotificationRule'               => 'wi_compliance_notification',
            'getOperationalAction'                 => 'wi_compliance_operational_action',
            'saveOperationalAction'                => 'wi_compliance_operational_action',
            'getOperationalActionFeed'             => 'wi_compliance_operational_action',
            'getAuditTemplate'                     => 'wi_compliance_audit',
            'saveAuditTemplate'                    => 'wi_compliance_audit',
            'runAuditTemplate'                     => 'wi_compliance_audit',
            'getAuditRunView'                      => 'wi_compliance_audit',
            'getAuditFindingView'                  => 'wi_compliance_audit',
            'createCorrectiveActionFromAuditFinding' => 'wi_compliance_audit',
            'updateAuditFindingStatus'             => 'wi_compliance_audit',
            'save_settings'                        => 'wi_compliance_settings',
            'save_options'                         => 'wi_compliance_options',
            'setup_workspace'                      => 'wi_compliance_setup',
            'save_setup'                           => 'wi_compliance_setup',

            'saveComplianceRequirementReview'      => 'wi_compliance_registry',
            'saveComplianceChecklistBuilderLayout' => 'wi_compliance_builder',
            'saveComplianceChecklistQuestionOrder' => 'wi_compliance_builder',
            'saveComplianceChecklistQuestionSections' => 'wi_compliance_builder',
        ];

        return $map[$action] ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | Guards
    |--------------------------------------------------------------------------
    */

    /**
     * Ensure the user is logged in.
     *
     * @return void
     */
    private function requireLoggedIn(): void
    {
        if (!$this->login->isLoggedIn()) {
            WIResponse::error('You must be logged in.', [], 401);
        }
    }

    /**
     * Ensure the current user is an admin and return the admin object.
     *
     * @return WIAdmin
     */
    private function requireAdmin(): WIAdmin
    {
        $this->requireLoggedIn();

        $admin = new WIAdmin((int) WISession::get('user_id', 0));

        if (!$admin->isAdmin()) {
            WIResponse::error('Admin access required.', [], 403);
        }

        return $admin;
    }


    /**
     * Enforce granular permissions for mapped AJAX actions.
     *
     * Unknown actions are left to their existing route-level guards until
     * they are mapped in WICMSAdminActionGuard.
     *
     * @param string $action Action name.
     * @param string $method HTTP method.
     *
     * @return void
     */
    private function requirePermissionForAction(string $action, string $method): void
    {
        if (!class_exists('WICMSAdminActionGuard')) {
            return;
        }

        if (!WICMSAdminActionGuard::hasRule($action, $method)) {
            return;
        }

        $admin = $this->requireAdmin();
        WICMSAdminActionGuard::enforce($action, $method, $admin);
    }

    /**
     * Call a method only if it exists.
     *
     * @param object $object Target object.
     * @param string $method Method name.
     * @param array<int, mixed> $args Arguments.
     *
     * @return mixed
     */
    private function safeMethodCall(object $object, string $method, array $args = []): mixed
    {
        if (!method_exists($object, $method)) {
            WIResponse::error("Unsupported legacy action: missing method {$method}.", [], 400);
        }

        return $object->{$method}(...$args);
    }

    /**
     * Convert a legacy result array into a standard success payload.
     *
     * @param array<string, mixed> $result Result payload.
     * @param string $fallbackMessage Fallback message.
     *
     * @return never
     */
    private function successFromResult(array $result, string $fallbackMessage = 'Success'): never
    {
        $status = (string) ($result['status'] ?? 'success');

        if ($status !== 'success' && $status !== 'completed') {
            WIResponse::json($result, 422);
        }

        WIResponse::json([
            'status'  => 'success',
            'message' => (string) ($result['message'] ?? $result['msg'] ?? $fallbackMessage),
            'data'    => $result['data'] ?? $result,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | POST Dispatcher
    |--------------------------------------------------------------------------
    */

    /**
     * Handle POST actions.
     *
     * @param string $action Action name.
     *
     * @return never
     */
    private function handlePost(string $action): never
    {
        if ($this->routeMedia($action, 'POST')) {
            exit;
        }

        /*
        |----------------------------------------------------------------------
        | Auth actions must run before any optional plugin/compliance routing.
        |----------------------------------------------------------------------
        |
        | Fresh WICMS installs may not have optional plugin/compliance classes
        | available yet. Login/register/password actions are core and must stay
        | independent so alogin.php cannot be hidden behind a generic System
        | error from an unrelated router.
        |----------------------------------------------------------------------
        */
        if (in_array($action, ['checkLogin', 'checkAdminLogin', 'registerUser', 'forgotPassword', 'resetPassword'], true)) {
            if ($this->routeWICMS($action, 'POST')) {
                exit;
            }
        }

        /*
        |----------------------------------------------------------------------
        | Plugin actions must run before Compliance/Builder routing.
        |----------------------------------------------------------------------
        |
        | Some compliance bootstrap paths resolve heavy engine services even for
        | unrelated actions. Local plugin install must stay independent so a
        | plugin-store request cannot be hidden behind a generic compliance
        | bootstrap "System error".
        |----------------------------------------------------------------------
        */
        if ($this->routePlugin($action, 'POST')) {
            exit;
        }

        /*
        |----------------------------------------------------------------------
        | WIOrg / WIHR actions must route before Compliance.
        |----------------------------------------------------------------------
        |
        | Compliance is intentionally broad and can bootstrap heavy services.
        | Organisation and HR actions are independent admin actions, so they
        | must be handled before the compliance fallback to prevent unrelated
        | POST requests from being hidden behind a generic "System error".
        |----------------------------------------------------------------------
        */
        if ($this->routeOrgHr($action, 'POST')) {
            exit;
        }

        if ($this->routePluginAdmin($action, 'POST')) {
            exit;
        }

        if ($this->routeCompliance($action, 'POST')) {
            exit;
        }

        if ($this->routeBugReporter($action, 'POST')) {
            exit;
        }

        if ($this->routeBuilder($action, 'POST')) {
            exit;
        }

        if ($this->routeMarketplace($action, 'POST')) {
            exit;
        }

        if ($this->routeWICMS($action, 'POST')) {
            exit;
        }

        WIResponse::error('Unknown POST action: ' . $action, [], 400);
    }



    /*
    |--------------------------------------------------------------------------
    | GET Dispatcher
    |--------------------------------------------------------------------------
    */

    /**
     * Handle GET actions.
     *
     * @param string $action Action name.
     *
     * @return never
     */
    private function handleGet(string $action): never
    {
        if ($this->routeMedia($action, 'GET')) {
            exit;
        }

        if ($this->routePlugin($action, 'GET')) {
            exit;
        }

        if ($this->routePluginAdmin($action, 'GET')) {
            exit;
        }

        if ($this->routeCompliance($action, 'GET')) {
            exit;
        }

        if ($this->routeBugReporter($action, 'GET')) {
            exit;
        }

        if ($this->routeOrgHr($action, 'GET')) {
            exit;
        }

        if ($this->routeBuilder($action, 'GET')) {
            exit;
        }

        if ($this->routeMarketplace($action, 'GET')) {
            exit;
        }

        if ($this->routeWICMS($action, 'GET')) {
            exit;
        }

        WIResponse::error('Unknown GET action: ' . $action, [], 400);
    }



    /*
    |--------------------------------------------------------------------------
    | Media
    |--------------------------------------------------------------------------
    */

    /**
     * Routes WIMedia AJAX actions.
     *
     * @param string $action Action name.
     * @param string $method HTTP method.
     *
     * @return bool
     */
    private function routeMedia(string $action, string $method): bool
    {
        $canonicalAction = $this->normaliseMediaAction($action);

        $mediaActions = [
            'media_upload',
            'media_upload_bulk',
            'media_list',
            'media_get',
            'media_update',
            'media_delete',
            'media_restore',
            'media_link',
            'media_unlink',
            'media_linked',
            'media_view_url',
            'media_download_url',
            'media_access_check',
        ];

        if (!in_array($canonicalAction, $mediaActions, true)) {
            return false;
        }

        $this->requireAdmin();

        require_once __DIR__ . '/WIAjaxMedia.php';

        $request = $method === 'GET' ? $_GET : $_POST;
        $router = new WIAjaxMedia(new WIMedia(WIdb::getInstance()));

        WIResponse::json(
            $router->handle($canonicalAction, $request, $_FILES)
        );

        return true;
    }

    /**
     * Normalises legacy and dotted WIMedia action names to canonical media_* actions.
     *
     * @param string $action Incoming action name.
     *
     * @return string
     */
    private function normaliseMediaAction(string $action): string
    {
        $aliases = [
            'media.upload' => 'media_upload',
            'media.uploadBulk' => 'media_upload_bulk',
            'media.list' => 'media_list',
            'media.get' => 'media_get',
            'media.update' => 'media_update',
            'media.delete' => 'media_delete',
            'media.restore' => 'media_restore',
            'media.link' => 'media_link',
            'media.unlink' => 'media_unlink',
            'media.linked' => 'media_linked',
            'media.viewUrl' => 'media_view_url',
            'media.downloadUrl' => 'media_download_url',
            'media.accessCheck' => 'media_access_check',
            'uploadMedia' => 'media_upload',
            'uploadBulkMedia' => 'media_upload_bulk',
            'getMediaList' => 'media_list',
            'getMediaItem' => 'media_get',
            'updateMedia' => 'media_update',
            'deleteMedia' => 'media_delete',
            'restoreMedia' => 'media_restore',
            'linkMedia' => 'media_link',
            'unlinkMedia' => 'media_unlink',
            'getLinkedMedia' => 'media_linked',
        ];

        return $aliases[$action] ?? $action;
    }


    /*
    |--------------------------------------------------------------------------
    | Compliance
    |--------------------------------------------------------------------------
    */

    /**
     * Route compliance actions.
     *
     * @param string $action Action name.
     * @param string $method HTTP method.
     *
     * @return bool
     */
    private function routeCompliance(string $action, string $method): bool
    {
        /*
        |--------------------------------------------------------------------------
        | Modern compliance action delegation
        |--------------------------------------------------------------------------
        |
        | New database-driven compliance tabs use action keys like:
        | - compliance_dashboard_widget
        | - compliance_sites_load
        | - compliance_sites_by_business
        |
        | These must be delegated to WIAjaxCompliance before the legacy WIAjax
        | switch blocks, otherwise WIAjax.php will reject them as unknown actions.
        */
        $complianceCamelCaseActions = [
            'getComplianceSettingsData',
            'getComplianceSettingsSites',
            'getComplianceSettingsPageData',
            'getCompliancePackagesAddonsWorkspace',
        ];

        if (
            str_starts_with($action, 'compliance_')
            || str_starts_with($action, 'compliance.')
            || str_starts_with($action, 'equipment.')
            || str_starts_with($action, 'wimedia.proof.')
            || in_array($action, $complianceCamelCaseActions, true)
        ) {
            $this->requireAdmin();

            require_once __DIR__ . '/WIAjaxCompliance.php';

            $request = $method === 'POST' ? $_POST : $_GET;
            $router = new WIAjaxCompliance($this->compliance(), WIdb::getInstance());

            WIResponse::json($router->handle($action, $request));

            return true;
        }

        if ($method !== 'GET') {
            return false;
        }

        $c = $this->compliance();

        switch ($action) {
                /*
                |--------------------------------------------------------------------------
                | Core compliance CRUD / admin
                |--------------------------------------------------------------------------
                */

                case 'getComplianceOverviewData':
                    $this->requireAdmin();
                    WIResponse::json($c->getComplianceOverviewData($_GET));

                case 'getComplianceOverviewSites':
                    $this->requireAdmin();
                    WIResponse::json($c->getComplianceOverviewSites($_GET));


                case 'getSites':
                    $this->requireAdmin();
                    if (!class_exists('WIOrg')) {
                        $orgPath = __DIR__ . '/WIOrg.php';
                        if (is_file($orgPath)) {
                            require_once $orgPath;
                        }
                    }
                    $businessId = (int)($_GET['business_id'] ?? $_GET['org_business_id'] ?? $_POST['business_id'] ?? $_POST['org_business_id'] ?? 0);
                    $sites = class_exists('WIOrg') ? (array)(new WIOrg())->getSites($businessId) : [];
                    WIResponse::json([
                        'success' => true,
                        'status' => 'success',
                        'message' => 'Sites loaded from WIOrg.',
                        'data' => $sites,
                        'sites' => $sites,
                    ]);

                case 'addSite':
                    $this->requireAdmin();
                    WIResponse::json($c->addSite($_POST));

                case 'updateSite':
                    $this->requireAdmin();
                    WIResponse::json($c->updateSite(WIRequest::postInt('id'), $_POST));

                case 'deleteSite':
                    $this->requireAdmin();
                    WIResponse::json($c->deleteSite(WIRequest::postInt('id')));

                case 'getChecklists':
                    $this->requireAdmin();
                    WIResponse::json($c->getChecklists());

                case 'addChecklist':
                    $this->requireAdmin();
                    WIResponse::json($c->addChecklist($_POST));

                case 'updateChecklist':
                    $this->requireAdmin();
                    WIResponse::json($c->updateChecklist(WIRequest::postInt('id'), $_POST));

                case 'deleteChecklist':
                    $this->requireAdmin();
                    WIResponse::json($c->deleteChecklist(WIRequest::postInt('id')));

                case 'getQuestions':
                    $this->requireAdmin();
                    WIResponse::json($c->getQuestions(WIRequest::postInt('checklist_id')));

                case 'addQuestion':
                    $this->requireAdmin();
                    WIResponse::json($c->addQuestion($_POST));

                case 'updateQuestion':
                    $this->requireAdmin();
                    WIResponse::json($c->updateQuestion(WIRequest::postInt('id'), $_POST));

                case 'deleteQuestion':
                    $this->requireAdmin();
                    WIResponse::json($c->deleteQuestion(WIRequest::postInt('id')));

                case 'getEquipment':
                    $this->requireAdmin();
                    WIResponse::json($c->getEquipment());

                case 'addEquipment':
                    $this->requireAdmin();
                    WIResponse::json($c->addEquipment($_POST));

                case 'updateEquipment':
                    $this->requireAdmin();
                    WIResponse::json($c->updateEquipment(WIRequest::postInt('id'), $_POST));

                case 'deleteEquipment':
                    $this->requireAdmin();
                    WIResponse::json($c->deleteEquipment(WIRequest::postInt('id')));

                case 'getIncidents':
                    $this->requireAdmin();
                    WIResponse::json($c->getIncidents());

                case 'addIncident':
                    $this->requireAdmin();
                    WIResponse::json($c->addIncident($_POST));

                case 'updateIncident':
                    $this->requireAdmin();
                    WIResponse::json($c->updateIncident(WIRequest::postInt('id'), $_POST));

                case 'deleteIncident':
                    $this->requireAdmin();
                    WIResponse::json($c->deleteIncident(WIRequest::postInt('id')));

                case 'getDocuments':
                    $this->requireAdmin();
                    WIResponse::json($c->getDocuments());

                case 'addDocument':
                    $this->requireAdmin();
                    WIResponse::json($c->addDocument($_POST));

                case 'updateDocument':
                    $this->requireAdmin();
                    WIResponse::json($c->updateDocument(WIRequest::postInt('id'), $_POST));

                case 'deleteDocument':
                    $this->requireAdmin();
                    WIResponse::json($c->deleteDocument(WIRequest::postInt('id')));


                /*
                |--------------------------------------------------------------------------
                | Compliance Document Control Centre
                |--------------------------------------------------------------------------
                */

                case 'compliance_documents_load':
                    $this->requireAdmin();
                    WIResponse::json(
                        $c->getComplianceDocumentsPageData([
                            'business_id' => WIRequest::postInt('business_id', 0),
                            'org_business_id' => WIRequest::postInt('org_business_id', WIRequest::postInt('business_id', 0)),
                        ])
                    );

                case 'compliance_document_save':
                    $this->requireAdmin();
                    WIResponse::json($c->saveComplianceDocument($_POST));

                case 'compliance_document_archive':
                    $this->requireAdmin();
                    WIResponse::json($c->deleteComplianceDocument($_POST));

                case 'compliance_document_review':
                    $this->requireAdmin();
                    WIResponse::json($c->reviewComplianceDocument($_POST));

                case 'compliance_document_media_upload':
                    $this->requireAdmin();
                    WIResponse::json($c->uploadComplianceDocumentMedia($_POST));

                case 'compliance_document_media_link':
                    $this->requireAdmin();
                    WIResponse::json($c->linkComplianceDocumentMedia($_POST));

                case 'compliance_document_media_unlink':
                    $this->requireAdmin();
                    WIResponse::json($c->unlinkComplianceDocumentMedia($_POST));

                case 'compliance_document_media_list':
                    $this->requireAdmin();
                    WIResponse::json($c->getComplianceDocumentMedia($_POST));

                case 'getTraining':
                    $this->requireAdmin();
                    WIResponse::json($c->getTraining());

                case 'addTrainingItem':
                    $this->requireAdmin();
                    WIResponse::json($c->addTrainingItem($_POST));

                case 'updateTrainingItem':
                    $this->requireAdmin();
                    WIResponse::json($c->updateTrainingItem(WIRequest::postInt('id'), $_POST));

                case 'deleteTrainingItem':
                    $this->requireAdmin();
                    WIResponse::json($c->deleteTrainingItem(WIRequest::postInt('id')));

                /*
                |--------------------------------------------------------------------------
                | Compliance dashboard / alerts / reminders
                |--------------------------------------------------------------------------
                */

                case 'getAlerts':
                    $this->requireAdmin();
                    WIResponse::json($c->getAlerts());

                case 'getNextActions':
                    $this->requireAdmin();
                    WIResponse::json($c->getNextActions());

                case 'getComplianceScore':
                    $this->requireAdmin();
                    WIResponse::json($c->getComplianceScore());

                case 'getInspectionMode':
                    $this->requireAdmin();
                    WIResponse::json($c->getInspectionModeData());

                case 'getComplianceDashboardScope':
                    $this->requireAdmin();
                    WIResponse::json($c->getAdminDashboardScopeOptions());

                case 'saveComplianceDashboardLayout':
                    $this->requireAdmin();
                    WIResponse::json(
                        $c->saveAdminDashboardLayout(
                            is_array($_POST['layout'] ?? null) ? $_POST['layout'] : []
                        )
                    );

                case 'resetComplianceDashboardLayout':
                    $this->requireAdmin();
                    WIResponse::json($c->resetAdminDashboardLayout());

                case 'getReminders':
                    $this->requireAdmin();
                    WIResponse::json($c->getReminders());

                case 'getNotificationRules':
                    $this->requireAdmin();
                    WIResponse::json([
                        'success' => true,
                        'data'    => $c->getNotificationRules(),
                    ]);

                case 'saveNotificationRule':
                    $this->requireAdmin();
                    WIResponse::json($c->saveNotificationRule($this->requestArray('rule')));

                case 'deleteNotificationRule':
                    $this->requireAdmin();
                    WIResponse::json($c->deleteNotificationRule(WIRequest::postInt('rule_id')));

                /*
                |--------------------------------------------------------------------------
                | Shared service bridge wrappers
                |--------------------------------------------------------------------------
                */

                case 'getComplianceFeatureDefinitions':
                    $this->requireAdmin();
                    WIResponse::json($c->getComplianceFeatureDefinitions($this->requestArray('filters')));

                case 'getComplianceGuide':
                case 'getComplianceGuideByKey':
                    $this->requireAdmin();
                    WIResponse::json(
                        $c->getComplianceGuideByKey(
                            WIRequest::postString('guide_key', WIRequest::postString('key', ''))
                        )
                    );

                case 'getComplianceSectionGuide':
                    $this->requireAdmin();
                    WIResponse::json($c->getComplianceSectionGuide(WIRequest::postString('section_code', '')));

                case 'getComplianceActionGuide':
                    $this->requireAdmin();
                    WIResponse::json($c->getComplianceActionGuide(WIRequest::postString('action_code', '')));

                case 'getComplianceKnowledge':
                case 'getComplianceKnowledgeByKey':
                    $this->requireAdmin();
                    WIResponse::json(
                        $c->getComplianceKnowledgeByKey(
                            WIRequest::postString('knowledge_key', WIRequest::postString('key', ''))
                        )
                    );

                case 'searchComplianceKnowledge':
                case 'searchComplianceKnowledgeEntries':
                    $this->requireAdmin();
                    WIResponse::json(
                        $c->searchComplianceKnowledgeEntries(
                            WIRequest::postString('query', ''),
                            $this->requestArray('filters')
                        )
                    );

                case 'getComplianceRequirementKnowledge':
                    $this->requireAdmin();
                    WIResponse::json($c->getComplianceRequirementKnowledge(WIRequest::postString('requirement_code', '')));

                case 'getComplianceDocumentKnowledge':
                    $this->requireAdmin();
                    WIResponse::json($c->getComplianceDocumentKnowledge(WIRequest::postString('document_code', '')));

                /*
                |--------------------------------------------------------------------------
                | Legal register / documents / evidence
                |--------------------------------------------------------------------------
                */

                case 'getComplianceRegistryPageData':
                    $this->requireAdmin();
                    WIResponse::json($c->getComplianceRegistryPageData($this->requestArray('context')));

                case 'getComplianceDraftRequirements':
                    $this->requireAdmin();
                    WIResponse::json($c->getComplianceDraftRequirements($this->requestArray('context')));

                case 'saveComplianceRequirementReview':
                    $this->requireAdmin();
                    WIResponse::json(
                        $c->saveComplianceRequirementReview(
                            $this->requestArray('requirement'),
                            WIRequest::postString('status', 'proposed'),
                            WIRequest::postString('note', '')
                        )
                    );

                case 'getComplianceDocumentsPageData':
                    $this->requireAdmin();
                    WIResponse::json(
                        $c->getComplianceDocumentsPageData(
                            $this->requestArrayList('requirements'),
                            $this->requestArrayList('evidence_pool')
                        )
                    );

                case 'getComplianceRequiredDocuments':
                    $this->requireAdmin();
                    WIResponse::json(
                        $c->getComplianceRequiredDocuments(
                            $this->requestArrayList('requirements')
                        )
                    );

                case 'getComplianceEvidenceGallery':
                    $this->requireAdmin();
                    WIResponse::json(
                        $c->getComplianceEvidenceGallery(
                            $this->requestArrayList('evidence_pool')
                        )
                    );

                /*
                |--------------------------------------------------------------------------
                | Setup / options / settings
                |--------------------------------------------------------------------------
                */

                case 'save_settings':
                    $this->requireAdmin();
                    WIResponse::json($c->saveSettings($_POST));

                case 'save_options':
                    $this->requireAdmin();
                    WIResponse::json($c->saveOptions($_POST));

                case 'setup_workspace':
                    $this->requireAdmin();
                    WIResponse::json($c->getSetupWorkspace($_POST));

                case 'save_setup':
                    $this->requireAdmin();
                    WIResponse::json($c->saveSetup($_POST));
        }

        return false;
    }

    /**
     * Route WIOrg / WIHR admin actions.
     *
     * @param string $action Action name.
     * @param string $method HTTP method.
     *
     * @return bool
     */
    private function routeOrgHr(string $action, string $method): bool
    {
        if (!str_starts_with($action, 'hr_') && !str_starts_with($action, 'org_')) {
            return false;
        }

        $this->requireAdmin();

        $router = $this->orgHrRouter();

        if (!$router->owns($action)) {
            return false;
        }

        WIResponse::json($router->handle($action, $method));

        return true;
    }



    /*
    |--------------------------------------------------------------------------
    | Bug Reporter
    |--------------------------------------------------------------------------
    */

    /**
     * Route bug reporter actions.
     *
     * @param string $action Action name.
     * @param string $method HTTP method.
     *
     * @return bool
     */
    private function routeBugReporter(string $action, string $method): bool
    {
        if ($method !== 'POST') {
            return false;
        }

        $bug = $this->bugReporter();

        switch ($action) {
            case 'getBugReports':
            case 'wi_bug_list_filtered':
                $this->requireAdmin();

                $filters = $this->requestArray('filters');

                foreach (['status', 'severity', 'area_type', 'module_name'] as $field) {
                    $value = WIRequest::postString($field, '');

                    if ($value !== '') {
                        $filters[$field] = $value;
                    }
                }

                foreach (['site_id', 'business_id', 'user_id'] as $field) {
                    $value = WIRequest::postInt($field, 0);

                    if ($value > 0) {
                        $filters[$field] = $value;
                    }
                }

                WIResponse::json($bug->getReports($filters));

            case 'getBugReportThread':
            case 'wi_bug_get':
                $this->requireAdmin();

                WIResponse::json(
                    $bug->getReportThread(WIRequest::postInt('report_id'))
                );

            case 'wi_bug_reply':
                $this->requireAdmin();

                WIResponse::json(
                    $bug->replyToReport(
                        WIRequest::postInt('report_id'),
                        WIRequest::postString('message', ''),
                        (int) WISession::get('user_id', 0)
                    )
                );

            case 'wi_bug_status':
                $this->requireAdmin();

                WIResponse::json(
                    $bug->updateReportStatus(
                        WIRequest::postInt('report_id'),
                        WIRequest::postString('status', ''),
                        (int) WISession::get('user_id', 0)
                    )
                );

            case 'wi_bug_forward':
                $this->requireAdmin();

                WIResponse::json(
                    $bug->forwardReportNow(WIRequest::postInt('report_id'))
                );

            case 'createBugReport':
            case 'create_bug_report':
                $this->requireLoggedIn();

                WIResponse::json($bug->createReport($_POST));
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Builder
    |--------------------------------------------------------------------------
    */

    /**
     * Route shared builder actions.
     *
     * @param string $action Action name.
     * @param string $method HTTP method.
     *
     * @return bool
     */
    private function routeBuilder(string $action, string $method): bool
    {
        if ($method !== 'POST') {
            return false;
        }

        $c = $this->compliance();

        switch ($action) {
            case 'getComplianceChecklistBuilderData':
                $this->requireAdmin();
                WIResponse::json(
                    $c->getComplianceChecklistBuilderData(
                        $this->requestArray('checklist'),
                        $this->requestArrayList('questions'),
                        $this->requestArray('saved_layout')
                    )
                );

            case 'saveComplianceChecklistBuilderLayout':
                $this->requireAdmin();
                WIResponse::json(
                    $c->saveComplianceChecklistBuilderLayout(
                        WIRequest::postInt('checklist_id'),
                        $this->requestArray('layout')
                    )
                );

            case 'saveComplianceChecklistQuestionOrder':
                $this->requireAdmin();
                WIResponse::json(
                    $c->saveComplianceChecklistQuestionOrder(
                        WIRequest::postInt('checklist_id'),
                        $this->requestIntArray('ordered_ids')
                    )
                );

            case 'saveComplianceChecklistQuestionSections':
                $this->requireAdmin();
                WIResponse::json(
                    $c->saveComplianceChecklistQuestionSections(
                        WIRequest::postInt('checklist_id'),
                        $this->requestArray('section_map')
                    )
                );
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Generic Plugin Admin Controllers
    |--------------------------------------------------------------------------
    */

    /**
     * Route plugin admin AJAX actions to their thin plugin controllers.
     *
     * @param string $action Action name.
     * @param string $method HTTP method.
     *
     * @return bool
     */
    private function routePluginAdmin(string $action, string $method): bool
    {
        if (!in_array($method, ['GET', 'POST'], true)) {
            return false;
        }

        require_once __DIR__ . '/WIPluginAdminAjaxRouter.php';

        $router = new WIPluginAdminAjaxRouter(WIdb::getInstance());
        if (!$router->supports($action)) {
            return false;
        }

        $this->requireAdmin();

        $request = $method === 'POST' ? $_POST : $_GET;
        WIResponse::json($router->dispatch($action, $request));

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Plugins
    |--------------------------------------------------------------------------
    */

    /**
     * Route plugin actions.
     *
     * @param string $action Action name.
     * @param string $method HTTP method.
     *
     * @return bool
     */
    private function routePlugin(string $action, string $method): bool
    {
        if ($method !== 'POST') {
            return false;
        }

        $pluginActions = [
            'install_plugin',
            'uninstall_plugin',
            'enable_plugin',
            'disable_plugin',
            'plugin_validate_license',
            'plugin_create_order',
            'plugin_complete_purchase',
        ];

        if (!in_array($action, $pluginActions, true)) {
            return false;
        }

        try {
            switch ($action) {
                case 'install_plugin':
                    $this->requireAdmin();
                    $plugin = new WIPlugin();
                    $ok = $plugin->install(WIRequest::postString('plugin'));

                    if (!$ok) {
                        $message = method_exists($plugin, 'getLastError') && $plugin->getLastError() !== ''
                            ? $plugin->getLastError()
                            : 'Failed to install plugin.';

                        WIResponse::error($message, [], 400);
                    }

                    WIResponse::success('Plugin installed successfully.', method_exists($plugin, 'getLastResult') ? $plugin->getLastResult() : []);

                case 'uninstall_plugin':
                    $this->requireAdmin();
                    $plugin = new WIPlugin();
                    $ok = $plugin->uninstall(WIRequest::postString('plugin'));

                    if (!$ok) {
                        $message = method_exists($plugin, 'getLastError') && $plugin->getLastError() !== ''
                            ? $plugin->getLastError()
                            : 'Failed to uninstall plugin.';

                        WIResponse::error($message, [], 400);
                    }

                    WIResponse::success('Plugin uninstalled successfully.', ['plugin_slug' => WIRequest::postString('plugin')]);

                case 'enable_plugin':
                    $this->requireAdmin();
                    $plugin = new WIPlugin();
                    $ok = $plugin->enable(WIRequest::postString('plugin'));

                    if (!$ok) {
                        $message = method_exists($plugin, 'getLastError') && $plugin->getLastError() !== ''
                            ? $plugin->getLastError()
                            : 'Failed to enable plugin.';

                        WIResponse::error($message, [], 400);
                    }

                    WIResponse::success('Plugin enabled successfully.', method_exists($plugin, 'getLastResult') ? $plugin->getLastResult() : []);

                case 'disable_plugin':
                    $this->requireAdmin();
                    $plugin = new WIPlugin();
                    $ok = $plugin->disable(WIRequest::postString('plugin'));

                    if (!$ok) {
                        $message = method_exists($plugin, 'getLastError') && $plugin->getLastError() !== ''
                            ? $plugin->getLastError()
                            : 'Failed to disable plugin.';

                        WIResponse::error($message, [], 400);
                    }

                    WIResponse::success('Plugin disabled successfully.', ['plugin_slug' => WIRequest::postString('plugin')]);

                case 'plugin_validate_license':
                    $this->requireAdmin();
                    $plugin = new WIPlugin();
                    $valid = $plugin->validateLicense(
                        WIRequest::postString('license_key'),
                        WIRequest::postString('plugin_slug')
                    );

                    WIResponse::json([
                        'status'  => 'success',
                        'message' => 'License validation complete.',
                        'data'    => ['valid' => $valid],
                    ]);

                case 'plugin_create_order':
                    $this->requireAdmin();
                    $plugin = new WIPlugin();
                    $orderId = $plugin->createOrder(
                        WIRequest::postInt('user_id'),
                        WIRequest::postString('plugin_slug'),
                        (float) WIRequest::postString('price', '0'),
                        WIRequest::postString('currency', 'GBP'),
                        WIRequest::postString('gateway', 'manual')
                    );

                    if ($orderId <= 0) {
                        WIResponse::error('Failed to create plugin order.', [], 400);
                    }

                    WIResponse::json([
                        'status' => 'success',
                        'message' => 'Plugin order created.',
                        'data' => ['order_id' => $orderId],
                    ]);

                case 'plugin_complete_purchase':
                    $this->requireAdmin();
                    $plugin = new WIPlugin();
                    $result = $plugin->completePurchase(
                        WIRequest::postInt('user_id'),
                        WIRequest::postString('plugin_slug'),
                        (float) WIRequest::postString('price', '0'),
                        WIRequest::postString('currency', 'GBP'),
                        WIRequest::postString('gateway', 'manual'),
                        WIRequest::postString('license_type', 'lifetime'),
                        (bool) WIRequest::postInt('subscription', 0),
                        WIRequest::postString('subscription_period', 'monthly')
                    );

                    WIResponse::json([
                        'status' => 'success',
                        'message' => 'Plugin purchase completed.',
                        'data' => $result,
                    ]);
            }
        } catch (Throwable $e) {
            @file_put_contents(
                __DIR__ . '/WIPlugin_ajax_error.log',
                '[' . date('Y-m-d H:i:s') . '] action=' . $action
                    . ' message=' . $e->getMessage()
                    . ' file=' . $e->getFile()
                    . ' line=' . $e->getLine()
                    . PHP_EOL,
                FILE_APPEND
            );

            WIResponse::error('Plugin action failed: ' . $e->getMessage(), [], 500);
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Marketplace
    |--------------------------------------------------------------------------
    */

    /**
     * Route marketplace actions.
     *
     * @param string $action Action name.
     * @param string $method HTTP method.
     *
     * @return bool
     */
    private function routeMarketplace(string $action, string $method): bool
    {
        if ($method !== 'POST') {
            return false;
        }

        switch ($action) {
            case 'plugin_create_order':
                $this->requireAdmin();
                $plugin = new WIPlugin();
                $orderId = $plugin->createOrder(
                    WIRequest::postInt('user_id'),
                    WIRequest::postString('plugin_slug'),
                    WIRequest::float('price', 0.0, 'post'),
                    WIRequest::postString('currency', 'GBP'),
                    WIRequest::postString('gateway', 'manual')
                );

                if ($orderId <= 0) {
                    WIResponse::error('Failed to create plugin order.');
                }

                WIResponse::json([
                    'status'  => 'success',
                    'message' => 'Plugin order created.',
                    'data'    => ['order_id' => $orderId],
                ]);

            case 'plugin_complete_purchase':
                $this->requireAdmin();
                $plugin = new WIPlugin();
                $result = $plugin->completePurchase(
                    WIRequest::postInt('user_id'),
                    WIRequest::postString('plugin_slug'),
                    WIRequest::float('price', 0.0, 'post'),
                    WIRequest::postString('currency', 'GBP'),
                    WIRequest::postString('gateway', 'manual'),
                    WIRequest::postString('license_type', 'lifetime'),
                    WIRequest::postBool('subscription', false),
                    WIRequest::postString('subscription_period', 'monthly')
                );

                if (!is_array($result) || ($result['status'] ?? '') !== 'success') {
                    WIResponse::json([
                        'status'  => 'error',
                        'message' => 'Plugin purchase failed.',
                        'errors'  => ['result' => $result],
                    ], 422);
                }

                WIResponse::json([
                    'status'  => 'success',
                    'message' => 'Plugin purchase completed successfully.',
                    'data'    => $result,
                ]);
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | WICMS / Core Admin / Shared Platform
    |--------------------------------------------------------------------------
    */


    /**
     * Fresh-install-safe admin login.
     *
     * The normal WILogin path is still fine for an established system, but a
     * brand-new WICMS install can be missing one baseline row/table and throw a
     * generic "System error" before the first admin gets in. This route keeps
     * CSRF enabled, verifies the password using the canonical register service,
     * repairs the small admin/auth baseline, then creates the admin session.
     *
     * @return never
     */
    private function handleAdminLoginCompatibility(): never
    {
        $username = WIRequest::postString('username');
        $password = WIRequest::postString('password');

        if ($username === '' || $password === '') {
            WIResponse::json([
                'status'  => 'error',
                'message' => 'Username and password are required.',
                'errors'  => [],
            ], 422);
        }

        try {
            $db = WIdb::getInstance();
            $this->ensureAdminAuthBaseline($db);

            $rows = $db->select(
                'SELECT * FROM `wi_members`
                  WHERE `username` = :username OR `email` = :email
                  LIMIT 1',
                [
                    'username' => $username,
                    'email'    => $username,
                ]
            );

            if (count($rows) !== 1) {
                WIResponse::json([
                    'status'  => 'error',
                    'message' => 'Wrong username or password.',
                    'errors'  => [],
                ], 422);
            }

            $user = $rows[0];
            $userId = (int) ($user['user_id'] ?? 0);
            $storedHash = (string) ($user['password'] ?? '');

            if ($userId <= 0 || !$this->verifyAdminLoginPassword($password, $storedHash)) {
                WIResponse::json([
                    'status'  => 'error',
                    'message' => 'Wrong username or password.',
                    'errors'  => [],
                ], 422);
            }

            if ((string) ($user['banned'] ?? 'N') === 'Y') {
                WIResponse::json([
                    'status'  => 'error',
                    'message' => 'This admin account is banned.',
                    'errors'  => [],
                ], 403);
            }

            if ((string) ($user['confirmed'] ?? 'Y') === 'N') {
                WIResponse::json([
                    'status'  => 'error',
                    'message' => 'This admin account is not confirmed.',
                    'errors'  => [],
                ], 403);
            }

            $roleId = (int) ($user['user_role'] ?? 0);

            // On a fresh install, user_id 1 is the first installer admin.
            // Make sure it has a real admin role before checking admin access.
            if ($userId === 1 && $roleId <= 70) {
                $db->update(
                    'wi_members',
                    ['user_role' => 91, 'confirmed' => 'Y', 'banned' => 'N'],
                    '`user_id` = :user_id',
                    ['user_id' => $userId]
                );
                $roleId = 91;
            }

            if ($roleId <= 70) {
                WIResponse::json([
                    'status'  => 'error',
                    'message' => 'Admin access required for this login page.',
                    'errors'  => [],
                ], 403);
            }

            $db->update(
                'wi_members',
                ['last_login' => date('Y-m-d H:i:s')],
                '`user_id` = :user_id',
                ['user_id' => $userId]
            );

            WISession::set('user_id', $userId);
            WISession::set('login_time', time());

            // Regenerate first, then store the fingerprint using the new
            // session id. The older WILogin service did this in the reverse
            // order, which could immediately invalidate the login when login
            // fingerprinting was enabled.
            if (class_exists('WISession') && method_exists('WISession', 'regenerate')) {
                WISession::regenerate(true);
            }

            WISession::set('user_id', $userId);
            WISession::set('login_time', time());

            if ($this->adminLoginFingerprintEnabled()) {
                WISession::set('login_fingerprint', $this->currentAdminLoginFingerprint());
            } else {
                WISession::destroy('login_fingerprint');
            }

            try {
                $db->insert('wi_logs', [
                    'date' => date('Y-m-d H:i:s'),
                    'user' => (string) ($user['username'] ?? $username),
                    'opperation' => 'Successfully logged in admin',
                ]);
            } catch (Throwable $ignored) {
            }

            WIResponse::json([
                'status'   => 'success',
                'message'  => 'Admin login successful.',
                'page'     => 'WIAdmin/dashboard.php',
                'redirect' => 'WIAdmin/dashboard.php',
                'data'     => [
                    'page'     => 'WIAdmin/dashboard.php',
                    'redirect' => 'WIAdmin/dashboard.php',
                ],
            ]);
        } catch (Throwable $e) {
            @file_put_contents(
                __DIR__ . '/admin_login_runtime_error.log',
                '[' . date('Y-m-d H:i:s') . '] ' . $e->getMessage()
                    . ' file=' . $e->getFile()
                    . ' line=' . $e->getLine()
                    . PHP_EOL,
                FILE_APPEND
            );

            $isLocal = in_array((string) ($_SERVER['REMOTE_ADDR'] ?? ''), ['127.0.0.1', '::1', ''], true)
                || str_contains(strtolower((string) ($_SERVER['HTTP_HOST'] ?? '')), 'localhost');

            WIResponse::json([
                'status'  => 'error',
                'message' => $isLocal
                    ? 'Admin login system error: ' . $e->getMessage()
                    : 'System error.',
                'errors'  => [],
            ], 500);
        }
    }

    /**
     * Verify password using the canonical service, with safe fallbacks.
     */
    private function verifyAdminLoginPassword(string $password, string $storedHash): bool
    {
        if ($storedHash === '') {
            return false;
        }

        if (class_exists('WIRegister')) {
            try {
                $register = new WIRegister();
                if (method_exists($register, 'verifyPassword')) {
                    return (bool) $register->verifyPassword($password, $storedHash);
                }
            } catch (Throwable $ignored) {
            }
        }

        if (password_verify($password, $storedHash)) {
            return true;
        }

        /*
        |------------------------------------------------------------------
        | Installer v2.x/v3.0 compatibility
        |------------------------------------------------------------------
        |
        | Earlier installer JavaScript SHA512-hashed the admin password before
        | posting it to PHP. PHP then bcrypt-hashed that SHA512 value. A normal
        | login submits the real password, so password_verify() above fails.
        | Keep this fallback so existing test installs created by those installer
        | builds can still log in, while the v3.5 installer stops creating this
        | mismatch for new installs.
        |
        */
        $clientHashedPassword = hash('sha512', $password);
        if (password_verify($clientHashedPassword, $storedHash)) {
            return true;
        }

        if (defined('PASSWORD_SALT')) {
            $legacy = (string) PASSWORD_SALT . $password . (string) PASSWORD_SALT;
            for ($i = 0; $i < 35000; $i++) {
                $legacy = hash('sha512', $legacy);
            }
            if (hash_equals($legacy, $storedHash)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Create/repair the small auth/admin baseline needed for first login.
     */
    private function ensureAdminAuthBaseline(WIdb $db): void
    {
        $db->exec("CREATE TABLE IF NOT EXISTS `wi_login_attempts` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `ip_addr` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
            `date` date NOT NULL,
            `attempt_number` int(11) NOT NULL DEFAULT 1,
            PRIMARY KEY (`id`),
            KEY `idx_wi_login_attempts_ip_date` (`ip_addr`, `date`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        $db->exec("CREATE TABLE IF NOT EXISTS `wi_logs` (
            `ID` int(11) NOT NULL AUTO_INCREMENT,
            `date` datetime NOT NULL,
            `user` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
            `opperation` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        $db->exec("CREATE TABLE IF NOT EXISTS `wi_user_roles` (
            `role_id` int(11) NOT NULL AUTO_INCREMENT,
            `role` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
            PRIMARY KEY (`role_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci");

        $db->exec("INSERT INTO `wi_user_roles` (`role_id`, `role`)
            SELECT 90, 'Super Admin'
            WHERE NOT EXISTS (SELECT 1 FROM `wi_user_roles` WHERE `role_id` = 90)");

        $db->exec("INSERT INTO `wi_user_roles` (`role_id`, `role`)
            SELECT 91, 'Platform Owner'
            WHERE NOT EXISTS (SELECT 1 FROM `wi_user_roles` WHERE `role_id` = 91)");

        $db->exec("CREATE TABLE IF NOT EXISTS `wi_permissions` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `code` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
            `group_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'General',
            `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
            `is_active` tinyint(1) NOT NULL DEFAULT 1,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_wi_permissions_code` (`code`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->exec("CREATE TABLE IF NOT EXISTS `wi_role_permissions` (
            `id` int unsigned NOT NULL AUTO_INCREMENT,
            `role_id` int unsigned NOT NULL,
            `permission_id` int unsigned NOT NULL,
            `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uk_wi_role_permissions` (`role_id`, `permission_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $db->exec("INSERT INTO `wi_permissions` (`name`, `code`, `group_name`, `description`, `is_active`)
            VALUES ('Admin Access', 'admin.access', 'Admin', 'Can enter the WICMS admin area', 1)
            ON DUPLICATE KEY UPDATE `name` = VALUES(`name`), `is_active` = VALUES(`is_active`)");

        $db->exec("INSERT INTO `wi_role_permissions` (`role_id`, `permission_id`)
            SELECT 90, p.`id`
              FROM `wi_permissions` p
             WHERE p.`code` = 'admin.access'
               AND NOT EXISTS (
                    SELECT 1 FROM `wi_role_permissions` rp
                     WHERE rp.`role_id` = 90
                       AND rp.`permission_id` = p.`id`
               )");

        $db->exec("INSERT INTO `wi_role_permissions` (`role_id`, `permission_id`)
            SELECT 91, p.`id`
              FROM `wi_permissions` p
             WHERE p.`code` = 'admin.access'
               AND NOT EXISTS (
                    SELECT 1 FROM `wi_role_permissions` rp
                     WHERE rp.`role_id` = 91
                       AND rp.`permission_id` = p.`id`
               )");

        $db->exec("UPDATE `wi_members`
             SET `user_role` = CASE WHEN `user_id` = 1 THEN 91 ELSE `user_role` END,
                 `confirmed` = 'Y',
                 `banned` = 'N'
           WHERE `user_id` = 1");
    }

    /**
     * Mirrors WILogin fingerprint setting without using its private methods.
     */
    private function adminLoginFingerprintEnabled(): bool
    {
        if (defined('LOGIN_FINGERPRINT')) {
            return (bool) LOGIN_FINGERPRINT;
        }

        try {
            if (class_exists('WISettings')) {
                $settings = new WISettings();
                return filter_var((string) $settings->website('login_fingerprint'), FILTER_VALIDATE_BOOLEAN);
            }
        } catch (Throwable $ignored) {
        }

        return false;
    }

    /**
     * Current login fingerprint for the active regenerated session.
     */
    private function currentAdminLoginFingerprint(): string
    {
        return hash(
            'sha256',
            (string) ($_SERVER['HTTP_USER_AGENT'] ?? '') . '|' . session_id()
        );
    }

    /**
     * Route core WICMS/admin/shared platform actions.
     *
     * Anything removed from the generic route area because it belongs to
     * compliance, builder, plugin, or marketplace should be removed from
     * here only.
     *
     * @param string $action Action name.
     * @param string $method HTTP method.
     *
     * @return bool
     */
    private function routeWICMS(string $action, string $method): bool
    {
        if ($method === 'GET') {
            return false;
        }

        switch ($action) {
            /*
            |--------------------------------------------------------------------------
            | Auth / account
            |--------------------------------------------------------------------------
            */

            case 'checkLogin':
                $ok = $this->login->userLogin(
                    WIRequest::postString('username'),
                    WIRequest::postString('password')
                );

                if ($ok === true) {
                    $redirectPage = function_exists('get_redirect_page')
                        ? (string) get_redirect_page()
                        : 'dashboard.php';

                    WIResponse::json([
                        'status'   => 'success',
                        'message'  => 'Login successful',
                        'page'     => $redirectPage,
                        'redirect' => $redirectPage,
                    ]);
                }

                WIResponse::json($this->login->getLastResult(), 422);


            case 'checkAdminLogin':
                $this->handleAdminLoginCompatibility();

            case 'registerUser':
                $register = new WIRegister();
                $result = $register->register((array) WIRequest::post('User', []));

                if (($result['status'] ?? 'error') !== 'success') {
                    WIResponse::json($result, 422);
                }

                WIResponse::json([
                    'status'  => 'success',
                    'message' => (string) ($result['message'] ?? $result['msg'] ?? 'Registration successful'),
                    'data'    => [
                        'user_id' => (int) ($result['user_id'] ?? 0),
                    ],
                ]);

            case 'forgotPassword':
                $register = new WIRegister();
                $result = $register->forgotPassword(WIRequest::postString('email'));

                if ($result !== true) {
                    WIResponse::error((string) $result, [], 422);
                }

                WIResponse::success('Password reset email sent.');

            case 'resetPassword':
                $register = new WIRegister();
                $register->resetPassword(
                    WIRequest::postString('newPass'),
                    WIRequest::postString('key')
                );

                WIResponse::success('Password reset complete.');

            case 'updatePassword':
                $this->requireLoggedIn();
                $user = new WIAdmin((int) WISession::get('user_id', 0));
                $user->updatePassword(
                    WIRequest::postString('oldpass'),
                    WIRequest::postString('newpass')
                );

                WIResponse::success('Password updated.');

            case 'updateDetails':
                $this->requireLoggedIn();
                $user = new WIAdmin((int) WISession::get('user_id', 0));
                $user->updateDetails((array) WIRequest::post('details', []));

                WIResponse::success('Details updated.');

            /*
            |--------------------------------------------------------------------------
            | Comments
            |--------------------------------------------------------------------------
            */

            case 'postComment':
                $this->requireLoggedIn();
                $comment = new WIComment();

                echo $comment->insertComment(
                    WISession::get('user_id'),
                    WIRequest::postString('comment')
                );
                exit;

            /*
            |--------------------------------------------------------------------------
            | Roles / permissions
            |--------------------------------------------------------------------------
            */

            case 'getRole':
                $this->requireAdmin();
                $role = new WIRole();
                WIResponse::json($role->getRoleById(WIRequest::postInt('role_id')) ?? []);

            case 'saveRole':
                $this->requireAdmin();
                $role = new WIRole();
                WIResponse::json($role->saveRole($_POST));

            case 'deleteRole':
                $this->requireAdmin();
                $role = new WIRole();
                WIResponse::json($role->deleteRole(WIRequest::postInt('roleId')));

            case 'getRolePermissions':
                $this->requireAdmin();
                $role = new WIRole();
                WIResponse::json([
                    'status'  => 'success',
                    'message' => 'Role permissions loaded.',
                    'data'    => $role->getRolePermissionIds(WIRequest::postInt('role_id')),
                ]);

            case 'updateRolePermissions':
                $this->requireAdmin();
                $role = new WIRole();
                WIResponse::json(
                    $role->updateRolePermissions(
                        WIRequest::postInt('role_id'),
                        $_POST['permission_ids'] ?? []
                    )
                );

            case 'getPermission':
                $this->requireAdmin();
                $permissions = new WIPermissions();
                WIResponse::json($permissions->getPermissionById(WIRequest::postInt('id')) ?? []);

            case 'savePermission':
                $this->requireAdmin();
                $permissions = new WIPermissions();
                WIResponse::json($permissions->savePermission($_POST));

            case 'deletePermission':
                $this->requireAdmin();
                $permissions = new WIPermissions();
                WIResponse::json($permissions->deletePermission(WIRequest::postInt('id')));

            case 'toggleRolePermission':
                $this->requireAdmin();
                $permissions = new WIPermissions();
                WIResponse::json(
                    $permissions->toggleRolePermission(
                        WIRequest::postInt('role_id'),
                        WIRequest::postInt('permission_id'),
                        WIRequest::postInt('enabled')
                    )
                );

            /*
            |--------------------------------------------------------------------------
            | Modules / elements
            |--------------------------------------------------------------------------
            */

            case 'install_module':
            case 'mod_install':
                $this->requireAdmin();
                $mod = new WIModules();
                $ok = $mod->install_mod(WIRequest::postString('mod_name'));

                if (!$ok) {
                    WIResponse::error('Failed to install module.');
                }

                WIResponse::success('Module installed successfully.');

            case 'uninstall_module':
            case 'mod_uninstall':
                $this->requireAdmin();
                $mod = new WIModules();
                $ok = $mod->uninstall_mod(WIRequest::postString('mod_name'));

                if (!$ok) {
                    WIResponse::error('Failed to uninstall module.');
                }

                WIResponse::success('Module uninstalled successfully.');

            case 'enable_module':
            case 'mod_enable':
                $this->requireAdmin();
                $mod = new WIModules();
                $ok = $mod->active_available_mod(WIRequest::postString('mod_name'));

                if (!$ok) {
                    WIResponse::error('Failed to enable module.');
                }

                WIResponse::success('Module enabled successfully.');

            case 'disable_module':
            case 'mod_disable':
                $this->requireAdmin();
                $mod = new WIModules();
                $ok = $mod->deactive_available_mod(WIRequest::postString('mod_name'));

                if (!$ok) {
                    WIResponse::error('Failed to disable module.');
                }

                WIResponse::success('Module disabled successfully.');

            case 'install_element':
            case 'element_install':
                $this->requireAdmin();
                $mod = new WIModules();
                $ok = $mod->installElement(WIRequest::postString('element_name'));

                if (!$ok) {
                    WIResponse::error('Failed to install element.');
                }

                WIResponse::success('Element installed successfully.');

            case 'uninstall_element':
            case 'ele_uninstall':
                $this->requireAdmin();
                $mod = new WIModules();
                $elementName = WIRequest::postString('element_name');

                if ($elementName === '') {
                    $elementName = WIRequest::postString('mod_name');
                }

                $ok = $mod->unistall_Element($elementName);

                if (!$ok) {
                    WIResponse::error('Failed to uninstall element.');
                }

                WIResponse::success('Element uninstalled successfully.');

            case 'enable_element':
            case 'Element_enable':
                $this->requireAdmin();
                $mod = new WIModules();
                $ok = $mod->activateAvailableElements(WIRequest::postString('element_name'));

                if (!$ok) {
                    WIResponse::error('Failed to enable element.');
                }

                WIResponse::success('Element enabled successfully.');

            case 'disable_element':
            case 'Element_disable':
                $this->requireAdmin();
                $mod = new WIModules();
                $ok = $mod->deactivateAvailableElements(WIRequest::postString('element_name'));

                if (!$ok) {
                    WIResponse::error('Failed to disable element.');
                }

                WIResponse::success('Element disabled successfully.');

            /*
            |--------------------------------------------------------------------------
            | Theme / page / menu / settings
            |--------------------------------------------------------------------------
            */

            case 'site_settings':
                $this->requireAdmin();
                $site = new WISite();
                WIResponse::json($site->Site_Settings((array) WIRequest::post('settings', [])));

            case 'database_settings':
                $this->requireAdmin();
                $site = new WISite();
                WIResponse::json($site->DataBase_settings((array) WIRequest::post('settings', [])));

            case 'email_settings':
                $this->requireAdmin();
                $site = new WISite();
                WIResponse::json($site->Email_settings((array) WIRequest::post('settings', [])));

            case 'mailer_settings':
                $this->requireAdmin();
                $site = new WISite();
                WIResponse::json($site->Email_Method((array) WIRequest::post('settings', [])));

            case 'session_settings':
                $this->requireAdmin();
                $site = new WISite();
                WIResponse::json($site->Session_Settings((array) WIRequest::post('settings', [])));

            case 'bug_reporter_settings':
                $this->requireAdmin();

                if (!class_exists('WIBugReporterSettings')) {
                    $settingsClass = __DIR__ . '/WIBugReporterSettings.php';

                    if (is_file($settingsClass)) {
                        require_once $settingsClass;
                    }
                }

                if (!class_exists('WIBugReporterSettings')) {
                    WIResponse::json([
                        'success' => false,
                        'status'  => 'error',
                        'message' => 'WIBugReporter settings service could not be loaded.',
                        'errors'  => [],
                    ], 500);
                }

                $settings = new WIBugReporterSettings();
                WIResponse::json($settings->save((array) WIRequest::post('settings', [])));

            case 'verification_settings':
                $this->requireAdmin();
                $site = new WISite();
                WIResponse::json($site->verification_Settings((array) WIRequest::post('settings', [])));

            case 'encryption':
                $this->requireAdmin();
                $site = new WISite();
                WIResponse::json(
                    $site->Security_Settings(
                        WIRequest::postString('encryption'),
                        WIRequest::post('cost', null)
                    )
                );

            case 'login_settings':
                $this->requireAdmin();
                $site = new WISite();
                WIResponse::json($site->Login_Settings((array) WIRequest::post('settings', [])));

            case 'social_settings':
                $this->requireAdmin();
                $site = new WISite();
                WIResponse::json($site->social_settings((array) WIRequest::post('settings', [])));

            case 'twitter':
                $this->requireAdmin();
                $site = new WISite();
                WIResponse::json($site->twitter((array) WIRequest::post('tw', [])));


            case 'header_settings':
                $this->requireAdmin();
                require_once __DIR__ . '/WIHeaderFooterSettingsService.php';
                $headerFooterService = new WIHeaderFooterSettingsService(WIdb::getInstance());
                WIResponse::json($headerFooterService->saveLegacyHeader((array) WIRequest::post('settings', [])));

            case 'footer_settings':
                $this->requireAdmin();
                require_once __DIR__ . '/WIHeaderFooterSettingsService.php';
                $headerFooterService = new WIHeaderFooterSettingsService(WIdb::getInstance());
                WIResponse::json($headerFooterService->saveLegacyFooter((array) WIRequest::post('settings', [])));

            case 'wicms_header_footer_save':
                $this->requireAdmin();
                require_once __DIR__ . '/WIHeaderFooterSettingsService.php';
                $headerFooterService = new WIHeaderFooterSettingsService(WIdb::getInstance());
                WIResponse::json($headerFooterService->save($_POST));

            case 'wicms_header_footer_upload':
                $this->requireAdmin();
                require_once __DIR__ . '/WIHeaderFooterSettingsService.php';
                $headerFooterService = new WIHeaderFooterSettingsService(WIdb::getInstance());
                WIResponse::json($headerFooterService->uploadAsset($_POST, $_FILES));

            case 'lang_settings':
                $this->requireAdmin();
                $site = new WISite();
                WIResponse::json($site->lang_Settings((array) WIRequest::post('settings', [])));

            case 'wicms_language_settings_save':
                $this->requireAdmin();
                require_once __DIR__ . '/WICMSLanguageSettingsService.php';
                $languageService = new WICMSLanguageSettingsService(WIdb::getInstance());
                WIResponse::json($languageService->saveSettings((array) WIRequest::post('language', [])));

            case 'wicms_language_save':
                $this->requireAdmin();
                require_once __DIR__ . '/WICMSLanguageSettingsService.php';
                $languageService = new WICMSLanguageSettingsService(WIdb::getInstance());
                WIResponse::json($languageService->saveLanguage((array) WIRequest::post('language_record', [])));

            case 'wicms_language_delete':
                $this->requireAdmin();
                require_once __DIR__ . '/WICMSLanguageSettingsService.php';
                $languageService = new WICMSLanguageSettingsService(WIdb::getInstance());
                WIResponse::json($languageService->deleteLanguage(WIRequest::postInt('id')));

            case 'wicms_translation_save':
                $this->requireAdmin();
                require_once __DIR__ . '/WICMSLanguageSettingsService.php';
                $languageService = new WICMSLanguageSettingsService(WIdb::getInstance());
                WIResponse::json($languageService->saveTranslation((array) WIRequest::post('translation', [])));

            case 'wicms_translation_delete':
                $this->requireAdmin();
                require_once __DIR__ . '/WICMSLanguageSettingsService.php';
                $languageService = new WICMSLanguageSettingsService(WIdb::getInstance());
                WIResponse::json($languageService->deleteTranslation(WIRequest::postInt('id')));

            case 'version_control':
                $this->requireAdmin();
                $site = new WISite();
                WIResponse::json($site->VersionControl(WIRequest::postString('version')));

            case 'savePage':
                $this->requireAdmin();
                $page = new WIPage();
                WIResponse::json($page->savePage($_POST));

            case 'deletePage':
                $this->requireAdmin();
                $page = new WIPage();
                WIResponse::json($page->deletePage(WIRequest::postInt('id')));
        }

        return false;
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Get the shared compliance bridge.
     *
     * @return WICompliance
     */
    /**
     * Returns the HR/Org router.
     *
     * @return WIAjaxOrgHr
     */
    private function orgHrRouter(): WIAjaxOrgHr
    {
        if ($this->orgHrRouter === null) {
            $this->orgHrRouter = new WIAjaxOrgHr();
        }

        return $this->orgHrRouter;
    }

    
    private function compliance(): WICompliance
    {
        if (!$this->compliance instanceof WICompliance) {
            $this->compliance = new WICompliance();
        }

        return $this->compliance;
    }

    /**
     * Get the bug reporter bridge.
     *
     * @return WIBugReporter
     */
    private function bugReporter(): WIBugReporter
    {
        if (!$this->bugReporter instanceof WIBugReporter) {
            $this->bugReporter = new WIBugReporter();
        }

        return $this->bugReporter;
    }

    /**
     * Return a request array payload.
     *
     * @param string $key Payload key.
     *
     * @return array<string, mixed>
     */
    private function requestArray(string $key): array
    {
        $value = $_POST[$key] ?? $_REQUEST[$key] ?? [];

        return is_array($value) ? $value : [];
    }

    /**
     * Return a request list payload.
     *
     * @param string $key Payload key.
     *
     * @return array<int, array<string, mixed>>
     */
    private function requestArrayList(string $key): array
    {
        $value = $_POST[$key] ?? $_REQUEST[$key] ?? [];

        return is_array($value) ? array_values($value) : [];
    }

    /**
     * Return a request int array payload.
     *
     * @param string $key Payload key.
     *
     * @return array<int, int>
     */
    private function requestIntArray(string $key): array
    {
        $value = $_POST[$key] ?? $_REQUEST[$key] ?? [];

        if (!is_array($value)) {
            return [];
        }

        return array_values(array_map('intval', $value));
    }


}

(new WIAdminAjax())->handle();