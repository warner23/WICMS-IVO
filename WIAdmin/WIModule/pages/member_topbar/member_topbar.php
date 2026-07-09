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
| File: member_topbar.php
| Location: /WIAdmin/WIModule/pages/member_topbar/member_topbar.php
| Type: Component Module
| Layer: Front-Side UI Component
| Purpose Area: Member workspace topbar
| Version: 2.1.0
| Created: 2026-05-24
| Last Updated: 2026-06-14
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Shared member/profile topbar component loaded by WIModules::renderComponent().
| The component is UI-only and safely falls back if a user/avatar service is not
| available during a fresh install or partial module test.
*/

final class WIMemberTopbarModule
{
    public static function moduleMeta(): array
    {
        return [
            'code' => 'member_topbar',
            'name' => 'Member Topbar',
            'type' => 'component',
            'area' => 'member',
            'version' => '2.1.0',
            'description' => 'Reusable WIProfile/WIMembers topbar component.',
        ];
    }

    public function Install(string $moduleName = 'member_topbar', array $context = []): array
    {
        return ['success' => true, 'message' => 'Member topbar component ready.', 'module' => $moduleName];
    }

    public function editMod(array $context = []): void
    {
        echo '<section class="wi-admin-module-editor"><h2>Member topbar</h2><p>Shared member workspace topbar component.</p></section>';
    }

    public function editPageContent(string|int $page = 'member_topbar', array $context = []): void
    {
        $this->editMod($context);
    }

    public function mod_name(string $module = 'member_topbar', string $page = 'profile', array $payload = []): void
    {
        $page = $this->normalisePage((string)($payload['page'] ?? $page));
        [$name, $avatar] = $this->userPayload();

        echo '<header class="wi-member-topbar">';
        echo '<div><p class="wi-kicker">' . wi_e(ucwords(str_replace('_', ' ', $page))) . '</p><h1>' . wi_e($this->title($page)) . '</h1></div>';
        echo '<div class="wi-member-topbar__actions"><button type="button" class="wi-icon-button" data-wi-theme-toggle>☾</button><a class="wi-member-avatar-mini" href="profile.php"><img src="' . wi_e($avatar) . '" alt=""><span>' . wi_e($name) . '</span></a></div>';
        echo '</header>';
    }

    /** @return array{0:string,1:string} */
    private function userPayload(): array
    {
        try {
            if (class_exists('WIUser')) {
                $user = new WIUser();
                $name = method_exists($user, 'fullName') ? (string) $user->fullName() : 'Member';
                $avatar = method_exists($user, 'avatarUrl') ? (string) $user->avatarUrl() : '';
                return [$name !== '' ? $name : 'Member', $avatar];
            }
        } catch (Throwable $e) {
            return ['Member', ''];
        }

        return ['Member', ''];
    }

    private function title(string $page): string
    {
        return match ($page) {
            'account' => 'Account details',
            'settings' => 'Settings and preferences',
            'forms' => 'Forms, HR and training',
            'support' => 'Support centre',
            default => 'Profile workspace',
        };
    }

    private function normalisePage(string $page): string
    {
        $page = preg_replace('/[^A-Za-z0-9_\-]/', '', $page) ?: 'profile';
        return str_replace('-', '_', $page);
    }
}
