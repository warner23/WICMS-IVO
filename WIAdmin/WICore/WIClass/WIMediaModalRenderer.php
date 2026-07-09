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
| File: WIMediaModalRenderer.php
| Location: /root/WIAdmin/WICore/WIClass/WIMediaModalRenderer.php
| Type: PHP UI Renderer
| Layer: Shared UI Support
| Purpose Area: Shared Media Modal Bodies
| Version: 1.2.0
| Created: 2026-05-05
| Last Updated: 2026-05-05
| Status: Production Refactor Batch 4
|--------------------------------------------------------------------------
| Summary:
| Renderer for WIMedia modal body fragments.
| - WIModal owns the modal shell
| - This class returns small WIMedia component bodies only
| - Does not render or modalise the full Media Centre admin page
| - Contains no upload, database, permission or compliance business logic
|--------------------------------------------------------------------------
*/

require_once __DIR__ . '/WIMediaComponentRenderer.php';

class WIMediaModalRenderer
{
    private WIMediaComponentRenderer $components;

    /*
    |--------------------------------------------------------------------------
    | Constructor
    |--------------------------------------------------------------------------
    */

    /**
     * Creates the modal renderer with the shared component renderer.
     *
     * @param WIMediaComponentRenderer|null $components Optional component renderer.
     */
    public function __construct(?WIMediaComponentRenderer $components = null)
    {
        $this->components = $components ?? new WIMediaComponentRenderer();
    }

    /*
    |--------------------------------------------------------------------------
    | Modal Body Renderers
    |--------------------------------------------------------------------------
    */

    /**
     * Renders an upload modal body.
     *
     * @param array<string, mixed> $context WIMedia context.
     *
     * @return string
     */
    public function renderUpload(array $context = []): string
    {
        return $this->components->renderUpload($context);
    }

    /**
     * Renders a picker modal body.
     *
     * @param array<string, mixed> $context WIMedia context.
     *
     * @return string
     */
    public function renderPicker(array $context = []): string
    {
        return $this->components->renderPicker($context);
    }


    /**
     * Renders a document attach modal body.
     *
     * @param array<string, mixed> $context WIMedia/document context.
     *
     * @return string
     */
    public function renderDocumentAttach(array $context = []): string
    {
        return $this->components->renderDocumentAttach($context);
    }

    /**
     * Renders a viewer modal body.
     *
     * @param array<string, mixed> $context WIMedia context.
     *
     * @return string
     */
    public function renderViewer(array $context = []): string
    {
        return $this->components->renderViewer($context);
    }

    /**
     * Renders an attached-media modal body.
     *
     * @param array<string, mixed> $context WIMedia context.
     *
     * @return string
     */
    public function renderAttachedList(array $context = []): string
    {
        return $this->components->renderAttachedList($context);
    }

    /**
     * Returns a small manager body for modal use.
     *
     * Important:
     * This intentionally does not render the full Media Centre page.
     * The full Media Centre belongs to /root/WIAdmin/WIInc/media.php.
     *
     * @param array<string, mixed> $context WIMedia context.
     *
     * @return string
     */
    public function renderManager(array $context = []): string
    {
        return $this->components->renderPicker($context)
            . $this->components->renderUpload($context)
            . $this->components->renderAttachedList($context);
    }
}
