<?php
declare(strict_types=1);

#[\AllowDynamicProperties]
class WIBooker
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function create(array $data): bool
    {
        return $this->WIdb->insert(
            "wi_bookings",
            [
                "name" => trim((string)$data['name']),
                "email" => trim((string)$data['email']),
                "date" => trim((string)$data['date']),
                "created" => date("Y-m-d H:i:s")
            ]
        );
    }

    public function getAll(): array
    {
        return $this->WIdb->bindfree(
            "SELECT * FROM wi_bookings ORDER BY created DESC"
        );
    }
}
?>