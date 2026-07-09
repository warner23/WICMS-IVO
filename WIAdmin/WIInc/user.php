<?php
/*
|--------------------------------------------------------------------------
| WICMS Core Users Manager
|--------------------------------------------------------------------------
| Active include for /WIAdmin/WIUser.php.
| Modernised during WICMS core cleanup. Core users only; no Compliance logic.
|--------------------------------------------------------------------------
*/

$esc = $esc ?? static fn(mixed $value): string => htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$userService = class_exists('WIUser') ? new WIUser() : null;
$roles = $userService instanceof WIUser ? $userService->getRoles() : [];
$initial = $userService instanceof WIUser ? $userService->searchUsers(['page' => 1, 'per_page' => 10]) : ['items' => [], 'total' => 0, 'page' => 1, 'pages' => 1, 'per_page' => 10];
$csrfToken = class_exists('WIToken') ? WIToken::getToken('wicms_users_manager') : '';
$currentAdminId = class_exists('WISession') ? (int)WISession::get('user_id', 0) : 0;
?>

<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-settings.css">
<link rel="stylesheet" href="../WITheme/WICMS/admin/css/core-admin-users.css">

<aside class="right-side">
    <div class="wi-settings-page wi-users-page" data-wicms-users-manager data-current-user-id="<?php echo (int)$currentAdminId; ?>">
        <section class="wi-settings-hero">
            <div>
                <p class="wi-settings-kicker">WICMS Core</p>
                <h1>Users</h1>
                <p>Manage WICMS users, roles, account status and core profile details from one clean admin workspace.</p>
            </div>
            <div class="wi-settings-hero-badge">
                <span>Core Module</span>
                <strong>User Manager</strong>
            </div>
        </section>

        <section class="wi-users-summary" aria-label="User summary">
            <article class="wi-users-stat-card">
                <span>Total users</span>
                <strong data-users-total><?php echo (int)($initial['total'] ?? 0); ?></strong>
            </article>
            <article class="wi-users-stat-card">
                <span>Current page</span>
                <strong data-users-page><?php echo (int)($initial['page'] ?? 1); ?></strong>
            </article>
            <article class="wi-users-stat-card">
                <span>Roles loaded</span>
                <strong><?php echo count($roles); ?></strong>
            </article>
        </section>

        <section class="wi-settings-panel-view wi-users-manager-panel">
            <div class="wi-users-toolbar">
                <div>
                    <h2>User Manager</h2>
                    <p>Search, filter, add, edit, ban/unban and safely delete WICMS users.</p>
                </div>
                <button type="button" class="wi-users-primary-btn" data-user-new>+ Add User</button>
            </div>

            <form class="wi-users-filters" data-users-filters autocomplete="off">
                <input type="hidden" name="csrf_token" value="<?php echo $esc($csrfToken); ?>" data-users-csrf>

                <label>
                    <span>Search</span>
                    <input type="search" name="search" placeholder="Name, username, email or phone">
                </label>

                <label>
                    <span>Role</span>
                    <select name="role_id">
                        <option value="0">All roles</option>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?php echo (int)($role['role_id'] ?? 0); ?>"><?php echo $esc($role['role'] ?? ''); ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>

                <label>
                    <span>Status</span>
                    <select name="status">
                        <option value="all">All users</option>
                        <option value="active">Active</option>
                        <option value="banned">Banned</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="unconfirmed">Unconfirmed</option>
                    </select>
                </label>

                <label>
                    <span>Show</span>
                    <select name="per_page">
                        <option value="5">5</option>
                        <option value="10" selected>10</option>
                        <option value="20">20</option>
                        <option value="50">50</option>
                    </select>
                </label>

                <button type="submit" class="wi-users-secondary-btn">Apply</button>
            </form>

            <div class="wi-users-feedback" data-users-feedback hidden></div>

            <div class="wi-users-content-grid">
                <section class="wi-users-list-card" aria-label="Users list">
                    <div class="wi-users-list-head">
                        <h3>Users</h3>
                        <p data-users-count-label><?php echo (int)($initial['total'] ?? 0); ?> records</p>
                    </div>

                    <ul class="wi-users-list" data-users-list>
                        <?php foreach (($initial['items'] ?? []) as $user): ?>
                            <li class="wi-user-row" data-user-id="<?php echo (int)($user['user_id'] ?? 0); ?>">
                                <div class="wi-user-avatar" aria-hidden="true"><?php echo $esc(mb_strtoupper(mb_substr((string)($user['name'] ?? $user['username'] ?? 'U'), 0, 1))); ?></div>
                                <div class="wi-user-main">
                                    <strong><?php echo $esc($user['name'] ?? $user['username'] ?? ''); ?></strong>
                                    <span><?php echo $esc($user['email'] ?? ''); ?></span>
                                    <small>@<?php echo $esc($user['username'] ?? ''); ?> · <?php echo $esc($user['role_name'] ?? $user['role'] ?? 'No role'); ?></small>
                                </div>
                                <div class="wi-user-badges">
                                    <span class="wi-user-badge <?php echo (($user['banned'] ?? 'N') === 'Y') ? 'is-danger' : 'is-success'; ?>"><?php echo $esc($user['status_label'] ?? 'Active'); ?></span>
                                </div>
                                <div class="wi-user-actions">
                                    <button type="button" data-user-edit="<?php echo (int)($user['user_id'] ?? 0); ?>">Edit</button>
                                    <button type="button" data-user-delete="<?php echo (int)($user['user_id'] ?? 0); ?>" class="is-danger">Delete</button>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="wi-users-empty" data-users-empty <?php echo (($initial['items'] ?? []) === []) ? '' : 'hidden'; ?>>
                        No users found for the current filters.
                    </div>

                    <nav class="wi-users-pager" aria-label="Users pagination">
                        <button type="button" data-users-prev>Previous</button>
                        <span data-users-pager-label>Page <?php echo (int)($initial['page'] ?? 1); ?> of <?php echo (int)($initial['pages'] ?? 1); ?></span>
                        <button type="button" data-users-next>Next</button>
                    </nav>
                </section>

                <section class="wi-users-form-card" data-user-form-card aria-label="Add or edit user">
                    <div class="wi-users-form-head">
                        <h3 data-user-form-title>Add User</h3>
                        <p>Core account details only. Compliance access is managed separately in Compliance/Access.</p>
                    </div>

                    <form data-user-form autocomplete="off">
                        <input type="hidden" name="user_id" value="0">

                        <label>
                            <span>Email</span>
                            <input type="email" name="email" maxlength="254" required>
                        </label>

                        <label>
                            <span>Username</span>
                            <input type="text" name="username" maxlength="250" placeholder="Defaults to email if blank">
                        </label>

                        <div class="wi-users-two-col">
                            <label>
                                <span>First name</span>
                                <input type="text" name="first_name" maxlength="35">
                            </label>
                            <label>
                                <span>Last name</span>
                                <input type="text" name="last_name" maxlength="35">
                            </label>
                        </div>

                        <label>
                            <span>Phone</span>
                            <input type="text" name="phone" maxlength="30">
                        </label>

                        <label>
                            <span>Address</span>
                            <input type="text" name="address" maxlength="100">
                        </label>

                        <label>
                            <span>Role</span>
                            <select name="role_id" required>
                                <?php foreach ($roles as $role): ?>
                                    <option value="<?php echo (int)($role['role_id'] ?? 0); ?>"><?php echo $esc($role['role'] ?? ''); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <div class="wi-users-switch-grid">
                            <label class="wi-users-switch-row">
                                <input type="checkbox" name="confirmed" value="Y" checked>
                                <span class="wi-users-switch-ui"></span>
                                <span>Confirmed</span>
                            </label>

                            <label class="wi-users-switch-row">
                                <input type="checkbox" name="banned" value="Y">
                                <span class="wi-users-switch-ui"></span>
                                <span>Banned</span>
                            </label>
                        </div>

                        <label>
                            <span>Password</span>
                            <input type="password" name="password" minlength="8" autocomplete="new-password" placeholder="Required for new users; leave blank when editing">
                        </label>

                        <div class="wi-users-form-actions">
                            <button type="button" class="wi-users-secondary-btn" data-user-reset>Reset</button>
                            <button type="submit" class="wi-users-primary-btn">Save User</button>
                        </div>
                    </form>
                </section>
            </div>
        </section>
    </div>
</aside>

<script type="application/json" id="wicms-user-roles-json"><?php echo json_encode($roles, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?></script>
<script type="text/javascript" src="../WITheme/WICMS/admin/js/core-admin-users.js"></script>
