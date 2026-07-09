<?php
declare(strict_types=1);

/**
 * File Information
 * Written By: Jules Warner / Warner Infinity
 * Company: Warner Infinity
 * Product: WICMS
 * Project: WIMembers
 * File: WIUser.php
 * Location: WIMembers/WICore/WIClass/
 * Type: Class
 * Layer: User
 * Purpose Area: Member workspace, profile, account, settings, payments, forms and training
 * Version: 1.0.0
 * Created: 2026-05-24
 * Last Updated: 2026-05-24
 * Status: Refactored
 * Summary: WICMS-compatible WIMembers modernisation. Keeps WI-prefixed classes, database-driven modules, sessions and page rendering.
 */


class WIUser
{
    private WIdb $WIdb;
    private int $userId;

    public function __construct(?int $userId = null)
    {
        $this->WIdb = WIdb::getInstance();
        $this->userId = $userId !== null && $userId > 0 ? $userId : (int) WISession::get('user_id', 0);
    }

    public function id(?int $newId = null): int
    {
        if ($newId !== null) {
            $this->userId = max(0, $newId);
        }
        return $this->userId;
    }

    public function getInfo(): array
    {
        if ($this->userId <= 0 || !$this->WIdb->tableExists('wi_members')) {
            return [];
        }

        return $this->WIdb->row('SELECT * FROM `wi_members` WHERE `user_id` = :id LIMIT 1', ['id' => $this->userId]);
    }

    public function getDetails(): array
    {
        if ($this->userId <= 0 || !$this->WIdb->tableExists('wi_user_details')) {
            return [];
        }

        return $this->WIdb->row('SELECT * FROM `wi_user_details` WHERE `user_id` = :id LIMIT 1', ['id' => $this->userId]);
    }

    public function updateInfo(array $data): bool
    {
        if ($this->userId <= 0 || $data === []) {
            return false;
        }

        return $this->WIdb->update('wi_members', $data, '`user_id` = :id', ['id' => $this->userId]);
    }

    public function updateDetails(array $data): bool
    {
        if ($this->userId <= 0 || $data === []) {
            return false;
        }

        $existing = $this->getDetails();
        if ($existing === []) {
            $data['user_id'] = $this->userId;
            return $this->WIdb->insert('wi_user_details', $data);
        }

        return $this->WIdb->update('wi_user_details', $data, '`user_id` = :id', ['id' => $this->userId]);
    }

    public function fullName(): string
    {
        $details = $this->getDetails();
        $name = trim((string) ($details['first_name'] ?? '') . ' ' . (string) ($details['last_name'] ?? ''));
        if ($name !== '') {
            return $name;
        }

        $info = $this->getInfo();
        return (string) ($info['username'] ?? 'Member');
    }

    public function avatarUrl(): string
    {
        $details = $this->getDetails();
        $avatar = trim((string) ($details['avatar'] ?? ''));

        if ($avatar !== '') {
            if (preg_match('/^(https?:)?\/\//i', $avatar) || str_starts_with($avatar, 'data:image/')) {
                return $avatar;
            }

            if (ctype_digit($avatar)) {
                $media = $this->mediaById((int)$avatar);
                if ($media !== []) {
                    return $this->mediaAvatarUrl($media);
                }
            }

            if (str_starts_with($avatar, 'WIAdmin/WIMedia/') || str_contains($avatar, '/')) {
                return $this->rootUrl($avatar);
            }

            /* Legacy avatars stored only as a filename. */
            return $this->rootUrl('WIAdmin/WIMedia/Img/avator/' . rawurlencode($avatar));
        }

        return 'https://ui-avatars.com/api/?name=' . rawurlencode($this->fullName()) . '&background=7047ff&color=fff';
    }

    public function setAvatarFromMedia(int $mediaId): array
    {
        if ($this->userId <= 0 || $mediaId <= 0) {
            return ['success' => false, 'message' => 'A valid user and media item are required.'];
        }

        $media = $this->mediaById($mediaId);
        if ($media === []) {
            return ['success' => false, 'message' => 'Selected WIMedia item could not be found.'];
        }

        if ((string)($media['media_type'] ?? '') !== 'image' && !str_starts_with((string)($media['mime_type'] ?? ''), 'image/')) {
            return ['success' => false, 'message' => 'Profile pictures must be image files.'];
        }

        $avatar = trim((string)($media['file_path'] ?? $media['file_url'] ?? ''));
        if ($avatar === '') {
            return ['success' => false, 'message' => 'Selected WIMedia item does not have a usable file path.'];
        }

        $update = ['avatar' => $avatar];
        if ($this->WIdb->columnExists('wi_user_details', 'avatar_media_id')) {
            $update['avatar_media_id'] = $mediaId;
        }

        $this->updateDetails($update);

        return [
            'success' => true,
            'message' => 'Profile picture updated.',
            'media_id' => $mediaId,
            'avatar' => $avatar,
            'avatar_url' => $this->mediaAvatarUrl($media),
            'media' => $media,
        ];
    }

