<?php
$permissionsObj = new WIPermissions();
$roles = $permissionsObj->getRoles();
$groupedPermissions = $permissionsObj->getGroupedPermissions();
$rolePermissionMap = $permissionsObj->getRolePermissionMap();
?>

<aside class="right-side">
    <?php echo WIToken::csrfField('wi_ajax'); ?>

    <div class="wi-admin-header">
        <h2>Permissions</h2>
        <p class="text-muted">Create system permissions and assign them to roles.</p>
    </div>

    <section class="content wi-permissions-shell">
        <div class="wi-admin-panel">

            <div class="wi-section-head">
                <div>
                    <h3>Permission Manager</h3>
                    <p>Manage permission definitions and connect them to roles.</p>
                </div>

                <button type="button" class="btn btn-primary wi-btn-primary" onclick="WIPermissions.openCreateModal();">
                    Add Permission
                </button>
            </div>

            <div class="wi-permissions-table-wrap">
                <table class="table table-striped wi-permissions-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Group</th>
                            <th>Status</th>
                            <th>Description</th>
                            <th style="width: 140px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($groupedPermissions as $group => $permissions): ?>
                        <?php foreach ($permissions as $permission): ?>
                            <tr id="permission-row-<?php echo (int)$permission['id']; ?>">
                                <td><?php echo htmlspecialchars($permission['name']); ?></td>
                                <td><code><?php echo htmlspecialchars($permission['code']); ?></code></td>
                                <td><?php echo htmlspecialchars($permission['group_name']); ?></td>
                                <td>
                                    <?php if ((int)$permission['is_active'] === 1): ?>
                                        <span class="wi-badge wi-badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="wi-badge wi-badge-muted">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars((string)$permission['description']); ?></td>
                                <td>
                                    <button
                                        type="button"
                                        class="btn btn-default btn-sm"
                                        onclick='WIPermissions.openEditModal(<?php echo json_encode([
                                            "id" => (int)$permission["id"],
                                            "name" => $permission["name"],
                                            "code" => $permission["code"],
                                            "group_name" => $permission["group_name"],
                                            "description" => (string)$permission["description"],
                                            "is_active" => (int)$permission["is_active"]
                                        ], JSON_HEX_APOS | JSON_HEX_QUOT); ?>)'>
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        class="btn btn-danger btn-sm"
                                        onclick="WIPermissions.deletePermission(<?php echo (int)$permission['id']; ?>);">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="wi-role-matrix">
                <div class="wi-role-matrix-head">
                    <h3>Role Permission Matrix</h3>
                    <p>Tick permissions on or off for each role.</p>
                </div>

                <?php foreach ($groupedPermissions as $group => $permissions): ?>
                    <div class="wi-role-group">
                        <h4><?php echo htmlspecialchars($group); ?></h4>

                        <div class="table-responsive">
                            <table class="table table-bordered wi-role-matrix-table">
                                <thead>
                                    <tr>
                                        <th>Permission</th>
                                        <?php foreach ($roles as $role): ?>
                                            <th><?php echo htmlspecialchars($role['role']); ?></th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($permissions as $permission): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($permission['name']); ?></strong><br>
                                                <small><?php echo htmlspecialchars($permission['code']); ?></small>
                                            </td>

                                            <?php foreach ($roles as $role): ?>
                                                <?php
                                                $roleId = (int)$role['role_id'];
                                                $permissionId = (int)$permission['id'];
                                                $checked = !empty($rolePermissionMap[$roleId][$permissionId]);
                                                ?>
                                                <td class="text-center">
                                                    <input
                                                        type="checkbox"
                                                        class="wi-role-permission-toggle"
                                                        data-role-id="<?php echo $roleId; ?>"
                                                        data-permission-id="<?php echo $permissionId; ?>"
                                                        <?php echo $checked ? 'checked' : ''; ?>>
                                                </td>
                                            <?php endforeach; ?>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <div id="wi-permission-modal" class="wi-permission-modal hide">
        <div class="wi-permission-modal-dialog">
            <div class="wi-permission-modal-header">
                <h3 id="wi-permission-modal-title">Add Permission</h3>
                <button type="button" class="wi-modal-close" onclick="WIPermissions.closeModal();">&times;</button>
            </div>

            <div class="wi-permission-modal-body">
                <input type="hidden" id="permission-id" value="0">

                <div class="form-group">
                    <label for="permission-name">Name</label>
                    <input type="text" id="permission-name" class="form-control" placeholder="View Users">
                </div>

                <div class="form-group">
                    <label for="permission-code">Code</label>
                    <input type="text" id="permission-code" class="form-control" placeholder="users.view">
                </div>

                <div class="form-group">
                    <label for="permission-group">Group</label>
                    <input type="text" id="permission-group" class="form-control" placeholder="Users">
                </div>

                <div class="form-group">
                    <label for="permission-description">Description</label>
                    <textarea id="permission-description" class="form-control" rows="4" placeholder="Can view users list"></textarea>
                </div>

                <div class="form-group">
                    <label for="permission-active">Status</label>
                    <select id="permission-active" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>

                <div id="permission-results"></div>
            </div>

            <div class="wi-permission-modal-footer">
                <button type="button" class="btn btn-default" onclick="WIPermissions.closeModal();">Cancel</button>
                <button type="button" class="btn btn-primary" id="permission-save-btn" onclick="WIPermissions.savePermission();">Save Permission</button>
            </div>
        </div>
    </div>

    <script type="text/javascript" src="WICore/WIJ/WICore.js"></script>
    <script type="text/javascript" src="WICore/WIJ/WIPermissions.js"></script>
</aside>