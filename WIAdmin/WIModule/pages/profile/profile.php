<?php
declare(strict_types=1);

/** Profile/workspace module using the standard WI module lifecycle. */
final class WIProfileModule
{
    public static function moduleMeta(): array
    {
        return ['code'=>'profile','name'=>'Profile Workspace','type'=>'page','area'=>'member','version'=>'2.0.0','description'=>'Member dashboard, business context and quick links.'];
    }

    public function Install(string $moduleName = 'profile', array $context = []): array { return $this->installRecord($moduleName); }
    public function editMod(array $context = []): void { $this->adminPanel('Profile Workspace', 'Configure dashboard cards, quick links and member workspace defaults.'); }
    public function editPageContent(string|int $page = 'profile', array $context = []): void { $this->adminPanel('Profile Page Content', 'Page content is rendered from the member profile payload and assigned modules.'); }

    public function mod_name(string $module = 'profile', string $page = 'profile', array $payload = []): void
    {
        $data = $this->payload();
        $user = $data['user'] ?? [];
        $business = $data['business'] ?? [];
        $employee = $data['employee'] ?? [];
        $training = $data['training'] ?? [];
        $forms = $data['forms'] ?? [];
        $checklists = $data['checklists'] ?? [];
        $actions = $data['actions'] ?? [];
        $tasks = $data['tasks'] ?? [];

        $flags = is_array($business['flags'] ?? null) ? $business['flags'] : [];
        $hasCompliance = !empty($flags['has_business_context'])
            || !empty($flags['has_compliance_assignment'])
            || !empty($flags['can_open_compliance_workspace'])
            || !empty($business['sites']);

        $businessName = (string)(($business['businesses'][0]['display_name'] ?? '') ?: ($employee['business_name'] ?? 'No business linked'));
        $primarySite = is_array($business['primary_site'] ?? null) ? $business['primary_site'] : [];
        $siteName = (string)(($primarySite['site_name'] ?? '') ?: 'No primary site');
        $roleLabel = $this->primaryRoleLabel($employee['roles'] ?? [], (string)($user['role_name'] ?? 'Member'));

        $this->openShell($page);
        echo '<section class="wi-profile-hero wi-profile-hero--compact">';
        echo '<div class="wi-profile-hero__avatar"><img src="' . wi_e((string)($user['avatar'] ?? '')) . '" alt=""></div>';
        echo '<div class="wi-profile-hero__content"><p class="wi-kicker">Welcome back</p><h2>' . wi_e((string)($user['name'] ?? 'Member')) . '</h2><p>Your WIProfile workspace connects your role, sites, training, forms, documents and compliance work.</p><div class="wi-profile-tags"><span>' . wi_e($businessName) . '</span><span>' . wi_e($siteName) . '</span><span>' . wi_e($roleLabel) . '</span></div></div>';
        echo '<div class="wi-profile-hero__score"><strong>' . (int)($user['profile_completion'] ?? 0) . '%</strong><span>Profile complete</span></div>';
        echo '</section>';

        echo '<section class="wi-member-grid wi-member-grid--stats">';
        if ($hasCompliance) {
            $this->statCard('Checklists', (int)($checklists['available'] ?? 0), 'available today');
            $this->statCard('Training', (int)($training['completed'] ?? 0) . '/' . (int)($training['total'] ?? 0), 'completed');
            $this->statCard('Forms', (int)($forms['forms_due'] ?? 0), 'to complete');
            $this->statCard('Documents', (int)($forms['documents_due'] ?? 0), 'to acknowledge');
        } else {
            $this->statCard('Profile', (int)($user['profile_completion'] ?? 0) . '%', 'complete');
            $this->statCard('Account', 'Ready', 'member access');
            $this->statCard('Settings', 'Open', 'preferences');
            $this->statCard('Support', 'Available', 'help centre');
        }
        echo '</section>';

        echo '<section class="wi-member-grid wi-member-grid--cards">';
        $cards = [];
        if ($hasCompliance) {
            $cards = array_merge($cards, [
                ['My Checklists','Open your current checklist queue and complete daily checks.','../WIMembers/profile.php','Open profile'],
                ['My Training','View assigned training, due dates and completion status.','forms.php#training','View training'],
                ['New Employee Docs','Review onboarding and starter documents.','forms.php#onboarding','Open docs'],
                ['My Forms','Complete worker forms and profile confirmations.','forms.php#forms','Open forms'],
                ['My Documents','Acknowledge policies and compliance documents.','forms.php#documents','View documents'],
                ['My Actions','Open assigned actions and checklist follow-ups.','../WIMembers/profile.php','Open actions'],
                ['My Sites','Review your assigned business/site access.','profile.php#sites','View sites'],
            ]);
        }
        $cards = array_merge($cards, [
            ['Account details','Keep your login and profile details correct.','account.php','Update account'],
            ['Settings','Control notifications and preferences.','settings.php','Open settings'],
            ['Support','Contact support or request help.','support.php','Open support'],
        ]);
        foreach ($cards as $card) { $this->card($card[0], $card[1], $card[2], $card[3]); }
        echo '</section>';

        echo '<section class="wi-member-grid wi-member-grid--two">';
        $this->renderScrollablePanel('Training', 'Assigned training', $training['items'] ?? [], 'forms.php#training', 'No training assignments found yet.');
        $this->renderContextPanel($business, $employee, $actions, $tasks);
        echo '</section>';
        $this->closeShell();
    }

