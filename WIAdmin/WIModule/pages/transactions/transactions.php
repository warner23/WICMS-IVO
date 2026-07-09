<?php
declare(strict_types=1);

/** Payments module using the standard WI module lifecycle. */
final class WITransactionsModule
{
    public static function moduleMeta(): array { return ['code'=>'transactions','name'=>'Transactions','type'=>'page','area'=>'member','version'=>'2.0.0','description'=>'Member order/payment history.']; }
    public function Install(string $moduleName = 'payments', array $context = []): array { return ['success'=>true,'message'=>'Payments module ready.','module'=>$moduleName]; }
    public function editMod(array $context = []): void { $this->adminPanel('Payments Module', 'Configure member payment list display.'); }
    public function editPageContent(string|int $page = 'payments', array $context = []): void { $this->adminPanel('Payments Page Content', 'Payment rows are loaded from the member account.'); }
    public function mod_name(string $module = 'payments', string $page = 'payments', array $payload = []): void
    {
        $orders = $this->orders();
        $this->openShell($page);
        echo '<section class="wi-profile-hero wi-profile-hero--compact"><div class="wi-profile-hero__content"><p class="wi-kicker">Transactions</p><h2>Transaction history</h2><p>Review membership, package and plugin order payments linked to your account.</p></div></section>';
        echo '<section class="wi-member-panel"><div class="wi-table-wrap"><table><thead><tr><th>Date</th><th>Item</th><th>Status</th><th>Total</th></tr></thead><tbody>';
        if ($orders === []) { echo '<tr><td colspan="4">No payments found yet.</td></tr>'; }
        foreach ($orders as $order) { echo '<tr><td>' . wi_e((string)($order['order_date'] ?? '')) . '</td><td>' . wi_e((string)($order['plugin_slug'] ?? $order['item_name'] ?? '')) . '</td><td>' . wi_e((string)($order['order_status'] ?? '')) . '</td><td>' . wi_e((string)($order['order_currency'] ?? 'GBP')) . ' ' . number_format((float)($order['order_price'] ?? $order['total'] ?? 0), 2) . '</td></tr>'; }
        echo '</tbody></table></div></section>';
        $this->closeShell();
    }
    private function orders(): array { try { return (new WIPayments())->orders((int)WISession::get('user_id', 0)); } catch (Throwable $e) { return []; } }
    private function openShell(string $page): void { $m = new WIModules(); echo '<div class="wi-member-shell">'; $m->renderComponent('member_sidebar', ['page'=>$page]); echo '<main class="wi-member-main">'; $m->renderComponent('member_topbar', ['page'=>$page]); }
    private function closeShell(): void { echo '</main></div>'; }
    private function adminPanel(string $title, string $body): void { echo '<section class="wi-admin-module-editor"><h2>' . wi_e($title) . '</h2><p>' . wi_e($body) . '</p></section>'; }
}
