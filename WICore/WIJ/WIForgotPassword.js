/**
 * FILE:
 * WICMS-IVO/WICore/WIJ/WIForgotPassword.js
 *
 * Canonical forgotten password handler for WICMS.
 */

var WIForgotPassword = (function () {

    function showMessage(type, message) {
        const box = $('#forgot-message');

        box
            .removeClass('success error')
            .addClass(type)
            .text(message)
            .show();
    }

    function clearMessage() {
        $('#forgot-message')
            .removeClass('success error')
            .text('')
            .hide();
    }

    function handleSubmit(form) {
        const emailField = $('#forgot-email');
        const button = $('#btn-forgot-password');
        const email = $.trim(emailField.val());

        clearMessage();
        WICore.removeErrorMessages();

        if (email === '') {
            WICore.displayErrorMessage(emailField, 'Email is required.');
            return false;
        }

        if (!WICore.validateEmail(email)) {
            WICore.displayErrorMessage(emailField, 'Please enter a valid email address.');
            return false;
        }

        WICore.loadingButton(button, 'Sending...');

        WICore.ajax({
            url: form.data('ajax') || 'WICore/WIClass/WIAjax.php',
            data: {
                action: 'forgotPassword',
                email: email,
                csrf_token: form.find('input[name="csrf_token"]').val() || WICore.getCSRF()
            },

            onSuccess: function (response) {
                showMessage('success', response.message || 'Password reset request sent.');
                form[0].reset();
            },

            onError: function (response) {
                showMessage('error', response.message || 'Unable to process request.');
            },

            onComplete: function () {
                WICore.removeLoadingButton(button);
            }
        });

        return false;
    }

    function init() {
        $(document).on('submit', '#forgot-password-form', function (e) {
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