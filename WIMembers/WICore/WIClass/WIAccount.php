<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: WIAccount.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Class
 * Layer: Account
 * Purpose Area: Member workspace, profile, account, settings, payments, forms and training
 * Version: 1.0.0
 * Created: 2026-05-24
 * Last Updated: 2026-05-24
 * Status: Refactored
 * Summary: WICMS-compatible WIMembers modernisation. Keeps WI-prefixed classes, database-driven modules, sessions and page rendering.
 */


class WIAccount
{
    private WIUser $user;
    private WIdb $WIdb;

    public function __construct(?int $userId = null)
    {
        $this->user = new WIUser($userId);
        $this->WIdb = WIdb::getInstance();
    }

    public function updateAccount(array $data): array
    {
        $info = [];
        $email = trim((string) ($data['email'] ?? ''));
        $username = trim((string) ($data['username'] ?? ''));

        if ($email !== '' && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $info['email'] = $email;
        }

        if ($username !== '') {
            $info['username'] = preg_replace('/[^a-zA-Z0-9_.@-]/', '', $username);
        }

        if ($info !== []) {
            $this->user->updateInfo($info);
        }

        return ['success' => true, 'message' => 'Account details saved.'];
    }

    public function updatePassword(array $data): array
    {
        $current = (string) ($data['current_password'] ?? '');
        $new = (string) ($data['new_password'] ?? '');
        $confirm = (string) ($data['confirm_password'] ?? '');

        if ($new === '' || strlen($new) < 10) {
            return ['success' => false, 'message' => 'New password must be at least 10 characters.'];
        }

        if ($new !== $confirm) {
            return ['success' => false, 'message' => 'The new password confirmation does not match.'];
        }

        $info = $this->user->getInfo();
        $register = new WIRegister();
        if (!$register->verifyPassword($current, (string) ($info['password'] ?? ''))) {
            return ['success' => false, 'message' => 'Current password was incorrect.'];
        }

        $this->user->updateInfo(['password' => $register->hashPassword($new)]);
        return ['success' => true, 'message' => 'Password updated.'];
    }
}
