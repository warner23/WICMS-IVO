<?php
declare(strict_types=1);

/**
 * File Information
 * ----------------
 * Written By: WI Labs
 * Company: WI Labs
 * Product: WICMS / WICOS
 * Project: WIMedia Shared Proof System
 * File: WIMediaProofBridge.php
 * Location: root/WIAdmin/WICore/WIClass/WIMediaProofBridge.php
 * Type: Class
 * Layer: Shared Admin Media Bridge
 * Purpose Area: Media / Evidence / Proof
 * Version: 1.1.0
 * Created: 2026-05-14
 * Last Updated: 2026-05-14
 * Status: Production-ready first pass
 *
 * Summary:
 * Shared proof/evidence bridge for compliance/admin tabs. It does not contain
 * custom upload logic. It delegates linking/listing/detaching to WIMedia.
 */

require_once __DIR__ . '/WIMedia.php';
require_once __DIR__ . '/Media/WIMediaContext.php';
require_once __DIR__ . '/Media/WIMediaProofContext.php';

final class WIMediaProofBridge
{
    private WIdb $WIdb;

    private WIMedia $media;

    private int $currentUserId;

    /**
     * Constructor.
     *
     * @param WIdb         $WIdb          WIdb instance.
     * @param WIMedia|null $media         Existing WIMedia service.
     * @param int          $currentUserId Current user id.
     */
    public function __construct(WIdb $WIdb, ?WIMedia $media = null, int $currentUserId = 0)
    {
        $this->WIdb = $WIdb;
        $this->media = $media ?? new WIMedia($this->WIdb);
        $this->currentUserId = max(0, $currentUserId);
    }

    /**
     * Get modal bootstrap data for a proof context.
     *
     * @param array<string,mixed> $context Raw context.
     *
     * @return array<string,mixed>
     */
    public function getProofModalPayload(array $context): array
    {
        $validation = WIMediaProofContext::validate($this->withUser($context));

        if (($validation['success'] ?? false) !== true) {
            return $validation;
        }

        $normalised = $validation['data']['context'];

        return [
            'success' => true,
            'status' => 'success',
            'message' => 'Proof modal payload loaded.',
            'data' => [
                'context' => $normalised,
                'media_context' => $this->toWIMediaContext($normalised),
                'capabilities' => [
                    'camera' => true,
                    'upload' => true,
                    'library' => true,
                    'preview' => true,
                    'detach' => true,
                    'replace' => true,
                ],
                'attached' => $this->listAttached($normalised)['data']['items'] ?? [],
            ],
        ];
    }

    /**
     * Attach existing media.
     *
     * @param int                 $mediaId Media id.
     * @param array<string,mixed> $context Raw context.
     *
     * @return array<string,mixed>
     */
    public function attachExisting(int $mediaId, array $context): array
    {
        $mediaId = max(0, $mediaId);
        $validation = WIMediaProofContext::validate($this->withUser($context));

        if ($mediaId < 1) {
            return $this->error('Missing or invalid media id.');
        }

        if (($validation['success'] ?? false) !== true) {
            return $validation;
        }

        return $this->normalise($this->media->attachMedia(
            $mediaId,
            $this->toWIMediaContext($validation['data']['context'])
        ));
    }

    /**
     * List attached media for an entity context.
     *
     * @param array<string,mixed> $context Raw context.
     *
     * @return array<string,mixed>
     */
    public function listAttached(array $context): array
    {
        $validation = WIMediaProofContext::validate($this->withUser($context));

        if (($validation['success'] ?? false) !== true) {
            return $validation;
        }

        $items = $this->media->linkedFor($this->toWIMediaContext($validation['data']['context']));

        return [
            'success' => true,
            'status' => 'success',
            'message' => 'Attached media loaded.',
            'data' => [
                'items' => is_array($items) ? $items : [],
                'media' => is_array($items) ? $items : [],
                'context' => $validation['data']['context'],
            ],
        ];
    }

