<?php
/**
 * File Information
 *
 * Written By: Warner Infinity
 * Company: Warner Infinity
 * Product: WIMaintenance
 * Project: WI Ecosystem
 * File: WIMaintenanceService.php
 * Location: /WIAdmin/WIPlugin/WIMaintenance/WIAdmin/WICore/WIClass/WIMaintenanceService.php
 * Type: Plugin class
 * Layer: Service / Business logic
 * Purpose Area: WIMaintenance workspace and record orchestration
 * Version: 0.1.0-dev
 * Created: 2026-06-01
 * Last Updated: 2026-06-01
 * Status: Functional developer foundation
 * Summary: Builds workspace payloads and coordinates records, state changes and events.
 */

declare(strict_types=1);

final class WIMaintenanceService
{
    private WIMaintenanceRepository $repository;

    public function __construct(WIMaintenanceRepository $repository)
    {
        $this->repository = $repository;
    }

    public function workspace(array $request = []): array
    {
        return $this->success('WIMaintenance workspace loaded.', [
            'plugin' => [
                'code' => 'wimaintenance',
                'name' => 'WIMaintenance',
                'area' => 'operations',
                'description' => 'Equipment, faults, repairs, contractor visits and planned maintenance foundation.',
            ],
            'ready' => $this->repository->tableReady(),
            'summary' => $this->repository->summary($request),
            'businesses' => $this->repository->businesses(),
            'sites' => $this->repository->sites((int)($request['org_business_id'] ?? $request['business_id'] ?? 0)),
            'records' => $this->repository->records($request),
            'events' => $this->repository->events($request),
            'record_types' => ["asset", "fault", "repair", "service_visit"],
            'states' => ['grey', 'green', 'amber', 'red'],
            'statuses' => ['open', 'in_progress', 'complete', 'cancelled', 'archived'],
            'priorities' => ['low', 'normal', 'high', 'critical'],
            'plugin_boundaries' => [
                'owns' => ["asset", "fault", "repair", "service_visit"],
                'optional_integrations' => ["WIKitchenCompli", "WIRooms", "WITaskEngine", "WIScheduler", "WIEvidence"],
                'does_not_own' => ['WICMS core identity', 'WIdb', 'WIMedia', 'WIModal', 'WIKitchenCompli compliance core'],
            ],
        ]);
    }

    public function saveRecord(array $request = []): array
    {
        $title = trim((string)($request['title'] ?? ''));
        if ($title === '') {
            return $this->error('Title is required.');
        }

        $id = $this->repository->saveRecord($request);
        $this->repository->logEvent([
            'org_business_id' => $request['org_business_id'] ?? null,
            'org_site_id' => $request['org_site_id'] ?? null,
            'event_type' => 'record_saved',
            'related_type' => 'record',
            'related_id' => $id,
            'state' => (string)($request['state'] ?? 'grey'),
            'message' => 'WIMaintenance record saved.',
            'created_by_user_id' => $request['user_id'] ?? null,
        ]);

        return $this->success('Record saved.', ['id' => $id]);
    }

    public function updateState(array $request = []): array
    {
        $id = (int)($request['id'] ?? 0);
        $state = (string)($request['state'] ?? 'grey');
        $status = (string)($request['status'] ?? '');

        if ($id <= 0) {
            return $this->error('Record ID is required.');
        }

        $ok = $this->repository->updateRecordState($id, $state, $status);
        $this->repository->logEvent([
            'org_business_id' => $request['org_business_id'] ?? null,
            'org_site_id' => $request['org_site_id'] ?? null,
            'event_type' => 'record_state_changed',
            'related_type' => 'record',
            'related_id' => $id,
            'state' => $state,
            'message' => 'WIMaintenance state changed to ' . $state . '.',
            'created_by_user_id' => $request['user_id'] ?? null,
        ]);

        return $ok ? $this->success('Record state updated.', ['id' => $id]) : $this->error('Record state could not be updated.');
    }

    private function success(string $message, array $data): array
    {
        return ['success' => true, 'status' => 'success', 'message' => $message, 'data' => $data, 'errors' => []];
    }

    private function error(string $message): array
    {
        return ['success' => false, 'status' => 'error', 'message' => $message, 'data' => [], 'errors' => [$message]];
    }
}
