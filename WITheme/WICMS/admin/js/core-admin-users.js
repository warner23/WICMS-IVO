(function () {
    'use strict';

    var root = document.querySelector('[data-wicms-users-manager]');
    if (!root) {
        return;
    }

    var endpoint = 'WICore/WIAjax/WIUsersManager.php';
    var csrfInput = root.querySelector('[data-users-csrf]');
    var feedback = root.querySelector('[data-users-feedback]');
    var list = root.querySelector('[data-users-list]');
    var empty = root.querySelector('[data-users-empty]');
    var filters = root.querySelector('[data-users-filters]');
    var form = root.querySelector('[data-user-form]');
    var formTitle = root.querySelector('[data-user-form-title]');
    var totalNode = root.querySelector('[data-users-total]');
    var pageNode = root.querySelector('[data-users-page]');
    var countLabel = root.querySelector('[data-users-count-label]');
    var pagerLabel = root.querySelector('[data-users-pager-label]');
    var prevButton = root.querySelector('[data-users-prev]');
    var nextButton = root.querySelector('[data-users-next]');
    var newButton = root.querySelector('[data-user-new]');
    var resetButton = root.querySelector('[data-user-reset]');

    var state = {
        page: 1,
        pages: 1,
        perPage: 10,
        loading: false
    };

    function escapeHtml(value) {
        return String(value === null || typeof value === 'undefined' ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function setFeedback(message, isError) {
        if (!feedback) {
            return;
        }

        if (!message) {
            feedback.hidden = true;
            feedback.textContent = '';
            feedback.classList.remove('is-error');
            return;
        }

        feedback.hidden = false;
        feedback.textContent = message;
        feedback.classList.toggle('is-error', !!isError);
    }

    function updateCsrf(token) {
        if (csrfInput && token) {
            csrfInput.value = token;
        }
    }

    function buildPayload(action, extra) {
        var payload = new FormData();
        payload.append('action', action);
        payload.append('csrf_token', csrfInput ? csrfInput.value : '');

        Object.keys(extra || {}).forEach(function (key) {
            payload.append(key, extra[key]);
        });

        return payload;
    }

    function request(action, extra) {
        return fetch(endpoint, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: buildPayload(action, extra)
        }).then(function (response) {
            return response.json().catch(function () {
                return {
                    success: false,
                    status: 'error',
                    message: 'Invalid JSON response.'
                };
            }).then(function (json) {
                updateCsrf(json.csrf_token);
                if (!response.ok || json.success === false || json.status === 'error') {
                    throw json;
                }
                return json;
            });
        });
    }

    function filtersToObject() {
        var data = new FormData(filters);
        return {
            search: data.get('search') || '',
            role_id: data.get('role_id') || '0',
            status: data.get('status') || 'all',
            per_page: data.get('per_page') || '10',
            page: String(state.page)
        };
    }

    function userInitial(user) {
        var name = user.name || user.username || user.email || 'U';
        return String(name).charAt(0).toUpperCase();
    }

    function userStatusClass(user) {
        return user.banned === 'Y' ? 'is-danger' : 'is-success';
    }

    function renderUser(user) {
        var id = Number(user.user_id || user.id || 0);
        var name = user.name || user.username || '';
        var role = user.role_name || user.role || 'No role';
        var status = user.status_label || (user.banned === 'Y' ? 'Banned' : 'Active');

        return '' +
            '<li class="wi-user-row" data-user-id="' + id + '">' +
                '<div class="wi-user-avatar" aria-hidden="true">' + escapeHtml(userInitial(user)) + '</div>' +
                '<div class="wi-user-main">' +
                    '<strong>' + escapeHtml(name) + '</strong>' +
                    '<span>' + escapeHtml(user.email || '') + '</span>' +
                    '<small>@' + escapeHtml(user.username || '') + ' · ' + escapeHtml(role) + '</small>' +
                '</div>' +
                '<div class="wi-user-badges">' +
                    '<span class="wi-user-badge ' + userStatusClass(user) + '">' + escapeHtml(status) + '</span>' +
                '</div>' +
                '<div class="wi-user-actions">' +
                    '<button type="button" data-user-edit="' + id + '">Edit</button>' +
                    '<button type="button" data-user-delete="' + id + '" class="is-danger">Delete</button>' +
                '</div>' +
            '</li>';
    }

    function renderList(data) {
        var items = data.items || [];
        state.page = Number(data.page || 1);
        state.pages = Number(data.pages || 1);
        state.perPage = Number(data.per_page || 10);

        if (list) {
            list.innerHTML = items.map(renderUser).join('');
        }

        if (empty) {
            empty.hidden = items.length > 0;
        }

        if (totalNode) {
            totalNode.textContent = String(data.total || 0);
        }

        if (pageNode) {
            pageNode.textContent = String(state.page);
        }

        if (countLabel) {
            countLabel.textContent = String(data.total || 0) + ' records';
        }

        if (pagerLabel) {
            pagerLabel.textContent = 'Page ' + state.page + ' of ' + state.pages;
        }

        if (prevButton) {
            prevButton.disabled = state.page <= 1;
        }

        if (nextButton) {
            nextButton.disabled = state.page >= state.pages;
        }
    }

    function loadUsers() {
        if (state.loading) {
            return;
        }

        state.loading = true;
        setFeedback('', false);

        request('wicms_users_list', filtersToObject()).then(function (json) {
            renderList(json.data || {});
        }).catch(function (error) {
            setFeedback(error.message || 'Unable to load users.', true);
        }).finally(function () {
            state.loading = false;
        });
    }

    function resetForm() {
        if (!form) {
            return;
        }

        form.reset();
        form.elements.user_id.value = '0';
        form.elements.confirmed.checked = true;
        form.elements.banned.checked = false;
        if (formTitle) {
            formTitle.textContent = 'Add User';
        }
    }

    function fillForm(user) {
        resetForm();
        form.elements.user_id.value = user.user_id || user.id || '0';
        form.elements.email.value = user.email || '';
        form.elements.username.value = user.username || '';
        form.elements.first_name.value = user.first_name || '';
        form.elements.last_name.value = user.last_name || '';
        form.elements.phone.value = user.phone || '';
        form.elements.address.value = user.address || '';
        form.elements.role_id.value = String(user.role_id || '1');
        form.elements.confirmed.checked = user.confirmed !== 'N';
        form.elements.banned.checked = user.banned === 'Y';
        form.elements.password.value = '';

        if (formTitle) {
            formTitle.textContent = 'Edit User';
        }

        form.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    function editUser(userId) {
        request('wicms_user_get', { user_id: String(userId) }).then(function (json) {
            fillForm((json.data || {}).user || {});
        }).catch(function (error) {
            setFeedback(error.message || 'Unable to load user.', true);
        });
    }

    function saveUser(event) {
        event.preventDefault();

        var data = new FormData(form);
        var payload = {};
        data.forEach(function (value, key) {
            payload[key] = value;
        });

        payload.confirmed = form.elements.confirmed.checked ? 'Y' : 'N';
        payload.banned = form.elements.banned.checked ? 'Y' : 'N';

        request('wicms_user_save', payload).then(function (json) {
            setFeedback(json.message || 'User saved.', false);
            resetForm();
            loadUsers();
        }).catch(function (error) {
            setFeedback(error.message || 'Unable to save user.', true);
        });
    }

    function deleteUser(userId) {
        var confirmed = window.confirm('Delete this WICMS user? This removes the core account and core profile details.');
        if (!confirmed) {
            return;
        }

        request('wicms_user_delete', { user_id: String(userId) }).then(function (json) {
            setFeedback(json.message || 'User deleted.', false);
            loadUsers();
        }).catch(function (error) {
            setFeedback(error.message || 'Unable to delete user.', true);
        });
    }

    if (filters) {
        filters.addEventListener('submit', function (event) {
            event.preventDefault();
            state.page = 1;
            loadUsers();
        });
    }

    if (prevButton) {
        prevButton.addEventListener('click', function () {
            if (state.page > 1) {
                state.page -= 1;
                loadUsers();
            }
        });
    }

    if (nextButton) {
        nextButton.addEventListener('click', function () {
            if (state.page < state.pages) {
                state.page += 1;
                loadUsers();
            }
        });
    }

    if (newButton) {
        newButton.addEventListener('click', function () {
            resetForm();
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    }

    if (resetButton) {
        resetButton.addEventListener('click', resetForm);
    }

    if (form) {
        form.addEventListener('submit', saveUser);
    }

    if (list) {
        list.addEventListener('click', function (event) {
            var editButton = event.target.closest('[data-user-edit]');
            if (editButton) {
                editUser(editButton.getAttribute('data-user-edit'));
                return;
            }

            var deleteButton = event.target.closest('[data-user-delete]');
            if (deleteButton) {
                deleteUser(deleteButton.getAttribute('data-user-delete'));
            }
        });
    }

    // Refresh once on boot so the list is driven by the same production endpoint
    // as search/pagination/save/delete.
    loadUsers();
})();
