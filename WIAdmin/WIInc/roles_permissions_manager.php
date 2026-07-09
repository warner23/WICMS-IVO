<?php
/*
|--------------------------------------------------------------------------
| WICMS Core Roles & Permissions Manager
|--------------------------------------------------------------------------
| Shared modern manager for:
| - /WIAdmin/WIRoles.php
| - /WIAdmin/WIPermissions.php
|
| Core WICMS only. No Compliance logic belongs here.
|--------------------------------------------------------------------------
*/

if (!class_exists('WIRolesPermissionsService')) {
    require_once __DIR__ . '/../WICore/WIClass/WIRolesPermissionsService.php';
}

$esc = $esc ?? static fn(mixed $value): string => htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$initialTab = isset($wiRolesPermissionsInitialTab) && $wiRolesPermissionsInitialTab === 'permissions' ? 'permissions' : 'roles';
$service = new WIRolesPermissionsService();
$dashboard = $service->dashboard();
$roles = $dashboard['roles'] ?? [];
$permissions = $dashboard['permissions'] ?? [];
$groups = $dashboard['groups'] ?? [];
$stats = $dashboard['stats'] ?? ['roles' => 0, 'permissions' => 0, 'groups' => 0, 'links' => 0];
$permissionGroups = $service->permissionGroups();
$csrfToken = class_exists('WIToken') ? WIToken::getToken('wicms_roles_permissions_manager') : '';
?>

<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-settings.css">
<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-roles-permissions.css">

