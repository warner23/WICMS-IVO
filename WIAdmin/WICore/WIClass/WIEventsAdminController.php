<?php
/**
 * File Information
 *
 * Written By: Warner Infinity
 * Company: Warner Infinity
 * Product: WIEvents
 * Project: WI Ecosystem
 * File: WIEventsAdminController.php
 * Location: /WIAdmin/WIPlugin/WIEvents/WIAdmin/WICore/WIClass/WIEventsAdminController.php
 * Type: Plugin class
 * Layer: Controller / Thin orchestration
 * Purpose Area: WIEvents AJAX dispatch
 * Version: 0.1.0-dev
 * Created: 2026-06-01
 * Last Updated: 2026-06-01
 * Status: Functional developer foundation
 * Summary: Dispatches WIEvents actions to service methods. Business logic remains in service/repository.
 */

declare(strict_types=1);

final class WIEventsAdminController
{
    private WIEventsService $service;

    public function __construct(WIdb $WIdb)
    {
        $this->service = new WIEventsService(new WIEventsRepository($WIdb));
    }

    public function dispatch(array $request): array
    {
        $action = $this->canonicalAction((string)($request['action'] ?? 'wievents_workspace'));

        return match ($action) {
            'wievents_workspace' => $this->service->workspace($request),
            'wievents_record_save' => $this->service->saveRecord($request),
            'wievents_state_save' => $this->service->updateState($request),
            default => [
                'success' => false,
                'status' => 'error',
                'message' => 'Unknown WIEvents action.',
                'data' => ['action' => $action],
                'errors' => ['Unknown action: ' . $action],
            ],
        };
    }

    private function canonicalAction(string $action): string
    {
        $action = trim($action);
        $aliases = [
            'events_workspace' => 'wievents_workspace',
            'events_record_save' => 'wievents_record_save',
            'events_state_save' => 'wievents_state_save',
        ];

        return $aliases[$action] ?? $action;
    }
}
