<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Admin Action Guard
|--------------------------------------------------------------------------
| Protects admin AJAX actions with granular WICMS permission codes.
|
| admin.access only allows a user into the admin shell. This class decides
| whether the current role may save, delete, install, uninstall, purge,
| edit settings, manage users, or perform other sensitive AJAX actions.
|--------------------------------------------------------------------------
*/

final class WICMSAdminActionGuard
{
    /**
     * Action rules are intentionally explicit. Unknown actions are left to
     * existing route-level requireAdmin() checks until they are mapped.
     *
     * @return array<string, array<int, string>>
     */
    private static function rules(): array
    {
        return [
            // Roles / permissions legacy WIAjax actions.
            'getRole' => ['roles.view'],
            'saveRole' => ['roles.edit'],
            'deleteRole' => ['roles.delete'],
            'getRolePermissions' => ['permissions.view'],
            'updateRolePermissions' => ['permissions.edit'],
            'getPermission' => ['permissions.view'],
            'savePermission' => ['permissions.edit'],
            'deletePermission' => ['permissions.delete'],
            'toggleRolePermission' => ['permissions.edit'],

            // Plugin lifecycle.
            'install_plugin' => ['plugins.manage'],
            'enable_plugin' => ['plugins.manage'],
            'disable_plugin' => ['plugins.manage'],
            'uninstall_plugin' => ['plugins.uninstall'],
            'plugin_validate_license' => ['plugins.manage'],
            'plugin_create_order' => ['plugins.manage'],
            'plugin_complete_purchase' => ['plugins.manage'],

            // Module / element lifecycle.
            'install_module' => ['modules.manage'],
            'mod_install' => ['modules.manage'],
            'uninstall_module' => ['modules.delete'],
            'mod_uninstall' => ['modules.delete'],
            'enable_module' => ['modules.manage'],
            'mod_enable' => ['modules.manage'],
            'disable_module' => ['modules.manage'],
            'mod_disable' => ['modules.manage'],
            'install_element' => ['modules.manage'],
            'element_install' => ['modules.manage'],
            'uninstall_element' => ['modules.delete'],
            'ele_uninstall' => ['modules.delete'],
            'enable_element' => ['modules.manage'],
            'Element_enable' => ['modules.manage'],
            'disable_element' => ['modules.manage'],
            'Element_disable' => ['modules.manage'],

            // WICMS settings.
            'site_settings' => ['settings.edit'],
            'database_settings' => ['settings.edit'],
            'email_settings' => ['settings.edit'],
            'mailer_settings' => ['settings.edit'],
            'session_settings' => ['settings.edit'],
            'bug_reporter_settings' => ['settings.edit'],
            'verification_settings' => ['settings.edit'],
            'encryption' => ['settings.edit'],
            'login_settings' => ['settings.edit'],
            'social_settings' => ['settings.edit'],
            'twitter' => ['settings.edit'],
            'version_control' => ['settings.edit'],

            // Header/footer, language, content and media config.
            'header_settings' => ['settings.edit'],
            'footer_settings' => ['settings.edit'],
            'wicms_header_footer_save' => ['settings.edit'],
            'wicms_header_footer_upload' => ['media.upload', 'settings.edit'],
            'lang_settings' => ['settings.edit'],
            'wicms_language_settings_save' => ['settings.edit'],
            'wicms_language_save' => ['settings.edit'],
            'wicms_language_delete' => ['settings.edit'],
            'wicms_translation_save' => ['settings.edit'],
            'wicms_translation_delete' => ['settings.edit'],

            // Pages.
            'savePage' => ['pages.edit'],
            'deletePage' => ['pages.delete'],

            // Users/account admin actions.
            'updateDetails' => ['users.edit'],
            'updatePassword' => ['users.edit'],
        ];
    }

    public static function hasRule(string $action, string $method = 'POST'): bool
    {
        $action = trim($action);
        return $action !== '' && isset(self::rules()[$action]);
    }

    /**
     * @return string[]
     */
    public static function requiredPermissions(string $action, string $method = 'POST'): array
    {
        return self::rules()[trim($action)] ?? [];
    }

    public static function enforce(string $action, string $method, WIAdmin $admin): void
    {
        $required = self::requiredPermissions($action, $method);
        if ($required === []) {
            return;
        }

        if (!$admin->hasAnyPermission($required)) {
            WIResponse::error(
                'Permission required for this action.',
                [
                    'action' => $action,
                    'required_permissions' => $required,
                ],
                403
            );
        }
    }
}
