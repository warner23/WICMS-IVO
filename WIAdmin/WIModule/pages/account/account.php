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
| File: account.php
| Location: /WIAdmin/WIModule/pages/account/account.php
| Type: Module
| Layer: Front-Side UI Module
| Purpose Area: Member profile/account details
| Version: 2.1.0
| Created: 2026-05-24
| Last Updated: 2026-06-14
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Renders the member account workspace through the canonical WIModules flow.
| This file is UI-only: it does not query the database directly and delegates
| profile/account persistence to existing shared WIUser/member AJAX services.
*/

final class WIAccountModule
{
    public static function moduleMeta(): array
    {
        return [
            'code' => 'account',
            'name' => 'Account Details',
            'type' => 'page',
            'area' => 'member',
            'version' => '2.1.0',
            'description' => 'Member account and profile details workspace.',
        ];
    }

    public function Install(string $moduleName = 'account', array $context = []): array
    {
        return ['success' => true, 'message' => 'Account module ready.', 'module' => $moduleName];
    }

    public function editMod(array $context = []): void
    {
        $this->adminPanel('Account module', 'Member account details module. Rendering is handled by the front-side module runtime.');
    }

    public function editPageContent(string|int $page = 'account', array $context = []): void
    {
        $this->adminPanel('Account page content', 'Account content is rendered from WIUser/profile payloads.');
    }

    public function mod_name(string $module = 'account', string $page = 'account', array $payload = []): void
    {
        [$info, $details] = $this->accountPayload();

        $this->openShell($page);
        echo '<section class="wi-profile-hero wi-profile-hero--compact"><div class="wi-profile-hero__content"><p class="wi-kicker">Account</p><h2>Account details</h2><p>Keep your login, contact and profile details correct. Compliance, HR and audit records stay linked to this user account.</p></div></section>';
        echo '<section class="wi-member-grid wi-member-grid--two">';
        $this->renderAccountForm($info);
        $this->renderProfileForm($details);
        echo '</section>';
        $this->closeShell();
    }

    /** @return array{0:array<string,mixed>,1:array<string,mixed>} */
    private function accountPayload(): array
    {
        try {
            $user = new WIUser();
            return [
                is_array($user->getInfo()) ? $user->getInfo() : [],
                is_array($user->getDetails()) ? $user->getDetails() : [],
            ];
        } catch (Throwable $e) {
            return [[], []];
        }
    }

    /** @param array<string,mixed> $info */
    private function renderAccountForm(array $info): void
    {
        echo '<section class="wi-member-panel"><div class="wi-panel-head"><div><p class="wi-kicker">Login</p><h2>Account login</h2></div></div>';
        echo '<form class="wi-member-form" data-wi-ajax-form="member_account_save">';
        echo $this->csrfField();
        echo '<label>Email<input name="email" type="email" autocomplete="email" value="' . wi_e($info['email'] ?? '') . '"></label>';
        echo '<label>Username<input name="username" autocomplete="username" value="' . wi_e($info['username'] ?? '') . '"></label>';
        echo '<button type="submit">Save account</button><div data-wi-form-message></div>';
        echo '</form></section>';
    }

    /** @param array<string,mixed> $details */
    private function renderProfileForm(array $details): void
    {
        echo '<section class="wi-member-panel"><div class="wi-panel-head"><div><p class="wi-kicker">Profile</p><h2>Profile information</h2></div></div>';
        echo '<form class="wi-member-form wi-member-form--grid" data-wi-ajax-form="member_profile_save">';
        echo $this->csrfField();
        echo '<label>First name<input name="first_name" autocomplete="given-name" value="' . wi_e($details['first_name'] ?? '') . '"></label>';
        echo '<label>Last name<input name="last_name" autocomplete="family-name" value="' . wi_e($details['last_name'] ?? '') . '"></label>';
        echo '<label>Phone<input name="phone" autocomplete="tel" value="' . wi_e($details['phone'] ?? '') . '"></label>';
        echo '<label>City<input name="city" autocomplete="address-level2" value="' . wi_e($details['city'] ?? '') . '"></label>';
        echo '<label class="wide">Address<input name="address" autocomplete="street-address" value="' . wi_e($details['address'] ?? '') . '"></label>';
        echo '<label class="wide">Bio<textarea name="bio_body">' . wi_e($details['bio_body'] ?? '') . '</textarea></label>';
        echo '<button type="submit">Save profile</button><div data-wi-form-message></div>';
        echo '</form></section>';
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
