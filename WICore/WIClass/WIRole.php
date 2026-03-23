<?php
declare(strict_types=1);

/**
 * Role Class
 * Created by Warner Infinity
 */

#[\AllowDynamicProperties]
class WIRole
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function getAll(): array
    {
        $result = $this->WIdb->bindfree(
            "SELECT * FROM wi_roles ORDER BY id ASC"
        );

        return is_array($result) ? $result : [];
    }

    public function getRole(int $id): ?array
    {
        $result = $this->WIdb->select(
            "SELECT * FROM wi_roles WHERE id = :id LIMIT 1",
            ["id" => $id]
        );

        return $result[0] ?? null;
    }

    public function create(string $role): bool
    {
        return $this->WIdb->insert(
            "wi_roles",
            [
                "role_name" => trim($role)
            ]
        );
    }

    public function delete(int $id): bool
    {
        return $this->WIdb->delete(
            "wi_roles",
            "`id` = :id",
            ["id" => $id]
        );
    }
}
?>