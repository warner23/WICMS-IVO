<?php
declare(strict_types=1);

class WISite
{
    private WIdb $WIdb;
    private WIMaintenace $maint;
    private int $settingsRowId = 1;

    public function __construct()
    {
        $this->WIdb = WIdb::getInstance();
        $this->maint = new WIMaintenace();
    }

    /*
    |--------------------------------------------------------------------------
    | Shared settings helpers
    |--------------------------------------------------------------------------
    */

    private function extractSettingsPayload(array $settings): array
    {
        if (isset($settings['UserData']) && is_array($settings['UserData'])) {
            return $settings['UserData'];
        }

        return $settings;
    }

    private function updateSiteSettings(array $data): bool
    {
        if ($data === []) {
            return false;
        }

        return $this->WIdb->update(
            'wi_site',
            $data,
            '`id` = :id',
            ['id' => $this->settingsRowId]
        );
    }

    private function logSettingsChange(string $message): void
    {
        $userId = (string) WISession::get('user_id', '0');
        $this->maint->Notifications($userId, $message);
    }

    private function settingsResult(bool $success, string $successMessage = ''): array
    {
        if ($success) {
            return [
                'status' => 'success',
                'message' => $successMessage !== ''
                    ? $successMessage
                    : WILang::get('successfully_updated_site_settings'),
            ];
        }

        return [
            'status' => 'error',
            'message' => 'Unable to update settings.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Settings methods
    |--------------------------------------------------------------------------
    */

    public function Site_Settings(array $settings): array
    {
        $site = $this->extractSettingsPayload($settings);

        $ok = $this->updateSiteSettings($site);

        if ($ok) {
            $this->logSettingsChange('Updated site settings');
        }

        return $this->settingsResult($ok);
    }

    public function DataBase_settings(array $settings): array
    {
        $database = $this->extractSettingsPayload($settings);

        // keep database values stored in wi_site if you still want them visible/editable
        // but do NOT recreate db.php anymore
        unset($database['TYPE'], $database['HOST'], $database['USER'], $database['PASS'], $database['NAME']);

        $ok = $this->updateSiteSettings($database);

        if ($ok) {
            $this->logSettingsChange('Updated database settings');
        }

        return $this->settingsResult(
            $ok,
            $ok
                ? 'Database settings saved. Server connection values are now expected from environment configuration.'
                : ''
        );
    }

    public function Email_settings(array $settings): array
    {
        $email = $this->extractSettingsPayload($settings);

        $ok = $this->updateSiteSettings($email);

        if ($ok) {
            $this->logSettingsChange('Updated email settings');
        }

        return $this->settingsResult($ok);
    }

    public function Email_Method(array $mailer): array
    {
        $mailerSettings = $this->extractSettingsPayload($mailer);

        $ok = $this->updateSiteSettings($mailerSettings);

        if ($ok) {
            $this->logSettingsChange('Changed email method');
        }

        return $this->settingsResult($ok);
    }

    public function Session_Settings(array $settings): array
    {
        $session = $this->extractSettingsPayload($settings);

        $ok = $this->updateSiteSettings($session);

        if ($ok) {
            $this->logSettingsChange('Updated session settings');
        }

        return $this->settingsResult($ok);
    }

    public function Security_Settings(string $encryption, int|string|null $cost): array
    {
        $data = [
            'password_encryption' => trim($encryption),
            'cost' => is_numeric($cost) ? (int) $cost : 0,
        ];

        $ok = $this->updateSiteSettings($data);

        if ($ok) {
            $this->logSettingsChange('Updated security settings');
        }

        return $this->settingsResult($ok);
    }

    public function Login_Settings(array $settings): array
    {
        $loginSettings = $this->extractSettingsPayload($settings);

        $ok = $this->updateSiteSettings($loginSettings);

        if ($ok) {
            $this->logSettingsChange('Updated login settings');
        }

        return $this->settingsResult($ok);
    }

    public function lang_Settings(array $settings): array
    {
        $lang = $this->extractSettingsPayload($settings);

        $ok = $this->updateSiteSettings($lang);

        if ($ok) {
            $this->logSettingsChange('Updated language settings');
        }

        return $this->settingsResult($ok);
    }

    public function verification_Settings(array $settings): array
    {
        $verification = $this->extractSettingsPayload($settings);

        $ok = $this->updateSiteSettings($verification);

        if ($ok) {
            $this->logSettingsChange('Updated verification settings');
        }

        return $this->settingsResult($ok);
    }

    public function social_settings(array $settings): array
    {
        $social = $this->extractSettingsPayload($settings);

        $ok = $this->updateSiteSettings($social);

        if ($ok) {
            $this->logSettingsChange('Updated social settings');
        }

        return $this->settingsResult($ok);
    }

    public function twitter(array $settings): array
    {
        $twitter = $this->extractSettingsPayload($settings);

        $ok = $this->updateSiteSettings($twitter);

        if ($ok) {
            $this->logSettingsChange('Updated Twitter settings');
        }

        return $this->settingsResult($ok);
    }


    public function VersionControl(string $version): array
    {
        $version = trim($version);

        if ($version === '') {
            return [
                'status' => 'error',
                'message' => 'No version provided.',
            ];
        }

        $this->logSettingsChange('Checked version control');

        return [
            'status' => 'success',
            'message' => 'Version control check queued.',
            'data' => [
                'current_version' => $version,
                'system_version' => defined('WICMS_VERSION') ? WICMS_VERSION : null,
            ],
        ];
    }

    public function AddMultiLang(string $lang, string $keyword, string $translation): array
    {
        $lang = trim($lang);
        $keyword = trim($keyword);
        $translation = trim($translation);

        if ($lang === '' || $keyword === '' || $translation === '') {
            return [
                'status' => 'error',
                'message' => 'Language, keyword and translation are required.',
            ];
        }

        $this->WIdb->insert('wi_multi_lang', [
            'lang' => $lang,
            'keyword' => $keyword,
            'translation' => $translation,
        ]);

        $this->logSettingsChange('Added multi-language translation');

        return [
            'status' => 'success',
            'message' => 'Translation added successfully.',
        ];
    }




    public function Website_Info(string $column): mixed
    {
        $settings = new WISettings();
        return $settings->website($column);
    }

    public function POS_Info(string $column): mixed
    {
        $settings = new WISettings();
        return $settings->pos($column);
    }

    public function Shop_Info(string $column): mixed
    {
        $settings = new WISettings();
        return $settings->shop($column);
    }

    public function Membership_Info(string $column): mixed
    {
        $settings = new WISettings();
        return $settings->membership($column);
    }


    //old stuff
    public function notifications_badge(): void
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_notifications`");
        echo count($result);
    }

    public function notifications_badge_count(): int
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_notifications`");
        return count($result);
    }

    public function MessageBagde(): void
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_admin_msg`");
        echo count($result);
    }

    public function messageBadgeCount(): int
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_admin_msg`");
        return count($result);
    }

