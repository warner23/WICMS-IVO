<?php
declare(strict_types=1);

/**
 * Translation Class
 */

#[\AllowDynamicProperties]
class WITrans
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function getAll(): array
    {
        $result = $this->WIdb->bindfree(
            "SELECT * FROM wi_trans ORDER BY id ASC"
        );

        return is_array($result) ? $result : [];
    }

    public function get(string $key): ?string
    {
        $result = $this->WIdb->selectColumn(
            "SELECT * FROM wi_trans WHERE keyword = :key LIMIT 1",
            ["key" => $key],
            "translation"
        );

        return $result ?? null;
    }

    public function create(string $key, string $value): bool
    {
        return $this->WIdb->insert(
            "wi_trans",
            [
                "keyword" => trim($key),
                "translation" => trim($value)
            ]
        );
    }

    public function update(int $id, string $value): bool
    {
        return $this->WIdb->update(
            "wi_trans",
            [
                "translation" => trim($value)
            ],
            "`id` = :id",
            ["id" => $id]
        );
    }

    public function delete(int $id): bool
    {
        return $this->WIdb->delete(
            "wi_trans",
            "`id` = :id",
            ["id" => $id]
        );
    }
}
?>