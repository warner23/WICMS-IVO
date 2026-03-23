<?php
declare(strict_types=1);

/**
 * Register Class
 * Created by Warner Infinity
 * Author Jules Warner
 */

class WIRegister
{
    private WIdb $WIdb;
    private WIEmail $mailer;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->mailer = new WIEmail();
    }

    public function register(array $data): void
    {
        $user = $data['UserData'] ?? [];

        $errors = $this->validateUser($data);

        if (count($errors) !== 0) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            return;
        }

        $key = $this->_generateKey();
        $confirmed = (defined('MAIL_CONFIRMATION_REQUIRED') && MAIL_CONFIRMATION_REQUIRED === true) ? 'N' : 'Y';

        $email = trim((string) ($user['email'] ?? ''));
        $username = trim(strip_tags((string) ($user['username'] ?? '')));
        $password = (string) ($user['password'] ?? '');

        $this->WIdb->insert('wi_members', [
            'email' => $email,
            'username' => $username,
            'password' => $this->hashPassword($password),
            'confirmed' => $confirmed,
            'confirmation_key' => $key,
            'register_date' => date('Y-m-d'),
            'ip_addr' => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'
        ]);

        $userId = (int) $this->WIdb->lastInsertId();

        $st1 = $username;
        $st2 = 'Added new user';

        if (class_exists('WIMaintenace')) {
            WIMaintenace::LogFunction($st1, $st2);
        }

        $this->WIdb->insert('wi_user_details', [
            'user_id' => $userId
        ]);

        if (defined('MAIL_CONFIRMATION_REQUIRED') && MAIL_CONFIRMATION_REQUIRED === true) {
            $this->mailer->confirmationEmail($email, $key);
            $msg = WILang::get('success_registration_with_confirm');
        } else {
            $msg = WILang::get('success_registration_no_confirm');
        }

        echo json_encode([
            'status' => 'success',
            'msg' => $msg
        ]);
    }

    /**
     * Get user by email.
     */
    public function getByEmail(string $email): array
    {
        $result = $this->WIdb->select(
            'SELECT * FROM `wi_members` WHERE `email` = :e',
            ['e' => $email]
        );

        return count($result) > 0 ? $result[0] : [];
    }

    /**
     * Get user by social provider.
     */
    public function getBySocial(string $provider, string $id): array
    {
        $result = $this->WIdb->select(
            'SELECT * FROM `wi_social_logins` WHERE `provider` = :p AND `provider_id` = :id',
            [
                'p' => $provider,
                'id' => $id
            ]
        );

        if (count($result) > 0) {
            $res = $result[0];
            $user = new WIUser((int) $res['user_id']);
            return $user->getInfo();
        }

        return [];
    }

    /**
     * Check if user registered via social account.
     */
    public function registeredViaSocial(string $provider, string $id): bool
    {
        $result = $this->getBySocial($provider, $id);
        return count($result) !== 0;
    }

    /**
     * Connect social account.
     */
    public function addSocialAccount(int $userId, string $provider, string $providerId): void
    {
        $this->WIdb->insert('wi_social_logins', [
            'user_id' => $userId,
            'provider' => $provider,
            'provider_id' => $providerId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Send forgot password email.
     */
    public function forgotPassword(string $userEmail): bool|string
    {
        $validator = new WIValidator();

        if ($userEmail === '') {
            return WILang::get('email_required');
        }

        if (!$validator->emailValid($userEmail)) {
            return WILang::get('email_wrong_format');
        }

        if (!$validator->emailExist($userEmail)) {
            return WILang::get('email_not_exist');
        }

        $login = new WILogin();

        if ($login->_isBruteForce()) {
            return WILang::get('brute_force');
        }

        $key = $this->_generateKey();

        $this->WIdb->update(
            'wi_members',
            [
                'password_reset_key' => $key,
                'password_reset_confirmed' => 'N',
                'password_reset_timestamp' => date('Y-m-d H:i:s')
            ],
            '`email` = :email',
            ['email' => $userEmail]
        );

        $login->increaseLoginAttempts();
        $this->mailer->passwordResetEmail($userEmail, $key);

        return true;
    }

    /**
     * Reset password.
     */
    public function resetPassword(string $newPass, string $passwordResetKey): void
    {
        $validator = new WIValidator();

        if (!$validator->prKeyValid($passwordResetKey)) {
            echo 'Invalid password reset key!';
            return;
        }

        $pass = $this->hashPassword($newPass);

        $this->WIdb->update(
            'wi_members',
            [
                'password' => $pass,
                'password_reset_confirmed' => 'Y',
                'password_reset_key' => ''
            ],
            '`password_reset_key` = :prk',
            ['prk' => $passwordResetKey]
        );
    }

    /**
     * Hash password using modern PHP password API.
     */
    public function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Generate anti-bot numbers.
     */
    public function botProtection(): void
    {
        WISession::set('bot_first_number', random_int(1, 9));
        WISession::set('bot_second_number', random_int(1, 9));
    }

    /**
     * Validate registration data.
     */
    public function validateUser(array $data, bool $botProtection = true): array
    {
        $id = $data['FieldId'] ?? [];
        $user = $data['UserData'] ?? [];
        $errors = [];
        $validator = new WIValidator();

        $email = trim((string) ($user['email'] ?? ''));
        $username = trim((string) ($user['username'] ?? ''));
        $password = (string) ($user['password'] ?? '');
        $confirmPassword = (string) ($user['confirm_password'] ?? '');
        $botSum = (string) ($user['bot_sum'] ?? '');

        if ($validator->isEmpty($email)) {
            $errors[] = [
                'id' => $id['email'] ?? 'email',
                'msg' => WILang::get('email_required')
            ];
        }

        if ($validator->isEmpty($username)) {
            $errors[] = [
                'id' => $id['username'] ?? 'username',
                'msg' => WILang::get('username_required')
            ];
        }

        if ($validator->isEmpty($password)) {
            $errors[] = [
                'id' => $id['password'] ?? 'password',
                'msg' => WILang::get('password_required')
            ];
        }

        if ($password !== $confirmPassword) {
            $errors[] = [
                'id' => $id['confirm_password'] ?? 'confirm_password',
                'msg' => WILang::get('passwords_dont_match')
            ];
        }

        if (!$validator->emailValid($email)) {
            $errors[] = [
                'id' => $id['email'] ?? 'email',
                'msg' => WILang::get('email_wrong_format')
            ];
        }

        if ($validator->emailExist($email)) {
            $errors[] = [
                'id' => $id['email'] ?? 'email',
                'msg' => WILang::get('email_taken')
            ];
        }

        if ($validator->usernameExist($username)) {
            $errors[] = [
                'id' => $id['username'] ?? 'username',
                'msg' => WILang::get('username_taken')
            ];
        }

        if ($botProtection) {
            $sum = (int) WISession::get('bot_first_number', 0) + (int) WISession::get('bot_second_number', 0);

            if ($sum !== (int) $botSum) {
                $errors[] = [
                    'id' => $id['bot_sum'] ?? 'bot_sum',
                    'msg' => WILang::get('wrong_sum')
                ];
            }
        }

        return $errors;
    }

    /**
     * Generate random password.
     */
    public function randomPassword(int $length = 7): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Generate social token.
     */
    public function socialToken(): string
    {
        return bin2hex(random_bytes(20));
    }

    /**
     * Generate secure key for confirmation and reset flows.
     */
    private function _generateKey(): string
    {
        return bin2hex(random_bytes(32));
    }
}