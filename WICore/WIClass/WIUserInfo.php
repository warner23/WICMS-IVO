<?php
declare(strict_types=1);

/**
 * WIUserInfo Class
 * Handles user profile information
 */

#[\AllowDynamicProperties]
class WIUserInfo
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    /**
     * Get user info by user id
     */
    public function getUserInfo($userId): ?array
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return null;
        }

        $result = $this->WIdb->select(
            "SELECT * FROM wi_user_info WHERE user_id = :id LIMIT 1",
            ["id" => $userId]
        );

        return $result[0] ?? null;
    }

    /**
     * Update user info
     */
    public function updateUserInfo($userId, array $data): bool
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return false;
        }

        return $this->WIdb->update(
            "wi_user_info",
            $data,
            "`user_id` = :id",
            ["id" => $userId]
        );
    }

    /**
     * Create user info row
     */
    public function createUserInfo($userId, array $data): bool
    {
        $userId = (int)$userId;

        if ($userId <= 0) {
            return false;
        }

        $data['user_id'] = $userId;

        return $this->WIdb->insert(
            "wi_user_info",
            $data
        );
    }
}