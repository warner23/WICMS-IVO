/**
 * FILE:
 * WICMS-IVO/WICore/WIJ/WICore.js
 *
 * Canonical front/core JS helper for WICMS.
 * Handles UI + AJAX + validation in a clean, reusable way.
 */

var WICore = (function () {

    function e(value) {
        return String(value ?? '');
    }

    /* =========================
       BUTTON STATE
    ========================= */

    function loadingButton(button, loadingText = 'Loading...') {
        if (!button || button.length === 0) return;

        const oldText = button.text();

        button
            .data('old-text', oldText)
            .text(loadingText)
            .prop('disabled', true)
            .addClass('wi-loading');
    }

    function removeLoadingButton(button) {
        if (!button || button.length === 0) return;

        const oldText = button.data('old-text');

        if (oldText) {
            button.text(oldText);
        }

        button
            .prop('disabled', false)
            .removeClass('wi-loading')
            .removeData('old-text');
    }

    /* =========================
       ALERTS (MODERN)
    ========================= */

    function displaySuccessMessage(parent, message) {
        clearAlerts(parent);

        const html = `
            <div class="wi-alert wi-alert-success">
                ${e(message)}
            </div>
        `;

        parent.append(html);
    }

    function displayErrorMessage(input, message) {
        const group = input.closest('.wi-form-group');

        group.addClass('wi-error');

        if (message) {
            const error = $(`<div class="wi-error-text">${e(message)}</div>`);
            group.append(error);
        }
    }

    function removeErrorMessages() {
        $('.wi-form-group')
            .removeClass('wi-error')
            .find('.wi-error-text')
            .remove();
    }

    function clearAlerts(parent) {
        parent.find('.wi-alert').remove();
    }

    /* =========================
       VALIDATION
    ========================= */

    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    /* =========================
       URL HELPERS
    ========================= */

    function urlParam(name) {
        const params = new URLSearchParams(window.location.search);
        return params.get(name);
    }

    function refresh() {
        window.location.reload();
    }

    /* =========================
       AJAX HELPER (IMPORTANT)
    ========================= */

    function ajax(options) {
        const defaults = {
            url: "WICore/WIClass/WIAjax.php",
            method: "POST",
            dataType: "json",
            timeout: 10000,
            data: {},
            onSuccess: function () {},
            onError: function () {},
            onComplete: function () {}
        };

        const settings = Object.assign({}, defaults, options);

        $.ajax({
            url: settings.url,
            type: settings.method,
            dataType: settings.dataType,
            data: settings.data,
            timeout: settings.timeout,

            success: function (response) {
                if (response && response.status === 'success') {
                    settings.onSuccess(response);
                } else {
                    settings.onError(response || { message: 'Unknown error' });
                }
            },

            error: function (xhr) {
                settings.onError({
                    message: 'Network or server error',
                    xhr: xhr
                });
            },

            complete: function () {
                settings.onComplete();
            }
        });
    }

    /* =========================
       CSRF HELPER
    ========================= */

    function getCSRF() {
        const token = $('input[name="csrf_token"]').val();
        return token || '';
    }

    /* =========================
       PUBLIC API
    ========================= */

    return {
        loadingButton,
        removeLoadingButton,
        displaySuccessMessage,
        displayErrorMessage,
        removeErrorMessages,
        validateEmail,
        urlParam,
        refresh,
        ajax,
        getCSRF
    };

})();