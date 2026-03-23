<?php
declare(strict_types=1);

/**
 * Register Class
 * Created by Warner Infinity
 * Author Jules Warner
 */

class WIRegister
{
    private WIEmail $mailer;
    private WIdb $WIdb;
    private ?WIMaintenace $maint;
    private ?WILogin $login;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->mailer = new WIEmail();
        $this->maint = class_exists('WIMaintenace') ? new WIMaintenace() : null;
        $this->login = class_exists('WILogin') ? new WILogin() : null;
    }

    public function register($data): void
    {
        $user = $data['UserData'] ?? [];
        $errors = $this->validateUser($data);

        if (count($errors) > 0) {
            echo json_encode([
                'status' => 'error',
                'errors' => $errors
            ]);
            return;
        }

        $key = $this->generateKey();
        $confirmed = $this->mailConfirmationRequired() ? 'N' : 'Y';

        $this->WIdb->insert('wi_members', [
            'email' => trim((string) ($user['email'] ?? '')),
            'username' => trim(strip_tags((string) ($user['username'] ?? ''))),
            'password' => $this->hashPassword((string) ($user['password'] ?? '')),
            'confirmed' => $confirmed,
            'confirmation_key' => $key,
            'register_date' => date('Y-m-d H:i:s'),
            'ip_addr' => $_SERVER['REMOTE_ADDR'] ?? null
        ]);

        $userId = (int) $this->WIdb->lastInsertId();

        $folder = dirname(dirname(dirname(__FILE__))) . '/WIAdmin/WIMedia/Img/avator/' . $userId . '/';
        if (!is_dir($folder)) {
            @mkdir($folder, 0755, true);
        }

        if ($this->maint !== null) {
            $this->maint->LogFunction((string) ($user['username'] ?? ''), 'Added new user');
        }

        $this->WIdb->insert('wi_user_details', [
            'user_id' => $userId
        ]);

        $msg = WILang::get('success_registration_no_confirm');

        if ($this->mailConfirmationRequired()) {
            $this->mailer->confirmationEmail((string) ($user['email'] ?? ''), $key);
            $msg = WILang::get('success_registration_with_confirm');
        }

        echo json_encode([
            'status' => 'success',
            'msg' => $msg
        ]);
    }

    public function getByEmail($email)
    {
        $result = $this->WIdb->select(
            'SELECT * FROM `wi_members` WHERE `email` = :e',
            ['e' => trim((string) $email)]
        );

        return (count($result) > 0) ? $result[0] : [];
    }

    public function getBySocial($provider, $id)
    {
        $result = $this->WIdb->select(
            'SELECT * FROM `wi_social_logins` WHERE `provider` = :p AND `provider_id` = :id',
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
        $result = $this->getBySocial($provider, $id);
        return !empty($result);
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
            echo 'Invalid password reset key!';
            return;
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
        $password = (string) $password;
        $cost = $this->getPasswordCost();

        return password_hash($password, PASSWORD_BCRYPT, [
            'cost' => $cost
        ]);
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

        if (($info['algo'] ?? 0) === 0) {
            return true;
        }

        return password_needs_rehash($storedHash, PASSWORD_BCRYPT, [
            'cost' => $this->getPasswordCost()
        ]);
    }

    public function botProtection(): void
    {
        WISession::set('bot_first_number', random_int(1, 9));
        WISession::set('bot_second_number', random_int(1, 9));
    }

    public function validateUser($data, $botProtection = false): array
    {
        $id = $data['FieldId'] ?? [];
        $user = $data['UserData'] ?? [];
        $errors = [];
        $validator = new WIValidator();

        $email = trim((string) ($user['email'] ?? ''));
        $username = trim((string) ($user['username'] ?? ''));
        $password = (string) ($user['password'] ?? '');
        $confirmPassword = (string) ($user['confirm_password'] ?? '');

        if ($validator->isEmpty($email)) {
            $errors[] = ['id' => $id['email'] ?? 'email', 'msg' => WILang::get('email_required')];
        }

        if ($validator->isEmpty($username)) {
            $errors[] = ['id' => $id['username'] ?? 'username', 'msg' => WILang::get('username_required')];
        }

        if ($validator->isEmpty($password)) {
            $errors[] = ['id' => $id['password'] ?? 'password', 'msg' => WILang::get('password_required')];
        }

        if ($password !== $confirmPassword) {
            $errors[] = ['id' => $id['confirm_password'] ?? 'confirm_password', 'msg' => WILang::get('passwords_dont_match')];
        }

        if (!$validator->emailValid($email)) {
            $errors[] = ['id' => $id['email'] ?? 'email', 'msg' => WILang::get('email_wrong_format')];
        }

        if ($validator->emailExist($email)) {
            $errors[] = ['id' => $id['email'] ?? 'email', 'msg' => WILang::get('email_taken')];
        }

        if ($validator->usernameExist($username)) {
            $errors[] = ['id' => $id['username'] ?? 'username', 'msg' => WILang::get('username_taken')];
        }

        if ($botProtection) {
            $botOne = (int) WISession::get('bot_first_number', 0);
            $botTwo = (int) WISession::get('bot_second_number', 0);
            $sum = $botOne + $botTwo;

            if ($sum !== (int) ($user['bot_sum'] ?? 0)) {
                $errors[] = ['id' => $id['bot_sum'] ?? 'bot_sum', 'msg' => WILang::get('wrong_sum')];
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

    public function registerForm(): void
    {
        echo '<div class="col-sm-2"></div><div class="col-sm-8">';

        if ($this->login && $this->login->isLoggedIn()) {
            header('Location: index.php');
            exit;
        }

        echo '<div class="card bg-light">
        <article class="card-body mx-auto" style="max-width: 400px;">
            <h4 class="card-title mt-3 text-center">Create Account</h4>';

        if ($this->socialEnabled('twitter')) {
            echo '<a href="WICore/WIVendor/Hybridauth/index.php?p=twitter&token=' . WISession::get('WI_social_token') . '" class="btn btn-block btn-twitter"><i class="fa fa-twitter"></i> Login via Twitter</a>';
        }

        if ($this->socialEnabled('google')) {
            echo '<a href="WICore/WIVendor/Hybridauth/index.php?p=google&token=' . WISession::get('WI_social_token') . '" class="btn btn-block btn-googleplus"><i class="fa fa-googleplus"></i> Login via Google</a>';
        }

        if ($this->socialEnabled('facebook')) {
            echo '<a href="WICore/WIVendor/Hybridauth/index.php?p=facebook&token=' . WISession::get('WI_social_token') . '" class="btn btn-block btn-facebook"><i class="fa fa-facebook-f"></i> Login via Facebook</a>';
        }

        echo '</p>
        <p class="divider-text"><span class="bg-light">OR</span></p>
        <form class="form-horizontal register-form">
            <fieldset>
                <div class="control-group form-group">
                    <label class="control-label col-lg-2 col-md-2 col-sm-2 col-xs-2" for="reg-email">
                        <span class="input-group-text"><i class="fa fa-envelope" title="email"></i></span>
                    </label>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <input type="text" id="reg-email" class="input-xlarge form-control regular" placeholder="Email">
                    </div>
                </div>

                <div class="control-group form-group">
                    <label class="control-label col-lg-2 col-md-2 col-sm-2 col-xs-2" for="reg-username">
                        <span class="input-group-text"><i class="fa fa-user" title="user"></i></span>
                    </label>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <input type="text" id="reg-username" class="input-xlarge form-control regular" placeholder="Username">
                    </div>
                </div>

                <div class="control-group form-group">
                    <label class="control-label col-lg-2 col-md-2 col-sm-2 col-xs-2" for="reg-password">
                        <span class="input-group-text"><i class="fa fa-lock" title="password"></i></span>
                    </label>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <input type="password" id="reg-password" class="input-xlarge form-control regular" placeholder="Password">
                    </div>
                </div>

                <div class="control-group form-group">
                    <label class="control-label col-lg-2 col-md-2 col-sm-2 col-xs-2" for="reg-repeat-password">
                        <span class="input-group-text"><i class="fa fa-lock" title="repeat password"></i></span>
                    </label>
                    <div class="col-lg-8 col-md-8 col-sm-8 col-xs-8">
                        <input type="password" id="reg-repeat-password" class="input-xlarge form-control regular" placeholder="Repeat Password">
                    </div>
                </div>

                <div class="control-group form-group">
                    <div class="col-lg-12 col-md-8 col-sm-8 col-xs-8">
                        <button id="btn-register" class="btn btn-primary btn-block">' . WILang::get('create_account') . '</button>
                    </div>
                    <p id="regmess" class="text-center">Have an account? <a href="login.php">Log In</a></p>
                </div>
            </fieldset>
        </form>
        </article>
        </div>';
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

            $fromBcryptCost = (int) $settings->website('bcrypt_cost');
            if ($fromBcryptCost > 0) {
                return max(10, $fromBcryptCost);
            }

            $fromLegacyCost = (int) $settings->website('cost');
            if ($fromLegacyCost > 0) {
                return max(10, $fromLegacyCost);
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

    private function socialEnabled(string $provider): bool
    {
        $provider = strtolower($provider);

        $constantMap = [
            'twitter' => 'TWITTER_ENABLED',
            'google' => 'GOOGLE_ENABLED',
            'facebook' => 'FACEBOOK_ENABLED',
        ];

        if (isset($constantMap[$provider]) && defined($constantMap[$provider])) {
            return filter_var((string) constant($constantMap[$provider]), FILTER_VALIDATE_BOOLEAN);
        }

        try {
            $settings = new WISettings();
            return filter_var(
                (string) $settings->website($provider . '_enabled'),
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