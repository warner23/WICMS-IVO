<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: WIRegister.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Class
 * Layer: Registration
 * Purpose Area: Member workspace, profile, account, settings, payments, forms and training
 * Version: 1.0.0
 * Created: 2026-05-24
 * Last Updated: 2026-05-24
 * Status: Refactored
 * Summary: WICMS-compatible WIMembers modernisation. Keeps WI-prefixed classes, database-driven modules, sessions and page rendering.
 */


class WIRegister
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function hashPassword($password): string
    {
        return password_hash((string) $password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function verifyPassword(string $password, string $storedHash): bool
    {
        if ($storedHash === '') {
            return false;
        }

        if (password_get_info($storedHash)['algo'] !== 0) {
            return password_verify($password, $storedHash);
        }

        return hash_equals($storedHash, hash('sha512', $password));
    }

    public function needsRehash(string $storedHash): bool
    {
        return password_get_info($storedHash)['algo'] !== 0 && password_needs_rehash($storedHash, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    public function socialToken(): string
    {
        return bin2hex(random_bytes(16));
    }

    public function botProtection(): void
    {
        WISession::set('wi_bot_protection', bin2hex(random_bytes(8)));
    }
}
