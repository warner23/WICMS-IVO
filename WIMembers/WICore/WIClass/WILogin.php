<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: WILogin.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Class
 * Layer: Authentication
 * Purpose Area: Member workspace, profile, account, settings, payments, forms and training
 * Version: 1.0.0
 * Created: 2026-05-24
 * Last Updated: 2026-05-24
 * Status: Refactored
 * Summary: WICMS-compatible WIMembers modernisation. Keeps WI-prefixed classes, database-driven modules, sessions and page rendering.
 */


class WILogin
{
    private WIdb $WIdb;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
    }

    public function isLoggedIn(): bool
    {
        return (int) WISession::get('user_id', 0) > 0;
    }

    public function byId(int $id): void
    {
        if ($id <= 0) {
            return;
        }

        WISession::regenerate(true);
        WISession::set('user_id', $id);
        WISession::set('login_time', time());

        if ($this->WIdb->tableExists('wi_members')) {
            $this->WIdb->update('wi_members', ['last_login' => date('Y-m-d H:i:s')], '`user_id` = :id', ['id' => $id]);
        }
    }

    public function userLogin(string $username, string $password): bool
    {
        $row = $this->WIdb->row('SELECT * FROM `wi_members` WHERE `username` = :username OR `email` = :username LIMIT 1', ['username' => trim($username)]);
        if ($row === [] || ($row['banned'] ?? 'N') === 'Y') {
            return false;
        }

        $register = new WIRegister();
        if (!$register->verifyPassword($password, (string) ($row['password'] ?? ''))) {
            return false;
        }

        $this->byId((int) $row['user_id']);
        return true;
    }

    public function logout(): void
    {
        WISession::destroySession();
    }
}
