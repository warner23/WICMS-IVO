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
| File: member_sidebar.php
| Location: /WIAdmin/WIModule/pages/member_sidebar/member_sidebar.php
| Type: Component Module
| Layer: Front-Side UI Component
| Purpose Area: Member workspace navigation
| Version: 2.1.0
| Created: 2026-05-24
| Last Updated: 2026-06-14
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Shared member/profile sidebar component loaded by WIModules::renderComponent().
| The component is UI-only and keeps routes inside /WIMembers/ or the correct
| product-owned area instead of sending member pages back to root by default.
*/

final class WIMemberSidebarModule
{
    public static function moduleMeta(): array
    {
        return [
            'code' => 'member_sidebar',
            'name' => 'Member Sidebar',
            'type' => 'component',
            'area' => 'member',
            'version' => '2.1.0',
            'description' => 'Reusable WIProfile/WIMembers sidebar navigation.',
        ];
    }

    public function Install(string $moduleName = 'member_sidebar', array $context = []): array
    {
        return ['success' => true, 'message' => 'Member sidebar component ready.', 'module' => $moduleName];
    }

    public function editMod(array $context = []): void
    {
        echo '<section class="wi-admin-module-editor"><h2>Member sidebar</h2><p>Shared member workspace sidebar component.</p></section>';
    }

    public function editPageContent(string|int $page = 'member_sidebar', array $context = []): void
    {
        $this->editMod($context);
    }

    public function mod_name(string $module = 'member_sidebar', string $page = 'profile', array $payload = []): void
    {
        $page = $this->normalisePage((string)($payload['page'] ?? $page));
        $items = $this->items();

        echo '<aside class="wi-member-sidebar">';
        echo '<div class="wi-member-sidebar__brand"><span class="wi-member-mark">WI</span><div><strong>WIProfile</strong><small>Member workspace</small></div></div>';
        echo '<nav class="wi-member-sidebar__nav">';

        foreach ($items as $key => $item) {
            $active = $key === $page ? ' is-active' : '';
            echo '<a class="wi-member-nav-item' . $active . '" href="' . wi_e($item['href']) . '"><span data-icon="' . wi_e($item['icon']) . '"></span>' . wi_e($item['label']) . '</a>';
        }

        echo '</nav>';
        echo '<div class="wi-member-sidebar__footer"><a href="../index.php">Public site</a><a href="../alogin.php">Admin portal</a><a href="logout.php">Logout</a></div>';
        echo '</aside>';
    }

    /** @return array<string,array{label:string,icon:string,href:string}> */
    private function items(): array
    {
        return [
            'profile' => ['label' => 'Profile', 'icon' => 'user-round', 'href' => 'profile.php'],
            'account' => ['label' => 'Account', 'icon' => 'id-card', 'href' => 'account.php'],
            'forms' => ['label' => 'Forms & Training', 'icon' => 'clipboard-list', 'href' => 'forms.php'],
            'settings' => ['label' => 'Settings', 'icon' => 'sliders-horizontal', 'href' => 'settings.php'],
            'support' => ['label' => 'Support', 'icon' => 'life-buoy', 'href' => 'support.php'],
        ];
    }

    private function normalisePage(string $page): string
    {
        $page = preg_replace('/[^A-Za-z0-9_\-]/', '', $page) ?: 'profile';
        return str_replace('-', '_', $page);
    }
}
