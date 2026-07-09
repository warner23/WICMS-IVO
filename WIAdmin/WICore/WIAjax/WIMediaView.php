<?php
declare(strict_types=1);

/**
 * FILE:
 * /WIAdmin/WICore/WIAjax/WIMediaView.php
 *
 * Written By: Jules Warner
 * Company: WILabs
 * Product: WICMS / WICOS / WIKitchenCompli
 * Type: Media View/Download Endpoint
 * Layer: Shared AJAX Endpoint
 * Version: 1.0.0
 * Status: Active
 *
 * Purpose:
 * - Safely views/downloads media through WIMedia.
 * - Avoids exposing private media as raw public files.
 */

$root = dirname(__DIR__);

require_once $root . '/WIClass/WI.php';
require_once $root . '/WIClass/WIMedia.php';

if (session_status() === PHP_SESSION_NONE) {
    if (class_exists('WISession')) {
        WISession::startSession();
    } else {
        session_start();
    }
}

/**
 * Aborts the media endpoint.
 *
 * @param int $code Status code.
 * @param string $message Message.
 *
 * @return never
 */
function wiMediaViewAbort(int $code, string $message): never
{
    http_response_code($code);
    header('Content-Type: text/plain; charset=utf-8');
    echo $message;
    exit;
}

try {
    $mediaId = (int)($_GET['id'] ?? $_GET['media_id'] ?? 0);
    $mode = strtolower(trim((string)($_GET['mode'] ?? 'view')));

    if ($mediaId <= 0) {
        wiMediaViewAbort(400, 'A valid media ID is required.');
    }

    if (!in_array($mode, ['view', 'download'], true)) {
        wiMediaViewAbort(400, 'Invalid media delivery mode.');
    }

    $context = [
        'system_code' => trim((string)($_GET['system_code'] ?? '')),
        'entity_type' => trim((string)($_GET['entity_type'] ?? '')),
        'entity_id' => (int)($_GET['entity_id'] ?? 0),
        'link_type' => trim((string)($_GET['link_type'] ?? '')),
        'org_business_id' => (int)($_GET['org_business_id'] ?? $_GET['business_id'] ?? 0),
        'org_site_id' => (int)($_GET['org_site_id'] ?? $_GET['site_id'] ?? 0),
        'org_department_id' => (int)($_GET['org_department_id'] ?? $_GET['department_id'] ?? 0),
    ];

    $media = new WIMedia(WIdb::getInstance());
    $media->deliverMedia($mediaId, $mode, $context);
} catch (Throwable $e) {
    wiMediaViewAbort(
        500,
        defined('APP_DEBUG') && APP_DEBUG ? $e->getMessage() : 'Media delivery error.'
    );
}