<?php
declare(strict_types=1);

/**
 * FILE INFORMATION
 * ----------------
 * Written By: Jules Warner
 * Company: WILabs
 * Product: WICMS / WICOS / WICompliance
 * Project: WI Ecosystem
 * File: WIFlipcard.php
 * Location: root/WIAdmin/WICore/WIClass/WIFlipcard.php
 * Type: Shared UI Renderer
 * Layer: UI Component
 * Purpose Area: Reusable Sites-style flipcard rendering
 * Version: 3.0.0
 * Created: Legacy
 * Last Updated: 2026-05-17
 * Status: Production-ready shared baseline
 *
 * SUMMARY
 * -------
 * One shared reusable flipcard renderer for the WI Ecosystem.
 *
 * This class renders structured card payloads only. It does not fetch data,
 * save data, open modals, upload media, query the database, or contain
 * product/plugin business logic.
 *
 * Sites, Equipment, Documents, Staff, Users, Checklists, and future plugins
 * should prepare payload arrays and call this one class.
 *
 * ARCHITECTURE RULES
 * ------------------
 * - One class for the flipcard feature.
 * - Product/plugin agnostic.
 * - UI rendering only.
 * - No WIdb.
 * - No DB access.
 * - No business logic.
 * - No modal logic.
 * - No upload/media logic.
 * - Safe escaping by default.
 * - Optional trusted HTML supported only when caller explicitly requests it.
 */
final class WIFlipcard
{
    /**
     * Tracks whether assets have already been rendered this request.
     *
     * @var bool
     */
    private static bool $assetsRendered = false;

    /**
     * Echo one flipcard.
     *
     * @param array<string,mixed> $card Card payload.
     *
     * @return void
     */
    public function render(array $card): void
    {
        echo $this->build($card);
    }

    /**
     * Build one flipcard.
     *
     * @param array<string,mixed> $card Card payload.
     *
     * @return string
     */
    public function build(array $card): string
    {
        $id = $this->safeId($card['id'] ?? uniqid('wi-flipcard-', false));

        $classes = $this->classes([
            'wi-flipcard',
            'wi-flipcard--sites-style',
            (string)($card['class'] ?? ''),
            !empty($card['state']) ? 'wi-flipcard--' . $this->slug((string)$card['state']) : '',
        ]);

        $attributes = $this->attributes((array)($card['attributes'] ?? []));

        return $this->assets()
            . '<article class="' . $classes . '" id="' . $this->e($id) . '" data-wi-flipcard ' . $attributes . '>'
            . '<div class="wi-flipcard__inner">'
            . $this->face('front', (array)($card['front'] ?? []), $id)
            . $this->face('back', (array)($card['back'] ?? []), $id)
            . '</div>'
            . '</article>';
    }

    /**
     * Echo a flipcard grid.
     *
     * @param array<int,array<string,mixed>> $cards Cards.
     * @param array<string,mixed>            $opts  Options.
     *
     * @return void
     */
    public function renderList(array $cards, array $opts = []): void
    {
        echo $this->buildList($cards, $opts);
    }

