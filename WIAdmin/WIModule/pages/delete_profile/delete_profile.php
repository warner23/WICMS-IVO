<?php
declare(strict_types=1);

/** Delete profile module using the standard WI module lifecycle. */
final class WIDeleteProfileModule
{
    public static function moduleMeta(): array { return ['code'=>'delete_profile','name'=>'Delete Profile','type'=>'page','area'=>'member','version'=>'2.0.0','description'=>'Safe profile deletion request form.']; }
    public function Install(string $moduleName = 'delete_profile', array $context = []): array { return ['success'=>true,'message'=>'Delete profile module ready.','module'=>$moduleName]; }
    public function editMod(array $context = []): void { $this->adminPanel('Delete Profile Module', 'Configure deletion-request messaging.'); }
    public function editPageContent(string|int $page = 'delete_profile', array $context = []): void { $this->adminPanel('Delete Profile Page Content', 'Deletion form is protected by member auth and CSRF.'); }
    public function mod_name(string $module = 'delete_profile', string $page = 'delete_profile', array $payload = []): void
    {
        $this->openShell($page);
        echo '<section class="wi-member-panel wi-danger-panel"><h2>Delete profile</h2><p>This records a deletion request and disables login. It does not blindly destroy compliance, HR, audit or legal records that may need retention.</p><form class="wi-member-form" data-wi-ajax-form="member_delete_request">' . $this->csrfField() . '<label>Reason<textarea name="reason"></textarea></label><label>Type DELETE to confirm<input name="confirm_text" autocomplete="off"></label><button type="submit" class="danger">Request deletion</button><div data-wi-form-message></div></form></section>';
        $this->closeShell();
    }
    private function csrfField(): string { return class_exists('WICsrf') ? WICsrf::inputField() : '<input type="hidden" name="csrf_token" value="">'; }
    private function openShell(string $page): void { $m = new WIModules(); echo '<div class="wi-member-shell">'; $m->renderComponent('member_sidebar', ['page'=>$page]); echo '<main class="wi-member-main">'; $m->renderComponent('member_topbar', ['page'=>$page]); }
    private function closeShell(): void { echo '</main></div>'; }
    private function adminPanel(string $title, string $body): void { echo '<section class="wi-admin-module-editor"><h2>' . wi_e($title) . '</h2><p>' . wi_e($body) . '</p></section>'; }
}
