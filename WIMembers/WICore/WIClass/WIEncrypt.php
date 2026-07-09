<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: WIEncrypt.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Class
 * Layer: Security
 * Purpose Area: Member workspace, profile, account, settings, payments, forms and training
 * Version: 1.0.0
 * Created: 2026-05-24
 * Last Updated: 2026-05-24
 * Status: Refactored
 * Summary: WICMS-compatible WIMembers modernisation. Keeps WI-prefixed classes, database-driven modules, sessions and page rendering.
 */


class WIEncrypt
{
    private const CIPHER = 'aes-256-gcm';

    public static function encrypt(?string $plainText): ?string
    {
        if ($plainText === null || $plainText === '') {
            return $plainText;
        }

        $key = self::key();
        $iv = random_bytes(12);
        $tag = '';
        $cipher = openssl_encrypt($plainText, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv, $tag);

        if ($cipher === false) {
            throw new RuntimeException('Encryption failed.');
        }

        return base64_encode(json_encode([
            'v' => 1,
            'iv' => base64_encode($iv),
            'tag' => base64_encode($tag),
            'data' => base64_encode($cipher),
        ], JSON_THROW_ON_ERROR));
    }

    public static function decrypt(?string $payload): ?string
    {
        if ($payload === null || $payload === '') {
            return $payload;
        }

        $decoded = json_decode(base64_decode($payload, true) ?: '', true);
        if (!is_array($decoded) || !isset($decoded['iv'], $decoded['tag'], $decoded['data'])) {
            return $payload;
        }

        $plain = openssl_decrypt(
            base64_decode((string) $decoded['data'], true),
            self::CIPHER,
            self::key(),
            OPENSSL_RAW_DATA,
            base64_decode((string) $decoded['iv'], true),
            base64_decode((string) $decoded['tag'], true)
        );

        return $plain === false ? null : $plain;
    }

    public static function blindIndex(string $value, string $context = 'member'): string
    {
        return hash_hmac('sha256', mb_strtolower(trim($value)), self::key() . '|' . $context);
    }

    private static function key(): string
    {
        $raw = '';
        if (defined('WI_ENCRYPTION_KEY') && trim((string) WI_ENCRYPTION_KEY) !== '') {
            $raw = (string) WI_ENCRYPTION_KEY;
        } elseif (defined('SECRET_KEY') && trim((string) SECRET_KEY) !== '') {
            $raw = (string) SECRET_KEY;
        } elseif (defined('PASSWORD_ENCRYPTION') && trim((string) PASSWORD_ENCRYPTION) !== '') {
            $raw = (string) PASSWORD_ENCRYPTION;
        } else {
            $raw = 'change-this-wimembers-encryption-key';
        }

        return hash('sha256', $raw, true);
    }
}
