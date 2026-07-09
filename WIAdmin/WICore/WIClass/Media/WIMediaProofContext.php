<?php
declare(strict_types=1);

/**
 * File Information
 * ----------------
 * Written By: WI Labs
 * Company: WI Labs
 * Product: WICMS / WICOS
 * Project: WIMedia Shared Proof System
 * File: WIMediaProofContext.php
 * Location: root/WIAdmin/WICore/WIClass/Media/WIMediaProofContext.php
 * Type: Class
 * Layer: Shared Admin Media Layer
 * Purpose Area: Media / Evidence / Proof Context
 * Version: 1.0.0
 * Created: 2026-05-14
 * Last Updated: 2026-05-14
 * Status: Production-ready first pass
 *
 * Summary:
 * Normalises proof/media context for any feature that needs to attach media.
 * This keeps Documents, Evidence, Equipment, Audits, Incidents, Training,
 * Legal Register and Checklist proof using the same WIMedia contract.
 */

final class WIMediaProofContext
{
    /**
     * Allowed entity types for shared proof/media attachment.
     *
     * @var array<int,string>
     */
    private const ALLOWED_ENTITY_TYPES = [
        'checklist',
        'checklist_instance',
        'checklist_answer',
        'checklist_submission',
        'corrective_action',
        'document',
        'legal_register',
        'audit',
        'audit_finding',
        'incident',
        'training',
        'training_certificate',
        'equipment',
        'equipment_fault',
        'inspection',
        'inspection_item',
        'site',
        'business',
        'user',
        'hr_employee',
        'generic',
    ];

    /**
     * Allowed purposes.
     *
     * @var array<int,string>
     */
    private const ALLOWED_PURPOSES = [
        'proof',
        'photo_proof',
        'required_photo_proof',
        'evidence',
        'document',
        'certificate',
        'signature',
        'repair_proof',
        'service_document',
        'incident_attachment',
        'audit_evidence',
        'inspection_evidence',
        'legal_evidence',
        'training_evidence',
        'equipment_photo',
        'site_photo',
        'general_attachment',
    ];

    /**
     * Normalise incoming context.
     *
     * @param array<string,mixed> $input Raw context.
     *
     * @return array<string,mixed>
     */
    public static function normalise(array $input): array
    {
        $entityType = self::cleanKey((string)($input['entity_type'] ?? $input['entityType'] ?? 'generic'));
        $purpose = self::cleanKey((string)($input['purpose'] ?? 'proof'));

        if (!in_array($entityType, self::ALLOWED_ENTITY_TYPES, true)) {
            $entityType = 'generic';
        }

        if (!in_array($purpose, self::ALLOWED_PURPOSES, true)) {
            $purpose = 'proof';
        }

        $acceptedTypes = $input['accepted_types'] ?? $input['acceptedTypes'] ?? ['image/*', 'application/pdf'];

        if (!is_array($acceptedTypes)) {
            $acceptedTypes = ['image/*', 'application/pdf'];
        }

        $acceptedTypes = array_values(array_filter(array_map(static function ($type): string {
            return trim((string)$type);
        }, $acceptedTypes)));

        if ($acceptedTypes === []) {
            $acceptedTypes = ['image/*', 'application/pdf'];
        }

        return [
            'entity_type' => $entityType,
            'entity_id' => self::toInt($input['entity_id'] ?? $input['entityId'] ?? 0),
            'purpose' => $purpose,
            'business_id' => self::toInt($input['business_id'] ?? $input['businessId'] ?? 0),
            'site_id' => self::toInt($input['site_id'] ?? $input['siteId'] ?? 0),
            'department_id' => self::toInt($input['department_id'] ?? $input['departmentId'] ?? 0),
            'user_id' => self::toInt($input['user_id'] ?? $input['userId'] ?? 0),
            'required' => self::toBool($input['required'] ?? false),
            'multiple' => self::toBool($input['multiple'] ?? true),
            'accepted_types' => $acceptedTypes,
            'label' => trim((string)($input['label'] ?? 'Add proof')),
            'source' => self::cleanKey((string)($input['source'] ?? 'wimedia_proof_modal')),
            'metadata' => self::normaliseMetadata($input['metadata'] ?? []),
        ];
    }

    /**
     * Validate normalised context.
     *
     * @param array<string,mixed> $context Context.
     *
     * @return array<string,mixed>
     */
    public static function validate(array $context): array
    {
        $context = self::normalise($context);

        if ((int)$context['entity_id'] < 1) {
            return [
                'success' => false,
                'status' => 'error',
                'message' => 'Missing or invalid media entity id.',
                'data' => [
                    'context' => $context,
                ],
            ];
        }

        return [
            'success' => true,
            'status' => 'success',
            'message' => 'Media proof context is valid.',
            'data' => [
                'context' => $context,
            ],
        ];
    }

    /**
     * Convert context to HTML-safe JSON.
     *
     * @param array<string,mixed> $context Context.
     *
     * @return string
     */
    public static function toJson(array $context): string
    {
        return (string)json_encode(
            self::normalise($context),
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP
        );
    }

    /**
     * Clean a context key.
     *
     * @param string $value Value.
     *
     * @return string
     */
    private static function cleanKey(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_\-]/', '_', $value) ?? '';

        return trim($value, '_-') ?: 'generic';
    }

    /**
     * Convert mixed value to integer.
     *
     * @param mixed $value Value.
     *
     * @return int
     */
    private static function toInt(mixed $value): int
    {
        if (is_numeric($value)) {
            return max(0, (int)$value);
        }

        return 0;
    }

    /**
     * Convert mixed value to bool.
     *
     * @param mixed $value Value.
     *
     * @return bool
     */
    private static function toBool(mixed $value): bool
    {
        if (is_bool($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int)$value === 1;
        }

        if (is_string($value)) {
            return in_array(strtolower(trim($value)), ['1', 'true', 'yes', 'on'], true);
        }

        return false;
    }

    /**
     * Normalise metadata payload.
     *
     * @param mixed $metadata Metadata.
     *
     * @return array<string,mixed>
     */
    private static function normaliseMetadata(mixed $metadata): array
    {
        if (!is_array($metadata)) {
            return [];
        }

        $clean = [];

        foreach ($metadata as $key => $value) {
            $safeKey = self::cleanKey((string)$key);

            if (is_scalar($value) || $value === null) {
                $clean[$safeKey] = $value;
            }
        }

        return $clean;
    }
}