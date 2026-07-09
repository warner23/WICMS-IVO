<?php
/**
 * File Information
 *
 * Written By: Warner Infinity
 * Company: Warner Infinity
 * Product: WITaskEngine / WIProfile
 * Project: WI Ecosystem
 * File: WIProfileTaskEngineBridge.php
 * Location: /WICore/WIClass/WIProfileTaskEngineBridge.php
 * Type: Bridge class
 * Layer: Member workspace bridge
 * Purpose Area: Displays and updates WITaskEngine tasks inside WIProfile/WIMembers without duplicating task logic.
 * Version: 1.4.1-profile-safe-fallback
 * Created: 2026-06-03
 * Last Updated: 2026-06-09
 * Status: Production-ready bridge batch 4
 * Summary: Uses WITaskEngineBridge where available and safely falls back when the current bridge does not expose WIProfile dashboard helpers.
 */

declare(strict_types=1);

final class WIProfileTaskEngineBridge
{
    private int $userId;
    private ?WITaskEngineBridge $taskBridge = null;

    public function __construct(int $userId)
    {
        $this->userId = max(0, $userId);
        $this->loadTaskEngineBridge();
    }

    public function isReady(): bool
    {
        return $this->taskBridge instanceof WITaskEngineBridge && $this->taskBridge->isReady();
    }

    public function dashboard(array $filters = []): array
    {
        if (!$this->isReady()) {
            return [
                'success' => false,
                'message' => 'WITaskEngine is not ready for WIProfile.',
                'summary' => $this->emptySummary(),
                'tasks' => [],
            ];
        }

        if ($this->taskBridge instanceof WITaskEngineBridge && method_exists($this->taskBridge, 'profileDashboardForUser')) {
            $payload = $this->taskBridge->profileDashboardForUser($this->userId, $filters);
            $data = (array)($payload['data'] ?? []);

            return [
                'success' => (bool)($payload['success'] ?? false),
                'message' => (string)($payload['message'] ?? ''),
                'summary' => (array)($data['summary'] ?? $this->emptySummary()),
                'tasks' => (array)($data['tasks'] ?? []),
            ];
        }

        return $this->dashboardFromRepository($filters);
    }

    public function completeTask(int $taskId): array
    {
        if (!$this->isReady() || !$this->taskBridge instanceof WITaskEngineBridge) {
            return ['success' => false, 'message' => 'WITaskEngine is not ready.'];
        }

        if (method_exists($this->taskBridge, 'completeTaskForUser')) {
            return $this->taskBridge->completeTaskForUser($taskId, $this->userId);
        }

        if (method_exists($this->taskBridge, 'completeTask')) {
            return $this->taskBridge->completeTask($taskId);
        }

        return ['success' => false, 'message' => 'This WITaskEngine bridge cannot complete profile tasks yet.'];
    }

    public function attachEvidence(int $taskId, int $mediaId, string $evidenceType = 'photo', string $caption = ''): array
    {
        if (!$this->isReady() || !$this->taskBridge instanceof WITaskEngineBridge) {
            return ['success' => false, 'message' => 'WITaskEngine is not ready.'];
        }

        if (!method_exists($this->taskBridge, 'attachEvidence')) {
            return ['success' => false, 'message' => 'This WITaskEngine bridge cannot attach evidence yet.'];
        }

        return $this->taskBridge->attachEvidence($taskId, $mediaId, $evidenceType, $caption, $this->userId);
    }

    private function dashboardFromRepository(array $filters): array
    {
        if (!class_exists('WITaskEngineRepository', false)) {
            return [
                'success' => false,
                'message' => 'WITaskEngine repository is not available to WIProfile.',
                'summary' => $this->emptySummary(),
                'tasks' => [],
            ];
        }

        try {
            $repository = new WITaskEngineRepository(WIdb::getInstance());
            if (!method_exists($repository, 'tasks')) {
                return [
                    'success' => false,
                    'message' => 'WITaskEngine repository does not expose task listing yet.',
                    'summary' => $this->emptySummary(),
                    'tasks' => [],
                ];
            }

            $filters['user_id'] = $this->userId;
            $filters['status'] = $filters['status'] ?? 'all';
            $tasks = array_map([$this, 'normaliseTaskRow'], $repository->tasks($filters, 'my'));

            return [
                'success' => true,
                'message' => '',
                'summary' => $this->summaryFromTasks($tasks),
                'tasks' => array_slice($tasks, 0, 12),
            ];
        } catch (Throwable $exception) {
            return [
                'success' => false,
                'message' => 'WITaskEngine task list could not be loaded safely.',
                'summary' => $this->emptySummary(),
                'tasks' => [],
            ];
        }
    }

