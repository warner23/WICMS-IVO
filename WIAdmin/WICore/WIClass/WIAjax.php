<?php
declare(strict_types=1);

require_once 'WI.php';
require_once 'WIA.php';

/*
|--------------------------------------------------------------------------
| Security checks
|--------------------------------------------------------------------------
*/

if (!WIRequest::isAjax()) {
    WILogger::security('Non-AJAX request blocked', [
        'ip' => WIRequest::ip(),
        'user_agent' => WIRequest::userAgent(),
    ]);

    WIResponse::forbidden('Invalid request');
}

$referrer = parse_url((string) WIRequest::server('HTTP_REFERER', ''));

if (!isset($referrer['host']) || $referrer['host'] !== WIRequest::server('SERVER_NAME')) {
    WILogger::security('Invalid AJAX referer blocked', [
        'referer' => WIRequest::server('HTTP_REFERER', ''),
        'ip' => WIRequest::ip(),
    ]);

    WIResponse::forbidden('Invalid origin');
}

if (WIRequest::isPost()) {
    if (!WIToken::validatePostToken('wi_ajax', 'csrf_token', false)) {
        WILogger::security('Invalid CSRF token on AJAX request', [
            'action' => WIRequest::string('action', '', 'post'),
            'ip' => WIRequest::ip(),
        ]);

        WIResponse::error('Invalid security token.', [], 403);
    }
}

/*
|--------------------------------------------------------------------------
| Helpers
|--------------------------------------------------------------------------
*/

function onlyAdmin(): void
{
    $login = new WILogin();

    if (!$login->isLoggedIn()) {
        WILogger::security('Unauthorized admin access attempt', [
            'ip' => WIRequest::ip(),
        ]);

        WIResponse::unauthorized('You must be logged in.');
    }

    $admin = new WIAdmin((int) WISession::get('user_id', 0));

    if (!$admin->isAdmin()) {
        WILogger::security('Non-admin attempted admin action', [
            'user_id' => WISession::get('user_id', null),
            'ip' => WIRequest::ip(),
        ]);

        WIResponse::forbidden('Admin access required.');
    }
}

function postValue(string $key, mixed $default = null): mixed
{
    return WIRequest::post($key, $default);
}

function getValue(string $key, mixed $default = null): mixed
{
    return WIRequest::get($key, $default);
}

function safeMethodCall(object $object, string $method, array $args = []): mixed
{
    if (!method_exists($object, $method)) {
        WIResponse::error("Unsupported legacy action: missing method {$method}.", [], 400);
    }

    return $object->{$method}(...$args);
}

/*
|--------------------------------------------------------------------------
| POST actions
|--------------------------------------------------------------------------
*/

$postAction = WIRequest::string('action', '', 'post');

