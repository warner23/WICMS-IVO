/**
 * FILE:
 * WICMS-IVO/WICore/WIJ/WIResetPassword.js
 *
 * Canonical reset password handler for WICMS.
 */

var WIResetPassword = (function () {

    function showMessage(type, message) {
        const box = $('#reset-message');

        box
            .removeClass('success error')
            .addClass(type)
            .text(message)
            .show();
    }

    function clearMessage() {
        $('#reset-message')
            .removeClass('success error')
            .text('')
            .hide();
    }

    function handleSubmit(form) {
        const passwordField = $('#reset-password');
        const confirmField = $('#reset-password-confirm');
        const keyField = $('#reset-key');
        const button = $('#btn-reset-password');

        const password = passwordField.val();
        const confirmPassword = confirmField.val();
        const key = $.trim(keyField.val());

        clearMessage();
        WICore.removeErrorMessages();

        if ($.trim(password) === '') {
            WICore.displayErrorMessage(passwordField, 'Password is required.');
            return false;
        }

        if (password.length < 8) {
            WICore.displayErrorMessage(passwordField, 'Password must be at least 8 characters.');
            return false;
        }

        if ($.trim(confirmPassword) === '') {
            WICore.displayErrorMessage(confirmField, 'Please confirm your password.');
            return false;
        }

        if (password !== confirmPassword) {
            WICore.displayErrorMessage(confirmField, 'Passwords do not match.');
            return false;
        }

        if (key === '') {
            showMessage('error', 'Missing reset key.');
            return false;
        }

        WICore.loadingButton(button, 'Updating...');

        WICore.ajax({
            url: form.data('ajax') || 'WICore/WIClass/WIAjax.php',
            data: {
                action: 'resetPassword',
                newPass: password,
                key: key,
                csrf_token: form.find('input[name="csrf_token"]').val() || WICore.getCSRF()
            },

            onSuccess: function (response) {
                showMessage('success', response.message || 'Password reset complete.');
                form[0].reset();
            },

            onError: function (response) {
                showMessage('error', response.message || 'Unable to reset password.');
            },

            onComplete: function () {
                WICore.removeLoadingButton(button);
            }
        });

        return false;
    }

    function init() {
        $(document).on('submit', '#reset-password-form', function (e) {
            e.preventDefault();
            handleSubmit($(this));
        });
    }

    $(document).ready(function () {
        init();
    });

    return {
        init: init
    };

})();