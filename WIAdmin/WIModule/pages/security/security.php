<?php
declare(strict_types=1);

/** Security module using the standard WI module lifecycle. */
final class WISecurityModule
{
    public static function moduleMeta(): array { return ['code'=>'security','name'=>'Security and Login','type'=>'page','area'=>'member','version'=>'2.0.0','description'=>'Member password/security controls.']; }
    public function Install(string $moduleName = 'security', array $context = []): array { return ['success'=>true,'message'=>'Security module ready.','module'=>$moduleName]; }
    public function editMod(array $context = []): void { $this->adminPanel('Security Module', 'Configure password/security controls.'); }
    public function editPageContent(string|int $page = 'security', array $context = []): void { $this->adminPanel('Security Page Content', 'Security form is runtime user data.'); }
    public function mod_name(string $module = 'security', string $page = 'security', array $payload = []): void
    {
        $this->openShell($page);
        echo '<section class="wi-profile-hero wi-profile-hero--compact"><div class="wi-profile-hero__content"><p class="wi-kicker">Security</p><h2>Security and login</h2><p>Update your password using the active WICMS member session.</p></div></section>';
        echo '<section class="wi-member-panel"><form class="wi-member-form" data-wi-ajax-form="member_password_save">' . $this->csrfField() . '<label>Current password<input type="password" name="current_password" autocomplete="current-password"></label><label>New password<input type="password" name="new_password" autocomplete="new-password"></label><label>Confirm new password<input type="password" name="confirm_password" autocomplete="new-password"></label><button type="submit">Update password</button><div data-wi-form-message></div></form></section>';
        $this->closeShell();
    }
    private function csrfField(): string { return class_exists('WICsrf') ? WICsrf::inputField() : '<input type="hidden" name="csrf_token" value="">'; }
    private function openShell(string $page): void { $m = new WIModules(); echo '<div class="wi-member-shell">'; $m->renderComponent('member_sidebar', ['page'=>$page]); echo '<main class="wi-member-main">'; $m->renderComponent('member_topbar', ['page'=>$page]); }
    private function closeShell(): void { echo '</main></div>'; }
    private function adminPanel(string $title, string $body): void { echo '<section class="wi-admin-module-editor"><h2>' . wi_e($title) . '</h2><p>' . wi_e($body) . '</p></section>'; }
}
