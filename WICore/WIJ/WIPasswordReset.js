/**
 * FILE:
 * WICMS-IVO/WICore/WIJ/WIPasswordReset.js
 *
 * Canonical password reset flow handler for WICMS.
 */

var passres = (function () {

    function forgotPassword(userEmail) {
        var btn = $("#btn-forgot-password");
        var form = $("#forgot-password-form");

        WICore.loadingButton(btn, $_lang.working || "Working...");

        WICore.ajax({
            url: form.data("ajax") || "WICore/WIClass/WIAjax.php",
            data: {
                action: "forgotPassword",
                email: userEmail,
                csrf_token: form.find('input[name="csrf_token"]').val() || WICore.getCSRF()
            },

            onSuccess: function (response) {
                WICore.displaySuccessMessage(
                    $("#forgot-password-form fieldset, #forgot-password-form"),
                    response.message || $_lang.password_reset_email_sent || "Password reset request sent."
                );

                if (form.length && form[0]) {
                    form[0].reset();
                }
            },

            onError: function (response) {
                WICore.displayErrorMessage(
                    $("#forgot-password-email, #forgot-email"),
                    response.message || "Unable to process password reset request."
                );
            },

            onComplete: function () {
                WICore.removeLoadingButton(btn);
            }
        });
    }

    function resetPassword(newPass) {
        var btn = $("#btn-reset-pass, #btn-reset-password").first();
        var form = $("#password-reset-form, #reset-password-form").first();
        var key = $("#reset-key").val() || WICore.urlParam("key") || WICore.urlParam("k") || "";

        WICore.loadingButton(btn, $_lang.resetting || "Resetting...");

        WICore.ajax({
            url: form.data("ajax") || "WICore/WIClass/WIAjax.php",
            data: {
                action: "resetPassword",
                newPass: newPass,
                key: key,
                csrf_token: form.find('input[name="csrf_token"]').val() || WICore.getCSRF()
            },

            onSuccess: function (response) {
                WICore.displaySuccessMessage(
                    $("#password-reset-form fieldset, #reset-password-form fieldset, #password-reset-form, #reset-password-form").first(),
                    response.message || $_lang.password_updated_successfully_login || "Password reset complete."
                );

                if (form.length && form[0]) {
                    form[0].reset();
                }
            },

            onError: function (response) {
                WICore.displayErrorMessage(
                    $("#password-reset-new-password, #reset-password").first(),
                    response.message || "Unable to reset password."
                );
            },

            onComplete: function () {
                WICore.removeLoadingButton(btn);
            }
        });
    }

    function initForgotPassword() {
        $("#btn-forgot-password").on("click", function (e) {
            e.preventDefault();

            var emailField = $("#forgot-password-email, #forgot-email").first();
            var email = $.trim(emailField.val());

            WICore.removeErrorMessages();

            if (email === "") {
                WICore.displayErrorMessage(emailField, $_lang.email_required || "Email is required.");
                return false;
            }

            if (!WICore.validateEmail(email)) {
                WICore.displayErrorMessage(emailField, $_lang.email_wrong_format || "Please enter a valid email address.");
                return false;
            }

            forgotPassword(email);
            return false;
        });
    }

    function initResetPassword() {
        $("#btn-reset-pass, #btn-reset-password").on("click", function (e) {
            e.preventDefault();

            var passwordField = $("#password-reset-new-password, #reset-password").first();
            var confirmField = $("#password-reset-repeat-password, #reset-password-confirm").first();
            var password = passwordField.val();
            var confirmPassword = confirmField.val();

            WICore.removeErrorMessages();

            if ($.trim(password) === "") {
                WICore.displayErrorMessage(passwordField, $_lang.password_required || "Password is required.");
                return false;
            }

            if ($.trim(password).length < 8) {
                WICore.displayErrorMessage(passwordField, $_lang.password_length || "Password must be at least 8 characters.");
                return false;
            }

            if ($.trim(confirmPassword) === "") {
                WICore.displayErrorMessage(confirmField, $_lang.password_required || "Please confirm your password.");
                return false;
            }

            if (password !== confirmPassword) {
                WICore.displayErrorMessage(confirmField, $_lang.password_dont_match || "Passwords do not match.");
                return false;
            }

            resetPassword(password);
            return false;
        });
    }

    function init() {
        $(".form-horizontal").on("submit", function (e) {
            e.preventDefault();
            return false;
        });

        initForgotPassword();
        initResetPassword();
    }

    $(document).ready(function () {
        init();
    });

    return {
        init: init,
        forgotPassword: forgotPassword,
        resetPassword: resetPassword
    };

})();