    public function TaskBagde(): void
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_tasks`");
        echo count($result);
    }

    public function taskBadgeCount(): int
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_tasks`");
        return count($result);
    }

    public function ActiveChatCount(): void
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_active_chat`");
        echo count($result);
    }

    public function activeChatCountValue(): int
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_active_chat`");
        return count($result);
    }

    public function RegisteredUsers(): void
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_members`");
        echo count($result);
    }

    public function registeredUsersCount(): int
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_members`");
        return count($result);
    }

    public function tasks(): void
    {
        $result = $this->WIdb->bindfree("SELECT * FROM `wi_tasks` ORDER BY `id` DESC");

        echo '<ul class="todo-list">';

        foreach ($result as $res) {
            $id = (int) ($res['id'] ?? 0);
            $task = htmlspecialchars((string) ($res['task'] ?? ''), ENT_QUOTES, 'UTF-8');
            $completed = (string) ($res['completed'] ?? 'N');

            echo '<li>';
            echo '<span class="handle"><i class="fa fa-ellipsis-v"></i><i class="fa fa-ellipsis-v"></i></span>';
            echo '<input type="checkbox" ' . ($completed === 'Y' ? 'checked' : '') . ' disabled> ';
            echo '<span class="text">' . $task . '</span>';
            echo '<div class="tools">';
            echo '<i class="fa fa-check" onclick="WIDashboard.completeTodo(' . $id . ')"></i>';
            echo '</div>';
            echo '</li>';
        }

        echo '</ul>';
    }



    
}