$(document).ready(function () {
    $('.register-form').on('submit', function (e) {
        e.preventDefault();
        register.handleSubmit($(this));
    });

    $('#btn-register').on('click', function (e) {
        e.preventDefault();
        register.handleSubmit($(this).closest('form'));
    });
});

var register = {};

register.handleSubmit = function (form) {
    if (register.validateRegistration() !== true) {
        return false;
    }

    var data = {
        UserData: {
            email: $('#reg-email').val(),
            username: $('#reg-username').val(),
            password: $('#reg-password').val(),
            confirm_password: $('#reg-repeat-password').val(),
            bot_sum: $('#reg-bot-sum').length ? $('#reg-bot-sum').val() : ''
        },
        FieldId: {
            email: 'reg-email',
            username: 'reg-username',
            password: 'reg-password',
            confirm_password: 'reg-repeat-password',
            bot_sum: 'reg-bot-sum'
        },
        csrf_token: register.getCsrfToken(form)
    };

    register.registerUser(data, form);

    return false;
};

register.registerUser = function (data, form) {
    var btn = $('#btn-register');
    var ajaxUrl = register.getAjaxUrl(form);

    if (typeof WICore !== 'undefined' && typeof WICore.loadingButton === 'function') {
        WICore.loadingButton(btn, $_lang.creating_account || 'Creating account...');
    }

    $.ajax({
        url: ajaxUrl,
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'registerUser',
            User: {
                UserData: data.UserData,
                FieldId: data.FieldId
            },
            csrf_token: data.csrf_token
        },
        success: function (result) {
            register.resetButton(btn);
            WICore.removeErrorMessages();

            if (result && result.status === 'success') {
                var successMessage = result.message || result.msg || 'Registration successful.';
                WICore.displaySuccessMessage($('.register-form fieldset'), successMessage);
                return;
            }

            if (result && $.isArray(result.errors)) {
                for (var i = 0; i < result.errors.length; i++) {
                    var error = result.errors[i];

                    if (error && error.id) {
                        WICore.displayErrorMessage($('#' + error.id), error.msg || 'Invalid value');
                    }
                }
                return;
            }

            WICore.displayErrorMessage($('#reg-password'), (result && result.message) ? result.message : 'Registration failed.');
        },
        error: function (xhr) {
            register.resetButton(btn);
            WICore.removeErrorMessages();

            var message = 'Something went wrong. Please try again.';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }

            WICore.displayErrorMessage($('#reg-password'), message);
        }
    });
};

register.validateRegistration = function () {
    var valid = true;

    WICore.removeErrorMessages();

    var emailField = $('#reg-email');
    var usernameField = $('#reg-username');
    var passwordField = $('#reg-password');
    var confirmField = $('#reg-repeat-password');

    if ($.trim(emailField.val()) === '') {
        WICore.displayErrorMessage(emailField, $_lang.email_required || 'Email is required');
        valid = false;
    } else if (!WICore.validateEmail(emailField.val())) {
        WICore.displayErrorMessage(emailField, $_lang.email_wrong_format || 'Email format is invalid');
        valid = false;
    }

    if ($.trim(usernameField.val()) === '') {
        WICore.displayErrorMessage(usernameField, $_lang.username_required || 'Username is required');
        valid = false;
    }

    if ($.trim(passwordField.val()) === '') {
        WICore.displayErrorMessage(passwordField, $_lang.password_required || 'Password is required');
        valid = false;
    } else if ($.trim(passwordField.val()).length < 8) {
        WICore.displayErrorMessage(passwordField, $_lang.password_length || 'Password is too short');
        valid = false;
    }

    if ($.trim(confirmField.val()) === '') {
        WICore.displayErrorMessage(confirmField, $_lang.password_required || 'Please confirm your password');
        valid = false;
    } else if (passwordField.val() !== confirmField.val()) {
        WICore.displayErrorMessage(confirmField, $_lang.passwords_dont_match || 'Passwords do not match');
        valid = false;
    }

/*    if ($('#reg-bot-sum').length && $.trim($('#reg-bot-sum').val()) === '') {
        WICore.displayErrorMessage($('#reg-bot-sum'));
        valid = false;
    }*/

    return valid;
};

register.getCsrfToken = function (form) {
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

register.getAjaxUrl = function (form) {
    var defaultUrl = 'WICore/WIClass/WIAjax.php';

    if (!form || !form.length) {
        return defaultUrl;
    }

    return form.data('ajax') || defaultUrl;
};

register.resetButton = function (btn) {
    if (typeof WICore !== 'undefined' && typeof WICore.removeLoadingButton === 'function') {
        WICore.removeLoadingButton(btn);
    }
};