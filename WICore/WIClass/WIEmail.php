<?php
declare(strict_types=1);

require_once dirname(dirname(__FILE__)) . '/WIVendor/phpmailer/PHPMailerAutoload.php';

/**
 * Class for sending emails.
 */
#[\AllowDynamicProperties]
class WIEmail
{
    public function confirmationEmail($email, $key): bool
    {
        $mail = $this->_getMailer();
        $email = trim((string)$email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mail->addAddress($email);

        $link = REGISTER_CONFIRM . "?k=" . urlencode((string)$key);
        $body = $this->loadTemplate('confirmation-mail.php', [
            '{{website_name}}' => WEBSITE_NAME,
            '{{link}}'         => $link
        ]);

        $mail->Subject = WEBSITE_NAME . " - Registration Confirmation";
        $mail->Body    = $body;

        return $this->sendMailer($mail);
    }

    public function confirmationAppointmentEmail($email, $docReceipt): bool
    {
        $mail = $this->_getMailer();
        $email = trim((string)$email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mail->addAddress($email);
        $mail->Subject = WEBSITE_NAME . " - Purchase Confirmation";
        $mail->Body    = (string)$docReceipt;

        return $this->sendMailer($mail);
    }

    public function passwordResetEmail($email, $key): bool
    {
        $mail = $this->_getMailer();
        $email = trim((string)$email);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mail->addAddress($email);

        $resetUrl = WEBSITE_DOMAIN . REGISTER_PASSWORD_RESET . '?k=' . urlencode((string)$key);
        $link = '<a href="' . htmlspecialchars($resetUrl, ENT_QUOTES, 'UTF-8') . '">Reset Password</a>';

        $body = $this->loadTemplate('forgot-password-mail.php', [
            '{{website_name}}' => WEBSITE_NAME,
            '{{linkage}}'      => $link
        ]);

        $mail->Subject = WEBSITE_NAME . " - Password Reset";
        $mail->Body    = $body;

        return $this->sendMailer($mail);
    }

    public function contactEmail($email, $name, $subject, $message): bool
    {
        $mail = $this->_getMailer();
        $endAddress = trim((string)CONTACT_EMAIL);

        if (!filter_var($endAddress, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $mail->addAddress($endAddress);

        $body = $this->loadTemplate('contact-us.php', [
            '{{name}}'         => htmlspecialchars((string)$name, ENT_QUOTES, 'UTF-8'),
            '{{subject}}'      => htmlspecialchars((string)$subject, ENT_QUOTES, 'UTF-8'),
            '{{message}}'      => nl2br(htmlspecialchars((string)$message, ENT_QUOTES, 'UTF-8')),
            '{{from}}'         => htmlspecialchars((string)$email, ENT_QUOTES, 'UTF-8'),
            '{{website_name}}' => WEBSITE_NAME
        ]);

        $mail->Subject = WEBSITE_NAME . " - Someone has sent Contact Mail";
        $mail->Body    = $body;

        return $this->sendMailer($mail);
    }

    private function _getMailer()
    {
        $mail = new PHPMailer();

        if (MAILER === 'smtp') {
            $mail->isSMTP();
            $mail->Host       = SMTP_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = SMTP_USERNAME;
            $mail->Password   = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_ENCRYPTION;
            $mail->Port       = (int)SMTP_PORT;
        }

        $mail->isHTML(true);

        $domain = preg_replace('#^https?://#', '', (string)WEBSITE_DOMAIN);
        $domain = trim((string)$domain, '/');
        $fromEmail = 'noreply@' . $domain;

        if (!filter_var($fromEmail, FILTER_VALIDATE_EMAIL)) {
            $fromEmail = CONTACT_EMAIL;
        }

        $mail->From     = $fromEmail;
        $mail->FromName = WEBSITE_NAME;
        $mail->addReplyTo($fromEmail, WEBSITE_NAME);
        $mail->CharSet = 'UTF-8';

        return $mail;
    }

    private function loadTemplate(string $filename, array $replacements = []): string
    {
        $path = dirname(dirname(__FILE__)) . '/WITemp/' . $filename;

        if (!file_exists($path)) {
            return '';
        }

        $body = (string)file_get_contents($path);

        foreach ($replacements as $search => $replace) {
            $body = str_replace($search, (string)$replace, $body);
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
?>
