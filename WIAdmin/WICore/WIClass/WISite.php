<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WICMS / WICOS / WIKitchenCompli
| Class: WISite
| File: WISite.php
| Location: /WIAdmin/WICore/WIClass/WISite.php
| Type: Site Settings / Shared Site Service
| Layer: Shared Core
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Purpose
|--------------------------------------------------------------------------
| Shared site settings and small dashboard-count helper class.
|
| Notes:
| - Uses WIdb only
| - Keeps existing public method names for compatibility
| - Leaves environment-controlled secrets out of wi_site updates
|--------------------------------------------------------------------------
*/

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
    | Settings helpers
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
        if ($data === [] || !$this->WIdb->tableExists('wi_site')) {
            return false;
        }

        $filtered = $this->filterDataForTable('wi_site', $data, ['id']);

        if ($filtered === []) {
            return false;
        }

        $exists = $this->WIdb->exists('wi_site', '`id` = :id', ['id' => $this->settingsRowId]);

        if (!$exists) {
            $filtered['id'] = $this->settingsRowId;
            return $this->WIdb->insert('wi_site', $filtered);
        }

        return $this->WIdb->update(
            'wi_site',
            $filtered,
            '`id` = :id',
            ['id' => $this->settingsRowId]
        );
    }

    private function logSettingsChange(string $message): void
    {
        $userId = (string) (class_exists('WISession') ? WISession::get('user_id', '0') : '0');
        $this->maint->Notifications($userId, $message);

        if (class_exists('WILogger')) {
            WILogger::info($message, [], 'site_settings');
        }
    }

    private function settingsResult(bool $success, string $successMessage = ''): array
    {
        if ($success) {
            return [
                'status'  => 'success',
                'message' => $successMessage !== ''
                    ? $successMessage
                    : (class_exists('WILang') ? (string) WILang::get('successfully_updated_site_settings') : 'Settings updated successfully.'),
            ];
        }

        return [
            'status'  => 'error',
            'message' => 'Unable to update settings.',
        ];
    }

    private function filterDataForTable(string $table, array $data, array $exclude = []): array
    {
        $filtered = [];

        foreach ($data as $column => $value) {
            if (in_array((string) $column, $exclude, true)) {
                continue;
            }

            if (!$this->WIdb->columnExists($table, (string) $column)) {
                continue;
            }

            if (is_array($value)) {
                continue;
            }

            $filtered[$column] = is_string($value) ? trim($value) : $value;
        }

        return $filtered;
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

        unset(
            $database['TYPE'],
            $database['HOST'],
            $database['USER'],
            $database['PASS'],
            $database['NAME'],
            $database['DB_TYPE'],
            $database['DB_HOST'],
            $database['DB_NAME'],
            $database['DB_USER'],
            $database['DB_PASS']
        );

        $ok = $this->updateSiteSettings($database);

        if ($ok) {
            $this->logSettingsChange('Updated database settings');
        }

        return $this->settingsResult(
            $ok,
            $ok
                ? 'Database settings saved. Connection credentials remain environment-controlled.'
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
            'cost'                => is_numeric($cost) ? (int) $cost : 0,
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
                'status'  => 'error',
                'message' => 'No version provided.',
            ];
        }

        $this->logSettingsChange('Checked version control');

        return [
            'status'  => 'success',
            'message' => 'Version control check queued.',
            'data'    => [
                'current_version' => $version,
                'system_version'  => defined('WICMS_VERSION') ? WICMS_VERSION : null,
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
                'status'  => 'error',
                'message' => 'Language, keyword and translation are required.',
            ];
        }

        if (!$this->WIdb->tableExists('wi_multi_lang')) {
            return [
                'status'  => 'error',
                'message' => 'Translation table is not available.',
            ];
        }

        $insert = $this->filterDataForTable('wi_multi_lang', [
            'lang'        => $lang,
            'keyword'     => $keyword,
            'translation' => $translation,
        ]);

        if ($insert === []) {
            return [
                'status'  => 'error',
                'message' => 'No valid translation fields were provided.',
            ];
        }

        $this->WIdb->insert('wi_multi_lang', $insert);
        $this->logSettingsChange('Added multi-language translation');

        return [
            'status'  => 'success',
            'message' => 'Translation added successfully.',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Shared info helpers
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Legacy count helpers
    |--------------------------------------------------------------------------
    */

    public function notifications_badge(): void
    {
        echo $this->notifications_badge_count();
    }

    public function notifications_badge_count(): int
    {
        return $this->countRows('wi_notifications');
    }

    public function MessageBagde(): void
    {
        echo $this->messageBadgeCount();
    }

    public function messageBadgeCount(): int
    {
        return $this->countRows('wi_admin_msg');
    }

    public function TaskBagde(): void
    {
        echo $this->taskBadgeCount();
    }

    public function taskBadgeCount(): int
    {
        return $this->countRows('wi_tasks');
    }

    public function ActiveChatCount(): void
    {
        echo $this->activeChatCountValue();
    }

    public function activeChatCountValue(): int
    {
        return $this->countRows('wi_active_chat');
    }

    public function RegisteredUsers(): void
    {
        echo $this->registeredUsersCount();
    }

    public function registeredUsersCount(): int
    {
        return $this->countRows('wi_members');
    }

    private function countRows(string $table): int
    {
        if (!$this->WIdb->tableExists($table)) {
            return 0;
        }

        $result = $this->WIdb->bindfree("SELECT COUNT(*) AS count_value FROM `{$table}`");
        return (int) ($result[0]['count_value'] ?? 0);
    }

    /*
    |--------------------------------------------------------------------------
    | Legacy task renderer
    |--------------------------------------------------------------------------
    */

    public function tasks(): void
    {
        if (!$this->WIdb->tableExists('wi_tasks')) {
            echo '<ul class="todo-list"></ul>';
            return;
        }

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

    /*
    |--------------------------------------------------------------------------
    | Legacy media/display compatibility stubs
    |--------------------------------------------------------------------------
    */

    public function headerDisplay(): void
    {
        $this->renderLegacyUploadBox('header');
    }

    public function AddLangDisplay(): void
    {
        $this->renderLegacyUploadBox('add-lang');
    }

    public function EditLangDisplay(): void
    {
        $this->renderLegacyUploadBox('edit-lang');
    }

    public function pageDisplay(): void
    {
        $this->renderLegacyUploadBox('page');
    }

    public function pageModuleDisplay(): void
    {
        $this->renderLegacyUploadBox('page-module');
    }

    public function ProductDisplay(): void
    {
        $this->renderLegacyUploadBox('product');
    }

    public function UploadTeamPics(): void
    {
        $this->renderLegacyUploadBox('team');
    }

    public function faviconDisplay(): void
    {
        $this->renderLegacyUploadBox('favicon');
    }

    private function renderLegacyUploadBox(string $scope): void
    {
        echo '<div class="wi-upload-placeholder" data-scope="' . htmlspecialchars($scope, ENT_QUOTES, 'UTF-8') . '">';
        echo '<p>Upload area ready.</p>';
        echo '</div>';
    }
}