if ($postAction !== '') {
    switch ($postAction) {
        /*
        |--------------------------------------------------------------------------
        | Auth / account
        |--------------------------------------------------------------------------
        */

        case 'checkLogin':
            $login = new WILogin();

            $username = WIRequest::string('username', '', 'post');
            $password = WIRequest::string('password', '', 'post');

            $logged = $login->userLogin($username, $password);

            if ($logged === true) {
                WIResponse::success('Login successful', [
                    'page' => get_redirect_page()
                ]);
            }

            WIResponse::error('Login failed.');
            break;

        case 'registerUser':
            $register = new WIRegister();
            $register->register((array) postValue('User', []));
            break;

        case 'resetPassword':
            $register = new WIRegister();
            $register->resetPassword(
                WIRequest::string('newPass', '', 'post'),
                WIRequest::string('key', '', 'post')
            );
            break;

        case 'forgotPassword':
            $register = new WIRegister();
            $result = $register->forgotPassword(WIRequest::string('email', '', 'post'));

            if ($result !== true) {
                WIResponse::error((string) $result);
            }

            WIResponse::success('Password reset email sent.');
            break;

        case 'updatePassword':
            $user = new WIAdmin((int) WISession::get('user_id', 0));
            $user->updatePassword(
                WIRequest::string('oldpass', '', 'post'),
                WIRequest::string('newpass', '', 'post')
            );
            break;

        case 'updateDetails':
            $user = new WIAdmin((int) WISession::get('user_id', 0));
            $user->updateDetails(postValue('details', []));
            break;

        /*
        |--------------------------------------------------------------------------
        | Comments
        |--------------------------------------------------------------------------
        */

        case 'postComment':
            $comment = new WIComment();
            echo $comment->insertComment(
                WISession::get('user_id'),
                WIRequest::string('comment', '', 'post')
            );
            exit;

        /*
        |--------------------------------------------------------------------------
        | User admin
        |--------------------------------------------------------------------------
        */

        case 'getRole':
            onlyAdmin();
            $role = new WIRole();
            WIResponse::json(
                $role->getRoleById(WIRequest::int('role_id', 0, 'post'))
            );
            break;

        case 'saveRole':
            onlyAdmin();
            $role = new WIRole();
            WIResponse::json($role->saveRole(WIRequest::allPost()));
            break;

        case 'deleteRole':
            onlyAdmin();
            $role = new WIRole();
            WIResponse::json(
                $role->deleteRole(WIRequest::int('roleId', 0, 'post'))
            );
            break;

        case 'getRolePermissions':
            onlyAdmin();
            $role = new WIRole();
            WIResponse::json([
                'status' => 'success',
                'data'   => $role->getRolePermissionIds(WIRequest::int('role_id', 0, 'post'))
            ]);
            break;

        case 'updateRolePermissions':
            onlyAdmin();
            $role = new WIRole();
            WIResponse::json(
                $role->updateRolePermissions(
                    WIRequest::int('role_id', 0, 'post'),
                    $_POST['permission_ids'] ?? []
                )
            );
            break;

        case 'getPermission':
            onlyAdmin();
            $permissions = new WIPermissions();
            WIResponse::json(
                $permissions->getPermissionById(WIRequest::int('id', 0, 'post'))
            );
            break;

        case 'savePermission':
            onlyAdmin();
            $permissions = new WIPermissions();
            WIResponse::json($permissions->savePermission(WIRequest::allPost()));
            break;

        case 'deletePermission':
            onlyAdmin();
            $permissions = new WIPermissions();
            WIResponse::json(
                $permissions->deletePermission(WIRequest::int('id', 0, 'post'))
            );
            break;

        case 'toggleRolePermission':
            onlyAdmin();
            $permissions = new WIPermissions();
            WIResponse::json(
                $permissions->toggleRolePermission(
                    WIRequest::int('role_id', 0, 'post'),
                    WIRequest::int('permission_id', 0, 'post'),
                    WIRequest::int('enabled', 0, 'post')
                )
            );
            break;

        /*
        |--------------------------------------------------------------------------
        | Site settings
        |--------------------------------------------------------------------------
        */

        case 'site_settings':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json($site->Site_Settings((array) WIRequest::post('settings', [])));
            break;

        case 'database_settings':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json($site->DataBase_settings((array) WIRequest::post('settings', [])));
            break;

        case 'email_settings':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json($site->Email_settings((array) WIRequest::post('settings', [])));
            break;

        case 'mailer_settings':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json($site->Email_Method((array) WIRequest::post('settings', [])));
            break;

        case 'session_settings':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json($site->Session_Settings((array) WIRequest::post('settings', [])));
            break;

        case 'verification_settings':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json($site->verification_Settings((array) WIRequest::post('settings', [])));
            break;

        case 'encryption':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json(
                $site->Security_Settings(
                    WIRequest::string('encryption', '', 'post'),
                    WIRequest::post('cost', null)
                )
            );
            break;

        case 'login_settings':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json($site->Login_Settings((array) WIRequest::post('settings', [])));
            break;

        case 'social_settings':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json($site->social_settings((array) WIRequest::post('settings', [])));
            break;

        case 'twitter':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json($site->twitter((array) WIRequest::post('tw', [])));
            break;

        case 'header_settings':
            onlyAdmin();
            $web = new WIWebsite();
            $web->headerSettings(postValue('settings', []));
            exit;

        case 'lang_settings':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json($site->lang_Settings((array) WIRequest::post('settings', [])));
            break;

        case 'multilanguage':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json(
                $site->AddMultiLang(
                    WIRequest::string('lang', '', 'post'),
                    WIRequest::string('keyword', '', 'post'),
                    WIRequest::string('trans', '', 'post')
                )
            );
            break;

        case 'multiLang':
            onlyAdmin();
            $web = new WIWebsite();
            $web->CheckMultiLang();
            exit;

        case 'version_control':
            onlyAdmin();
            $site = new WISite();
            WIResponse::json($site->VersionControl(WIRequest::string('version', '', 'post')));
            break;

         /*
        |--------------------------------------------------------------------------
        | Menu settings
        |--------------------------------------------------------------------------
        */

        case 'editMenu':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json(
                $web->editMenu(WIRequest::int('id', 0, 'post'))
            );
            break;

        case 'menuEdit':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json(
                $web->menuEdit((array) WIRequest::post('menu', []))
            );
            break;

        case 'newmenuitem':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json(
                $web->newmenuitem((array) WIRequest::post('menu', []))
            );
            break;

        case 'DeleteMenu':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json(
                $web->DeleteMenu(WIRequest::int('id', 0, 'post'))
            );
            break;

        case 'menuLink':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json(
                $web->CreateSidebarLink(
                    WIRequest::string('name', '', 'post'),
                    WIRequest::string('link', '', 'post'),
                    WIRequest::int('parent', 0, 'post')
                )
            );
            break;

        case 'saveSidebarMenu':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json(
                $web->saveSidebarItems((array) WIRequest::post('sidebar_items', []))
            );
            break;

        case 'editAdminMenu':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json(
                $web->editAdminMenu(WIRequest::int('id', 0, 'post'))
            );
            break;

        case 'adminMenuEdit':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json(
                $web->adminMenuEdit((array) WIRequest::post('menu', []))
            );
            break;

        case 'newAdminMenuItem':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json(
                $web->newAdminMenuItem((array) WIRequest::post('menu', []))
            );
            break;

        case 'deleteAdminMenu':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json(
                $web->deleteAdminMenu(WIRequest::int('id', 0, 'post'))
            );
            break;

        /*
        |--------------------------------------------------------------------------
        | Page settings
        |--------------------------------------------------------------------------
        */

        case 'getPage':
            onlyAdmin();
            $page = new WIPage();
            WIResponse::json(
                $page->getPageById(WIRequest::int('id', 0, 'post'))
            );
            break;

        case 'savePage':
            onlyAdmin();
            $page = new WIPage();
            WIResponse::json($page->savePage(WIRequest::allPost()));
            break;

        case 'deletePage':
            onlyAdmin();
            $page = new WIPage();
            WIResponse::json(
                $page->deletePage(WIRequest::int('id', 0, 'post'))
            );
            break;

        /*
        |--------------------------------------------------------------------------
        | Themes / website assets
        |--------------------------------------------------------------------------
        */

        case 'viewThemes':
            onlyAdmin();
            $web = new WIWebsite();
            $web->viewThemes();
            exit;

        case 'themeActivate':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->activateThemes(WIRequest::int('id', 0, 'post')));
            break;

        case 'theme':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->NewTheme(WIRequest::string('name', '', 'post')));
            break;

        case 'setTheme':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json(
                $web->setTheme(
                    WIRequest::int('id', 0, 'post'),
                    postValue('val', null)
                )
            );
            break;

        case 'deletetheme':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->deletetheme(WIRequest::int('id', 0, 'post')));
            break;

        case 'editMeta':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->ViewEditMeta(WIRequest::int('id', 0, 'post')));
            break;

        case 'editMetaDetails':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->EditMeta((array) WIRequest::post('meta', [])));
            break;

        case 'DeleteMeta':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->DeleteMeta(WIRequest::int('id', 0, 'post')));
            break;

        case 'editCss':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->ViewEditCSS(WIRequest::int('id', 0, 'post')));
            break;

        case 'editCssDetails':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->EditCss((array) WIRequest::post('CSS', [])));
            break;

        case 'DeleteCss':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->DeleteCss(WIRequest::int('id', 0, 'post')));
            break;

        case 'ViewEditJs':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->ViewEditJs(WIRequest::int('id', 0, 'post')));
            break;

        case 'editJsDetails':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->EditJs((array) WIRequest::post('script', [])));
            break;

        case 'deletejs':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->deletejs(WIRequest::int('id', 0, 'post')));
            break;

        case 'editScript':
            onlyAdmin();
            $web = new WIWebsite();
            WIResponse::json($web->editScript((array) WIRequest::post('script', [])));
            break;

        /*
        |--------------------------------------------------------------------------
        | Modules / Elements store actions
        |--------------------------------------------------------------------------
        */

        case 'install_module':
        case 'mod_install':
            onlyAdmin();

            $mod = new WIModules();
            $ok = $mod->install_mod(WIRequest::string('mod_name', '', 'post'));

            if (!$ok) {
                WIResponse::error('Failed to install module.');
            }

            WIResponse::success('Module installed successfully.');
            break;

        case 'uninstall_module':
        case 'mod_uninstall':
            onlyAdmin();

            $mod = new WIModules();
            $ok = $mod->uninstall_mod(WIRequest::string('mod_name', '', 'post'));

            if (!$ok) {
                WIResponse::error('Failed to uninstall module.');
            }

            WIResponse::success('Module uninstalled successfully.');
            break;

        case 'enable_module':
        case 'mod_enable':
            onlyAdmin();

            $mod = new WIModules();
            $ok = $mod->active_available_mod(WIRequest::string('mod_name', '', 'post'));

            if (!$ok) {
                WIResponse::error('Failed to enable module.');
            }

            WIResponse::success('Module enabled successfully.');
            break;

        case 'disable_module':
        case 'mod_disable':
            onlyAdmin();

            $mod = new WIModules();
            $ok = $mod->deactive_available_mod(WIRequest::string('mod_name', '', 'post'));

            if (!$ok) {
                WIResponse::error('Failed to disable module.');
            }

            WIResponse::success('Module disabled successfully.');
            break;

        case 'install_element':
        case 'element_install':
            onlyAdmin();

            $mod = new WIModules();
            $ok = $mod->installElement(WIRequest::string('element_name', '', 'post'));

            if (!$ok) {
                WIResponse::error('Failed to install element.');
            }

            WIResponse::success('Element installed successfully.');
            break;

        case 'uninstall_element':
        case 'ele_uninstall':
            onlyAdmin();

            $mod = new WIModules();
            $elementName = WIRequest::string('element_name', '', 'post');

            if ($elementName === '') {
                $elementName = WIRequest::string('mod_name', '', 'post');
            }

            $ok = $mod->unistall_Element($elementName);

            if (!$ok) {
                WIResponse::error('Failed to uninstall element.');
            }

            WIResponse::success('Element uninstalled successfully.');
            break;

        case 'enable_element':
        case 'Element_enable':
            onlyAdmin();

            $mod = new WIModules();
            $ok = $mod->activateAvailableElements(WIRequest::string('element_name', '', 'post'));

            if (!$ok) {
                WIResponse::error('Failed to enable element.');
            }

            WIResponse::success('Element enabled successfully.');
            break;

        case 'disable_element':
        case 'Element_disable':
            onlyAdmin();

            $mod = new WIModules();
            $ok = $mod->deactivateAvailableElements(WIRequest::string('element_name', '', 'post'));

            if (!$ok) {
                WIResponse::error('Failed to disable element.');
            }

            WIResponse::success('Element disabled successfully.');
            break;

        /*
        |--------------------------------------------------------------------------
        | Modules / Elements pagination
        |--------------------------------------------------------------------------
        */

        case 'NextModPage':
            onlyAdmin();
            $mod = new WIModules();

            ob_start();
            $mod->displayModules();
            $html = ob_get_clean();

            WIResponse::success('Modules page loaded.', [
                'html' => $html
            ]);
            break;

        case 'NextElementsPage':
            onlyAdmin();
            $mod = new WIModules();

            ob_start();
            $mod->displayElements();
            $html = ob_get_clean();

            WIResponse::success('Elements page loaded.', [
                'html' => $html
            ]);
            break;

        case 'NextInstalledModulesPage':
            onlyAdmin();
            $mod = new WIModules();

            ob_start();
            $mod->getModules();
            $html = ob_get_clean();

            WIResponse::success('Installed modules page loaded.', [
                'html' => $html
            ]);
            break;

        case 'NextInstalledElementsPage':
            onlyAdmin();
            $mod = new WIModules();

            ob_start();
            $mod->getElements();
            $html = ob_get_clean();

            WIResponse::success('Installed elements page loaded.', [
                'html' => $html
            ]);
            break;

        case 'NextBuilderModulesPage':
            onlyAdmin();
            $mod = new WIModules();

            ob_start();
            $mod->getPageModules();
            $html = ob_get_clean();

            WIResponse::success('Builder modules page loaded.', [
                'html' => $html
            ]);
            break;

        /*
        |--------------------------------------------------------------------------
        | Plugins
        |--------------------------------------------------------------------------
        */

        case 'install_plugin':
            onlyAdmin();

            $plugin = new WIPlugin();
            $ok = $plugin->install(WIRequest::string('plugin', '', 'post'));

            if (!$ok) {
                WIResponse::error('Failed to install plugin.');
            }

            WIResponse::success('Plugin installed successfully.');
            break;

        case 'uninstall_plugin':
            onlyAdmin();

            $plugin = new WIPlugin();
            $ok = $plugin->uninstall(WIRequest::string('plugin', '', 'post'));

            if (!$ok) {
                WIResponse::error('Failed to uninstall plugin.');
            }

            WIResponse::success('Plugin uninstalled successfully.');
            break;

        case 'enable_plugin':
            onlyAdmin();

            $plugin = new WIPlugin();
            $ok = $plugin->enable(WIRequest::string('plugin', '', 'post'));

            if (!$ok) {
                WIResponse::error('Failed to enable plugin.');
            }

            WIResponse::success('Plugin enabled successfully.');
            break;

        case 'disable_plugin':
            onlyAdmin();

            $plugin = new WIPlugin();
            $ok = $plugin->disable(WIRequest::string('plugin', '', 'post'));

            if (!$ok) {
                WIResponse::error('Failed to disable plugin.');
            }

            WIResponse::success('Plugin disabled successfully.');
            break;

        case 'plugin_create_order':
            onlyAdmin();

            $plugin = new WIPlugin();
            $orderId = $plugin->createOrder(
                WIRequest::int('user_id', 0, 'post'),
                WIRequest::string('plugin_slug', '', 'post'),
                (float) WIRequest::post('price', 0),
                WIRequest::string('currency', 'GBP', 'post'),
                WIRequest::string('gateway', 'manual', 'post')
            );

            if ($orderId <= 0) {
                WIResponse::error('Failed to create plugin order.');
            }

            WIResponse::success('Plugin order created.', [
                'order_id' => $orderId
            ]);
            break;

        case 'plugin_complete_purchase':
            onlyAdmin();

            $plugin = new WIPlugin();
            $result = $plugin->completePurchase(
                WIRequest::int('user_id', 0, 'post'),
                WIRequest::string('plugin_slug', '', 'post'),
                (float) WIRequest::post('price', 0),
                WIRequest::string('currency', 'GBP', 'post'),
                WIRequest::string('gateway', 'manual', 'post'),
                WIRequest::string('license_type', 'lifetime', 'post'),
                (bool) WIRequest::post('subscription', false),
                WIRequest::string('subscription_period', 'monthly', 'post')
            );

            if (!is_array($result) || ($result['status'] ?? '') !== 'success') {
                WIResponse::error('Plugin purchase failed.', [
                    'result' => $result
                ]);
            }

            WIResponse::success('Plugin purchase completed successfully.', $result);
            break;

        case 'plugin_validate_license':
            onlyAdmin();

            $plugin = new WIPlugin();
            $valid = $plugin->validateLicense(
                WIRequest::string('license_key', '', 'post'),
                WIRequest::string('plugin_slug', '', 'post')
            );

            WIResponse::success('License validation complete.', [
                'valid' => $valid
            ]);
            break;

        /*
        |--------------------------------------------------------------------------
        | Legacy / compatibility hooks
        |--------------------------------------------------------------------------
        */

        case 'addAttr':
            onlyAdmin();
            $mod = new WIModules();
            safeMethodCall($mod, 'fieldEdit');
            exit;

        default:
            WIResponse::error('Unknown POST action: ' . $postAction, [], 400);
            break;
    }
}

