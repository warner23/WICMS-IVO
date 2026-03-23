<?php
#[\AllowDynamicProperties]
/**
 * WIPermissions Class
 * Rewritten for:
 * - wi_permissions
 * - wi_role_permissions
 * - wi_user_roles
 */

class WIPermissions
{
    protected $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function getRoles(): array
    {
        return $this->WIdb->bindfree(
            "SELECT `role_id`, `role`
             FROM `wi_user_roles`
             ORDER BY `role_id` ASC"
        );
    }

    public function getPermissions(): array
    {
        return $this->WIdb->bindfree(
            "SELECT `id`, `name`, `code`, `group_name`, `description`, `is_active`, `created_at`, `updated_at`
             FROM `wi_permissions`
             ORDER BY `group_name` ASC, `name` ASC"
        );
    }

    public function getGroupedPermissions(): array
    {
        $permissions = $this->getPermissions();
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

    public function getPermissionById(int $id): ?array
    {
        $result = $this->WIdb->select(
            "SELECT `id`, `name`, `code`, `group_name`, `description`, `is_active`
             FROM `wi_permissions`
             WHERE `id` = :id
             LIMIT 1",
            ['id' => $id]
        );

        return count($result) > 0 ? $result[0] : null;
    }

    public function permissionCodeExists(string $code, int $excludeId = 0): bool
    {
        $sql = "SELECT `id`
                FROM `wi_permissions`
                WHERE `code` = :code";

        $params = ['code' => $code];

        if ($excludeId > 0) {
            $sql .= " AND `id` != :id";
            $params['id'] = $excludeId;
        }

        $sql .= " LIMIT 1";

        $result = $this->WIdb->select($sql, $params);

        return count($result) > 0;
    }

    public function savePermission(array $data): array
    {
        $id          = isset($data['id']) ? (int)$data['id'] : 0;
        $name        = trim((string)($data['name'] ?? ''));
        $code        = trim((string)($data['code'] ?? ''));
        $groupName   = trim((string)($data['group_name'] ?? 'General'));
        $description = trim((string)($data['description'] ?? ''));
        $isActive    = isset($data['is_active']) ? (int)$data['is_active'] : 1;

        $errors = [];

        if ($name === '') {
            $errors[] = ['id' => 'permission-name', 'msg' => 'Permission name is required.'];
        }

        if ($code === '') {
            $errors[] = ['id' => 'permission-code', 'msg' => 'Permission code is required.'];
        } elseif (!preg_match('/^[a-z0-9._-]+$/', $code)) {
            $errors[] = ['id' => 'permission-code', 'msg' => 'Use lowercase letters, numbers, dots, hyphens and underscores only.'];
        }

        if ($groupName === '') {
            $errors[] = ['id' => 'permission-group', 'msg' => 'Group name is required.'];
        }

        if ($this->permissionCodeExists($code, $id)) {
            $errors[] = ['id' => 'permission-code', 'msg' => 'Permission code already exists.'];
        }

        if (!empty($errors)) {
            return [
                'status' => 'error',
                'errors' => $errors
            ];
        }

        $payload = [
            'name'        => $name,
            'code'        => $code,
            'group_name'  => $groupName,
            'description' => $description !== '' ? $description : null,
            'is_active'   => $isActive
        ];

        if ($id > 0) {
            $this->WIdb->update(
                'wi_permissions',
                $payload,
                '`id` = :id',
                ['id' => $id]
            );

            return [
                'status' => 'success',
                'msg'    => 'Permission updated successfully.'
            ];
        }

        $this->WIdb->insert('wi_permissions', $payload);

        return [
            'status' => 'success',
            'msg'    => 'Permission created successfully.'
        ];
    }

    public function deletePermission(int $id): array
    {
        if ($id <= 0) {
            return [
                'status' => 'error',
                'msg'    => 'Invalid permission id.'
            ];
        }

        $linked = $this->WIdb->select(
            "SELECT COUNT(*) AS total
             FROM `wi_role_permissions`
             WHERE `permission_id` = :id",
            ['id' => $id]
        );

        if (!empty($linked) && (int)$linked[0]['total'] > 0) {
            return [
                'status' => 'error',
                'msg'    => 'Permission is assigned to one or more roles. Remove role links first.'
            ];
        }

        $this->WIdb->delete('wi_permissions', '`id` = :id', ['id' => $id]);

        return [
            'status' => 'success',
            'msg'    => 'Permission deleted successfully.'
        ];
    }

    public function roleHasPermission(int $roleId, int $permissionId): bool
    {
        $result = $this->WIdb->select(
            "SELECT `id`
             FROM `wi_role_permissions`
             WHERE `role_id` = :role_id
               AND `permission_id` = :permission_id
             LIMIT 1",
            [
                'role_id'       => $roleId,
                'permission_id' => $permissionId
            ]
        );

        return count($result) > 0;
    }

    public function getRolePermissionMap(): array
    {
        $rows = $this->WIdb->bindfree(
            "SELECT `role_id`, `permission_id`
             FROM `wi_role_permissions`"
        );

        $map = [];

        foreach ($rows as $row) {
            $roleId = (int)$row['role_id'];
            $permissionId = (int)$row['permission_id'];

            if (!isset($map[$roleId])) {
                $map[$roleId] = [];
            }

            $map[$roleId][$permissionId] = true;
        }

        return $map;
    }

    public function toggleRolePermission(int $roleId, int $permissionId, int $enabled): array
    {
        if ($roleId <= 0 || $permissionId <= 0) {
            return [
                'status' => 'error',
                'msg'    => 'Invalid role or permission.'
            ];
        }

        if ($enabled === 1) {
            if (!$this->roleHasPermission($roleId, $permissionId)) {
                $this->WIdb->insert('wi_role_permissions', [
                    'role_id'       => $roleId,
                    'permission_id' => $permissionId
                ]);
            }

            return [
                'status' => 'success',
                'msg'    => 'Permission assigned to role.'
            ];
        }

        $this->WIdb->delete(
            'wi_role_permissions',
            '`role_id` = :role_id AND `permission_id` = :permission_id',
            [
                'role_id'       => $roleId,
                'permission_id' => $permissionId
            ]
        );

        return [
            'status' => 'success',
            'msg'    => 'Permission removed from role.'
        ];
    }
}