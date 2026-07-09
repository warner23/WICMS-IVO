<?php
declare(strict_types=1);

/**
 * FILE:
 * /WIAdmin/WICore/WIClass/Media/WIMediaAccessPolicy.php
 *
 * Written By: Jules Warner
 * Company: WILabs
 * Product: WICMS / WICOS / WIKitchenCompli
 * Class: WIMediaAccessPolicy
 * Type: Shared Media Access Policy
 * Layer: Shared Core
 * Version: 1.0.0
 * Status: Active
 *
 * Purpose:
 * - Central media access policy.
 * - Protects private/sensitive media.
 * - Keeps access decisions out of download/view endpoints.
 */

final class WIMediaAccessPolicy
{
    private WIdb $WIdb;

    public function __construct(?WIdb $WIdb = null)
    {
        $this->WIdb = $WIdb ?? WIdb::getInstance();
    }

    /**
     * Checks if the current user can view/download a media row.
     *
     * @param array<string, mixed> $media Media row.
     * @param array<string, mixed> $context Request context.
     *
     * @return array<string, mixed>
     */
    public function canAccess(array $media, array $context = []): array
    {
        if ($media === []) {
            return $this->deny('Media item was not found.');
        }

        if (!empty($media['deleted_at'])) {
            return $this->deny('Media item has been deleted.');
        }

        $visibility = strtolower((string)($media['visibility'] ?? 'private'));
        $isPrivate = (int)($media['is_private'] ?? 1) === 1;
        $isSensitive = (int)($media['is_sensitive'] ?? 0) === 1;

        if ($visibility === 'public' && !$isPrivate && !$isSensitive) {
            return $this->allow('Public media access allowed.');
        }

        $userId = $this->currentUserId($context);

        if ($userId <= 0) {
            return $this->deny('You must be signed in to access this media.');
        }

        /*
         * Admin/session-level access is accepted here for now.
         * Later this can be tightened using WIOrg/WIRole/WIPermissions.
         */
        if ($this->isAdminSession($context)) {
            return $this->allow('Admin media access allowed.');
        }

        /*
         * Business scoped access.
         * This is intentionally conservative.
         */
        $mediaBusinessId = (int)($media['org_business_id'] ?? 0);
        $requestBusinessId = (int)($context['org_business_id'] ?? $context['business_id'] ?? 0);

        if ($mediaBusinessId > 0 && $requestBusinessId > 0 && $mediaBusinessId === $requestBusinessId) {
            return $this->allow('Business scoped media access allowed.');
        }

        /*
         * Uploaded-by owner access.
         */
        $uploadedBy = (int)($media['uploaded_by_user_id'] ?? $media['uploaded_by'] ?? 0);

        if ($uploadedBy > 0 && $uploadedBy === $userId) {
            return $this->allow('Uploader media access allowed.');
        }

        return $this->deny('You do not have permission to access this media.');
    }

    /**
     * Resolves current user ID.
     *
     * @param array<string, mixed> $context Context.
     *
     * @return int
     */
    private function currentUserId(array $context): int
    {
        $userId = (int)($context['user_id'] ?? 0);

        if ($userId > 0) {
            return $userId;
        }

        if (class_exists('WISession')) {
            $sessionUserId = (int)WISession::get('user_id', 0);

            if ($sessionUserId > 0) {
                return $sessionUserId;
            }

            $adminId = (int)WISession::get('admin_id', 0);

            if ($adminId > 0) {
                return $adminId;
            }
        }

        if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
            return (int)$_SESSION['user_id'];
        }

        if (isset($_SESSION['admin_id']) && is_numeric($_SESSION['admin_id'])) {
            return (int)$_SESSION['admin_id'];
        }

        return 0;
    }

    /**
     * Checks if this is an admin-level session.
     *
     * @param array<string, mixed> $context Context.
     *
     * @return bool
     */
    private function isAdminSession(array $context): bool
    {
        if (!empty($context['is_admin'])) {
            return true;
        }

        if (class_exists('WISession')) {
            if ((int)WISession::get('admin_id', 0) > 0) {
                return true;
            }

            if ((string)WISession::get('user_role', '') === 'admin') {
                return true;
            }
        }

        if (isset($_SESSION['admin_id']) && (int)$_SESSION['admin_id'] > 0) {
            return true;
        }

        if (isset($_SESSION['user_role']) && (string)$_SESSION['user_role'] === 'admin') {
            return true;
        }

        return false;
    }

    /**
     * Allows access.
     *
     * @param string $message Message.
     *
     * @return array<string, mixed>
     */
    private function allow(string $message): array
    {
        return [
            'success' => true,
            'allowed' => true,
            'message' => $message,
        ];
    }

    /**
     * Denies access.
     *
     * @param string $message Message.
     *
     * @return array<string, mixed>
     */
    private function deny(string $message): array
    {
        return [
            'success' => false,
            'allowed' => false,
            'message' => $message,
        ];
    }
}