<?php
#[\AllowDynamicProperties]

class WIUser
{
    protected $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function getUsers(): array
    {
        return $this->WIdb->bindfree(
            "SELECT m.`id`,
                    m.`username`,
                    m.`email`,
                    m.`first_name`,
                    m.`last_name`,
                    m.`registered`,
                    r.`role`
             FROM `wi_members` m
             LEFT JOIN `wi_user_roles` r
             ON m.`user_role` = r.`role_id`
             ORDER BY m.`id` DESC"
        );
    }

    public function getUserById(int $id): ?array
    {
        $result = $this->WIdb->select(
            "SELECT *
             FROM `wi_members`
             WHERE `id` = :id
             LIMIT 1",
            ['id' => $id]
        );

        return count($result) ? $result[0] : null;
    }

    public function getRoles(): array
    {
        return $this->WIdb->bindfree(
            "SELECT `role_id`,`role`
             FROM `wi_user_roles`
             ORDER BY `role_id` ASC"
        );
    }

    public function updateUserRole(int $userId, int $roleId): array
    {
        $this->WIdb->update(
            'wi_members',
            ['user_role' => $roleId],
            '`id` = :id',
            ['id' => $userId]
        );

        return [
            'status' => 'success',
            'msg' => 'User role updated'
        ];
    }

    public function deleteUser(int $id): array
    {
        $this->WIdb->delete(
            'wi_members',
            '`id` = :id',
            ['id' => $id]
        );

        return [
            'status' => 'success',
            'msg' => 'User deleted'
        ];
    }
}