<?php
declare(strict_types=1);

/**
 * File Information
 * ----------------
 * Written By: WI Labs
 * Company: WI Labs
 * Product: WICMS / WICOS
 * Project: WIMedia Shared Proof System
 * File: WIAjaxMediaProof.php
 * Location: root/WIAdmin/WICore/WIClass/WIAjaxMediaProof.php
 * Type: Class
 * Layer: AJAX Adapter
 * Purpose Area: Media Proof AJAX Routing
 * Version: 1.0.0
 * Created: 2026-05-14
 * Last Updated: 2026-05-14
 * Status: Production-ready first pass
 *
 * Summary:
 * Thin AJAX adapter for WIMedia proof modal actions. It delegates to
 * WIMediaProofBridge and does not contain media business logic.
 */

require_once __DIR__ . '/WIMediaProofBridge.php';

final class WIAjaxMediaProof
{
    /**
     * Proof bridge.
     *
     * @var WIMediaProofBridge
     */
    private WIMediaProofBridge $bridge;

    /**
     * Constructor.
     *
     * @param WIMediaProofBridge $bridge Proof bridge.
     */
    public function __construct(WIMediaProofBridge $bridge)
    {
        $this->bridge = $bridge;
    }

    /**
     * Dispatch media proof action.
     *
     * @param string              $action Action.
     * @param array<string,mixed> $request Request payload.
     *
     * @return array<string,mixed>
     */
    public function dispatch(string $action, array $request): array
    {
        $action = strtolower(trim($action));

        return match ($action) {
            'media_proof_modal',
            'wimedia_proof_modal' => $this->modal($request),

            'media_proof_list',
            'wimedia_proof_list' => $this->list($request),

            'media_proof_attach',
            'wimedia_proof_attach' => $this->attach($request),

            'media_proof_detach',
            'wimedia_proof_detach' => $this->detach($request),

            default => [
                'success' => false,
                'status' => 'error',
                'message' => 'Unknown WIMedia proof action.',
                'data' => [
                    'action' => $action,
                ],
            ],
        };
    }

    /**
     * Load proof modal payload.
     *
     * @param array<string,mixed> $request Request.
     *
     * @return array<string,mixed>
     */
    private function modal(array $request): array
    {
        return $this->bridge->getProofModalPayload($this->contextFromRequest($request));
    }

    /**
     * List attached proof.
     *
     * @param array<string,mixed> $request Request.
     *
     * @return array<string,mixed>
     */
    private function list(array $request): array
    {
        return $this->bridge->listAttached($this->contextFromRequest($request));
    }

    /**
     * Attach existing media.
     *
     * @param array<string,mixed> $request Request.
     *
     * @return array<string,mixed>
     */
    private function attach(array $request): array
    {
        $mediaId = (int)($request['media_id'] ?? $request['mediaId'] ?? 0);

        return $this->bridge->attachExisting($mediaId, $this->contextFromRequest($request));
    }

    /**
     * Detach media.
     *
     * @param array<string,mixed> $request Request.
     *
     * @return array<string,mixed>
     */
    private function detach(array $request): array
    {
        $linkId = (int)($request['link_id'] ?? $request['linkId'] ?? 0);

        return $this->bridge->detach($linkId);
    }

    /**
     * Resolve context from AJAX request.
     *
     * @param array<string,mixed> $request Request.
     *
     * @return array<string,mixed>
     */
    private function contextFromRequest(array $request): array
    {
        $context = $request['context'] ?? [];

        if (is_string($context)) {
            $decoded = json_decode($context, true);
            $context = is_array($decoded) ? $decoded : [];
        }

        if (!is_array($context)) {
            $context = [];
        }

        foreach ($request as $key => $value) {
            if ($key === 'context') {
                continue;
            }

            if (!array_key_exists((string)$key, $context)) {
                $context[(string)$key] = $value;
            }
        }

        return $context;
    }
}