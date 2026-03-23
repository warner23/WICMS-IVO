<?php

declare(strict_types=1);

require_once __DIR__ . '/WI.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? '';
$compliance = new WICompliance();

try {
    switch ($action) {
        case 'dashboardSummary':
            respond(['status' => 'success', 'data' => $compliance->dashboardSummary()]);
            break;

        case 'saveSetup':
            respond($compliance->saveSetup($_POST));
            break;

        case 'addChecklist':
            respond($compliance->addChecklist($_POST));
            break;

        case 'addLegalItem':
            respond($compliance->addLegalItem($_POST));
            break;

        case 'addReminder':
            respond($compliance->addReminder($_POST));
            break;

        case 'getMenu':
            respond(['status' => 'success', 'data' => $compliance->getMenu()]);
            break;

        case 'haveposts':
            header('Content-Type: text/html; charset=utf-8');
            echo $compliance->renderAdminDashboard();
            break;

        case 'getCat':
            header('Content-Type: text/html; charset=utf-8');
            $legacy = new WIBlog();
            $legacy->Cat();
            break;

        case 'nomodepost':
            respond($compliance->addChecklist([
                'title' => $_POST['post_title'] ?? '',
                'category' => $_POST['type'] ?? 'general',
                'frequency' => 'daily',
                'assigned_role' => $_POST['user'] ?? 'manager',
                'evidence_required' => 0,
            ]));
            break;

        case 'postimage':
        case 'PostVideo':
            respond($compliance->addChecklist([
                'title' => $_POST['post_title'] ?? '',
                'category' => $_POST['type'] ?? 'general',
                'frequency' => 'daily',
                'assigned_role' => $_POST['user'] ?? 'manager',
                'evidence_required' => 1,
            ]));
            break;

        default:
            respond(['status' => 'error', 'message' => 'Unknown action.'], 400);
    }
} catch (Throwable $e) {
    respond(['status' => 'error', 'message' => $e->getMessage()], 500);
}

function respond(array $payload, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    exit;
}
