<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIRole
| File: WIRole.php
| Location: /WIAdmin/WICore/WIClass/WIRole.php
| Type: Role / Permission Service
| Layer: Shared Core
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Shared role and permission administration service.
|
| Notes:
| - Uses WIdb only
| - Preserves existing public method names for compatibility
| - Keeps core/system roles protected from accidental deletion
|--------------------------------------------------------------------------
*/

class WIRole
{
    protected WIdb $WIdb;
    protected ?object $validator = null;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();

        if (class_exists('WIValidator')) {
            $this->validator = new WIValidator();
        }
    }

    public function getRoles(): array
    {
        if (!$this->WIdb->tableExists('wi_user_roles')) {
            return [];
        }

        $sql = "SELECT r.`role_id`,
                       r.`role`,
                       (
                           SELECT COUNT(*)
                           FROM `wi_members` m
                           WHERE m.`user_role` = r.`role_id`
                       ) AS users_count";

        if ($this->WIdb->tableExists('wi_role_permissions')) {
            $sql .= ",
                       (
                           SELECT COUNT(*)
                           FROM `wi_role_permissions` rp
                           WHERE rp.`role_id` = r.`role_id`
                       ) AS permissions_count";
        } else {
            $sql .= ", 0 AS permissions_count";
        }

        $sql .= "
                FROM `wi_user_roles` r
                ORDER BY r.`role_id` ASC";

        return $this->WIdb->bindfree($sql);
    }

    public function getRoleById(int $roleId): ?array
    {
        if ($roleId <= 0 || !$this->WIdb->tableExists('wi_user_roles')) {
            return null;
        }

        $result = $this->WIdb->select(
            "SELECT `role_id`, `role`
             FROM `wi_user_roles`
             WHERE `role_id` = :role_id
             LIMIT 1",
            ['role_id' => $roleId]
        );

        return $result[0] ?? null;
    }

    public function roleExists(string $roleName, int $excludeId = 0): bool
    {
        if (!$this->WIdb->tableExists('wi_user_roles')) {
            return false;
        }

        $roleName = strtolower(trim($roleName));

        if ($roleName === '') {
            return false;
        }

        $sql = "SELECT `role_id`
                FROM `wi_user_roles`
                WHERE LOWER(`role`) = :role";

        $params = ['role' => $roleName];

        if ($excludeId > 0) {
            $sql .= " AND `role_id` != :role_id";
            $params['role_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $result = $this->WIdb->select($sql, $params);

        return $result !== [];
    }

    public function saveRole(array $data): array
    {
        if (!$this->WIdb->tableExists('wi_user_roles')) {
            return [
                'status' => 'error',
                'msg'    => 'Roles table is not available.',
            ];
        }

        $roleId = isset($data['role_id']) ? (int) $data['role_id'] : 0;
        $role   = trim((string) ($data['role'] ?? ''));

        $errors = [];

        if ($role === '') {
            $errors[] = ['id' => 'role-name', 'msg' => 'Role name is required.'];
        }

        if ($this->roleExists($role, $roleId)) {
            $errors[] = ['id' => 'role-name', 'msg' => 'Role already exists.'];
        }

        if ($errors !== []) {
            return [
                'status' => 'error',
                'errors' => $errors,
            ];
        }

        if ($roleId > 0) {
            $this->WIdb->update(
                'wi_user_roles',
                ['role' => $role],
                '`role_id` = :role_id',
                ['role_id' => $roleId]
            );

            $this->logRoleEvent('Updated role', [
                'role_id'   => $roleId,
                'role_name' => $role,
            ]);

            return [
                'status' => 'success',
                'msg'    => 'Role updated successfully.',
            ];
        }

        $this->WIdb->insert('wi_user_roles', ['role' => $role]);
        $newId = (int) $this->WIdb->lastInsertId();

        $this->logRoleEvent('Created role', [
            'role_id'   => $newId,
            'role_name' => $role,
        ]);

        return [
            'status'   => 'success',
            'msg'      => 'Role created successfully.',
            'roleId'   => $newId,
            'roleName' => $role,
        ];
    }

    public function deleteRole(int $roleId): array
    {
        if ($roleId <= 0) {
            return [
                'status' => 'error',
                'msg'    => 'Invalid role id.',
            ];
        }

        if (!$this->WIdb->tableExists('wi_user_roles')) {
            return [
                'status' => 'error',
                'msg'    => 'Roles table is not available.',
            ];
        }

        if (in_array($roleId, [1, 2, 3], true)) {
            return [
                'status' => 'error',
                'msg'    => 'This system role cannot be deleted.',
            ];
        }

        if ($this->WIdb->tableExists('wi_members')) {
            $inUse = $this->WIdb->select(
                "SELECT COUNT(*) AS total
                 FROM `wi_members`
                 WHERE `user_role` = :role_id",
                ['role_id' => $roleId]
            );

            if ((int) ($inUse[0]['total'] ?? 0) > 0) {
                return [
                    'status' => 'error',
                    'msg'    => 'This role is assigned to one or more users. Reassign users first.',
                ];
            }
        }

        if ($this->WIdb->tableExists('wi_role_permissions')) {
            $this->WIdb->Fulldelete(
                'wi_role_permissions',
                '`role_id` = :role_id',
                ['role_id' => $roleId]
            );
        }

        $this->WIdb->delete(
            'wi_user_roles',
            '`role_id` = :role_id',
            ['role_id' => $roleId]
        );

        $this->logRoleEvent('Deleted role', ['role_id' => $roleId]);

        return [
            'status' => 'success',
            'msg'    => 'Role deleted successfully.',
        ];
    }

    public function getAllPermissions(): array
    {
        if (!$this->WIdb->tableExists('wi_permissions')) {
            return [];
        }

        $sql = "SELECT `id`, `name`, `code`, `group_name`, `description`, `is_active`
                FROM `wi_permissions`";

        if ($this->WIdb->columnExists('wi_permissions', 'is_active')) {
            $sql .= " WHERE `is_active` = 1";
        }

        $sql .= " ORDER BY `group_name` ASC, `name` ASC";

        return $this->WIdb->bindfree($sql);
    }

    public function getGroupedPermissions(): array
    {
        $permissions = $this->getAllPermissions();
        $grouped = [];

        foreach ($permissions as $permission) {
            $group = !empty($permission['group_name']) ? (string) $permission['group_name'] : 'General';

            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }

            $grouped[$group][] = $permission;
        }

        return $grouped;
    }

    public function getRolePermissionIds(int $roleId): array
    {
        if ($roleId <= 0 || !$this->WIdb->tableExists('wi_role_permissions')) {
            return [];
        }

        $rows = $this->WIdb->select(
            "SELECT `permission_id`
             FROM `wi_role_permissions`
             WHERE `role_id` = :role_id",
            ['role_id' => $roleId]
        );

        $ids = [];

        foreach ($rows as $row) {
            $ids[] = (int) ($row['permission_id'] ?? 0);
        }

        return $ids;
    }

    public function updateRolePermissions(int $roleId, array $permissionIds): array
    {
        if ($roleId <= 0) {
            return [
                'status' => 'error',
                'msg'    => 'Invalid role id.',
            ];
        }

        if (!$this->WIdb->tableExists('wi_role_permissions')) {
            return [
                'status' => 'error',
                'msg'    => 'Role permissions table is not available.',
            ];
        }

        $role = $this->getRoleById($roleId);

        if ($role === null) {
            return [
                'status' => 'error',
                'msg'    => 'Role not found.',
            ];
        }

        $cleanIds = [];

        foreach ($permissionIds as $permissionId) {
            $permissionId = (int) $permissionId;
            if ($permissionId > 0) {
                $cleanIds[$permissionId] = $permissionId;
            }
        }

        $this->WIdb->Fulldelete(
            'wi_role_permissions',
            '`role_id` = :role_id',
            ['role_id' => $roleId]
        );

        foreach ($cleanIds as $permissionId) {
            $this->WIdb->insert('wi_role_permissions', [
                'role_id'       => $roleId,
                'permission_id' => $permissionId,
            ]);
        }

        $this->logRoleEvent('Updated role permissions', [
            'role_id'        => $roleId,
            'permission_ids' => array_values($cleanIds),
        ]);

        return [
            'status' => 'success',
            'msg'    => 'Role permissions updated successfully.',
        ];
    }

    private function logRoleEvent(string $message, array $context = []): void
    {
        if (class_exists('WILogger')) {
            WILogger::info($message, $context, 'roles');
        }
    }
}