<?php
declare(strict_types=1);

/**
 * WICMS Canonical Registration Service
 * Root core source of truth
 */

class WIRegister
{
    private WIEmail $mailer;
    private WIdb $WIdb;
    private ?WIMaintenace $maint;
    private ?WILogin $login;
    private array $lastResult = [
        'status' => 'error',
        'message' => 'Registration not attempted',
        'errors' => []
    ];

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->mailer = new WIEmail();
        $this->maint = class_exists('WIMaintenace') ? new WIMaintenace() : null;
        $this->login = class_exists('WILogin') ? new WILogin() : null;
    }

    public function getLastResult(): array
    {
        return $this->lastResult;
    }

    public function register(array $data): array
    {
        $normalized = $this->normalizeRegistrationPayload($data);
        $user = $normalized['UserData'];

        $errors = $this->validateUser($normalized);

        if ($errors !== []) {
            $this->lastResult = [
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $errors
            ];

            return $this->lastResult;
        }

        $confirmationKey = $this->generateKey();
        $confirmed = $this->mailConfirmationRequired() ? 'N' : 'Y';

        $this->WIdb->insert('wi_members', [
            'email' => trim((string) ($user['email'] ?? '')),
            'username' => trim(strip_tags((string) ($user['username'] ?? ''))),
            'password' => $this->hashPassword((string) ($user['password'] ?? '')),
            'confirmation_key' => $confirmationKey,
            'confirmed' => $confirmed,
            'password_reset_key' => '',
            'password_reset_confirmed' => 'N',
            'password_reset_timestamp' => date('Y-m-d H:i:s'),
            'register_date' => date('Y-m-d'),
            'ip_addr' => (string) ($_SERVER['REMOTE_ADDR'] ?? ''),
            'banned' => 'N'
        ]);

        $userId = (int) $this->WIdb->lastInsertId();

        $this->WIdb->insert('wi_user_details', [
            'user_id' => $userId
        ]);

        $this->createUserAvatarFolder($userId);

        if ($this->maint !== null) {
            $this->maint->LogFunction(
                (string) ($user['username'] ?? ''),
                'Added new user'
            );
        }

        $message = WILang::get('success_registration_no_confirm');

        if ($this->mailConfirmationRequired()) {
            $this->mailer->confirmationEmail(
                (string) ($user['email'] ?? ''),
                $confirmationKey
            );
            $message = WILang::get('success_registration_with_confirm');
        }

        $this->lastResult = [
            'status' => 'success',
            'message' => $message,
            'msg' => $message,
            'user_id' => $userId
        ];

        return $this->lastResult;
    }

    public function getByEmail($email)
    {
        $result = $this->WIdb->select(
            'SELECT * FROM `wi_members` WHERE `email` = :e LIMIT 1',
            ['e' => trim((string) $email)]
        );

        return $result[0] ?? [];
    }

    public function getBySocial($provider, $id)
    {
        $result = $this->WIdb->select(
            'SELECT * FROM `wi_social_logins`
             WHERE `provider` = :p AND `provider_id` = :id
             LIMIT 1',
            [
                'p' => (string) $provider,
                'id' => (string) $id
            ]
        );

        if (count($result) > 0) {
            $res = $result[0];
            $user = new WIUser((int) $res['user_id']);

            return $user->getInfo();
        }

        return [];
    }

    public function registeredViaSocial($provider, $id): bool
    {
        return !empty($this->getBySocial($provider, $id));
    }

    public function addSocialAccount($userId, $provider, $providerId): void
    {
        $this->WIdb->insert('wi_social_logins', [
            'user_id' => (int) $userId,
            'provider' => (string) $provider,
            'provider_id' => (string) $providerId,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function forgotPassword($userEmail)
    {
        $validator = new WIValidator();
        $userEmail = trim((string) $userEmail);

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

        if ($login->isBruteForce()) {
            return WILang::get('brute_force');
        }

        $key = $this->generateKey();

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

    public function resetPassword($newPass, $passwordResetKey): void
    {
        $validator = new WIValidator();

        if (!$validator->prKeyValid($passwordResetKey)) {
            throw new RuntimeException('Invalid password reset key.');
        }

        $this->WIdb->update(
            'wi_members',
            [
                'password' => $this->hashPassword((string) $newPass),
                'password_reset_confirmed' => 'Y',
                'password_reset_key' => ''
            ],
            '`password_reset_key` = :prk',
            ['prk' => (string) $passwordResetKey]
        );
    }

    public function hashPassword($password): string
    {
        return password_hash(
            (string) $password,
            PASSWORD_BCRYPT,
            ['cost' => $this->getPasswordCost()]
        );
    }

    public function verifyPassword(string $plainPassword, string $storedHash): bool
    {
        if ($storedHash === '') {
            return false;
        }

        if (password_verify($plainPassword, $storedHash)) {
            return true;
        }

        return hash_equals($this->legacyHashPassword($plainPassword), $storedHash);
    }

    public function needsRehash(string $storedHash): bool
    {
        $info = password_get_info($storedHash);

        if (($info['algo'] ?? null) === null || ($info['algo'] ?? 0) === 0) {
            return true;
        }

        return password_needs_rehash(
            $storedHash,
            PASSWORD_BCRYPT,
            ['cost' => $this->getPasswordCost()]
        );
    }

    public function botProtection(): void
    {
        WISession::set('bot_first_number', random_int(1, 9));
        WISession::set('bot_second_number', random_int(1, 9));
    }

    public function validateUser($data, $botProtection = false): array
    {
        $normalized = $this->normalizeRegistrationPayload((array) $data);
        $id = $normalized['FieldId'];
        $user = $normalized['UserData'];

        $validator = new WIValidator();
        $errors = [];

        $email = trim((string) ($user['email'] ?? ''));
        $username = trim((string) ($user['username'] ?? ''));
        $password = (string) ($user['password'] ?? '');
        $confirmPassword = (string) ($user['confirm_password'] ?? '');

        if ($validator->isEmpty($email)) {
            $errors[] = ['id' => $id['email'] ?? 'reg-email', 'msg' => WILang::get('email_required')];
        } elseif (!$validator->emailValid($email)) {
            $errors[] = ['id' => $id['email'] ?? 'reg-email', 'msg' => WILang::get('email_wrong_format')];
        } elseif ($validator->emailExist($email)) {
            $errors[] = ['id' => $id['email'] ?? 'reg-email', 'msg' => WILang::get('email_taken')];
        }

        if ($validator->isEmpty($username)) {
            $errors[] = ['id' => $id['username'] ?? 'reg-username', 'msg' => WILang::get('username_required')];
        } elseif ($validator->usernameExist($username)) {
            $errors[] = ['id' => $id['username'] ?? 'reg-username', 'msg' => WILang::get('username_taken')];
        }

        if ($validator->isEmpty($password)) {
            $errors[] = ['id' => $id['password'] ?? 'reg-password', 'msg' => WILang::get('password_required')];
        } elseif (mb_strlen($password) < 8) {
            $errors[] = ['id' => $id['password'] ?? 'reg-password', 'msg' => WILang::get('password_length')];
        }

        if ($confirmPassword === '') {
            $errors[] = ['id' => $id['confirm_password'] ?? 'reg-repeat-password', 'msg' => WILang::get('password_required')];
        } elseif ($password !== $confirmPassword) {
            $errors[] = ['id' => $id['confirm_password'] ?? 'reg-repeat-password', 'msg' => WILang::get('passwords_dont_match')];
        }

        if ($botProtection) {
            $botOne = (int) WISession::get('bot_first_number', 0);
            $botTwo = (int) WISession::get('bot_second_number', 0);
            $expectedSum = $botOne + $botTwo;
            $providedSum = (int) ($user['bot_sum'] ?? 0);

            if ($expectedSum !== $providedSum) {
                $errors[] = ['id' => $id['bot_sum'] ?? 'reg-bot-sum', 'msg' => WILang::get('wrong_sum')];
            }
        }

        return $errors;
    }

    public function randomPassword($length = 12): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randomString = '';
        $maxIndex = strlen($characters) - 1;

        for ($i = 0; $i < (int) $length; $i++) {
            $randomString .= $characters[random_int(0, $maxIndex)];
        }

        return $randomString;
    }

    public function socialToken(): string
    {
        return bin2hex(random_bytes(20));
    }

    private function normalizeRegistrationPayload(array $data): array
    {
        $userData = $data['UserData'] ?? $data['userData'] ?? [];
        $fieldId = $data['FieldId'] ?? $data['fieldId'] ?? [];

        return [
            'UserData' => is_array($userData) ? $userData : [],
            'FieldId' => is_array($fieldId) ? $fieldId : []
        ];
    }

    private function createUserAvatarFolder(int $userId): void
    {
        if ($userId <= 0) {
            return;
        }

        $folder = dirname(dirname(dirname(__FILE__))) . '/WIAdmin/WIMedia/Img/avator/' . $userId . '/';

        if (!is_dir($folder)) {
            @mkdir($folder, 0755, true);
        }
    }

    private function generateKey(): string
    {
        return bin2hex(random_bytes(32));
    }

    private function getPasswordCost(): int
    {
        if (defined('PASSWORD_BCRYPT_COST')) {
            return max(10, (int) PASSWORD_BCRYPT_COST);
        }

        try {
            $settings = new WISettings();

            $bcryptCost = (int) $settings->website('bcrypt_cost');
            if ($bcryptCost > 0) {
                return max(10, $bcryptCost);
            }

            $legacyCost = (int) $settings->website('cost');
            if ($legacyCost > 0) {
                return max(10, $legacyCost);
            }
        } catch (Throwable $e) {
        }

        return 12;
    }

    private function getPasswordEncryption(): string
    {
        if (defined('PASSWORD_ENCRYPTION')) {
            return strtolower((string) PASSWORD_ENCRYPTION);
        }

        try {
            $settings = new WISettings();
            $value = strtolower((string) $settings->website('password_encryption'));

            if ($value !== '') {
                return $value;
            }
        } catch (Throwable $e) {
        }

        return 'bcrypt';
    }

    private function getPasswordSalt(): string
    {
        if (defined('PASSWORD_SALT')) {
            return (string) PASSWORD_SALT;
        }

        try {
            $settings = new WISettings();
            $value = (string) $settings->website('password_salt');

            if ($value !== '') {
                return $value;
            }
        } catch (Throwable $e) {
        }

        return 'wicms_default_salt_change_me';
    }

    private function getSha512Iterations(): int
    {
        if (defined('PASSWORD_SHA512_ITERATIONS')) {
            return max(1, (int) PASSWORD_SHA512_ITERATIONS);
        }

        try {
            $settings = new WISettings();
            $value = (int) $settings->website('sha512_iterations');

            if ($value > 0) {
                return $value;
            }
        } catch (Throwable $e) {
        }

        return 5000;
    }

    private function mailConfirmationRequired(): bool
    {
        if (defined('MAIL_CONFIRMATION_REQUIRED')) {
            return (bool) MAIL_CONFIRMATION_REQUIRED;
        }

        try {
            $settings = new WISettings();

            return filter_var(
                (string) $settings->website('mail_confirm_required'),
                FILTER_VALIDATE_BOOLEAN
            );
        } catch (Throwable $e) {
            return false;
        }
    }

    private function legacyHashPassword(string $password): string
    {
        $cost = str_pad((string) $this->getPasswordCost(), 2, '0', STR_PAD_LEFT);
        $salt = '$2a$' . $cost . '$' . substr(str_pad($this->getPasswordSalt(), 22, 'x'), 0, 22);

        if ($this->getPasswordEncryption() === 'bcrypt') {
            return crypt($password, $salt);
        }

        $newPassword = $password;
        $iterations = $this->getSha512Iterations();
        $legacySalt = $this->getPasswordSalt();

        for ($i = 0; $i < $iterations; $i++) {
            $newPassword = hash('sha512', $legacySalt . $newPassword . $legacySalt);
        }

        return $newPassword;
    }
}