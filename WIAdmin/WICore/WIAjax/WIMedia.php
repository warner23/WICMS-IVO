<?php
declare(strict_types=1);

/**
 * FILE:
 * /WIAdmin/WICore/WIAjax/WIMedia.php
 *
 * Written By: Jules Warner
 * Company: WILabs
 * Product: WICMS / WICOS / WIKitchenCompli
 * Type: Legacy AJAX Compatibility Wrapper
 * Layer: Compatibility
 * Version: 2.0.0
 * Status: Compatibility
 *
 * Purpose:
 * - Keeps old direct WIMedia AJAX URL working.
 * - Routes all media actions through WIAjaxMedia.
 */

header('Content-Type: application/json; charset=utf-8');

$root = dirname(__DIR__);

require_once $root . '/WIClass/WI.php';
require_once $root . '/WIClass/WIMedia.php';
require_once $root . '/WIClass/WIAjaxMedia.php';

if (session_status() === PHP_SESSION_NONE) {
    if (class_exists('WISession')) {
        WISession::startSession();
    } else {
        session_start();
    }
}

/**
 * Emits JSON.
 *
 * @param array<string, mixed> $payload Payload.
 * @param int $statusCode HTTP status code.
 *
 * @return never
 */
function wiMediaLegacyJson(array $payload, int $statusCode = 200): never
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

/**
 * Resolves action.
 *
 * @return string
 */
function wiMediaLegacyAction(): string
{
    $action = trim((string)($_POST['action'] ?? ''));

    if ($action !== '') {
        return $action;
    }

    return trim((string)($_GET['action'] ?? ''));
}

try {
    $action = wiMediaLegacyAction();

    if ($action === '') {
        wiMediaLegacyJson([
            'success' => false,
            'status' => 'error',
            'message' => 'No action supplied.',
            'data' => [],
        ], 400);
    }

    $router = new WIAjaxMedia(new WIMedia(WIdb::getInstance()));
    $request = strtoupper((string)($_SERVER['REQUEST_METHOD'] ?? 'POST')) === 'GET'
        ? $_GET
        : $_POST;

    wiMediaLegacyJson(
        $router->handle($action, $request, $_FILES)
    );
} catch (Throwable $e) {
    wiMediaLegacyJson([
        'success' => false,
        'status' => 'error',
        'message' => defined('APP_DEBUG') && APP_DEBUG
            ? $e->getMessage()
            : 'System error.',
        'data' => [],
    ], 500);
}