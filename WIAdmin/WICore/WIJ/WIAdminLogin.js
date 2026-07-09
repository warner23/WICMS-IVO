/*
|--------------------------------------------------------------------------
| WICMS Admin Login — jQuery AJAX Flow v3.9
|--------------------------------------------------------------------------
| The form stays clean in alogin.php.
| Endpoint, action and redirect are owned by this jQuery file, matching the
| preferred WICMS login pattern rather than using data-* attributes in HTML.
*/
(function ($) {
    'use strict';

    if (!$) {
        return;
    }

    var AdminLogin = {
        ajaxUrl: 'WIAdmin/WICore/WIClass/WIAjax.php',
        action: 'checkAdminLogin',
        successRedirect: 'WIAdmin/dashboard.php',
        selectors: {
            form: '#wi-admin-login-form',
            username: '#admin-login-username',
            password: '#admin-login-password',
            passwordToggle: '#admin-login-password-toggle',
            button: '#btn-admin-login',
            error: '#aerror'
        }
    };

    AdminLogin.init = function () {
        var $form = $(AdminLogin.selectors.form);

        if (!$form.length) {
            return;
        }

        $form.off('submit.wiAdminLogin').on('submit.wiAdminLogin', function (event) {
            event.preventDefault();
            AdminLogin.submit(false);
            return false;
        });

        $(AdminLogin.selectors.button).off('click.wiAdminLogin').on('click.wiAdminLogin', function (event) {
            event.preventDefault();
            $form.trigger('submit');
            return false;
        });

        $(AdminLogin.selectors.passwordToggle).off('click.wiAdminLogin').on('click.wiAdminLogin', function (event) {
            event.preventDefault();
            AdminLogin.togglePassword();
            return false;
        });

        $(AdminLogin.selectors.username).trigger('focus');
    };

    AdminLogin.submit = function (retrying) {
        var $form = $(AdminLogin.selectors.form);
        var $username = $(AdminLogin.selectors.username);
        var $password = $(AdminLogin.selectors.password);
        var $button = $(AdminLogin.selectors.button);

        AdminLogin.clear();

        if (!AdminLogin.validate($username, $password)) {
            AdminLogin.show('Username/email and password are required.', false);
            return;
        }

        AdminLogin.loading($button, true);

        $.ajax({
            url: AdminLogin.ajaxUrl,
            type: 'POST',
            dataType: 'json',
            cache: false,
            data: {
                action: AdminLogin.action,
                username: $.trim($username.val()),
                password: $password.val(),
                csrf_token: AdminLogin.csrf($form)
            },
            success: function (result) {
                if (result && (result.status === 'success' || result.success === true)) {
                    AdminLogin.show(result.message || 'Admin login successful.', true);
                    window.location.href = AdminLogin.redirect(result);
                    return;
                }

                if (!retrying && AdminLogin.refreshToken($form, result)) {
                    AdminLogin.submit(true);
                    return;
                }

                AdminLogin.loading($button, false);
                AdminLogin.show(AdminLogin.message(result, 'Admin login failed. Please check your details and try again.'), false);
            },
            error: function (xhr) {
                var payload = xhr && xhr.responseJSON ? xhr.responseJSON : null;

                if (!retrying && AdminLogin.refreshToken($form, payload)) {
                    AdminLogin.submit(true);
                    return;
                }

                AdminLogin.loading($button, false);
                AdminLogin.show(AdminLogin.message(payload, 'Network or server error while logging in.'), false);
            }
        });
    };


    AdminLogin.togglePassword = function () {
        var $password = $(AdminLogin.selectors.password);
        var $toggle = $(AdminLogin.selectors.passwordToggle);

        if (!$password.length || !$toggle.length) {
            return;
        }

        var showing = $password.attr('type') === 'text';
        $password.attr('type', showing ? 'password' : 'text');
        $toggle.attr('aria-pressed', showing ? 'false' : 'true');
        $toggle.text(showing ? 'Show' : 'Hide');
        $password.trigger('focus');
    };

    AdminLogin.validate = function ($username, $password) {
        var ok = true;
        AdminLogin.removeError($username);
        AdminLogin.removeError($password);

        if (!$username.length || $.trim($username.val()) === '') {
            AdminLogin.addError($username);
            ok = false;
        }

        if (!$password.length || $.trim($password.val()) === '') {
            AdminLogin.addError($password);
            ok = false;
        }

        return ok;
    };

    AdminLogin.csrf = function ($form) {
        var $token = $form.find('input[name="csrf_token"]');
        return $token.length ? $token.val() : '';
    };

    AdminLogin.refreshToken = function ($form, payload) {
        var token = payload && (payload.csrf_token || (payload.data && payload.data.csrf_token));

        if (!token) {
            return false;
        }

        var $field = $form.find('input[name="csrf_token"]');
        if (!$field.length) {
            $field = $('<input>', { type: 'hidden', name: 'csrf_token' }).appendTo($form);
        }

        $field.val(token);
        return true;
    };

    AdminLogin.redirect = function (payload) {
        if (payload && payload.redirect) { return payload.redirect; }
        if (payload && payload.page) { return payload.page; }
        if (payload && payload.data && payload.data.redirect) { return payload.data.redirect; }
        if (payload && payload.data && payload.data.page) { return payload.data.page; }
        return AdminLogin.successRedirect;
    };

    AdminLogin.message = function (payload, fallback) {
        if (payload && payload.message) { return payload.message; }
        return fallback;
    };

    AdminLogin.loading = function ($button, active) {
        if (!$button || !$button.length) {
            return;
        }

        if (active) {
            if (!$button.data('old-html')) {
                $button.data('old-html', $button.html());
            }
            $button.prop('disabled', true).text('Signing in...');
            return;
        }

        $button.prop('disabled', false).html($button.data('old-html') || '<span>Sign in to admin</span>');
        $button.removeData('old-html');
    };

    AdminLogin.clear = function () {
        AdminLogin.removeError($(AdminLogin.selectors.username));
        AdminLogin.removeError($(AdminLogin.selectors.password));
        $(AdminLogin.selectors.error).removeClass('is-success').hide().text('');
    };

    AdminLogin.show = function (message, success) {
        var $box = $(AdminLogin.selectors.error);

        if (!$box.length) {
            alert(message);
            return;
        }

        $box.toggleClass('is-success', success === true).text(message || '').toggle(!!message);
    };

    AdminLogin.addError = function ($field) {
        if ($field && $field.length) {
            $field.addClass('wi-input-error');
        }
    };

    AdminLogin.removeError = function ($field) {
        if ($field && $field.length) {
            $field.removeClass('wi-input-error');
        }
    };

    $(AdminLogin.init);
    window.WICMSAdminLogin = AdminLogin;
}(window.jQuery));
