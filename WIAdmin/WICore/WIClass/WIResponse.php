<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WIResponse
| File: WIResponse.php
| Location: /WIAdmin/WICore/WIClass/WIResponse.php
| Type: Core Response Helper
| Layer: Shared Core
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Canonical response helper with backward-compatible support for both the
| older WIResponse usage style and the newer structured JSON style.
|--------------------------------------------------------------------------
*/

final class WIResponse
{
    public static function json(array $data, int $statusCode = 200): never
    {
        self::setStatusCode($statusCode);
        self::setJsonHeaders();

        echo json_encode(
            $data,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        exit;
    }

    public static function success(string|array $message = 'Success', array $data = [], int $statusCode = 200): never
    {
        if (is_array($message) && $data === []) {
            $payload = $message;

            if (!isset($payload['status'])) {
                $payload['status'] = 'success';
            }

            if (!isset($payload['message'])) {
                $payload['message'] = 'Success';
            }

            self::json($payload, $statusCode);
        }

        self::json([
            'status'  => 'success',
            'message' => $message,
            'data'    => $data,
        ], $statusCode);
    }

    public static function error(string $message = 'Error', array|int $errors = [], int $statusCode = 400): never
    {
        if (is_int($errors)) {
            $statusCode = $errors;
            $errors = [];
        }

        self::json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode);
    }

    public static function validationError(array $errors, string $message = 'Validation failed', int $statusCode = 422): never
    {
        self::json([
            'status'  => 'error',
            'message' => $message,
            'errors'  => $errors,
        ], $statusCode);
    }

    public static function unauthorized(string $message = 'Unauthorized'): never
    {
        self::error($message, [], 401);
    }

    public static function forbidden(string $message = 'Forbidden'): never
    {
        self::error($message, [], 403);
    }

    public static function notFound(string $message = 'Not found'): never
    {
        self::error($message, [], 404);
    }

    public static function serverError(string $message = 'Server error', array $errors = []): never
    {
        self::error($message, $errors, 500);
    }

    public static function redirect(string $url, int $statusCode = 302): never
    {
        self::setStatusCode($statusCode);
        header('Location: ' . $url);
        exit;
    }

    public static function text(string $content, int $statusCode = 200): never
    {
        self::setStatusCode($statusCode);

        if (!headers_sent()) {
            header('Content-Type: text/plain; charset=UTF-8');
        }

        echo $content;
        exit;
    }

    public static function html(string $content, int $statusCode = 200): never
    {
        self::setStatusCode($statusCode);

        if (!headers_sent()) {
            header('Content-Type: text/html; charset=UTF-8');
        }

        echo $content;
        exit;
    }

    private static function setJsonHeaders(): void
    {
        if (!headers_sent()) {
            header('Content-Type: application/json; charset=UTF-8');
            header('X-Content-Type-Options: nosniff');
            header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
            header('Pragma: no-cache');
        }
    }

    private static function setStatusCode(int $statusCode): void
    {
        if (!headers_sent()) {
            http_response_code($statusCode);
        }
    }
}