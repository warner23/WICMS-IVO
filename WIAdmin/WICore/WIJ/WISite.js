/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS
| Project: WI Ecosystem
| File: WISite.js
| Location: /WIAdmin/WICore/WIJ/
| Type: JavaScript
| Layer: Admin Settings UI
| Purpose Area: Website settings save flow
| Version: 2.0.0
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
*/

(function () {
    'use strict';

    function ready(callback) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', callback);
            return;
        }

        callback();
    }

    function getField(form, selector) {
        return form.querySelector(selector);
    }

    function fieldValue(form, selector) {
        var field = getField(form, selector);

        return field ? String(field.value || '').trim() : '';
    }

    function setButtonLoading(button, loading) {
        if (!button) {
            return;
        }

        if (loading) {
            button.dataset.originalText = button.textContent;
            button.textContent = 'Saving...';
            button.disabled = true;
            return;
        }

        button.textContent = button.dataset.originalText || 'Save Website Settings';
        button.disabled = false;
        delete button.dataset.originalText;
    }

    function showResult(target, message, success) {
        if (!target) {
            return;
        }

        target.classList.remove('is-success', 'is-error');
        target.classList.add(success ? 'is-success' : 'is-error');
        target.textContent = message || '';
        target.style.display = message ? 'block' : 'none';
    }

    function clearInlineErrors(form) {
        var fields = form.querySelectorAll('.wi-settings-input');

        fields.forEach(function (field) {
            field.classList.remove('is-error');
        });
    }

    function markErrorField(form, id) {
        if (!id) {
            return;
        }

        var field = form.querySelector('#' + CSS.escape(id));

        if (field) {
            field.classList.add('is-error');
        }
    }

    function normaliseResponse(payload) {
        if (!payload || typeof payload !== 'object') {
            return {
                status: 'error',
                message: 'Invalid server response.'
            };
        }

        if (payload.status === 'successful') {
            payload.status = 'success';
        }

        if (!payload.message && payload.msg) {
            payload.message = payload.msg;
        }

        if (!payload.message) {
            payload.message = payload.status === 'success' ? 'Settings saved.' : 'Unable to save settings.';
        }

        return payload;
    }

    function validateWebsiteForm(form) {
        var errors = [];
        var siteName = fieldValue(form, '#website_name');
        var siteUrl = fieldValue(form, '#website_url');

        if (siteName === '') {
            errors.push({ id: 'website_name', msg: 'Website name is required.' });
        }

        if (siteUrl !== '' && !/^https?:\/\//i.test(siteUrl)) {
            errors.push({ id: 'website_url', msg: 'Website URL should start with http:// or https://.' });
        }

        return errors;
    }

    function submitWebsiteSettings(form) {
        var button = getField(form, '#site_settings');
        var resultBox = document.getElementById('wresults');
        var errors = validateWebsiteForm(form);

        clearInlineErrors(form);
        showResult(resultBox, '', true);

        if (errors.length) {
            errors.forEach(function (error) {
                markErrorField(form, error.id);
            });

            showResult(resultBox, errors[0].msg, false);
            return;
        }

        var formData = new FormData(form);
        formData.set('action', 'site_settings');

        setButtonLoading(button, true);

        fetch('WICore/WIClass/WIAjax.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function (response) {
                return response.json().catch(function () {
                    return {
                        status: 'error',
                        message: 'Invalid server response.'
                    };
                });
            })
            .then(function (payload) {
                payload = normaliseResponse(payload);

                if (payload.status === 'success' || payload.success === true) {
                    showResult(resultBox, payload.message || 'Website settings saved.', true);
                    return;
                }

                if (Array.isArray(payload.errors)) {
                    payload.errors.forEach(function (error) {
                        markErrorField(form, error.id || '');
                    });
                }

                showResult(resultBox, payload.message || 'Unable to save website settings.', false);
            })
            .catch(function () {
                showResult(resultBox, 'Network or server error while saving website settings.', false);
            })
            .finally(function () {
                setButtonLoading(button, false);
            });
    }

    ready(function () {
        var form = document.getElementById('website-settings-form');

        if (!form) {
            return;
        }

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            submitWebsiteSettings(form);
        });
    });

    window.WISite = window.WISite || {};

    window.WISite.Version = function (currentVersion) {
        fetch('WICore/WIClass/WIAjax.php', {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                action: 'version_control',
                version: currentVersion
            })
        })
            .then(function (response) { return response.text(); })
            .then(function (html) {
                var target = document.getElementById('version_results');

                if (target) {
                    target.innerHTML = html;
                }
            });
    };
}());
