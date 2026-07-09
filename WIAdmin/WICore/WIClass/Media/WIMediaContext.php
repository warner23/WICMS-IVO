<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Ecosystem
| Project: WICMS / WICOS / WIKitchenCompli
| File: WIMediaContext.php
| Location: /root/WIAdmin/WICore/WIClass/Media/WIMediaContext.php
| Type: PHP Helper Class
| Layer: Shared Media Foundation
| Purpose Area: Site-Wide Media Context Normalisation
| Version: 1.0.0
| Created: 2026-05-08
| Last Updated: 2026-05-08
| Status: WM-01 Production Batch
|--------------------------------------------------------------------------
| Summary:
| Normalises context for site-wide WIMedia use. Documents, evidence,
| checklist proof, incidents, training, equipment and member/profile media
| should all pass through this helper before upload/link actions.
|--------------------------------------------------------------------------
*/

final class WIMediaContext
{
    public const DEFAULT_SYSTEM = 'wicms';
    public const DEFAULT_LINK_TYPE = 'primary_file';
    public const DEFAULT_VISIBILITY = 'private';
    public const DEFAULT_ACCESS_SCOPE = 'business';

    /**
     * Builds a canonical media context from request/service payload data.
     *
     * @param array<string, mixed> $payload Raw payload.
     * @param int|null $currentUserId Current authenticated user ID.
     *
     * @return array<string, mixed>
     */
    public static function fromPayload(array $payload, ?int $currentUserId = null): array
    {
        $context = [
            'system_code' => self::string($payload['system_code'] ?? self::DEFAULT_SYSTEM, self::DEFAULT_SYSTEM),
            'entity_type' => self::string($payload['entity_type'] ?? $payload['module'] ?? $payload['owner_type'] ?? null),
            'entity_id' => self::positiveInt($payload['entity_id'] ?? $payload['record_id'] ?? $payload['owner_id'] ?? null),
            'link_type' => self::string($payload['link_type'] ?? $payload['media_role'] ?? self::DEFAULT_LINK_TYPE, self::DEFAULT_LINK_TYPE),

            'org_business_id' => self::positiveInt($payload['org_business_id'] ?? $payload['business_id'] ?? null),
            'org_site_id' => self::positiveInt($payload['org_site_id'] ?? $payload['site_id'] ?? null),
            'org_department_id' => self::positiveInt($payload['org_department_id'] ?? $payload['department_id'] ?? null),

            'folder' => self::string($payload['folder'] ?? null),
            'title' => self::string($payload['title'] ?? null),
            'alt_text' => self::string($payload['alt_text'] ?? null),
            'caption' => self::string($payload['caption'] ?? null),
            'description' => self::string($payload['description'] ?? null),

            'visibility' => self::string($payload['visibility'] ?? self::DEFAULT_VISIBILITY, self::DEFAULT_VISIBILITY),
            'access_scope' => self::string($payload['access_scope'] ?? self::DEFAULT_ACCESS_SCOPE, self::DEFAULT_ACCESS_SCOPE),
            'is_private' => isset($payload['is_private']) ? (int)$payload['is_private'] : 1,
            'is_sensitive' => isset($payload['is_sensitive']) ? (int)$payload['is_sensitive'] : 0,

            'created_by_user_id' => self::positiveInt($payload['created_by_user_id'] ?? $payload['user_id'] ?? $currentUserId),
            'uploaded_by_user_id' => self::positiveInt($payload['uploaded_by_user_id'] ?? $payload['user_id'] ?? $currentUserId),
            'user_id' => self::positiveInt($payload['user_id'] ?? $currentUserId),
        ];

        return array_filter($context, static fn ($value) => $value !== null && $value !== '');
    }

    /**
     * Returns true when a context has the minimum fields required for linking.
     *
     * @param array<string, mixed> $context Canonical context.
     *
     * @return bool
     */
    public static function canLink(array $context): bool
    {
        return !empty($context['system_code'])
            && !empty($context['entity_type'])
            && (int)($context['entity_id'] ?? 0) > 0
            && !empty($context['link_type']);
    }

    /**
     * Creates a named evidence context.
     *
     * @param string $entityType Entity type.
     * @param int $entityId Entity ID.
     * @param array<string, mixed> $extra Extra context.
     *
     * @return array<string, mixed>
     */
    public static function evidence(string $entityType, int $entityId, array $extra = []): array
    {
        return self::fromPayload(array_merge($extra, [
            'system_code' => 'wicos',
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'link_type' => $extra['link_type'] ?? 'evidence',
            'is_sensitive' => $extra['is_sensitive'] ?? 1,
        ]));
    }

    /**
     * Converts a value to a trimmed string or default.
     *
     * @param mixed $value Raw value.
     * @param string|null $default Default.
     *
     * @return string|null
     */
    private static function string(mixed $value, ?string $default = null): ?string
    {
        if ($value === null) {
            return $default;
        }

        $value = trim((string)$value);

        return $value !== '' ? $value : $default;
    }

    /**
     * Converts a value to a positive integer or null.
     *
     * @param mixed $value Raw value.
     *
     * @return int|null
     */
    private static function positiveInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (int)$value;

        return $value > 0 ? $value : null;
    }
}
