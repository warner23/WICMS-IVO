/*
 * WICMS Core Pages Manager
 * Location: /WITheme/WICMS/admin/js/core-admin-pages.js
 */
(function () {
    'use strict';

    var boot = window.WICMS_PAGES_BOOTSTRAP || {};
    var pages = Array.isArray(boot.pages) ? boot.pages.slice() : [];
    var modules = Array.isArray(boot.modules) ? boot.modules.slice() : [];
    var endpoint = boot.endpoint || 'WICore/WIAjax/WIPagesManager.php';
    var csrfToken = boot.csrfToken || '';

    var state = {
        search: '',
        module: 'all',
        layout: 'all',
        page: 1,
        perPage: 10
    };

    var flags = ['panel', 'top_head', 'header', 'left_sidebar', 'right_sidebar', 'footer'];

    function qs(selector) {
        return document.querySelector(selector);
    }

    function qsa(selector) {
        return Array.prototype.slice.call(document.querySelectorAll(selector));
    }

    function escapeHtml(value) {
        return String(value === null || value === undefined ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function flagLabel(flag) {
        return flag.replace(/_/g, ' ').replace(/\b\w/g, function (char) {
            return char.toUpperCase();
        });
    }

    function getFlagId(flag) {
        return '#wicms-page-' + flag.replace(/_/g, '-');
    }

    function normaliseBool(value) {
        return String(value || '0') === '1';
    }

    function filteredPages() {
        var search = state.search.toLowerCase();
        return pages.filter(function (page) {
            var matchesSearch = !search ||
                String(page.name || '').toLowerCase().indexOf(search) !== -1 ||
                String(page.contents || '').toLowerCase().indexOf(search) !== -1 ||
                String(page.id || '').indexOf(search) !== -1;

            var matchesModule = state.module === 'all' || String(page.contents || '') === state.module;
            var matchesLayout = state.layout === 'all' || String(page[state.layout] || '0') === '1';

            return matchesSearch && matchesModule && matchesLayout;
        });
    }

    function renderPages() {
        var list = qs('#wicms-pages-list');
        var count = qs('[data-wicms-pages-count]');
        var pageLabel = qs('[data-wicms-pages-page-label]');
        if (!list) {
            return;
        }

        var data = filteredPages();
        var totalPages = Math.max(1, Math.ceil(data.length / state.perPage));
        if (state.page > totalPages) {
            state.page = totalPages;
        }

        var start = (state.page - 1) * state.perPage;
        var visible = data.slice(start, start + state.perPage);

        if (count) {
            count.textContent = data.length + ' records';
        }

        if (pageLabel) {
            pageLabel.textContent = 'Page ' + state.page + ' of ' + totalPages;
        }

        if (!visible.length) {
            list.innerHTML = '<li class="wi-pages-item"><strong>No pages found.</strong><p class="text-muted">Try changing the filters or add a new page.</p></li>';
            return;
        }

        list.innerHTML = visible.map(function (page) {
            var flagHtml = flags.map(function (flag) {
                var on = normaliseBool(page[flag]);
                return '<span class="wi-page-flag ' + (on ? 'is-on' : '') + '">' + escapeHtml(flagLabel(flag)) + ': ' + (on ? 'On' : 'Off') + '</span>';
            }).join('');

            return '' +
                '<li class="wi-pages-item" data-page-id="' + escapeHtml(page.id) + '">' +
                    '<div class="wi-pages-item-main">' +
                        '<div>' +
                            '<span class="wi-page-id">#' + escapeHtml(page.id) + '</span>' +
                            '<h4>' + escapeHtml(page.name) + '</h4>' +
                            '<code>' + escapeHtml(page.contents || 'notfound') + '</code>' +
                            '<div class="wi-page-flags">' + flagHtml + '</div>' +
                        '</div>' +
                        '<div class="wi-pages-actions">' +
                            '<button type="button" class="wi-btn wi-btn-soft" data-page-edit="' + escapeHtml(page.id) + '">Edit</button>' +
                            '<button type="button" class="wi-btn wi-btn-soft" data-page-assign="' + escapeHtml(page.id) + '">Assign</button>' +
                            '<button type="button" class="wi-btn wi-btn-danger" data-page-delete="' + escapeHtml(page.id) + '">Delete</button>' +
                        '</div>' +
                    '</div>' +
                '</li>';
        }).join('');
    }

    function getPage(id) {
        id = parseInt(id, 10);
        for (var i = 0; i < pages.length; i++) {
            if (parseInt(pages[i].id, 10) === id) {
                return pages[i];
            }
        }
        return null;
    }

    function setMessage(message, type) {
        var box = qs('#wicms-pages-message');
        if (!box) {
            return;
        }
        box.innerHTML = message ? '<div class="' + (type === 'error' ? 'is-error' : 'is-success') + '">' + escapeHtml(message) + '</div>' : '';
    }

    function resetForm() {
        var title = qs('[data-wicms-pages-editor-title]');
        if (title) {
            title.textContent = 'Add Page';
        }

        qs('#wicms-page-id').value = '0';
        qs('#wicms-page-name').value = '';
        qs('#wicms-page-name').readOnly = false;
        qs('#wicms-page-contents').value = modules.indexOf('notfound') !== -1 ? 'notfound' : '';

        var root = document.querySelector('input[name="wicms-page-destination"][value="root"]');
        if (root && !root.disabled) {
            root.checked = true;
        }

        flags.forEach(function (flag) {
            var el = qs(getFlagId(flag));
            if (el) {
                el.checked = flag === 'panel' || flag === 'footer';
            }
        });

        qs('#wicms-page-create-route').checked = true;
        qs('#wicms-page-create-defaults').checked = true;
        qs('#wicms-page-overwrite-route').checked = false;
        setMessage('', 'success');
    }

    function editPage(page) {
        if (!page) {
            return;
        }

        var title = qs('[data-wicms-pages-editor-title]');
        if (title) {
            title.textContent = 'Edit Page';
        }

        qs('#wicms-page-id').value = page.id || '0';
        qs('#wicms-page-name').value = page.name || '';
        qs('#wicms-page-name').readOnly = false;
        qs('#wicms-page-contents').value = page.contents || 'notfound';

        flags.forEach(function (flag) {
            var el = qs(getFlagId(flag));
            if (el) {
                el.checked = normaliseBool(page[flag]);
            }
        });

        qs('#wicms-page-create-route').checked = false;
        qs('#wicms-page-create-defaults').checked = false;
        qs('#wicms-page-overwrite-route').checked = false;
        setMessage('', 'success');

        var editor = qs('#wicms-page-editor-card');
        if (editor && editor.scrollIntoView) {
            editor.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function getPayload() {
        var checkedDestination = document.querySelector('input[name="wicms-page-destination"]:checked');
        var payload = new FormData();
        payload.append('action', 'wicms_page_save');
        payload.append('csrf_token', csrfToken);
        payload.append('id', qs('#wicms-page-id').value || '0');
        payload.append('name', qs('#wicms-page-name').value || '');
        payload.append('contents', qs('#wicms-page-contents').value || 'notfound');
        payload.append('destination', checkedDestination ? checkedDestination.value : 'root');
        payload.append('create_route', qs('#wicms-page-create-route').checked ? '1' : '0');
        payload.append('create_defaults', qs('#wicms-page-create-defaults').checked ? '1' : '0');
        payload.append('overwrite_route', qs('#wicms-page-overwrite-route').checked ? '1' : '0');

        flags.forEach(function (flag) {
            var el = qs(getFlagId(flag));
            payload.append(flag, el && el.checked ? '1' : '0');
        });

        return payload;
    }

    function refreshFromResponse(res) {
        if (res.csrf_token) {
            csrfToken = res.csrf_token;
            var token = qs('#wicms-pages-csrf-token');
            if (token) {
                token.value = csrfToken;
            }
        }
    }

    function post(payload) {
        return fetch(endpoint, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: payload
        }).then(function (response) {
            return response.json().then(function (json) {
                refreshFromResponse(json);
                if (!response.ok || json.status === 'error' || json.success === false) {
                    throw json;
                }
                return json;
            });
        });
    }

    function savePage(event) {
        event.preventDefault();
        var btn = qs('#wicms-page-save-btn');
        if (btn) {
            btn.disabled = true;
            btn.textContent = 'Saving...';
        }

        post(getPayload()).then(function (res) {
            setMessage(res.message || 'Page saved.', 'success');
            window.setTimeout(function () {
                window.location.reload();
            }, 450);
        }).catch(function (err) {
            setMessage(err.message || err.msg || 'Unable to save page.', 'error');
        }).finally(function () {
            if (btn) {
                btn.disabled = false;
                btn.textContent = 'Save Page';
            }
        });
    }

    function deletePage(id) {
        var page = getPage(id);
        if (!page || !window.confirm('Delete page record "' + page.name + '"? Physical route files will not be deleted.')) {
            return;
        }

        var payload = new FormData();
        payload.append('action', 'wicms_page_delete');
        payload.append('csrf_token', csrfToken);
        payload.append('id', id);

        post(payload).then(function () {
            pages = pages.filter(function (item) {
                return parseInt(item.id, 10) !== parseInt(id, 10);
            });
            renderPages();
            resetForm();
        }).catch(function (err) {
            setMessage(err.message || err.msg || 'Unable to delete page.', 'error');
        });
    }

    function assignModule(id) {
        var page = getPage(id);
        if (!page) {
            return;
        }

        editPage(page);
        var moduleInput = qs('#wicms-page-contents');
        if (moduleInput) {
            moduleInput.focus();
            moduleInput.select();
        }
        setMessage('Update the Contents Module field, then save.', 'success');
    }

    function bindEvents() {
        var search = qs('#wicms-pages-search');
        if (search) {
            search.addEventListener('input', function () {
                state.search = search.value || '';
                state.page = 1;
                renderPages();
            });
        }

        var moduleFilter = qs('#wicms-pages-module-filter');
        if (moduleFilter) {
            moduleFilter.addEventListener('change', function () {
                state.module = moduleFilter.value || 'all';
                state.page = 1;
                renderPages();
            });
        }

        var layoutFilter = qs('#wicms-pages-layout-filter');
        if (layoutFilter) {
            layoutFilter.addEventListener('change', function () {
                state.layout = layoutFilter.value || 'all';
                state.page = 1;
                renderPages();
            });
        }

        var perPage = qs('#wicms-pages-per-page');
        if (perPage) {
            perPage.addEventListener('change', function () {
                state.perPage = parseInt(perPage.value, 10) || 10;
                state.page = 1;
                renderPages();
            });
        }

        var prev = qs('[data-wicms-pages-prev]');
        if (prev) {
            prev.addEventListener('click', function () {
                state.page = Math.max(1, state.page - 1);
                renderPages();
            });
        }

        var next = qs('[data-wicms-pages-next]');
        if (next) {
            next.addEventListener('click', function () {
                var total = Math.max(1, Math.ceil(filteredPages().length / state.perPage));
                state.page = Math.min(total, state.page + 1);
                renderPages();
            });
        }

        qsa('[data-wicms-page-create], [data-wicms-page-reset]').forEach(function (button) {
            button.addEventListener('click', resetForm);
        });

        var form = qs('#wicms-page-form');
        if (form) {
            form.addEventListener('submit', savePage);
        }

        document.addEventListener('click', function (event) {
            var edit = event.target.closest('[data-page-edit]');
            if (edit) {
                editPage(getPage(edit.getAttribute('data-page-edit')));
                return;
            }

            var assign = event.target.closest('[data-page-assign]');
            if (assign) {
                assignModule(assign.getAttribute('data-page-assign'));
                return;
            }

            var del = event.target.closest('[data-page-delete]');
            if (del) {
                deletePage(del.getAttribute('data-page-delete'));
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindEvents();
        resetForm();
        renderPages();
    });
}());
