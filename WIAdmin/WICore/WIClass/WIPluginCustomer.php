<?php
declare(strict_types=1);

#[\AllowDynamicProperties]
class WIPluginCustomer
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function getById(int $customerId): ?array
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_plugin_customers` WHERE `customer_id` = :id LIMIT 1",
            ['id' => $customerId]
        );

        return $rows[0] ?? null;
    }

    public function getByEmail(string $email): ?array
    {
        $rows = $this->WIdb->select(
            "SELECT * FROM `wi_plugin_customers` WHERE `email` = :email LIMIT 1",
            ['email' => $email]
        );

        return $rows[0] ?? null;
    }

    public function create(string $email, string $password, string $firstName = '', string $lastName = '', string $company = ''): bool
    {
        return (bool)$this->WIdb->insert('wi_plugin_customers', [
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'company_name' => $company
        ]);
    }
}