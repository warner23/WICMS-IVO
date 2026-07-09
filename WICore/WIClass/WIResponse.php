<?php
declare(strict_types=1);

/**
 * FILE:
 * WICMS-IVO/WICore/WIClass/WIResponse.php
 *
 * Canonical response helper for WICMS.
 */
final class WIResponse
{
    public static function json(array $data, int $statusCode = 200): never
    {
        self::jsonHeaders();
        http_response_code($statusCode);

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        exit;
    }

    public static function success(array $data = [], int $statusCode = 200): never
    {
        self::json(array_merge([
            'status' => 'success',
            'message' => 'OK',
            'errors' => [],
        ], $data), $statusCode);
    }

    public static function error(string $message, int $statusCode = 400, array $errors = []): never
    {
        self::json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    public static function html(string $content, int $statusCode = 200): never
    {
        if (!headers_sent()) {
            header('Content-Type: text/html; charset=UTF-8');
            header('X-Content-Type-Options: nosniff');
        }

        http_response_code($statusCode);
        echo $content;
        exit;
    }

    public static function redirect(string $url, int $statusCode = 302): never
    {
        if (!headers_sent()) {
            header('Location: ' . $url, true, $statusCode);
        }

        exit;
    }

    public static function noContent(): never
    {
        http_response_code(204);
        exit;
    }

    private static function jsonHeaders(): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8');
            header('X-Content-Type-Options: nosniff');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
        }
    }
}