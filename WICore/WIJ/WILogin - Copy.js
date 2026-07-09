$(document).ready(function () {
    $('.form-horizontal').on('submit', function (e) {
        e.preventDefault();
        login.handleSubmit($(this));
    });

    $('#btn-login').on('click', function (e) {
        e.preventDefault();
        login.handleSubmit($(this).closest('form'));
    });

    $('#login-username').trigger('focus');
});

var login = {};

login.handleSubmit = function (form) {
    var usernameField = $('#login-username');
    var passwordField = $('#login-password');

    if (login.validateLogin(usernameField, passwordField) !== true) {
        return false;
    }

    var data = {
        username: $.trim(usernameField.val()),
        password: passwordField.val(),
        csrf_token: login.getCsrfToken(form),
        id: {
            username: 'login-username',
            password: 'login-password'
        }
    };

    login.loginUser(data, form);

    return false;
};

login.loginUser = function (data, form) {
    var btn = $('#btn-login');
    var ajaxUrl = login.getAjaxUrl(form);

    if (typeof WICore !== 'undefined' && typeof WICore.loadingButton === 'function') {
        WICore.loadingButton(btn, $_lang.logging_in || 'Logging in...');
    }

    var formData = form.serialize() + '&action=checkLogin';

    $.ajax({
        url: ajaxUrl,
        type: 'POST',
        dataType: 'json',
        data: formData,
        success: function (result) {
            login.resetButton(btn);

            if (result && result.status === 'success') {
                window.location = result.page || result.redirect || 'index.php';
                return;
            }

            WICore.removeErrorMessages();
            var message = (result && result.message) ? result.message : 'Login failed.';
            WICore.displayErrorMessage($('#login-password'), message);
        },
        error: function (xhr) {
            login.resetButton(btn);
            WICore.removeErrorMessages();

            var message = 'Something went wrong. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }

            WICore.displayErrorMessage($('#login-password'), message);
        }
    });
};

login.validateLogin = function (usernameField, passwordField) {
    var valid = true;

    WICore.removeErrorMessages();

    if ($.trim(usernameField.val()) === '') {
        WICore.displayErrorMessage(usernameField, ($_lang.username_required || 'Username is required'));
        valid = false;
    }

    if ($.trim(passwordField.val()) === '') {
        WICore.displayErrorMessage(passwordField, ($_lang.password_required || 'Password is required'));
        valid = false;
    }

    return valid;
};

login.getCsrfToken = function (form) {
    var token = '';

    if (form && form.length) {
        token = form.find('input[name="csrf_token"]').val() || '';
    }

    if (!token) {
        token = $('input[name="csrf_token"]').first().val() || '';
    }

    if (!token) {
        token = $('meta[name="csrf-token"]').attr('content') || '';
    }

    return token;
};

login.getAjaxUrl = function (form) {
    var defaultUrl = 'WICore/WIClass/WIAjax.php';

    if (!form || !form.length) {
        return defaultUrl;
    }

    return form.data('ajax') || defaultUrl;
};

login.resetButton = function (btn) {
    if (typeof WICore !== 'undefined' && typeof WICore.removeLoadingButton === 'function') {
        WICore.removeLoadingButton(btn);
    }
};