    /**
     * Build a flipcard grid.
     *
     * @param array<int,array<string,mixed>> $cards Cards.
     * @param array<string,mixed>            $opts  Options.
     *
     * @return string
     */
    public function buildList(array $cards, array $opts = []): string
    {
        $class = $this->classes([
            'wi-flipcard-grid',
            (string)($opts['class'] ?? ''),
        ]);

        $attributes = $this->attributes((array)($opts['attributes'] ?? []));

        if ($cards === []) {
            return $this->assets()
                . '<div class="' . $class . '" ' . $attributes . '>'
                . '<div class="wi-flipcard-empty">'
                . $this->e($opts['empty_message'] ?? 'No cards to display.')
                . '</div>'
                . '</div>';
        }

        $html = $this->assets() . '<div class="' . $class . '" ' . $attributes . '>';

        foreach ($cards as $card) {
            if (!is_array($card)) {
                continue;
            }

            $html .= $this->build($card);
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Backwards-compatible legacy site call.
     *
     * New Sites code should prepare payloads and call build()/render() directly.
     *
     * @param array<string,mixed> $site Site payload.
     *
     * @return void
     */
    public function FlipcardSites(array $site): void
    {
        $siteId = (int)($site['org_site_id'] ?? $site['id'] ?? 0);

        if ($siteId <= 0) {
            return;
        }

        $title = (string)($site['site_name'] ?? 'Unnamed Site');
        $trafficState = $this->normaliseState((string)($site['traffic_state'] ?? 'green'));
        $status = (string)($site['status'] ?? 'active');

        $subtitle = trim(implode(' · ', array_filter([
            $site['site_code'] ?? '',
            $site['city_name'] ?? '',
            $site['region_name'] ?? '',
        ])));

        $this->render([
            'id' => 'wi-site-card-' . $siteId,
            'state' => $trafficState,
            'class' => 'wi-site-card-wrap wi-site-flipcard',
            'attributes' => [
                'data-site-id' => $siteId,
                'data-org-site-id' => $siteId,
            ],
            'front' => [
                'eyebrow' => 'Site',
                'title' => $title,
                'subtitle' => $subtitle !== '' ? $subtitle : 'Site profile',
                'status' => [
                    'label' => ucfirst($status),
                    'state' => $trafficState,
                ],
                'top_actions_left' => [
                    [
                        'label' => 'Edit',
                        'variant' => 'primary',
                        'icon' => 'fa fa-pencil',
                        'attributes' => [
                            'data-wi-flip-toggle' => true,
                            'data-site-action' => 'flip',
                            'data-site-id' => $siteId,
                        ],
                    ],
                ],
                'top_actions_right' => [
                    [
                        'label' => 'Delete',
                        'variant' => 'danger',
                        'icon' => 'fa fa-trash',
                        'attributes' => [
                            'data-site-action' => 'archive',
                            'data-site-id' => $siteId,
                        ],
                    ],
                ],
                'metrics' => [
                    ['label' => 'Type', 'value' => $site['site_type'] ?? 'Site'],
                    ['label' => 'Timezone', 'value' => $site['timezone'] ?? 'Europe/London'],
                    ['label' => 'Status', 'value' => ucfirst($status)],
                ],
                'sections' => [
                    [
                        'class' => 'middle-bit-site',
                        'content' => [
                            'trusted_html' => false,
                            'text' => (string)($site['notes'] ?? 'Site compliance profile.'),
                        ],
                    ],
                ],
                'actions' => [
                    [
                        'label' => 'Inspector Mode',
                        'variant' => 'ghost',
                        'attributes' => [
                            'data-site-action' => 'inspector',
                            'data-site-id' => $siteId,
                        ],
                    ],
                ],
            ],
            'back' => [
                'eyebrow' => 'Site controls',
                'title' => $title,
                'subtitle' => 'Edit full site details using the Sites controller/modal.',
                'top_actions_left' => [
                    [
                        'label' => 'Back',
                        'variant' => 'ghost',
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'data-wi-flip-toggle' => true,
                        ],
                    ],
                ],
                'top_actions_right' => [
                    [
                        'label' => 'Delete',
                        'variant' => 'danger',
                        'icon' => 'fa fa-trash',
                        'attributes' => [
                            'data-site-action' => 'archive',
                            'data-site-id' => $siteId,
                        ],
                    ],
                ],
                'content' => [
                    'trusted_html' => true,
                    'html' => '<div class="wi-flipcard-note">This shared flipcard is display-only. Full site editing should be handled by the Sites tab using WIModal and backend services.</div>',
                ],
                'actions' => [
                    [
                        'label' => 'Open Edit Modal',
                        'variant' => 'primary',
                        'attributes' => [
                            'data-site-action' => 'edit',
                            'data-site-id' => $siteId,
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Legacy product wrapper retained only to avoid fatal errors.
     *
     * New product/POS code should prepare payloads and call build()/render().
     *
     * @param array<string,mixed> $product Product payload.
     *
     * @return void
     */
    public function Flipcard(array $product): void
    {
        $id = (int)($product['prod_id'] ?? $product['id'] ?? 0);
        $title = (string)($product['prod_name'] ?? $product['name'] ?? 'Product');

        $this->render([
            'id' => 'wi-product-card-' . $id,
            'class' => 'wi-product-card',
            'front' => [
                'eyebrow' => 'Product',
                'title' => $title,
                'subtitle' => (string)($product['prod_desc'] ?? $product['description'] ?? ''),
                'media' => [
                    'src' => !empty($product['prod_img'])
                        ? 'WIMedia/Img/pos/products/' . (string)$product['prod_img']
                        : '',
                    'alt' => (string)($product['alt'] ?? $title),
                ],
                'metrics' => [
                    [
                        'label' => 'Price',
                        'value' => (defined('CURRENCY_SYMBOL') ? CURRENCY_SYMBOL : '£') . (string)($product['prod_price'] ?? '0.00'),
                    ],
                ],
                'top_actions_left' => [
                    [
                        'label' => 'Edit',
                        'variant' => 'primary',
                        'icon' => 'fa fa-pencil',
                        'attributes' => [
                            'data-wi-flip-toggle' => true,
                            'data-product-id' => $id,
                        ],
                    ],
                ],
            ],
            'back' => [
                'eyebrow' => 'Product',
                'title' => $title,
                'subtitle' => 'Legacy product editing should be handled by the product controller.',
                'top_actions_left' => [
                    [
                        'label' => 'Back',
                        'variant' => 'ghost',
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'data-wi-flip-toggle' => true,
                        ],
                    ],
                ],
            ],
        ]);
    }

    /**
     * Legacy wrapper retained only to avoid fatal errors.
     *
     * @param array<string,mixed> $product Product payload.
     *
     * @return void
     */
    public function FlipcardExtras(array $product): void
    {
        $this->Flipcard($product);
    }

    /**
     * Build one flipcard face.
     *
     * @param string              $side Face side.
     * @param array<string,mixed> $face Face payload.
     * @param string              $id   Parent card ID.
     *
     * @return string
     */
    private function face(string $side, array $face, string $id): string
    {
        $side = $side === 'back' ? 'back' : 'front';

        $html = '<section class="wi-flipcard__face wi-flipcard__face--' . $side . '" data-wi-flip-face="' . $side . '">';

        $html .= $this->topActionRow($face);

        $html .= '<div class="top-bit wi-flipcard__header">';
        $html .= '<div class="wi-flipcard__header-main">';

        if (!empty($face['eyebrow'])) {
            $html .= '<span class="wi-flipcard__eyebrow">' . $this->e($face['eyebrow']) . '</span>';
        }

        if (!empty($face['title'])) {
            $html .= '<h3 class="wi-flipcard__title">' . $this->e($face['title']) . '</h3>';
        }

        if (!empty($face['subtitle'])) {
            $html .= '<p class="wi-flipcard__subtitle">' . $this->e($face['subtitle']) . '</p>';
        }

        if (!empty($face['badges']) && is_array($face['badges'])) {
            $html .= $this->badges((array)$face['badges']);
        }

        $html .= '</div>';
        $html .= $this->status((array)($face['status'] ?? []));
        $html .= '</div>';

        $html .= '<div class="middle-bit wi-flipcard__body">';

        $html .= $this->mediaBlock($face);

        if (!empty($face['metrics']) && is_array($face['metrics'])) {
            $html .= $this->metrics((array)$face['metrics']);
        }

        if (!empty($face['sections']) && is_array($face['sections'])) {
            $html .= $this->sections((array)$face['sections']);
        }

        if (!empty($face['content'])) {
            $html .= '<div class="wi-flipcard__content">'
                . $this->content($face['content'], (bool)($face['content_is_html'] ?? false))
                . '</div>';
        }

        $html .= '</div>';

        if (!empty($face['actions']) && is_array($face['actions'])) {
            $html .= '<div class="end-bit wi-flipcard__footer">';
            $html .= $this->actions((array)$face['actions'], $id);
            $html .= '</div>';
        }

        $html .= '</section>';

        return $html;
    }

    /**
     * Build top action row: Edit/Back left, Delete right.
     *
     * @param array<string,mixed> $face Face payload.
     *
     * @return string
     */
    private function topActionRow(array $face): string
    {
        $left = (array)($face['top_actions_left'] ?? []);
        $right = (array)($face['top_actions_right'] ?? []);

        /*
         * Backwards compatibility:
         * old top_actions defaults to right side.
         */
        if ($right === [] && !empty($face['top_actions']) && is_array($face['top_actions'])) {
            $right = (array)$face['top_actions'];
        }

        return '<div class="wi-flipcard__actions-top">'
            . '<div class="wi-flipcard__actions-top-left">' . $this->topActions($left) . '</div>'
            . '<div class="wi-flipcard__actions-top-right">' . $this->topActions($right) . '</div>'
            . '</div>';
    }

    /**
     * Build top action buttons.
     *
     * @param array<int,array<string,mixed>> $actions Actions.
     *
     * @return string
     */
    private function topActions(array $actions): string
    {
        $html = '';

        foreach ($actions as $action) {
            if (!is_array($action)) {
                continue;
            }

            $label = (string)($action['label'] ?? '');

            if ($label === '') {
                continue;
            }

            $variant = $this->slug((string)($action['variant'] ?? 'ghost'));
            $icon = (string)($action['icon'] ?? '');
            $attrs = (array)($action['attributes'] ?? []);
            $attrs['type'] = $attrs['type'] ?? 'button';

            $extraClass = trim((string)($attrs['class'] ?? ''));
            unset($attrs['class']);

            $class = $this->classes([
                'wi-flipcard-top-btn',
                'wi-flipcard-top-btn--' . $variant,
                $extraClass,
            ]);

            $html .= '<button class="' . $class . '" ' . $this->attributes($attrs) . '>';

            if ($icon !== '') {
                $html .= '<i class="' . $this->e($icon) . '" aria-hidden="true"></i>';
            }

            $html .= '<span>' . $this->e($label) . '</span>';
            $html .= '</button>';
        }

        return $html;
    }

    /**
     * Build media block if configured.
     *
     * Supports both `media` and legacy `image`.
     *
     * @param array<string,mixed> $face Face payload.
     *
     * @return string
     */
    private function mediaBlock(array $face): string
    {
        $media = (array)($face['media'] ?? $face['image'] ?? []);

        if ($media === []) {
            return '';
        }

        $src = trim((string)($media['src'] ?? ''));
        $alt = (string)($media['alt'] ?? $face['title'] ?? '');
        $title = trim((string)($face['title'] ?? ''));
        $initial = strtoupper(substr($title !== '' ? $title : '?', 0, 1));

        if ($src !== '') {
            return '<div class="wi-flipcard__media">'
                . '<img src="' . $this->e($src) . '" alt="' . $this->e($alt) . '">'
                . '</div>';
        }

        return '<div class="wi-flipcard__media wi-flipcard__media--placeholder" aria-hidden="true">'
            . $this->e($initial)
            . '</div>';
    }

    /**
     * Build status pill/light.
     *
     * @param array<string,mixed> $status Status payload.
     *
     * @return string
     */
    private function status(array $status): string
    {
        $label = trim((string)($status['label'] ?? ''));

        if ($label === '') {
            return '';
        }

        $state = $this->normaliseState((string)($status['state'] ?? 'grey'));

        return '<span class="wi-flipcard-status wi-flipcard-status--' . $this->e($state) . '">'
            . '<span class="wi-flipcard-status__light"></span>'
            . '<span>' . $this->e($label) . '</span>'
            . '</span>';
    }

    /**
     * Build metric grid.
     *
     * @param array<int,array<string,mixed>> $metrics Metrics.
     *
     * @return string
     */
    private function metrics(array $metrics): string
    {
        $html = '<dl class="wi-flipcard__metrics">';

        foreach ($metrics as $metric) {
            if (!is_array($metric)) {
                continue;
            }

            $label = (string)($metric['label'] ?? '');
            $value = (string)($metric['value'] ?? '');

            if ($label === '' && $value === '') {
                continue;
            }

            $state = $this->slug((string)($metric['state'] ?? 'default'));

            $html .= '<div class="wi-flipcard__metric wi-flipcard__metric--' . $this->e($state) . '">'
                . '<dt>' . $this->e($label) . '</dt>'
                . '<dd>' . $this->e($value) . '</dd>'
                . '</div>';
        }

        $html .= '</dl>';

        return $html;
    }

    /**
     * Build generic content sections.
     *
     * @param array<int,array<string,mixed>> $sections Sections.
     *
     * @return string
     */
    private function sections(array $sections): string
    {
        $html = '<div class="wi-flipcard__sections">';

        foreach ($sections as $section) {
            if (!is_array($section)) {
                continue;
            }

            $class = $this->classes([
                'wi-flipcard__section',
                (string)($section['class'] ?? ''),
            ]);

            $title = (string)($section['title'] ?? '');
            $content = $section['content'] ?? '';

            $html .= '<section class="' . $class . '">';

            if ($title !== '') {
                $html .= '<h4>' . $this->e($title) . '</h4>';
            }

            $html .= $this->content($content, (bool)($section['content_is_html'] ?? false));
            $html .= '</section>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Build badges.
     *
     * @param array<int,mixed> $badges Badges.
     *
     * @return string
     */
    private function badges(array $badges): string
    {
        $html = '<div class="wi-flipcard__badges">';

        foreach ($badges as $badge) {
            if (is_array($badge)) {
                $label = (string)($badge['label'] ?? '');
                $state = $this->slug((string)($badge['state'] ?? 'default'));
            } else {
                $label = (string)$badge;
                $state = 'default';
            }

            if ($label === '') {
                continue;
            }

            $html .= '<span class="wi-flipcard-badge wi-flipcard-badge--' . $this->e($state) . '">'
                . $this->e($label)
                . '</span>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Build bottom actions.
     *
     * @param array<int,array<string,mixed>> $actions Actions.
     * @param string                         $id      Card ID.
     *
     * @return string
     */
    private function actions(array $actions, string $id): string
    {
        $html = '<div class="wi-flipcard__actions">';

        foreach ($actions as $action) {
            if (!is_array($action)) {
                continue;
            }

            $label = (string)($action['label'] ?? '');

            if ($label === '') {
                continue;
            }

            $variant = $this->slug((string)($action['variant'] ?? 'ghost'));
            $icon = (string)($action['icon'] ?? '');
            $attrs = (array)($action['attributes'] ?? []);
            $attrs['type'] = $attrs['type'] ?? 'button';

            $extraClass = trim((string)($attrs['class'] ?? ''));
            unset($attrs['class']);

            $class = $this->classes([
                'wi-flipcard-btn',
                'wi-flipcard-btn--' . $variant,
                $extraClass,
            ]);

            $html .= '<button class="' . $class . '" ' . $this->attributes($attrs) . '>';

            if ($icon !== '') {
                $html .= '<i class="' . $this->e($icon) . '" aria-hidden="true"></i>';
            }

            $html .= '<span>' . $this->e($label) . '</span>';
            $html .= '</button>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render content safely.
     *
     * Plain strings are escaped by default.
     * Trusted HTML must be passed as:
     * ['trusted_html' => true, 'html' => '<div>...</div>']
     *
     * @param mixed $content Content.
     * @param bool  $trusted Legacy trusted flag.
     *
     * @return string
     */
    private function content(mixed $content, bool $trusted = false): string
    {
        if (is_array($content)) {
            if (!empty($content['trusted_html'])) {
                return (string)($content['html'] ?? '');
            }

            if (isset($content['text'])) {
                return '<p>' . $this->e($content['text']) . '</p>';
            }

            return '';
        }

        if ($trusted) {
            return (string)$content;
        }

        return '<p>' . $this->e($content) . '</p>';
    }

    /**
     * Render shared CSS/JS once per request.
     *
     * @return string
     */
    private function assets(): string
    {
        if (self::$assetsRendered) {
            return '';
        }

        self::$assetsRendered = true;

        return '<style>
            .wi-flipcard-grid {
                display: grid;
                grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
                gap: 18px;
                width: 100%;
            }

            .wi-flipcard-empty {
                border: 1px solid rgba(255,255,255,0.18);
                border-radius: 18px;
                background: rgba(8,18,32,0.82);
                color: rgba(255,255,255,0.78);
                padding: 20px;
            }

            .wi-flipcard {
                position: relative;
                min-height: 430px;
                perspective: 1200px;
            }

            .wi-flipcard__inner {
                position: relative;
                width: 100%;
                min-height: 430px;
                transform-style: preserve-3d;
                transition: transform 0.65s ease;
            }

            .wi-flipcard.is-flipped .wi-flipcard__inner {
                transform: rotateY(180deg);
            }

            .wi-flipcard__face {
                position: absolute;
                inset: 0;
                display: flex;
                flex-direction: column;
                backface-visibility: hidden;
                border: 1px solid rgba(255,255,255,0.72);
                border-radius: 22px;
                background:
                    radial-gradient(circle at top right, rgba(191,108,218,0.18), transparent 36%),
                    radial-gradient(circle at top left, rgba(0,215,255,0.10), transparent 38%),
                    linear-gradient(145deg, #080d14 0%, #111821 58%, #151124 100%);
                box-shadow: 0 18px 42px rgba(0,0,0,0.34);
                color: #ffffff;
                padding: 16px;
                overflow: hidden;
            }

            .wi-flipcard__face--back {
                transform: rotateY(180deg);
            }

            .wi-flipcard__actions-top {
                display: grid;
                grid-template-columns: 1fr 1fr;
                align-items: center;
                gap: 12px;
                min-height: 36px;
                margin-bottom: 12px;
                z-index: 2;
            }

            .wi-flipcard__actions-top-left {
                display: flex;
                justify-content: flex-start;
                gap: 8px;
            }

            .wi-flipcard__actions-top-right {
                display: flex;
                justify-content: flex-end;
                gap: 8px;
            }

            .wi-flipcard-top-btn {
                border-radius: 999px;
                border: 1px solid rgba(255,255,255,0.20);
                background: rgba(255,255,255,0.06);
                color: #ffffff;
                padding: 7px 11px;
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 11px;
                font-weight: 900;
                cursor: pointer;
                transition: transform 0.16s ease, border-color 0.16s ease, background 0.16s ease;
            }

            .wi-flipcard-top-btn:hover {
                transform: translateY(-1px);
                border-color: rgba(255,255,255,0.56);
                background: rgba(255,255,255,0.10);
            }

            .wi-flipcard-top-btn--primary {
                background: rgba(34,211,238,0.16);
                border-color: rgba(34,211,238,0.42);
                color: #cffafe;
            }

            .wi-flipcard-top-btn--danger {
                background: rgba(239,68,68,0.12);
                border-color: rgba(239,68,68,0.45);
                color: #fecaca;
            }

            .wi-flipcard-top-btn--success {
                background: rgba(34,197,94,0.12);
                border-color: rgba(34,197,94,0.45);
                color: #bbf7d0;
            }

            .top-bit,
            .wi-flipcard__header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                gap: 14px;
                border-radius: 17px;
                border: 1px solid rgba(255,255,255,0.12);
                background: rgba(255,255,255,0.045);
                padding: 13px;
                margin-bottom: 12px;
            }

            .wi-flipcard__header-main {
                min-width: 0;
            }

            .wi-flipcard__eyebrow {
                display: block;
                color: rgba(124,232,255,0.86);
                font-size: 11px;
                font-weight: 900;
                letter-spacing: 0.11em;
                text-transform: uppercase;
                margin-bottom: 5px;
            }

            .wi-flipcard__title {
                margin: 0;
                color: #ffffff;
                font-size: 21px;
                font-weight: 950;
                line-height: 1.18;
            }

            .wi-flipcard__subtitle {
                margin: 6px 0 0;
                color: rgba(245,247,251,0.70);
                font-size: 13px;
                line-height: 1.45;
            }

            .wi-flipcard-status {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                flex: 0 0 auto;
                border-radius: 999px;
                border: 1px solid rgba(255,255,255,0.18);
                background: rgba(255,255,255,0.06);
                color: rgba(255,255,255,0.88);
                padding: 7px 10px;
                font-size: 12px;
                font-weight: 850;
                white-space: nowrap;
            }

            .wi-flipcard-status__light {
                width: 9px;
                height: 9px;
                border-radius: 999px;
                background: #94a3b8;
                box-shadow: 0 0 12px rgba(148,163,184,0.7);
            }

            .wi-flipcard-status--green .wi-flipcard-status__light {
                background: #22c55e;
                box-shadow: 0 0 14px rgba(34,197,94,0.9);
            }

            .wi-flipcard-status--amber .wi-flipcard-status__light,
            .wi-flipcard-status--warning .wi-flipcard-status__light {
                background: #f59e0b;
                box-shadow: 0 0 14px rgba(245,158,11,0.9);
            }

            .wi-flipcard-status--red .wi-flipcard-status__light,
            .wi-flipcard-status--danger .wi-flipcard-status__light {
                background: #ef4444;
                box-shadow: 0 0 14px rgba(239,68,68,0.9);
            }

            .wi-flipcard-status--grey .wi-flipcard-status__light,
            .wi-flipcard-status--gray .wi-flipcard-status__light {
                background: #94a3b8;
            }

            .middle-bit,
            .wi-flipcard__body {
                min-height: 0;
                overflow: auto;
                padding-right: 2px;
            }

            .wi-flipcard__body::-webkit-scrollbar {
                width: 7px;
            }

            .wi-flipcard__body::-webkit-scrollbar-thumb {
                border-radius: 999px;
                background: rgba(255,255,255,0.18);
            }

            .wi-flipcard__media {
                width: 58px;
                height: 58px;
                flex: 0 0 58px;
                border-radius: 18px;
                border: 1px solid rgba(255,255,255,0.42);
                background: rgba(255,255,255,0.08);
                display: inline-flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                font-size: 24px;
                font-weight: 900;
                color: #ffffff;
                margin-bottom: 12px;
            }

            .wi-flipcard__media img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
            }

            .wi-flipcard__media--placeholder {
                background: linear-gradient(135deg, rgba(59,130,246,0.88), rgba(16,185,129,0.76));
            }

            .wi-flipcard__badges {
                display: flex;
                flex-wrap: wrap;
                gap: 8px;
                margin-top: 10px;
            }

            .wi-flipcard-badge {
                display: inline-flex;
                border-radius: 999px;
                border: 1px solid rgba(255,255,255,0.18);
                padding: 5px 9px;
                color: rgba(255,255,255,0.82);
                background: rgba(255,255,255,0.06);
                font-size: 11px;
                font-weight: 800;
            }

            .wi-flipcard__metrics {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 10px;
                margin: 0 0 12px;
            }

            .wi-flipcard__metric {
                border: 1px solid rgba(255,255,255,0.14);
                border-radius: 14px;
                background: rgba(255,255,255,0.045);
                padding: 10px;
                min-width: 0;
            }

            .wi-flipcard__metric dt {
                color: rgba(255,255,255,0.55);
                font-size: 11px;
                font-weight: 900;
                letter-spacing: 0.05em;
                text-transform: uppercase;
                margin-bottom: 4px;
            }

            .wi-flipcard__metric dd {
                margin: 0;
                color: #ffffff;
                font-size: 14px;
                font-weight: 850;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
            }

            .wi-flipcard__sections {
                display: grid;
                gap: 10px;
            }

            .wi-flipcard__section,
            .wi-flipcard__content {
                border: 1px solid rgba(255,255,255,0.12);
                border-radius: 14px;
                background: rgba(255,255,255,0.04);
                color: rgba(255,255,255,0.78);
                padding: 12px;
                margin: 0 0 10px;
            }

            .wi-flipcard__section h4 {
                margin: 0 0 8px;
                color: #ffffff;
                font-size: 13px;
                font-weight: 900;
            }

            .wi-flipcard__section p,
            .wi-flipcard__content p,
            .wi-flipcard-note {
                margin: 0;
                color: rgba(255,255,255,0.74);
                font-size: 13px;
                line-height: 1.5;
            }

            .end-bit,
            .wi-flipcard__footer {
                margin-top: auto;
                padding-top: 12px;
            }

            .wi-flipcard__actions {
                display: flex;
                flex-wrap: wrap;
                gap: 9px;
            }

            .wi-flipcard-btn {
                border-radius: 999px;
                border: 1px solid rgba(255,255,255,0.18);
                background: rgba(255,255,255,0.06);
                color: #ffffff;
                padding: 8px 13px;
                display: inline-flex;
                align-items: center;
                gap: 7px;
                font-weight: 900;
                font-size: 12px;
                cursor: pointer;
                transition: transform 0.18s ease, border-color 0.18s ease, background 0.18s ease;
            }

            .wi-flipcard-btn:hover {
                transform: translateY(-1px);
                border-color: rgba(255,255,255,0.56);
                background: rgba(255,255,255,0.10);
            }

            .wi-flipcard-btn--primary {
                background: rgba(34,211,238,0.16);
                border-color: rgba(34,211,238,0.42);
                color: #cffafe;
            }

            .wi-flipcard-btn--danger {
                background: rgba(239,68,68,0.12);
                border-color: rgba(239,68,68,0.45);
                color: #fecaca;
            }

            .wi-flipcard-btn--success {
                background: rgba(34,197,94,0.12);
                border-color: rgba(34,197,94,0.45);
                color: #bbf7d0;
            }

            @media (max-width: 640px) {
                .wi-flipcard-grid {
                    grid-template-columns: 1fr;
                }

                .wi-flipcard__metrics {
                    grid-template-columns: 1fr;
                }

                .top-bit,
                .wi-flipcard__header {
                    display: grid;
                }

                .wi-flipcard-status {
                    justify-self: start;
                }
            }
        </style>
        <script>
            (function () {
                if (window.WIFlipcardSharedInit) {
                    return;
                }

                window.WIFlipcardSharedInit = true;

                document.addEventListener("click", function (event) {
                    var toggle = event.target.closest("[data-wi-flip-toggle]");

                    if (!toggle) {
                        return;
                    }

                    var card = toggle.closest("[data-wi-flipcard]");

                    if (!card) {
                        return;
                    }

                    event.preventDefault();
                    event.stopPropagation();

                    card.classList.toggle("is-flipped");
                }, true);
            })();
        </script>';
    }

    /**
     * Build HTML attributes safely.
     *
     * @param array<string,mixed> $attributes Attributes.
     *
     * @return string
     */
    private function attributes(array $attributes): string
    {
        $html = '';

        foreach ($attributes as $key => $value) {
            $key = trim((string)$key);

            if ($key === '' || $value === null || $value === false) {
                continue;
            }

            if ($value === true) {
                $html .= ' ' . $this->e($key);
                continue;
            }

            if (is_array($value) || is_object($value)) {
                $value = json_encode($value, JSON_HEX_APOS | JSON_HEX_QUOT) ?: '';
            }

            $html .= ' ' . $this->e($key) . '="' . $this->e($value) . '"';
        }

        return trim($html);
    }

    /**
     * Build class string.
     *
     * @param array<int,string> $classes Classes.
     *
     * @return string
     */
    private function classes(array $classes): string
    {
        $clean = [];

        foreach ($classes as $class) {
            $class = trim($class);

            if ($class !== '') {
                $clean[] = $class;
            }
        }

        return $this->e(implode(' ', array_unique($clean)));
    }

    /**
     * Normalise state for CSS modifiers.
     *
     * @param string $state Raw state.
     *
     * @return string
     */
    private function normaliseState(string $state): string
    {
        $state = strtolower(trim($state));

        return match ($state) {
            'green', 'amber', 'red', 'grey', 'gray', 'completed', 'missed', 'locked' => $state,
            'warning' => 'amber',
            'danger', 'critical', 'failed', 'fault' => 'red',
            'ok', 'good', 'active', 'operational', 'working' => 'green',
            'inactive', 'out_of_service', 'retired', 'unavailable' => 'grey',
            default => 'grey',
        };
    }

    /**
     * Safe slug.
     *
     * @param string $value Raw value.
     *
     * @return string
     */
    private function slug(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9_-]+/', '-', $value) ?: 'default';

        return trim($value, '-');
    }

    /**
     * Safe HTML ID.
     *
     * @param mixed $value Raw ID.
     *
     * @return string
     */
    private function safeId(mixed $value): string
    {
        $id = preg_replace('/[^A-Za-z0-9_-]+/', '-', (string)$value) ?: uniqid('wi-flipcard-', false);

        return trim($id, '-');
    }

    /**
     * Escape output.
     *
     * @param mixed $value Value.
     *
     * @return string
     */
    private function e(mixed $value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}