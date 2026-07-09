<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: member_topbar.php
 * Location: WIMembers/WICore/WIModules/components/member_topbar/
 * Type: Module
 * Layer: UI
 * Purpose Area: Member workspace, profile, account, settings, payments, forms and training
 * Version: 1.0.0
 * Created: 2026-05-24
 * Last Updated: 2026-05-24
 * Status: Refactored
 * Summary: WICMS-compatible WIMembers modernisation. Keeps WI-prefixed classes, database-driven modules, sessions and page rendering.
 */


class WIMemberTopbarModule
{
    public function mod_name(string $module = '', string $page = 'profile', array $payload = []): void
    {
        $user = new WIUser();
        echo '<header class="wi-member-topbar"><div><p class="wi-kicker">' . wi_e(ucwords(str_replace('_', ' ', $page))) . '</p><h1>' . wi_e($this->title($page)) . '</h1></div><div class="wi-member-topbar__actions"><button type="button" class="wi-icon-button" data-wi-theme-toggle>☾</button><a class="wi-member-avatar-mini" href="profile.php"><img src="' . wi_e($user->avatarUrl()) . '" alt=""><span>' . wi_e($user->fullName()) . '</span></a></div></header>';
    }

    private function title(string $page): string
    {
        return match ($page) {
            'account' => 'Account details',
            'settings' => 'Settings and preferences',
            'membership' => 'Membership and plan',
            'payments' => 'Payments',
            'transactions' => 'Transactions',
            'security' => 'Security and login',
            'forms' => 'Forms, HR and training',
            'support' => 'Support centre',
            'delete_profile' => 'Delete profile',
            default => 'Profile workspace',
        };
    }
}