    /**
     * Detach media.
     *
     * This method accepts link_id as a fallback only. Prefer media_id + context
     * from the frontend where possible because existing WIMedia unlink contract
     * works with media id and canonical context.
     *
     * @param int                 $linkIdOrMediaId Link id or media id.
     * @param array<string,mixed> $context         Context.
     *
     * @return array<string,mixed>
     */
    public function detach(int $linkIdOrMediaId, array $context = []): array
    {
        $id = max(0, $linkIdOrMediaId);

        if ($id < 1) {
            return $this->error('Missing or invalid media id.');
        }

        $validation = WIMediaProofContext::validate($this->withUser($context));

        if (($validation['success'] ?? false) !== true) {
            return $validation;
        }

        return $this->normalise($this->media->detachMedia(
            $id,
            $this->toWIMediaContext($validation['data']['context'])
        ));
    }

    /**
     * Add current user id to context.
     *
     * @param array<string,mixed> $context Context.
     *
     * @return array<string,mixed>
     */
    private function withUser(array $context): array
    {
        $userId = (int)($context['user_id'] ?? $context['userId'] ?? 0);

        if ($userId < 1 && $this->currentUserId > 0) {
            $context['user_id'] = $this->currentUserId;
        }

        return $context;
    }

    /**
     * Convert proof context into canonical WIMedia context.
     *
     * @param array<string,mixed> $context Proof context.
     *
     * @return array<string,mixed>
     */
    private function toWIMediaContext(array $context): array
    {
        $proof = WIMediaProofContext::normalise($context);

        return WIMediaContext::fromPayload([
            'system_code' => $context['system_code'] ?? 'wicos',
            'entity_type' => $proof['entity_type'],
            'entity_id' => $proof['entity_id'],
            'link_type' => $context['link_type'] ?? $proof['purpose'] ?? 'evidence',
            'media_role' => $context['media_role'] ?? $proof['purpose'] ?? 'evidence',

            'business_id' => $proof['business_id'],
            'site_id' => $proof['site_id'],
            'department_id' => $proof['department_id'],
            'org_business_id' => $context['org_business_id'] ?? $proof['business_id'],
            'org_site_id' => $context['org_site_id'] ?? $proof['site_id'],
            'org_department_id' => $context['org_department_id'] ?? $proof['department_id'],

            'user_id' => $proof['user_id'] > 0 ? $proof['user_id'] : $this->currentUserId,
            'created_by_user_id' => $context['created_by_user_id'] ?? $this->currentUserId,
            'uploaded_by_user_id' => $context['uploaded_by_user_id'] ?? $this->currentUserId,

            'visibility' => $context['visibility'] ?? 'private',
            'access_scope' => $context['access_scope'] ?? 'business',
            'is_private' => $context['is_private'] ?? 1,
            'is_sensitive' => $context['is_sensitive'] ?? 1,
        ], $this->currentUserId);
    }

    /**
     * Normalise WIMedia response.
     *
     * @param array<string,mixed> $response Response.
     *
     * @return array<string,mixed>
     */
    private function normalise(array $response): array
    {
        if (!array_key_exists('success', $response)) {
            $response['success'] = (($response['status'] ?? 'success') === 'success');
        }

        if (!array_key_exists('status', $response)) {
            $response['status'] = ($response['success'] ?? false) ? 'success' : 'error';
        }

        if (!array_key_exists('message', $response)) {
            $response['message'] = '';
        }

       if (!array_key_exists('data', $response) || !is_array($response['data'] ?? null)) {
            $response['data'] = [];
        }

        return [
            'success' => (bool)$response['success'],
            'status' => (string)$response['status'],
            'message' => (string)$response['message'],
            'data' => $response['data'],
            'errors' => isset($response['errors']) && is_array($response['errors']) ? $response['errors'] : [],
            'meta' => isset($response['meta']) && is_array($response['meta']) ? $response['meta'] : [],
        ] + array_diff_key($response, array_flip([
            'success',
            'status',
            'message',
            'data',
            'errors',
            'meta',
        ]));
    }

    /**
     * Error response helper.
     *
     * @param string $message Message.
     *
     * @return array<string,mixed>
     */
    private function error(string $message): array
    {
        return [
            'success' => false,
            'status' => 'error',
            'message' => $message,
            'data' => [],
            'errors' => [$message],
            'meta' => [],
        ];
    }
}