<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WILogger
| File: WILogger.php
| Location: /WIAdmin/WICore/WIClass/WILogger.php
| Type: Logger Service
| Layer: Shared Core
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Structured file logger for system, security, compliance, and admin events.
|--------------------------------------------------------------------------
*/

final class WILogger
{
    private const DEFAULT_CHANNEL = 'system';

    public static function info(string $message, array $context = [], string $channel = self::DEFAULT_CHANNEL): void
    {
        self::write('INFO', $message, $context, $channel);
    }

    public static function warning(string $message, array $context = [], string $channel = self::DEFAULT_CHANNEL): void
    {
        self::write('WARNING', $message, $context, $channel);
    }

    public static function error(string $message, array $context = [], string $channel = self::DEFAULT_CHANNEL): void
    {
        self::write('ERROR', $message, $context, $channel);
    }

    public static function security(string $message, array $context = [], string $channel = 'security'): void
    {
        self::write('SECURITY', $message, $context, $channel);
    }

    private static function write(string $level, string $message, array $context, string $channel): void
    {
        $logDir = self::getLogDirectory();

        if (!is_dir($logDir) && !mkdir($logDir, 0775, true) && !is_dir($logDir)) {
            return;
        }

        $filePath = $logDir . DIRECTORY_SEPARATOR . self::sanitizeChannel($channel) . '-' . date('Y-m-d') . '.log';

        $entry = [
            'timestamp'  => date('Y-m-d H:i:s'),
            'level'      => $level,
            'channel'    => $channel,
            'message'    => $message,
            'context'    => self::normalizeContext($context),
            'ip'         => $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'user_id'    => class_exists('WISession') ? WISession::get('user_id', null) : null,
        ];

        $line = json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        if ($line !== false) {
            file_put_contents($filePath, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
        }
    }

    private static function getLogDirectory(): string
    {
        if (class_exists('WIConfig') && method_exists('WIConfig', 'get')) {
            $configured = WIConfig::get('LOG_PATH');

            if (is_string($configured) && $configured !== '') {
                return rtrim($configured, '/\\');
            }
        }

        return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'logs';
    }

    private static function sanitizeChannel(string $channel): string
    {
        $channel = strtolower(trim($channel));
        $channel = preg_replace('/[^a-z0-9_\-]/', '-', $channel);

        return $channel !== '' ? $channel : self::DEFAULT_CHANNEL;
    }

    private static function normalizeContext(array $context): array
    {
        array_walk_recursive($context, static function (&$value): void {
            if (is_object($value)) {
                $value = method_exists($value, '__toString') ? (string) $value : get_class($value);
                return;
            }

            if (is_resource($value)) {
                $value = 'resource';
                return;
            }

            if (is_bool($value) || is_int($value) || is_float($value) || is_string($value) || $value === null) {
                return;
            }

            $encoded = json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            $value = $encoded !== false ? $encoded : 'unserializable';
        });

        return $context;
    }
}