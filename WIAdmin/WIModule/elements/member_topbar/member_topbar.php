<?php
declare(strict_types=1);

/** Central member topbar module. */
final class WIMemberTopbarModule
{
    public static function moduleMeta(): array
    {
        return ['code'=>'member_topbar','name'=>'Member Topbar','type'=>'element','area'=>'member','version'=>'2.0.0','description'=>'Member/profile workspace topbar.'];
    }

    public function Install(string $moduleName = 'member_topbar', array $context = []): array { return ['success'=>true,'message'=>'Member topbar is file-based shared chrome.','module'=>$moduleName]; }
    public function editMod(array $context = []): void { echo '<section class="wi-admin-module-editor"><h2>Member Topbar</h2><p>Shared member chrome.</p></section>'; }
    public function editPageContent(string|int $page = '', array $context = []): void { $this->editMod($context); }

    public function mod_name(string $module = '', string $page = 'profile', array $payload = []): void
    {
        $page = preg_replace('/[^a-zA-Z0-9_\-]/', '', (string)($payload['page'] ?? $page)) ?: 'profile';
        $user = new WIUser();
        echo '<header class="wi-member-topbar">';
        echo '<div><p class="wi-kicker">' . wi_e(ucwords(str_replace('_', ' ', $page))) . '</p><h1>' . wi_e($this->title($page)) . '</h1></div>';
        echo '<div class="wi-member-topbar__actions">';
        echo '<button type="button" class="wi-icon-button" data-wi-theme-toggle aria-label="Toggle contrast">☾</button>';
        echo '<a class="wi-member-avatar-mini" href="profile.php"><img src="' . wi_e($user->avatarUrl()) . '" alt=""><span>' . wi_e($user->fullName()) . '</span></a>';
        echo '</div></header>';
    }

    private function title(string $page): string
    {
        return match ($page) {
            'account' => 'Account details',
            'settings' => 'Settings and notifications',
            'membership', 'upgrade', 'downgrade' => 'Membership and plan',
            'payments', 'userpayments' => 'Payments',
            'transactions' => 'Transactions',
            'security', 'usersecurity' => 'Security and login',
            'forms', 'training', 'documents' => 'Forms, training and documents',
            'actions' => 'My actions',
            'sites' => 'My sites',
            'support' => 'Support centre',
            'delete_profile' => 'Delete profile',
            default => 'Profile workspace',
        };
    }
}
