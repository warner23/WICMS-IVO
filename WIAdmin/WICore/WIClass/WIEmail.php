<?php
declare(strict_types=1);

require_once dirname(dirname(__FILE__)) . '/WIVendor/phpmailer/PHPMailerAutoload.php';

/**
 * Email Service
 * WICMS Core
 */

final class WIEmail
{
    /**
     * Send confirmation email
     */
    public function confirmationEmail(string $email, string $key): bool
    {
        $mail = $this->getMailer();

        $mail->addAddress($email);

        $link = REGISTER_CONFIRM . '?k=' . $key;

        $template = dirname(dirname(__FILE__)) . '/WITemp/confirmation-mail.php';

        if (!file_exists($template)) {
            return false;
        }

        $body = file_get_contents($template);

        $body = str_replace('{{website_name}}', WEBSITE_NAME, $body);
        $body = str_replace('{{link}}', $link, $body);

        $mail->Subject = WEBSITE_NAME . ' - Registration Confirmation';
        $mail->Body = $body;

        return $this->sendMail($mail);
    }

    /**
     * Send password reset email
     */
    public function passwordResetEmail(string $email, string $key): bool
    {
        $mail = $this->getMailer();

        $mail->addAddress($email);

        $link = REGISTER_PASSWORD_RESET . '?k=' . $key;

        $template = dirname(dirname(__FILE__)) . '/WITemp/forgot-password-mail.php';

        if (!file_exists($template)) {
            return false;
        }

        $body = file_get_contents($template);

        $body = str_replace('{{website_name}}', WEBSITE_NAME, $body);
        $body = str_replace('{{link}}', $link, $body);

        $mail->Subject = WEBSITE_NAME . ' - Password Reset';
        $mail->Body = $body;

        return $this->sendMail($mail);
    }

    /**
     * Send email safely
     */
    private function sendMail(PHPMailer $mail): bool
    {
        try {

            if (!$mail->send()) {
                error_log('Mail error: ' . $mail->ErrorInfo);
                return false;
            }

            return true;

        } catch (Throwable $e) {

            error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Create PHPMailer instance
     */
    private function getMailer(): PHPMailer
    {
        $mail = new PHPMailer();

        if (defined('MAILER') && MAILER === 'smtp') {

            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->SMTPSecure = SMTP_ENCRYPTION;
            $mail->Port = SMTP_PORT;
        }

        $mail->isHTML(true);

        $email = 'noreply@' . str_replace(
            ['http://', 'https://'],
            '',
            WEBSITE_DOMAIN
        );

        $mail->From = $email;
        $mail->FromName = WEBSITE_NAME;
        $mail->addReplyTo($email, WEBSITE_NAME);

        $mail->CharSet = 'UTF-8';

        return $mail;
    }
}