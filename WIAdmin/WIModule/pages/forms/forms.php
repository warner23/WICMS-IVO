<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WIMembers / WIProfile
| Project: WI Ecosystem
| File: forms.php
| Location: /WIAdmin/WIModule/pages/forms/forms.php
| Type: Module
| Layer: Front-Side UI Module
| Purpose Area: Worker/member training, forms and document acknowledgements
| Version: 2.1.0
| Created: 2026-05-24
| Last Updated: 2026-06-14
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Worker-facing Forms & Training workspace. Reads assigned training, forms and
| compliance document acknowledgements without changing the checklist backend.
*/

final class WIFormsModule
{
    public static function moduleMeta(): array
    {
        return [
            'code' => 'forms',
            'name' => 'Forms & Training',
            'type' => 'page',
            'area' => 'member',
            'version' => '2.1.0',
            'description' => 'Member/worker forms, training and document acknowledgements.',
        ];
    }

    public function Install(string $moduleName = 'forms', array $context = []): array
    {
        return ['success' => true, 'message' => 'Forms module ready.', 'module' => $moduleName];
    }

    public function editMod(array $context = []): void
    {
        $this->adminPanel('Forms & Training module', 'Member forms, training and document acknowledgements.');
    }

    public function editPageContent(string|int $page = 'forms', array $context = []): void
    {
        $this->adminPanel('Forms page content', 'Runtime content is provided by WITraining/WIForms services.');
    }

    public function mod_name(string $module = '', string $page = 'forms', array $payload = []): void
    {
        $modules = new WIModules();
        [$training, $forms, $documents] = $this->workspacePayload();

        echo '<div class="wi-member-shell">';
        $modules->renderComponent('member_sidebar', ['page' => $page]);
        echo '<main class="wi-member-main">';
        $modules->renderComponent('member_topbar', ['page' => $page]);

        echo '<section class="wi-profile-hero wi-profile-hero--compact"><div class="wi-profile-hero__content"><p class="wi-kicker">Worker workspace</p><h2>Training, forms and new employee documents</h2><p>Complete assigned training, submit employee forms and acknowledge required documents from one place.</p></div></section>';

        echo '<section class="wi-member-grid wi-member-grid--stats">';
        $this->statCard('Training', count($training), 'assigned');
        $this->statCard('Forms', count($forms), 'available');
        $this->statCard('Documents', count($documents), 'visible');
        $this->statCard('Due now', $this->dueCount($training, $forms, $documents), 'items');
        echo '</section>';

        $this->renderOnboardingDocuments($forms, $documents);
        $this->renderTraining($training);
        $this->renderForms($forms);
        $this->renderDocuments($documents);

        echo '</main></div>';
    }

    /** @return array{0:array<int,array<string,mixed>>,1:array<int,array<string,mixed>>,2:array<int,array<string,mixed>>} */
    private function workspacePayload(): array
    {
        $userId = (int) WISession::get('user_id', 0);
        $training = [];
        $forms = [];
        $documents = [];

        try {
            if (class_exists('WITraining')) {
                $training = (new WITraining())->assignedToUser($userId, 100);
            }
        } catch (Throwable $e) {
            $training = [];
        }

        try {
            if (class_exists('WIForms')) {
                $formService = new WIForms();
                $forms = $formService->availableForUser($userId);
                $documents = $formService->documentAcknowledgementsForUser($userId);
            }
        } catch (Throwable $e) {
            $forms = [];
            $documents = [];
        }

        return [
            is_array($training) ? $training : [],
            is_array($forms) ? $forms : [],
            is_array($documents) ? $documents : [],
        ];
    }

    private function statCard(string $label, int $value, string $hint): void
    {
        echo '<article><span>' . wi_e($label) . '</span><strong>' . $value . '</strong><small>' . wi_e($hint) . '</small></article>';
    }


    private function renderOnboardingDocuments(array $forms, array $documents): void
    {
        $dueForms = array_values(array_filter($forms, static fn(array $form): bool => (string) ($form['state'] ?? 'due') !== 'completed'));
        $dueDocuments = array_values(array_filter($documents, static fn(array $document): bool => empty($document['acknowledged'])));

        echo '<section class="wi-member-panel" id="onboarding"><div class="wi-panel-head"><div><p class="wi-kicker">New employee documents</p><h2>Assigned onboarding items</h2></div></div><div class="wi-list wi-list--actions">';

        if ($dueForms === [] && $dueDocuments === []) {
            echo '<p>No new employee documents or forms are assigned to this profile yet.</p>';
        }

        foreach ($dueForms as $form) {
            $state = (string) ($form['state'] ?? 'due');
            echo '<article class="wi-list-row wi-state-' . wi_e($state) . '">';
            echo '<div><strong>' . wi_e($form['title'] ?? 'Employee form') . '</strong><small>Form · ' . wi_e($form['description'] ?? '') . '</small></div>';
            echo '<span>' . wi_e($form['status_label'] ?? $state) . '</span>';
            echo '<a class="wi-mini-link" href="#forms">Open forms</a>';
            echo '</article>';
        }

        foreach ($dueDocuments as $document) {
            $state = (string) ($document['state'] ?? 'due');
            echo '<article class="wi-list-row wi-state-' . wi_e($state) . '">';
            echo '<div><strong>' . wi_e($document['title'] ?? 'Employee document') . '</strong><small>Document · ' . wi_e($document['document_type'] ?? '') . '</small></div>';
            echo '<span>Due</span>';
            echo '<a class="wi-mini-link" href="#documents">Open documents</a>';
            echo '</article>';
        }

        echo '</div></section>';
    }

