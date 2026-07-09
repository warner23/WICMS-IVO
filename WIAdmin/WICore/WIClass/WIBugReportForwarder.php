<?php
declare(strict_types=1);

require_once dirname(dirname(__FILE__)) . '/WIVendor/phpmailer/PHPMailerAutoload.php';

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WI Shared Core
| Project: WI Ecosystem
| Class: WIBugReportForwarder
| File: WIBugReportForwarder.php
| Location: /WIAdmin/WICore/WIClass/
| Type: Shared Core Class
| Layer: Support Forwarding Service
| Purpose Area: Optional WILabs bug report forwarding
| Version: 1.0.0
| Created: 2026-06-22
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Optional forwarding service for WIBugReporter. Local bug reports are always
| saved first. This class only sends a sanitised summary to WILabs when the
| site owner has enabled forwarding and configured a support email address.
*/

final class WIBugReportForwarder
{
    private WIdb $WIdb;
    private WIBugReporterSettings $settings;
    private WIBugReportSanitizer $sanitizer;

    private string $reportTable = 'wi_system_bug_reports';

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->settings = new WIBugReporterSettings();
        $this->sanitizer = new WIBugReportSanitizer();
    }

    /**
     * Forward a report if forwarding is enabled.
     *
     * @param int $reportId Local report ID.
     * @param array<string,mixed> $report Report row plus optional messages.
     * @param array<int,array<string,mixed>> $messages Report messages/thread.
     * @return array{success:bool,status:string,message:string,forwarded?:bool}
     */
    public function forwardIfEnabled(int $reportId, array $report, array $messages = []): array
    {
        if (!$this->isForwardingEnabled()) {
            $this->markNotEnabled($reportId);

            return [
                'success' => true,
                'status' => 'not_enabled',
                'message' => 'WILabs forwarding is disabled.',
                'forwarded' => false,
            ];
        }

        return $this->forwardNow($reportId, $report, $messages);
    }

    /**
     * Forward a report now, used by automatic forwarding and admin retry.
     *
     * @param int $reportId Local report ID.
     * @param array<string,mixed> $report Report row plus optional messages.
     * @param array<int,array<string,mixed>> $messages Report messages/thread.
     * @return array{success:bool,status:string,message:string,forwarded?:bool}
     */
    public function forwardNow(int $reportId, array $report, array $messages = []): array
    {
        if ($reportId <= 0) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Invalid bug report id.',
                'forwarded' => false,
            ];
        }

        if (!$this->isForwardingEnabled()) {
            $this->markNotEnabled($reportId);

            return [
                'success' => false,
                'status' => 'not_enabled',
                'message' => 'WILabs forwarding is disabled.',
                'forwarded' => false,
            ];
        }

        $supportEmail = $this->settings->getString('wilabs_support_email', '');

        if (!filter_var($supportEmail, FILTER_VALIDATE_EMAIL)) {
            $this->markFailed($reportId, 'WILabs support email is missing or invalid.');

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'WILabs support email is missing or invalid.',
                'forwarded' => false,
            ];
        }

        $this->markPending($reportId);

        $mail = $this->mailer();
        $mail->addAddress($supportEmail);
        $mail->Subject = $this->buildSubject($reportId, $report);
        $mail->Body = $this->buildHtmlBody($reportId, $report, $messages);
        $mail->AltBody = $this->buildTextBody($reportId, $report, $messages);

        try {
            if (!$mail->send()) {
                $this->markFailed($reportId, $mail->ErrorInfo ?: 'Mailer failed without details.');

                return [
                    'success' => false,
                    'status' => 'failed',
                    'message' => 'Unable to forward bug report to WILabs.',
                    'forwarded' => false,
                ];
            }
        } catch (Throwable $e) {
            $this->markFailed($reportId, $e->getMessage());

            return [
                'success' => false,
                'status' => 'failed',
                'message' => 'Unable to forward bug report to WILabs.',
                'forwarded' => false,
            ];
        }

        $this->markSent($reportId);

        return [
            'success' => true,
            'status' => 'sent',
            'message' => 'Bug report forwarded to WILabs support.',
            'forwarded' => true,
        ];
    }

    private function isForwardingEnabled(): bool
    {
        $enabled = $this->settings->getBool('forwarding_enabled', false);
        $mode = strtolower($this->settings->getString('forwarding_mode', 'local_only'));

        return $enabled && $mode === 'email';
    }

    /** @param array<string,mixed> $report */
    private function buildSubject(int $reportId, array $report): string
    {
        $prefix = $this->settings->getString('wilabs_forward_subject_prefix', '[WIBugReporter]');
        $severity = strtoupper($this->safe($report['severity'] ?? 'medium', 30));
        $title = $this->safe($report['title'] ?? 'Untitled report', 90);

        return trim($prefix . ' ' . $severity . ' #' . $reportId . ' - ' . $title);
    }

    /**
     * @param array<string,mixed> $report
     * @param array<int,array<string,mixed>> $messages
     */
    private function buildHtmlBody(int $reportId, array $report, array $messages): string
    {
        $rows = [
            'Local report ID' => '#' . $reportId,
            'Website' => defined('WEBSITE_NAME') ? (string) WEBSITE_NAME : 'WICMS site',
            'Website domain' => defined('WEBSITE_DOMAIN') ? (string) WEBSITE_DOMAIN : '',
            'Severity' => $this->safe($report['severity'] ?? 'medium'),
            'Area' => $this->safe($report['area_type'] ?? ''),
            'Module' => $this->safe($report['module_name'] ?? ''),
            'Page' => $this->safe($report['page_key'] ?? ''),
            'URL' => $this->sanitizer->sanitizeUrl($report['page_url'] ?? ''),
            'Browser' => $this->safe($report['browser'] ?? ''),
        ];

        if ($this->settings->getBool('forwarding_include_user_agent', false)) {
            $rows['User agent'] = $this->safe($report['user_agent'] ?? '', 1000);
        }

        if ($this->settings->getBool('forwarding_include_ip_address', false)) {
            $rows['IP address'] = '[disabled by privacy default]';
        }

        $html = '<h2>WIBugReporter forwarded report</h2>';
        $html .= '<p>This is a sanitised support copy. Local customer data, passwords, tokens, cookies, payment data, full form contents, documents and evidence files are not included automatically.</p>';
        $html .= '<table cellpadding="8" cellspacing="0" border="1" style="border-collapse:collapse;">';

        foreach ($rows as $label => $value) {
            $html .= '<tr><th align="left">' . $this->e($label) . '</th><td>' . $this->e($value) . '</td></tr>';
        }

        $html .= '</table>';
        $html .= '<h3>Title</h3><p>' . $this->e($this->safe($report['title'] ?? '')) . '</p>';
        $html .= '<h3>Description</h3><p>' . nl2br($this->e($this->safe($report['description'] ?? '', 5000))) . '</p>';

        if ($messages !== []) {
            $html .= '<h3>Thread summary</h3>';

            foreach ($messages as $message) {
                $type = $this->safe($message['message_type'] ?? 'comment', 50);
                $body = $this->safe($message['message'] ?? '', 2000);
                $html .= '<p><strong>' . $this->e($type) . ':</strong><br>' . nl2br($this->e($body)) . '</p>';
            }
        }

        return $html;
    }

    /**
     * @param array<string,mixed> $report
     * @param array<int,array<string,mixed>> $messages
     */
    private function buildTextBody(int $reportId, array $report, array $messages): string
    {
        $lines = [
            'WIBugReporter forwarded report',
            'This is a sanitised support copy.',
            '',
            'Local report ID: #' . $reportId,
            'Website: ' . (defined('WEBSITE_NAME') ? (string) WEBSITE_NAME : 'WICMS site'),
            'Domain: ' . (defined('WEBSITE_DOMAIN') ? (string) WEBSITE_DOMAIN : ''),
            'Severity: ' . $this->safe($report['severity'] ?? 'medium'),
            'Area: ' . $this->safe($report['area_type'] ?? ''),
            'Module: ' . $this->safe($report['module_name'] ?? ''),
            'Page: ' . $this->safe($report['page_key'] ?? ''),
            'URL: ' . $this->sanitizer->sanitizeUrl($report['page_url'] ?? ''),
            'Browser: ' . $this->safe($report['browser'] ?? ''),
            '',
            'Title:',
            $this->safe($report['title'] ?? ''),
            '',
            'Description:',
            $this->safe($report['description'] ?? '', 5000),
        ];

        if ($messages !== []) {
            $lines[] = '';
            $lines[] = 'Thread summary:';

            foreach ($messages as $message) {
                $lines[] = '- ' . $this->safe($message['message_type'] ?? 'comment', 50) . ': ' . $this->safe($message['message'] ?? '', 2000);
            }
        }

        return implode("\n", $lines);
    }

    private function mailer(): PHPMailer
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
        $mail->From = $this->fromEmail();
        $mail->FromName = defined('WEBSITE_NAME') ? (string) WEBSITE_NAME : 'WICMS';
        $mail->addReplyTo($mail->From, $mail->FromName);
        $mail->CharSet = 'UTF-8';

        return $mail;
    }

    private function fromEmail(): string
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

        if (defined('CONTACT_EMAIL') && filter_var((string) CONTACT_EMAIL, FILTER_VALIDATE_EMAIL)) {
            return (string) CONTACT_EMAIL;
        }

        return 'noreply@example.com';
    }

    private function markNotEnabled(int $reportId): void
    {
        $this->updateForwardingStatus($reportId, [
            'forward_to_wilabs' => 0,
            'forwarding_status' => 'not_enabled',
            'forwarding_error' => null,
        ]);
    }

    private function markPending(int $reportId): void
    {
        $this->updateForwardingStatus($reportId, [
            'forward_to_wilabs' => 1,
            'forwarding_status' => 'pending',
            'forwarding_error' => null,
        ], true);
    }

    private function markSent(int $reportId): void
    {
        $this->updateForwardingStatus($reportId, [
            'forward_to_wilabs' => 1,
            'forwarding_status' => 'sent',
            'forwarded_at' => date('Y-m-d H:i:s'),
            'forwarding_error' => null,
        ]);
    }

    private function markFailed(int $reportId, string $error): void
    {
        $this->updateForwardingStatus($reportId, [
            'forward_to_wilabs' => 1,
            'forwarding_status' => 'failed',
            'forwarding_error' => $this->safe($error, 500),
        ], true);
    }

    /** @param array<string,mixed> $data */
    private function updateForwardingStatus(int $reportId, array $data, bool $incrementAttempts = false): void
    {
        if ($reportId <= 0) {
            return;
        }

        if ($incrementAttempts) {
            $stmt = $this->WIdb->prepare(
                'UPDATE `' . $this->reportTable . '`
                 SET `forwarding_attempts` = `forwarding_attempts` + 1
                 WHERE `id` = :id'
            );
            $stmt->bindValue(':id', $reportId, PDO::PARAM_INT);
            $stmt->execute();
            $stmt->closeCursor();
        }

        $this->WIdb->update(
            $this->reportTable,
            $data,
            '`id` = :id',
            ['id' => $reportId]
        );
    }

    private function safe(mixed $value, int $limit = 255): string
    {
        return $this->sanitizer->sanitizeScalar($value, $limit);
    }

    private function e(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}