    public function removeAvatar(): array
    {
        if ($this->userId <= 0) {
            return ['success' => false, 'message' => 'A valid user is required.'];
        }

        $update = ['avatar' => ''];
        if ($this->WIdb->columnExists('wi_user_details', 'avatar_media_id')) {
            $update['avatar_media_id'] = null;
        }

        $this->updateDetails($update);

        return [
            'success' => true,
            'message' => 'Profile picture removed.',
            'avatar' => '',
            'avatar_url' => $this->avatarUrl(),
        ];
    }

    private function mediaById(int $mediaId): array
    {
        if ($mediaId <= 0 || !$this->WIdb->tableExists('wi_media')) {
            return [];
        }

        return $this->WIdb->row('SELECT * FROM `wi_media` WHERE `id` = :id LIMIT 1', ['id' => $mediaId]);
    }

    private function mediaAvatarUrl(array $media): string
    {
        /*
         * Prefer WIMedia's canonical relative file_path over file_url.
         * Some older WIMedia storage paths build file_url as /WIAdmin/..., which
         * skips the app folder on localhost installs such as /Aurevia/. From a
         * WIMembers page the safe browser path is ../WIAdmin/WIMedia/...
         */
        $filePath = trim((string)($media['file_path'] ?? ''));
        if ($filePath !== '') {
            return $this->rootUrl($filePath);
        }

        $fileUrl = trim((string)($media['file_url'] ?? ''));
        if ($fileUrl !== '') {
            return $this->normaliseMediaUrl($fileUrl);
        }

        return $this->avatarUrl();
    }

    private function normaliseMediaUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        if (str_starts_with($url, 'data:image/')) {
            return $url;
        }

        $path = parse_url($url, PHP_URL_PATH);
        if (is_string($path) && $path !== '' && str_contains($path, '/WIAdmin/WIMedia/')) {
            $relative = substr($path, strpos($path, '/WIAdmin/WIMedia/') + 1);
            return $this->rootUrl($relative);
        }

        if (str_starts_with($url, '/WIAdmin/WIMedia/')) {
            return $this->rootUrl(ltrim($url, '/'));
        }

        if (str_starts_with($url, 'WIAdmin/WIMedia/')) {
            return $this->rootUrl($url);
        }

        if (preg_match('/^(https?:)?\/\//i', $url)) {
            return $url;
        }

        return $this->rootUrl($url);
    }

    private function rootUrl(string $path): string
    {
        $script = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? ''));
        $path = ltrim(trim($path), '/');
        return str_contains($script, '/WIMembers/') ? '../' . $path : $path;
    }

    public function getRoleId(): int
    {
        $info = $this->getInfo();
        return (int) ($info['user_role'] ?? 1);
    }

    public function getRoleName(): string
    {
        $roleId = $this->getRoleId();
        if ($this->WIdb->tableExists('wi_user_roles')) {
            $row = $this->WIdb->row('SELECT `role` FROM `wi_user_roles` WHERE `role_id` = :id LIMIT 1', ['id' => $roleId]);
            if ($row !== []) {
                return (string) $row['role'];
            }
        }
        return $roleId > 1 ? 'Member' : 'Website User';
    }

    public function isAdmin(): bool
    {
        return $this->getRoleId() > 4;
    }

    public function disableAccount(): bool
    {
        if ($this->userId <= 0) {
            return false;
        }

        return $this->WIdb->update('wi_members', ['banned' => 'Y'], '`user_id` = :id', ['id' => $this->userId]);
    }

    public function profileCompletion(): int
    {
        $details = $this->getDetails();
        $fields = ['first_name', 'last_name', 'phone', 'address', 'country', 'city', 'bio_body', 'avatar'];
        $filled = 0;
        foreach ($fields as $field) {
            if (trim((string) ($details[$field] ?? '')) !== '') {
                $filled++;
            }
        }
        return (int) round(($filled / count($fields)) * 100);
    }
}
