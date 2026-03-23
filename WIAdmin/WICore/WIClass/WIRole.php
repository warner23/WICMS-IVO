<?php
#[\AllowDynamicProperties]
/**
 * WIRole Class
 * Rewritten for:
 * - wi_user_roles
 * - wi_members.user_role
 * - wi_role_permissions
 * - wi_permissions
 */

class WIRole
{
    protected $WIdb;
    protected $validator;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->validator = new WIValidator();
    }

    public function getRoles(): array
    {
        return $this->WIdb->bindfree(
            "SELECT r.`role_id`, r.`role`,
                    (
                        SELECT COUNT(*)
                        FROM `wi_members` m
                        WHERE m.`user_role` = r.`role_id`
                    ) AS users_count,
                    (
                        SELECT COUNT(*)
                        FROM `wi_role_permissions` rp
                        WHERE rp.`role_id` = r.`role_id`
                    ) AS permissions_count
             FROM `wi_user_roles` r
             ORDER BY r.`role_id` ASC"
        );
    }

    public function getRoleById(int $roleId): ?array
    {
        $result = $this->WIdb->select(
            "SELECT `role_id`, `role`
             FROM `wi_user_roles`
             WHERE `role_id` = :role_id
             LIMIT 1",
            ['role_id' => $roleId]
        );

        return count($result) > 0 ? $result[0] : null;
    }

    public function roleExists(string $roleName, int $excludeId = 0): bool
    {
        $sql = "SELECT `role_id`
                FROM `wi_user_roles`
                WHERE LOWER(`role`) = :role";

        $params = ['role' => strtolower(trim($roleName))];

        if ($excludeId > 0) {
            $sql .= " AND `role_id` != :role_id";
            $params['role_id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $result = $this->WIdb->select($sql, $params);

        return count($result) > 0;
    }

    public function saveRole(array $data): array
    {
        $roleId = isset($data['role_id']) ? (int)$data['role_id'] : 0;
        $role   = trim((string)($data['role'] ?? ''));

        $errors = [];

        if ($role === '') {
            $errors[] = ['id' => 'role-name', 'msg' => 'Role name is required.'];
        }

        if ($this->roleExists($role, $roleId)) {
            $errors[] = ['id' => 'role-name', 'msg' => 'Role already exists.'];
        }

        if (!empty($errors)) {
            return [
                'status' => 'error',
                'errors' => $errors
            ];
        }

        if ($roleId > 0) {
            $this->WIdb->update(
                'wi_user_roles',
                ['role' => $role],
                '`role_id` = :role_id',
                ['role_id' => $roleId]
            );

            return [
                'status' => 'success',
                'msg'    => 'Role updated successfully.'
            ];
        }

        $this->WIdb->insert('wi_user_roles', ['role' => $role]);
        $newId = $this->WIdb->lastInsertId();

        return [
            'status'   => 'success',
            'msg'      => 'Role created successfully.',
            'roleId'   => $newId,
            'roleName' => $role
        ];
    }

    public function deleteRole(int $roleId): array
    {
        if ($roleId <= 0) {
            return [
                'status' => 'error',
                'msg'    => 'Invalid role id.'
            ];
        }

        /* preserve current core-role behaviour */
        if (in_array($roleId, [1, 2, 3], true)) {
            return [
                'status' => 'error',
                'msg'    => 'This system role cannot be deleted.'
            ];
        }

        $inUse = $this->WIdb->select(
            "SELECT COUNT(*) AS total
             FROM `wi_members`
             WHERE `user_role` = :role_id",
            ['role_id' => $roleId]
        );

        if (!empty($inUse) && (int)$inUse[0]['total'] > 0) {
            return [
                'status' => 'error',
                'msg'    => 'This role is assigned to one or more users. Reassign users first.'
            ];
        }

        $this->WIdb->delete(
            'wi_role_permissions',
            '`role_id` = :role_id',
            ['role_id' => $roleId]
        );

        $this->WIdb->delete(
            'wi_user_roles',
            '`role_id` = :role_id',
            ['role_id' => $roleId]
        );

        return [
            'status' => 'success',
            'msg'    => 'Role deleted successfully.'
        ];
    }

    public function getAllPermissions(): array
    {
        return $this->WIdb->bindfree(
            "SELECT `id`, `name`, `code`, `group_name`, `description`, `is_active`
             FROM `wi_permissions`
             WHERE `is_active` = 1
             ORDER BY `group_name` ASC, `name` ASC"
        );
    }

    public function getGroupedPermissions(): array
    {
        $permissions = $this->getAllPermissions();
        $grouped = [];

        foreach ($permissions as $permission) {
            $group = !empty($permission['group_name']) ? $permission['group_name'] : 'General';

            if (!isset($grouped[$group])) {
                $grouped[$group] = [];
            }

            $grouped[$group][] = $permission;
        }

        return $grouped;
    }

    public function getRolePermissionIds(int $roleId): array
    {
        $rows = $this->WIdb->select(
            "SELECT `permission_id`
             FROM `wi_role_permissions`
             WHERE `role_id` = :role_id",
            ['role_id' => $roleId]
        );

        $ids = [];

        foreach ($rows as $row) {
            $ids[] = (int)$row['permission_id'];
        }

        return $ids;
    }

    public function updateRolePermissions(int $roleId, array $permissionIds): array
    {
        if ($roleId <= 0) {
            return [
                'status' => 'error',
                'msg'    => 'Invalid role id.'
            ];
        }

        $role = $this->getRoleById($roleId);
        if (!$role) {
            return [
                'status' => 'error',
                'msg'    => 'Role not found.'
            ];
        }

        $cleanIds = [];
        foreach ($permissionIds as $permissionId) {
            $permissionId = (int)$permissionId;
            if ($permissionId > 0) {
                $cleanIds[$permissionId] = $permissionId;
            }
        }

        $this->WIdb->delete(
            'wi_role_permissions',
            '`role_id` = :role_id',
            ['role_id' => $roleId]
        );

        foreach ($cleanIds as $permissionId) {
            $this->WIdb->insert('wi_role_permissions', [
                'role_id'       => $roleId,
                'permission_id' => $permissionId
            ]);
        }

        return [
            'status' => 'success',
            'msg'    => 'Role permissions updated successfully.'
        ];
    }
}