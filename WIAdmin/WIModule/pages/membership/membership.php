<?php
declare(strict_types=1);

/** Membership module using the standard WI module lifecycle. */
final class WIMembershipModule
{
    public static function moduleMeta(): array { return ['code'=>'membership','name'=>'Membership','type'=>'page','area'=>'member','version'=>'2.0.0','description'=>'Member plan summary and plan movement.']; }
    public function Install(string $moduleName = 'membership', array $context = []): array { return ['success'=>true,'message'=>'Membership module ready.','module'=>$moduleName]; }
    public function editMod(array $context = []): void { $this->adminPanel('Membership Module', 'Configure membership/plan cards.'); }
    public function editPageContent(string|int $page = 'membership', array $context = []): void { $this->adminPanel('Membership Page Content', 'Membership details are loaded from the member account.'); }
    public function mod_name(string $module = 'membership', string $page = 'membership', array $payload = []): void
    {
        $summary = $this->summary();
        $mode = $page === 'upgrade' ? 'upgrade' : ($page === 'downgrade' ? 'downgrade' : 'membership');
        $this->openShell($page);
        echo '<section class="wi-membership-card"><div><p class="wi-kicker">Current plan</p><h2>' . wi_e(ucwords(str_replace(['_','-'], ' ', (string)($summary['plan'] ?? 'member')))) . '</h2><p>Status: <strong>' . wi_e((string)($summary['status'] ?? 'active')) . '</strong></p></div><div><span>Last payment</span><strong>' . wi_e((string)($summary['currency'] ?? 'GBP')) . ' ' . number_format((float)($summary['last_payment'] ?? 0), 2) . '</strong></div></section>';
        if ($mode === 'upgrade') { $this->planPanel('Upgrade options', 'Upgrade paths will connect to WILabs marketplace/package billing when pricing is live.', 'Return to membership', 'membership.php'); }
        elseif ($mode === 'downgrade') { $this->planPanel('Downgrade options', 'Downgrade requests can be reviewed safely without deleting profile, HR, compliance or audit history.', 'Return to membership', 'membership.php'); }
        else { echo '<section class="wi-member-grid wi-member-grid--cards">'; $this->card('Upgrade','Move to a higher plan or unlock extra modules when marketplace pricing is ready.','upgrade.php','Upgrade options'); $this->card('Downgrade','Manage membership level without deleting your profile or audit-linked records.','downgrade.php','Downgrade options'); echo '</section>'; }
        $this->closeShell();
    }
    private function summary(): array { try { return (new WIMembership())->summary((int)WISession::get('user_id', 0)); } catch (Throwable $e) { return ['plan'=>'member','status'=>'active','currency'=>'GBP','last_payment'=>0]; } }
    private function planPanel(string $title, string $body, string $button, string $href): void { echo '<section class="wi-member-panel"><h2>' . wi_e($title) . '</h2><p>' . wi_e($body) . '</p><a class="wi-mini-link" href="' . wi_e($href) . '">' . wi_e($button) . '</a></section>'; }
    private function card(string $title, string $body, string $href, string $button): void { echo '<article class="wi-member-card"><h3>' . wi_e($title) . '</h3><p>' . wi_e($body) . '</p><a href="' . wi_e($href) . '">' . wi_e($button) . '</a></article>'; }
    private function openShell(string $page): void { $m = new WIModules(); echo '<div class="wi-member-shell">'; $m->renderComponent('member_sidebar', ['page'=>$page]); echo '<main class="wi-member-main">'; $m->renderComponent('member_topbar', ['page'=>$page]); }
    private function closeShell(): void { echo '</main></div>'; }
    private function adminPanel(string $title, string $body): void { echo '<section class="wi-admin-module-editor"><h2>' . wi_e($title) . '</h2><p>' . wi_e($body) . '</p></section>'; }
}
