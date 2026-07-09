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
| File: settings.php
| Location: /WIAdmin/WIModule/pages/settings/settings.php
| Type: Module
| Layer: Front-Side UI Module
| Purpose Area: Member preferences/settings
| Version: 2.1.0
| Created: 2026-05-24
| Last Updated: 2026-06-14
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Renders the member settings workspace through the canonical WIModules flow.
| The module is UI-only and safely falls back to default preferences when the
| settings service/table is not available in a fresh install.
*/

final class WISettingsModule
{
    public static function moduleMeta(): array
    {
        return [
            'code' => 'settings',
            'name' => 'Member Settings',
            'type' => 'page',
            'area' => 'member',
            'version' => '2.1.0',
            'description' => 'Member notification and appearance preferences.',
        ];
    }

    public function Install(string $moduleName = 'settings', array $context = []): array
    {
        return ['success' => true, 'message' => 'Settings module ready.', 'module' => $moduleName];
    }

    public function editMod(array $context = []): void
    {
        $this->adminPanel('Settings module', 'Member preferences module. Rendering is handled by the front-side module runtime.');
    }

    public function editPageContent(string|int $page = 'settings', array $context = []): void
    {
        $this->adminPanel('Settings page content', 'Settings are rendered from WISettings/user preference payloads.');
    }

    public function mod_name(string $module = 'settings', string $page = 'settings', array $payload = []): void
    {
        $settings = $this->settingsPayload();

        $this->openShell($page);
        echo '<section class="wi-profile-hero wi-profile-hero--compact"><div class="wi-profile-hero__content"><p class="wi-kicker">Preferences</p><h2>Settings and notifications</h2><p>Control member-side preferences, notification channels and visual defaults.</p></div></section>';
        echo '<section class="wi-member-panel"><form class="wi-member-form" data-wi-ajax-form="member_settings_save">';
        echo $this->csrfField();
        $this->check('email_notifications', 'Email notifications', $settings);
        $this->check('training_notifications', 'Training reminders', $settings);
        $this->check('marketing_updates', 'Product and membership updates', $settings);
        echo '<label>Appearance<select name="appearance">';
        foreach (['dark' => 'Dark', 'light' => 'Light', 'system' => 'System'] as $value => $label) {
            $selected = (string) ($settings['appearance'] ?? 'dark') === $value ? ' selected' : '';
            echo '<option value="' . wi_e($value) . '"' . $selected . '>' . wi_e($label) . '</option>';
        }
        echo '</select></label><button type="submit">Save settings</button><div data-wi-form-message></div></form></section>';
        $this->closeShell();
    }

    /** @return array<string,mixed> */
    private function settingsPayload(): array
    {
        $defaults = [
            'email_notifications' => 1,
            'training_notifications' => 1,
            'marketing_updates' => 0,
            'appearance' => 'dark',
        ];

        try {
            if (class_exists('WISettings')) {
                $settings = (new WISettings())->getForUser((int) WISession::get('user_id', 0));
                return is_array($settings) ? array_merge($defaults, $settings) : $defaults;
            }
        } catch (Throwable $e) {
            return $defaults;
        }

        return $defaults;
    }

    /** @param array<string,mixed> $settings */
    private function check(string $name, string $label, array $settings): void
    {
        $checked = (int) ($settings[$name] ?? 0) === 1 ? ' checked' : '';
        echo '<label class="wi-check"><input type="checkbox" name="' . wi_e($name) . '" value="1"' . $checked . '> ' . wi_e($label) . '</label>';
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

    private function csrfField(): string
    {
        return class_exists('WICsrf') && method_exists('WICsrf', 'inputField') ? WICsrf::inputField() : '';
    }

    private function adminPanel(string $title, string $body): void
    {
        echo '<section class="wi-admin-module-editor"><h2>' . wi_e($title) . '</h2><p>' . wi_e($body) . '</p></section>';
    }
}
