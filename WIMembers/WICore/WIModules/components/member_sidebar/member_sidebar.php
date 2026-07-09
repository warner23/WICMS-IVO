<?php
declare(strict_types=1);

/** Central member sidebar module. */
final class WIMemberSidebarModule
{
    public static function moduleMeta(): array
    {
        return [
            'code' => 'member_sidebar',
            'name' => 'Member Sidebar',
            'type' => 'element',
            'area' => 'member',
            'version' => '2.1.0',
            'description' => 'Member/profile workspace navigation without public/root menu drift.',
        ];
    }

    public function Install(string $moduleName = 'member_sidebar', array $context = []): array
    {
        return ['success' => true, 'message' => 'Member sidebar is shared file-based workspace chrome.', 'module' => $moduleName];
    }

    public function editMod(array $context = []): void
    {
        $this->adminPanel('Edit Member Sidebar', 'Navigation is controlled here until the menu manager is connected.');
    }

    public function editPageContent(string|int $page = '', array $context = []): void
    {
        $this->adminPanel('Member Sidebar Content', 'Sidebar is shared workspace chrome and is not edited per page.');
    }

    public function mod_name(string $module = '', string $page = 'profile', array $payload = []): void
    {
        $page = $this->safe((string)($payload['page'] ?? $page));
        $hasCompliance = $this->hasComplianceWorkspace();

        echo '<!-- WIProfile sidebar staging patch 12 active -->';
        echo '<aside class="wi-member-sidebar" aria-label="Member workspace navigation">';
        echo '<div class="wi-member-sidebar__brand"><span class="wi-member-mark">WI</span><div><strong>WIProfile</strong><small>Member workspace</small></div></div>';
        echo '<nav class="wi-member-sidebar__nav" aria-label="Workspace navigation">';

        $this->renderGroup('Workspace', [
            ['profile', 'Dashboard', 'dashboard', $this->memberHref('profile.php'), ['profile', 'workspace']],
            ['forms', 'My Forms', 'forms', $this->memberHref('forms.php#forms'), ['forms']],
            ['account', 'Account', 'account', $this->memberHref('account.php'), ['account']],
            ['settings', 'Settings', 'settings', $this->memberHref('settings.php'), ['settings']],
        ], $page);

        if ($hasCompliance) {
            $this->renderGroup('Business & Compliance', [
                ['checklists', 'My Checklists', 'checklist', $this->rootHref('WICompliance/checklists.php'), ['checklists']],
                ['training', 'My Training', 'training', $this->memberHref('forms.php#training'), ['training']],
                ['onboarding', 'New Employee Docs', 'documents', $this->memberHref('forms.php#onboarding'), ['onboarding']],
                ['documents', 'My Documents', 'documents', $this->memberHref('forms.php#documents'), ['documents', 'document_acknowledgements']],
                // My Actions is intentionally preserved as the working Compliance route.
                ['actions', 'My Actions', 'actions', $this->rootHref('WICompliance/checklists.php#actions'), ['actions']],
                ['sites', 'My Sites', 'sites', $this->memberHref('profile.php#sites'), ['sites']],
            ], $page);
        }

        $this->renderGroup('Account & Help', [
            ['membership', 'Membership', 'membership', $this->memberHref('membership.php'), ['membership', 'upgrade', 'downgrade']],
            ['payments', 'Payments', 'payments', $this->memberHref('userpayments.php'), ['payments', 'userpayments']],
            ['transactions', 'Transactions', 'transactions', $this->memberHref('transactions.php'), ['transactions']],
            ['security', 'Security', 'security', $this->memberHref('usersecurity.php'), ['security', 'usersecurity']],
            ['support', 'Support', 'support', $this->memberHref('support.php'), ['support']],
        ], $page);

        echo '</nav>';
        echo '</aside>';
    }

    private function hasComplianceWorkspace(): bool
    {
        $userId = (int) WISession::get('user_id', 0);
        if ($userId <= 0) {
            return false;
        }

        try {
            if (class_exists('WIBusiness')) {
                $context = (new WIBusiness())->contextForUser($userId);
                $flags = is_array($context['flags'] ?? null) ? $context['flags'] : [];

                return !empty($flags['has_business_context'])
                    || !empty($flags['has_compliance_assignment'])
                    || !empty($flags['can_open_compliance_workspace'])
                    || !empty($flags['is_worker'])
                    || !empty($flags['is_manager'])
                    || !empty($context['sites']);
            }
        } catch (Throwable $e) {
            return false;
        }

        return false;
    }

    /** @param array<int,array{0:string,1:string,2:string,3:string,4:array<int,string>}> $items */
    private function renderGroup(string $label, array $items, string $page): void
    {
        echo '<div class="wi-member-nav-group"><span class="wi-member-nav-group__label">' . wi_e($label) . '</span>';
        foreach ($items as $item) {
            [$key, $text, $icon, $href, $matches] = $item;
            $active = in_array($page, $matches, true) ? ' is-active' : '';
            echo '<a class="wi-member-nav-item' . $active . '" href="' . wi_e($href) . '"><span data-icon="' . wi_e($icon) . '"></span><strong>' . wi_e($text) . '</strong></a>';
        }
        echo '</div>';
    }

    private function memberHref(string $path): string
    {
        return ltrim($path, '/');
    }

    private function rootHref(string $path): string
    {
        return '../' . ltrim($path, '/');
    }

    private function safe(string $page): string
    {
        return preg_replace('/[^a-zA-Z0-9_\-]/', '', trim($page)) ?: 'profile';
    }

    private function adminPanel(string $title, string $body): void
    {
        echo '<section class="wi-admin-module-editor"><h2>' . wi_e($title) . '</h2><p>' . wi_e($body) . '</p></section>';
    }
}
