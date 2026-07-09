<?php
/**
 * File Information
 *
 * Written By: Warner Infinity
 * Company: Warner Infinity
 * Product: WIMaintenance
 * Project: WI Ecosystem
 * File: WIMaintenanceAdminController.php
 * Location: /WIAdmin/WIPlugin/WIMaintenance/WIAdmin/WICore/WIClass/WIMaintenanceAdminController.php
 * Type: Plugin class
 * Layer: Controller / Thin orchestration
 * Purpose Area: WIMaintenance AJAX dispatch
 * Version: 0.1.0-dev
 * Created: 2026-06-01
 * Last Updated: 2026-06-01
 * Status: Functional developer foundation
 * Summary: Dispatches WIMaintenance actions to service methods. Business logic remains in service/repository.
 */

declare(strict_types=1);

final class WIMaintenanceAdminController
{
    private WIMaintenanceService $service;

    public function __construct(WIdb $WIdb)
    {
        $this->service = new WIMaintenanceService(new WIMaintenanceRepository($WIdb));
    }

    public function dispatch(array $request): array
    {
        $action = $this->canonicalAction((string)($request['action'] ?? 'wimaintenance_workspace'));

        return match ($action) {
            'wimaintenance_workspace' => $this->service->workspace($request),
            'wimaintenance_record_save' => $this->service->saveRecord($request),
            'wimaintenance_state_save' => $this->service->updateState($request),
            default => [
                'success' => false,
                'status' => 'error',
                'message' => 'Unknown WIMaintenance action.',
                'data' => ['action' => $action],
                'errors' => ['Unknown action: ' . $action],
            ],
        };
    }

    private function canonicalAction(string $action): string
    {
        $action = trim($action);
        $aliases = [
            'maintenance_workspace' => 'wimaintenance_workspace',
            'maintenance_record_save' => 'wimaintenance_record_save',
            'maintenance_state_save' => 'wimaintenance_state_save',
        ];

        return $aliases[$action] ?? $action;
    }
}