    /**
     * @param array<string,mixed> $task
     * @return array<string,mixed>
     */
    private function normaliseTaskRow(array $task): array
    {
        $status = trim((string)($task['status_code'] ?? 'open')) ?: 'open';
        $priority = trim((string)($task['priority_code'] ?? 'normal')) ?: 'normal';
        $dueAt = trim((string)($task['due_at'] ?? ''));

        return [
            'id' => (int)($task['id'] ?? 0),
            'title' => (string)($task['title'] ?? 'Task'),
            'description' => (string)($task['description'] ?? ''),
            'status_code' => $status,
            'status_label' => ucwords(str_replace('_', ' ', $status)),
            'priority_code' => $priority,
            'priority_label' => ucwords(str_replace('_', ' ', $priority)),
            'state' => (string)($task['state'] ?? 'grey'),
            'due_at' => $dueAt,
            'due_date' => $dueAt !== '' ? substr($dueAt, 0, 10) : '',
            'proof_required' => (int)($task['proof_required'] ?? 0),
            'proof_status' => (string)($task['proof_status'] ?? 'not_required'),
            'queue_code' => (string)($task['queue_code'] ?? 'operations'),
            'queue_label' => (string)($task['queue_label'] ?? $task['queue_code'] ?? 'Operations'),
            'business_name' => (string)($task['business_name'] ?? ''),
            'site_name' => (string)($task['site_name'] ?? ''),
        ];
    }

    /**
     * @param array<int,array<string,mixed>> $tasks
     * @return array<string,int>
     */
    private function summaryFromTasks(array $tasks): array
    {
        $summary = $this->emptySummary();
        $today = date('Y-m-d');

        foreach ($tasks as $task) {
            $summary['total']++;
            $status = (string)($task['status_code'] ?? 'open');
            $priority = (string)($task['priority_code'] ?? 'normal');
            $dueDate = (string)($task['due_date'] ?? '');
            $proofRequired = (int)($task['proof_required'] ?? 0) === 1;
            $proofStatus = (string)($task['proof_status'] ?? 'not_required');

            if (in_array($status, ['completed', 'closed'], true)) {
                $summary['completed']++;
                continue;
            }

            $summary['open']++;

            if ($dueDate === $today) {
                $summary['due_today']++;
            }

            if ($dueDate !== '' && $dueDate < $today) {
                $summary['overdue']++;
            }

            if (in_array($priority, ['high', 'critical'], true)) {
                $summary['high_priority']++;
            }

            if ($proofRequired && !in_array($proofStatus, ['complete', 'approved', 'not_required'], true)) {
                $summary['awaiting_proof']++;
            }
        }

        return $summary;
    }

    private function loadTaskEngineBridge(): void
    {
        $projectRoot = dirname(__DIR__, 2);
        $adminClassRoot = $projectRoot . '/WIAdmin/WICore/WIClass';
        $pluginClassRoot = $projectRoot . '/WIAdmin/WIPlugin/WITaskEngine/WIAdmin/WICore/WIClass';

        $paths = [
            $adminClassRoot . '/WITaskEngineRepository.php',
            $adminClassRoot . '/WITaskEngineBridge.php',
            $pluginClassRoot . '/WITaskEngineRepository.php',
            $pluginClassRoot . '/WITaskEngineBridge.php',
        ];

        foreach ($paths as $path) {
            if (is_file($path)) {
                if (str_ends_with($path, 'WITaskEngineRepository.php') && class_exists('WITaskEngineRepository', false)) {
                    continue;
                }
                if (str_ends_with($path, 'WITaskEngineBridge.php') && class_exists('WITaskEngineBridge', false)) {
                    continue;
                }
                require_once $path;
            }
        }

        if (class_exists('WITaskEngineBridge', false)) {
            $this->taskBridge = new WITaskEngineBridge(WIdb::getInstance());
        }
    }

    private function emptySummary(): array
    {
        return [
            'total' => 0,
            'open' => 0,
            'due_today' => 0,
            'overdue' => 0,
            'high_priority' => 0,
            'awaiting_proof' => 0,
            'completed' => 0,
        ];
    }
}
