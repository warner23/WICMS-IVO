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
| File: WIMediaComponentRenderer.php
| Location: /root/WIAdmin/WICore/WIClass/WIMediaComponentRenderer.php
| Type: PHP UI Renderer
| Layer: Shared UI Support
| Purpose Area: Shared Media Components
| Version: 1.1.0
| Created: 2026-05-05
| Last Updated: 2026-05-05
| Status: Production Refactor Batch 4
|--------------------------------------------------------------------------
| Summary:
| Shared WIMedia component renderer for small reusable media UI fragments.
| - Provides upload, picker, attached-list and viewer component bodies
| - Designed for use inside tabs, pages or WIModal bodies
| - Does not render the full Media Centre admin page
| - Contains no upload, database, permission or compliance business logic
|--------------------------------------------------------------------------
*/

class WIMediaComponentRenderer
{
    /*
    |--------------------------------------------------------------------------
    | Context Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Encodes a component context array for safe HTML attribute usage.
     *
     * @param array<string, mixed> $context Component context.
     *
     * @return string
     */
    private function contextJson(array $context): string
    {
        return htmlspecialchars(
            json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) ?: '{}',
            ENT_QUOTES,
            'UTF-8'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Upload Component
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a compact upload component.
     *
     * @param array<string, mixed> $context WIMedia context.
     *
     * @return string
     */
    public function renderUpload(array $context = []): string
    {
        $contextJson = $this->contextJson($context);

        return <<<HTML
<section class="wi-media-component wi-media-component-upload" data-wimedia-mount="upload" data-wimedia-context="{$contextJson}">
    <form class="wi-media-component-upload__form" data-wimedia-upload-form enctype="multipart/form-data">
        <div class="wi-media-component-dropzone" data-wimedia-dropzone>
            <strong>Upload files</strong>
            <p>Choose one or more files to attach through WIMedia.</p>
            <input type="file" name="files[]" multiple data-wimedia-upload-input data-wimedia-file-input>
        </div>

        <div class="wi-media-component-actions">
            <button type="submit" class="wi-btn wi-btn-primary" data-wimedia-component-upload-submit>
                Upload
            </button>
        </div>

        <div class="wi-media-component-status" data-wimedia-upload-status data-wimedia-upload-progress>
            Ready.
        </div>
    </form>
</section>
HTML;
    }

    /*
    |--------------------------------------------------------------------------
    | Picker Component
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a compact media picker component.
     *
     * @param array<string, mixed> $context WIMedia context.
     *
     * @return string
     */
    public function renderPicker(array $context = []): string
    {
        $contextJson = $this->contextJson($context);

        return <<<HTML
<section class="wi-media-component wi-media-component-picker" data-wimedia-picker data-wimedia-context="{$contextJson}">
    <header class="wi-media-component-header">
        <div>
            <strong>Select media</strong>
            <p>Choose an existing WIMedia item to attach.</p>
        </div>

        <button type="button" class="wi-btn" data-wimedia-picker-refresh>
            Refresh
        </button>
    </header>

    <div class="wi-media-component-filters">
        <input type="search" data-wimedia-filter="search" placeholder="Search media">
        <select data-wimedia-filter="media_type">
            <option value="">All types</option>
            <option value="document">Documents</option>
            <option value="image">Images</option>
            <option value="video">Videos</option>
            <option value="audio">Audio</option>
            <option value="archive">Archives</option>
            <option value="other">Other</option>
        </select>
    </div>

    <div class="wi-media-component-list" data-wimedia-list="picker"></div>
</section>
HTML;
    }

    /*
    |--------------------------------------------------------------------------
    | Attached List Component
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a component placeholder for media already linked to an entity.
     *
     * @param array<string, mixed> $context WIMedia context.
     *
     * @return string
     */
    public function renderAttachedList(array $context = []): string
    {
        $contextJson = $this->contextJson($context);

        return <<<HTML
<section class="wi-media-component wi-media-component-attached-list" data-wimedia-mount="attached-list" data-wimedia-context="{$contextJson}">
    <div class="wi-media-component-list" data-wimedia-attached-list>
        Loading attached media...
    </div>
</section>
HTML;
    }

    /*
    |--------------------------------------------------------------------------
    | Document Attach Component
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a compact document attach component body.
     *
     * @param array<string, mixed> $context WIMedia/document context.
     *
     * @return string
     */
    public function renderDocumentAttach(array $context = []): string
    {
        $documentId = (int)($context['document_id'] ?? 0);
        $contextJson = $this->contextJson($context);

        return <<<HTML
<section class="wi-media-component wi-media-component-document-attach" data-wimedia-document-attach data-wimedia-context="{$contextJson}">
    <form class="wi-comp-document-attach-form" data-document-attach-form enctype="multipart/form-data">
        <input type="hidden" name="document_id" data-document-attach-id value="{$documentId}">

        <label class="wi-comp-field">
            <span>Media Role</span>
            <select name="link_type" data-document-attach-role>
                <option value="primary_file">Primary document file</option>
                <option value="supporting_evidence">Supporting evidence</option>
                <option value="review_evidence">Review evidence</option>
                <option value="replacement_file">Replacement file</option>
                <option value="archive_proof">Archive proof</option>
            </select>
        </label>

        <label class="wi-comp-field">
            <span>Upload New File</span>
            <input type="file" name="file" data-document-attach-file>
        </label>

        <label class="wi-comp-field">
            <span>Or Existing Media ID</span>
            <input type="number" name="media_id" min="0" placeholder="Optional existing WIMedia ID">
        </label>

        <p class="wi-comp-muted" data-document-attach-status>Ready.</p>

        <div class="wi-comp-modal-actions">
            <button type="button" class="wi-comp-btn wi-comp-btn-secondary" data-document-attach-close>Cancel</button>
            <button type="submit" class="wi-comp-btn wi-comp-btn-primary">Attach File</button>
        </div>
    </form>
</section>
HTML;
    }


    /*
    |--------------------------------------------------------------------------
    | Viewer Component
    |--------------------------------------------------------------------------
    */

    /**
     * Renders a compact viewer placeholder for a single media item.
     *
     * @param array<string, mixed> $context WIMedia context.
     *
     * @return string
     */
    public function renderViewer(array $context = []): string
    {
        $mediaId = (int) ($context['media_id'] ?? 0);
        $contextJson = $this->contextJson($context);

        return <<<HTML
<section class="wi-media-component wi-media-component-viewer" data-wimedia-viewer data-wimedia-media-id="{$mediaId}" data-wimedia-context="{$contextJson}">
    <div class="wi-media-component-viewer__stage" data-wimedia-viewer-stage>
        Loading media...
    </div>
</section>
HTML;
    }
}
