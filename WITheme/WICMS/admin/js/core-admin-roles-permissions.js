(function () {
    'use strict';

    var root = document.querySelector('[data-wicms-rp-manager]');
    if (!root) {
        return;
    }

    var endpoint = 'WICore/WIAjax/WIRolesPermissionsManager.php';
    var csrfInput = root.querySelector('[data-rp-csrf]');
    var feedback = root.querySelector('[data-rp-feedback]');
    var state = {
        roles: [],
        permissions: [],
        groups: {},
        rolePermissionMap: {},
        activeRoleId: 0
    };

    function readInitialData() {
        var node = document.getElementById('wi-rp-initial-data');
        if (!node) {
            return;
        }

        try {
            var data = JSON.parse(node.textContent || '{}');
            state.roles = data.roles || [];
            state.permissions = data.permissions || [];
            state.groups = data.groups || {};
            state.rolePermissionMap = data.role_permission_map || {};
        } catch (e) {
            state.roles = [];
            state.permissions = [];
            state.groups = {};
            state.rolePermissionMap = {};
        }
    }

    function esc(value) {
        return String(value === null || typeof value === 'undefined' ? '' : value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getCsrf() {
        return csrfInput ? csrfInput.value : '';
    }

    function setCsrf(token) {
        if (token && csrfInput) {
            csrfInput.value = token;
        }
    }

    function showFeedback(message, isError) {
        if (!feedback) {
            if (isError) {
                alert(message);
            }
            return;
        }

        feedback.textContent = message;
        feedback.hidden = false;
        feedback.classList.toggle('is-error', !!isError);

        window.setTimeout(function () {
            feedback.hidden = true;
        }, 4500);
    }

    function post(action, payload) {
        var form = new FormData();
        form.append('action', action);
        form.append('csrf_token', getCsrf());

        Object.keys(payload || {}).forEach(function (key) {
            var value = payload[key];

            if (Array.isArray(value)) {
                value.forEach(function (item) {
                    form.append(key + '[]', item);
                });
                return;
            }

            form.append(key, value);
        });

        return fetch(endpoint, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {'X-Requested-With': 'XMLHttpRequest'},
            body: form
        }).then(function (response) {
            return response.json().then(function (json) {
                setCsrf(json.csrf_token);

                if (!response.ok || json.status === 'error' || json.success === false) {
                    throw json;
                }

                return json;
            });
        });
    }

    function activateTab(name) {
        var selected = name || 'roles';

        root.querySelectorAll('[data-rp-tab]').forEach(function (button) {
            var active = button.getAttribute('data-rp-tab') === selected;
            button.classList.toggle('is-active', active);
            button.setAttribute('aria-selected', active ? 'true' : 'false');
        });

        root.querySelectorAll('[data-rp-panel]').forEach(function (panel) {
            panel.hidden = panel.getAttribute('data-rp-panel') !== selected;
        });
    }

    function refreshDashboard() {
        return post('wicms_roles_permissions_dashboard', {}).then(function (json) {
            var data = json.data || {};
            state.roles = data.roles || [];
            state.permissions = data.permissions || [];
            state.groups = data.groups || {};
            state.rolePermissionMap = data.role_permission_map || {};
            renderRoles();
            renderPermissions();
            renderRoleOptions();
            renderAssignments();
            updateStats(data.stats || {});
            applyAssignmentSelection();
        }).catch(function (err) {
            showFeedback(err.message || err.msg || 'Unable to refresh roles and permissions.', true);
        });
    }

    function updateStats(stats) {
        Object.keys(stats || {}).forEach(function (key) {
            var node = root.querySelector('[data-rp-stat="' + key + '"]');
            if (node) {
                node.textContent = String(stats[key]);
            }
        });
    }

    function renderRoles() {
        var list = root.querySelector('[data-rp-role-list]');
        if (!list) {
            return;
        }

        if (!state.roles.length) {
            list.innerHTML = '<li class="wi-rp-item"><div class="wi-rp-item-main"><strong>No roles found</strong><span>Create the first role using the form.</span></div></li>';
            return;
        }

        list.innerHTML = state.roles.map(function (role) {
            var protectedRole = !!role.is_protected;
            var users = parseInt(role.users_count || 0, 10);
            var deleteButton = (!protectedRole && users === 0)
                ? '<button type="button" class="is-danger" data-rp-role-delete="' + esc(role.role_id) + '">Delete</button>'
                : '';

            return '<li class="wi-rp-item" data-role-id="' + esc(role.role_id) + '" data-role-name="' + esc(role.role) + '">'
                + '<div class="wi-rp-item-main">'
                    + '<strong>' + esc(role.role) + '</strong>'
                    + '<span>' + esc(users) + ' users · ' + esc(role.permissions_count || 0) + ' permissions</span>'
                + '</div>'
                + '<div class="wi-rp-badges"><span class="wi-rp-badge ' + (protectedRole ? 'is-system' : 'is-custom') + '">' + esc(role.label || (protectedRole ? 'System/Core' : 'Custom')) + '</span></div>'
                + '<div class="wi-rp-actions">'
                    + '<button type="button" data-rp-role-edit="' + esc(role.role_id) + '">Edit</button>'
                    + '<button type="button" data-rp-role-permissions="' + esc(role.role_id) + '">Permissions</button>'
                    + deleteButton
                + '</div>'
            + '</li>';
        }).join('');

        filterRoles();
    }

    function renderPermissions() {
        var list = root.querySelector('[data-rp-permission-list]');
        var count = root.querySelector('[data-rp-permission-count]');
        if (!list) {
            return;
        }

        if (count) {
            count.textContent = String(state.permissions.length);
        }

        if (!state.permissions.length) {
            list.innerHTML = '<li class="wi-rp-item"><div class="wi-rp-item-main"><strong>No permissions found</strong><span>Create the first permission using the form.</span></div></li>';
            return;
        }

        var html = '';
        Object.keys(state.groups || {}).forEach(function (groupName) {
            html += '<li class="wi-rp-group-label">' + esc(groupName) + '</li>';
            (state.groups[groupName] || []).forEach(function (permission) {
                var linked = parseInt(permission.linked_roles_count || 0, 10);
                var deleteButton = linked === 0
                    ? '<button type="button" class="is-danger" data-rp-permission-delete="' + esc(permission.id) + '">Delete</button>'
                    : '';

                html += '<li class="wi-rp-item" data-permission-id="' + esc(permission.id) + '" data-permission-group="' + esc(permission.group_name || 'General') + '" data-permission-status="' + (parseInt(permission.is_active || 0, 10) === 1 ? 'active' : 'inactive') + '">'
                    + '<div class="wi-rp-item-main">'
                        + '<strong>' + esc(permission.name) + '</strong>'
                        + '<span><code>' + esc(permission.code) + '</code> · ' + esc(permission.group_name || 'General') + '</span>'
                        + (permission.description ? '<small>' + esc(permission.description) + '</small>' : '')
                    + '</div>'
                    + '<div class="wi-rp-badges">'
                        + '<span class="wi-rp-badge ' + (parseInt(permission.is_active || 0, 10) === 1 ? 'is-active' : 'is-muted') + '">' + (parseInt(permission.is_active || 0, 10) === 1 ? 'Active' : 'Inactive') + '</span>'
                        + '<span class="wi-rp-badge is-custom">' + esc(linked) + ' roles</span>'
                    + '</div>'
                    + '<div class="wi-rp-actions">'
                        + '<button type="button" data-rp-permission-edit="' + esc(permission.id) + '">Edit</button>'
                        + deleteButton
                    + '</div>'
                + '</li>';
            });
        });

        list.innerHTML = html;
        applyPermissionFilters();
    }

    function renderRoleOptions() {
        var select = root.querySelector('[data-rp-assignment-role]');
        if (!select) {
            return;
        }

        var selected = state.activeRoleId || parseInt(select.value || '0', 10);
        select.innerHTML = '<option value="0">Choose role</option>' + state.roles.map(function (role) {
            return '<option value="' + esc(role.role_id) + '">' + esc(role.role) + '</option>';
        }).join('');
        select.value = String(selected || 0);
    }

    function renderAssignments() {
        var container = root.querySelector('[data-rp-assignment-groups]');
        if (!container) {
            return;
        }

        var html = '';
        Object.keys(state.groups || {}).forEach(function (groupName) {
            html += '<section class="wi-rp-permission-group"><h3>' + esc(groupName) + '</h3><ul>';
            (state.groups[groupName] || []).forEach(function (permission) {
                html += '<li><label class="wi-rp-switch-row">'
                    + '<span><strong>' + esc(permission.name) + '</strong><small><code>' + esc(permission.code) + '</code></small></span>'
                    + '<input type="checkbox" value="' + esc(permission.id) + '" data-rp-assignment-permission>'
                + '</label></li>';
            });
            html += '</ul></section>';
        });

        container.innerHTML = html || '<p>No active permissions available.</p>';
    }

    function applyAssignmentSelection() {
        var roleId = state.activeRoleId || parseInt((root.querySelector('[data-rp-assignment-role]') || {}).value || '0', 10);
        var selectedMap = state.rolePermissionMap[String(roleId)] || state.rolePermissionMap[roleId] || {};

        root.querySelectorAll('[data-rp-assignment-permission]').forEach(function (checkbox) {
            var permissionId = checkbox.value;
            checkbox.checked = !!(selectedMap[String(permissionId)] || selectedMap[permissionId]);
            checkbox.disabled = roleId <= 0;
        });
    }

    function resetRoleForm() {
        var form = root.querySelector('[data-rp-role-form]');
        if (!form) {
            return;
        }
        form.reset();
        form.elements.role_id.value = '0';
        var title = root.querySelector('[data-rp-role-form-title]');
        if (title) {
            title.textContent = 'Add Role';
        }
    }

    function resetPermissionForm() {
        var form = root.querySelector('[data-rp-permission-form]');
        if (!form) {
            return;
        }
        form.reset();
        form.elements.id.value = '0';
        form.elements.group_name.value = 'General';
        form.elements.is_active.checked = true;
        var title = root.querySelector('[data-rp-permission-form-title]');
        if (title) {
            title.textContent = 'Add Permission';
        }
    }

    function editRole(roleId) {
        var role = state.roles.find(function (item) {
            return parseInt(item.role_id || 0, 10) === parseInt(roleId || 0, 10);
        });
        var form = root.querySelector('[data-rp-role-form]');
        if (!role || !form) {
            return;
        }

        form.elements.role_id.value = role.role_id;
        form.elements.role.value = role.role || '';
        var title = root.querySelector('[data-rp-role-form-title]');
        if (title) {
            title.textContent = 'Edit Role';
        }
    }

    function editPermission(permissionId) {
        var permission = state.permissions.find(function (item) {
            return parseInt(item.id || 0, 10) === parseInt(permissionId || 0, 10);
        });
        var form = root.querySelector('[data-rp-permission-form]');
        if (!permission || !form) {
            return;
        }

        form.elements.id.value = permission.id;
        form.elements.name.value = permission.name || '';
        form.elements.code.value = permission.code || '';
        form.elements.group_name.value = permission.group_name || 'General';
        form.elements.description.value = permission.description || '';
        form.elements.is_active.checked = parseInt(permission.is_active || 0, 10) === 1;
        var title = root.querySelector('[data-rp-permission-form-title]');
        if (title) {
            title.textContent = 'Edit Permission';
        }
    }

    function filterRoles() {
        var input = root.querySelector('[data-rp-role-search]');
        var q = input ? input.value.trim().toLowerCase() : '';

        root.querySelectorAll('[data-role-id]').forEach(function (row) {
            var name = (row.getAttribute('data-role-name') || '').toLowerCase();
            row.classList.toggle('is-hidden', q !== '' && name.indexOf(q) === -1);
        });
    }

    function applyPermissionFilters() {
        var form = root.querySelector('[data-rp-permission-filters]');
        if (!form) {
            return;
        }

        var search = (form.elements.search.value || '').trim().toLowerCase();
        var group = form.elements.group.value || '';
        var status = form.elements.status.value || 'all';

        root.querySelectorAll('[data-permission-id]').forEach(function (row) {
            var text = row.textContent.toLowerCase();
            var rowGroup = row.getAttribute('data-permission-group') || '';
            var rowStatus = row.getAttribute('data-permission-status') || 'active';
            var hide = false;

            if (search && text.indexOf(search) === -1) {
                hide = true;
            }
            if (group && rowGroup !== group) {
                hide = true;
            }
            if (status !== 'all' && rowStatus !== status) {
                hide = true;
            }

            row.classList.toggle('is-hidden', hide);
        });
    }

    function saveRole(form) {
        return post('wicms_role_save', {
            role_id: form.elements.role_id.value,
            role: form.elements.role.value
        }).then(function (json) {
            showFeedback(json.message || 'Role saved.', false);
            resetRoleForm();
            return refreshDashboard();
        }).catch(function (err) {
            showFeedback(err.message || err.msg || 'Unable to save role.', true);
        });
    }

    function savePermission(form) {
        return post('wicms_permission_save', {
            id: form.elements.id.value,
            name: form.elements.name.value,
            code: form.elements.code.value,
            group_name: form.elements.group_name.value,
            description: form.elements.description.value,
            is_active: form.elements.is_active.checked ? 1 : 0
        }).then(function (json) {
            showFeedback(json.message || 'Permission saved.', false);
            resetPermissionForm();
            return refreshDashboard();
        }).catch(function (err) {
            showFeedback(err.message || err.msg || 'Unable to save permission.', true);
        });
    }

    function deleteRole(roleId) {
        if (!window.confirm('Delete this role? This cannot be undone.')) {
            return;
        }

        post('wicms_role_delete', {role_id: roleId}).then(function (json) {
            showFeedback(json.message || 'Role deleted.', false);
            return refreshDashboard();
        }).catch(function (err) {
            showFeedback(err.message || err.msg || 'Unable to delete role.', true);
        });
    }

    function deletePermission(permissionId) {
        if (!window.confirm('Delete this permission? This cannot be undone.')) {
            return;
        }

        post('wicms_permission_delete', {id: permissionId}).then(function (json) {
            showFeedback(json.message || 'Permission deleted.', false);
            return refreshDashboard();
        }).catch(function (err) {
            showFeedback(err.message || err.msg || 'Unable to delete permission.', true);
        });
    }

    function selectRolePermissions(roleId) {
        state.activeRoleId = parseInt(roleId || 0, 10);
        var select = root.querySelector('[data-rp-assignment-role]');
        if (select) {
            select.value = String(state.activeRoleId);
        }
        activateTab('assignments');
        applyAssignmentSelection();
    }

    function saveAssignments() {
        var select = root.querySelector('[data-rp-assignment-role]');
        var roleId = select ? parseInt(select.value || '0', 10) : 0;

        if (!roleId) {
            showFeedback('Choose a role first.', true);
            return;
        }

        var permissionIds = [];
        root.querySelectorAll('[data-rp-assignment-permission]:checked').forEach(function (checkbox) {
            permissionIds.push(checkbox.value);
        });

        post('wicms_role_permissions_save', {
            role_id: roleId,
            permission_ids: permissionIds
        }).then(function (json) {
            showFeedback(json.message || 'Role permissions saved.', false);
            return refreshDashboard();
        }).catch(function (err) {
            showFeedback(err.message || err.msg || 'Unable to save role permissions.', true);
        });
    }

    root.addEventListener('click', function (event) {
        var tab = event.target.closest('[data-rp-tab]');
        if (tab) {
            activateTab(tab.getAttribute('data-rp-tab'));
            return;
        }

        var roleNew = event.target.closest('[data-rp-role-new]');
        if (roleNew) {
            resetRoleForm();
            return;
        }

        var roleReset = event.target.closest('[data-rp-role-reset]');
        if (roleReset) {
            resetRoleForm();
            return;
        }

        var permissionNew = event.target.closest('[data-rp-permission-new]');
        if (permissionNew) {
            resetPermissionForm();
            return;
        }

        var permissionReset = event.target.closest('[data-rp-permission-reset]');
        if (permissionReset) {
            resetPermissionForm();
            return;
        }

        var roleEdit = event.target.closest('[data-rp-role-edit]');
        if (roleEdit) {
            editRole(roleEdit.getAttribute('data-rp-role-edit'));
            return;
        }

        var rolePermissions = event.target.closest('[data-rp-role-permissions]');
        if (rolePermissions) {
            selectRolePermissions(rolePermissions.getAttribute('data-rp-role-permissions'));
            return;
        }

        var roleDelete = event.target.closest('[data-rp-role-delete]');
        if (roleDelete) {
            deleteRole(roleDelete.getAttribute('data-rp-role-delete'));
            return;
        }

        var permissionEdit = event.target.closest('[data-rp-permission-edit]');
        if (permissionEdit) {
            editPermission(permissionEdit.getAttribute('data-rp-permission-edit'));
            return;
        }

        var permissionDelete = event.target.closest('[data-rp-permission-delete]');
        if (permissionDelete) {
            deletePermission(permissionDelete.getAttribute('data-rp-permission-delete'));
            return;
        }

        if (event.target.closest('[data-rp-save-assignments]')) {
            saveAssignments();
        }
    });

    root.addEventListener('submit', function (event) {
        var roleForm = event.target.closest('[data-rp-role-form]');
        if (roleForm) {
            event.preventDefault();
            saveRole(roleForm);
            return;
        }

        var permissionForm = event.target.closest('[data-rp-permission-form]');
        if (permissionForm) {
            event.preventDefault();
            savePermission(permissionForm);
            return;
        }

        var permissionFilters = event.target.closest('[data-rp-permission-filters]');
        if (permissionFilters) {
            event.preventDefault();
            applyPermissionFilters();
        }
    });

    var roleSearch = root.querySelector('[data-rp-role-search]');
    if (roleSearch) {
        roleSearch.addEventListener('input', filterRoles);
    }

    var permissionFilters = root.querySelector('[data-rp-permission-filters]');
    if (permissionFilters) {
        permissionFilters.addEventListener('change', applyPermissionFilters);
        permissionFilters.addEventListener('input', applyPermissionFilters);
    }

    var assignmentRole = root.querySelector('[data-rp-assignment-role]');
    if (assignmentRole) {
        assignmentRole.addEventListener('change', function () {
            state.activeRoleId = parseInt(assignmentRole.value || '0', 10);
            applyAssignmentSelection();
        });
    }

    readInitialData();
    activateTab(root.getAttribute('data-initial-tab') || 'roles');
    applyAssignmentSelection();
})();
