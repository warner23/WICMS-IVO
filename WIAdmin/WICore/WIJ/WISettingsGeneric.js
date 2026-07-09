/*
|--------------------------------------------------------------------------
| WICMS Generic Settings Form Handler
|--------------------------------------------------------------------------
| Handles modern admin settings forms that post a hidden action field to the
| canonical admin WIAjax endpoint. This lets new settings panels avoid the old
| one-script-per-form drift while legacy panels continue to work as-is.
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

    function setMessage(form, message, ok) {
        var box = form.querySelector('[data-wi-settings-result]') || form.querySelector('.results');

        if (!box) {
            return;
        }

        box.className = 'results ' + (ok ? 'text-success' : 'text-danger');
        box.textContent = message || '';
    }

    function setLoading(button, loading) {
        if (!button) {
            return;
        }

        if (loading) {
            button.dataset.oldText = button.textContent;
            button.textContent = 'Saving...';
            button.disabled = true;
            return;
        }

        button.textContent = button.dataset.oldText || 'Save';
        button.disabled = false;
        delete button.dataset.oldText;
    }

    function responseMessage(payload) {
        return payload && (payload.message || payload.msg) ? (payload.message || payload.msg) : '';
    }

    function handleForm(form) {
        form.addEventListener('submit', function (event) {
            event.preventDefault();

            var button = form.querySelector('button[type="submit"]');
            var url = form.getAttribute('action') || 'WICore/WIClass/WIAjax.php';
            var body = new FormData(form);

            setMessage(form, '', true);
            setLoading(button, true);

            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                body: body,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(function (response) {
                    return response.json().catch(function () {
                        return { status: 'error', message: 'Invalid server response.' };
                    });
                })
                .then(function (payload) {
                    var ok = payload && (payload.success === true || payload.status === 'success' || payload.status === 'successful');
                    setMessage(form, responseMessage(payload) || (ok ? 'Settings saved.' : 'Unable to save settings.'), ok);
                })
                .catch(function () {
                    setMessage(form, 'Network or server error while saving settings.', false);
                })
                .finally(function () {
                    setLoading(button, false);
                });
        });
    }

    ready(function () {
        document.querySelectorAll('.wi-settings-ajax-form').forEach(handleForm);
    });
}());
