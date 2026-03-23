<?php
declare(strict_types=1);

/**
 * Validator Class
 * WICMS Core
 * Created by Warner Infinity
 * Author Jules Warner
 */

#[\AllowDynamicProperties]
final class WIValidator
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    /**
     * Check if input is empty.
     */
    public function isEmpty(mixed $input): bool
    {
        if (is_array($input)) {
            return empty($input);
        }

        return trim((string)$input) === '';
    }

    /**
     * Check if string is longer than provided characters.
     */
    public function longerThan(string $string, int $characters): bool
    {
        return strlen($string) > $characters;
    }

    /**
     * Validate email format.
     */
    public function emailValid(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Check if username exists.
     */
    public function usernameExist(string $username): bool
    {
        return $this->exists('wi_members', 'username', $username);
    }

    /**
     * Check if email exists.
     */
    public function emailExist(string $email): bool
    {
        return $this->exists('wi_members', 'email', $email);
    }

    /**
     * Check if role exists.
     */
    public function roleExist(string $role): bool
    {
        return $this->exists('wi_user_roles', 'role', $role);
    }

    /**
     * Validate password reset key.
     */
    public function prKeyValid(string $key): bool
    {
        // secure tokens are now 64 chars
        if (strlen($key) !== 64) {
            return false;
        }

        $result = $this->WIdb->select(
            'SELECT * FROM `wi_members` WHERE `password_reset_key` = :k',
            ['k' => $key]
        );

        if (count($result) !== 1) {
            return false;
        }

        $user = $result[0];

        if (($user['password_reset_confirmed'] ?? 'Y') === 'Y') {
            return false;
        }

        $now = time();
        $requestedAt = strtotime((string)$user['password_reset_timestamp']);

        $lifetime = defined('PASSWORD_RESET_KEY_LIFE')
            ? (int)PASSWORD_RESET_KEY_LIFE
            : 60;

        if ($requestedAt + ($lifetime * 60) < $now) {
            return false;
        }

        return true;
    }

    /**
     * Generic existence check.
     */
    private function exists(string $table, string $column, string $value): bool
    {
        $result = $this->WIdb->select(
            "SELECT 1 FROM `$table` WHERE `$column` = :value LIMIT 1",
            ['value' => $value]
        );

        return count($result) > 0;
    }
}