    private function renderTraining(array $training): void
    {
        echo '<section class="wi-member-panel" id="training"><div class="wi-panel-head"><div><p class="wi-kicker">Training</p><h2>Assigned training</h2></div></div><div class="wi-list wi-list--actions">';
        if ($training === []) {
            echo '<p>No training assignments found yet.</p>';
        }

        foreach ($training as $item) {
            $state = (string) ($item['state'] ?? 'due');
            echo '<article class="wi-list-row wi-state-' . wi_e($state) . '">';
            echo '<div><strong>' . wi_e($item['title'] ?? 'Training') . '</strong><small>' . wi_e($item['category'] ?? 'General') . ' · Due: ' . wi_e($item['due_date'] ?: 'No due date') . '</small></div>';
            echo '<span>' . wi_e($item['status_label'] ?? $state) . '</span>';
            echo '<div class="wi-row-actions">';
            if (!empty($item['can_start'])) {
                echo '<button type="button" data-wi-member-action="member_training_start" data-assignment-id="' . (int) $item['assignment_id'] . '">Start</button>';
            }
            if (!empty($item['can_complete'])) {
                echo '<button type="button" data-wi-member-action="member_training_complete" data-assignment-id="' . (int) $item['assignment_id'] . '">Complete</button>';
            }
            echo '</div></article>';
        }
        echo '</div></section>';
    }

    private function renderForms(array $forms): void
    {
        echo '<section class="wi-member-panel" id="forms"><div class="wi-panel-head"><div><p class="wi-kicker">Forms</p><h2>Assigned forms</h2></div></div><div class="wi-list wi-list--actions">';
        if ($forms === []) {
            echo '<p>No forms available yet. Run the included SQL patch to seed starter worker forms.</p>';
        }

        foreach ($forms as $form) {
            $state = (string) ($form['state'] ?? 'due');
            echo '<article class="wi-list-row wi-state-' . wi_e($state) . '">';
            echo '<div><strong>' . wi_e($form['title'] ?? 'Form') . '</strong><small>' . wi_e($form['description'] ?? '') . '</small></div>';
            echo '<span>' . wi_e($form['status_label'] ?? $state) . '</span>';
            echo '<form class="wi-inline-form" data-wi-ajax-form="member_form_submit"><input type="hidden" name="csrf_token" value="' . wi_e(WICsrf::getToken()) . '"><input type="hidden" name="form_id" value="' . (int) $form['id'] . '"><input type="hidden" name="answers[acknowledged]" value="1"><button type="submit">Submit</button><div data-wi-form-message></div></form>';
            echo '</article>';
        }
        echo '</div></section>';
    }

    private function renderDocuments(array $documents): void
    {
        echo '<section class="wi-member-panel" id="documents"><div class="wi-panel-head"><div><p class="wi-kicker">Documents</p><h2>Document acknowledgements</h2></div></div><div class="wi-list wi-list--actions">';
        if ($documents === []) {
            echo '<p>No document acknowledgements found yet.</p>';
        }

        foreach ($documents as $document) {
            $state = (string) ($document['state'] ?? 'due');
            echo '<article class="wi-list-row wi-state-' . wi_e($state) . '">';
            echo '<div><strong>' . wi_e($document['title'] ?? 'Document') . '</strong><small>' . wi_e($document['document_type'] ?? '') . '</small></div>';
            echo '<span>' . ($document['acknowledged'] ? 'Acknowledged' : 'Due') . '</span>';
            if (!$document['acknowledged']) {
                echo '<button type="button" data-wi-member-action="member_document_acknowledge" data-document-id="' . (int) $document['document_id'] . '">Acknowledge</button>';
            }
            echo '</article>';
        }
        echo '</div></section>';
    }

    private function dueCount(array $training, array $forms, array $documents): int
    {
        $count = 0;
        foreach ($training as $item) {
            $count += in_array((string) ($item['state'] ?? ''), ['due', 'overdue', 'in_progress'], true) ? 1 : 0;
        }
        foreach ($forms as $item) {
            $count += (string) ($item['state'] ?? '') !== 'completed' ? 1 : 0;
        }
        foreach ($documents as $item) {
            $count += empty($item['acknowledged']) ? 1 : 0;
        }
        return $count;
    }

    private function adminPanel(string $title, string $body): void
    {
        echo '<section class="wi-admin-module-editor"><h2>' . wi_e($title) . '</h2><p>' . wi_e($body) . '</p></section>';
    }

}
