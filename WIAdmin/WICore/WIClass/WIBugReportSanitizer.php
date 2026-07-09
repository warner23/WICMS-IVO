<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WI Shared Core
| Project: WI Ecosystem
| Class: WIBugReportSanitizer
| File: WIBugReportSanitizer.php
| Location: /WIAdmin/WICore/WIClass/
| Type: Shared Core Class
| Layer: Security / Privacy
| Purpose Area: Bug report sanitisation
| Version: 1.0.0
| Created: 2026-06-22
| Last Updated: 2026-06-22
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Sanitises bug report content before local storage and future WILabs
| forwarding. This class removes or redacts secrets, tokens, session values,
| password-like data, private path fragments and sensitive URL parameters.
*/

final class WIBugReportSanitizer
{
    /**
     * Sensitive key fragments that must not be stored or forwarded raw.
     *
     * @var array<int,string>
     */
    private array $sensitiveKeyFragments = [
        'password',
        'passwd',
        'pwd',
        'pass',
        'csrf',
        'token',
        'session',
        'cookie',
        'secret',
        'api_key',
        'apikey',
        'access_key',
        'private_key',
        'auth',
        'authorization',
        'bearer',
        'payment',
        'card',
        'cvv',
        'cvc',
        'stripe',
        'paypal',
        'db_pass',
        'db_user',
        'database_password',
        'mysql',
    ];

    public function sanitizeTitle(mixed $value): string
    {
        return $this->limit($this->sanitizeText($value), 255);
    }

    public function sanitizeDescription(mixed $value): string
    {
        return $this->limit($this->sanitizeText($value), 5000);
    }

    public function sanitizeScalar(mixed $value, int $maxLength = 255): string
    {
        return $this->limit($this->sanitizeText($value), $maxLength);
    }

    public function sanitizeUrl(mixed $value, int $maxLength = 500): string
    {
        $url = trim((string) $value);

        if ($url === '') {
            return '';
        }

        $url = str_replace(["\r", "\n", "\t"], '', $url);
        $parts = @parse_url($url);

        if (!is_array($parts)) {
            return $this->limit($this->sanitizeText($url), $maxLength);
        }

        $query = [];

        if (!empty($parts['query'])) {
            parse_str((string) $parts['query'], $query);

            foreach ($query as $key => $queryValue) {
                if ($this->isSensitiveKey((string) $key)) {
                    $query[$key] = '[redacted]';
                    continue;
                }

                if (is_array($queryValue)) {
                    unset($query[$key]);
                    continue;
                }

                $query[$key] = $this->sanitizeScalar((string) $queryValue, 120);
            }
        }

        $rebuilt = '';

        if (!empty($parts['scheme'])) {
            $rebuilt .= $parts['scheme'] . '://';
        }

        if (!empty($parts['host'])) {
            $rebuilt .= $parts['host'];
        }

        if (!empty($parts['port'])) {
            $rebuilt .= ':' . (int) $parts['port'];
        }

        if (!empty($parts['path'])) {
            $rebuilt .= $parts['path'];
        }

        if ($query !== []) {
            $rebuilt .= '?' . http_build_query($query);
        }

        return $this->limit($this->sanitizeText($rebuilt), $maxLength);
    }

    /**
     * Sanitise array payloads for future technical context / forwarding.
     *
     * @param array<string,mixed> $payload
     * @return array<string,mixed>
     */
    public function sanitizePayload(array $payload, int $depth = 0): array
    {
        if ($depth > 4) {
            return ['_truncated' => true];
        }

        $clean = [];

        foreach ($payload as $key => $value) {
            $safeKey = preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $key) ?: 'field';

            if ($this->isSensitiveKey((string) $key)) {
                $clean[$safeKey] = '[redacted]';
                continue;
            }

            if (is_array($value)) {
                $clean[$safeKey] = $this->sanitizePayload($value, $depth + 1);
                continue;
            }

            if (is_object($value)) {
                $clean[$safeKey] = '[object omitted]';
                continue;
            }

            $clean[$safeKey] = $this->sanitizeScalar($value, 500);
        }

        return $clean;
    }

    private function sanitizeText(mixed $value): string
    {
        if ($value === null || is_array($value) || is_object($value)) {
            return '';
        }

        $text = trim((string) $value);

        if ($text === '') {
            return '';
        }

        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text) ?? '';
        $text = $this->redactInlineSecrets($text);
        $text = $this->redactServerPaths($text);
        $text = strip_tags($text);

        return trim($text);
    }

    private function redactInlineSecrets(string $text): string
    {
        $patterns = [
            '/\b(password|passwd|pwd|pass)\s*[:=]\s*([^\s,;]+)/i',
            '/\b(csrf[_\-]?token|token|session[_\-]?id|phpsessid|cookie)\s*[:=]\s*([^\s,;]+)/i',
            '/\b(api[_\-]?key|secret|access[_\-]?token|refresh[_\-]?token|bearer)\s*[:=]\s*([^\s,;]+)/i',
            '/\b(db[_\-]?pass|database[_\-]?password|mysql[_\-]?password)\s*[:=]\s*([^\s,;]+)/i',
            '/Authorization:\s*Bearer\s+[A-Za-z0-9\.\-_]+/i',
        ];

        foreach ($patterns as $pattern) {
            $text = preg_replace($pattern, '$1=[redacted]', $text) ?? $text;
        }

        return $text;
    }

    private function redactServerPaths(string $text): string
    {
        $patterns = [
            '/[A-Z]:\\\\(?:[^\\\\\s]+\\\\)+[^\\\\\s]+/i',
            '#/(?:home|var|etc|mnt|root|srv|usr|opt)/[^\s]+#i',
        ];

        foreach ($patterns as $pattern) {
            $text = preg_replace($pattern, '[server-path-redacted]', $text) ?? $text;
        }

        return $text;
    }

    private function isSensitiveKey(string $key): bool
    {
        $key = strtolower(trim($key));

        foreach ($this->sensitiveKeyFragments as $fragment) {
            if ($fragment !== '' && str_contains($key, $fragment)) {
                return true;
            }
        }

        return false;
    }

    private function limit(string $value, int $maxLength): string
    {
        if ($maxLength <= 0) {
            return $value;
        }

        if (function_exists('mb_substr')) {
            return mb_substr($value, 0, $maxLength);
        }

        return substr($value, 0, $maxLength);
    }
}
