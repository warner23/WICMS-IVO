$(document).ready(function () {
    const $doc = $(document);

    $doc.on('submit', '.form-horizontal', function (e) {
        e.preventDefault();
        login.handleSubmit($(this));
        return false;
    });

    $doc.on('click', '#btn-login', function (e) {
        e.preventDefault();
        login.handleSubmit($(this).closest('form'));
        return false;
    });

    $('#login-username').trigger('focus');
});

var login = {};

login.handleSubmit = function (form) {
    if (!form || !form.length) {
        return false;
    }

    const $username = form.find('#login-username');
    const $password = form.find('#login-password');

    if (!login.validateLogin($username, $password)) {
        return false;
    }

    const ajaxUrl = login.getAjaxUrl(form);
    const payload = {
        action: 'checkLogin',
        username: $.trim($username.val()),
        password: $password.val(),
        csrf_token: login.getCsrfToken(form)
    };

    login.loginUser(ajaxUrl, payload, form);

    return false;
};

login.loginUser = function (ajaxUrl, payload, form) {
    const $btn = form.find('#btn-login');
    const loadingText = (typeof $_lang !== 'undefined' && $_lang.logging_in)
        ? $_lang.logging_in
        : 'Logging in...';

    login.clearMessages(form);

    if (typeof WICore !== 'undefined' && typeof WICore.loadingButton === 'function') {
        WICore.loadingButton($btn, loadingText);
    } else {
        $btn.prop('disabled', true).text(loadingText);
    }

    $.ajax({
        url: ajaxUrl,
        type: 'POST',
        dataType: 'json',
        data: payload,
        success: function (result) {
            login.resetButton($btn);

            if (result && result.status === 'success') {
                const redirectUrl = login.extractRedirect(result);

                if (redirectUrl) {
                    window.location.href = redirectUrl;
                    return;
                }

                window.location.href = 'WIMembers/profile.php';
                return;
            }

            const message = (result && result.message)
                ? result.message
                : 'Login failed. Please check your details and try again.';

            login.showError(form, message);
        },
        error: function (xhr) {
            login.resetButton($btn);

            let message = 'Something went wrong. Please try again.';

            if (xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }

            login.showError(form, message);
        }
    });
};

login.validateLogin = function ($username, $password) {
    let valid = true;

    login.removeErrorState($username);
    login.removeErrorState($password);

    if (!$username.length || $.trim($username.val()) === '') {
        login.addErrorState($username);
        valid = false;
    }

    if (!$password.length || $.trim($password.val()) === '') {
        login.addErrorState($password);
        valid = false;
    }

    return valid;
};

login.getAjaxUrl = function (form) {
    const dataAjax = form.data('ajax');
    return dataAjax ? dataAjax : 'WICore/WIClass/WIAjax.php';
};

login.getCsrfToken = function (form) {
    const $token = form.find('input[name="csrf_token"]');
    return $token.length ? $token.val() : '';
};

login.extractRedirect = function (result) {
    if (result.data && result.data.page) {
        return result.data.page;
    }

    if (result.page) {
        return result.page;
    }

    if (result.redirect) {
        return result.redirect;
    }

    if (result.data && result.data.redirect) {
        return result.data.redirect;
    }

    return '';
};

login.clearMessages = function (form) {
    login.removeErrorState(form.find('#login-username'));
    login.removeErrorState(form.find('#login-password'));

    const $errorBox = form.find('#aerror');
    if ($errorBox.length) {
        $errorBox.html('').hide();
    }
};

login.showError = function (form, message) {
    const $password = form.find('#login-password');
    login.addErrorState($password);

    const $errorBox = form.find('#aerror');
    if ($errorBox.length) {
        $errorBox.html(message).show();
        return;
    }

    alert(message);
};

login.addErrorState = function ($field) {
    if (!$field || !$field.length) {
        return;
    }

    $field.addClass('wi-input-error');
    $field.css({
        borderColor: '#dc2626',
        boxShadow: '0 0 0 4px rgba(220,38,38,.10)'
    });
};

login.removeErrorState = function ($field) {
    if (!$field || !$field.length) {
        return;
    }

    $field.removeClass('wi-input-error');
    $field.css({
        borderColor: '',
        boxShadow: ''
    });
};

login.resetButton = function ($btn) {
    if (!$btn || !$btn.length) {
        return;
    }

    if (typeof WICore !== 'undefined' && typeof WICore.removeLoadingButton === 'function') {
        WICore.removeLoadingButton($btn);
    } else {
        $btn.prop('disabled', false).text('Log In');
    }
};