<?php
declare(strict_types=1);

/* WICMS Admin Login Module v3.7 — delegates to the unified AJAX form markup. */
class alogin
{
    public function Install($element_name): void
    {
        try {
            $db = WIdb::getInstance();
            if (method_exists($db, 'insert')) {
                $db->insert('wi_modules', [
                    'module_name'    => (string) $element_name,
                    'module_author'  => 'Jules Warner',
                    'module_type'    => 'module',
                    'module_font'    => 'wi_' . (string) $element_name,
                    'module_powered' => 'power_on',
                ]);
            }
        } catch (Throwable $ignored) {
        }
    }

    public function editPageContent($page_id = null): void
    {
        echo '<div class="wi-admin-login-preview"><h3>Admin Login</h3><p>Unified AJAX admin login module.</p></div>';
    }

    public function mod_name($module = null, $page = null, array $payload = []): void
    {
        $csrfField = '<input type="hidden" name="csrf_token" value="">';
        try {
            if (class_exists('WIToken') && method_exists('WIToken', 'csrfField')) {
                $csrfField = WIToken::csrfField('login');
            }
        } catch (Throwable $ignored) {
        }

        echo '<link rel="stylesheet" href="WITheme/WICMS/admin/css/admin-login.css?v=3.9">';
        echo '<div class="wi-admin-login-shell"><section class="wi-admin-login-card" aria-labelledby="wi-admin-login-title">';
        echo '<div class="wi-admin-login-brand" aria-hidden="true">WI</div>';
        echo '<div class="wi-admin-login-header"><p class="wi-admin-login-eyebrow">Secure administration</p><h1 id="wi-admin-login-title">Admin login</h1><p>Enter your administrator account to continue.</p></div>';
        echo '<form id="wi-admin-login-form" class="wi-admin-login-form" method="post" action="#" data-ajax="WIAdmin/WICore/WIClass/WIAjax.php" data-action="checkAdminLogin" data-success-redirect="WIAdmin/dashboard.php" novalidate>';
        echo $csrfField;
        echo '<div class="wi-admin-login-group"><label class="wi-admin-login-label" for="admin-login-username">Username or email</label><input type="text" class="wi-admin-login-input" id="admin-login-username" name="username" placeholder="Username or email" autocomplete="username" required></div>';
        echo '<div class="wi-admin-login-group"><label class="wi-admin-login-label" for="admin-login-password">Password</label><div class="wi-admin-login-password-wrap"><input type="password" class="wi-admin-login-input" id="admin-login-password" name="password" placeholder="Password" autocomplete="current-password" required><button type="button" class="wi-admin-login-password-toggle" id="admin-login-password-toggle" aria-controls="admin-login-password" aria-pressed="false">Show</button></div></div>';
        echo '<button type="submit" id="btn-admin-login" class="wi-admin-login-btn"><span>Sign in to admin</span></button><div class="aerror" id="aerror" aria-live="polite"></div><div class="wi-admin-login-footer"><a href="login.php">Member login</a></div></form></section></div>';
        echo '<script src="WITheme/WICMS/admin/js/jquery-1.12.4.js"></script><script src="WIAdmin/WICore/WIJ/WIAdminLogin.js?v=3.9"></script>';
    }
}