/*
|--------------------------------------------------------------------------
| GET actions
|--------------------------------------------------------------------------
*/

$getAction = WIRequest::string('action', '', 'get');

if ($getAction !== '') {
    switch ($getAction) {
        /*
        |--------------------------------------------------------------------------
        | Dashboard (IVO)
        |--------------------------------------------------------------------------
        */

        case 'dashboard_refresh':
            onlyAdmin();

            $dashboard = new WIDashboard();

            WIResponse::json([
                'status' => 'success',
                'data'   => $dashboard->getDashboardData(),
            ]);
            break;

        /*
        |--------------------------------------------------------------------------
        | Counts / small widgets
        |--------------------------------------------------------------------------
        */

        case 'registeredUsercount':
            onlyAdmin();
            $site = new WISite();
            $site->RegisteredUsers();
            exit;

        case 'NotificationsCount':
            onlyAdmin();
            $site = new WISite();
            $site->notifications_badge();
            exit;

        case 'MessagesCount':
            onlyAdmin();
            $site = new WISite();
            $site->MessageBagde();
            exit;

        case 'activeChatCount':
            onlyAdmin();
            $site = new WISite();
            $site->ActiveChatCount();
            exit;

        case 'TasksCount':
            onlyAdmin();
            $site = new WISite();
            $site->TaskBagde();
            exit;

        case 'tasks':
            onlyAdmin();
            $site = new WISite();
            $site->tasks();
            exit;

        /*
        |--------------------------------------------------------------------------
        | Chat / messages / calendar
        |--------------------------------------------------------------------------
        */

        case 'CheckChat':
            $chat = new WIChat();
            $chat->getChatMessages(
                WIRequest::string('last_chat_time', '', 'get'),
                WIRequest::int('userId', 0, 'get')
            );
            exit;

        case 'getChats':
            WIResponse::json(Chat::getChats(WIRequest::int('lastID', 0, 'get')));
            break;

        case 'calendar':
            $calendar = new WICalendar();
            $calendar->getCalendar();
            exit;

        case 'getMessages':
            $contact = new WIContact();
            $contact->Messages();
            exit;

        /*
        |--------------------------------------------------------------------------
        | Shop
        |--------------------------------------------------------------------------
        */

        case 'getProdShipping':
            $shop = new WIShop();
            $shop->getProdShipping();
            exit;

        /*
        |--------------------------------------------------------------------------
        | Legacy / compatibility hooks
        |--------------------------------------------------------------------------
        */

        case 'load_mod':
            onlyAdmin();
            $mod = new WIModules();
            safeMethodCall($mod, 'tasks');
            exit;

        default:
            WIResponse::error('Unknown GET action: ' . $getAction, [], 400);
            break;
    }
}

WIResponse::error('No action supplied.', [], 400);