<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Core Roles & Permissions Service
|--------------------------------------------------------------------------
| Core WICMS only. No Compliance ownership logic belongs here.
|
| Purpose:
| - Manage WICMS roles.
| - Manage WICMS permission definitions.
| - Assign permissions to roles.
| - Keep system/admin roles protected from accidental damage.
|--------------------------------------------------------------------------
*/

class WIRolesPermissionsService
{
    private WIdb $db;

    /** @var int[] */
    private array $protectedRoleIds = [1, 2, 3, 8, 9, 90, 91];

    /** @var string[] */
    private array $protectedRoleNames = [
        'guest',
        'member',
        'vip member',
        'administrator',
        'head administrator',
        'super admin',
        'platform owner',
        'developer',
    ];

    public function __construct(?WIdb $db = null)
    {
        $this->db = $db ?? WIdb::getInstance();
    }

    /**
     * @return array<string, mixed>
     */
    public function dashboard(): array
    {
        $roles = $this->listRoles();
        $permissions = $this->listPermissions();
        $groups = $this->groupPermissions($permissions);
        $map = $this->rolePermissionMap();

        return [
            'roles' => $roles,
            'permissions' => $permissions,
            'groups' => $groups,
            'role_permission_map' => $map,
            'stats' => [
                'roles' => count($roles),
                'permissions' => count($permissions),
                'groups' => count($groups),
                'links' => $this->countRolePermissionLinks(),
            ],
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listRoles(): array
    {
        if (!$this->db->tableExists('wi_user_roles')) {
            return [];
        }

        $hasMembers = $this->db->tableExists('wi_members');
        $hasRolePermissions = $this->db->tableExists('wi_role_permissions');

        $sql = "SELECT r.`role_id`, r.`role`";

        if ($hasMembers) {
            $sql .= ",
                (SELECT COUNT(*) FROM `wi_members` m WHERE m.`user_role` = r.`role_id`) AS users_count";
        } else {
            $sql .= ", 0 AS users_count";
        }

        if ($hasRolePermissions) {
            $sql .= ",
                (SELECT COUNT(*) FROM `wi_role_permissions` rp WHERE rp.`role_id` = r.`role_id`) AS permissions_count";
        } else {
            $sql .= ", 0 AS permissions_count";
        }

        $sql .= " FROM `wi_user_roles` r ORDER BY r.`role` ASC";

        $rows = $this->db->bindfree($sql);

        foreach ($rows as &$row) {
            $row['role_id'] = (int)($row['role_id'] ?? 0);
            $row['users_count'] = (int)($row['users_count'] ?? 0);
            $row['permissions_count'] = (int)($row['permissions_count'] ?? 0);
            $row['is_protected'] = $this->isProtectedRole((int)$row['role_id'], (string)($row['role'] ?? ''));
            $row['label'] = $row['is_protected'] ? 'System/Core' : 'Custom';
        }
        unset($row);

        return $rows;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function listPermissions(array $filters = []): array
    {
        if (!$this->db->tableExists('wi_permissions')) {
            return [];
        }

        $search = trim((string)($filters['search'] ?? ''));
        $group = trim((string)($filters['group'] ?? ''));
        $status = trim((string)($filters['status'] ?? 'all'));

        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = "(`name` LIKE :search OR `code` LIKE :search OR `description` LIKE :search)";
            $params['search'] = '%' . $search . '%';
        }

        if ($group !== '') {
            $where[] = "`group_name` = :group_name";
            $params['group_name'] = $group;
        }

        if ($status === 'active') {
            $where[] = "`is_active` = 1";
        } elseif ($status === 'inactive') {
            $where[] = "`is_active` = 0";
        }

        $sql = "SELECT `id`, `name`, `code`, `group_name`, `description`, `is_active`, `created_at`, `updated_at`
                FROM `wi_permissions`";

        if ($where !== []) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $sql .= " ORDER BY `group_name` ASC, `name` ASC";

        $rows = $this->db->select($sql, $params);

        foreach ($rows as &$row) {
            $row['id'] = (int)($row['id'] ?? 0);
            $row['is_active'] = (int)($row['is_active'] ?? 0);
            $row['linked_roles_count'] = $this->countRolesForPermission((int)$row['id']);
        }
        unset($row);

        return $rows;
    }

    /**
     * @return array<int, string>
     */
    public function permissionGroups(): array
    {
        if (!$this->db->tableExists('wi_permissions')) {
            return [];
        }

        $rows = $this->db->bindfree(
            "SELECT DISTINCT `group_name`
             FROM `wi_permissions`
             ORDER BY `group_name` ASC"
        );

        $groups = [];
        foreach ($rows as $row) {
            $group = trim((string)($row['group_name'] ?? ''));
            if ($group !== '') {
                $groups[] = $group;
            }
        }

        return $groups;
    }

    /**
     * @param array<int, array<string, mixed>> $permissions
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function groupPermissions(array $permissions): array
    {
        $groups = [];

        foreach ($permissions as $permission) {
            $group = trim((string)($permission['group_name'] ?? ''));
            if ($group === '') {
                $group = 'General';
            }

            if (!isset($groups[$group])) {
                $groups[$group] = [];
            }

            $groups[$group][] = $permission;
        }

        return $groups;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getRole(int $roleId): ?array
    {
        if ($roleId <= 0 || !$this->db->tableExists('wi_user_roles')) {
            return null;
        }

        $rows = $this->db->select(
            "SELECT `role_id`, `role`
             FROM `wi_user_roles`
             WHERE `role_id` = :role_id
             LIMIT 1",
            ['role_id' => $roleId]
        );

        if ($rows === []) {
            return null;
        }

        $role = $rows[0];
        $role['role_id'] = (int)($role['role_id'] ?? 0);
        $role['is_protected'] = $this->isProtectedRole((int)$role['role_id'], (string)($role['role'] ?? ''));
        $role['permission_ids'] = $this->getRolePermissionIds((int)$role['role_id']);

        return $role;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getPermission(int $permissionId): ?array
    {
        if ($permissionId <= 0 || !$this->db->tableExists('wi_permissions')) {
            return null;
        }

        $rows = $this->db->select(
            "SELECT `id`, `name`, `code`, `group_name`, `description`, `is_active`
             FROM `wi_permissions`
             WHERE `id` = :id
             LIMIT 1",
            ['id' => $permissionId]
        );

        if ($rows === []) {
            return null;
        }

        $permission = $rows[0];
        $permission['id'] = (int)($permission['id'] ?? 0);
        $permission['is_active'] = (int)($permission['is_active'] ?? 0);
        $permission['linked_roles_count'] = $this->countRolesForPermission((int)$permission['id']);

        return $permission;
    }

    /**
     * @return array<string, mixed>
     */
    public function saveRole(array $data): array
    {
        if (!$this->db->tableExists('wi_user_roles')) {
            return $this->error('Roles table is not available.');
        }

        $roleId = max(0, (int)($data['role_id'] ?? 0));
        $roleName = trim((string)($data['role'] ?? ''));

        $errors = [];

        if ($roleName === '') {
            $errors[] = ['field' => 'role', 'message' => 'Role name is required.'];
        } elseif (mb_strlen($roleName) > 50) {
            $errors[] = ['field' => 'role', 'message' => 'Role name must be 50 characters or less.'];
        }

        if ($roleName !== '' && $this->roleNameExists($roleName, $roleId)) {
            $errors[] = ['field' => 'role', 'message' => 'Role already exists.'];
        }

        if ($roleId > 0) {
            $existing = $this->getRole($roleId);
            if ($existing === null) {
                $errors[] = ['field' => 'role_id', 'message' => 'Role not found.'];
            }
        }

        if ($errors !== []) {
            return $this->error('Please check the role fields.', ['errors' => $errors]);
        }

        if ($roleId > 0) {
            $this->db->update(
                'wi_user_roles',
                ['role' => $roleName],
                '`role_id` = :role_id',
                ['role_id' => $roleId]
            );

            return $this->success('Role updated successfully.', ['role_id' => $roleId]);
        }

        $this->db->insert('wi_user_roles', ['role' => $roleName]);
        $newId = (int)$this->db->lastInsertId();

        return $this->success('Role created successfully.', ['role_id' => $newId]);
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteRole(int $roleId): array
    {
        if ($roleId <= 0) {
            return $this->error('Invalid role id.');
        }

        $role = $this->getRole($roleId);
        if ($role === null) {
            return $this->error('Role not found.');
        }

        if ($this->isProtectedRole($roleId, (string)($role['role'] ?? ''))) {
            return $this->error('This core/system role cannot be deleted.');
        }

        if ($this->countUsersForRole($roleId) > 0) {
            return $this->error('This role is assigned to one or more users. Reassign users first.');
        }

        if ($this->db->tableExists('wi_role_permissions')) {
            $this->db->delete('wi_role_permissions', '`role_id` = :role_id', ['role_id' => $roleId]);
        }

        $this->db->delete('wi_user_roles', '`role_id` = :role_id', ['role_id' => $roleId]);

        return $this->success('Role deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    public function savePermission(array $data): array
    {
        if (!$this->db->tableExists('wi_permissions')) {
            return $this->error('Permissions table is not available.');
        }

        $id = max(0, (int)($data['id'] ?? 0));
        $name = trim((string)($data['name'] ?? ''));
        $code = strtolower(trim((string)($data['code'] ?? '')));
        $group = trim((string)($data['group_name'] ?? 'General'));
        $description = trim((string)($data['description'] ?? ''));
        $isActive = !empty($data['is_active']) ? 1 : 0;

        if ($group === '') {
            $group = 'General';
        }

        $errors = [];

        if ($name === '') {
            $errors[] = ['field' => 'name', 'message' => 'Permission name is required.'];
        } elseif (mb_strlen($name) > 100) {
            $errors[] = ['field' => 'name', 'message' => 'Permission name must be 100 characters or less.'];
        }

        if ($code === '') {
            $errors[] = ['field' => 'code', 'message' => 'Permission code is required.'];
        } elseif (!preg_match('/^[a-z0-9._-]+$/', $code)) {
            $errors[] = ['field' => 'code', 'message' => 'Use lowercase letters, numbers, dots, hyphens and underscores only.'];
        } elseif (mb_strlen($code) > 150) {
            $errors[] = ['field' => 'code', 'message' => 'Permission code must be 150 characters or less.'];
        }

        if (mb_strlen($group) > 100) {
            $errors[] = ['field' => 'group_name', 'message' => 'Group name must be 100 characters or less.'];
        }

        if (mb_strlen($description) > 255) {
            $errors[] = ['field' => 'description', 'message' => 'Description must be 255 characters or less.'];
        }

        if ($code !== '' && $this->permissionCodeExists($code, $id)) {
            $errors[] = ['field' => 'code', 'message' => 'Permission code already exists.'];
        }

        if ($id > 0 && $this->getPermission($id) === null) {
            $errors[] = ['field' => 'id', 'message' => 'Permission not found.'];
        }

        if ($errors !== []) {
            return $this->error('Please check the permission fields.', ['errors' => $errors]);
        }

        $payload = [
            'name' => $name,
            'code' => $code,
            'group_name' => $group,
            'description' => $description !== '' ? $description : null,
            'is_active' => $isActive,
        ];

        if ($id > 0) {
            $this->db->update('wi_permissions', $payload, '`id` = :id', ['id' => $id]);
            return $this->success('Permission updated successfully.', ['id' => $id]);
        }

        $this->db->insert('wi_permissions', $payload);
        $newId = (int)$this->db->lastInsertId();

        return $this->success('Permission created successfully.', ['id' => $newId]);
    }

    /**
     * @return array<string, mixed>
     */
    public function deletePermission(int $permissionId): array
    {
        if ($permissionId <= 0) {
            return $this->error('Invalid permission id.');
        }

        if ($this->getPermission($permissionId) === null) {
            return $this->error('Permission not found.');
        }

        if ($this->countRolesForPermission($permissionId) > 0) {
            return $this->error('Permission is assigned to one or more roles. Remove role links first.');
        }

        $this->db->delete('wi_permissions', '`id` = :id', ['id' => $permissionId]);

        return $this->success('Permission deleted successfully.');
    }

    /**
     * @param int[] $permissionIds
     * @return array<string, mixed>
     */
    public function updateRolePermissions(int $roleId, array $permissionIds): array
    {
        if ($roleId <= 0) {
            return $this->error('Invalid role id.');
        }

        if (!$this->db->tableExists('wi_role_permissions')) {
            return $this->error('Role permissions table is not available.');
        }

        $role = $this->getRole($roleId);
        if ($role === null) {
            return $this->error('Role not found.');
        }

        $cleanIds = [];
        foreach ($permissionIds as $permissionId) {
            $permissionId = (int)$permissionId;
            if ($permissionId > 0 && $this->getPermission($permissionId) !== null) {
                $cleanIds[$permissionId] = $permissionId;
            }
        }

        $this->db->delete('wi_role_permissions', '`role_id` = :role_id', ['role_id' => $roleId]);

        foreach ($cleanIds as $permissionId) {
            $this->db->insert('wi_role_permissions', [
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }

        return $this->success('Role permissions saved successfully.', [
            'role_id' => $roleId,
            'permission_ids' => array_values($cleanIds),
        ]);
    }

    /**
     * @return int[]
     */
    public function getRolePermissionIds(int $roleId): array
    {
        if ($roleId <= 0 || !$this->db->tableExists('wi_role_permissions')) {
            return [];
        }

        $rows = $this->db->select(
            "SELECT `permission_id`
             FROM `wi_role_permissions`
             WHERE `role_id` = :role_id",
            ['role_id' => $roleId]
        );

        $ids = [];
        foreach ($rows as $row) {
            $ids[] = (int)($row['permission_id'] ?? 0);
        }

        return $ids;
    }

    /**
     * @return array<int, array<int, bool>>
     */
    public function rolePermissionMap(): array
    {
        if (!$this->db->tableExists('wi_role_permissions')) {
            return [];
        }

        $rows = $this->db->bindfree("SELECT `role_id`, `permission_id` FROM `wi_role_permissions`");
        $map = [];

        foreach ($rows as $row) {
            $roleId = (int)($row['role_id'] ?? 0);
            $permissionId = (int)($row['permission_id'] ?? 0);
            if ($roleId <= 0 || $permissionId <= 0) {
                continue;
            }
            if (!isset($map[$roleId])) {
                $map[$roleId] = [];
            }
            $map[$roleId][$permissionId] = true;
        }

        return $map;
    }

    public function roleNameExists(string $roleName, int $excludeId = 0): bool
    {
        if (!$this->db->tableExists('wi_user_roles')) {
            return false;
        }

        $sql = "SELECT `role_id` FROM `wi_user_roles` WHERE LOWER(`role`) = :role LIMIT 1";
        $params = ['role' => mb_strtolower(trim($roleName))];

        if ($excludeId > 0) {
            $sql = "SELECT `role_id` FROM `wi_user_roles` WHERE LOWER(`role`) = :role AND `role_id` != :role_id LIMIT 1";
            $params['role_id'] = $excludeId;
        }

        return $this->db->select($sql, $params) !== [];
    }

    public function permissionCodeExists(string $code, int $excludeId = 0): bool
    {
        if (!$this->db->tableExists('wi_permissions')) {
            return false;
        }

        $sql = "SELECT `id` FROM `wi_permissions` WHERE `code` = :code LIMIT 1";
        $params = ['code' => $code];

        if ($excludeId > 0) {
            $sql = "SELECT `id` FROM `wi_permissions` WHERE `code` = :code AND `id` != :id LIMIT 1";
            $params['id'] = $excludeId;
        }

        return $this->db->select($sql, $params) !== [];
    }

    public function countUsersForRole(int $roleId): int
    {
        if ($roleId <= 0 || !$this->db->tableExists('wi_members')) {
            return 0;
        }

        $rows = $this->db->select(
            "SELECT COUNT(*) AS total FROM `wi_members` WHERE `user_role` = :role_id",
            ['role_id' => $roleId]
        );

        return (int)($rows[0]['total'] ?? 0);
    }

    public function countRolesForPermission(int $permissionId): int
    {
        if ($permissionId <= 0 || !$this->db->tableExists('wi_role_permissions')) {
            return 0;
        }

        $rows = $this->db->select(
            "SELECT COUNT(*) AS total FROM `wi_role_permissions` WHERE `permission_id` = :permission_id",
            ['permission_id' => $permissionId]
        );

        return (int)($rows[0]['total'] ?? 0);
    }

    public function countRolePermissionLinks(): int
    {
        if (!$this->db->tableExists('wi_role_permissions')) {
            return 0;
        }

        $rows = $this->db->bindfree("SELECT COUNT(*) AS total FROM `wi_role_permissions`");
        return (int)($rows[0]['total'] ?? 0);
    }

    public function isProtectedRole(int $roleId, string $roleName = ''): bool
    {
        if (in_array($roleId, $this->protectedRoleIds, true)) {
            return true;
        }

        $normalised = mb_strtolower(trim($roleName));
        return $normalised !== '' && in_array($normalised, $this->protectedRoleNames, true);
    }

    /**
     * @return array<string, mixed>
     */
    private function success(string $message, array $data = []): array
    {
        return [
            'success' => true,
            'status' => 'success',
            'message' => $message,
            'msg' => $message,
            'data' => $data,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function error(string $message, array $data = []): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'message' => $message,
            'msg' => $message,
            'data' => $data,
        ];
    }
}
