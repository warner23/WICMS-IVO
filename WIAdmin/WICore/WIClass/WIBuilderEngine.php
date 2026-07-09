<?php
declare(strict_types=1);

/*
|--------------------------------------------------------------------------
| File Information
|--------------------------------------------------------------------------
| Written By: Jules Warner
| Company: WILabs
| Product: WI Builder
| Project: WI Ecosystem
| Class: WIBuilderEngine
| File: WIBuilderEngine.php
| Location: /WIAdmin/WICore/WIClass/WIBuilderEngine.php
| Type: Admin Shared Builder Engine
| Layer: Admin
| Purpose Area: Shared Builder / Layout / Schema Logic
| Version: 1.0.0
| Created: 2026-04-25
| Last Updated: 2026-04-25
| Status: Active
|--------------------------------------------------------------------------
| Summary
|--------------------------------------------------------------------------
| Shared admin-side builder engine for layout/schema preparation.
|
| Responsibilities:
| - Normalise generic builder layouts
| - Build safe section payloads
| - Build safe block payloads
| - Keep builder logic reusable outside compliance
|
| Non-Responsibilities:
| - No HTML rendering
| - No direct AJAX output
| - No direct DB writes
|--------------------------------------------------------------------------
*/

if (!class_exists('WIBuilderEngine')) {
    class WIBuilderEngine
    {
        /**
         * Normalises a generic builder layout payload.
         *
         * @param array<string, mixed> $layout Raw layout payload.
         *
         * @return array<string, mixed>
         */
        public function normaliseLayout(array $layout): array
        {
            $sections  = [];
            $sortOrder = 10;

            foreach ((array) ($layout['sections'] ?? []) as $section) {
                if (!is_array($section)) {
                    continue;
                }

                $normalisedBlocks = [];
                $blockSortOrder   = 10;

                foreach ((array) ($section['blocks'] ?? []) as $block) {
                    if (!is_array($block)) {
                        continue;
                    }

                    $normalisedBlocks[] = $this->normaliseBlock($block, $blockSortOrder);
                    $blockSortOrder += 10;
                }

                $sections[] = [
                    'section_id'          => $this->resolveStringValue((string) ($section['section_id'] ?? ''), 'section_' . $sortOrder),
                    'section_code'        => $this->resolveStringValue((string) ($section['section_code'] ?? ''), 'section_' . $sortOrder),
                    'section_title'       => $this->resolveStringValue((string) ($section['section_title'] ?? ''), 'Section'),
                    'section_description' => trim((string) ($section['section_description'] ?? '')),
                    'sort_order'          => (int) ($section['sort_order'] ?? $sortOrder),
                    'blocks'              => $normalisedBlocks,
                ];

                $sortOrder += 10;
            }

            usort(
                $sections,
                static fn(array $left, array $right): int => (int) ($left['sort_order'] ?? 0) <=> (int) ($right['sort_order'] ?? 0)
            );

            return [
                'layout_version' => $this->resolveStringValue((string) ($layout['layout_version'] ?? ''), '1.0.0'),
                'builder_type'   => $this->resolveStringValue((string) ($layout['builder_type'] ?? ''), 'generic'),
                'meta'           => is_array($layout['meta'] ?? null) ? $layout['meta'] : [],
                'sections'       => $sections,
            ];
        }

        /**
         * Builds a flat block map keyed by block ID.
         *
         * @param array<string, mixed> $layout Normalised layout payload.
         *
         * @return array<string, array<string, mixed>>
         */
        public function buildBlockMap(array $layout): array
        {
            $map = [];

            foreach ((array) ($layout['sections'] ?? []) as $section) {
                if (!is_array($section)) {
                    continue;
                }

                foreach ((array) ($section['blocks'] ?? []) as $block) {
                    if (!is_array($block)) {
                        continue;
                    }

                    $blockId = trim((string) ($block['block_id'] ?? ''));
                    if ($blockId === '') {
                        continue;
                    }

                    $map[$blockId] = $block;
                }
            }

            return $map;
        }

        /**
         * Builds a simple builder summary.
         *
         * @param array<string, mixed> $layout Normalised layout payload.
         *
         * @return array<string, mixed>
         */
        public function buildLayoutSummary(array $layout): array
        {
            $sectionCount = 0;
            $blockCount   = 0;

            foreach ((array) ($layout['sections'] ?? []) as $section) {
                if (!is_array($section)) {
                    continue;
                }

                $sectionCount++;
                $blockCount += count((array) ($section['blocks'] ?? []));
            }

            return [
                'layout_version' => (string) ($layout['layout_version'] ?? '1.0.0'),
                'builder_type'   => (string) ($layout['builder_type'] ?? 'generic'),
                'section_count'  => $sectionCount,
                'block_count'    => $blockCount,
            ];
        }

        /**
         * Builds a checklist-style section payload from question IDs.
         *
         * @param string $sectionCode Section code.
         * @param string $sectionTitle Section title.
         * @param array<int, int> $questionIds Question IDs.
         * @param int $sortOrder Sort order.
         *
         * @return array<string, mixed>
         */
        public function buildChecklistSection(
            string $sectionCode,
            string $sectionTitle,
            array $questionIds,
            int $sortOrder = 10
        ): array {
            $normalisedBlocks = [];
            $blockSortOrder   = 10;

            foreach ($questionIds as $questionId) {
                $questionId = (int) $questionId;

                if ($questionId <= 0) {
                    continue;
                }

                $normalisedBlocks[] = [
                    'block_id'    => 'question_' . $questionId,
                    'block_type'  => 'question',
                    'source_type' => 'question',
                    'source_id'   => $questionId,
                    'sort_order'  => $blockSortOrder,
                    'settings'    => [],
                ];

                $blockSortOrder += 10;
            }

            return [
                'section_id'          => trim($sectionCode) !== '' ? trim($sectionCode) : 'section_' . $sortOrder,
                'section_code'        => trim($sectionCode) !== '' ? trim($sectionCode) : 'section_' . $sortOrder,
                'section_title'       => trim($sectionTitle) !== '' ? trim($sectionTitle) : 'Section',
                'section_description' => '',
                'sort_order'          => $sortOrder,
                'blocks'              => $normalisedBlocks,
            ];
        }

        /**
         * Normalises a block payload.
         *
         * @param array<string, mixed> $block Raw block payload.
         * @param int $sortOrder Default sort order.
         *
         * @return array<string, mixed>
         */
        protected function normaliseBlock(array $block, int $sortOrder = 10): array
        {
            return [
                'block_id'    => $this->resolveStringValue((string) ($block['block_id'] ?? ''), 'block_' . $sortOrder),
                'block_type'  => $this->resolveStringValue((string) ($block['block_type'] ?? ''), 'generic'),
                'source_type' => trim((string) ($block['source_type'] ?? '')),
                'source_id'   => (int) ($block['source_id'] ?? 0),
                'sort_order'  => (int) ($block['sort_order'] ?? $sortOrder),
                'settings'    => is_array($block['settings'] ?? null) ? $block['settings'] : [],
            ];
        }

        /**
         * Resolves a string value with fallback.
         *
         * @param string $value Raw value.
         * @param string $fallback Fallback value.
         *
         * @return string
         */
        protected function resolveStringValue(string $value, string $fallback): string
        {
            $value = trim($value);

            return $value !== '' ? $value : $fallback;
        }
    }
}