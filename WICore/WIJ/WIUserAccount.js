/**
 * FILE:
 * WICMS-IVO/WICore/WIJ/WIUserAccount.js
 *
 * Canonical user account actions for WICMS.
 */

var WIUserAccount = (function () {

    function bindPasswordUpdate() {
        $(document).on('submit', '#update-password-form', function (e) {
            e.preventDefault();

            const form = $(this);
            const oldPassword = $.trim($('#account-old-password').val());
            const newPassword = $('#account-new-password').val();
            const confirmPassword = $('#account-confirm-password').val();
            const button = form.find('button[type="submit"]');

            WICore.removeErrorMessages();
            form.find('.wi-alert').remove();

            if (oldPassword === '') {
                WICore.displayErrorMessage($('#account-old-password'), 'Current password is required.');
                return false;
            }

            if ($.trim(newPassword) === '') {
                WICore.displayErrorMessage($('#account-new-password'), 'New password is required.');
                return false;
            }

            if (newPassword.length < 8) {
                WICore.displayErrorMessage($('#account-new-password'), 'Password must be at least 8 characters.');
                return false;
            }

            if (newPassword !== confirmPassword) {
                WICore.displayErrorMessage($('#account-confirm-password'), 'Passwords do not match.');
                return false;
            }

            WICore.loadingButton(button, 'Saving...');

            WICore.ajax({
                url: form.data('ajax') || 'WICore/WIClass/WIAjax.php',
                data: {
                    action: 'updatePassword',
                    oldpass: oldPassword,
                    newpass: newPassword,
                    csrf_token: form.find('input[name="csrf_token"]').val() || WICore.getCSRF()
                },

                onSuccess: function (response) {
                    WICore.displaySuccessMessage(form, response.message || 'Password updated.');
                    form[0].reset();
                },

                onError: function (response) {
                    WICore.displayErrorMessage($('#account-new-password'), response.message || 'Unable to update password.');
                },

                onComplete: function () {
                    WICore.removeLoadingButton(button);
                }
            });

            return false;
        });
    }

    function bindDetailsUpdate() {
        $(document).on('submit', '#update-details-form', function (e) {
            e.preventDefault();

            const form = $(this);
            const button = form.find('button[type="submit"]');
            const details = form.serializeArray();
            const payload = {};

            form.find('.wi-alert').remove();

            details.forEach(function (item) {
                if (item.name !== 'csrf_token') {
                    payload[item.name] = item.value;
                }
            });

            WICore.loadingButton(button, 'Saving...');

            WICore.ajax({
                url: form.data('ajax') || 'WICore/WIClass/WIAjax.php',
                data: {
                    action: 'updateDetails',
                    details: payload,
                    csrf_token: form.find('input[name="csrf_token"]').val() || WICore.getCSRF()
                },

                onSuccess: function (response) {
                    WICore.displaySuccessMessage(form, response.message || 'Details updated.');
                },

                onError: function (response) {
                    WICore.displayErrorMessage(form.find(':input').first(), response.message || 'Unable to update details.');
                },

                onComplete: function () {
                    WICore.removeLoadingButton(button);
                }
            });

            return false;
        });
    }

    function init() {
        bindPasswordUpdate();
        bindDetailsUpdate();
    }

    $(document).ready(function () {
        init();
    });

    return {
        init: init
    };

})();