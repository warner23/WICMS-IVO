<?php
/**
 * File Information
 *
 * Written By: Warner Infinity
 * Company: Warner Infinity
 * Product: WINotifications
 * Project: WI Ecosystem
 * File: WINotificationsAjax.php
 * Location: /WICore/WIAjax/WINotificationsAjax.php
 * Type: AJAX endpoint
 * Layer: Route / Request bridge
 * Purpose Area: Standalone WINotifications AJAX bridge
 * Version: 0.2.0-production-foundation
 * Created: 2026-06-03
 * Last Updated: 2026-06-03
 * Status: Production foundation
 * Summary: Provides a plugin-local AJAX endpoint where global WIAjax is not yet wired.
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

try {
    $root = dirname(__DIR__, 2);
    $classPath = $root . '/WIAdmin/WICore/WIClass/';

    require_once $classPath . 'WINotificationsRepository.php';
    require_once $classPath . 'WINotificationsService.php';
    require_once $classPath . 'WINotificationsAdminController.php';

    if (!isset($WIdb) && class_exists('WIdb')) {
        $WIdb = WIdb::getInstance();
    }

    if (!isset($WIdb) || !$WIdb instanceof WIdb) {
        throw new RuntimeException('WIdb is not available for WINotifications AJAX.');
    }

    $request = array_merge($_GET, $_POST);
    $action = (string)($request['action'] ?? 'winotifications_workspace');

    $controller = new WINotificationsAdminController(
        new WINotificationsService(new WINotificationsRepository($WIdb))
    );

    echo json_encode($controller->handle($action, $request), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'status' => 'error',
        'message' => 'WINotifications AJAX error.',
        'data' => [],
        'errors' => [$e->getMessage()],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}
