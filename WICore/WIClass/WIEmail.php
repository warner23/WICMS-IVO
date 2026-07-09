<?php
declare(strict_types=1);

require_once dirname(dirname(__FILE__)) . '/WIVendor/phpmailer/PHPMailerAutoload.php';

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS
| Project: WI Ecosystem
| File: WIEmail.php
| Location: /WICore/WIClass/
| Type: PHP Class
| Layer: Shared Core Service
| Purpose Area: Core email delivery
| Version: 2.1.0
| Created: Legacy
| Last Updated: 2026-06-21
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Shared WICMS email service for registration confirmation, password reset,
| contact messages and other core system emails. Builds safe public URLs and
| keeps email sending generic so plugins can use the service without owning it.
*/

#[\AllowDynamicProperties]
class WIEmail
{
    public function confirmationEmail($email, $key): bool
    {
        $mail = $this->_getMailer();
        $email = trim((string) $email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $confirmUrl = $this->buildPublicUrl((string) REGISTER_CONFIRM, [
            'k' => (string) $key,
        ]);

        $mail->addAddress($email);

        $body = $this->loadTemplate('confirmation-mail.php', [
            '{{website_name}}' => WEBSITE_NAME,
            '{{link}}' => $confirmUrl,
            '{{url}}' => $confirmUrl,
        ]);

        $mail->Subject = WEBSITE_NAME . ' - Registration Confirmation';
        $mail->Body = $body;

        return $this->sendMailer($mail);
    }

    public function confirmationAppointmentEmail($email, $docReceipt): bool
    {
        $mail = $this->_getMailer();
        $email = trim((string) $email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mail->addAddress($email);
        $mail->Subject = WEBSITE_NAME . ' - Purchase Confirmation';
        $mail->Body = (string) $docReceipt;

        return $this->sendMailer($mail);
    }

    public function passwordResetEmail($email, $key): bool
    {
        $mail = $this->_getMailer();
        $email = trim((string) $email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $resetUrl = $this->buildPublicUrl((string) REGISTER_PASSWORD_RESET, [
            'k' => (string) $key,
        ]);

        $resetLink = '<a href="' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '">Reset Password</a>';

        $mail->addAddress($email);

        $body = $this->loadTemplate('forgot-password-mail.php', [
            '{{website_name}}' => WEBSITE_NAME,
            '{{linkage}}' => $resetLink,
            '{{url}}' => htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8'),
        ]);

        $mail->Subject = WEBSITE_NAME . ' - Password Reset';
        $mail->Body = $body;

        return $this->sendMailer($mail);
    }

    public function contactEmail($email, $name, $subject, $message): bool
    {
        $mail = $this->_getMailer();
        $endAddress = trim((string) CONTACT_EMAIL);

        if (!filter_var($endAddress, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mail->addAddress($endAddress);

        $body = $this->loadTemplate('contact-us.php', [
            '{{name}}' => htmlspecialchars((string) $name, ENT_QUOTES, 'UTF-8'),
            '{{subject}}' => htmlspecialchars((string) $subject, ENT_QUOTES, 'UTF-8'),
            '{{message}}' => nl2br(htmlspecialchars((string) $message, ENT_QUOTES, 'UTF-8')),
            '{{from}}' => htmlspecialchars((string) $email, ENT_QUOTES, 'UTF-8'),
            '{{website_name}}' => WEBSITE_NAME,
        ]);

        $mail->Subject = WEBSITE_NAME . ' - Someone has sent Contact Mail';
        $mail->Body = $body;

        return $this->sendMailer($mail);
    }

    private function _getMailer()
    {
        $mail = new PHPMailer();

        if (defined('MAILER') && MAILER === 'smtp') {
            $mail->isSMTP();
            $mail->Host = (string) SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = (string) SMTP_USERNAME;
            $mail->Password = (string) SMTP_PASSWORD;
            $mail->SMTPSecure = (string) SMTP_ENCRYPTION;
            $mail->Port = (int) SMTP_PORT;
        }

        $mail->isHTML(true);

        $fromEmail = $this->buildFromEmail();

        $mail->From = $fromEmail;
        $mail->FromName = WEBSITE_NAME;
        $mail->addReplyTo($fromEmail, WEBSITE_NAME);
        $mail->CharSet = 'UTF-8';

        return $mail;
    }

    private function buildPublicUrl(string $path, array $query = []): string
    {
        $path = trim($path);

        if ($path === '') {
            $path = 'index.php';
        }

        if (preg_match('#^https?://#i', $path) === 1) {
            $url = $path;
        } else {
            $baseUrl = $this->normaliseBaseUrl(defined('WEBSITE_DOMAIN') ? (string) WEBSITE_DOMAIN : '');
            $url = $baseUrl . ltrim($path, '/');
        }

        if ($query !== []) {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator . http_build_query($query);
        }

        return $url;
    }

    private function normaliseBaseUrl(string $baseUrl): string
    {
        $baseUrl = trim($baseUrl);

        if ($baseUrl === '') {
            return '/';
        }

        return rtrim($baseUrl, '/') . '/';
    }

    private function buildFromEmail(): string
    {
        $domain = '';

        if (defined('WEBSITE_DOMAIN')) {
            $host = parse_url((string) WEBSITE_DOMAIN, PHP_URL_HOST);
            $domain = is_string($host) ? trim($host) : '';
        }

        if ($domain !== '') {
            $candidate = 'noreply@' . $domain;

            if (filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                return $candidate;
            }
        }

        $contactEmail = defined('CONTACT_EMAIL') ? trim((string) CONTACT_EMAIL) : '';

        if (filter_var($contactEmail, FILTER_VALIDATE_EMAIL)) {
            return $contactEmail;
        }

        return 'noreply@example.com';
    }

    private function loadTemplate(string $filename, array $replacements = []): string
    {
        $path = dirname(dirname(__FILE__)) . '/WITemp/' . $filename;

        if (!is_file($path)) {
            return '';
        }

        $body = (string) file_get_contents($path);

        foreach ($replacements as $search => $replace) {
            $body = str_replace($search, (string) $replace, $body);
        }

        return $body;
    }

    private function sendMailer($mail): bool
    {
        if (!$mail->send()) {
            error_log('Mailer Error: ' . $mail->ErrorInfo);
            return false;
        }

        return true;
    }
}