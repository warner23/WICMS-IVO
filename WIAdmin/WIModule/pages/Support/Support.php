<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WIProfile
| Project: WI Ecosystem
| File: support.php
| Location: /WIAdmin/WIModule/pages/support/support.php
| Type: Module
| Layer: Front-Side UI Module
| Purpose Area: Member support centre
| Version: 1.0.0
| Created: 2026-06-14
| Last Updated: 2026-06-14
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Member-facing support module. This is a safe placeholder workspace that uses
| existing shared support/bug-report routes where available and keeps business
| logic out of the UI module.
*/

final class WISupportModule
{
    public static function moduleMeta(): array
    {
        return [
            'code' => 'support',
            'name' => 'Support Centre',
            'type' => 'page',
            'area' => 'member',
            'version' => '1.0.0',
            'description' => 'Member support, help and issue reporting workspace.',
        ];
    }

    public function Install(string $moduleName = 'support', array $context = []): array
    {
        return ['success' => true, 'message' => 'Support module ready.', 'module' => $moduleName];
    }

    public function editMod(array $context = []): void
    {
        $this->adminPanel('Support module', 'Member support centre module.');
    }

    public function editPageContent(string|int $page = 'support', array $context = []): void
    {
        $this->adminPanel('Support page content', 'Support content is rendered from reusable cards and shared issue-reporting routes.');
    }

    public function mod_name(string $module = 'support', string $page = 'support', array $payload = []): void
    {
        $this->openShell($page);
        echo '<section class="wi-profile-hero wi-profile-hero--compact"><div class="wi-profile-hero__content"><p class="wi-kicker">Support</p><h2>Support centre</h2><p>Get help with your account, profile workspace, forms, training or compliance access.</p></div></section>';
        echo '<section class="wi-member-grid wi-member-grid--cards">';
        $this->card('Report an issue', 'Tell us about a bug, broken page or missing workflow.', '../WIAdmin/bug-reporter.php', 'Open reporter');
        $this->card('Account help', 'Need help with login, profile details or access?', 'account.php', 'Open account');
        $this->card('Member help', 'Open your member workspace and account tools.', '../WIMembers/profile.php', 'Open profile');
        echo '</section>';
        $this->closeShell();
    }

    private function card(string $title, string $body, string $href, string $button): void
    {
        echo '<article class="wi-member-card wi-action-card"><h3>' . wi_e($title) . '</h3><p>' . wi_e($body) . '</p><a href="' . wi_e($href) . '">' . wi_e($button) . '</a></article>';
    }

    private function openShell(string $page): void
    {
        $modules = new WIModules();
        echo '<div class="wi-member-shell">';
        $modules->renderComponent('member_sidebar', ['page' => $page]);
        echo '<main class="wi-member-main">';
        $modules->renderComponent('member_topbar', ['page' => $page]);
    }

    private function closeShell(): void
    {
        echo '</main></div>';
    }

    private function adminPanel(string $title, string $body): void
    {
        echo '<section class="wi-admin-module-editor"><h2>' . wi_e($title) . '</h2><p>' . wi_e($body) . '</p></section>';
    }
}
