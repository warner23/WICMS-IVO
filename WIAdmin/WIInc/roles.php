<?php
$roleObj = new WIRole();
$roles = $roleObj->getRoles();
$groupedPermissions = $roleObj->getGroupedPermissions();
?>

<aside class="right-side">
    <?php echo WIToken::csrfField('wi_ajax'); ?>

    <div class="wi-admin-header">
        <h2>Roles</h2>
        <p class="text-muted">Create, update and manage roles for users and permissions.</p>
    </div>

    <section class="content wi-roles-shell">
        <div class="wi-admin-panel">

            <div class="wi-section-head">
                <div>
                    <h3>Role Manager</h3>
                    <p>Manage user roles and assign permissions to them.</p>
                </div>

                <button type="button" class="btn btn-primary wi-btn-primary" onclick="WIRoles.openCreateModal();">
                    Add Role
                </button>
            </div>

            <div class="wi-roles-table-wrap">
                <table class="table table-striped wi-roles-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Role Name</th>
                            <th>Users</th>
                            <th>Permissions</th>
                            <th style="width: 260px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($roles as $role): ?>
                        <tr id="role-row-<?php echo (int)$role['role_id']; ?>">
                            <td><?php echo (int)$role['role_id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($role['role']); ?></strong>
                                <?php if (in_array((int)$role['role_id'], [1,2,3], true)): ?>
                                    <span class="wi-badge wi-badge-muted">System</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo (int)$role['users_count']; ?></td>
                            <td><?php echo (int)$role['permissions_count']; ?></td>
                            <td>
                                <button
                                    type="button"
                                    class="btn btn-default btn-sm"
                                    onclick='WIRoles.openEditModal(<?php echo json_encode([
                                        "role_id" => (int)$role["role_id"],
                                        "role" => $role["role"]
                                    ], JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                    Edit
                                </button>

                                <button
                                    type="button"
                                    class="btn btn-info btn-sm"
                                    onclick="WIRoles.openPermissionsModal(<?php echo (int)$role['role_id']; ?>, <?php echo json_encode($role['role'], JSON_HEX_APOS | JSON_HEX_QUOT); ?>);">
                                    Permissions
                                </button>

                                <?php if (!in_array((int)$role['role_id'], [1,2,3], true)): ?>
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="WIRoles.deleteRole(<?php echo (int)$role['role_id']; ?>);">
                                        Delete
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </section>

    <div id="wi-role-modal" class="wi-role-modal hide">
        <div class="wi-role-modal-dialog">
            <div class="wi-role-modal-header">
                <h3 id="wi-role-modal-title">Add Role</h3>
                <button type="button" class="wi-modal-close" onclick="WIRoles.closeModal();">&times;</button>
            </div>

            <div class="wi-role-modal-body">
                <input type="hidden" id="role-id" value="0">

                <div class="form-group">
                    <label for="role-name">Role Name</label>
                    <input type="text" id="role-name" class="form-control" placeholder="Manager">
                </div>

                <div id="role-results"></div>
            </div>

            <div class="wi-role-modal-footer">
                <button type="button" class="btn btn-default" onclick="WIRoles.closeModal();">Cancel</button>
                <button type="button" class="btn btn-primary" id="role-save-btn" onclick="WIRoles.saveRole();">Save Role</button>
            </div>
        </div>
    </div>

    <div id="wi-role-permissions-modal" class="wi-role-modal hide">
        <div class="wi-role-modal-dialog wi-role-permissions-dialog">
            <div class="wi-role-modal-header">
                <h3 id="wi-role-permissions-title">Role Permissions</h3>
                <button type="button" class="wi-modal-close" onclick="WIRoles.closePermissionsModal();">&times;</button>
            </div>

            <div class="wi-role-modal-body">
                <input type="hidden" id="role-permissions-role-id" value="0">

                <div class="wi-role-permissions-groups">
                    <?php foreach ($groupedPermissions as $group => $permissions): ?>
                        <div class="wi-role-group">
                            <h4><?php echo htmlspecialchars((string)$group, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?></h4>

                            <div class="wi-permission-checklist">
                                <?php foreach ($permissions as $permission): ?>
                                    <label class="wi-permission-item">
                                        <input
                                            type="checkbox"
                                            class="role-permission-checkbox"
                                            value="<?php echo (int)$permission['id']; ?>">
                                        <span>
                                            <strong><?php echo htmlspecialchars($permission['name']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($permission['code']); ?></small>
                                        </span>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div id="role-permissions-results"></div>
            </div>

            <div class="wi-role-modal-footer">
                <button type="button" class="btn btn-default" onclick="WIRoles.closePermissionsModal();">Cancel</button>
                <button type="button" class="btn btn-primary" id="role-permissions-save-btn" onclick="WIRoles.saveRolePermissions();">Save Permissions</button>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIRoles.js"></script>
</aside>