<aside class="right-side">
    <div class="wi-settings-page wi-rp-page" data-wicms-rp-manager data-initial-tab="<?php echo $esc($initialTab); ?>">
        <input type="hidden" value="<?php echo $esc($csrfToken); ?>" data-rp-csrf>

        <section class="wi-settings-hero">
            <div>
                <p class="wi-settings-kicker">WICMS Core</p>
                <h1>Roles & Permissions</h1>
                <p>Manage core WICMS user roles, permission definitions and role assignments in one clean workspace.</p>
            </div>
            <div class="wi-settings-hero-badge">
                <span>Core Security</span>
                <strong>Access Manager</strong>
            </div>
        </section>

        <section class="wi-rp-stats" aria-label="Roles and permissions summary">
            <article>
                <span>Roles</span>
                <strong data-rp-stat="roles"><?php echo (int)($stats['roles'] ?? 0); ?></strong>
            </article>
            <article>
                <span>Permissions</span>
                <strong data-rp-stat="permissions"><?php echo (int)($stats['permissions'] ?? 0); ?></strong>
            </article>
            <article>
                <span>Groups</span>
                <strong data-rp-stat="groups"><?php echo (int)($stats['groups'] ?? 0); ?></strong>
            </article>
            <article>
                <span>Assignments</span>
                <strong data-rp-stat="links"><?php echo (int)($stats['links'] ?? 0); ?></strong>
            </article>
        </section>

        <section class="wi-settings-shell wi-rp-shell">
            <nav class="wi-settings-tabs wi-rp-tabs" aria-label="Roles and permissions tabs">
                <button type="button" class="wi-settings-tab" data-rp-tab="roles">
                    <span class="wi-settings-tab-icon">👥</span>
                    <span>Roles</span>
                </button>
                <button type="button" class="wi-settings-tab" data-rp-tab="permissions">
                    <span class="wi-settings-tab-icon">🔐</span>
                    <span>Permissions</span>
                </button>
                <button type="button" class="wi-settings-tab" data-rp-tab="assignments">
                    <span class="wi-settings-tab-icon">✅</span>
                    <span>Assignments</span>
                </button>
                <button type="button" class="wi-settings-tab" data-rp-tab="health">
                    <span class="wi-settings-tab-icon">🧪</span>
                    <span>Health</span>
                </button>
            </nav>

            <main class="wi-settings-main wi-rp-main">
                <div class="wi-rp-feedback" data-rp-feedback hidden></div>

                <section class="wi-settings-panel-view" data-rp-panel="roles">
                    <div class="wi-rp-panel-head">
                        <div>
                            <h2>Role Manager</h2>
                            <p>Create and update WICMS roles. Core/system roles are protected from accidental deletion.</p>
                        </div>
                        <button type="button" class="wi-rp-primary" data-rp-role-new>+ Add Role</button>
                    </div>

                    <div class="wi-rp-grid">
                        <section class="wi-rp-list-card">
                            <div class="wi-rp-list-head">
                                <h3>Roles</h3>
                                <label>
                                    <span>Search</span>
                                    <input type="search" data-rp-role-search placeholder="Search roles">
                                </label>
                            </div>

                            <ul class="wi-rp-list wi-rp-role-list" data-rp-role-list>
                                <?php foreach ($roles as $role): ?>
                                    <li class="wi-rp-item" data-role-id="<?php echo (int)($role['role_id'] ?? 0); ?>" data-role-name="<?php echo $esc($role['role'] ?? ''); ?>">
                                        <div class="wi-rp-item-main">
                                            <strong><?php echo $esc($role['role'] ?? ''); ?></strong>
                                            <span><?php echo (int)($role['users_count'] ?? 0); ?> users · <?php echo (int)($role['permissions_count'] ?? 0); ?> permissions</span>
                                        </div>
                                        <div class="wi-rp-badges">
                                            <span class="wi-rp-badge <?php echo !empty($role['is_protected']) ? 'is-system' : 'is-custom'; ?>"><?php echo $esc($role['label'] ?? 'Role'); ?></span>
                                        </div>
                                        <div class="wi-rp-actions">
                                            <button type="button" data-rp-role-edit="<?php echo (int)($role['role_id'] ?? 0); ?>">Edit</button>
                                            <button type="button" data-rp-role-permissions="<?php echo (int)($role['role_id'] ?? 0); ?>">Permissions</button>
                                            <?php if (empty($role['is_protected']) && (int)($role['users_count'] ?? 0) === 0): ?>
                                                <button type="button" class="is-danger" data-rp-role-delete="<?php echo (int)($role['role_id'] ?? 0); ?>">Delete</button>
                                            <?php endif; ?>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </section>

                        <section class="wi-rp-form-card">
                            <div class="wi-rp-form-head">
                                <h3 data-rp-role-form-title>Add Role</h3>
                                <p>Use clear names such as “Editor”, “Manager” or “Support Staff”.</p>
                            </div>

                            <form data-rp-role-form autocomplete="off">
                                <input type="hidden" name="role_id" value="0">
                                <label>
                                    <span>Role Name</span>
                                    <input type="text" name="role" maxlength="50" required placeholder="Manager">
                                </label>
                                <div class="wi-rp-form-actions">
                                    <button type="submit" class="wi-rp-primary">Save Role</button>
                                    <button type="button" class="wi-rp-secondary" data-rp-role-reset>Reset</button>
                                </div>
                            </form>
                        </section>
                    </div>
                </section>

                <section class="wi-settings-panel-view" data-rp-panel="permissions" hidden>
                    <div class="wi-rp-panel-head">
                        <div>
                            <h2>Permission Manager</h2>
                            <p>Create permission definitions using clear codes like <code>users.view</code> or <code>settings.edit</code>.</p>
                        </div>
                        <button type="button" class="wi-rp-primary" data-rp-permission-new>+ Add Permission</button>
                    </div>

                    <form class="wi-rp-filters" data-rp-permission-filters autocomplete="off">
                        <label>
                            <span>Search</span>
                            <input type="search" name="search" placeholder="Name, code or description">
                        </label>
                        <label>
                            <span>Group</span>
                            <select name="group">
                                <option value="">All groups</option>
                                <?php foreach ($permissionGroups as $groupName): ?>
                                    <option value="<?php echo $esc($groupName); ?>"><?php echo $esc($groupName); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <label>
                            <span>Status</span>
                            <select name="status">
                                <option value="all">All</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </label>
                        <button type="submit" class="wi-rp-secondary">Apply</button>
                    </form>

                    <div class="wi-rp-grid">
                        <section class="wi-rp-list-card">
                            <div class="wi-rp-list-head">
                                <h3>Permissions</h3>
                                <p><span data-rp-permission-count><?php echo count($permissions); ?></span> records</p>
                            </div>
                            <ul class="wi-rp-list wi-rp-permission-list" data-rp-permission-list>
                                <?php foreach ($groups as $groupName => $groupPermissions): ?>
                                    <li class="wi-rp-group-label"><?php echo $esc($groupName); ?></li>
                                    <?php foreach ($groupPermissions as $permission): ?>
                                        <li class="wi-rp-item" data-permission-id="<?php echo (int)($permission['id'] ?? 0); ?>">
                                            <div class="wi-rp-item-main">
                                                <strong><?php echo $esc($permission['name'] ?? ''); ?></strong>
                                                <span><code><?php echo $esc($permission['code'] ?? ''); ?></code> · <?php echo $esc($permission['group_name'] ?? 'General'); ?></span>
                                                <?php if (!empty($permission['description'])): ?>
                                                    <small><?php echo $esc($permission['description']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="wi-rp-badges">
                                                <span class="wi-rp-badge <?php echo (int)($permission['is_active'] ?? 0) === 1 ? 'is-active' : 'is-muted'; ?>"><?php echo (int)($permission['is_active'] ?? 0) === 1 ? 'Active' : 'Inactive'; ?></span>
                                                <span class="wi-rp-badge is-custom"><?php echo (int)($permission['linked_roles_count'] ?? 0); ?> roles</span>
                                            </div>
                                            <div class="wi-rp-actions">
                                                <button type="button" data-rp-permission-edit="<?php echo (int)($permission['id'] ?? 0); ?>">Edit</button>
                                                <?php if ((int)($permission['linked_roles_count'] ?? 0) === 0): ?>
                                                    <button type="button" class="is-danger" data-rp-permission-delete="<?php echo (int)($permission['id'] ?? 0); ?>">Delete</button>
                                                <?php endif; ?>
                                            </div>
                                        </li>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </ul>
                        </section>

                        <section class="wi-rp-form-card">
                            <div class="wi-rp-form-head">
                                <h3 data-rp-permission-form-title>Add Permission</h3>
                                <p>Permission codes must be lowercase and machine-readable.</p>
                            </div>

                            <form data-rp-permission-form autocomplete="off">
                                <input type="hidden" name="id" value="0">
                                <label>
                                    <span>Name</span>
                                    <input type="text" name="name" maxlength="100" required placeholder="View Users">
                                </label>
                                <label>
                                    <span>Code</span>
                                    <input type="text" name="code" maxlength="150" required placeholder="users.view">
                                </label>
                                <label>
                                    <span>Group</span>
                                    <input type="text" name="group_name" maxlength="100" value="General" list="wi-rp-known-groups">
                                </label>
                                <datalist id="wi-rp-known-groups">
                                    <?php foreach ($permissionGroups as $groupName): ?>
                                        <option value="<?php echo $esc($groupName); ?>"></option>
                                    <?php endforeach; ?>
                                </datalist>
                                <label>
                                    <span>Description</span>
                                    <textarea name="description" maxlength="255" rows="4" placeholder="What does this permission allow?"></textarea>
                                </label>
                                <label class="wi-rp-switch-row">
                                    <span>
                                        <strong>Active</strong>
                                        <small>Inactive permissions stay in the database but should not be offered to roles.</small>
                                    </span>
                                    <input type="checkbox" name="is_active" value="1" checked>
                                </label>
                                <div class="wi-rp-form-actions">
                                    <button type="submit" class="wi-rp-primary">Save Permission</button>
                                    <button type="button" class="wi-rp-secondary" data-rp-permission-reset>Reset</button>
                                </div>
                            </form>
                        </section>
                    </div>
                </section>

                <section class="wi-settings-panel-view" data-rp-panel="assignments" hidden>
                    <div class="wi-rp-panel-head">
                        <div>
                            <h2>Role Permission Assignments</h2>
                            <p>Select a role, then switch permissions on or off using grouped list cards.</p>
                        </div>
                    </div>

                    <div class="wi-rp-assignment-card">
                        <label class="wi-rp-role-picker">
                            <span>Role</span>
                            <select data-rp-assignment-role>
                                <option value="0">Choose role</option>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo (int)($role['role_id'] ?? 0); ?>"><?php echo $esc($role['role'] ?? ''); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <div class="wi-rp-permission-groups" data-rp-assignment-groups>
                            <?php foreach ($groups as $groupName => $groupPermissions): ?>
                                <section class="wi-rp-permission-group">
                                    <h3><?php echo $esc($groupName); ?></h3>
                                    <ul>
                                        <?php foreach ($groupPermissions as $permission): ?>
                                            <li>
                                                <label class="wi-rp-switch-row">
                                                    <span>
                                                        <strong><?php echo $esc($permission['name'] ?? ''); ?></strong>
                                                        <small><code><?php echo $esc($permission['code'] ?? ''); ?></code></small>
                                                    </span>
                                                    <input type="checkbox" value="<?php echo (int)($permission['id'] ?? 0); ?>" data-rp-assignment-permission>
                                                </label>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </section>
                            <?php endforeach; ?>
                        </div>

                        <div class="wi-rp-form-actions">
                            <button type="button" class="wi-rp-primary" data-rp-save-assignments>Save Role Permissions</button>
                        </div>
                    </div>
                </section>

                <section class="wi-settings-panel-view" data-rp-panel="health" hidden>
                    <div class="wi-rp-panel-head">
                        <div>
                            <h2>Access Health Check</h2>
                            <p>A small safety view for core roles, permission definitions and assignment readiness.</p>
                        </div>
                    </div>

                    <ul class="wi-rp-health-list">
                        <li>
                            <strong>Roles table</strong>
                            <span><?php echo $service->listRoles() !== [] ? 'Available' : 'No roles found or table unavailable'; ?></span>
                        </li>
                        <li>
                            <strong>Permissions table</strong>
                            <span><?php echo $permissions !== [] ? 'Available' : 'No permissions found or table unavailable'; ?></span>
                        </li>
                        <li>
                            <strong>Role permission links</strong>
                            <span><?php echo (int)($stats['links'] ?? 0); ?> assignments</span>
                        </li>
                        <li>
                            <strong>Protected roles</strong>
                            <span>Guest, Member, VIP/Admin-level and platform roles are protected from direct deletion.</span>
                        </li>
                        <li>
                            <strong>UI cleanup</strong>
                            <span>Old table/modal route has been bypassed for this manager. Records now use list cards.</span>
                        </li>
                    </ul>
                </section>
            </main>
        </section>
    </div>

    <script type="application/json" id="wi-rp-initial-data"><?php echo json_encode($dashboard, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?></script>
    <script src="../WITheme/WICMS/admin/js/core-admin-roles-permissions.js"></script>
</aside>
