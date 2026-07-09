<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WIKitchenCompli / WICOS
| File: WIMediaAccessService.php
| Location: /root/WIAdmin/WICore/WIClass/Media/WIMediaAccessService.php
| Type: PHP Service Class
| Layer: Shared Media Security
| Purpose Area: Media Access Checks
| Version: 1.2.0
| Created: 2026-05-05
| Last Updated: 2026-05-05
| Status: Production Refactor Batch 7
|--------------------------------------------------------------------------
| Summary:
| Conservative shared WIMedia access-check service.
| - Validates link targets structurally
| - Checks business/site context before view/download access
| - Does not bypass WIPermissions or higher-level permission services
| - Contains no database logic and no compliance dependency
|--------------------------------------------------------------------------
*/

class WIMediaAccessService
{
    /**
     * Validates whether a target is structurally safe for media linking.
     *
     * @param array<string, mixed> $target Target context.
     *
     * @return bool
     */
    public function canLinkToTarget(array $target): bool
    {
        $system = trim((string)($target['system_code'] ?? ''));
        $entityType = trim((string)($target['entity_type'] ?? ''));
        $linkType = trim((string)($target['link_type'] ?? ''));
        $entityId = (int)($target['entity_id'] ?? 0);

        if ($system === '' || $entityType === '' || $linkType === '' || $entityId <= 0) {
            return false;
        }

        return (bool)preg_match('/^[a-z0-9_\-]+$/i', $system)
            && (bool)preg_match('/^[a-z0-9_\-]+$/i', $entityType)
            && (bool)preg_match('/^[a-z0-9_\-]+$/i', $linkType);
    }

    /**
     * Checks whether the current context can view a media record.
     *
     * @param array<string, mixed> $media Media row.
     * @param array<string, mixed> $context Current context.
     *
     * @return bool
     */
    public function canViewMedia(array $media, array $context = []): bool
    {
        $mediaBusiness = (int)($media['org_business_id'] ?? 0);
        $contextBusiness = (int)($context['org_business_id'] ?? 0);

        if ($mediaBusiness > 0 && $contextBusiness > 0 && $mediaBusiness !== $contextBusiness) {
            return false;
        }

        $mediaSite = (int)($media['org_site_id'] ?? 0);
        $contextSite = (int)($context['org_site_id'] ?? 0);

        if ($mediaSite > 0 && $contextSite > 0 && $mediaSite !== $contextSite) {
            return false;
        }

        return true;
    }

    /**
     * Checks whether media can be downloaded.
     *
     * @param array<string, mixed> $media Media row.
     * @param array<string, mixed> $context Current context.
     *
     * @return bool
     */
    public function canDownloadMedia(array $media, array $context = []): bool
    {
        return $this->canViewMedia($media, $context);
    }
}
