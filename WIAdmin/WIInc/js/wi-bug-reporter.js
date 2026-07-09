/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WI Shared Core
| Project: WI Ecosystem
| File: wi-bug-reporter.js
| Location: /WIAdmin/WIInc/js/
| Type: Shared UI Script
| Layer: Admin / Shared Module UI
| Purpose Area: Bug reporter widget and admin report thread
| Version: 1.1.0
| Created: 2026-04-16
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
*/

(function () {
    'use strict';

    function appBasePath() {
        const path = window.location.pathname || '';

        const markers = [
            '/WIAdmin/',
            
            '/WIMembers/',
            '/WICore/'
        ];

        for (let i = 0; i < markers.length; i += 1) {
            const index = path.toLowerCase().indexOf(markers[i].toLowerCase());

            if (index > -1) {
                return path.substring(0, index);
            }
        }

        return '';
    }

    function resolveAjaxUrl(url) {
        url = String(url || '').trim();

        if (url === '') {
            url = '/WIAdmin/WICore/WIClass/WIAjax.php';
        }

        if (/^https?:\/\//i.test(url)) {
            return url;
        }

        if (url.charAt(0) === '/') {
            return appBasePath() + url;
        }

        return url;
    }

    function getData(element, key) {
        return String((element && element.dataset && element.dataset[key]) || '').trim();
    }

    function escapeHtml(value) {
        return String(value === null || value === undefined ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function setFeedback(feedback, message, state) {
        if (!feedback) {
            return;
        }

        feedback.textContent = message;
        feedback.classList.remove('is-success', 'is-error', 'is-info');

        if (state === 'success') {
            feedback.classList.add('is-success');
        } else if (state === 'error') {
            feedback.classList.add('is-error');
        } else {
            feedback.classList.add('is-info');
        }
    }

    function openPanel(root) {
        root.classList.add('is-open');

        const panel = root.querySelector('.wi-bug-reporter-panel');

        if (panel) {
            panel.setAttribute('aria-hidden', 'false');
        }
    }

    function closePanel(root) {
        root.classList.remove('is-open');

        const panel = root.querySelector('.wi-bug-reporter-panel');

        if (panel) {
            panel.setAttribute('aria-hidden', 'true');
        }
    }

    function setFormValue(form, name, value) {
        if (form.elements[name]) {
            form.elements[name].value = value;
        }
    }

    function syncDynamicContext(root, form) {
        setFormValue(form, 'page_url', window.location.href);
        setFormValue(form, 'page_key', getData(root, 'pageKey') || document.title || 'unknown-page');
        setFormValue(form, 'module_name', getData(root, 'moduleName'));
        setFormValue(form, 'area_type', getData(root, 'areaType'));
        setFormValue(form, 'action_name', getData(root, 'actionName'));
        setFormValue(form, 'business_id', getData(root, 'businessId'));
        setFormValue(form, 'site_id', getData(root, 'siteId'));
        setFormValue(form, 'user_id', getData(root, 'userId'));
        setFormValue(form, 'user_role_id', getData(root, 'userRoleId'));
        setFormValue(form, 'browser', getData(root, 'browser'));
        setFormValue(form, 'user_agent', navigator.userAgent || getData(root, 'userAgent'));
    }

    async function submitForm(root, form, submitButton, feedback) {
        const ajaxUrl = resolveAjaxUrl(getData(root, 'ajaxUrl'));

        syncDynamicContext(root, form);

        const formData = new FormData(form);

        submitButton.disabled = true;
        setFeedback(feedback, 'Submitting report...', 'info');

        try {
            const response = await fetch(ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const payload = await response.json();

            if (payload && (payload.status === 'success' || payload.success === true)) {
                form.reset();

                if (form.elements.severity) {
                    form.elements.severity.value = 'medium';
                }

                setFeedback(feedback, payload.message || 'Bug report submitted successfully.', 'success');

                window.setTimeout(function () {
                    closePanel(root);
                }, 900);

                return;
            }

            setFeedback(feedback, payload && payload.message ? payload.message : 'Unable to submit the bug report.', 'error');
        } catch (error) {
            setFeedback(feedback, 'Network or server error while submitting the bug report.', 'error');
        } finally {
            submitButton.disabled = false;
        }
    }

    function initBugReporter(root) {
        const toggle = root.querySelector('.wi-bug-reporter-toggle');
        const close = root.querySelector('.wi-bug-reporter-close');
        const form = root.querySelector('.wi-bug-reporter-form');
        const submitButton = root.querySelector('.wi-bug-reporter-submit');
        const feedback = root.querySelector('.wi-bug-reporter-feedback');

        if (!toggle || !close || !form || !submitButton || !feedback) {
            return;
        }

        toggle.addEventListener('click', function () {
            openPanel(root);
        });

        close.addEventListener('click', function () {
            closePanel(root);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && root.classList.contains('is-open')) {
                closePanel(root);
            }
        });

        form.addEventListener('submit', function (event) {
            event.preventDefault();
            submitForm(root, form, submitButton, feedback);
        });
    }

    function ajax(action, data, callback) {
        data = data || {};
        data.action = action;

        fetch(resolveAjaxUrl('/WIAdmin/WICore/WIClass/WIAjax.php'), {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams(data)
        })
            .then(function (res) {
                return res.json();
            })
            .then(function (payload) {
                callback(payload || {});
            })
            .catch(function () {
                callback({
                    success: false,
                    status: 'error',
                    message: 'Unable to contact bug report backend.'
                });
            });
    }

    function loadList(status) {
        const list = document.getElementById('wiBugList');

        if (!list) {
            return;
        }

        list.innerHTML = '<p class="wi-bug-admin-empty">Loading bug reports...</p>';

        ajax('wi_bug_list_filtered', { status: status || '' }, function (res) {
            if (!(res.success === true || res.status === 'success')) {
                list.innerHTML = '<p class="wi-bug-admin-error">' + escapeHtml(res.message || 'Unable to load reports.') + '</p>';
                return;
            }

            const rows = Array.isArray(res.data) ? res.data : [];

            if (!rows.length) {
                list.innerHTML = '<p class="wi-bug-admin-empty">No bug reports found.</p>';
                return;
            }

            let html = '';

            rows.forEach(function (r) {
                html += ''
                    + '<button type="button" class="wi-bug-item" data-id="' + escapeHtml(r.id) + '">'
                    + '<strong>#' + escapeHtml(r.id) + '</strong> '
                    + '<span>' + escapeHtml(r.title || 'Untitled report') + '</span>'
                    + '<small>' + escapeHtml(r.area_type || '') + ' · ' + escapeHtml(r.module_name || '') + '</small>'
                    + '<em class="status ' + escapeHtml(r.status || 'open') + '">' + escapeHtml(r.status || 'open') + '</em>'
                    + '</button>';
            });

            list.innerHTML = html;
        });
    }

    function messageHtml(message) {
        return ''
            + '<div class="wi-bug-message wi-bug-message--' + escapeHtml(message.message_type || 'comment') + '">'
            + '<small>' + escapeHtml(message.message_type || 'comment') + ' · ' + escapeHtml(message.created_at || '') + '</small>'
            + '<p>' + escapeHtml(message.message || '') + '</p>'
            + '</div>';
    }

    function loadThread(id) {
        const thread = document.getElementById('wiBugThread');

        if (!thread) {
            return;
        }

        thread.innerHTML = '<p class="wi-bug-admin-empty">Loading report...</p>';

        ajax('wi_bug_get', { report_id: id }, function (res) {
            if (!(res.success === true || res.status === 'success')) {
                thread.innerHTML = '<p class="wi-bug-admin-error">' + escapeHtml(res.message || 'Unable to load report.') + '</p>';
                return;
            }

            const r = res.data || {};
            const messages = Array.isArray(r.messages) ? r.messages : [];

            let html = ''
                + '<div class="wi-bug-thread-head">'
                + '<h3>#' + escapeHtml(r.id) + ' - ' + escapeHtml(r.title || 'Untitled report') + '</h3>'
                + '<p><strong>Severity:</strong> ' + escapeHtml(r.severity || 'medium') + ' · '
                + '<strong>Area:</strong> ' + escapeHtml(r.area_type || '') + ' · '
                + '<strong>Module:</strong> ' + escapeHtml(r.module_name || '') + '</p>'
                + '<p><strong>Page:</strong> ' + escapeHtml(r.page_key || '') + '</p>'
                + '<p><strong>URL:</strong> ' + escapeHtml(r.page_url || '') + '</p>'
                + '</div>'
                + '<div class="wi-bug-status-bar">'
                + '<label>Status</label>'
                + '<select id="wiBugStatusUpdate">'
                + '<option value="open">Open</option>'
                + '<option value="in_progress">In Progress</option>'
                + '<option value="resolved">Resolved</option>'
                + '<option value="closed">Closed</option>'
                + '</select>'
                + '</div>'
                + '<div class="wi-bug-messages">';

            messages.forEach(function (m) {
                html += messageHtml(m);
            });

            html += ''
                + '</div>'
                + '<textarea id="wiBugReply" placeholder="Reply or add an internal note..."></textarea>'
                + '<button type="button" id="wiBugReplyBtn">Send reply</button>';

            thread.innerHTML = html;

            const statusSelect = document.getElementById('wiBugStatusUpdate');
            const replyButton = document.getElementById('wiBugReplyBtn');

            if (statusSelect) {
                statusSelect.value = r.status || 'open';

                statusSelect.addEventListener('change', function () {
                    ajax('wi_bug_status', {
                        report_id: id,
                        status: this.value
                    }, function () {
                        loadList(document.getElementById('wiBugStatusFilter') ? document.getElementById('wiBugStatusFilter').value : '');
                        loadThread(id);
                    });
                });
            }

            if (replyButton) {
                replyButton.addEventListener('click', function () {
                    const input = document.getElementById('wiBugReply');
                    const message = input ? input.value : '';

                    ajax('wi_bug_reply', {
                        report_id: id,
                        message: message
                    }, function () {
                        loadThread(id);
                    });
                });
            }
        });
    }

    function bootWidget() {
        const roots = document.querySelectorAll('.wi-bug-reporter-root');

        roots.forEach(function (root) {
            initBugReporter(root);
        });
    }

    function bootAdmin() {
        const filter = document.getElementById('wiBugStatusFilter');

        document.addEventListener('click', function (event) {
            const item = event.target.closest('.wi-bug-item');

            if (!item) {
                return;
            }

            loadThread(item.dataset.id);
        });

        if (filter) {
            filter.addEventListener('change', function () {
                loadList(this.value);
            });
        }

        if (document.getElementById('wiBugList')) {
            loadList('');
        }
    }

    function boot() {
        bootWidget();
        bootAdmin();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', boot);
    } else {
        boot();
    }
})();
