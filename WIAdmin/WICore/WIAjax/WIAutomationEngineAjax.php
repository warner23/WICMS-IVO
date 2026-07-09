<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
require_once dirname(__DIR__) . '/init.php';
foreach (['WIAutomationEngineRepository.php','WIAutomationEngineService.php'] as $file) {
    $path = dirname(__DIR__) . '/WIClass/' . $file;
    if (is_file($path)) { require_once $path; }
}
try {
    if (!isset($GLOBALS['WIdb']) || !class_exists('WIAutomationEngineRepository')) {
        throw new RuntimeException('Automation engine is not available.');
    }
    $service = new WIAutomationEngineService(new WIAutomationEngineRepository($GLOBALS['WIdb']));
    $action = (string)($_POST['action'] ?? 'workspace');
    if ($action === 'probe') {
        $service->trigger('manual', 'probe', ['org_business_id' => isset($_POST['business_id']) ? (int)$_POST['business_id'] : null]);
    }
    echo json_encode(['status'=>'success','data'=>$service->workspace(isset($_POST['business_id']) ? (int)$_POST['business_id'] : null)], JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status'=>'error','message'=>$e->getMessage()], JSON_UNESCAPED_SLASHES);
}
