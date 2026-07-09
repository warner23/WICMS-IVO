<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| WICMS Permission Guard
|--------------------------------------------------------------------------
| Shared WICMS permission checker.
|
| Purpose:
| - Resolve the current user's role from wi_members.user_role.
| - Check role links in wi_role_permissions.
| - Check active permission codes in wi_permissions.code.
| - Keep permission enforcement out of UI/view files.
|--------------------------------------------------------------------------
*/

final class WICMSPermissionGuard
{
    private WIdb $db;
    private int $userId;

    /** @var array<string, bool> */
    private array $permissionCache = [];

    private ?int $roleId = null;
    private ?string $roleName = null;

    public function __construct(int $userId, ?WIdb $db = null)
    {
        $this->userId = max(0, $userId);
        $this->db = $db ?? WIdb::getInstance();
    }

    public function userId(): int
    {
        return $this->userId;
    }

    public function roleId(): int
    {
        if ($this->roleId !== null) {
            return $this->roleId;
        }

        if ($this->userId <= 0 || !$this->db->tableExists('wi_members')) {
            return $this->roleId = 0;
        }

        $rows = $this->db->select(
            "SELECT `user_role` FROM `wi_members` WHERE `user_id` = :user_id LIMIT 1",
            ['user_id' => $this->userId]
        );

        return $this->roleId = (int)($rows[0]['user_role'] ?? 0);
    }

    public function roleName(): string
    {
        if ($this->roleName !== null) {
            return $this->roleName;
        }

        $roleId = $this->roleId();
        if ($roleId <= 0 || !$this->db->tableExists('wi_user_roles')) {
            return $this->roleName = '';
        }

        $rows = $this->db->select(
            "SELECT `role` FROM `wi_user_roles` WHERE `role_id` = :role_id LIMIT 1",
            ['role_id' => $roleId]
        );

        return $this->roleName = (string)($rows[0]['role'] ?? '');
    }

    public function has(string $permissionCode): bool
    {
        $permissionCode = strtolower(trim($permissionCode));
        if ($permissionCode === '' || $this->userId <= 0) {
            return false;
        }

        if (array_key_exists($permissionCode, $this->permissionCache)) {
            return $this->permissionCache[$permissionCode];
        }

        if (!$this->db->tableExists('wi_members') || !$this->db->tableExists('wi_role_permissions') || !$this->db->tableExists('wi_permissions')) {
            return $this->permissionCache[$permissionCode] = false;
        }

        $rows = $this->db->select(
            "SELECT 1
             FROM `wi_members` m
             INNER JOIN `wi_role_permissions` rp ON rp.`role_id` = m.`user_role`
             INNER JOIN `wi_permissions` p ON p.`id` = rp.`permission_id`
             WHERE m.`user_id` = :user_id
               AND p.`code` = :permission_code
               AND COALESCE(p.`is_active`, 1) = 1
             LIMIT 1",
            [
                'user_id' => $this->userId,
                'permission_code' => $permissionCode,
            ]
        );

        return $this->permissionCache[$permissionCode] = ($rows !== []);
    }

    /**
     * @param string[] $permissionCodes
     */
    public function hasAny(array $permissionCodes): bool
    {
        foreach ($permissionCodes as $permissionCode) {
            if ($this->has((string)$permissionCode)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $permissionCodes
     */
    public function hasAll(array $permissionCodes): bool
    {
        foreach ($permissionCodes as $permissionCode) {
            if (!$this->has((string)$permissionCode)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return string[]
     */
    public function permissions(): array
    {
        if ($this->userId <= 0 || !$this->db->tableExists('wi_members') || !$this->db->tableExists('wi_role_permissions') || !$this->db->tableExists('wi_permissions')) {
            return [];
        }

        $rows = $this->db->select(
            "SELECT p.`code`
             FROM `wi_members` m
             INNER JOIN `wi_role_permissions` rp ON rp.`role_id` = m.`user_role`
             INNER JOIN `wi_permissions` p ON p.`id` = rp.`permission_id`
             WHERE m.`user_id` = :user_id
               AND COALESCE(p.`is_active`, 1) = 1
             ORDER BY p.`group_name`, p.`code`",
            ['user_id' => $this->userId]
        );

        $codes = [];
        foreach ($rows as $row) {
            $code = strtolower(trim((string)($row['code'] ?? '')));
            if ($code !== '') {
                $codes[] = $code;
                $this->permissionCache[$code] = true;
            }
        }

        return array_values(array_unique($codes));
    }

    public function canEnterAdmin(): bool
    {
        return $this->has('admin.access');
    }
}
