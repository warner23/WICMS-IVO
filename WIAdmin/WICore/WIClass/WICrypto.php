<?php
declare(strict_types=1);

/**
 * WICMS Encryption Service
 */

 final class WICrypto
{
    private static string $key;

    public static function init(): void
    {
        $key = WIConfig::getRequired('APP_KEY');

        if (strlen($key) < 32) {
            throw new RuntimeException('Encryption key too short.');
        }

        self::$key = hash('sha256', $key, true);
    }

    public static function encrypt(string $data): string
    {
        $nonce = random_bytes(24);

        $cipher = sodium_crypto_secretbox(
            $data,
            $nonce,
            self::$key
        );

        return base64_encode($nonce . $cipher);
    }

    public static function decrypt(string $data): string
    {
        $decoded = base64_decode($data);

        $nonce = substr($decoded, 0, 24);
        $cipher = substr($decoded, 24);

        $plain = sodium_crypto_secretbox_open(
            $cipher,
            $nonce,
            self::$key
        );

        if ($plain === false) {
            throw new RuntimeException("Decryption failed");
        }

        return $plain;
    }
}