    private function payload(): array
    {
        try { return (new WIProfile())->dashboardPayload(); } catch (Throwable $e) { return ['success'=>false,'user'=>['name'=>'Member','avatar'=>'','profile_completion'=>0], 'business'=>[], 'employee'=>[], 'training'=>[], 'forms'=>[], 'checklists'=>[], 'actions'=>[], 'tasks'=>[]]; }
    }

    private function renderScrollablePanel(string $kicker, string $title, array $items, string $href, string $empty): void
    {
        echo '<section class="wi-member-panel"><div class="wi-panel-head"><div><p class="wi-kicker">' . wi_e($kicker) . '</p><h2>' . wi_e($title) . '</h2></div><a href="' . wi_e($href) . '">View all</a></div><div class="wi-list wi-list--scroll wi-list--six">';
        if ($items === []) { echo '<p>' . wi_e($empty) . '</p>'; }
        foreach (array_slice($items, 0, 12) as $item) {
            $state = (string)($item['state'] ?? 'due');
            echo '<article class="wi-list-row wi-state-' . wi_e($state) . '"><div><strong>' . wi_e((string)($item['title'] ?? 'Training')) . '</strong><small>Due: ' . wi_e((string)($item['due_date'] ?? 'No due date')) . '</small></div><span>' . wi_e((string)($item['status_label'] ?? $state)) . '</span></article>';
        }
        echo '</div></section>';
    }

    private function renderContextPanel(array $business, array $employee, array $actions, array $tasks): void
    {
        $sites = is_array($business['sites'] ?? null) ? $business['sites'] : [];
        echo '<section class="wi-member-panel" id="sites"><div class="wi-panel-head"><div><p class="wi-kicker">Context</p><h2>My sites</h2></div></div><div class="wi-list wi-list--scroll wi-list--six">';
        if ($sites === []) { echo '<p>No site assignments found yet.</p>'; }
        foreach ($sites as $site) {
            echo '<article class="wi-list-row"><div><strong>' . wi_e((string)($site['site_name'] ?? 'Site')) . '</strong><small>' . wi_e((string)($site['city_name'] ?? $site['region_name'] ?? '')) . '</small></div><span>' . (((int)($site['is_primary'] ?? 0) === 1) ? 'Primary' : 'Assigned') . '</span></article>';
        }
        echo '</div><hr class="wi-member-divider"><p><strong>' . (int)($actions['total'] ?? 0) . '</strong> open compliance actions · <strong>' . (int)($tasks['summary']['open'] ?? 0) . '</strong> profile tasks.</p></section>';
    }

    private function statCard(string $label, string|int $value, string $hint): void { echo '<article><span>' . wi_e($label) . '</span><strong>' . wi_e((string)$value) . '</strong><small>' . wi_e($hint) . '</small></article>'; }
    private function card(string $title, string $body, string $href, string $button): void { echo '<article class="wi-member-card wi-action-card"><h3>' . wi_e($title) . '</h3><p>' . wi_e($body) . '</p><a href="' . wi_e($href) . '">' . wi_e($button) . '</a></article>'; }
    private function primaryRoleLabel(array $roles, string $fallback): string { foreach ($roles as $role) { if ((int)($role['is_primary'] ?? 0) === 1) { return (string)($role['role_name'] ?? $fallback); } } return (string)($roles[0]['role_name'] ?? $fallback ?: 'Member'); }
    private function openShell(string $page): void { $m = new WIModules(); echo '<div class="wi-member-shell">'; $m->renderComponent('member_sidebar', ['page'=>$page]); echo '<main class="wi-member-main">'; $m->renderComponent('member_topbar', ['page'=>$page]); }
    private function closeShell(): void { echo '</main></div>'; }
    private function installRecord(string $moduleName): array { return ['success'=>true,'message'=>'Profile module ready.','module'=>$moduleName]; }
    private function adminPanel(string $title, string $body): void { echo '<section class="wi-admin-module-editor"><h2>' . wi_e($title) . '</h2><p>' . wi_e($body) . '</p></section>'; }
}
