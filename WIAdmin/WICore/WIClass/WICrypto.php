<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WICrypto
| File: WICrypto.php
| Location: /WIAdmin/WICore/WIClass/WICrypto.php
| Type: Encryption Service
| Layer: Shared Core
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Canonical encryption service for sensitive data at rest.
|
| Rules:
| - No plaintext secret storage
| - Authenticated encryption only
| - Supports blind indexing for searchable sensitive values
|--------------------------------------------------------------------------
*/

final class WICrypto
{
    private static ?string $key = null;

    public static function init(): void
    {
        if (self::$key !== null) {
            return;
        }

        $rawKey = '';

        if (class_exists('WIConfig') && method_exists('WIConfig', 'getRequired')) {
            $rawKey = (string) WIConfig::getRequired('APP_KEY');
        } elseif (defined('APP_KEY')) {
            $rawKey = (string) APP_KEY;
        }

        if ($rawKey === '') {
            throw new RuntimeException('APP_KEY is not configured.');
        }

        self::$key = hash('sha256', $rawKey, true);
    }

    public static function encrypt(string $data): string
    {
        self::init();

        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);
        $cipher = sodium_crypto_secretbox(
            $data,
            $nonce,
            self::$key
        );

        return base64_encode($nonce . $cipher);
    }

    public static function decrypt(string $data): string
    {
        self::init();

        $decoded = base64_decode($data, true);

        if ($decoded === false) {
            throw new RuntimeException('Encrypted payload is not valid base64.');
        }

        $nonceLength = SODIUM_CRYPTO_SECRETBOX_NONCEBYTES;
        if (strlen($decoded) <= $nonceLength) {
            throw new RuntimeException('Encrypted payload is invalid.');
        }

        $nonce = substr($decoded, 0, $nonceLength);
        $cipher = substr($decoded, $nonceLength);

        $plain = sodium_crypto_secretbox_open(
            $cipher,
            $nonce,
            self::$key
        );

        if ($plain === false) {
            throw new RuntimeException('Decryption failed.');
        }

        return $plain;
    }

    public static function blindIndex(string $value, string $context = 'default'): string
    {
        self::init();

        $normalized = mb_strtolower(trim($value), 'UTF-8');
        $contextKey = hash_hmac('sha256', $context, self::$key, true);

        return hash_hmac('sha256', $normalized, $contextKey);
    }

    public static function hashValue(string $value, string $context = 'default'): string
    {
        self::init();

        return password_hash(
            $context . '|' . $value,
            PASSWORD_DEFAULT
        );
    }

    public static function verifyHashedValue(string $plainValue, string $storedHash, string $context = 'default'): bool
    {
        self::init();

        return password_verify($context . '|' . $plainValue, $storedHash);
    }

    public static function encryptNullable(?string $data): ?string
    {
        if ($data === null || $data === '') {
            return null;
        }

        return self::encrypt($data);
    }

    public static function decryptNullable(?string $data): ?string
    {
        if ($data === null || $data === '') {
            return null;
        }

        return self::decrypt($data);
    }
}